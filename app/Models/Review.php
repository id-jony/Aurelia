<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Review extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    protected $table = 'reviews';

    protected $fillable = [
        'id',
        'kaspi_id',
        'customer_id',
        'customer_author',
        'product_id',
        'rating',
        'photo',
        'plus',
        'minus',
        'text',
        'date'
    ];

    protected $allowedFilters = [
        'id',
        'kaspi_id',
        'customer_id',
        'customer_author',
        'product_id',
        'rating',
        'photo',
        'plus',
        'minus',
        'text',
        'date'
    ];

    protected $allowedSorts = [
        'id',
        'kaspi_id',
        'customer_id',
        'customer_author',
        'product_id',
        'rating',
        'photo',
        'plus',
        'minus',
        'text',
        'date'
    ];

    public function getReviewAttribute()
    {
        $text = '';

        if ($this->plus != null) {
            $text = 'Достоинства: '. $this->plus;
        } elseif ($this->minus != null) {
            $text = $text . 'Недостатки: '. $this->minus;
        } elseif ($this->text != null) {
            $text = $text . 'Комментарий: '. $this->text;
        }

        return $text;
    }

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'date' => 'datetime:d-m-Y',
    ];


    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {

            // $customers = Customer::where('name', 'like', '%'.$model->customer_author.'%', )->get();

            // foreach ($customers as $customer) {

            //     $order = Order::where('customer_id', $customer->id)
            //                 ->where('status', 'COMPLETED')
            //                 ->where('products->id' , $model->product_id)
            //                 ->first();

            //     if ($order != null) {
            //         $model->customer_id = $order->customer_id;
            //         $model->save();
            //     }

            // }


        });

        self::deleting(function ($model) {
        });
    }


}
