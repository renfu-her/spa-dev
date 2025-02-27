<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopSupply extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'total_quantity',
        'opened_quantity',
        'unopened_quantity',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function openingRecords()
    {
        return $this->hasMany(SupplyOpeningRecord::class);
    }
} 