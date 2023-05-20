<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class KaspiSetting extends Model
{
    use HasFactory, AsSource;

    protected $table = 'kaspi_settings';

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

    protected $allowedFilters = [
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

    protected $allowedSorts = [
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


    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {

        });

        self::deleting(function ($model) {

        });
    }
}
