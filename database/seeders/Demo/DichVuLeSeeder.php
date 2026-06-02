<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class DichVuLeSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'dich_vu_le';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/dich_vu_le.php');
    }
}
