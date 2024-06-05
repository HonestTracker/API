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
            if($product->site_name == "mediamarkt.nl")
            {
                $raw_price = $crawler->filterXPath('//div[@data-test="product-price"]//div[@data-test="mms-price"]//span[contains(@class, "iOrmAX") or contains(@class, "fnhqEi")]')->text();
                $price = preg_replace('/[^\d,]/', '', $raw_price);
                $price = str_replace(',', '', $price);
            }
            else
            {
                $response = $client->request('GET', $product->url);
            }
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $date = Carbon::now();
            if($product->site_name == "bol.com")
            {
                $raw_price = $crawler->filter('span[data-test="price"]')->text();
                $raw_fraction = $crawler->filter('sup[data-test="price-fraction"]')->text();
                if ($raw_fraction !== "-") {
                    $price = preg_replace('/ /', '.', $raw_price);
                } else {
                    $price = preg_replace('/[- ]/', '', $raw_price);
                }
            }
            elseif($product->site_name == "coolblue.nl")
            {
                $raw_price = $crawler->filter('strong.sales-price__current.js-sales-price-current')->text();
                $price = preg_replace('/[^\d]/', '', $raw_price);
            }
            /*
            elseif($product->site_name == "mediamarkt.nl")
            {
                $raw_price = $crawler->filterXPath('//div[@data-test="product-price"]//div[@data-test="mms-price"]//span[contains(@class, "iOrmAX") or contains(@class, "fnhqEi")]')->text();
                $price = preg_replace('/[^\d,]/', '', $raw_price);
                $price = str_replace(',', '', $price);
            }
            */
            $product_price = new ProductPrice();
            $product_price->price = $price;
            $product_price->date = $date;
            $last_price = ProductPrice::where('product_id', $product->id)->first();
            
            $product_price->product_id = $product->id;
            $product_price->save();
        }
        return response("Products crawled!");
    }
}
