<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class CrawlController extends Controller
{
    protected $signature = 'crawl';
    public function get_category_products(){
        $client = new Client();
        $response = $client->request('GET', 'https://www.bol.com/nl/nl/l/tv-s/7291/'); // Replace with the URL of the webshop
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $products = $crawler->filter('li.product-item--row');

        // Iterate through each <li> element and extract the data-id attribute
        $ids = [];
        $products->each(function (Crawler $product, $i) use (&$ids) {
            $dataId = $product->attr('data-id');
            if ($dataId) {
                $link = $product->filter('a')->attr('href');
                $title = $product->filter('a.product-title')->text();
                $ids[] = [$dataId, "https://www.bol.com".$link, $title];
            }
            $extractedData[] = [
                'data-id' => $dataId,
                'title' => $title,
                'link' => $link
            ];
        });
        return $ids;
    }
    public function handle()
    {
        // Process or store product name and price as needed
        // For example, you can save them to the database
        $client2 = new Client();
        $response = $client2->request('GET', 'https://www.bol.com/nl/nl/p/mywall-tv-muurbeugel-voor-23-42-inch-schermen-full-motion-tot-25kg-zwart/9200000105605877/?bltgh=j-2pun2UHel-t3XNN0hKCA.2_18.30.ProductTitle'); // Replace with the URL of the webshop
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $productName = $crawler->filter('[data-test="title"]')->text();
        $productPrice = $crawler->filter('[data-test="price"]')->text();

        // Process or store product name and price as needed
        // For example, you can save them to the database
        return [$productName, $productPrice];

    }
}
