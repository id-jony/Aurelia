<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class GetProductPromo
{
    /**
     * Проверяем промо акции товара
     */
    public static function gen($sku)
    {
        $config = config('kaspi.api');
        $headers = self::createHeaders();
        $maxAttempts = $config['max_attempts']; // Максимальное количество попыток
        $attempt = 1;

        while ($attempt <= $maxAttempts) {
            try {
                $queryString = http_build_query([
                    "text" => $sku,
                    "page" => "0",
                    "mc" => "true",
                    "limit" => "100"
                ]);

                $response = Http::withHeaders($headers)
                    ->withOptions([
                        'debug' => $config['debug'],
                        'allow_redirects' => false,
                        'proxy' => 'socks5://jekajecka7755:a3e5e3@185.102.73.37:10012',
                        'timeout' => 0,
                        'connect_timeout' => 0,
                        'verify' => false,
                    ])
                    ->acceptJson()
                    ->get("https://kaspi.kz/yml/product-view/pl/results?{$queryString}");

                if ($response->successful()) {
                    return $response->json();
                } else {
                    throw new KaspiException('Не удалось получить промо товара: ' . $sku);
                }
            } catch (ConnectException $exception) {
                if ($attempt < $maxAttempts) {
                    $attempt++;
                     continue;
                } else {
                    throw new KaspiException('Ошибка подключения: ' . $exception->getMessage());
                }
            } catch (RequestException $exception) {
                if ($attempt < $maxAttempts) {
                    $attempt++;
                    continue;
                } else {
                    throw new KaspiException('Ошибка запроса: ' . $exception->getMessage());
                }
            } catch (\Exception $exception) {
                if ($attempt < $maxAttempts) {
                    $attempt++;
                    continue;
                } else {
                    throw new KaspiException('Ошибка при получении цены товара продавца: ' . $sku . ' ' . $exception->getMessage());
                }
            }
        }
    }

    /**
     * Создание заголовков для HTTP-запроса
     *
     * @return array
     */
    private static function createHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
        ];
    }
}
