<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class UpdateProductPrice
{
    /**
     * Изменение стоимости продукта
     *
     * @param string $token
     * @param string $city
     * @param float $price
     * @param array $points
     * @param string $productName
     * @param string $productSku
     * @return array|null
     *
     * @throws KaspiException
     */
    public static function gen(string $token, string $city, float $price, array $points, string $productName, string $productSku): ?array
    {
        $pickupPoints = collect();
        $config = config('services.kaspi');
        $headers = self::createHeaders($token);

        foreach ($points as $point) {
            $point_data = [
                "name" => $point['name'],
                "available" => true,
                "status" => "ACTIVE"
            ];
            $pickupPoints->push($point_data);
        }

        try {
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'debug' => $config['debug'],
                    'allow_redirects' => false,
                ])
                ->accept('application/json')
                ->post('https://kaspi.kz/merchantcabinet/api/offer/save/', [
                    "cityData" => [
                        [
                            "id" => $city,
                            "priceRow" => [
                                "price" => $price
                            ],
                            "pickupPoints" => $pickupPoints
                        ]
                    ],
                    "productName" => $productName,
                    "productSku" => $productSku
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to update product price. Response: ' . $response->body());
                throw new KaspiException('Failed to update product price.');
            }
        } catch (ConnectException $exception) {
            throw new KaspiException('Connection error: ' . $exception->getMessage());
        } catch (RequestException $exception) {
            throw new KaspiException('Request error: ' . $exception->getMessage());
        } catch (\Exception $exception) {
            throw new KaspiException('Error while updating product price.');
        }
    }

    /**
     * Создание заголовков для HTTP-запроса
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
