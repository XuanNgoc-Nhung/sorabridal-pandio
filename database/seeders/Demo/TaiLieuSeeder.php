<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class TaiLieuSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'tai_lieu';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/tai_lieu.php');
    }
}
