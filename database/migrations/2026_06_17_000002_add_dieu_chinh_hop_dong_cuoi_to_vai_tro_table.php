<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->boolean('dieu_chinh_hop_dong_cuoi')->nullable()->default(null)->after('ds_menu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->dropColumn('dieu_chinh_hop_dong_cuoi');
        });
    }
};
