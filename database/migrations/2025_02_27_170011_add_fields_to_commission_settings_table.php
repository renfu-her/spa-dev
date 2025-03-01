<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('commission_settings')) {
            Schema::table('commission_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('commission_settings', 'type')) {
                    $table->string('type')->nullable();
                }
                if (!Schema::hasColumn('commission_settings', 'target_amount')) {
                    $table->decimal('target_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('commission_settings', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('end_date');
                }
            });
        } else {
            Schema::create('commission_settings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['product', 'treatment', 'package', 'operation']);
                $table->decimal('target_amount', 10, 2)->nullable();
                $table->decimal('commission_rate', 5, 2);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('commission_settings') &&
            Schema::hasColumn('commission_settings', 'target_amount') &&
            Schema::hasColumn('commission_settings', 'is_active')
        ) {
            Schema::table('commission_settings', function (Blueprint $table) {
                $table->dropColumn(['target_amount', 'is_active']);
            });
        } else {
            Schema::dropIfExists('commission_settings');
        }
    }
};
