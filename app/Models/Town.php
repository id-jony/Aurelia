<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $fillable = [
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
