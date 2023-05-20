<?php

namespace App\Api\Proxy;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class GetProxy
{
    /**
     * Получение информации о заказе
     *
     * @param integer $lenght
     * @return object
     */

    public static function gen()
    {
        $result = '';
        $config = config('services.kaspi');

        $headers = [
                        // 'Content-Type' => 'application/json',
                    ];

        try {
            $result = Http::withHeaders($headers)
                            ->withOptions([
                                'debug' => $config['debug'],
                            ])
                            ->get('https://hidemy.name/api/proxylist.php?out=js&code=279188766051083%0A%20&maxtime=600&type=5');


            return $result->body();

        } catch (ConnectionException $err) {
            // Вывод ошибки
        }
    }
}
