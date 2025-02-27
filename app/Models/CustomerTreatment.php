<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'package_id',
        'sales_order_id',
        'start_date',
        'expiry_date',
        'remaining_times',
        'total_times',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(TreatmentPackage::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function usageRecords()
    {
        return $this->hasMany(TreatmentUsageRecord::class);
    }
} 