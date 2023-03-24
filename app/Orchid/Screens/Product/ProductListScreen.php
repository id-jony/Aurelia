<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Product;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;


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
            'items' => Product::query()->orderBy('updated_at', 'desc')->paginate(),
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

            Layout::table('items', [
                TD::make('id', 'ID')->sort(),
                TD::make('name', 'Наименование')->sort(),
                TD::make('code', 'Код')->sort(),
                TD::make('category', 'Категория')->sort(),

                TD::make('basePrice', 'Цена продажи')->sort()
                    ->render(function (Product $product) {
                        return number_format($product->basePrice, 0, ',', ' ') . ' ₸';
                    }),
                TD::make('minPrice', 'Минимальная цена')->width('150px')->sort()
                    ->render(function (Product $product) {
                        return number_format($product->minPrice, 0, ',', ' ') . ' ₸';
                    }),

                TD::make('merchants', 'Цена конкурентов')->sort(),
                
                TD::make('Обновлено')->sort()
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
            ]),

        ];
    }



    public function remove(Request $request): void
    {
        Product::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }
}
