<?php

namespace App\Orchid\Screens\Customer;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\Customer;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;


class CustomerListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'items' => Customer::query()->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Список покупателей';
    }

    public function description(): ?string
    {
        return 'В разделе представлен список ваших покупателей.';
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
            Layout::table('items', [
                TD::make('id', 'ID'),
                TD::make('kaspi_id', 'ID Каспи'),
                TD::make('name', 'ФИО'),
                TD::make('phone', 'Телефон'),
                TD::make('town', 'Город'),
                TD::make('Создано')
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
        Customer::findOrFail($request->get('id'))->delete();
        Toast::info('Событие успешно удалено');
    }
}
