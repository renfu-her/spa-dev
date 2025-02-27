<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_treatments', function (Blueprint $table) {
            $table->renameColumn('total_sessions', 'total_times');
            $table->renameColumn('used_sessions', 'used_times');
            $table->renameColumn('remaining_sessions', 'remaining_times');
            $table->date('start_date')->after('sales_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('customer_treatments', function (Blueprint $table) {
            $table->renameColumn('total_times', 'total_sessions');
            $table->renameColumn('used_times', 'used_sessions');
            $table->renameColumn('remaining_times', 'remaining_sessions');
            $table->dropColumn('start_date');
        });
    }
}; 