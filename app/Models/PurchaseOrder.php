<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'staff_id',
        'purchase_date',
        'expected_delivery_date',
        'status',
        'subtotal',
        'tax',
        'shipping_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
        'pdf_path',
        'excel_path',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            if (!$purchaseOrder->purchase_number) {
                $purchaseOrder->purchase_number = static::generatePurchaseNumber();
            }
        });
    }

    public static function generatePurchaseNumber()
    {
        $prefix = 'PO-';
        $date = Carbon::now()->format('Ymd');
        
        // 獲取今天最後一個編號
        $lastOrder = static::where('purchase_number', 'like', $prefix . $date . '%')
            ->orderBy('purchase_number', 'desc')
            ->first();

        if ($lastOrder) {
            // 從最後一個編號提取序號並加1
            $lastNumber = intval(substr($lastOrder->purchase_number, -5));
            $newNumber = $lastNumber + 1;
        } else {
            // 如果今天還沒有編號，從1開始
            $newNumber = 1;
        }

        // 格式化為5位數的序號
        $sequence = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        
        return $prefix . $date . $sequence;
    }

    /**
     * 獲取此採購單關聯的供應商
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * 獲取此採購單關聯的員工
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * 獲取此採購單的所有項目
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * 獲取此採購單的所有收貨記錄
     */
    public function receivingRecords(): HasMany
    {
        return $this->hasMany(ReceivingRecord::class);
    }

    /**
     * 計算採購單總金額
     */
    public function calculateTotals()
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $tax = $subtotal * 0.05; // 假設稅率為 5%
        $total = $subtotal + $tax + ($this->shipping_fee ?? 0);

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total_amount' => $total,
        ]);

        return $this;
    }
} 