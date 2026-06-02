<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class HopDongCuoiNhomDichVuSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'hop_dong_cuoi_nhom_dich_vu';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/hop_dong_cuoi_nhom_dich_vu.php');
    }
}
