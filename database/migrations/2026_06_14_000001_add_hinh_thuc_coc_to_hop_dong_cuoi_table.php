<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hop_dong_cuoi', function (Blueprint $table) {
            $table->string('hinh_thuc_coc', 50)->nullable()->after('tien_coc');
        });
    }

    public function down(): void
    {
        Schema::table('hop_dong_cuoi', function (Blueprint $table) {
            $table->dropColumn('hinh_thuc_coc');
        });
    }
};
