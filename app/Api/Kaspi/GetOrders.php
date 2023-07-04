<?php

namespace App\Api\Kaspi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;
use GuzzleHttp\Exception\RequestException;

class GetOrders
{
    /**
     * Получение списка заказов
     *
     * @return object
     * @throws KaspiException
     */
    public static function gen($page_number, $page_size, $status, $start_date, $end_date, $user): object
    {
        $config = config('services.kaspi');
        $setting = Shop::where('user_id', $user)->first();
        $maxAttempts = 5; // Максимальное количество попыток
        $attempt = 1;

        if (!$setting) {
            throw new KaspiException('Kaspi setting not found for user');
        }

        $headers = self::createHeaders($setting->token);
        while ($attempt <= $maxAttempts) {
            try {
                $response = Http::withHeaders($headers)
                    ->withOptions([
                        'debug' => $config['debug'],
                    ])
                    ->accept('application/vnd.api+json')
                    ->get($config['url'] . 'orders/', [
                        'page[number]' => $page_number,
                        'page[size]' => $page_size,
                        'filter[orders][state]' => $status,
                        'filter[orders][creationDate][$ge]' => $start_date,
                        'filter[orders][creationDate][$le]' => $end_date,
                        'include[orders]' => 'user',
                    ]);

                if ($response->successful()) {

                    $json_decode = json_decode($response->body());

                    foreach ($json_decode->data as $data) {
                        if (isset($data->attributes->plannedDeliveryDate)) {
                            $data->attributes->plannedDeliveryDate = date("Y-m-d H:i:s", $data->attributes->plannedDeliveryDate / 1000);
                        } else {
                            $data->attributes->plannedDeliveryDate = null;
                        }

                        // if ($data->attributes->isKaspiDelivery === true) {
                        //     $data->attributes->kaspiDelivery->courierTransmissionPlanningDate = date("Y-m-d H:i:s", $data->attributes->kaspiDelivery->courierTransmissionPlanningDate / 1000);
                        // } else {
                        //     $data->attributes->kaspiDelivery = [];
                        //     $data->attributes->kaspiDelivery += ['courierTransmissionPlanningDate' => null];
                        // }
                    }

                    return $json_decode;
                } else {
                    throw new KaspiException('Failed to get order information', $response->status());
                }
            } catch (ConnectionException $err) {
                if ($attempt < $maxAttempts) {
                    $attempt++;
                    continue;
                } else {
                    throw new KaspiException('Ошибка подключения: ' . $err->getMessage());
                }
            } catch (RequestException $error) {
                throw new KaspiException('Request error: ' . $error->getMessage());
            } catch (\Exception $e) {
                throw new KaspiException('An error occurred: ' . $e->getMessage());
            }
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
            'Content-Type' => 'application/vnd.api+json',
            'X-Auth-Token' => $token,
        ];
    }

    /**
     * Преобразование данных заказа
     *
     * @param array $orderData
     * @return array
     */
    private static function transformOrderData(array $orderData): array
    {
        // foreach ($orderData as $data) {
        //     if (isset($data->attributes->plannedDeliveryDate)) {
        //         $data->attributes->plannedDeliveryDate = date("Y-m-d H:i:s", $data->attributes->plannedDeliveryDate / 1000);
        //     } else {
        //         $data->attributes->plannedDeliveryDate = null;
        //     }

        //     if (isset($data->attributes->isKaspiDelivery)) {
        //         $data->attributes->kaspiDelivery->courierTransmissionPlanningDate = date("Y-m-d H:i:s", $data->attributes->kaspiDelivery->courierTransmissionPlanningDate / 1000);
        //     } else {
        //         $data->attributes->kaspiDelivery += ['courierTransmissionPlanningDate' => null];
        //     }
        // }

        return $orderData;
    }
}
