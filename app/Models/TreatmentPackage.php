<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'validity_days',
        'members_only',
        'material_fee',
        'material_quantity',
        'deduction_times',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'material_fee' => 'decimal:2',
        'members_only' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function treatmentItems()
    {
        return $this->belongsToMany(TreatmentItem::class, 'package_treatment_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'package_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function customerTreatments()
    {
        return $this->hasMany(CustomerTreatment::class, 'package_id');
    }
}
