<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->string('loai', 100)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('trang_phuc', function (Blueprint $table) {
            $table->string('loai', 20)->default('cuoi')->nullable(false)->change();
        });
    }
};
