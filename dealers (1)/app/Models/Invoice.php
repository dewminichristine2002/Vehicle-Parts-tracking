<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{

    protected $table = 'invoices';
    protected $primaryKey = 'invoice_no';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'invoice_no',
        'customer_name',
        'contact_number',
        'vehicle_number',
        'make',
        'vehicle_model',
        'odo_value',
        'odo_type',
        'discount',
        'grand_total',
        'date',
        'dealer_id',
        'total_expense',
    ];
    
    

    public function soldParts(): HasMany
    {
        return $this->hasMany(SoldPart::class, 'invoice_no', 'invoice_no');
    }

    public function otherCosts(): HasMany
    {
        return $this->hasMany(OtherCost::class, 'invoice_no', 'invoice_no');
    }

    public function dealer()
{
    return $this->belongsTo(Dealer::class);
}

}
