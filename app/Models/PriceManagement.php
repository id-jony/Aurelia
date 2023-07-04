<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceManagement extends Model
{
    use HasFactory;

    protected $table = 'price_management';

    protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'keep_published',
        'autoreduction',
    ];

    protected $casts = [
        'keep_published' => 'boolean',
        'autoreduction' => 'boolean',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
