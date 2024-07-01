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

                $price = null;

                if ($product->site->site_name == "bol.com") {
                    $raw_price = $crawler->filter('span[data-test="price"]')->text();
                    $raw_fraction = $crawler->filter('sup[data-test="price-fraction"]')->text();

                    if ($raw_fraction !== "-") {
                        $price = preg_replace('/ /', '.', $raw_price);
                    } else {
                        $price = preg_replace('/[- ]/', '', $raw_price);
                    }
                    $image_url = $crawler->filter('img[data-test="product-main-image"]')->attr('src');

                } elseif ($product->site->site_name == "coolblue.nl") {
                    $raw_price = $crawler->filter('strong.sales-price__current.js-sales-price-current')->text();
                    $price = preg_replace('/[^\d]/', '', $raw_price);
                    $image_url = $crawler->filter('img.product-media-gallery__item-image')->attr('src');
                }

                if ($price !== null) {
                    // Calculate change percentage
                    $last_price = $product->prices()->orderBy('date', 'desc')->first();
                    if ($last_price) {
                        $last_recorded_price = $last_price->price;
                        $change_percentage = (($price - $last_recorded_price) / $last_recorded_price) * 100;
                    } else {
                        // No previous price recorded, set a default change percentage
                        $change_percentage = 0;
                    }

                    // Save new product price
                    $productPrice = new ProductPrice();
                    $productPrice->site_id = $product->site->id;
                    $productPrice->price = $price;
                    $productPrice->date = $date;
                    $productPrice->product_id = $product->id;
                    $productPrice->change_percentage = $change_percentage;
                    $productPrice->save();

                    // Update product's current price and change percentage
                    $product->current_price = $price;
                    $product->change_percentage = $change_percentage;
                    $product->picture_url = $image_url;
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