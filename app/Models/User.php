<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Nova\Auth\Impersonatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, Impersonatable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
        'active'

    ];

    protected $hidden = [
        'password',
        'remember_token',
        'permissions',

    ];

    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    public function viewAnyRole()
    {
        return true; // или другая логика, определяющая разрешение
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }
}
