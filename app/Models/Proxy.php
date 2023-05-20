<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Proxy extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    protected $table = 'proxy';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'status',
        'port'
    ];

    protected $allowedFilters = [
        'id',
        'protocol',
        'ip',
        'status',
        'port'
    ];

    protected $allowedSorts = [
        'id',
        'protocol',
        'ip',
        'status',
        'port'
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
