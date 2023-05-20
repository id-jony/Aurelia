<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Cookie\CookieJar;

class MerchantGetSettings
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return object
     */


    public static function gen($token)
    {
        $result = '';
        $config = config('services.kaspi');


        $headers = [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/mc/',
            'User-Agent' => 'Macintosh; OS X/13.1.0',
            'Cookie' => 'X-Mc-Api-Session-Id=' . $token,
        ];
        try {
            $result = Http::withHeaders($headers)
                ->withOptions([
                    'debug' => $config['debug'],
                    'allow_redirects' => false,
                ])
                ->accept('application/json')
                ->get('https://kaspi.kz/merchantcabinet/api/merchant/settings');




            if ($result->successful()) {
                return json_decode($result->body());
            }

        } catch (ConnectionException $err) {
        }
    }
}
