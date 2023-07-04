<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GetCategoryComission;
use App\Jobs\GetPosition;

class KaspiComission extends Command
{
    protected $signature = 'kaspi:comission';
    protected $description = 'Get KaspiApi Orders whith status';

    public function handle()
    {
        // Проверяем кописсию категории
            GetCategoryComission::dispatch();

        // Проверяем позицию товаров категории
            // GetPosition::dispatch($category);
    }
}
