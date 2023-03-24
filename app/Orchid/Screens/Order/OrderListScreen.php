<?php

namespace App\Orchid\Screens\Order;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Order;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;


class OrderListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        $total_count = number_format(Order::whereNot('status', 'CANCELLED')->sum('totalPrice'), 0, ',', ' ') . ' ₸' ;
        $avg_count = number_format(Order::whereNot('status', 'CANCELLED')->avg('totalPrice'), 0, ',', ' ') . ' ₸' ;
        $order_count = number_format(Order::whereNot('status', 'CANCELLED')->count('totalPrice'), 0, ',', ' ');
        
        return [
            'items' => Order::query()->orderBy('creationDate', 'desc')->paginate(),
            'total_count' => $total_count,
            'avg_count' => $avg_count,
            'order_count' => $order_count,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Заказы покупателей';
    }

    public function description(): ?string
    {
        return 'Заказы покупателей позволяют планировать продажи.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [

            Layout::metrics([
                'Сумма продаж' => 'total_count',
                'Средний чек' => 'avg_count',
                'Кол-во заказов' => 'order_count',
                // 'Дата завершения' => 'event_finish',

            ]),

            Layout::table('items', [
                TD::make('id', 'ID')->sort(),
                TD::make('type', 'Тип')->sort()->defaultHidden(),
                TD::make('kaspi_id', 'ID Каспи')->sort()->defaultHidden(),
                TD::make('code', 'Код заказа')->sort(),
                TD::make('state', 'Cостояние')->sort()
                    ->render(function (Order $order) {
                        foreach (Order::STATE_VALUE as $key => $value) {
                            if ($key == $order->state) {
                                return $value;
                            }
                        }
                    }),
                TD::make('status', 'Статус')->sort()
                    ->render(function (Order $order) {
                        foreach (Order::STATUS_VALUE as $key => $value) {
                            if ($key == $order->status) {
                                return $value;
                            }
                        }
                    }),
                TD::make('totalPrice', 'Общая сумма')->sort()
                    ->render(function (Order $order) {
                        return number_format($order->totalPrice, 0, ',', ' ') . ' ₸';
                    }),
                TD::make('paymentMode', 'Способ оплаты')->sort()
                    ->render(function (Order $order) {
                        foreach (Order::PAYMENT_VALUE as $key => $value) {
                            if ($key == $order->paymentMode) {
                                return $value;
                            }
                        }
                    }),
                TD::make('deliveryCostForSeller', 'Комиссия за доставку')->width('150px')->sort()
                    ->render(function (Order $order) {
                        return number_format($order->deliveryCostForSeller, 0, ',', ' ') . ' ₸';
                    }),
                TD::make('isKaspiDelivery', ' Kaspi Доставка')->sort()
                    ->render(function (Order $order) {
                        if ($order->isKaspiDelivery === 0) {
                            return 'Нет';
                        } else {
                            return 'Да';
                        }
                    }),

                TD::make('deliveryCost', 'Стоимость доставки')->sort()
                    ->render(function (Order $order) {
                        return number_format($order->deliveryCost, 0, ',', ' ') . ' ₸';
                    }),
                
                TD::make('deliveryMode', 'Способ доставки')->sort()
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
                TD::make('waybill', 'Накладная kaspi')->sort()
                    ->render(function (Order $order) {
                        if ($order->waybill != NULL) {
                            return Link::make('Скачать')->href($order->waybill);
                        } else {
                            return '-';
                        }
                    }),
                TD::make('customer_id', 'Покупатель')->sort()
                    ->render(function (Order $order) {
                        return $order->customer->name;
                    }),

                // TD::make('pickupPointId', ''),
                TD::make('deliveryAddress', 'Адрес доставки')->sort(),

                TD::make('creditTerm', 'Срок кредита')->sort(),

                TD::make('signatureRequired', 'Подписание кредита')->sort()
                ->render(function (Order $order) {
                    if ($order->signatureRequired === 0) {
                        return 'Нет';
                    } else {
                        return 'Да';
                    }
                }),

                TD::make('preOrder', 'Предзаказ')->sort()
                    ->render(function (Order $order) {
                        if ($order->preOrder === 0) {
                            return 'Нет';
                        } else {
                            return 'Да';
                        }
                    }),

                TD::make('creationDate', 'Дата создания заказа')->sort()
                    ->render(fn ($item) => $item->creationDate->format('d.m.Y H:i')),
                TD::make('Импортированно')->sort()
                    ->render(fn ($item) => $item->created_at->format('d.m.Y H:i')),
                TD::make('Опции')
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->cantHide()
                    ->render(function ($item) {
                        return DropDown::make()
                            ->icon('options-vertical')
                            ->list([
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->confirm('Вы уверены в этом?')
                                    ->method('remove', [
                                        'id' => $item->id
                                    ]),
                            ]);
                    }),
            ]),

        ];
    }



    public function remove(Request $request): void
    {
        Order::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }
}
