<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class BaoCaoAdsSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'bao_cao_ads';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/bao_cao_ads.php');
    }
}
