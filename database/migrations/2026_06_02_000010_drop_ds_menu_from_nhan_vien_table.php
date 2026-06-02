<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->dropColumn('ds_menu');
        });
    }

    public function down(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->json('ds_menu')->nullable()->after('luong_tang_ca');
        });
    }
};
