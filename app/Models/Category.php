<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'id',
        'name',
        'code',
        'restricted',
        'closed',
        'commission'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category');
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
