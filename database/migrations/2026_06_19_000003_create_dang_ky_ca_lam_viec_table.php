<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dang_ky_ca_lam_viec', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ca_lam_id')->constrained('ca_lam_viec')->cascadeOnDelete();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->date('ngay_lam');
            $table->unique(['ca_lam_id', 'nguoi_dung_id', 'ngay_lam']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dang_ky_ca_lam_viec');
    }
};
