<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'type',
        'kaspi_id',
        'code',
        'name',
        'category',
        'basePrice',

    ];

    protected $allowedFilters = [
        'type',
        'kaspi_id',
        'code',
        'name',
        'category',
        'basePrice',
    ];

    protected $allowedSorts = [
        'type',
        'kaspi_id',
        'code',
        'name',
        'category',
        'basePrice',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
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
