<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Builder;


class ProductMerchant extends Model
{
    use MassPrunable;

    protected $table = 'nova_notifications';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
    ];

    protected $casts = [
        'read_at' => 'datetime:d-m-Y H:i:s',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];


    public function prunable(): Builder
    {
        return static::whereNotNull('read_at')
            ->where('read_at', '<=', now()->subWeek());
    }

}
