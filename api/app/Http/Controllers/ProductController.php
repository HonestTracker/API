<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategorySite;
use App\Models\Product;
use App\Models\ProductPrice;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\DomCrawler\Crawler;

class ProductController extends Controller
{
    public function homepage(Request $request)
    {
        if ($request->amount !== null) {
            $products = Product::with(['prices', 'site.category'])->take($request->amount)->get();
        } else {
            $products = Product::with(['prices', 'site.category'])->get();
        }
        $user = Auth::user();
        return response()->json([
            "user" => $user,
            "products" => $products
        ]);
    }
    public function homepage_web(Request $request)
    {
        $featured_product = Product::inRandomOrder()->with(['prices', 'site.category'])->first();
        $featured_categories = Category::inRandomOrder()->take(3)->get();
        // Get the latest 5 products with a positive change_percentage (latest rises)
        $latest_rise_products = Product::where('change_percentage', '>', 0)
            ->with('site')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get the latest 5 products with a negative change_percentage (latest drops)
        $latest_drop_products = Product::where('change_percentage', '<', 0)
            ->with('site')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get the latest 5 products with a non-zero change_percentage (latest updates)
        $latest_updated_products = Product::where('change_percentage', '<>', '0.00')
            ->with('site')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        return response()->json([
            "featured_product" => $featured_product,
            "featured_categories" => $featured_categories,
            "latest_rise_products" => $latest_rise_products,
            "latest_drop_products" => $latest_drop_products,
            "latest_updated_products" => $latest_updated_products,
        ]);
    }
    public function product_page(Request $request)
    {
        $products = Product::with(['prices', 'site.category'])->get();
        $categories = Category::all();
        $user = Auth::user();
        return response()->json([
            "user" => $user,
            "categories" => $categories,
            "products" => $products
        ]);
    }
    public function product_page_web(Request $request)
    {
        $products = Product::with('prices')->with('site')->get();
        $categories = Category::with('sites.products')->get();
        $user = Auth::user();
        return response()->json([
            "user" => $user,
            "categories" => $categories,
            "products" => $products
        ]);
    }
    public function product_page_single(Request $request)
    {
        $product = Product::with('site.category')->where('id', $request->product_id)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $user = Auth::user();

        return response()->json([
            "user" => $user,
            "product" => $product,
        ]);
    }

    public function search_products(Request $request)
    {
        $searchData = $request->search_data;

        // Assuming your logic to filter products based on search data
        $products = Product::where('name', 'like', '%' . $searchData . '%')
            ->with(['prices', 'site.category'])
            ->get();

        // Fetch categories related to the searched products
        $categories = Category::whereHas('sites.products', function ($query) use ($searchData) {
            $query->where('name', 'like', '%' . $searchData . '%');
        })
            ->with('sites.products')
            ->get();

        $user = Auth::user();

        return response()->json([
            "user" => $user,
            "categories" => $categories,
            "products" => $products
        ]);
    }
    public function search_product_web(Request $request)
    {
        $search_data = $request->search_data;

        $products = Product::where('name', 'like', '%' . $search_data . '%')
            ->with('prices')
            ->with('site')
            ->get();

        $categories = Category::whereHas('sites.products', function ($query) use ($search_data) {
            $query->where('name', 'like', '%' . $search_data . '%');
        })
            ->with('sites.products')
            ->get();

        $user = Auth::user();

        return response()->json([
            "user" => $user,
            "categories" => $categories,
            "products" => $products
        ]);
    }
    public function filter_products(Request $request)
    {
        $id = $request->id;
        if ($id == "all") {
            $products = Product::with(['prices', 'site.category'])->get();
        } else {
            $products = Product::whereHas('site', function ($query) use ($id) {
                $query->where('category_id', $id);
            })->with(['prices', 'site.category'])->get();
        }

        $user = Auth::user();

        return response()->json([
            "user" => $user,
            "products" => $products
        ]);
    }
    public function filter_products_web(Request $request)
    {
        $categories = $request->input('categories');

        $categoryIds = collect($categories)->pluck('category_id')->toArray();
        $siteIds = collect($categories)->pluck('site_id')->toArray();

        $products = Product::whereIn('site_id', $siteIds)
            ->whereHas('site', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->with(['prices', 'site.category'])->get();

        $user = auth()->user();
        return response()->json([
            'user' => $user,
            'products' => $products,
        ]);
    }
    public function delete(Product $product)
    {
        if ($product->prices) {
            foreach ($product->prices as $price) {
                $price->delete();
            }
        }
        $product->delete();
        return redirect('/admin/products')->with('success', "Product deleted!");
    }
    public function fetch_all_products()
    {
        $sites = CategorySite::all();

        foreach ($sites as $site) {
            $url = $site->url;
            $site_name = $site->site_name;
            $client = new Client();
            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            if ($site_name == "bol.com") {
                $products = $crawler->filter('li.product-item--row');
            } elseif ($site_name == "coolblue.nl") {
                $products = $crawler->filter('div.product-card');
            } else {
                continue;
            }

            $products = $products->slice(0, 10);

            $ids = [];

            $products->each(function (Crawler $product, $i) use (&$ids, $site, $site_name) {
                if ($site_name == "bol.com") {
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
                            $product->change_percentage = mt_rand(-1000, 1000) / 100;
                            $product->current_price = $price;
                            $product->url = "https://www.bol.com" . $link;
                            $product->currency = "EUR";
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
                    $ahref = $product->filter('div.product-card__title');
                    if ($ahref->count() > 0) {
                        $href = $ahref->filter('a.link')->attr('href');
                        if (preg_match('/product\/(\d+)\//', $href, $matches)) {
                            $dataId = $matches[1];
                        }
                        if (!empty($dataId)) {
                            $title = $ahref->filter('a.link')->attr('title');
                            $title = explode(' - ', $title, 2)[0];
                            $raw_price = $product->filter('strong.sales-price__current.js-sales-price-current')->text();
                            $price = preg_replace('/[^\d]/', '', $raw_price);
                            $product_check = Product::where('site_id', $site->id)->where('name', $title)->exists();

                            if ($product_check) {
                                $action = "update";
                                $product = Product::where('site_id', $site->id)->where('name', $title)->first();
                                $last_price = $product->prices()->orderBy('date', 'desc')->first();

                                if ($last_price) {
                                    $last_recorded_price = $last_price->price;
                                    $change_percentage = (($price - $last_recorded_price) / $last_recorded_price) * 100;
                                } else {
                                    $change_percentage = 0;
                                }

                                $product->change_percentage = $change_percentage;
                                $product->current_price = $price;
                                $product->update();
                            } else {
                                $action = "new";
                                $product = new Product;
                                $product->name = $title;
                                $product->site_id = $site->id;
                                $product->change_percentage = 0;
                                $product->current_price = $price;
                                $product->currency = "EUR";
                                $product->url = "https://www.coolblue.nl" . $href;
                                $product->save();
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
            });

            $site->last_crawled = now();
            $site->update();
        }

        return redirect()->back()->with('success', "Products fetched!");
    }

    public function get_product_by_id($id)
    {
        $product = Product::with(['prices', 'site.category'])->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $similar_products = Product::whereHas('site', function ($query) use ($product) {
            $query->where('category_id', $product->site->category_id);
        })
        ->inRandomOrder()
        ->take(3)
        ->get();
    
        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'similair_products' => $similar_products,
            'product' => $product,
        ]);
    }


}
