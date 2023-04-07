<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use DefStudio\Telegraph\Models\TelegraphChat;

class KaspiCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_kaspi_merchant_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    $products = Product::all();
    
    foreach ($products as $product) {

        $url = "https://kaspi.kz/yml/offer-view/offers/". $product->sku;
        $proxy = "185.28.251.67:1080";
        $headers = array(
           "Content-Type: application/json",
           "Referer: https://kaspi.kz/",
           "User-Agent: Macintosh; OS X/13.1.0",
        );
        $data = '{
            "cityId":"750000000", 
            "limit":10,
            "page":0,
            "sort":true
        }';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_PROXY, $proxy);
        // curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $merchants = collect();

        foreach (json_decode($resp)->offers as $offers) {

            $merchant = array(
                "merchantName" => $offers->merchantName,
                "price" => $offers->price,
            );

            $merchants->push($merchant);

            
        }

        $product->merchants = $merchants;
        $product->save();
    }
}
}
