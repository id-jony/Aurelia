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
use Orchid\Screen\Fields\Input;

use Orchid\Screen\Sight;
use App\Orchid\Layouts\Product\ActiveProductListLayout;
use App\Orchid\Layouts\Product\ArchiveProductListLayout;
use Orchid\Screen\Actions\ModalToggle;
use App\Models\KaspiSetting;
use App\Api\Kaspi\MerchantLogin;
use App\Api\Kaspi\UpdateProductPrice;
use Illuminate\Support\Facades\Auth;

class ProductListScreen extends Screen
{

    public function query(): iterable
    {

        $settings = KaspiSetting::where('user_id', Auth::user()->id)->first();
        return [
            'active' => Product::where('offerStatus', 'ACTIVE')->where('user_id', Auth::user()->id)->filters()->defaultSort('offerStatus', 'asc')->paginate(),
            'archive' => Product::where('offerStatus', 'ARCHIVE')->where('user_id', Auth::user()->id)->filters()->defaultSort('offerStatus', 'asc')->paginate(),
            'percent_demp' => $settings->percent_demp ?? 0,
        ];
    }

    public function name(): ?string
    {
        return 'Товары';
    }

    public function description(): ?string
    {
        return 'В разделе представлены все ваши товары.';
    }

    public function commandBar(): iterable
    {
        return [
                ModalToggle::make('Сформировать цены')
            ->modal('update_price')
            ->method('update_price')
            ->icon('money'),
        ];
    }

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
                'Активные позиции' => ActiveProductListLayout::class,
                'В архиве' => ArchiveProductListLayout::class,
             ]),

           Layout::modal('update_price', [
            Layout::rows([
                Input::make('percent_demp')
                          ->type('text')
                          ->max(255)
                          ->required()
                          ->title('Максимальный процент демпинга')
                          ->placeholder('Введите процент')
                          ->mask([
                            'alias' => 'currency',
                            'suffix' => ' % ',
                            'groupSeparator' => ' ',
                            'digitsOptional' => true,
                        ]),
            ])
        ])->title('Настройка минимальной цены'),
        ];
    }



    public function remove(Request $request): void
    {
        Product::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }

    public function update_price(Request $request)
    {
        $products = Product::where('user_id', Auth::user()->id)->where('offerStatus', 'ACTIVE')->get();

        foreach ($products as $product) {
            $percent_demp = $product->priceBase * (trim($request->input('percent_demp'), '%') / 100);
            $product->priceMin =  $product->priceBase - $percent_demp;
            $product->save();
        }
        Toast::info('Сформированна минимальная цена для товаров');
    }
}
