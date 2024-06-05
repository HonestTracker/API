<?php

namespace App\Http\Controllers;

use App\Models\CategorySite;
use App\Models\Product;
use App\Models\ProductPrice;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class CrawlController extends Controller
{
    protected $signature = 'crawl';
    public function test()
    {
        return "test";
    }
    public function crawl()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $client = new Client();
            $response = $client->request('GET', $product->url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $date = Carbon::now();
            if ($product->site->site_name == "bol.com") {
                $raw_price = $crawler->filter('span[data-test="price"]')->text();
                $raw_fraction = $crawler->filter('sup[data-test="price-fraction"]')->text();
                if ($raw_fraction !== "-") {
                    $price = preg_replace('/ /', '.', $raw_price);
                } else {
                    $price = preg_replace('/[- ]/', '', $raw_price);
                }
            } elseif ($product->site->site_name == "coolblue.nl") {
                $raw_price = $crawler->filter('strong.sales-price__current.js-sales-price-current')->text();
                $price = preg_replace('/[^\d]/', '', $raw_price);
            }
            $product_price = new ProductPrice();
            $product_price->price = $price;
            $product_price->date = $date;
            $last_price = ProductPrice::where('product_id', $product->id)->first();

            $product_price->product_id = $product->id;
            $product_price->save();
            $product->current_price = $price;
            $product->update();
        }
        return response("Products crawled!");
    }
}
