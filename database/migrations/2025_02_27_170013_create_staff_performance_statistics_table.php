<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_performance_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users');
            $table->date('statistic_date');
            $table->enum('period_type', ['daily', 'monthly', 'yearly']);
            $table->decimal('total_sales', 12, 2);
            $table->decimal('total_product_sales', 12, 2);
            $table->decimal('total_treatment_sales', 12, 2);
            $table->decimal('total_package_sales', 12, 2);
            $table->integer('total_operations');
            $table->decimal('total_commission', 10, 2);
            $table->decimal('sales_commission', 10, 2);
            $table->decimal('operation_commission', 10, 2);
            $table->json('top_products')->nullable();
            $table->json('top_treatments')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'statistic_date', 'period_type'], 'staff_perf_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_performance_statistics');
    }
};
