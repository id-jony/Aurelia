<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
use App\Models\PriceManagement;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GetOrdersKaspiApi::class,
        \App\Console\Commands\KaspiApi::class,
        \App\Console\Commands\UpdateProduct::class,
        \App\Console\Commands\KaspiPromo::class,
        \App\Console\Commands\KaspiComission::class,
    ];

    protected function schedule(Schedule $schedule)
    {

        foreach (User::where('active', 1)->get() as $user) {
            $schedule->command('kaspi:get --user='. $user->id)->cron('*/15 * * * *');
            $schedule->command('kaspi:product --user='. $user->id)->cron('0 */1 * * *');
            $schedule->command('kaspi:promo --user='. $user->id)->cron('0 */1 * * *');
            $schedule->command('product:update --user='. $user->id)->cron('0 */1 * * *');
            $schedule->command('discount:product --user='. $user->id)->cron('*/60 * * * *');
        }
        
        $schedule->command('kaspi:comission')->daily(10);
        // $schedule->command('queue:prune-failed --hours=12')->daily();
        $schedule->command('model:prune')->daily();

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
