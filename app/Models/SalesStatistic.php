<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'statistic_date',
        'period_type',
        'total_orders',
        'total_sales',
        'total_product_sales',
        'total_treatment_sales',
        'total_package_sales',
        'total_products_sold',
        'total_treatments_sold',
        'total_packages_sold',
        'cash_payment_amount',
        'card_payment_amount',
        'transfer_payment_amount',
        'other_payment_amount',
        'new_customers',
        'returning_customers',
        'average_order_value',
        'discount_amount',
        'notes',
    ];

    protected $casts = [
        'statistic_date' => 'date',
        'total_orders' => 'integer',
        'total_sales' => 'decimal:2',
        'total_product_sales' => 'decimal:2',
        'total_treatment_sales' => 'decimal:2',
        'total_package_sales' => 'decimal:2',
        'total_products_sold' => 'integer',
        'total_treatments_sold' => 'integer',
        'total_packages_sold' => 'integer',
        'cash_payment_amount' => 'decimal:2',
        'card_payment_amount' => 'decimal:2',
        'transfer_payment_amount' => 'decimal:2',
        'other_payment_amount' => 'decimal:2',
        'new_customers' => 'integer',
        'returning_customers' => 'integer',
        'average_order_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];
}
