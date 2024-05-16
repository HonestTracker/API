<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class CrawlController extends Controller
{
    protected $signature = 'crawl';
    public function get_category_products()
    {
        $url = "https://www.bol.com/nl/nl/l/tv-s/7291/";
        $client = new Client();
        $response = $client->request('GET', $url); // Replace with the URL of the webshop
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $products = $crawler->filter('li.product-item--row');
        $products = $products->slice(0, 5);
        $count = 0;
        $ids = [];
        $products->each(function (Crawler $product, $i) use (&$ids, $url, $count) {
            $dataId = $product->attr('data-id');
            if ($dataId) {
                $link = $product->filter('a')->attr('href');
                $title = $product->filter('a.product-title')->text();
                $ids[] = [$dataId, "https://www.bol.com".$link, $title];
                $product = new Product;
                $product->name = $title;
                $product->site_name = "bol.com";
                $product->url = "https://www.bol.com".$link;
                $product->save();
            }
            $count++;
        });
        return $ids;
    }
    public function crawl()
    {
        $products = Product::all();
        foreach($products as $product)
        {
            $client = new Client();
            $response = $client->request('GET', $product->url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $productPrice = $crawler->filter('[data-test="price"]')->text();
            $productFraction = $crawler->filter('[data-test="price-fraction"]')->text();
            $date = Carbon::now();
            if($productFraction !== "-")
            {
                $productPrice = preg_replace('/ /', '.', $productPrice);
            }
            else
            {
                $productPrice = preg_replace('/[- ]/', '', $productPrice);
            }
            $product_price = new ProductPrice();
            $product_price->price = $productPrice;
            $product_price->date = $date;
            $product_price->product_id = $product->id;
            $product_price->save();
        }
    }
}
