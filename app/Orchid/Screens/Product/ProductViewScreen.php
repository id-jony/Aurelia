<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Sight;

use Orchid\Support\Color;

class ProductViewScreen extends Screen
{
    public $product;


    public function query(Product $product): iterable
    {
        $count = 0;
        $sum_money = 0;
        $customer = collect();

        foreach (Order::all() as $order) {
            foreach ($order->products as $product_data) {
                if ($product_data['id'] == $product->id) {
                    $count = $count + $product_data['quantity'];
                    $customer->push($order->customer_id);
                    $sum_money = $sum_money + $order->totalPrice;
                }
            }
        }

        $customers = Customer::whereIn('id', $customer)->get();

        
        Toast::info($product->merchants['0']['price']);


        return [
            'product' => $product,
            'total_count' => number_format($count, 0, ',', ' ') . ' шт.',
            'customers' => $customers,
            'sum_money' => number_format($sum_money, 0, ',', ' ') . ' ₸',
            'money' => number_format($sum_money - $sum_money * 0.22, 0, ',', ' ') . ' ₸',
            'merchants' => [],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Товар - ' . $this->product->name;
    }

    public function description(): ?string
    {
        return $this->product->brand;
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

                 Layout::metrics([
                'Кол-во продаж' => 'total_count',
                'Сумма продаж' => 'sum_money',
                'Чистая прибыль' => 'money',

            ]),
            Layout::columns([
                 Layout::view('kaspi.image'),

            // Layout::legend('product', [
            //     Sight::make('sku'),
            //     Sight::make('name'),
            //     Sight::make('category')
            //          ->render(function (Product $product) {
            //             return $product->categories->name;
            //         }),
            //     Sight::make('brand'),
            //     Sight::make('brandCode'),

            //     // Sight::make('email_verified_at', 'Email Verified')->render(fn (User $user) => $user->email_verified_at === null
            //     //     ? '<i class="text-danger">●</i> False'
            //     //     : '<i class="text-success">●</i> True'),
            //     // Sight::make('created_at', 'Created'),
            //     // Sight::make('updated_at', 'Updated'),

            // ]),
          ]),



            Layout::tabs([
                'Список покупателей' => Layout::table('customers', [
                    TD::make('name', 'Имя'),
                    TD::make('phone', 'Телефон'),
                    TD::make('town', 'Город'),

                ]),
                'Список конкурентов' => Layout::table('merchants', [
                    // TD::make('delivery', 'Стоимость'),
                    // TD::make('phone', 'Телефон'),
                    // TD::make('town', 'Город'),
                ])
            ])



        ];
    }



    public function remove(Request $request): void
    {
        Product::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }
}
