<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerVehicle extends Model
{
    public $incrementing = false;
    public $timestamps = true;

    protected $table = 'customer_vehicles';

    // Set dummy primary key to bypass Laravel's internal logic
    protected $primaryKey = 'fake_id';

    protected $fillable = [
        'contact_number',
        'vehicle_number',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'contact_number', 'contact_number');
    }

    // Tell Laravel how to build the WHERE condition when updating
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('contact_number', $this->getAttribute('contact_number'))
                     ->where('vehicle_number', $this->getAttribute('vehicle_number'));
    }
}
