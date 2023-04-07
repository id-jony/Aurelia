<?php

namespace App\Helpers\Kaspi;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\KaspiSetting;
use Illuminate\Support\Facades\Http;

class GetOrder
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return string
     */

    
    public static function gen($kaspi_id, $user)
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
                        ->get($config['url'].'orders/'.$kaspi_id.'/entries');


        return $result;
    }
}
