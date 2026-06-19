<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->unsignedSmallInteger('thoi_gian_di_muon')->default(0)->after('di_muon');
            $table->unsignedSmallInteger('thoi_gian_ve_som')->default(0)->after('thoi_gian_di_muon');
        });
    }

    public function down(): void
    {
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->dropColumn(['thoi_gian_di_muon', 'thoi_gian_ve_som']);
        });
    }
};
