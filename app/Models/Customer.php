<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (!$customer->customer_code) {
                $customer->customer_code = static::generateCustomerCode();
            }

            // 新增客戶時，如果是會員但沒有設定入會日期，則設為當天
            if ($customer->is_member && !$customer->member_since) {
                $customer->member_since = Carbon::now()->toDateString();
            }
        });

        static::updating(function ($customer) {
            // 更新時，如果取消會員資格，清除入會日期
            if ($customer->isDirty('is_member') && !$customer->is_member) {
                $customer->member_since = null;
            }
        });
    }

    public static function generateCustomerCode()
    {
        $prefix = env('APP_CUSTOMER_CODE', 'SPA');
        $date = Carbon::now()->format('Ymd');

        // 獲取今天最後一個編號
        $lastCustomer = static::where('customer_code', 'like', $prefix . $date . '%')
            ->orderBy('customer_code', 'desc')
            ->first();

        if ($lastCustomer) {
            // 從最後一個編號提取序號並加1
            $lastNumber = intval(substr($lastCustomer->customer_code, -5));
            $newNumber = $lastNumber + 1;
        } else {
            // 如果今天還沒有編號，從1開始
            $newNumber = 1;
        }

        // 格式化為5位數的序號
        $sequence = str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        return $prefix . $date . $sequence;
    }

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
