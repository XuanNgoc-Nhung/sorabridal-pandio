<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class TrangPhucSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'trang_phuc';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/trang_phuc.php');
    }
}
