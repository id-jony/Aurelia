<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use App\Models\Shop;
use App\Exceptions\KaspiException;

class ConfirmOrder
{
    /**
     * Обновление статуса заказа
     *
     * @param integer $kaspi_id
     * @param string $code
     * @param string $status
     * @param integer $user_id
     * @return string
     * @throws KaspiException
     */
    public static function update($kaspi_id, $code, $status, $user_id)
    {
        $config = config('services.kaspi');
        $setting = Shop::where('user_id', $user_id)->first();
        if (!$setting) {
            throw new KaspiException('Kaspi setting not found for user');
        }
        $headers = self::createHeaders($setting->token);

        try {
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'debug' => $config['debug'],
                ])
                ->accept('application/vnd.api+json')
                ->post($config['url'] . 'orders', [
                    'data' => [
                        'type' => 'orders',
                        'id' => $kaspi_id,
                        'attributes' => [
                            'code' => $code,
                            'status' => $status,
                        ],
                    ],
                ]);

            return $response->body();
        } catch (ConnectionException $err) {
            throw new KaspiException('Ошибка подключения: ' . $err->getMessage());
        } catch (RequestException $error) {
            throw new KaspiException('Ошибка запроса: ' . $error->getMessage());
        } catch (\Exception $e) {
            throw new KaspiException('Произошла ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Создание заголовков для запроса
     *
     * @param string $token
     * @return array
     */
    private static function createHeaders($token)
    {
        return [
            'Content-Type' => 'application/vnd.api+json',
            'X-Auth-Token' => $token,
        ];
    }
}
