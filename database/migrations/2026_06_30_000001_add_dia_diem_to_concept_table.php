<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('concept', function (Blueprint $table) {
            $table->string('dia_diem')->nullable()->after('ten_concept');
        });
    }

    public function down(): void
    {
        Schema::table('concept', function (Blueprint $table) {
            $table->dropColumn('dia_diem');
        });
    }
};
