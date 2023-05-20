<?php

namespace App\Api\Kaspi;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateProductPrice
{
    /**
     * Изменение стоимость продукта
     */

    public static function gen($token, $city, $price, $points, $productName, $productSku)
    {
        $result = '';
        $pickupPoints = collect();
        $config = config('services.kaspi');

        foreach ($points as $point) {
            $point_data = array(
                "name" => $point['name'],
                "available" => true,
                "status" => "ACTIVE"
            );
            $pickupPoints->push($point_data);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/mc/',
            'User-Agent' => 'Macintosh; OS X/13.1.0',
            'Cookie' => 'X-Mc-Api-Session-Id=' . $token,
        ];

        $result = Http::withHeaders($headers)
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
        return $result->json();
    }
}
