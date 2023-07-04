<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';

    protected $fillable = [
        'user_id',
        'token',
        'username',
        'password',
        'count_day',
        'interval_day',
        'shop_name',
        'shop_id',
        'points',
        'proxy',
        'percent_demp',
        'interval_demp',
        'percent_sales'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
        'points' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
