<?php

namespace Database\Seeders\Demo\Concerns;

use Illuminate\Support\Facades\DB;

trait SeedsTableFromDataFile
{
    abstract protected function dataFile(): string;

    public function run(): void
    {
        $rows = require $this->dataFile();

        if ($rows === []) {
            return;
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table($this->table)->insertOrIgnore($chunk);
        }

        $maxId = collect($rows)->max('id');
        if ($maxId !== null) {
            DB::statement(
                "ALTER TABLE `{$this->table}` AUTO_INCREMENT = ".((int) $maxId + 1)
            );
        }
    }
}
