<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phong_ban', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phong_ban');
            $table->string('ma_phong_ban')->unique();
            $table->text('mo_ta')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });

        Schema::create('nhan_vien', function (Blueprint $table) {
            $table->id();
            $table->string('hinh_anh')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('phong_ban')->nullable();
            $table->string('ngan_hang')->nullable();
            $table->string('chi_nhanh')->nullable();
            $table->string('so_tai_khoan')->nullable();
            $table->string('gioi_tinh', 10)->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->string('cccd', 20)->nullable();
            $table->string('vi_tri_lam_viec')->nullable();
            $table->date('ngay_vao_cong_ty')->nullable();
            $table->date('ngay_ky_hop_dong')->nullable();
            $table->unsignedBigInteger('luong_co_ban')->nullable();
            $table->unsignedBigInteger('luong_tang_ca')->nullable();
            $table->json('ds_menu')->nullable();
            $table->timestamps();
        });

        Schema::create('nhan_vien_phong_ban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhan_vien_id')->constrained('nhan_vien')->cascadeOnDelete();
            $table->foreignId('phong_ban_id')->constrained('phong_ban')->cascadeOnDelete();
            $table->unique(['nhan_vien_id', 'phong_ban_id']);
            $table->timestamps();
        });

        Schema::create('diem_danh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('gio_vao')->nullable();
            $table->dateTime('gio_ra')->nullable();
            $table->boolean('di_muon')->default(false);
            $table->boolean('hop_le')->default(false);
            $table->string('ly_do')->nullable();
            $table->boolean('nghi_phep')->default(false);
            $table->string('loai_phep')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->decimal('gio_lam_co_ban', 8, 2)->default(0);
            $table->decimal('gio_lam_tang_ca', 8, 2)->default(0);
            $table->decimal('luong_co_ban', 15, 2)->default(0);
            $table->decimal('luong_tang_ca', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cham_cong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('diem_danh_id')->constrained('diem_danh')->cascadeOnDelete();
            $table->date('ngay_diem_danh');
            $table->timestamps();
        });

        Schema::create('ngan_hang_thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->string('hinh_anh_logo', 500)->nullable();
            $table->string('ten_ngan_hang', 150);
            $table->string('ten_chi_tiet', 255)->nullable();
            $table->string('so_tai_khoan', 50);
            $table->string('chu_tai_khoan', 150);
            $table->string('chi_nhanh', 255)->nullable();
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });

        Schema::create('tai_lieu', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tai_lieu');
            $table->text('mo_ta')->nullable();
            $table->string('file');
            $table->string('duong_dan', 500);
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('tai_lieu');
        Schema::dropIfExists('ngan_hang_thanh_toan');
        Schema::dropIfExists('cham_cong');
        Schema::dropIfExists('diem_danh');
        Schema::dropIfExists('nhan_vien_phong_ban');
        Schema::dropIfExists('nhan_vien');
        Schema::dropIfExists('phong_ban');
    }
};
