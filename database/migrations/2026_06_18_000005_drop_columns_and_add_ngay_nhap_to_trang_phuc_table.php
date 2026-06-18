<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->dropColumn(['mo_ta', 'slug', 'loai']);
            $table->string('ngay_nhap')->nullable()->after('ma_san_pham');
        });
    }

    public function down(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->dropColumn('ngay_nhap');
            $table->string('slug')->unique()->after('ma_san_pham');
            $table->string('loai', 100)->nullable()->after('slug');
            $table->text('mo_ta')->nullable()->after('hinh_anh');
        });
    }
};
