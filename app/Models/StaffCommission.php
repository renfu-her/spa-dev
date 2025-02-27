<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'amount',
        'commission_date',
        'commission_type',
        'commissionable_id',
        'commissionable_type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function commissionable()
    {
        return $this->morphTo();
    }
} 