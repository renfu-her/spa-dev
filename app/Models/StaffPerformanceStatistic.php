<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPerformanceStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'statistic_date',
        'period_type',
        'total_sales',
        'total_product_sales',
        'total_treatment_sales',
        'total_package_sales',
        'total_products_sold',
        'total_treatments_sold',
        'total_packages_sold',
        'total_customers_served',
        'total_appointments',
        'completed_appointments',
        'cancelled_appointments',
        'commission_amount',
        'bonus_amount',
        'total_working_hours',
        'overtime_hours',
        'performance_score',
        'notes',
    ];

    protected $casts = [
        'statistic_date' => 'date',
        'total_sales' => 'decimal:2',
        'total_product_sales' => 'decimal:2',
        'total_treatment_sales' => 'decimal:2',
        'total_package_sales' => 'decimal:2',
        'total_products_sold' => 'integer',
        'total_treatments_sold' => 'integer',
        'total_packages_sold' => 'integer',
        'total_customers_served' => 'integer',
        'total_appointments' => 'integer',
        'completed_appointments' => 'integer',
        'cancelled_appointments' => 'integer',
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'total_working_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'performance_score' => 'decimal:1',
    ];

    /**
     * 獲取此績效統計關聯的員工
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
