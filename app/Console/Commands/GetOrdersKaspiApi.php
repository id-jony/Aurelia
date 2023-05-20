<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

use App\Api\Kaspi\GetOrders;
use App\Api\Kaspi\GetOrder;
use App\Api\Kaspi\GetProduct;
use App\Models\KaspiSetting;

use App\Notifications\KaspiInfo;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use GreenApi\RestApi\GreenApiClient;

class GetOrdersKaspiApi extends Command
{
    protected $signature = 'getorders:status {--user=} {--status=} {--page_number=} {--page_size=}';
    protected $description = 'Get KaspiApi Orders whith status';

    public function handle()
    {
        $this->info('Старт импорт заказов Kaspi');

        $user = User::find($this->option('user'));
        $setting = KaspiSetting::where('user_id', $user->id)->first();
        $status = $this->option('status');
        $page_number = $this->option('page_number');
        $page_size = $this->option('page_size');

        for ($i = 0; $i < $setting->count_day; $i = $i + $setting->interval_day) {

            // Получаем интервал дат импорта
            $this->info('Проверено'. $i. '- дней');
            $start_date = strtotime(Carbon::now()->subDays($setting->interval_day + $i)->format('d.m.Y H:i')) * 1000;
            $end_date = strtotime(Carbon::now()->subDays(0 + $i)->format('d.m.Y H:i')) * 1000;

            // Получаем список заказов
            $orders = GetOrders::gen($page_number, $page_size, $status, $start_date, $end_date, $user->id);
            foreach ($orders->data as $data) {

                // Проверяем покупателей если нет в базе добавляем
                $customer = Customer::where('kaspi_id', $data->attributes->customer->id)->first();
                if ($customer === null) {
                    // $greenApi = new GreenApiClient('1101814899', '019c1b14c8cb458ea0e61040fdfe8ddc178a50c480454faf81');
                    // $whatsapp_check = $greenApi->serviceMethods->CheckWhatsapp($data->attributes->customer->cellPhone);

                    $customer = new Customer();
                    $customer->kaspi_id = $data->attributes->customer->id;
                    $customer->name = $data->attributes->customer->lastName . ' ' . $data->attributes->customer->firstName ?? '';
                    $customer->phone = $data->attributes->customer->cellPhone ?? '';
                    $customer->town = $data->attributes->deliveryAddress->town ?? '';
                    // $customer->whatsapp = $whatsapp_check;
                    $customer->user_id = $user->id;

                    $user->notify(new KaspiInfo('Найден новый покупатель', $customer->name, '#'));
                } elseif ($customer->phone != $data->attributes->customer->cellPhone ) {
                    $customer->name = $data->attributes->customer->lastName . ' ' . $data->attributes->customer->firstName ?? '';
                    $customer->phone = $data->attributes->customer->cellPhone ?? '';
                    $customer->town = $data->attributes->deliveryAddress->town ?? '';
                    $user->notify(new KaspiInfo('Обновлен покупатель', $customer->name, '#'));
                }
                $customer->save();

                // ---------------------------

                // Проверяем наличие заказа
                $order = Order::where('kaspi_id', $data->id)->first();
                if ($order === null) {
                    $order = new Order();
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
                    $order->creationDate = date("Y-m-d H:i:s", $data->attributes->creationDate / 1000) ?? '';
                    $order->transmissionDate = $data->attributes->kaspiDelivery->courierTransmissionPlanningDate ?? null;
                    $order->plannedDeliveryDate =  $data->attributes->plannedDeliveryDate ?? null;
                    $order->user_id = $user->id;
                    $this->info('Найден новый заказ');
                    $user->notify(new KaspiInfo('Найден новый заказ', $order->code, route('platform.order.view', $order->id)));
                } elseif ($order->status != $data->attributes->status || $order->state != $data->attributes->state) {
                    $order->status = $data->attributes->status;
                    $order->state = $data->attributes->state;
                    $order->updated_at = now();
                    $user->notify(new KaspiInfo('Обновлен заказ', $order->code, route('platform.order.view', $order->id)));
                }
                $order->save();
                // ---------------------------

                // Получаем список заказанных товаров
                $getOrder = GetOrder::gen($data->id, $user->id);
                foreach ($getOrder->data as $order_data) {

                    // Проверяем товар
                    $getProduct = GetProduct::gen($order_data->id, $user->id);
                    $product = Product::where('master_sku', $getProduct->data->attributes->code)->first();
                    if ($product === null) {
                        $product = new Product();
                        $product->name = $getProduct->data->attributes->name;
                        $product->master_sku = $getProduct->data->attributes->code;
                        $customer->user_id = $user->id;
                        $user->notify(new KaspiInfo('Найден новый продукт', $product->name, route('platform.product.view', $product->id)));
                    }
                    $product->save();
                    // ---------------------------

                    // Проверяем список товаров в заказе
                    $order_product = OrderShipment::where('order_id', $order->id)->where('product_id', $product->id)->first();
                    if ($order_product === null) {
                        $order_product = new OrderShipment();
                        $order_product->order_id = $order->id;
                        $order_product->product_id = $product->id;
                        $order_product->kaspi_id = $order_data->id;
                        $order_product->price = $order_data->attributes->basePrice;
                        $order_product->quantity = $order_data->attributes->quantity;

                        $user->notify(new KaspiInfo('Новый товар в заказе ', $product->name . ' - ' . $order->code, route('platform.order.view', $order->id)));
                    } elseif ($order_product->price != $order_data->attributes->basePrice || $order_product->quantity != $order_data->attributes->quantity) {
                        $order_product->price = $order_data->attributes->basePrice;
                        $order_product->quantity = $order_data->attributes->quantity;

                        $user->notify(new KaspiInfo('Обновлен товар в заказе ', $product->name . ' - ' . $order->code, route('platform.order.view', $order->id)));
                    }
                    $order_product->save();
                    // -------------------------------------

                }
            }

            $this->info('Импорт завершен');
        }
    }
}
