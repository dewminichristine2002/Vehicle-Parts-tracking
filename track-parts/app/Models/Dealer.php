<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Dealer extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'company_name', 'email', 'password', 'registered_at'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
    ];
}