<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('treatment_categories');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('member_price', 10, 2);
            $table->decimal('operation_commission_rate', 5, 2)->default(0);
            $table->boolean('is_experience')->default(false);
            $table->integer('duration_minutes')->default(60);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_items');
    }
}; 