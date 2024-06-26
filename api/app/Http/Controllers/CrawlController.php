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

            // Initialize variables for cheapest price and corresponding product_price
            $cheapestPrice = PHP_INT_MAX;
            $cheapestProductPrice = null;

            // Loop through each site associated with the product
            foreach ($product->site as $site) {
                $price = null;

                if ($site->site_name == "bol.com") {
                    $raw_price = $crawler->filter('span[data-test="price"]')->text();
                    $raw_fraction = $crawler->filter('sup[data-test="price-fraction"]')->text();

                    if ($raw_fraction !== "-") {
                        $price = preg_replace('/ /', '.', $raw_price);
                    } else {
                        $price = preg_replace('/[- ]/', '', $raw_price);
                    }

                } elseif ($site->site_name == "coolblue.nl") {
                    $raw_price = $crawler->filter('strong.sales-price__current.js-sales-price-current')->text();
                    $price = preg_replace('/[^\d]/', '', $raw_price);
                }

                if ($price !== null) {
                    // Save product price for this site
                    $productPrice = new ProductPrice();
                    $productPrice->site_id = $site->id;
                    $productPrice->price = $price;
                    $productPrice->date = $date;
                    $productPrice->product_id = $product->id;
                    $productPrice->save();

                    // Determine if this price is the cheapest
                    if ($price < $cheapestPrice) {
                        $cheapestPrice = $price;
                        $cheapestProductPrice = $productPrice;
                    }
                }
            }

            if ($cheapestProductPrice !== null) {
                // Calculate change percentage based on the last recorded price for this site
                $last_price = $product->prices()->where('site_id', $cheapestProductPrice->site_id)->orderBy('date', 'desc')->first();
                if ($last_price) {
                    $last_recorded_price = $last_price->price;
                    $change_percentage = (($cheapestPrice - $last_recorded_price) / $last_recorded_price) * 100;
                } else {
                    // No previous price recorded, set a default change percentage
                    $change_percentage = 0;
                }

                // Update product with cheapest price and corresponding product_price ID
                $product->current_price = $cheapestPrice;
                $product->current_price_id = $cheapestProductPrice->id;
                $product->change_percentage = $change_percentage;
                $product->update();
            }

        } catch (\Exception $e) {
            // Handle GuzzleHttp exceptions (like 503 Service Temporarily Unavailable)
            \Log::error('Error crawling ' . $product->url . ': ' . $e->getMessage());
            continue; // Skip this product and move to the next one
        }
    }

    return redirect()->back()->with('success', 'Prices crawled successfully!');
}

}