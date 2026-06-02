<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->json('ds_menu')->nullable()->after('ghi_chu');
        });
    }

    public function down(): void
    {
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->dropColumn('ds_menu');
        });
    }
};
