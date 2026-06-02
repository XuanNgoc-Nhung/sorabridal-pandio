<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    use SeedsTableFromDataFile;

    protected string $table = 'users';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/users.php');
    }
}
