<?php

namespace App\Helpers\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class GetOrders
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return string
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


        $result = Http::withHeaders($headers)
                   ->withOptions(['debug' => false,])
                   ->accept('application/vnd.api+json')
                   ->get($config['url'].'orders/', [
                       'page[number]' => $page_number,
                       'page[size]' => $page_size,
                       'filter[orders][state]' => $status,
                       'filter[orders][creationDate][$ge]' => $start_date,
                       'filter[orders][creationDate][$le]' => $end_date,
                       'include[orders]' => 'user',
                   ]);


        return $result;
    }
}
