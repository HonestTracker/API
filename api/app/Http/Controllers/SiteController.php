<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiteRequest;
use App\Models\Category;
use App\Models\CategorySite;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class SiteController extends Controller
{
    public function create(Category $category)
    {
        return view('admin.categories.sites.create', compact("category"));
    }
    public function store(StoreSiteRequest $request, Category $category)
    {
        $site = new CategorySite;
        $site->category_id = $category->id;
        $site->url = $request->url;
        $site->site_name = $request->site_name;
        $site->save();
        return redirect('admin/categories/' . $category->id . '/sites')->with('success', 'Site added!');
    }
    public function fetch_products(Category $category, CategorySite $site)
    {
        $url = $site->url;
        $site_name = $site->site_name;
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
        $products = $products->slice(0, 5);
        $ids = [];
        $products->each(
            function (Crawler $product, $i) use (&$ids, $url, $site) {
                if ($site->site_name == "bol.com") {
                    $dataId = $product->attr('data-id');
                    if ($dataId) {
                        $link = $product->filter('a')->attr('href');
                        $title = $product->filter('a.product-title')->text();
                        $title = explode(' - ', $title, 2)[0];
                        $raw_price = $product->filter('span[data-test="price"]')->text();
                        $raw_fraction = $product->filter('sup[data-test="price-fraction"]')->text();
                        if ($raw_fraction !== "-") {
                            $price = preg_replace('/ /', '.', $raw_price);
                        } else {
                            $price = preg_replace('/[- ]/', '', $raw_price);
                        }
                        $product_check = Product::where('site_id', $site->id)->where('name', $title)->exists();
                        if ($product_check) {
                            $action = "update";
                            $product = Product::where('site_id', $site->id)->where('name', $title)->first();
                            $product->current_price = $price;
                            $product->update();
                        } else {
                            $action = "new";
                            $product = new Product;
                            $product->name = $title;
                            $product->site_id = $site->id;
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
                } elseif ($site->site_name == "coolblue.nl") {
                    $ahref = $product->filter('.col--4 .position--relative a');
                    if ($ahref->count() > 0) {
                        $href = $ahref->attr('href');
                        if (preg_match('/product\/(\d+)\//', $href, $matches)) {
                            $dataId = $matches[1];
                        }
                        if (!empty($dataId)) {
                            $title = $product->filter('h3.color--link')->text();
                            $title = explode(' - ', $title, 2)[0];
                            $raw_price = $product->filter('strong.sales-price__current.js-sales-price-current')->text();
                            $price = preg_replace('/[^\d]/', '', $raw_price);
                            $product_check = Product::where('site_id', $site->id)->where('name', $title)->exists();
                            if ($product_check) {
                                $action = "update";
                                $product = Product::where('site_id', $site->id)->where('name', $title)->first();
                                $product->current_price = $price;
                                $product->update();
                            } else {
                                $action = "new";
                                $product = new Product;
                                $product->name = $title;
                                $product->site_id = $site->id;
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
                }
            }
        );
        $site->last_crawled = now();
        $site->update();
        return redirect()->back()->with('success', "Products fetched!");
    }
}
