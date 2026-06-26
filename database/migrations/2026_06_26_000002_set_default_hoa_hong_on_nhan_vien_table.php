<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('nhan_vien')->whereNull('hoa_hong_hop_dong_cuoi')->update(['hoa_hong_hop_dong_cuoi' => 1]);
        DB::table('nhan_vien')->whereNull('hoa_hong_hop_dong_trang_phuc')->update(['hoa_hong_hop_dong_trang_phuc' => 1]);

        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->decimal('hoa_hong_hop_dong_cuoi', 8, 2)->default(1)->change();
            $table->decimal('hoa_hong_hop_dong_trang_phuc', 8, 2)->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->decimal('hoa_hong_hop_dong_cuoi', 8, 2)->nullable()->default(null)->change();
            $table->decimal('hoa_hong_hop_dong_trang_phuc', 8, 2)->nullable()->default(null)->change();
        });
    }
};
