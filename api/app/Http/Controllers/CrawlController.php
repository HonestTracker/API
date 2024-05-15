<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class CrawlController extends Controller
{
    protected $signature = 'crawl';
    public function handle()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://www.bol.com/nl/nl/p/mywall-tv-muurbeugel-voor-23-42-inch-schermen-full-motion-tot-25kg-zwart/9200000105605877/?bltgh=j-2pun2UHel-t3XNN0hKCA.2_18.30.ProductTitle'); // Replace with the URL of the webshop
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);
        $productName = $crawler->filter('[data-test="title"]')->text();
        $productPrice = $crawler->filter('[data-test="price"]')->text();

        // Process or store product name and price as needed
        // For example, you can save them to the database
        return [$productName, $productPrice];

    }
}
