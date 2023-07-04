<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInteractives extends Command
{
    protected $signature = 'interactives:update';
    protected $description = 'Update interactives table';

    public function handle()
    {
        $startDate = '2023-05-30 00:00:00';
        $endDate = '2023-06-04 00:00:00';
        $randomStartDate = '2023-05-08 00:00:00';
        $randomEndDate = '2023-05-14 00:00:00';

        // Выборка строк с name = Interactive 3 и date в заданном диапазоне
        $interactives = DB::table('interactives')
            ->where('name', 'Interactive 2')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Изменение значения name на Interactive 5
        DB::table('interactives')
            ->where('name', 'Interactive 2')
            ->whereBetween('date', [$startDate, $endDate])
            ->update(['name' => 'Interactive 4']);

        // Создание дубликатов строк и изменение даты в заданном диапазоне
        foreach ($interactives as $interactive) {
            $newInteractive = (array) $interactive;
            $newInteractive['date'] = $this->generateRandomDate($randomStartDate, $randomEndDate);
            $newInteractive['id'] = null; // Удаление id у дубликата

            DB::table('interactives')->insert($newInteractive);
        }

        $this->info('Interactives table has been updated successfully.');
    }

    private function generateRandomDate($startDate, $endDate)
    {
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);

        $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);

        return date('Y-m-d H:i:s', $randomTimestamp);
    }
}
