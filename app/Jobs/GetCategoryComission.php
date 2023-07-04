<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

use App\Models\Category;
use App\Api\Kaspi\CategoryComission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetCategoryComission implements ShouldQueue
{
use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

public function __construct()
{
    //
}

public function handle()
{
    $categories = Category::pluck('name')->toArray();

    try {
        $categoriesData = CategoryComission::get($categories);

        foreach ($categoriesData as $categoryData) {
            $category = Category::where('name', $categoryData['title'])->first();
            if ($category) {
                $category->commission = $categoryData['commission_start'];
                $category->save();
            }
        }
    } catch (\Exception $exception) {
        // Обработка ошибок
        Log::error('Ошибка при получении данных о комиссиях категорий: ' . $exception->getMessage(), ['exception' => $exception]);
    }
}
}