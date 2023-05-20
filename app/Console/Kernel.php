<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GetOrdersKaspiApi::class,
    ];

    protected function schedule(Schedule $schedule)
    {

        foreach (User::where('active', 1)->get() as $user) {
            $schedule->command('getorders:status --user='. $user->id .' --status=NEW --page_number=0 --page_size=100')->cron('*/15 * * * *');
            $schedule->command('getorders:status --user='. $user->id .' --status=SIGN_REQUIRED --page_number=0 --page_size=100')->cron('*/15 * * * *');
            $schedule->command('getorders:status --user='. $user->id .' --status=PICKUP --page_number=0 --page_size=100')->cron('*/15 * * * *');
            $schedule->command('getorders:status --user='. $user->id .' --status=DELIVERY --page_number=0 --page_size=100')->cron('*/15 * * * *');
            $schedule->command('getorders:status --user='. $user->id .' --status=KASPI_DELIVERY --page_number=0 --page_size=100')->cron('*/15 * * * *');
            $schedule->command('getorders:status --user='. $user->id .' --status=ARCHIVE --page_number=0 --page_size=100')->cron('*/15 * * * *');
            $schedule->command('get-product:kaspi --user='. $user->id)->cron('0 */3 * * *');
        }
        $schedule->command('update:product --user=1')->cron('0 */3 * * *');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
