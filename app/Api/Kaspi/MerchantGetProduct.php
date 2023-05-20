<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Cookie\CookieJar;

class MerchantGetProduct
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return object
     */


    public static function gen($token, $offerStatus)
    {
        $result = '';
        $config = config('services.kaspi');



        $headers = [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/mc/',
            'User-Agent' => 'Macintosh; OS X/13.1.0',
            'Cookie' => 'X-Mc-Api-Session-Id=' . $token,
        ];

        $result = Http::withHeaders($headers)
            ->withOptions([
                'debug' => $config['debug'],
                'allow_redirects' => false,
            ])
            ->accept('application/json')
            ->post('https://kaspi.kz/merchantcabinet/api/offer/', [
                'categoryCode' => null,
                'cityId' => null,
                'count' => 1000,
                'offerStatus' => $offerStatus,
                'searchTerm' => null,
                'start' => 0,
            ]);




        return json_decode($result->body());
    }
}
