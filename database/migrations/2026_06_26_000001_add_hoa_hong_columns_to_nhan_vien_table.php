<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->decimal('hoa_hong_hop_dong_cuoi', 8, 2)->nullable()->after('luong_tang_ca');
            $table->decimal('hoa_hong_hop_dong_trang_phuc', 8, 2)->nullable()->after('hoa_hong_hop_dong_cuoi');
        });
    }

    public function down(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->dropColumn([
                'hoa_hong_hop_dong_cuoi',
                'hoa_hong_hop_dong_trang_phuc',
            ]);
        });
    }
};
