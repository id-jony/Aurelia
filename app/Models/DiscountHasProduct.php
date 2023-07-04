<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class DiscountHasProduct extends Model
{
    use HasFactory, HasJsonRelationships;

    protected $table = 'discount_has_products';

    protected $fillable = [
        'id',
        'discount_id',
        'product_id',
        'old_price',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

     public function statesLivedIn()
     {
         return $this->belongsToMany(Product::class, 'discount_has_products')->withTimestamps();
     }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
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
