<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trang_phuc', function (Blueprint $table) {
            $table->id();
            $table->string('ten_san_pham');
            $table->string('ma_san_pham')->unique();
            $table->string('slug')->unique();
            $table->string('hinh_anh')->nullable();
            $table->text('mo_ta')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->string('trang_thai', 50)->default('active');
            $table->decimal('gia_tri', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('hop_dong_cho_thue_trang_phuc', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khach_hang');
            $table->string('sdt_khach_hang', 20);
            $table->date('ngay_thue');
            $table->date('ngay_tra_du_kien');
            $table->date('ngay_tra_chinh_thuc')->nullable();
            $table->unsignedInteger('so_ngay_thue')->default(1);
            $table->decimal('tong_tien', 15, 2)->default(0);
            $table->decimal('tien_coc', 15, 2)->default(0);
            $table->tinyInteger('trang_thai')->default(0);
            $table->text('ghi_chu')->nullable();
            $table->foreignId('nguoi_cho_thue')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('san_pham_cho_thue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hop_dong_id')
                ->constrained('hop_dong_cho_thue_trang_phuc')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('san_pham_id')
                ->constrained('trang_phuc')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('san_pham_cho_thue');
        Schema::dropIfExists('hop_dong_cho_thue_trang_phuc');
        Schema::dropIfExists('trang_phuc');
    }
};
