<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use HasFactory, AsSource;

    // protected $table = 'kaspi_settings';

    protected $fillable = [
        'id',
        'name',
        'code',
        'restricted',
        'closed',
    ];

    protected $allowedFilters = [
        'id',
        'name',
        'code',
        'restricted',
        'closed',
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'code',
        'restricted',
        'closed',
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
