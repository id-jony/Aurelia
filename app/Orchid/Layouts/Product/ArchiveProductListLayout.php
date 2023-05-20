<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;

use App\Models\Product;

class ArchiveProductListLayout extends Table
{

    protected $target = 'archive';

    protected function columns(): iterable
    {
        return [
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
                    return $product->brandClosed === 0 ? 'Нет' : 'Да';
                }),
                TD::make('brandRestricted', 'Ограниченный бренд')
                ->render(function (Product $product) {
                    return $product->brandRestricted === 0 ? 'Нет' : 'Да';
                })
                ->defaultHidden()
                ->sort(),

            TD::make('rivals', 'Конкуренты')
                ->render(function (Product $product) {
                    return $product->rivals->count() === 0 ? 'Нет' : 'Да';
                })
                ->sort(),

                TD::make('reviews', 'Отзывы')->sort()
                ->render(function (Product $product) {
                    return $product->reviews->count();
                }),

                TD::make('priceBase', 'Цена продажи')->sort()
                    ->render(function (Product $product) {
                        return number_format($product->priceBase, 0, ',', ' ') . ' ₸';
                    }),
                TD::make('MinPrice', 'Цена min')->sort(),
                
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
                    })];
    }
}
