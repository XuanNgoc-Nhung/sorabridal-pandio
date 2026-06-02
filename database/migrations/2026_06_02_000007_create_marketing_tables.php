<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_khach_moi', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khach')->nullable();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->unsignedBigInteger('phu_trach_sale_id')->nullable();
            $table->date('ngay_hen_lich')->nullable();
            $table->date('ngay_den_thuc_te')->nullable();
            $table->string('nguon_khach')->nullable();
            $table->unsignedBigInteger('nguoi_tao_id')->nullable();
            $table->string('trang_thai', 50)->nullable();
            $table->text('ly_do_khong_chot')->nullable();
            $table->timestamps();

            $table->foreign('phu_trach_sale_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('nguoi_tao_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('note_khach_moi_phu_trach_sale', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_khach_moi_id')->constrained('note_khach_moi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['note_khach_moi_id', 'user_id']);
        });

        Schema::create('bao_cao_ads', function (Blueprint $table) {
            $table->id();
            $table->string('ngay')->nullable();
            $table->string('ads_tiktok')->nullable();
            $table->string('ads_fb')->nullable();
            $table->string('khach_moi')->nullable();
            $table->string('lich_hen')->nullable();
            $table->string('cpl')->nullable();
            $table->string('roas')->nullable();
            $table->string('ty_le_hen_tren_khach')->nullable();
            $table->string('khach_den_cua_hang')->nullable();
            $table->timestamps();
        });

        Schema::create('dang_ky_tu_van', function (Blueprint $table) {
            $table->id();
            $table->string('ten_co_dau', 150);
            $table->string('ten_chu_re', 150);
            $table->string('so_dien_thoai', 20);
            $table->date('ngay_cuoi_du_kien')->nullable();
            $table->string('phim_truong_quan_tam', 100)->nullable();
            $table->string('goi_dich_vu_quan_tam', 100)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dang_ky_tu_van');
        Schema::dropIfExists('bao_cao_ads');
        Schema::dropIfExists('note_khach_moi_phu_trach_sale');
        Schema::dropIfExists('note_khach_moi');
    }
};
