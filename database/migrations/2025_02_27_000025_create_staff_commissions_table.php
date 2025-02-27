<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('commission_type', ['sales', 'operation']);
            $table->decimal('amount', 10, 2);
            $table->date('commission_date');
            $table->string('reference_number')->nullable();
            $table->morphs('commissionable'); // 可以是销售订单或疗程使用记录
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_commissions');
    }
}; 