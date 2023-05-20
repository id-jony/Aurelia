<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ProductMerchant extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    protected $table = 'product_merchants';

    protected $fillable = [
        'id',
        'product_id',
        'rival_id',
        'price',
        'deliveryDuration',
    ];

    protected $allowedFilters = [
        'id',
        'product_id',
        'rival_id',
        'price',
        'deliveryDuration',
    ];

    protected $allowedSorts = [
        'id',
        'product_id',
        'rival_id',
        'price',
        'deliveryDuration',
    ];

    protected $casts = [
        'delivery_at' => 'datetime:d-m-Y H:i:s',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function rival()
    {
        return $this->belongsTo(Rival::class, 'rival_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
        });

        self::deleting(function ($model) {
        });
    }
}
