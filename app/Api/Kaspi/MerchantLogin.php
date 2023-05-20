<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class MerchantLogin
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return string
     */


    public static function gen($username, $password)
    {
        $result = '';
        $config = config('services.kaspi');

        
        $headers = [
                        'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
                        'User-Agent' => 'Macintosh; OS X/13.1.0',
                        'Accept-Encoding' => 'gzip'
                    ];

        $result = Http::withHeaders($headers)
                        ->asForm()
                        ->withOptions(['debug' => $config['debug'],])
                        ->post($config['url_mc'].'login', [
                                'username' => $username,
                                'password' => $password,
                        ]);
        $header = $result->header('Set-Cookie');

        preg_match('/(.*)(X-Mc-Api-Session-Id=[^&]*; D)(.*)/', $header, $matches);
        $session_token = str_replace(' D', '', $matches[2]);
        $session_token = str_replace('X-Mc-Api-Session-Id=', '', $session_token);

        return $session_token;
    }
}
