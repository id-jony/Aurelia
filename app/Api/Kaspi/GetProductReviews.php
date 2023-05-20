<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Client;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Cookie\CookieJar;
use App\Models\Town;
use App\Models\Proxy;
use App\Models\User;

use App\Notifications\KaspiInfo;

class GetProductReviews
{
    /**
     * Получение списка заказов
     *
     * @param integer $lenght
     * @return object
     */


    public static function gen($sku)
    {
        $result = '';
        $config = config('services.kaspi');
        $user = User::find(1);
        $proxies = Proxy::where('status', 1)->inRandomOrder()->get();
        $headers = [
                        'Content-Type' => 'application/json',
                        'Referer' => 'https://kaspi.kz/',
                        'User-Agent' => 'Macintosh; OS X/13.1.0',
                    ];

        // foreach ($proxies as $proxy) {
            try {
            $result = Http::withHeaders($headers)
                            ->withOptions([
                                'debug' => $config['debug'],
                                'allow_redirects' => false,
                                // 'proxy' => $proxy->protocol . $proxy->ip .':'. $proxy->port,
                                'proxy' => 'socks5://jekajecka7755:87303087@89.219.34.157:10602',
                                'timeout' => 0,
                                'connect_timeout' => 0,
                                'verify' => false,
                                ])
                            ->accept('application/json')
                            ->get('https://kaspi.kz/yml/creview/rest/misc/product/'.$sku.'/reviews?limit=100&page=0&id=all');


            if($result->successful()) {
                return json_decode($result->body());
            } 
            
            } catch (ConnectionException $err ) {
                // $proxy->status = 0;
                // $proxy->save();

                // $user->notify(new KaspiInfo('Прокси', 'Прокси отключен:'. $proxy->ip, ''));
            } catch (RequestException $error) {
                // $proxy->status = 0;
                // $proxy->save();

                // $user->notify(new KaspiInfo('Прокси', 'Прокси отключен:'. $proxy->ip, ''));
            }
        }

    // }
}
