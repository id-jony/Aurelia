<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class MerchantLogin
{
    /**
     * Получение токена сеанса авторизации в кабинете мерчанта
     *
     * @param string $username
     * @param string $password
     * @return string|null
     *
     * @throws KaspiException
     */
    public static function gen(string $username, string $password): ?string
    {
        $config = config('kaspi.api');
        $headers = self::createHeaders();

        try {
            $response = Http::withHeaders($headers)
                ->asForm()
                ->withOptions(['debug' => $config['debug']])
                ->post($config['kaspi_mc_api_url'] . 'login', [
                    'username' => $username,
                    'password' => $password,
                ]);

            if ($response->successful()) {
                $header = $response->header('Set-Cookie');
                preg_match('/(.*)(X-Mc-Api-Session-Id=[^&]*; D)(.*)/', $header, $matches);
                $sessionToken = str_replace(' D', '', $matches[2]);
                $sessionToken = str_replace('X-Mc-Api-Session-Id=', '', $sessionToken);
                return $sessionToken;
            } else {
                throw new KaspiException('Failed to login to Kaspi merchant cabinet.');
            }
        } catch (ConnectException $exception) {
            throw new KaspiException('Connection error: ' . $exception->getMessage());
        } catch (RequestException $exception) {
            throw new KaspiException('Request error: ' . $exception->getMessage());
        } catch (\Exception $exception) {
            throw new KaspiException('Error while logging into Kaspi merchant cabinet.');
        }
    }

    /**
     * Создание заголовков для HTTP-запроса
     *
     * @return array
     */
    private static function createHeaders(): array
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
            'Accept-Encoding' => 'gzip'
        ];
    }
}
