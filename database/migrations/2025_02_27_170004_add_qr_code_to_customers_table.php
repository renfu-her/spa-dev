<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 這些欄位已經在建立表格時加入，所以這個遷移檔案可以是空的
    }

    public function down(): void
    {
        // 不需要執行任何操作
    }
};
