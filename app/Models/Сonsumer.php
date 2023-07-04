<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Сonsumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'kaspi_id',
        'town',
        'whatsapp',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
    ];


    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function getOrdercountAttribute()
    {
        return $this->orders->where('status', 'COMPLETED')->count();
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
