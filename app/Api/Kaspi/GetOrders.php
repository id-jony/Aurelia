<?php

namespace App\Api\Kaspi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class GetOrders
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return object
     */


    public static function gen($page_number, $page_size, $status, $start_date, $end_date, $user)
    {
        $result = '';
        $config = config('services.kaspi');
        $setting = KaspiSetting::where('user_id', $user)->first();

        $headers = [
            'Content-Type' => 'application/vnd.api+json',
            'X-Auth-Token' => $setting->token,
        ];

        try {
            $result = Http::withHeaders($headers)
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

            $json_decode = json_decode($result->body());

            foreach ($json_decode->data as $data) {
                if (isset($data->attributes->plannedDeliveryDate)) {
                    $data->attributes->plannedDeliveryDate = date("Y-m-d H:i:s", $data->attributes->plannedDeliveryDate / 1000);
                } else {
                    $data->attributes->plannedDeliveryDate = null;
                }

                if ($data->attributes->isKaspiDelivery === true) {
                    $data->attributes->kaspiDelivery->courierTransmissionPlanningDate = date("Y-m-d H:i:s", $data->attributes->kaspiDelivery->courierTransmissionPlanningDate / 1000);
                } else {
                    $data->attributes->kaspiDelivery = [];
                    $data->attributes->kaspiDelivery += ['courierTransmissionPlanningDate' => null];
                }
            }

            return $json_decode;

        } catch (ConnectionException $err) {
        }
    }
}
