<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Town extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;


    protected $fillable = [
        'id',
        'kaspi_id',
        'ru',
        'kz',
    ];

    protected $allowedFilters = [
        'id',
        'kaspi_id',
        'ru',
        'kz',
    ];

    protected $allowedSorts = [
        'id',
        'kaspi_id',
        'ru',
        'kz',
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
