<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Review;
use App\Models\Product;
use App\Models\OrderShipment;

use App\Models\User;
use App\Api\Kaspi\GetProductPosition;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class GetPosition implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;

    public $failOnTimeout = false;
    public $timeout = 120000;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $category = str_replace("Master - ", "", $this->product->categories->code);
        $page = 0;

        $found = false;
        $position = 0;

        while (!$found) {
            $promo_data = GetProductPosition::gen($category, $page);
            // $cards = $promo_data->data->cards;
            
            foreach ($promo_data->data as $index => $card) {
                if ($card->id == $this->product->master_sku) {
                    $position =  $index + 1;
                    $found = true;
                    break;
                }
            }

            if ($found) {
                break;
            }

            $page++;

            if (count($promo_data->data) == 0) {
                break;
            }
        }

        $this->product->position = (($page + 1) * 12) + $position;
        $this->product->save();
    }
}
