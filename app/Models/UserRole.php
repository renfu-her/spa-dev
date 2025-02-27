<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
    ];

    /**
     * 獲取此用戶角色關聯的用戶
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 