<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'member_price',
        'operation_commission_rate',
        'is_experience',
        'duration_minutes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'member_price' => 'decimal:2',
        'operation_commission_rate' => 'decimal:2',
        'is_experience' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(TreatmentCategory::class, 'category_id');
    }

    public function packages()
    {
        return $this->belongsToMany(TreatmentPackage::class, 'package_treatment_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function usageRecords()
    {
        return $this->hasMany(TreatmentUsageRecord::class);
    }
} 