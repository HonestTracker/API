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
            
            try {
                $response = $client->request('GET', $product->url);
                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);
                $date = Carbon::now();
                
                $price = null; // Initialize price
                
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
                
                if ($price !== null) {
                    $product_price = new ProductPrice();
                    $product_price->site_id = $product->site->id;
                    $product_price->price = $price;
                    $product_price->date = $date;
                    $product_price->product_id = $product->id;
                    $product_price->change_percentage = mt_rand(-1000, 1000) / 100;
                    $product_price->save();
                    $product->current_price = $price;
                    $product->change_percentage = mt_rand(-1000, 1000) / 100;
                    $product->update();
                } else {
                    // Handle case where price extraction failed
                    // Log or skip the product update
                    continue;
                }
                
            } catch (\Exception $e) {
                // Handle GuzzleHttp exceptions (like 503 Service Temporarily Unavailable)
                // You can log the error or handle it based on your application's requirements
                \Log::error('Error crawling ' . $product->url . ': ' . $e->getMessage());
                continue; // Skip this product and move to the next one
            }
        }
        return redirect()->back()->with('success', 'Prices crawled successfully!');
    }
}