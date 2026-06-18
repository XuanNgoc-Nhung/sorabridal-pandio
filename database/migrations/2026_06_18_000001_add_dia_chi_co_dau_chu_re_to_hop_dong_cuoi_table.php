<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hop_dong_cuoi', function (Blueprint $table) {
            $table->string('dia_chi_chu_re', 500)->nullable()->after('email_sdt_chu_re');
            $table->string('dia_chi_co_dau', 500)->nullable()->after('dia_chi_chu_re');
        });
    }

    public function down(): void
    {
        Schema::table('hop_dong_cuoi', function (Blueprint $table) {
            $table->dropColumn(['dia_chi_chu_re', 'dia_chi_co_dau']);
        });
    }
};
