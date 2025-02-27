<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'credit_card', 'mobile_payment', 'mixed'])->change();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->change();
            $table->json('payment_details')->nullable()->after('payment_status');
            $table->string('credit_card_last_digits', 4)->nullable()->after('payment_details');
            $table->string('credit_card_type')->nullable()->after('credit_card_last_digits');
            $table->integer('installment_months')->nullable()->after('credit_card_type');
            $table->string('mobile_payment_provider')->nullable()->after('installment_months');
            $table->string('mobile_payment_reference')->nullable()->after('mobile_payment_provider');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('payment_method')->change();
            $table->string('payment_status')->change();
            $table->dropColumn([
                'payment_details',
                'credit_card_last_digits',
                'credit_card_type',
                'installment_months',
                'mobile_payment_provider',
                'mobile_payment_reference',
            ]);
        });
    }
}; 