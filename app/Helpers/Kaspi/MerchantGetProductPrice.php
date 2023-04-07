<?php

namespace App\Helpers\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Cookie\CookieJar;

class MerchantGetProductPrice
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return string
     */


    public static function gen($sku)
    {
        $result = '';
        $config = config('services.kaspi');

        $headers = [
                        'Content-Type' => 'application/json',
                        'Referer' => 'https://kaspi.kz/',
                        'User-Agent' => 'Macintosh; OS X/13.1.0',
                        // 'Cookie' => 'X-Mc-Api-Session-Id='.$token,
                    ];

        $result = Http::withHeaders($headers)
                        ->withOptions([
                            'debug' => true, 
                            // 'cookie' => $cookieJar,
                            'allow_redirects' => false,
                            'proxy' => 'socks5://68.183.25.31:59166',
                            'timeout' => 0,
                            'connect_timeout' => 0,
                            ])
                        ->accept('application/json')
                        ->post('https://kaspi.kz/yml/offer-view/offers/'.$sku, [
                                'cityId' => '750000000',
                                'limit' => 10,
                                'page' => 0,
                                'sort' => true,
                        ]);
                        
        return $result->body();
    }
}
