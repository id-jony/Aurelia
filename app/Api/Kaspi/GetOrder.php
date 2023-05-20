<?php

namespace App\Api\Kaspi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class GetOrder
{
    /**
     * Получение информации о заказе
     *
     * @param integer $lenght
     * @return object
     */

    public static function gen($kaspi_id, $user)
    {
        $result = '';
        $config = config('services.kaspi');
        $setting = KaspiSetting::where('user_id', $user)->first();

        $headers = [
            'Content-Type' => 'application/vnd.api+json',
            'X-Auth-Token' => $setting->token,
        ];

        try {
            $result = Http::withHeaders($headers)
                ->withOptions([
                    'debug' => $config['debug'],
                ])
                ->accept('application/vnd.api+json')
                ->get($config['url'] . 'orders/' . $kaspi_id . '/entries');


            return json_decode($result->body());
        } catch (ConnectionException $err) {
            // Вывод ошибки
        }
    }
}
