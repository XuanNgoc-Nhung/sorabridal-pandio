<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class NhanVienPhongBanSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'nhan_vien_phong_ban';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/nhan_vien_phong_ban.php');
    }
}
