<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class NhanVienSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'nhan_vien';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/nhan_vien.php');
    }
}
