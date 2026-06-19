<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->enum('loai_nhan_vien', ['full_time', 'part_time'])->nullable()->after('ngay_ky_hop_dong');
            $table->enum('loai_hop_dong', ['chinh_thuc', 'thu_viec', 'hoc_viec', 'thuc_tap'])->nullable()->after('loai_nhan_vien');
            $table->unsignedBigInteger('luong_cung')->default(0)->after('loai_hop_dong');
            $table->unsignedBigInteger('luong_mem')->default(0)->after('luong_cung');
            $table->unsignedBigInteger('phu_cap')->default(0)->after('luong_mem');
        });
    }

    public function down(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->dropColumn([
                'loai_nhan_vien',
                'loai_hop_dong',
                'luong_cung',
                'luong_mem',
                'phu_cap',
            ]);
        });
    }
};
