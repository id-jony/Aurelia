<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

use App\Helpers\Kaspi\GetOrders;
use App\Helpers\Kaspi\GetOrder;
use App\Helpers\Kaspi\GetProduct;
use App\Models\KaspiSetting;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class GetKaspiApi extends Command
{
    protected $signature = 'getorders:status {--status=} {--page_number=} {--page_size=} {--user=}';

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
        $user = $this->option('user');
        $setting = KaspiSetting::where('user_id', $user)->first();


        for ($i=0; $i < $setting->count_day;$i = $i + $setting->interval_day) {
            $this->info($i. '- дней');

            $start_date = strtotime(Carbon::now()->subDays($setting->interval_day+$i)->format('d.m.Y H:i')) * 1000;
            $end_date = strtotime(Carbon::now()->subDays(0+$i)->format('d.m.Y H:i')) * 1000;
            $page_number = $this->option('page_number');
            $page_size = $this->option('page_size');


            // // Получить список заказов
            $getOrders = GetOrders::gen($page_number, $page_size, $this->option('status'), $start_date, $end_date, $user);

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

                // Новый заказ
                if ($order === null) {
                    
                    $this->info('Найден новый заказ');

                    $products = collect();
                    $getOrder = GetOrder::gen($data->id, $user);

                    // Получаем список заказанных продуктов
                    foreach (json_decode($getOrder)->data as $order_data) {
                        $getProduct = GetProduct::gen($order_data->id, $user);
                        $product = Product::where('sku', json_decode($getProduct)->data->attributes->code)->first();

                        if ($product === null) {
                            $product = Product::create([
                                'type' => json_decode($getProduct)->data->type ?? '',
                                'kaspi_id' => json_decode($getProduct)->data->id ?? '',
                                'name' => json_decode($getProduct)->data->attributes->name ?? '',
                                'sku' => json_decode($getProduct)->data->attributes->code ?? '',
                                'category' => $order_data->attributes->category->code ?? '',
                                'basePrice' => $order_data->attributes->basePrice ?? '',
                            ]);

                            $this->info('Найден новый продукт');
                        }

                        $array = array(
                            "id" => $product->id,
                            "quantity" => $order_data->attributes->quantity,
                        );

                        $products->push($array);
                    }
                    
                    $order = Order::create([
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
                        // 'transmissionDate' => date("Y-m-d H:i:s", $data->attributes->kaspiDelivery->courierTransmissionPlanningDate / 1000) ?? '',
                        // 'plannedDeliveryDate' => date("Y-m-d H:i:s", $data->attributes->plannedDeliveryDate / 1000) ,
                        'products' => $products,

                    ]);


                    foreach (Order::DELIVERY_VALUE as $key => $value) {
                        if ($key === $order->deliveryMode) {
                            if ($order->isKaspiDelivery === 1 && $order->deliveryMode === 'DELIVERY_PICKUP') {
                                $order->deliveryMode = 'Kaspi постомат';
                            } else {
                                $order->deliveryMode = $value;
                            }
                        }
                    }

                    foreach (Order::STATE_VALUE as $key => $value) {
                        if ($key === $order->state) {
                            $order->state = $value;
                        }
                    }

                    $chat = TelegraphChat::find(1);
                    $chat->html(view('telegram.new_order', [
                        'order' => $order
                        ]))
                        ->keyboard(Keyboard::make()->buttons([
                            // Button::make("👀 Скачать накладную")->url($data->attributes->kaspiDelivery->waybill),
                        ])->chunk(2))->send();
                        
                } else {

                    
                    $this->info('Проверка обновлений заказов');
                    $products = collect();
                    $getOrder = GetOrder::gen($data->id, $user);

                    foreach (json_decode($getOrder)->data as $order_data) {
                        $getProduct = getProduct::gen($order_data->id, $user);
                        $product = Product::where('sku', json_decode($getProduct)->data->attributes->code)->first();

                        $product_array = array(
                            "id" => $product->id,
                            "quantity" => $order_data->attributes->quantity,
                        );
                        $products->push($product_array);
                    }

                    // if ($order->status != $data->attributes->status || $order->state != $data->attributes->state ) {
                    $order->status = $data->attributes->status ?? '';
                    $order->state = $data->attributes->state ?? '';
                    $chat = TelegraphChat::find(1);

                    // $buttons = [
                    //     Button::make("👀 Скачать накладную")->url($data->attributes->kaspiDelivery->waybill),
                    // ];

                    $order->save();

                    foreach (Order::DELIVERY_VALUE as $key => $value) {
                        if ($key === $order->deliveryMode) {
                            if ($order->isKaspiDelivery === 1 && $order->deliveryMode === 'DELIVERY_PICKUP') {
                                $order->deliveryMode = 'Kaspi постомат';
                            } else {
                                $order->deliveryMode = $value;
                            }
                        }
                    }

                    foreach (Order::STATE_VALUE as $key => $value) {
                        if ($key === $order->state) {
                            $order->state = $value;
                        }
                    }

                    // $chat->html(view('telegram.update_order', [
                    //     'order' => $order
                    //     ]))
                    //     ->keyboard(Keyboard::make()->buttons($buttons)->chunk(2))->send();
                    // }



                }
            }

            $this->info('Импорт завершен');
        }
    }
}
