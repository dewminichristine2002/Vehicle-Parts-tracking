<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoldPart extends Model
{

    protected $table = 'sold_parts';
    
    protected $fillable = [
        'part_number',
        'part_name',
        'quantity',
        'unit_price',
        'discount',        // ✅ Add this line
        'total',
        'invoice_no',
    ];
    

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');
    }
}
