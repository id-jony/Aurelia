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
        $schedule->command('getorders:status --user=1 --status=NEW --page_number=0 --page_size=100 --user=1')->cron('*/15 * * * *');
        $schedule->command('getorders:status --user=1 --status=SIGN_REQUIRED --page_number=0 --page_size=100 --user=1')->cron('*/15 * * * *');
        $schedule->command('getorders:status --user=1 --status=PICKUP --page_number=0 --page_size=100 --user=1')->cron('*/15 * * * *');
        $schedule->command('getorders:status --user=1 --status=DELIVERY --page_number=0 --page_size=100 --user=1')->cron('*/15 * * * *');
        $schedule->command('getorders:status --user=1 --status=KASPI_DELIVERY --page_number=0 --page_size=100 --user=1')->cron('*/15 * * * *');
        $schedule->command('getorders:status --user=1 --status=ARCHIVE --page_number=0 --page_size=100 --user=1')->cron('*/15 * * * *');

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
