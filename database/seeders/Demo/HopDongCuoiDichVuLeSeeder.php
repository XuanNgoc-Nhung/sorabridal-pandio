<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class HopDongCuoiDichVuLeSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'hop_dong_cuoi_dich_vu_le';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/hop_dong_cuoi_dich_vu_le.php');
    }
}
