<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Product;

use App\Models\Order;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Sight;

use Orchid\Screen\Fields\Input;

class ProductListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        // $total_count = number_format(Order::whereNot('status', 'CANCELLED')->sum('totalPrice'), 0, ',', ' ') . ' ₸' ;
        // $avg_count = number_format(Order::whereNot('status', 'CANCELLED')->avg('totalPrice'), 0, ',', ' ') . ' ₸' ;
        // $order_count = number_format(Order::whereNot('status', 'CANCELLED')->count('totalPrice'), 0, ',', ' ');

        return [
            'active' => Product::where('offerStatus', 'ACTIVE')->filters()->defaultSort('offerStatus', 'asc')->paginate(),
            'archive' => Product::where('offerStatus', 'ARCHIVE')->filters()->defaultSort('offerStatus', 'asc')->paginate(),

            // 'total_count' => $total_count,
            // 'avg_count' => $avg_count,
            // 'order_count' => $order_count,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Товары';
    }

    public function description(): ?string
    {
        return 'В разделе представлены все ваши товары.';
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

            // Layout::metrics([
            //     'Сумма продаж' => 'total_count',
            //     'Средний чек' => 'avg_count',
            //     'Кол-во заказов' => 'order_count',
            //     // 'Дата завершения' => 'event_finish',

            // ]),

             Layout::tabs([
                'Активные позиции' => 

            Layout::table('active', [
                TD::make('image', 'Фото')
                    ->width('120px')
                    ->render(function (Product $product) {
                        return "<img width='64px' src='{$product->primaryImage}' alt='sample' class='d-block img-fluid'>";
                    }),
                TD::make('sku', 'Sku')->sort()->defaultHidden(),
                TD::make('offerStatus', 'Статус')->sort()->defaultHidden(),
                TD::make('name', 'Наименование')->sort()
                    ->render(function (Product $product) {
                        return Link::make(mb_strimwidth($product->name, 0, 60, '...'))
                            ->route('platform.product.view', $product);
                    }),

                TD::make('category', 'Категория')->sort()
                        ->render(function (Product $product) {
                            return $product->categories->name;
                        }),
                TD::make('brand', 'Бренд'),

                TD::make('brandClosed', 'Закрытый бренд')->defaultHidden()
                ->render(function (Product $product) {
                    if ($product->brandClosed === 0) {
                        return 'Нет';
                    } else {
                        return 'Да';
                    }
                }),
                TD::make('brandRestricted', 'Ограниченный бренд')->defaultHidden()
                ->render(function (Product $product) {
                    if ($product->brandClosed === 0) {
                        return 'Нет';
                    } else {
                        return 'Да';
                    }
                }),


                TD::make('priceBase', 'Цена продажи')->sort()
                    ->render(function (Product $product) {
                        return number_format($product->priceBase, 0, ',', ' ') . ' ₸';
                    }),
                TD::make('MinPrice', 'Цена min')->sort(),
                TD::make('MaxPrice', 'Цена max')->sort(),
                TD::make('count', 'Кол-во покупок')->sort(),



                TD::make('Обновлено')->sort()
                    ->render(fn ($item) => $item->updated_at->format('d.m.Y H:i')),

                TD::make('Опции')
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->cantHide()
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('options-vertical')
                            ->list([
                                Link::make('Открыть')->href($product->productUrl),
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->confirm('Вы уверены в этом?')
                                    ->method('remove', [
                                        'id' => $product->id
                                    ]),
                            ]);
                    }),
            ]),

            'В архиве' => 

            Layout::table('archive', [
                TD::make('image', 'Изображение')
                    ->width('80px')
                    ->render(function (Product $product) {
                        return "<img width='64px' src='{$product->primaryImage}' alt='sample' class='d-block img-fluid'>";
                    }),
                TD::make('sku', 'Sku')->sort()->defaultHidden(),
                TD::make('offerStatus', 'Статус')->defaultHidden()->sort(),
                TD::make('name', 'Наименование')->sort()
                    ->render(function (Product $product) {
                        return Link::make(mb_strimwidth($product->name, 0, 60, '...'))
                            ->route('platform.product.view', $product);
                    }),

                TD::make('category', 'Категория')->sort()
                        ->render(function (Product $product) {
                            return $product->categories->name;
                        }),
                TD::make('brand', 'Бренд'),

                TD::make('brandClosed', 'Закрытый бренд')->defaultHidden()
                ->render(function (Product $product) {
                    if ($product->brandClosed === 0) {
                        return 'Нет';
                    } else {
                        return 'Да';
                    }
                }),
                TD::make('brandRestricted', 'Ограниченный бренд')->defaultHidden()
                ->render(function (Product $product) {
                    if ($product->brandClosed === 0) {
                        return 'Нет';
                    } else {
                        return 'Да';
                    }
                }),


                TD::make('price', 'Цена продажи')->sort(),
                TD::make('MinPrice', 'Цена min')->sort(),
                TD::make('MaxPrice', 'Цена max')->sort(),
                TD::make('count', 'Кол-во покупок')->sort(),

                TD::make('Обновлено')->sort()
                    ->render(fn ($item) => $item->updated_at->format('d.m.Y H:i')),

                TD::make('Опции')
                    ->align(TD::ALIGN_CENTER)
                    ->width(100)
                    ->cantHide()
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('options-vertical')
                            ->list([
                                Link::make('Открыть')->href($product->productUrl),
                                Button::make('Удалить')
                                    ->icon('trash')
                                    ->confirm('Вы уверены в этом?')
                                    ->method('remove', [
                                        'id' => $product->id
                                    ]),
                            ]);
                    }),
            ]),


            

             ]),
                
             
        ];
    }



    public function remove(Request $request): void
    {
        Product::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }
}
