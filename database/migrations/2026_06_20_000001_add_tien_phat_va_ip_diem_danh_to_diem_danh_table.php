<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->unsignedBigInteger('tien_phat_di_muon')->default(0)->after('thoi_gian_ve_som');
            $table->unsignedBigInteger('tien_phat_ve_som')->default(0)->after('tien_phat_di_muon');
            $table->string('ip_diem_danh', 45)->nullable()->after('tien_phat_ve_som');
        });
    }

    public function down(): void
    {
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->dropColumn(['tien_phat_di_muon', 'tien_phat_ve_som', 'ip_diem_danh']);
        });
    }
};
