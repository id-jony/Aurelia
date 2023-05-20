<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductMerchant;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Proxy;
use App\Models\Review;
use App\Models\PriceHistory;

use App\Orchid\Layouts\Product\ProductPriceChart;
use App\Orchid\Layouts\Product\ProductOrderChart;

use App\Models\KaspiSetting;
use App\Api\Kaspi\MerchantLogin;
use App\Api\Kaspi\UpdateProductPrice;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Sight;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Support\Facades\DB;

class ProductViewScreen extends Screen
{
    public $product;


    public function query(Product $product): iterable
    {

        $answers = PriceHistory::where('product_id', $product->id)->select(DB::raw('(DATE_FORMAT(price_history.created_at,"%Y-%m-%d %H:%i")) as date'), DB::raw('sum(price_history.price) as count'))
        ->groupBy('date')->orderBy('date', 'asc')->get();

        $labels = []; $values = [];

        foreach ($answers as $answer) {
            array_push($labels, $answer->date);
            array_push($values, $answer->count);
        }

        $ProductPriceChart = [['labels' => $labels, 'values' => $values]];


        $answers = PriceHistory::where('product_id', $product->id)->select(DB::raw('(DATE_FORMAT(price_history.created_at,"%Y-%m-%d %H:%i")) as date'), DB::raw('sum(price_history.price) as count'))
        ->groupBy('date')->orderBy('date', 'asc')->get();

        $labels = []; $values = [];

        foreach ($answers as $answer) {
            array_push($labels, $answer->date);
            array_push($values, $answer->count);
        }

        $ProductPriceChart = [['labels' => $labels, 'values' => $values]];


        
        $customer = collect();
        $orders_id = collect();
        $setting = KaspiSetting::where('user_id', Auth::user()->id)->first();

        foreach (Order::where('status', 'COMPLETED')->get() as $order) {
            foreach ($order->products as $product_data) {
                if ($product_data->product_id == $product->id) {
                    $customer->push($order->customer_id);
                    $orders_id->push($order->id);
                }
            }
        }



        $customers = Customer::whereIn('id', $customer)->get();
        $orders = Order::whereIn('id', $orders_id)->get();

        $answers3 = Order::whereIn('id', $orders_id)->select(DB::raw('(DATE_FORMAT(orders.creationDate,"%Y-%m-%d %H:%i")) as date'), DB::raw('count(orders.id) as count'))
        ->groupBy('date')->orderBy('date', 'asc')->get();

        $labels = []; $values = [];

        foreach ($answers3 as $answer) {
            array_push($labels, $answer->date);
            array_push($values, $answer->count);
        }

        $ProductOrderChart = [['labels' => $labels, 'values' => $values]];

        foreach ($orders as $order) {
            foreach ($order->products as $product_data) {
                if ($product->id === $product_data->product_id) {
                    $order->price_offer = $product_data->price;
                }
            }
        }
        $price_rec = $product->price_cost * 4 ?? 0;
        $price_clear =  $price_rec - ($price_rec * $setting->percent_sales / 100) - $product->price_cost ?? 0;
        $money = $product->sum_money - $product->sum_money * $setting->percent_sales / 100 ?? 0;
        // $profitability = ($money - $product->price_cost) / $product->sum_money * 100 ?? 0;

        return [
            'product' => $product,
            'total_count' => $product->count,
            'customers' => $customers,
            'sum_money' => number_format($product->sum_money, 0, ',', ' ') . ' ₸',
            'money' => number_format($money, 0, ',', ' ') . ' ₸',
            'price' => number_format($product->priceBase, 0, ',', ' ') . ' ₸',
            'orders' => $orders,
            'price_cost' => number_format($product->price_cost, 0, ',', ' ') . ' ₸',
            'price_rec' => number_format($price_rec, 0, ',', ' ') . ' ₸',
            'price_clear' => number_format($price_clear, 0, ',', ' ') . ' ₸',
            // 'profitability' => number_format($profitability, 1, ',', ' ') . ' %',
            'ProductPriceChart' => $ProductPriceChart,
            'ProductOrderChart' => $ProductOrderChart,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->product->name;
    }

    public function description(): ?string
    {
        return 'Обновлено ' . $this->product->updated_at->format('d.m.Y H:i');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Открыть на Kaspi.kz')
                ->href($this->product->productUrl)
                ->icon('eye'),

            ModalToggle::make('Изменить цену')
                ->modal('update_price')
                ->method('update_price')
                ->icon('money'),

            Button::make(__('Delete'))
                ->icon('trash')
                ->confirm('Как только продукт будет удален, все связанные данные будут удалены безвозвратно.')
                ->method('remove', [
                    'id' => $this->product->id,
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
            Layout::columns([
                Layout::metrics([
                    'Актуальная цена' => 'price',
                    'Оптимальная цена' => 'price_rec',
                    'Чистый доход' => 'price_clear',
                    'Себестоимость' => 'price_cost',
                ])->title('Информация о цене'),
                Layout::metrics([
                    'Кол-во продаж' => 'total_count',
                    'Сумма продаж' => 'sum_money',
                    'Чистая прибыль' => 'money',
                    // 'Рентабельность' => 'profitability',
                ])->title('Информация о продажах'),

            ]),
            // Layout::columns([
            //     Layout::legend('product', [
            //         Sight::make('brand', 'Бренд'),
            //         Sight::make('offerStatus', 'Статус')
            //             ->render(function (Product $product) {
            //                 return $product->offerStatus;
            //             }),

            //     ]),

            //     // Layout::legend('product', [
            //     //     Sight::make('updated_at', 'Обновлено')
            //     //         ->render(fn ($item) => $item->updated_at->format('d.m.Y H:i')),

            //     // ]),
            // ]),

            Layout::columns([
                ProductPriceChart::class,
                // ProductOrderChart::class,
            ]),
            
            Layout::tabs([
                'Список конкурентов' => Layout::table('product.rivals', [
                    TD::make('merchantId', 'ID')
                        ->render(fn (ProductMerchant $product) => $product->rival->merchantId),
                    TD::make('merchantName', 'Магазин')
                        ->render(fn (ProductMerchant $product) => $product->rival->merchantName),
                    TD::make('merchantRating', 'Рейтинг')
                        ->render(fn (ProductMerchant $product) => $product->rival->merchantRating),
                    TD::make('rival.merchantReviewsQuantity', 'Кол-во отзывов')
                        ->render(fn (ProductMerchant $product) => $product->rival->merchantReviewsQuantity),
                    TD::make('price', 'Стоимость'),
                    TD::make('deliveryDuration', 'Доставка'),
                    TD::make('delivery_at', 'Дата доставки'),
                ]),
                'Список заказов' => Layout::table('orders', [
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
                    TD::make('price_offer', 'Стоимость')->sort()
                        ->render(function (Order $order) {
                            return number_format($order->price_offer, 0, ',', ' ') . ' ₸';
                        }),

                    TD::make('creationDate', 'Дата создания заказа')->sort()
                        ->render(fn ($item) => $item->creationDate->format('d.m.Y H:i')),

                ]),
                'Список покупателей' => Layout::table('customers', [
                    TD::make('name', 'Имя'),
                    TD::make('phone', 'Телефон'),
                    TD::make('town', 'Город'),

                ]),
                'История изменения цены' => Layout::table('product.prices', [
                    TD::make('rival_id', 'Изменил')
                        ->render(function (PriceHistory $history) {
                            if ($history->rival_id != null) {
                                return $history->rival->merchantName;
                            } elseif ($history->user_id === 0) {
                                return 'Робот';
                            } else {
                                return $history->user->name;
                            }
                        }),
                    TD::make('price', 'Цена')
                        ->render(function (PriceHistory $history) {
                            return number_format($history->price, 0, ',', ' ') . ' ₸';
                        }),
                    TD::make('comment', 'Комментарий'),
                    TD::make('created_at', 'Дата изменения'),
                ]),
                'Отзывы товара' => Layout::table('product.reviews', [
                    TD::make('consumer', 'Покупатель')
                        ->render(function (Review $review) {
                            if ($review->customer_id != null) {
                                return $review->customer_id;
                            } else {
                                return $review->customer_author;
                            }
                        }),

                    TD::make('photo', 'Фото')
                        ->render(function (Review $review) {
                            if ($review->photo != null) {
                                return "<img width='64px' src='{$review->photo}' alt='sample' class='d-block img-fluid'>";
                            }
                        }),

                    TD::make('rating', 'Рейтинг'),
                    TD::make('Review', 'Текст'),
                    // TD::make('plus', 'Город'),
                    // TD::make('minus', 'Город'),
                    TD::make('date', 'Добавлен'),

                ]),
            ]),


            Layout::modal('update_price', [
                Layout::rows([
                    Input::make('product.priceBase')
                        ->type('text')
                        ->max(255)
                        ->title('Актуальная цена')
                        ->placeholder('Введите цену')
                        ->mask([
                            'alias' => 'currency',
                            'suffix' => ' ₸ ',
                            'groupSeparator' => ' ',
                            'digitsOptional' => true,
                        ]),
                    Input::make('product.priceMin')
                        ->type('text')
                        ->max(255)
                        ->title('Минимальная цена')
                        ->placeholder('Введите цену')
                        ->mask([
                            'alias' => 'currency',
                            'suffix' => ' ₸ ',
                            'groupSeparator' => ' ',
                            'digitsOptional' => true,
                        ]),

                    Input::make('product.price_cost')
                        ->type('text')
                        ->max(255)
                        ->title('Себестоимость')
                        ->placeholder('Введите цену')
                        ->mask([
                            'alias' => 'currency',
                            'suffix' => ' ₸ ',
                            'groupSeparator' => ' ',
                            'digitsOptional' => true,
                        ]),

                ])
            ])->title('Изменить цену товара'),



        ];
    }


    public function update_price(Request $request, Product $product)
    {

        $user = Auth::user();
        $price = preg_replace('/[^0-9]/', '', $request->input('product.priceBase'));
        $city = '750000000';

        $product->priceMin = preg_replace('/[^0-9]/', '', $request->input('product.priceMin'));
        $product->price_cost = preg_replace('/[^0-9]/', '', $request->input('product.price_cost'));
        $product->save();

        if ($price != $product->priceBase) {
            $setting = KaspiSetting::where('user_id', $user->id)->first();
            $SessionToken = MerchantLogin::gen($setting->username, $setting->password);
            $update_price = UpdateProductPrice::gen($SessionToken, $city, $price, $setting->points, $product->productName, $product->sku);


            $proce_history = new PriceHistory();
            $proce_history->product_id = $product->id;
            $proce_history->price = $price;
            $proce_history->user_id = $user->id;
            $proce_history->comment = 'Обновлено на сайте';
            $proce_history->save();

            Toast::info('Статус отправки в Kaspi: ' . $update_price['status']);
        }
    }



    public function remove(Request $request): void
    {
        // Product::findOrFail($request->get('id'))->delete();
        Toast::info(json_encode(Proxy::where('status', 1)->inRandomOrder()->get()));
    }
}
