<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE note_khach_moi MODIFY ngay_hen_lich DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE note_khach_moi MODIFY ngay_hen_lich DATE NULL');
    }
};
