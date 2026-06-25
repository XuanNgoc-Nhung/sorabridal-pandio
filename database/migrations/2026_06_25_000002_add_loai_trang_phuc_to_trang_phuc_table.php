<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->foreignId('loai_trang_phuc')
                ->nullable()
                ->after('ma_san_pham')
                ->constrained('danh_muc_trang_phuc')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loai_trang_phuc');
        });
    }
};
