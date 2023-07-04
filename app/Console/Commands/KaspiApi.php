<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GetProductKaspiApi;

class KaspiApi extends Command
{
    protected $signature = 'kaspi:get {--user=}';
    protected $description = 'Get KaspiApi Orders whith status';

    public function handle()
    {

        $status_collect = ['NEW','SIGN_REQUIRED','PICKUP','DELIVERY','KASPI_DELIVERY','ARCHIVE'];
        $user = $this->option('user');

        foreach ($status_collect as $status) {
            GetProductKaspiApi::dispatch($status, $user);
        }
    }
}
