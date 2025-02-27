<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_treatment_id')->constrained('customer_treatments');
            $table->foreignId('treatment_item_id')->constrained('treatment_items');
            $table->foreignId('performed_by')->constrained('users');
            $table->dateTime('performed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_usage_records');
    }
}; 