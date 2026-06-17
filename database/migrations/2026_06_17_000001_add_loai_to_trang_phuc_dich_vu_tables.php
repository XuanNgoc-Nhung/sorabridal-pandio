<?php

use App\Support\LoaiCuoiPhongSu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->string('loai', 20)->default(LoaiCuoiPhongSu::CUOI)->after('ma_san_pham');
        });

        Schema::table('dich_vu_le', function (Blueprint $table) {
            $table->string('loai', 20)->default(LoaiCuoiPhongSu::CUOI)->after('ma_dich_vu');
        });

        Schema::table('nhom_dich_vu', function (Blueprint $table) {
            $table->string('loai', 20)->default(LoaiCuoiPhongSu::CUOI)->after('ma_nhom');
        });
    }

    public function down(): void
    {
        Schema::table('nhom_dich_vu', function (Blueprint $table) {
            $table->dropColumn('loai');
        });

        Schema::table('dich_vu_le', function (Blueprint $table) {
            $table->dropColumn('loai');
        });

        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->dropColumn('loai');
        });
    }
};
