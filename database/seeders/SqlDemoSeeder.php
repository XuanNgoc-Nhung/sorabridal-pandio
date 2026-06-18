<?php

namespace Database\Seeders;

use Database\Seeders\Demo\BaoCaoAdsSeeder;
use Database\Seeders\Demo\DangKyTuVanSeeder;
use Database\Seeders\Demo\DichVuLeSeeder;
use Database\Seeders\Demo\HopDongCuoiDichVuLeSeeder;
use Database\Seeders\Demo\HopDongCuoiNhomDichVuSeeder;
use Database\Seeders\Demo\HopDongCuoiSeeder;
use Database\Seeders\Demo\HopDongCuoiThanhVienSaleSeeder;
use Database\Seeders\Demo\NganHangThanhToanSeeder;
use Database\Seeders\Demo\NhanVienSeeder;
use Database\Seeders\Demo\NhomDichVuDichVuLeSeeder;
use Database\Seeders\Demo\NhomDichVuSeeder;
use Database\Seeders\Demo\NoteKhachMoiPhuTrachSaleSeeder;
use Database\Seeders\Demo\NoteKhachMoiSeeder;
use Database\Seeders\Demo\PhongBanSeeder;
use Database\Seeders\Demo\TaiLieuSeeder;
use Database\Seeders\Demo\TrangPhucSeeder;
use Database\Seeders\Demo\UsersSeeder;
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
            $this->call([
                UsersSeeder::class,
                PhongBanSeeder::class,
                NhanVienSeeder::class,
                NganHangThanhToanSeeder::class,
                DichVuLeSeeder::class,
                NhomDichVuSeeder::class,
                NhomDichVuDichVuLeSeeder::class,
                TrangPhucSeeder::class,
                DangKyTuVanSeeder::class,
                BaoCaoAdsSeeder::class,
                NoteKhachMoiSeeder::class,
                NoteKhachMoiPhuTrachSaleSeeder::class,
                TaiLieuSeeder::class,
                HopDongCuoiSeeder::class,
                HopDongCuoiDichVuLeSeeder::class,
                HopDongCuoiNhomDichVuSeeder::class,
                HopDongCuoiThanhVienSaleSeeder::class,
            ]);
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}
