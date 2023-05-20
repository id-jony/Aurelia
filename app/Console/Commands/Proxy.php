<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Models\KaspiSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rival;
use App\Models\User;
use App\Models\Customer;

use App\Api\Proxy\GetProxy;
use App\Api\Kaspi\MerchantGetProduct;
use App\Api\Kaspi\UpdateProductPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Notifications\KaspiInfo;
use GreenApi\RestApi\GreenApiClient;

class Proxy extends Command
{
    protected $signature = 'get:proxy';

    protected $description = 'Get Proxy';

    private array $config = [];


    public function __construct()
    {
        parent::__construct();
        $this->config = config('services.kaspi');
    }

    public function handle()
    {

        $customers = Customer::where('whatsapp', 0)->get();
        $greenApi = new GreenApiClient('1101814899', '019c1b14c8cb458ea0e61040fdfe8ddc178a50c480454faf81');

        foreach ($customers as $customer) {
            $result = $greenApi->serviceMethods->CheckWhatsapp($customer->phone);
            $this->info($result->data->existsWhatsapp);

            // $customer->whatsapp = $result->data->existsWhatsapp;
            // $customer->save();

        }
        // $proxy_data = GetProxy::gen();
        // $this->info($proxy_data);
        // $greenApi = new GreenApiClient('1101814899', '019c1b14c8cb458ea0e61040fdfe8ddc178a50c480454faf81');
        // $result = $greenApi->sending->sendMessage('77023199635@c.us', 'Message text');
        // $greenApi = new GreenApiClient('1101814899', '019c1b14c8cb458ea0e61040fdfe8ddc178a50c480454faf81');
        // $result = $greenApi->serviceMethods->CheckWhatsapp('7052664787');
        // $this->info($result->data->existsWhatsapp);

    }
}
