<?php

namespace App\Api\Kaspi;

use App\Exceptions\KaspiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;

class ProductPutUpForSale
{
    /**
     * Изменение стоимости продукта
     *
     * @param string $token
     * @param string $productSku
     * @return array|null
     * 
     * productSku = [sku, sku, sku]
     *
     * @throws KaspiException
     */
    public static function gen(string $token, string $productSku): ?array
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
                ->post('https://kaspi.kz/merchantcabinet/api/offer/approve/', [
                    "merchantProductCodes" => $productSku
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['simpleResponse']['status']) && isset($responseData['simpleResponse']['messages'])) {
                    return [
                        'status' => $responseData['simpleResponse']['status'],
                        'messages' => $responseData['simpleResponse']['messages'],
                    ];
                } else {
                    throw new KaspiException('Invalid response format.');
                }
            } else {
                throw new KaspiException('Failed to put product up for sale.');
            }
        } catch (ConnectionException $exception) {
            throw new KaspiException('Connection error.');
        } catch (RequestException $exception) {
            throw new KaspiException('Request error.');
        } catch (\Exception $exception) {
            throw new KaspiException('Error while putting product up for sale.');
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
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/mc/',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
            'Cookie' => 'X-Mc-Api-Session-Id=' . $token,
        ];
    }
}
