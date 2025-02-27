<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_code')->unique()->after('id');
            $table->string('qr_code')->nullable()->after('customer_code');
            $table->string('barcode')->nullable()->after('qr_code');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['customer_code', 'qr_code', 'barcode']);
        });
    }
}; 