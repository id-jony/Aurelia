<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class MerchantGetProduct
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return object
     */


    public static function gen($token, $offerStatus)
    {
        $config = config('services.kaspi');

        $headers = self::createHeaders($token);

        try {
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'debug' => $config['debug'],
                    'allow_redirects' => false,
                ])
                ->accept('application/json')
                ->post('https://kaspi.kz/merchantcabinet/api/offer/', [
                    'categoryCode' => null,
                    'cityId' => null,
                    'count' => 1000,
                    'offerStatus' => $offerStatus,
                    'searchTerm' => null,
                    'start' => 0,
                ]);

                if ($response->successful()) {
                    return json_decode($response->body());
                } else {
                    throw new KaspiException('Failed to get merchant product.');
                }
            } catch (ConnectException $exception) {
                throw new KaspiException('Connection error: ' . $exception->getMessage());
            } catch (RequestException $exception) {
                throw new KaspiException('Request error: ' . $exception->getMessage());
            } catch (Exception $exception) {
                throw new KaspiException('Error while getting merchant product.');
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
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/mc/',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
            'Cookie' => 'X-Mc-Api-Session-Id=' . $token,
        ];
    }
}
