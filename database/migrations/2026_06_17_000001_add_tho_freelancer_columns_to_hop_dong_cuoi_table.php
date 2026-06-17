<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hop_dong_cuoi', function (Blueprint $table) {
            $table->string('tho_chup_freelancer')->nullable()->after('tho_chup_id');
            $table->string('tho_make_freelancer')->nullable()->after('tho_make_id');
            $table->string('tho_edit_freelancer')->nullable()->after('tho_edit_id');
        });
    }

    public function down(): void
    {
        Schema::table('hop_dong_cuoi', function (Blueprint $table) {
            $table->dropColumn([
                'tho_chup_freelancer',
                'tho_make_freelancer',
                'tho_edit_freelancer',
            ]);
        });
    }
};
