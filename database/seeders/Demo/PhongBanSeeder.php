<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class PhongBanSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'phong_ban';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/phong_ban.php');
    }
}
