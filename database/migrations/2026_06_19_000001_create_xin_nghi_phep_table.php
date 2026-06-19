<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xin_nghi_phep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('loai_nghi_phep', 50);
            // di_muon | ve_som | nua_ngay | ca_ngay | nhieu_ngay
            $table->string('buoi_nghi', 20)->nullable();
            // sang | chieu (chỉ áp dụng khi loai_nghi_phep = nua_ngay)
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc')->nullable();
            $table->text('ly_do');
            $table->string('trang_thai', 20)->default('cho_duyet');
            // cho_duyet | da_duyet | tu_choi
            $table->foreignId('nguoi_duyet')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xin_nghi_phep');
    }
};
