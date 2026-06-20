<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->renameColumn('ip_diem_danh', 'ip_checkin');
        });

        Schema::table('diem_danh', function (Blueprint $table) {
            $table->string('ip_checkout', 45)->nullable()->after('ip_checkin');
        });
    }

    public function down(): void
    {
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->dropColumn('ip_checkout');
        });

        Schema::table('diem_danh', function (Blueprint $table) {
            $table->renameColumn('ip_checkin', 'ip_diem_danh');
        });
    }
};
