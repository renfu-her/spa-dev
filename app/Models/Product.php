<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'barcode',
        'description',
        'image',
        'regular_price',
        'member_price',
        'stock_quantity',
        'safety_stock',
        'sales_commission_rate',
        'operation_commission_rate',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'member_price' => 'decimal:2',
        'sales_commission_rate' => 'decimal:2',
        'operation_commission_rate' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function shopSupplies()
    {
        return $this->hasMany(ShopSupply::class);
    }

    public function packages()
    {
        return $this->belongsToMany(TreatmentPackage::class, 'package_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }
} 