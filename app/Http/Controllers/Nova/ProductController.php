<?php

namespace App\Http\Controllers\Nova;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    public function saveAutosale(Request $request, $id)
    {
        $user = $request->user();

        // Проверяем количество товаров пользователя с autoreduction = 1
        $productCount = $user->products()
            ->where('autoreduction', 1)
            ->count();
        $maxProductCount = 10;
        
        if ($productCount > $maxProductCount) {
            return response()->json(['message' => 'Превышено количество товаров'], 200);
        }
        
        // Получение модели по идентификатору $id
        $model = Product::findOrFail($id);

        // Сохранение значения чекбокса автоснижения цены
        $model->autoreduction = $request->input('autosaleEnabled');
        $model->save();

        // Ответ на успешное сохранение
        return response()->json(['message' => 'Автоснижение цены сохранено успешно'], 200);
    }

    public function saveWeeklyUpdate(Request $request, $id)
    {
        // Получение модели по идентификатору $id
        $model = Product::findOrFail($id);

        // Сохранение значения чекбокса обновления каждые 7 дней
        $model->keep_published = $request->input('weeklyUpdateEnabled');
        $model->save();

        // Ответ на успешное сохранение
        return response()->json(['message' => 'Обновление каждые 7 дней сохранено успешно'], 200);
    }

    public function savePriceMin(Request $request, Product $product)
    {
        $product->priceMin = $request->input('priceMin');
        $product->save();

        return response()->json(['message' => 'Минимальная цена сохранена']);
    }

    public function savePriceCost(Request $request, Product $product)
    {
        $product->price_cost = $request->input('priceCost');
        $product->save();

        return response()->json(['message' => 'Минимальная цена сохранена']);
    }
}
