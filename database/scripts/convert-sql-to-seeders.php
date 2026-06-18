<?php

/**
 * Chuyển INSERT từ sql_ngocyen_one.sql sang file data PHP + seeders Laravel (SqlDemo).
 * Chạy: php database/scripts/convert-sql-to-seeders.php
 */

$sqlFile = dirname(__DIR__) . '/sql_ngocyen_one.sql';
$dataDir = dirname(__DIR__) . '/seeders/data/demo';
$seederDir = dirname(__DIR__) . '/seeders/Demo';

$skipTables = ['migrations', 'personal_access_tokens'];

$seedOrder = [
    'users',
    'phong_ban',
    'nhan_vien',
    'ngan_hang_thanh_toan',
    'dich_vu_le',
    'nhom_dich_vu',
    'nhom_dich_vu_dich_vu_le',
    'trang_phuc',
    'dang_ky_tu_van',
    'bao_cao_ads',
    'note_khach_moi',
    'note_khach_moi_phu_trach_sale',
    'tai_lieu',
    'hop_dong_cuoi',
    'hop_dong_cuoi_dich_vu_le',
    'hop_dong_cuoi_nhom_dich_vu',
    'hop_dong_cuoi_thanh_vien_sale',
];

if (! is_file($sqlFile)) {
    fwrite(STDERR, "Không tìm thấy: {$sqlFile}\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
$tables = [];

preg_match_all(
    '/INSERT INTO `([^`]+)` \(([^)]+)\) VALUES\s*(.+?);/s',
    $sql,
    $matches,
    PREG_SET_ORDER
);

foreach ($matches as $match) {
    $table = $match[1];
    if (in_array($table, $skipTables, true)) {
        continue;
    }

    $columns = array_map(
        static fn (string $col) => trim($col, " `\t\n\r"),
        explode(',', $match[2])
    );

    $rows = parseTuples($match[3]);
    $records = [];

    foreach ($rows as $row) {
        $values = parseRowValues($row);
        if (count($values) !== count($columns)) {
            fwrite(STDERR, "Cột không khớp ở bảng {$table}: ".count($columns).' vs '.count($values)."\n");
            exit(1);
        }
        $records[] = array_combine($columns, $values);
    }

    $tables[$table] = $records;
}

if (! is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
if (! is_dir($seederDir)) {
    mkdir($seederDir, 0755, true);
}

foreach ($tables as $table => $records) {
    $path = "{$dataDir}/{$table}.php";
    $export = var_export($records, true);
    file_put_contents($path, "<?php\n\nreturn {$export};\n");
    echo "Data: {$table} (".count($records)." rows)\n";
}

foreach ($seedOrder as $table) {
    if (! isset($tables[$table])) {
        continue;
    }
    $className = tableToClassName($table).'Seeder';
    $seederPath = "{$seederDir}/{$className}.php";
    $dataPath = "database/seeders/data/demo/{$table}.php";

    $content = <<<PHP
<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Concerns\SeedsTableFromDataFile;
use Illuminate\Database\Seeder;

class {$className} extends Seeder
{
    use SeedsTableFromDataFile;

    protected string \$table = '{$table}';

    protected function dataFile(): string
    {
        return database_path('seeders/data/demo/{$table}.php');
    }
}

PHP;
    file_put_contents($seederPath, $content);
    echo "Seeder: {$className}\n";
}

// Master seeder
$calls = array_map(static function (string $table) {
    return '                '.tableToClassName($table).'Seeder::class,';
}, array_filter($seedOrder, static fn (string $t) => isset($tables[$t])));

$masterPath = dirname(__DIR__) . '/seeders/SqlDemoSeeder.php';
$callList = implode("\n", $calls);

$useStatements = array_map(
    static fn (string $table) => 'use Database\\Seeders\\Demo\\'.tableToClassName($table).'Seeder;',
    array_filter($seedOrder, static fn (string $t) => isset($tables[$t]))
);
$useBlock = implode("\n", $useStatements);

file_put_contents($masterPath, <<<PHP
<?php

namespace Database\Seeders;

{$useBlock}
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Nhập dữ liệu từ dump sql_ngocyen_one.sql (chỉ INSERT, không tạo bảng).
 *
 * Chạy: php artisan db:seed --class=SqlDemoSeeder
 */
class SqlDemoSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            \$this->call([
{$callList}
            ]);
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}

PHP);

echo "Master: SqlDemoSeeder.php\n";
echo "Hoàn tất.\n";

function tableToClassName(string $table): string
{
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
}

/**
 * @return list<string>
 */
function parseTuples(string $valuesPart): array
{
    $valuesPart = trim($valuesPart);
    $tuples = [];
    $depth = 0;
    $start = null;
    $inString = false;
    $escape = false;
    $len = strlen($valuesPart);

    for ($i = 0; $i < $len; $i++) {
        $ch = $valuesPart[$i];

        if ($escape) {
            $escape = false;
            continue;
        }

        if ($inString) {
            if ($ch === '\\') {
                $escape = true;
            } elseif ($ch === "'") {
                $inString = false;
            }
            continue;
        }

        if ($ch === "'") {
            $inString = true;
            continue;
        }

        if ($ch === '(') {
            if ($depth === 0) {
                $start = $i + 1;
            }
            $depth++;
            continue;
        }

        if ($ch === ')') {
            $depth--;
            if ($depth === 0 && $start !== null) {
                $tuples[] = substr($valuesPart, $start, $i - $start);
                $start = null;
            }
        }
    }

    return $tuples;
}

/**
 * @return list<mixed>
 */
function parseRowValues(string $tuple): array
{
    $values = [];
    $current = '';
    $inString = false;
    $escape = false;
    $len = strlen($tuple);

    for ($i = 0; $i < $len; $i++) {
        $ch = $tuple[$i];

        if ($escape) {
            $current .= $ch;
            $escape = false;
            continue;
        }

        if ($inString) {
            if ($ch === '\\') {
                $escape = true;
            } elseif ($ch === "'") {
                $inString = false;
            } else {
                $current .= $ch;
            }
            continue;
        }

        if ($ch === "'") {
            $inString = true;
            continue;
        }

        if ($ch === ',') {
            $values[] = castSqlValue(trim($current));
            $current = '';
            continue;
        }

        $current .= $ch;
    }

    if ($current !== '' || str_ends_with($tuple, ',')) {
        $values[] = castSqlValue(trim($current));
    }

    return $values;
}

function castSqlValue(string $raw): mixed
{
    if ($raw === 'NULL') {
        return null;
    }

    if (preg_match('/^-?\d+$/', $raw)) {
        return (int) $raw;
    }

    if (preg_match('/^-?\d+\.\d+$/', $raw)) {
        return $raw;
    }

    return $raw;
}
