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
    public function test()
    {
        return "test";
    }
    public function get_category_products()
    {
        $url = "https://www.bol.com/nl/nl/l/tv-s/7291/";
        $site_name = "bol.com";
        //$url = "https://www.coolblue.nl/televisies/filter?sorteren=best-verkocht";
        //$site_name = "coolblue.nl";
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
                        $raw_fraction = $product->filter('sup[data-test="price-fraction"]')->text();
                        if ($raw_fraction !== "-") {
                            $price = preg_replace('/ /', '.', $raw_price);
                        } else {
                            $price = preg_replace('/[- ]/', '', $raw_price);
                        }
                        $product_check = Product::where('site_name', $site_name)->where('name', $title)->exists();
                        if($product_check)
                        {
                            $action = "update";
                            $product = Product::where('site_name', $site_name)->where('name', $title)->first();
                            $product->current_price = $price;
                            $product->update();
                        }
                        else
                        {
                            $action = "new";
                            $product = new Product;
                            $product->name = explode(' - ', $title, 2)[0];
                            $product->site_name = "bol.com";
                            $product->current_price = $price;
                            $product->url = "https://www.bol.com" . $link;
                            $product->save();
                        }
                        $ids[] = [
                            "data_id" => $dataId,
                            "site_name" => "https://www.bol.com" . $link, 
                            "name" => $title,
                            "price" => $price,
                            "action" => $action,
                        ];
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
                            $price = preg_replace('/[^\d]/', '', $raw_price);
                            $product_check = Product::where('site_name', $site_name)->where('name', $title)->exists();
                            if($product_check)
                            {
                                $action = "update";
                                $product = Product::where('site_name', $site_name)->where('name', $title)->first();
                                $product->current_price = $price;
                                $product->update();
                            }
                            else
                            {
                                $action = "new";
                                $product = new Product;
                                $product->name = explode(' - ', $title, 2)[0];
                                $product->site_name = "coolblue.nl";
                                $product->current_price = $price;
                                $product->url = "https://www.coolblue.nl" . $href;
                                $product->save();
                            }
                        }
                        $ids[] = [
                            "data_id" => $dataId,
                            "site_name" => "https://www.coolblue.com" . $href,
                            "name" => $title,
                            "price" => $price,
                            "action" => $action,
                        ];
                    }
                } elseif ($site_name == "mediamarkt.nl") {
                    $href_component = $product->filter('a[data-test="mms-product-list-item-link"]');
                    $href = $href_component->attr('href');
                    if (preg_match('/-(\d+)\.html$/', $href, $matches)) {
                        $dataId = $matches[1];
                    }
                    if (!empty ($dataId)) {
                        $title = $product->filter('p[data-test="product-title"]')->text();
                        $raw_price = $product->filterXPath('//div[@data-test="product-price"]//div[@data-test="mms-price"]//span[contains(@class, "iOrmAX") or contains(@class, "fnhqEi")]')->text();
                        $price = preg_replace('/[^\d,]/', '', $raw_price);
                        $price = str_replace(',', '', $price);
                        $product_check = Product::where('site_name', $site_name)->where('name', $title)->exists();
                        if($product_check)
                        {
                            $action = "update";
                            $product = Product::where('site_name', $site_name)->where('name', $title)->first();
                            $product->current_price = $price;
                            $product->update();
                        }
                        else
                        {
                            $action = "new";
                            $product = new Product;
                            $product->name = explode(' - ', $title, 2)[0];
                            $product->site_name = "mediamarkt.nl";
                            $product->current_price = $price;
                            $product->url = "https://www.mediamarkt.nl" . $href;
                            $product->save();
                        }
                        $ids[] = [
                            "data_id" => $dataId,
                            "site_name" => "https://www.mediamarkt.com" . $href,
                            "name" => $title,
                            "price" => $price,
                            "action" => $action,
                        ];
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
            $jar = new CookieJar();
    
            // Define a pool of user agents
            $userAgents = [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                // Add more user agents if needed
            ];
    
            // Define a pool of proxies (if necessary)
            $proxies = [
                'http://proxy1.example.com:8080',
                'http://proxy2.example.com:8080',
                // Add more proxies as needed
            ];
    
            // Select a random user agent and proxy
            $userAgent = $userAgents[array_rand($userAgents)];
            $proxy = $proxies[array_rand($proxies)];
    
            try {
                // Add a random delay between requests
                usleep(rand(500000, 2000000)); // 0.5 to 2 seconds delay
    
                $response = $client->request('GET', $product->url, [
                    'headers' => [
                        'User-Agent' => $userAgent,
                        'Accept-Language' => 'en-US,en;q=0.9',
                    ],
                    'cookies' => $jar,
                    'proxy' => $proxy,
                ]);
    
                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);
    
                // Process the crawler content as needed
            } catch (\Exception $e) {
                // Handle exceptions, e.g., logging the error or retrying
                echo 'Error: ' . $e->getMessage();
            }
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
            elseif($product->site_name == "mediamarkt.nl")
            {
                $raw_price = $crawler->filterXPath('//div[@data-test="product-price"]//div[@data-test="mms-price"]//span[contains(@class, "iOrmAX") or contains(@class, "fnhqEi")]')->text();
                $price = preg_replace('/[^\d,]/', '', $raw_price);
                $price = str_replace(',', '', $price);
            }
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
