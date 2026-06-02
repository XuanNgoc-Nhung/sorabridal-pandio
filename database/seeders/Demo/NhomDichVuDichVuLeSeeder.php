<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class NhomDichVuDichVuLeSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'nhom_dich_vu_dich_vu_le';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/nhom_dich_vu_dich_vu_le.php');
    }
}
