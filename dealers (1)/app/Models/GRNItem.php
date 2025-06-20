<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNItem extends Model
{
    use HasFactory;

    
    protected $table = 'grn_items';  
    protected $fillable = ['grn_id', 'global_part_id', 'quantity', 'grn_unit_price', 'dealer_id'];

    public function grn()
{
    return $this->belongsTo(GRN::class, 'grn_id'); 
}

    public function globalPart()
    {
        return $this->belongsTo(GlobalPart::class);
    }
}
