<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class DangKyTuVanSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'dang_ky_tu_van';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/dang_ky_tu_van.php');
    }
}
