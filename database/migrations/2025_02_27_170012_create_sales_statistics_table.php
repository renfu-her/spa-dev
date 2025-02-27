<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('statistic_date');
            $table->enum('period_type', ['daily', 'monthly', 'yearly']);
            $table->integer('total_orders');
            $table->decimal('total_sales', 12, 2);
            $table->decimal('total_product_sales', 12, 2);
            $table->decimal('total_treatment_sales', 12, 2);
            $table->decimal('total_package_sales', 12, 2);
            $table->integer('total_products_sold');
            $table->integer('total_treatments_sold');
            $table->integer('total_packages_sold');
            $table->json('payment_methods_breakdown')->nullable();
            $table->json('top_products')->nullable();
            $table->json('top_treatments')->nullable();
            $table->json('top_packages')->nullable();
            $table->timestamps();
            
            $table->unique(['statistic_date', 'period_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_statistics');
    }
}; 