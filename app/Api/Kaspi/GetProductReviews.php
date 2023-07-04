<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class GetProductReviews
{
    /**
     * Получение списка конкурентов товара
     */

    public static function gen($sku)
    {
        $config = config('services.kaspi');
        $headers = self::createHeaders();
        $maxAttempts = 5; // Максимальное количество попыток
        $attempt = 1;

        while ($attempt <= $maxAttempts) {
            try {
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
                    ->get('https://kaspi.kz/yml/creview/rest/misc/product/' . $sku . '/reviews?limit=100&page=0&id=all');

                if ($response->successful()) {
                    return $response->json();
                } else {
                    throw new KaspiException('Failed to get merchant product price.');
                }
            } catch (ConnectException $exception) {
                throw new KaspiException('Ошибка подключения: ' . $exception->getMessage());
            } catch (RequestException $exception) {
                if ($attempt < $maxAttempts) {
                    // Увеличиваем счетчик попыток и повторяем цикл
                    $attempt++;
                    continue;
                } else {
                    throw new KaspiException('Ошибка запроса: ' . $sku . ' | ' . $exception->getMessage());
                }
            } catch (\Exception $exception) {
                if ($attempt < $maxAttempts) {
                    // Увеличиваем счетчик попыток и повторяем цикл
                    $attempt++;
                    continue;
                } else {
                    throw new KaspiException('Ошибка при получении цены товара продавца. ' . $sku);
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
