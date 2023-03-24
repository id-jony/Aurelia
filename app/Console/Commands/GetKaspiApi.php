<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class GetKaspiApi extends Command
{

    protected $signature = 'getorders:status {--status=} {--days_number=} {--page_number=} {--page_size=}';

    protected $description = 'Get KaspiApi Orders whith status';

    private array $config = [];


    public function __construct()
    {
        parent::__construct();
        $this->config = config('services.kaspi');
    }


    public function handle()
    {
    $this->info('Старт импорт заказов Kaspi');


    for ($i=0; $i < $this->option('days_number'); $i = $i + 14) {
            $this->info($i. '- дней');


        $start_date = strtotime(Carbon::now()->subDays(14+$i)->format('d.m.Y')) * 1000;
        $end_date = strtotime(Carbon::now()->subDays(0+$i)->format('d.m.Y')) * 1000;
        $page_number = $this->option('page_number');
        $page_size = $this->option('page_size');

        $headers = [
            'Content-Type' => 'application/vnd.api+json',
            'X-Auth-Token' => $this->config['api_token'],
        ];

        // Получить список заказов
        $getOrders = Http::withHeaders($headers)
        ->withOptions(['debug' => false,])
        ->accept('application/vnd.api+json')
        ->get($this->config['url'].'orders/', [
            'page[number]' => $page_number,
            'page[size]' => $page_size,
            'filter[orders][state]' => $this->option('status'),
            'filter[orders][creationDate][$ge]' => $start_date,
            'filter[orders][creationDate][$le]' => $end_date,
            'include[orders]' => 'user',
        ]);

        // Разбираем список заказов
        foreach (json_decode($getOrders)->data as $data) {
            $customer = Customer::where('kaspi_id', $data->attributes->customer->id)->first();

            if ($customer === null) {
                $customer = Customer::create([
                    'kaspi_id' => $data->attributes->customer->id ?? '',
                    'name' => $data->attributes->customer->lastName . ' ' . $data->attributes->customer->firstName ?? '',
                    'phone' => $data->attributes->customer->cellPhone ?? '',
                    'town' => $data->attributes->deliveryAddress->town ?? '',
                ]);
                $this->info('Найден новый покупатель');
            }
            
            // Проверяем наличие заказа в базе
            $order = Order::where('kaspi_id', $data->id)->first();

            if ($order === null) {
                $this->info('Найден новый заказ');

                $products = collect();

                $getOrder = Http::withHeaders($headers)
                    ->withOptions(['debug' => false,])
                    ->accept('application/vnd.api+json')
                    ->get($this->config['url'].'orders/'.$data->id.'/entries');

                // Получаем список заказанных продуктов
                foreach (json_decode($getOrder)->data as $order_data) {
                    $getProduct = Http::withHeaders($headers)
                            ->withOptions(['debug' => false,])
                            ->accept('application/vnd.api+json')
                            ->get($this->config['url'].'orderentries/'.$order_data->id.'/product');

                    $product = Product::where('code', json_decode($getProduct)->data->attributes->code)->first();

                    if ($product === null) {
                        $this->info('Найден новый продукт');

                        $product = Product::create([
                            'type' => json_decode($getProduct)->data->type ?? '',
                            'kaspi_id' => json_decode($getProduct)->data->id ?? '',
                            'name' => json_decode($getProduct)->data->attributes->name ?? '',
                            'code' => json_decode($getProduct)->data->attributes->code ?? '',
                            'category' => $order_data->attributes->category->code ?? '',
                            'basePrice' => $order_data->attributes->basePrice ?? '',
                        ]);
                    }

                    $prod = array(
                        "id" => $product->id,
                        "quantity" => $order_data->attributes->quantity,
                    );

                    $products->push($prod);
                }

                Order::create([
                    'type' => $data->type ?? '',
                    'kaspi_id' => $data->id ?? '',
                    'code' => $data->attributes->code ?? '',
                    'totalPrice' => $data->attributes->totalPrice ?? '',
                    'paymentMode' => $data->attributes->paymentMode ?? '',
                    'deliveryCostForSeller' => $data->attributes->deliveryCostForSeller ?? '',
                    'isKaspiDelivery' => $data->attributes->isKaspiDelivery ?? '',
                    'signatureRequired' => $data->attributes->signatureRequired ?? '',
                    'deliveryMode' => $data->attributes->deliveryMode ?? '',
                    'creditTerm' => $data->attributes->creditTerm ?? '0',
                    'waybill' => $data->attributes->kaspiDelivery->waybill ?? '',
                    'state' => $data->attributes->state ?? '',
                    'status' => $data->attributes->status ?? '',
                    'customer_id' => $customer->id ?? '',
                    'preOrder' => $data->attributes->preOrder ?? '',
                    'pickupPointId' => $data->attributes->pickupPointId ?? '',
                    'deliveryAddress' => $data->attributes->deliveryAddress->formattedAddress ?? '',
                    'deliveryCost' => $data->attributes->deliveryCost ?? '',
                    'creationDate' => date("Y-m-d H:i:s", $data->attributes->creationDate / 1000),
                    'products' => $products,

                ]);
            } else {
                $this->info('Обновлен заказ');

                $products = collect();

                $getOrder = Http::withHeaders($headers)
                    ->withOptions(['debug' => false,])
                    ->accept('application/vnd.api+json')
                    ->get('https://kaspi.kz/shop/api/v2/orders/'.$data->id.'/entries');

                foreach (json_decode($getOrder)->data as $order_data) {
                    $getProduct = Http::withHeaders($headers)
                            ->withOptions(['debug' => false,])
                            ->accept('application/vnd.api+json')
                            ->get('https://kaspi.kz/shop/api/v2/orderentries/'.$order_data->id.'/product');

                    $product = Product::where('code', json_decode($getProduct)->data->attributes->code)->first();

                    if ($product === null) {
                        $this->info('Найден новый продукт');

                        $product = Product::create([
                            'type' => json_decode($getProduct)->data->type ?? '',
                            'kaspi_id' => json_decode($getProduct)->data->id ?? '',
                            'name' => json_decode($getProduct)->data->attributes->name ?? '',
                            'code' => json_decode($getProduct)->data->attributes->code ?? '',
                            'category' => $order_data->attributes->category->code ?? '',
                            'basePrice' => $order_data->attributes->basePrice ?? '',

                        ]);
                    }

                    $prod = array(
                        "id" => $product->id,
                        "quantity" => $order_data->attributes->quantity,
                    );

                    $products->push($prod);
                }

                $order->type = $data->type ?? '';
                $order->kaspi_id = $data->id ?? '';
                $order->code = $data->attributes->code ?? '';
                $order->totalPrice = $data->attributes->totalPrice ?? '';
                $order->paymentMode = $data->attributes->paymentMode ?? '';
                $order->deliveryCostForSeller = $data->attributes->deliveryCostForSeller ?? '';
                $order->isKaspiDelivery = $data->attributes->isKaspiDelivery ?? '';
                $order->signatureRequired = $data->attributes->signatureRequired ?? '';
                $order->deliveryMode = $data->attributes->deliveryMode ?? '';
                $order->creditTerm = $data->attributes->creditTerm ?? '0';
                $order->waybill = $data->attributes->kaspiDelivery->waybill ?? '';
                $order->state = $data->attributes->state ?? '';
                $order->status = $data->attributes->status ?? '';
                $order->customer_id = $customer->id ?? '';
                $order->preOrder = $data->attributes->preOrder ?? '';
                $order->pickupPointId = $data->attributes->pickupPointId ?? '';
                $order->deliveryAddress = $data->attributes->deliveryAddress->formattedAddress ?? '';
                $order->deliveryCost = $data->attributes->deliveryCost ?? '';
                $order->creationDate = date("Y-m-d H:i:s", $data->attributes->creationDate / 1000);
                $order->products = $products;
                $order->save();
            }
        }


        $this->info('Импорт завершен');
    }
}
}
