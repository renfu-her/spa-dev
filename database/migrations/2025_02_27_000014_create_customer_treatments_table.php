<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('package_id')->constrained('treatment_packages');
            $table->foreignId('sales_order_id')->constrained('sales_orders');
            $table->date('expiry_date');
            $table->integer('total_sessions');
            $table->integer('used_sessions')->default(0);
            $table->integer('remaining_sessions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_treatments');
    }
}; 