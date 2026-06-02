<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class NganHangThanhToanSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'ngan_hang_thanh_toan';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/ngan_hang_thanh_toan.php');
    }
}
