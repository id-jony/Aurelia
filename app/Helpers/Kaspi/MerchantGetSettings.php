<?php

namespace App\Helpers\Kaspi;

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
     * @return string
     */


    public static function gen($token)
    {
        $result = '';
        $config = config('services.kaspi');

        
        $headers = [
                        'Content-Type' => 'application/json',
                        'Referer' => 'https://kaspi.kz/mc/',
                        'User-Agent' => 'Macintosh; OS X/13.1.0',
                        'Cookie' => 'X-Mc-Api-Session-Id='.$token,
                    ];

        $result = Http::withHeaders($headers)
                        ->withOptions([
                            'debug' => false, 
                            // 'cookie' => $cookieJar,
                            'allow_redirects' => false,
                            ])
                        ->accept('application/json')
                        ->get('https://kaspi.kz/merchantcabinet/api/merchant/settings');

        
    
                        
        return $result->body();
    }
}
