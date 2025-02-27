<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_discount_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_discount_id')->constrained()->onDelete('cascade');
            $table->morphs('discountable');
            $table->decimal('discount_rate', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_discount_items');
    }
}; 