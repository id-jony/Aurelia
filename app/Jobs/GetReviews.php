<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

use App\Models\Review;
use App\Models\Product;
use App\Models\OrderShipment;

use App\Models\User;
use App\Api\Kaspi\GetProductReviews;
use App\Notifications\KaspiInfo;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class GetReviews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sku;
    private $product;
    private $user;

    public $failOnTimeout = false;
    public $timeout = 120000;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sku, Product $product, User $user, Request $request)
    {
        $this->sku = $sku;
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Проверяем отзывы товара
        $review_data = GetProductReviews::gen($this->sku);

        if (!empty($review_data->data)) {
            foreach ($review_data->data as $data) {
                $review = Review::where('kaspi_id', $data->id)->first();
                if ($review == null) {
                    $review = new Review();
                    $review->kaspi_id = $data->id ?? '';
                    $review->customer_author = $data->author ?? '';
                    $review->product_id = $this->product->id;
                    $review->plus = $data->comment->plus ?? '';
                    $review->minus = $data->comment->minus ?? '';
                    $review->text = $data->comment->text ?? '';
                    $review->photo = $data->galleryImages[0]->large ?? '';
                    $review->rating = $data->rating ?? '';
                    $review->date = $data->date ?? '';

                    $shipments = OrderShipment::where('product_id', $this->product->id)->get();
                    foreach ($shipments as $shipment) {
                        if (stristr($shipment->order->customer->name, $review->customer_author) != false) {
                            $review->customer_id = $shipment->order->customer->id;
                        }
                    }

                    $this->user->notify(
                        NovaNotification::make()
                            ->message('Новый отзыв у товара ' . $this->product->name . ' - ' . $review->customer_author)
                            ->action('Открыть', URL::remote('/app/resources/products/' . $this->product->id))
                            ->icon('chat')
                            ->type('info')
                    );
                } elseif ($review->plus != $data->comment->plus ?? '' || $review->minus != $data->comment->minus ?? '' || $review->text != $data->comment->text ?? '' || $review->photo != $data->galleryImages[0]->large || $review->rating != $data->rating ?? '') {
                    $review->plus = $data->comment->plus ?? '';
                    $review->minus = $data->comment->minus ?? '';
                    $review->text = $data->comment->text ?? '';
                    $review->photo = $data->galleryImages[0]->large ?? '';
                    $review->rating = $data->rating ?? '';
                    $review->date = $data->date ?? '';

                    $this->user->notify(
                        NovaNotification::make()
                            ->message('Обновлен отзыв у товара ' . $this->product->name . ' - ' . $review->customer_author)
                            ->action('Открыть', URL::remote('/app/resources/products/' . $this->product->id))
                            ->icon('chat')
                            ->type('info')
                    );
                }
                $review->save();
            }
        }
        // ---------------------------------
    }
}
