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
        //$url = "https://www.bol.com/nl/nl/l/tv-s/7291/";
        //$site_name = "bol.com";
        $url = "https://www.coolblue.nl/televisies/filter?sorteren=best-verkocht";
        $site_name = "coolblue.nl";
        //$url = "https://www.mediamarkt.nl/nl/category/televisies-453.html?sort=salescount+desc";
        //$site_name = "mediamarkt.nl";
        $client = new Client();
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);
        if ($site_name == "bol.com") {
            $products = $crawler->filter('li.product-item--row');
        }
        if ($site_name == "coolblue.nl") {
            $products = $crawler->filter('div.product-card.product-card__full-width.grid.gap-x--4.gap-y--2.js-product');
        }
        if ($site_name == "mediamarkt.nl") {
            $products = $crawler->filter('div[data-test="mms-product-card"]');
        }
        $products = $products->slice(0, 5);
        $ids = [];
        $products->each(
            function (Crawler $product, $i) use (&$ids, $url, $site_name) {
                if ($site_name == "bol.com") {
                    $dataId = $product->attr('data-id');
                    if ($dataId) {
                        $link = $product->filter('a')->attr('href');
                        $title = $product->filter('a.product-title')->text();
                        $raw_price = $product->filter('span[data-test="price"]')->text();
                        $raw_fraction = $product->filter('[data-test="price-fraction"]')->text();
                        if ($raw_fraction !== "-") {
                            $price = preg_replace('/ /', '.', $raw_price);
                        } else {
                            $price = preg_replace('/[- ]/', '', $raw_price);
                        }
                        $ids[] = [$dataId, "https://www.bol.com" . $link, $title, $price];
                        $product = new Product;
                        $product->name = $title;
                        $product->site_name = "bol.com";
                        $product->current_price = $price;
                        $product->url = "https://www.bol.com" . $link;
                        //$product->save();
                    }
                } elseif ($site_name == "coolblue.nl") {
                    $ahref = $product->filter('.col--4 .position--relative a');
                    if ($ahref->count() > 0) {
                        $href = $ahref->attr('href');
                        if (preg_match('/product\/(\d+)\//', $href, $matches)) {
                            $dataId = $matches[1];
                        }
                        if (!empty ($dataId)) {
                            $title = $product->filter('h3.color--link')->text();
                            $raw_price = $product->filter('strong.sales-price__current.js-sales-price-current')->text();
                            //$raw_price = $product->filter('.sales-price__current js-sales-price-current')->text();
                            //$raw_fraction = $product->filter('[data-test="price-fraction"]')->text();
                            $price = preg_replace('/[^\d]/', '', $raw_price);
                            $ids[] = [$dataId, "https://www.coolblue.nl" . $href, $title, $price];

                            $product = new Product;
                            $product->name = $title;
                            $product->site_name = "coolblue.nl";
                            $product->url = "https://www.coolblue.nl" . $href;
                            //$product->save();
                        }
                    }
                } elseif ($site_name == "mediamarkt.nl") {
                    $href_component = $product->filter('a[data-test="mms-product-list-item-link"]');
                    $href = $href_component->attr('href');
                    if (preg_match('/-(\d+)\.html$/', $href, $matches)) {
                        $dataId = $matches[1];
                    }
                    if (!empty ($dataId)) {
                        $title = $product->filter('p[data-test="product-title"]')->text();
                        $ids[] = [$dataId, "https://www.mediamarkt.nl" . $href, $title];
                        $product = new Product;
                        $product->name = $title;
                        $product->site_name = "mediamarkt.nl";
                        $product->url = "https://www.mediamarkt.nl" . $href;
                        //$product->save();
                    }
                }
            }
        );
        return $ids;
    }
    public function crawl()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $client = new Client();
            $response = $client->request('GET', $product->url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $productPrice = $crawler->filter('[data-test="price"]')->text();
            $productFraction = $crawler->filter('[data-test="price-fraction"]')->text();
            $date = Carbon::now();
            if ($productFraction !== "-") {
                $productPrice = preg_replace('/ /', '.', $productPrice);
            } else {
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
