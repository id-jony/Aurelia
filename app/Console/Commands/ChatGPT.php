<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GreenApi\RestApi\GreenApiClient;

use App\Api\Kaspi\MerchantGetProductPrice;
use App\Jobs\GetRivals;
use App\Models\Shop;



class ChatGPT extends Command
{
    protected $signature = 'ChatGPT:get';

    protected $description = 'Get KaspiApi Orders whith status';

    private array $config = [];


    public function __construct()
    {
        parent::__construct();
        $this->config = config('services.kaspi');
    }

    public function handle()
    {
        $this->info('Старт обновления цены продукта');
        $message = 
        'Действуй и думай как персонаж из книги Гарри-Поттера, ты Волон де Морт который работает в службе доставки, но не называй своего имени в сообщении. Представься сначала, напиши сообщение о том что заказ из интернет-магазина Aurelia уже в пути и скоро ты его привезешь. Придумай шутку на чем ты его привезешь. Добавь в текст немного смайлов для whatsapp. Раздели информацию на блоки что бы было удобнее читать. Обращайся к человеку по имени Анастасия. Используй номер заказа: 83224554.';

        // Отправка запроса к API модели ChatGPT
        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/completions', [
            'headers' => [
                'Authorization' => 'Bearer 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',
                'Content-Type' => 'application/json',
            ],

            'json' => [
                'prompt' => $message,
                'model' => "text-davinci-003",
                'max_tokens' => 1000, // Ограничение длины ответа
                'temperature' => 0.9, // "Творческость" ответов
                'top_p' => 1, // Вероятность для параметра top_p
                'n' => null, // Количество наиболее вероятных токенов для параметра n
                'frequency_penalty' => 0.0, // Частотный штраф
                'presence_penalty' => 0.0, // Штраф за наличие
                'stop' => null, // Строка или массив строк, чтобы указать модели, когда остановить генерацию
            ],

        ]);

        $data = json_decode($response->getBody(), true);
        $answer = $data['choices'][0]['text'];

         $greenApi = new GreenApiClient('1101814899', '019c1b14c8cb458ea0e61040fdfe8ddc178a50c480454faf81');
        // $whatsapp_check = $greenApi->serviceMethods->CheckWhatsapp($data->attributes->customer->cellPhone);
        $result = $greenApi->sending->sendMessage('77079150447@c.us', $answer);
        
    }
}
