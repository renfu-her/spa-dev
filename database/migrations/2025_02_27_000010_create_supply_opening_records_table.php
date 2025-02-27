<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_opening_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_supply_id')->constrained('shop_supplies');
            $table->foreignId('opened_by')->constrained('users');
            $table->dateTime('opened_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_opening_records');
    }
}; 