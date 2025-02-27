<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'permission_id',
    ];

    /**
     * 獲取此角色權限關聯的權限
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
} 