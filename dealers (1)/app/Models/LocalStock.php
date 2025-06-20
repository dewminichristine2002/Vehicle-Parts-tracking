<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalStock extends Model
{
    use HasFactory;

    protected $fillable = ['dealer_id', 'global_part_id', 'quantity'];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function globalPart()
    {
        return $this->belongsTo(GlobalPart::class);
    }
}
