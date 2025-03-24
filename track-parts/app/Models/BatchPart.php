<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchPart extends Model
{
    protected $fillable = [
        'part_number',
        'part_name',
        'quantity',
        'batch_no',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_no', 'batch_no');
    }
}
