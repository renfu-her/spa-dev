<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentUsageRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_treatment_id',
        'treatment_item_id',
        'performed_by',
        'performed_at',
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function customerTreatment()
    {
        return $this->belongsTo(CustomerTreatment::class);
    }

    public function treatmentItem()
    {
        return $this->belongsTo(TreatmentItem::class);
    }

    public function performedByStaff()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function commissions()
    {
        return $this->morphMany(StaffCommission::class, 'commissionable');
    }
} 