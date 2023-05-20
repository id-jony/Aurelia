<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class PriceHistory extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    protected $table = 'price_history';

    protected $fillable = [
        'id',
        'product_id',
        'price',
        'rival_id',
        'user_id',
        'comment'
    ];

    protected $allowedFilters = [
        'id',
        'product_id',
        'price',
        'rival_id',
        'user_id',
        'comment'
    ];

    protected $allowedSorts = [
        'id',
        'product_id',
        'price',
        'rival_id',
        'user_id',
        'comment'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function rival()
    {
        return $this->belongsTo(Rival::class, 'rival_id');
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
