<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('statistic_date');
            $table->string('period_type'); // daily, monthly, yearly
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('total_product_sales', 10, 2)->default(0);
            $table->decimal('total_treatment_sales', 10, 2)->default(0);
            $table->decimal('total_package_sales', 10, 2)->default(0);
            $table->integer('total_products_sold')->default(0);
            $table->integer('total_treatments_sold')->default(0);
            $table->integer('total_packages_sold')->default(0);
            $table->decimal('cash_payment_amount', 10, 2)->default(0);
            $table->decimal('card_payment_amount', 10, 2)->default(0);
            $table->decimal('transfer_payment_amount', 10, 2)->default(0);
            $table->decimal('other_payment_amount', 10, 2)->default(0);
            $table->integer('new_customers')->default(0);
            $table->integer('returning_customers')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['statistic_date', 'period_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_statistics');
    }
};
