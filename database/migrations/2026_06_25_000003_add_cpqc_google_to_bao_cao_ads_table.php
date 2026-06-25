<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bao_cao_ads', function (Blueprint $table) {
            $table->string('cpqc_google')->nullable()->after('ads_fb');
        });
    }

    public function down(): void
    {
        Schema::table('bao_cao_ads', function (Blueprint $table) {
            $table->dropColumn('cpqc_google');
        });
    }
};
