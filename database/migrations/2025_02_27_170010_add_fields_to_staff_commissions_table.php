<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff_commissions')) {
            Schema::table('staff_commissions', function (Blueprint $table) {
                if (!Schema::hasColumn('staff_commissions', 'is_paid')) {
                    $table->boolean('is_paid')->default(false)->after('commissionable_id');
                }
                if (!Schema::hasColumn('staff_commissions', 'paid_date')) {
                    $table->date('paid_date')->nullable()->after('is_paid');
                }
            });
        } else {
            Schema::create('staff_commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('staff_id')->constrained('users');
                $table->decimal('amount', 10, 2);
                $table->date('commission_date');
                $table->enum('commission_type', ['sales', 'operation', 'bonus']);
                $table->morphs('commissionable');
                $table->boolean('is_paid')->default(false);
                $table->date('paid_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff_commissions')) {
            Schema::table('staff_commissions', function (Blueprint $table) {
                $table->dropColumn(['is_paid', 'paid_date']);
            });
        } else {
            Schema::dropIfExists('staff_commissions');
        }
    }
}; 