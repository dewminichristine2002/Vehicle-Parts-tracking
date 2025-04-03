<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'contact_number';
    public $incrementing = false; // because it's a string
    protected $keyType = 'string';

    protected $fillable = [
        'contact_number',
        'customer_name',
    ];

    public function vehicles()
    {
        return $this->hasMany(CustomerVehicle::class, 'contact_number', 'contact_number');
    }
}
