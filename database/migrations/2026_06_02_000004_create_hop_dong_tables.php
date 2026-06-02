<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hop_dong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hop_dong')->nullable()->unique();
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tho_chup_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->foreignId('tho_make_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->foreignId('tho_edit_id')->nullable()->constrained('nhan_vien')->nullOnDelete();
            $table->string('dia_diem')->nullable();
            $table->dateTime('ngay_chup')->nullable();
            $table->text('trang_phuc')->nullable();
            $table->text('concept')->nullable();
            $table->text('ghi_chu_chup')->nullable();
            $table->string('trang_thai_chup', 50)->nullable();
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->string('nguoi_gioi_thieu')->nullable();
            $table->decimal('so_tien_giam_gia', 14, 2)->nullable();
            $table->decimal('thanh_toan_lan_1', 15, 2)->nullable();
            $table->string('anh_thanh_toan_1')->nullable();
            $table->decimal('thanh_toan_lan_2', 15, 2)->nullable();
            $table->string('anh_thanh_toan_2')->nullable();
            $table->decimal('thanh_toan_lan_3', 15, 2)->nullable();
            $table->string('anh_thanh_toan_3')->nullable();
            $table->string('trang_thai_hop_dong', 50)->nullable();
            $table->string('trang_thai_edit', 50)->nullable();
            $table->string('link_file_demo')->nullable();
            $table->string('link_file_in')->nullable();
            $table->date('ngay_tra_link_in')->nullable();
            $table->date('ngay_hen_tra_hang')->nullable();
            $table->timestamps();
        });

        Schema::create('hop_dong_dich_vu_le', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_id')->constrained('hop_dong')->cascadeOnDelete();
            $table->foreignId('dich_vu_le_id')->constrained('dich_vu_le')->cascadeOnDelete();
            $table->decimal('gia_goc', 15, 2)->default(0);
            $table->decimal('gia_thuc', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['hop_dong_id', 'dich_vu_le_id']);
        });

        Schema::create('dich_vu_trong_hop_dong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hop_dong')->constrained('hop_dong')->cascadeOnDelete();
            $table->foreignId('id_dich_vu')->constrained('dich_vu_le')->cascadeOnDelete();
            $table->unsignedInteger('so_luong')->default(1);
            $table->decimal('gia_goc', 15, 2)->default(0);
            $table->decimal('gia_thuc', 15, 2)->default(0);
            $table->decimal('thanh_tien', 15, 2)->default(0);
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->unique(['id_hop_dong', 'id_dich_vu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dich_vu_trong_hop_dong');
        Schema::dropIfExists('hop_dong_dich_vu_le');
        Schema::dropIfExists('hop_dong');
    }
};
