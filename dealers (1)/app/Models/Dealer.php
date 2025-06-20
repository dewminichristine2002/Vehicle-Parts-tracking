<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Dealer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'password',
        'registered_at',
        'last_login_at',
        'company_address',
        'company_mobile',
        'company_email',
        'company_logo',
        'owner_mobile',
        'user_name',
        'user_designation',
        'user_email',
        'user_contact'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function grns()
    {
        return $this->hasMany(\App\Models\GRN::class);
    }

    public function localStocks()
    {
        return $this->hasMany(\App\Models\LocalStock::class);
    }
}