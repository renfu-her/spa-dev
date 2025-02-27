<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyOpeningRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_supply_id',
        'opened_by',
        'opened_at',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function shopSupply()
    {
        return $this->belongsTo(ShopSupply::class);
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }
} 