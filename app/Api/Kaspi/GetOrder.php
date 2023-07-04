<?php

namespace App\Api\Kaspi;

use App\Exceptions\KaspiException;
use App\Models\Shop;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetOrder
{
    /**
     * Получение информации о заказе
     * @param int $userId     
     * @return object
     *
     * @throws KaspiException
     */
    public static function gen($kaspiId, int $userId): object
    {
        $config = config('services.kaspi');
        $setting = Shop::where('user_id', $userId)->first();

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
                ->get($config['url'] . 'orders/' . $kaspiId . '/entries');

            if ($response->successful()) {
                return json_decode($response->body());
            } else {
                throw new KaspiException('Failed to get order information', $response->status());
            }
        } catch (ConnectionException $err) {
            throw new KaspiException('Ошибка подключения: ' . $err->getMessage());
        } catch (RequestException $error) {
            throw new KaspiException('Ошибка запроса: ' . $error->getMessage());
        } catch (\Exception $e) {
            throw new KaspiException('Произошла ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Создание заголовков HTTP запроса
     *
     * @param string $token
     * @return array
     */
    private static function createHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/vnd.api+json',
            'X-Auth-Token' => $token,
        ];
    }
}
