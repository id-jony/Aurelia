<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'sku',
        'master_sku',
        'name',
        'category_id',
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
        'price_cost',
        'promo',
        'position',
        'discount_id',
        'price_old',
        'shop_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'expireDate' => 'datetime:d-m-Y H:i:s',
        'promo' => 'json',
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
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function managements()
    {
        return $this->hasMany(PriceManagement::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function rivals()
    {
        return $this->hasMany(ProductMerchant::class, 'product_id');
    }

    public function getRivalcountAttribute()
    {
        return $this->rivals()->count();
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

    public function getStatusAttribute()
    {
        return self::STATUS_VALUE[$this->offerStatus] ?? '';
    }

    public function getCountAttribute()
    {
        return OrderShipment::where('product_id', $this->id)
            ->join('orders', 'orders.id', '=', 'order_shipment.order_id')
            ->where('orders.status', 'COMPLETED')
            ->count();
    }

    public function getSumMoneyAttribute()
    {
        return $this->shipments()
            ->whereHas('order', function ($query) {
                $query->where('status', 'COMPLETED');
            })
            ->sum('price');
    }

    const  STATUS_VALUE = [
        'ACTIVE' => 'В продаже',
        'ARCHIVE' => 'В архиве',
    ];
}
