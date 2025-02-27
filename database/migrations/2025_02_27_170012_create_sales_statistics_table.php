<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 表格已經在之前的遷移中建立，所以這裡不需要重複建立
    }

    public function down(): void
    {
        // 不需要執行任何操作
    }
}; 