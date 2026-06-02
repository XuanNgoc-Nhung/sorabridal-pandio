<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class NoteKhachMoiPhuTrachSaleSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'note_khach_moi_phu_trach_sale';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/note_khach_moi_phu_trach_sale.php');
    }
}
