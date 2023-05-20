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
use App\Orchid\Layouts\Order\NewOrderListLayout;
use App\Orchid\Layouts\Order\DeliveryOrderListLayout;
use App\Orchid\Layouts\Order\ArchiveOrderListLayout;
use Illuminate\Support\Facades\Auth;

class OrderListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        $total_count = number_format(Order::whereNot('status', 'CANCELLED')->where('user_id', Auth::user()->id)->sum('totalPrice'), 0, ',', ' ') . ' ₸' ;
        $avg_count = number_format(Order::whereNot('status', 'CANCELLED')->where('user_id', Auth::user()->id)->avg('totalPrice'), 0, ',', ' ') . ' ₸' ;
        $order_count = number_format(Order::whereNot('status', 'CANCELLED')->where('user_id', Auth::user()->id)->count('totalPrice'), 0, ',', ' ');
        
        return [
            'new_orders' => Order::where('state', 'NEW')->where('user_id', Auth::user()->id)->orderBy('creationDate', 'desc')->paginate(),
            'delivery_orders' => Order::whereNotIn('state',['NEW', 'ARCHIVE'])->where('user_id', Auth::user()->id)->orderBy('creationDate', 'desc')->paginate(),
            'archive_orders' => Order::where('state', 'ARCHIVE')->where('user_id', Auth::user()->id)->orderBy('creationDate', 'desc')->paginate(),
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

            Layout::tabs([
                'Новые заказы' => NewOrderListLayout::class,
                'На доставке' => DeliveryOrderListLayout::class,
                'В архиве' => ArchiveOrderListLayout::class,
            ])


        ];
    }



    public function remove(Request $request): void
    {
        Order::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }
}
