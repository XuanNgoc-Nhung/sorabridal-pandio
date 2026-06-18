<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_diem_danh', function (Blueprint $table) {
            $table->id();
            $table->string('ten_ip');
            $table->string('dia_chi_ip', 45)->unique();
            $table->text('ghi_chu')->nullable();
            $table->unsignedTinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_diem_danh');
    }
};
