<?php

namespace App\Orchid\Layouts\Order;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;

use App\Models\Order;

class NewOrderListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'new_orders';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
                TD::make('id', 'ID')->sort()->defaultHidden(),
                TD::make('type', 'Тип')->sort()->defaultHidden(),
                TD::make('kaspi_id', 'ID Каспи')->sort()->defaultHidden(),
                TD::make('code', 'Код заказа')->sort()
                    ->render(function (Order $order) {
                        return Link::make($order->code)
                            ->route('platform.order.view', $order);
                    }),
                TD::make('state', 'Cостояние')->sort()->defaultHidden()
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
                TD::make('deliveryCostForSeller', 'Комиссия за доставку')->sort()
                    ->render(function (Order $order) {
                        return number_format($order->deliveryCostForSeller, 0, ',', ' ') . ' ₸';
                    }),
                TD::make('isKaspiDelivery', ' Kaspi Доставка')->sort()->defaultHidden()
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
                        if ($order->waybill != null) {
                            return Link::make('Скачать')->href($order->waybill);
                        } else {
                            return '-';
                        }
                    }),
                TD::make('customer_id', 'Покупатель')->sort()
                    ->render(function (Order $order) {
                        return Link::make($order->customer->name)->href('#');
                    }),

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
                    ->render(fn ($item) => $item->updated_at->format('d.m.Y H:i')),
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

        ];
    }
}
