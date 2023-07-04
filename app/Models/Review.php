<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'id',
        'kaspi_id',
        'customer_id',
        'customer_author',
        'product_id',
        'rating',
        'photo',
        'plus',
        'minus',
        'text',
        'date'
    ];

    public function getReviewAttribute()
    {
        $text = '';

        if ($this->plus != null) {
            $text = 'Достоинства: '. $this->plus;
        } elseif ($this->minus != null) {
            $text = $text . 'Недостатки: '. $this->minus;
        } elseif ($this->text != null) {
            $text = $text . 'Комментарий: '. $this->text;
        }

        return $text;
    }

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'date' => 'datetime:d-m-Y',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
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
