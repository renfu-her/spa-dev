<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('notes');
            $table->string('excel_path')->nullable()->after('pdf_path');
            $table->timestamp('last_exported_at')->nullable()->after('excel_path');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'excel_path', 'last_exported_at']);
        });
    }
}; 