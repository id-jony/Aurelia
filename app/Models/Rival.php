<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Rival extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;


    protected $fillable = [
        'id',
        'merchantId',
        'merchantName',
        'merchantRating',
        'merchantReviewsQuantity',
        'deliveryDuration',
        'delivery',
        'created_at',
        'updated_at',
    ];

    protected $allowedFilters = [
        'id',
        'merchantId',
        'merchantName',
        'merchantRating',
        'merchantReviewsQuantity',
        'deliveryDuration',
        'delivery',
        'created_at',
        'updated_at',
    ];

    protected $allowedSorts = [
        'id',
        'merchantId',
        'merchantName',
        'merchantRating',
        'merchantReviewsQuantity',
        'deliveryDuration',
        'delivery',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'delivery' => 'datetime:d-m-Y H:i:s',
    ];


    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
        });

        self::deleting(function ($model) {
        });
    }


}
