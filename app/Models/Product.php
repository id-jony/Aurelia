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
        'name',
        'category',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceMax',
        'priceBase',
        'expireDate',
        'offerStatus',
        'merchants'
    ];

    protected $allowedFilters = [
        'id',
        'sku',
        'name',
        'category',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceMax',
        'priceBase',
        'expireDate',
        'offerStatus',
        'merchants'
    ];

    protected $allowedSorts = [
        'id',
        'sku',
        'name',
        'category',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceMax',
        'priceBase',
        'expireDate',
        'offerStatus',
        'merchants'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'expireDate' => 'datetime:d-m-Y H:i:s',
        'merchants' => 'json',
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

    public function getPriceAttribute()
    {
        return number_format($this->priceBase, 0, ',', ' ') . ' ₸';
    }

    public function getMinPriceAttribute()
    {
        return number_format($this->priceMin, 0, ',', ' ') . ' ₸';
    }

    public function getMaxPriceAttribute()
    {
        return number_format($this->priceMax, 0, ',', ' ') . ' ₸';
    }



     public function getCountAttribute()
     {
         $count = 0;

         foreach (Order::all() as $order) {
             foreach ($order->products as $product_data) {
                 if ($product_data['id'] == $this->id) {
                     $count = $count + $product_data['quantity'];
                 }
             }
         }

         return $count . ' шт.';
     }
}
