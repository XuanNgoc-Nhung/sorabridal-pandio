<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concept', function (Blueprint $table) {
            $table->id();
            $table->string('ten_concept');
            $table->string('hinh_anh')->nullable();
            $table->unsignedTinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });

        Schema::create('dich_vu_le', function (Blueprint $table) {
            $table->id();
            $table->string('ten_dich_vu');
            $table->string('ma_dich_vu')->unique();
            $table->string('slug')->nullable()->unique();
            $table->text('mo_ta')->nullable();
            $table->unsignedTinyInteger('trang_thai')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->decimal('gia_dich_vu', 15, 2)->nullable();
            $table->string('don_vi')->nullable();
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phong_ban_id', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('nhom_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nhom');
            $table->string('ma_nhom')->unique();
            $table->string('slug')->unique();
            $table->decimal('gia_tien', 15, 2)->default(0);
            $table->decimal('gia_goc', 15, 2)->default(0);
            $table->text('the')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->text('mo_ta')->nullable();
            $table->unsignedTinyInteger('trang_thai')->default(1);
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('nhom_dich_vu_dich_vu_le', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhom_dich_vu_id')->constrained('nhom_dich_vu')->cascadeOnDelete();
            $table->foreignId('dich_vu_le_id')->constrained('dich_vu_le')->cascadeOnDelete();
            $table->unsignedInteger('so_luong')->default(1);
            $table->timestamps();

            $table->unique(['nhom_dich_vu_id', 'dich_vu_le_id'], 'uniq_ndv_dvl_pair');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhom_dich_vu_dich_vu_le');
        Schema::dropIfExists('nhom_dich_vu');
        Schema::dropIfExists('dich_vu_le');
        Schema::dropIfExists('concept');
    }
};
