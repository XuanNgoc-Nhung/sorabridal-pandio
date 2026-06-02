<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phieu_thu_chi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_tao_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->tinyInteger('loai_phieu');
            $table->decimal('so_tien', 15, 2)->default(0);
            $table->string('ly_do');
            $table->tinyInteger('trang_thai')->default(0);
            $table->text('ghi_chu')->nullable();
            $table->dateTime('ngay_duyet')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phieu_thu_chi');
    }
};
