<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;


    protected $fillable = [
        'id',
        'sku',
        'master_sku',
        'name',
        'category',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceBase',
        'expireDate',
        'offerStatus',
        'productName',
        'user_id',
        'price_cost'
    ];

    protected $allowedFilters = [
        'id',
        'sku',
        'master_sku',
        'name',
        'category',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceBase',
        'expireDate',
        'offerStatus',
        'productName',
        'user_id',
        'price_cost'
    ];

    protected $allowedSorts = [
        'id',
        'sku',
        'master_sku',
        'name',
        'category',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceBase',
        'expireDate',
        'offerStatus',
        'productName',
        'user_id',
        'price_cost'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'expireDate' => 'datetime:d-m-Y H:i:s',
    ];


    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
        });

        self::deleting(function ($model) {
        });
    }


    public function categories()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function rivals()
    {
        return $this->hasMany(ProductMerchant::class, 'product_id');
    }

    public function prices()
    {
        return $this->hasMany(PriceHistory::class, 'product_id');
    }

    public function shipments()
    {
        return $this->hasMany(OrderShipment::class, 'product_id');
    }

    public function getPriceAttribute()
    {
        return number_format($this->priceBase, 0, ',', ' ') . ' ₸';
    }

    public function getMinPriceAttribute()
    {
        return number_format($this->priceMin, 0, ',', ' ') . ' ₸';
    }



    public function getCountAttribute()
    {
        return OrderShipment::where('product_id', $this->id)
                ->join('orders', 'orders.id', '=', 'order_shipment.order_id')
                ->where('orders.status', 'COMPLETED')
                ->count() . ' шт.';
    }

    public function getSumMoneyAttribute()
    {
        return OrderShipment::where('product_id', $this->id)
                ->join('orders', 'orders.id', '=', 'order_shipment.order_id')
                ->where('orders.status', 'COMPLETED')
                ->sum('order_shipment.price');
    }
}
