<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class NhomDichVuSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'nhom_dich_vu';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/nhom_dich_vu.php');
    }
}
