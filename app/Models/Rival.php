<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rival extends Model
{
    use HasFactory;


    protected $fillable = [
        'id',
        'merchantId',
        'merchantName',
        'merchantRating',
        'merchantReviewsQuantity',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function rivals()
    {
        return $this->hasMany(ProductMerchant::class, 'rival_id');
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
