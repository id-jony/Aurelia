<?php

namespace App\Helpers\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\KaspiSetting;

class GetOrder
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return string
     */


    public static function gen($type, $kaspi_id, $code, $status, $user)
    {
        $result = '';
        $config = config('services.kaspi');
        $setting = KaspiSetting::where('user_id', $user)->first();

        $headers = [
                        'Content-Type' => 'application/vnd.api+json',
                        'X-Auth-Token' => $setting->token,
                    ];

        $result = Http::withHeaders($headers)
                        ->withOptions(['debug' => false,])
                        ->accept('application/vnd.api+json')
                        ->post($config['url'].'orders', [
                            'data' => [
                                'type' => $type,
                                'id' => $kaspi_id,
                                'attributes' => [
                                    'code' => $code,
                                    'status' => 'ACCEPTED_BY_MERCHANT=',
                                ]
                            ]
                        ]);

        return $result;
    }
}
