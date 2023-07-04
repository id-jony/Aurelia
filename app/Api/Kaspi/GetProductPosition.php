<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class GetProductPosition
{
    /**
     * Получение списка конкурентов продукта
     */

    public static function gen($category, $page)
    {
        $config = config('services.kaspi');
        $headers = self::createHeaders();
        $maxAttempts = 5; // Максимальное количество попыток
        $attempt = 1;

        while ($attempt <= $maxAttempts) {
            try {
                $response = null;
                $retryCount = 0;
                $queryString = http_build_query([
                    "page" => $page,
                    "q" => ":category:" . $category,
                    "sort" => "relevance",
                    "ui" => "d",
                    "i" => "-1"
                ]);

                do {
                    if ($response !== null) {
                        // Если ответ уже был получен, ждем 1 секунду перед повторной попыткой
                        usleep(10000000); // 10 секунд = 10 000 000 микросекунд
                    }

                    $response = Http::withHeaders($headers)
                        ->withOptions([
                            'debug' => $config['debug'],
                            'allow_redirects' => false,
                            'proxy' => 'socks5://jekajecka7755:a3e5e3@185.102.73.37:10012',
                            'timeout' => 0,
                            'connect_timeout' => 0,
                            'verify' => false,
                        ])
                        ->accept('application/json')
                        ->get("https://kaspi.kz/yml/product-view/pl/results?{$queryString}");

                    $retryCount++;
                } while ($response->getStatusCode() === 400 && $retryCount <= 3);

                if ($response->successful()) {
                    return json_decode($response->body());
                } else {
                    throw new KaspiException('Failed to get merchant product price.');
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
                    throw new KaspiException('Ошибка при получении позиции товара в категории: ' . $category . ' ' . $exception->getMessage());
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
