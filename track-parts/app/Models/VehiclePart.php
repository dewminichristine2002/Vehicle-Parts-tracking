<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehiclePart extends Model
{
    protected $primaryKey = 'part_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'part_number',
        'part_name',
        'unit_price',
    ];
}
