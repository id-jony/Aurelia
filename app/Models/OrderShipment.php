<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;


class OrderShipment extends Model
{
    use HasFactory;

    protected $table = 'order_shipment';

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'kaspi_id',
        'price',
        'quantity',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getNetProfitAttribute()
    {
        return $this->price - $this->product->price_cost - ($this->price * $this->product->categories->commission / 100);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
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
