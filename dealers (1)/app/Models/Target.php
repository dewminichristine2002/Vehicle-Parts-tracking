<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'target_amount',
        'achieved_amount',
        'dealer_id',
    ];

    // Optional: Accessor for full month name (e.g., "May")
    public function getMonthNameAttribute()
    {
        return date("F", mktime(0, 0, 0, $this->month, 10));
    }

    // Optional: Scope to get target for a specific year/month
    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }
}
