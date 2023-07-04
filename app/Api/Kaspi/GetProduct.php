<?php

namespace App\Api\Kaspi;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class GetProduct
{
    /**
     * Получение списка заказов
     *
     * @param integer $orderId
     * @param integer $userId
     * @return object
     * @throws KaspiException
     */
    public static function gen($orderId, $userId)
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
                ->get($config['url'] . 'orderentries/' . $orderId . '/product');

            if ($response->successful()) {
                return json_decode($response->body());
            } else {
                throw new KaspiException('Failed to get product.');
            }
        } catch (RequestException $exception) {
            throw new KaspiException('RequestException while getting product.');
        } catch (ConnectException $exception) {
            throw new KaspiException('ConnectException while getting product.');
        } catch (Exception $exception) {
            throw new KaspiException('Error while getting product.');
        }
    }

    /**
     * Создание заголовков запроса
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
