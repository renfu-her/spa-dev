<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'birthday',
        'is_member',
        'member_since',
        'notes',
    ];

    protected $casts = [
        'birthday' => 'date',
        'member_since' => 'date',
        'is_member' => 'boolean',
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function treatments()
    {
        return $this->hasMany(CustomerTreatment::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnRecord::class);
    }
} 