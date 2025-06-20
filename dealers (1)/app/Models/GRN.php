<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRN extends Model
{
    use HasFactory;

 
    protected $table = 'grns';

    protected $fillable = ['dealer_id', 'grn_number', 'invoice_number', 'grn_date'];

    public function items()
{
    return $this->hasMany(GRNItem::class, 'grn_id'); 
}

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    
protected $casts = [
    'grn_date' => 'date',
];
}
