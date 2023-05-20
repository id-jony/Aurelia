<?php

namespace App\Orchid\Screens\Order;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderShipment;

use App\Models\Rival;
use App\Models\Review;

use App\Models\KaspiSetting;
use App\Api\Kaspi\MerchantLogin;
use App\Api\Kaspi\UpdateProductPrice;
use App\Api\Kaspi\ConfirmOrder;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Sight;

use Orchid\Screen\Fields\Input;

use Illuminate\Support\Facades\Auth;

use Orchid\Screen\Actions\ModalToggle;

use Orchid\Support\Color;

class OrderViewScreen extends Screen
{
    public $order;


    public function query(Order $order): iterable
    {

        return [
            'order' =>  $order,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Заказ - ' . $this->order->code;
    }

    public function description(): ?string
    {
        return $this->order->brand;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Принять заказ')
                ->icon('eye')
                ->method('confirm'),


            Button::make('Отменить заказ')
                ->icon('trash')
                ->confirm('Как только продукт будет удален, все связанные данные будут удалены безвозвратно.')
                ->method('remove', [
                    'id' => $this->order->id,
            ]),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [

            Layout::legend('order', [
                Sight::make('status', 'Статус')
                    ->render(function (Order $order) {
                        foreach (Order::STATUS_VALUE as $key => $value) {
                            if ($key == $order->status) {
                                return $value;
                            }
                        }
                    }),

                Sight::make('creationDate', 'Дата поступления заказа')
                    ->render(fn ($item) => $item->creationDate),
                
                Sight::make('plannedDeliveryDate', 'Планируемая дата доставки заказа')
                    ->render(fn ($item) => $item->plannedDeliveryDate),

                Sight::make('totalPrice', 'Общая сумма')
                    ->render(function (Order $order) {
                        return number_format($order->totalPrice, 0, ',', ' ') . ' ₸';
                    }),

                Sight::make('totalPrice', 'Способ оплаты')
                    ->render(function (Order $order) {
                        foreach (Order::PAYMENT_VALUE as $key => $value) {
                            if ($key == $order->paymentMode) {
                                return $value;
                            }
                        }
                    }),

                Sight::make('totalPrice', 'Способ доставки')
                    ->render(function (Order $order) {
                        foreach (Order::DELIVERY_VALUE as $key => $value) {
                            if ($key == $order->deliveryMode) {
                                if ($order->isKaspiDelivery === 1 && $order->deliveryMode === 'DELIVERY_PICKUP') {
                                    return 'Kaspi постомат';
                                } else {
                                    return $value;
                                }
                            }
                        }
                    }),
                Sight::make('deliveryAddress', 'Адрес доставки'),
    
            ])->title('Информация о заказе'),
            
             Layout::legend('order', [
                Sight::make('name', 'Имя')
                    ->render(function (Order $order) {
                        return $order->customer->name;
                    }),

                Sight::make('phone', 'Телефон')
                    ->render(function (Order $order) {
                        return $order->customer->phone;
                    }),

            ])->title('Покупатель'),

            Layout::table('order.products', [
                     TD::make('image', 'Фото')
                    ->width('120px')
                    ->render(function (OrderShipment $order_product) {
                        return "<img width='64px' src='{$order_product->product->primaryImage}' alt='sample' class='d-block img-fluid'>";
                    }),
                    TD::make('name', 'Наименование')->sort()
                    ->render(function (OrderShipment $order_product) {
                        return Link::make(mb_strimwidth($order_product->product->name, 0, 60, '...'))
                            ->route('platform.product.view', $order_product);
                    }),
                    TD::make('quantity', 'Кол-во')->sort()
                    ->render(function (OrderShipment $order_product) {
                        return $order_product->quantity . ' шт.';
                    }),
                    TD::make('price', 'Цена')->sort()
                            ->render(function (OrderShipment $order_product) {
                        return $order_product->price;
                    }),
                 
                
                ])->title('Состав заказа'),
            

        ];
    }

    
    public function confirm(Product $product)
    {
        // $status = 'ACCEPTED_BY_MERCHANT';
        // $confirm = ConfirmOrder::update($product->kaspi_id, $product->code, $status, Auth::user()->id);
                Toast::info('Событие успешно удалено');


    }

    public function remove(Request $request, Product $product): void
    {
        $status = 'ACCEPTED_BY_MERCHANT';
        $confirm = ConfirmOrder::update($product->kaspi_id, $product->code, $status, Auth::user()->id);
        Toast::info('Событие успешно удалено');
    }
}
