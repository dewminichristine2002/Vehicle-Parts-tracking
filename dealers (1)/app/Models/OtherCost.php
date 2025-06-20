<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherCost extends Model
{

    protected $table = 'other_costs';
    
    protected $fillable = [
        'description',
        'discount',
        'price',
        'total',
        'invoice_no',
        'dealer_id'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');
    }
}
