<?php
namespace App\Api\Kaspi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class MerchantGetProductPrice
{
    /**
     * Отправляет POST-запрос к API и возвращает данные
     */


    private static $apiUrl = 'https://kaspi.kz/yml/offer-view/offers/';
    private static $maxRetries = 5;
    private static $retryTimeout = 20;

    public static function post($master_sku, $retry = 0)
    {
        // Создаем экземпляр клиента GuzzleHttp
        $client = new Client([
            'debug' => false,
            'allow_redirects' => false,
            'proxy' => 'socks5://jekajecka7755:a3e5e3@185.102.73.37:10012',
            'timeout' => 0,
            'connect_timeout' => 0,
            'verify' => false,
            'http_errors' => false,
        ]);

        $requestData = [
            'cityId' => 750000000,
            'limit' => 50,
            'page' => 0,
            'sort' => true,
        ];

        $response = null;

        try {
            // Отправляем POST-запрос к API
            $response = $client->post(self::$apiUrl.$master_sku, [
                'headers' => self::createHeaders(),
                'json' => $requestData,
            ]);

            // Парсим и возвращаем ответ
            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                return json_decode($response->getBody(), true);
            }
            
        } catch (GuzzleException $exception) {
            // Обработка ошибок Guzzle
            Log::error('Произошла ошибка при отправке запроса к API: ' . $exception->getMessage(), ['exception' => $exception]);
            // Если произошла ошибка соединения, повторяем запрос не более 5 раз с тайм-аутом 20 секунд
            if ($retry < self::$maxRetries) {
                sleep(self::$retryTimeout);
                return self::post($master_sku, $retry + 1);
            }
            throw $exception;

            return null;
        } catch (\Exception $exception) {
            // Обработка других ошибок
            Log::error('Ошибка: ' . $exception->getMessage(), ['exception' => $exception]);
            if ($retry < self::$maxRetries) {
                sleep(self::$retryTimeout);
                return self::post($master_sku, $retry + 1);
            }
            throw $exception;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Ошибка cURL: ' . $e->getMessage());
            if ($retry < self::$maxRetries) {
                sleep(self::$retryTimeout);
                return self::post($master_sku, $retry + 1);
            }
        }

    }

    /**
     * Создает заголовки для запроса
     *
     * @return array
     */
    private static function createHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
        ];
    }
}
