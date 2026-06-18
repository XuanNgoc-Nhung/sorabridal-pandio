<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('nhan_vien_phong_ban');
    }

    public function down(): void
    {
        // Bảng pivot không còn dùng; liên kết nhân viên–phòng ban qua nhan_vien.phong_ban = phong_ban.ma_phong_ban.
    }
};
