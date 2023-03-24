<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GetKaspiAPI::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('getorders:status --status=NEW --days_number=30 --page_number=0 --page_size=100')->cron('*/10 * * * *');
        $schedule->command('getorders:status --status=SIGN_REQUIRED --days_number=30 --page_number=0 --page_size=100')->cron('*/10 * * * *');
        $schedule->command('getorders:status --status=PICKUP --days_number=30 --page_number=0 --page_size=100')->cron('*/10 * * * *');
        $schedule->command('getorders:status --status=DELIVERY --days_number=30 --page_number=0 --page_size=100')->cron('*/10 * * * *');
        $schedule->command('getorders:status --status=KASPI_DELIVERY --days_number=30 --page_number=0 --page_size=100')->cron('*/10 * * * *');
        $schedule->command('getorders:status --status=ARCHIVE --days_number=30 --page_number=0 --page_size=100')->cron('*/10 * * * *');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
