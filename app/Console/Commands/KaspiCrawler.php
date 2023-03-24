<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class KaspiCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kaspicrawler';

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
        
        $link = 'https://kaspi.kz/shop/search/?text=107046399';
       
        $getProducts = Http::withOptions(['debug' => false,])
                    ->get($link);


        $crawler = new Crawler($getProducts->body());
        $link = $crawler->filter('a.ddl_product_link')->link();
       
        $getProduct = Http::withOptions(['debug' => false,])
                    ->get($link->getUri());

        $crawler = new Crawler($getProduct->body());

            $this->info($crawler->text());



                    Log::debug($crawler->text());


    }
}
