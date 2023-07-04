<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CategoryComission
{
    private static $apiUrl = 'https://guide.kaspi.kz/partner/api/content/category';
    private static $maxRetries = 5;
    private static $retryTimeout = 20;

    private static function createHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
            'gSystem' => 'pay',
            'gLanguage' => 'ru',
        ];
    }

    public static function get(array $categories, $retry = 0)
    {
        $client = new Client([
            'debug' => true,
            'allow_redirects' => false,
            'timeout' => 0,
            'connect_timeout' => 0,
            'verify' => false,
        ]);

        try {
            $response = $client->get(self::$apiUrl, [
                'headers' => self::createHeaders(),
                'query' => http_build_query(['queryString' => $categories]),
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $responseData = json_decode($response->getBody(), true);
                if ($responseData['status'] === 'OK') {
                    $categoriesData = $responseData['body'];
                    // Обработка полученных данных
                    return $categoriesData;
                }
            }
        } catch (GuzzleException $exception) {
            // Обработка ошибок Guzzle
            Log::error('Ошибка при выполнении запроса к API: ' . $exception->getMessage(), ['exception' => $exception]);
            if ($retry < self::$maxRetries) {
                sleep(self::$retryTimeout);
                return self::get($categories, $retry + 1);
            }
            throw $exception;
        } catch (\Exception $exception) {
            // Обработка других ошибок
            Log::error('Ошибка: ' . $exception->getMessage(), ['exception' => $exception]);
            if ($retry < self::$maxRetries) {
                sleep(self::$retryTimeout);
                return self::get($categories, $retry + 1);
            }
            throw $exception;
        }
    }
}
