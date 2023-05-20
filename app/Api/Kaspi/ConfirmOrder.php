<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use App\Models\KaspiSetting;

class ConfirmOrder
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return string
     */


    public static function update($kaspi_id, $code, $status, $user_id)
    {
        $result = '';
        $config = config('services.kaspi');
        $setting = KaspiSetting::where('user_id', $user_id)->first();

        $headers = [
                        'Content-Type' => 'application/vnd.api+json',
                        'X-Auth-Token' => $setting->token,
                    ];
        try {
        $result = Http::withHeaders($headers)
                        ->withOptions(['debug' => $config['debug'],])
                        ->accept('application/vnd.api+json')
                        ->post($config['url'].'orders', [
                            'data' => [
                                'type' => 'orders',
                                'id' => $kaspi_id,
                                'attributes' => [
                                    'code' => $code,
                                    'status' => $status,
                                ]
                            ]
                        ]);

        return json_decode($result->body());

        } catch (ConnectionException $err) {
            // Вывод ошибки
        }
    }
}
