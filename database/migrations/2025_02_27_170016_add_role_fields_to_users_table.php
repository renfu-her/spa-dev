<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'manager', 'staff', 'operator'])->default('staff')->after('password');
            }
            $table->boolean('is_active')->default(true)->after('role');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            $table->dropForeign(['created_by']);
            $table->dropColumn(['is_active', 'created_by']);
        });
    }
}; 