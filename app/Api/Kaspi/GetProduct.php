<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class GetProduct
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return object
     */


    public static function gen($order_id, $user)
    {
        $result = '';
        $config = config('services.kaspi');
        $setting = KaspiSetting::where('user_id', $user)->first();

        $headers = [
                        'Content-Type' => 'application/vnd.api+json',
                        'X-Auth-Token' => $setting->token,
                    ];

        $result = Http::withHeaders($headers)
                        ->withOptions([
                                'debug' => $config['debug'],
                            ])
                        ->accept('application/vnd.api+json')
                        ->get($config['url'].'orderentries/'.$order_id.'/product');


        return json_decode($result->body());
    }
}
