<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Discount extends Model
{
    use HasFactory, HasJsonRelationships;

    protected $table = 'discounts';

    protected $fillable = [
        'id',
        'name',
        'type',
        'value',
        'user_id',
        'start_date',
        'finish_date',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'start_date' => 'datetime:d-m-Y H:i:s',
        'finish_date' => 'datetime:d-m-Y H:i:s',
    ];

     public function products()
     {
         return $this->belongsToMany(Product::class, 'discount_has_products');
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
            $model->statesLivedIn()->delete();
        });
    }
}
