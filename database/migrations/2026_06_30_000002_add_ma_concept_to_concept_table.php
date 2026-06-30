<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('concept', function (Blueprint $table) {
            $table->string('ma_concept')->nullable()->unique()->after('ten_concept');
        });
    }

    public function down(): void
    {
        Schema::table('concept', function (Blueprint $table) {
            $table->dropUnique(['ma_concept']);
            $table->dropColumn('ma_concept');
        });
    }
};
