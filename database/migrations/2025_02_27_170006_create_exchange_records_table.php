<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_records', function (Blueprint $table) {
            $table->id();
            $table->string('exchange_number')->unique();
            $table->foreignId('return_id')->constrained('returns');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('staff_id')->constrained('users');
            $table->date('exchange_date');
            $table->decimal('return_amount', 10, 2);
            $table->decimal('exchange_amount', 10, 2);
            $table->decimal('difference_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'mobile_payment', 'store_credit'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_records');
    }
}; 