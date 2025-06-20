<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalPart extends Model
{
    use HasFactory;

    protected $fillable = ['part_number', 'part_name', 'price'];

    public function localStocks()
    {
        return $this->hasMany(LocalStock::class);
    }
}
