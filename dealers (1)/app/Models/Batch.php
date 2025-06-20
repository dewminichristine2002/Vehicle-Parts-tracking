<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $primaryKey = 'batch_no';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'batch_no',
        'bill_no',
        'received_date',
    ];

    public function batchParts(): HasMany
    {
        return $this->hasMany(BatchPart::class, 'batch_no', 'batch_no');
    }
}
