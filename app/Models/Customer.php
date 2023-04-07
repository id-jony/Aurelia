<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Customer extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'kaspi_id',
        'town'
    ];

    protected $allowedFilters = [
        'id',
        'name',
        'phone',
        'kaspi_id',
        'town'
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'phone',
        'kaspi_id',
        'town'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
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
