<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chot_luong_thang', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('thang');
            $table->unsignedSmallInteger('nam');
            $table->foreignId('nguoi_chot_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('ngay_chot');
            $table->json('du_lieu');
            $table->timestamps();

            $table->unique(['thang', 'nam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chot_luong_thang');
    }
};
