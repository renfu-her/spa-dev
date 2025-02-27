<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_treatment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('treatment_packages')->onDelete('cascade');
            $table->foreignId('treatment_item_id')->constrained('treatment_items');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_treatment_items');
    }
}; 