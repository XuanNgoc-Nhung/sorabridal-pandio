<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConceptController;
use App\Http\Controllers\Api\DichVuLeController;
use App\Http\Controllers\Api\DocumentationController;
use App\Http\Controllers\Api\HopDongCuoiController;
use App\Http\Controllers\Api\NganHangThanhToanController;
use App\Http\Controllers\Api\NhanSuController;
use App\Http\Controllers\Api\NhomDichVuController;
use App\Http\Controllers\Api\PhieuThuChiController;
use App\Http\Controllers\Api\PhongBanController;
use App\Http\Controllers\Api\TaiLieuController;
use App\Http\Controllers\Api\TrangPhucController;
use App\Http\Controllers\Api\TuVanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix: /api)
|--------------------------------------------------------------------------
| API cho app/mobile/third-party: chỉ trả JSON
| Header yêu cầu:
|   Accept: application/json
|   Authorization: Bearer {token} (với route cần đăng nhập)
*/

Route::get('/documents', [DocumentationController::class, 'index'])->name('api.documents');

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    Route::prefix('admin')->name('api.admin.')->group(function () {
        // Khách hàng (theo web.php)
        Route::prefix('khach-hang')->name('khach-hang.')->group(function () {
            Route::get('/danh-sach-hop-dong-cuoi', [HopDongCuoiController::class, 'index'])->name('danh-sach-hop-dong-cuoi');
        });

        // Alias cũ để không vỡ client API đang dùng
        Route::get('/hop-dong-cuoi', [HopDongCuoiController::class, 'index'])->name('hop-dong-cuoi.index');

        // Sản phẩm — Concept
        Route::prefix('concept')->name('concept.')->group(function () {
            Route::get('/', [ConceptController::class, 'index'])->name('index');
            Route::post('/', [ConceptController::class, 'store'])->name('store');
            Route::put('/{concept}', [ConceptController::class, 'update'])->name('update');
            Route::delete('/{concept}', [ConceptController::class, 'destroy'])->name('destroy');
        });

        // Sản phẩm — Dịch vụ lẻ & nhóm dịch vụ
        Route::prefix('dich-vu')->name('dich-vu.')->group(function () {
            Route::get('/dich-vu-le', [DichVuLeController::class, 'index'])->name('dich-vu-le.index');
            Route::post('/dich-vu-le', [DichVuLeController::class, 'store'])->name('dich-vu-le.store');
            Route::put('/dich-vu-le/{dichVuLe}', [DichVuLeController::class, 'update'])->name('dich-vu-le.update');
            Route::delete('/dich-vu-le/{dichVuLe}', [DichVuLeController::class, 'destroy'])->name('dich-vu-le.destroy');
            Route::get('/nhom-dich-vu', [NhomDichVuController::class, 'index'])->name('nhom-dich-vu.index');
            Route::post('/nhom-dich-vu', [NhomDichVuController::class, 'store'])->name('nhom-dich-vu.store');
            Route::put('/nhom-dich-vu/{nhomDichVu}', [NhomDichVuController::class, 'update'])->name('nhom-dich-vu.update');
            Route::delete('/nhom-dich-vu/{nhomDichVu}', [NhomDichVuController::class, 'destroy'])->name('nhom-dich-vu.destroy');
        });

        // Trang phục — sản phẩm
        Route::prefix('trang-phuc')->name('trang-phuc.')->group(function () {
            Route::get('/san-pham', [TrangPhucController::class, 'index'])->name('san-pham.index');
            Route::post('/san-pham', [TrangPhucController::class, 'store'])->name('san-pham.store');
            Route::put('/san-pham/{trangPhuc}', [TrangPhucController::class, 'update'])->name('san-pham.update');
            Route::delete('/san-pham/{trangPhuc}', [TrangPhucController::class, 'destroy'])->name('san-pham.destroy');
        });

        // Tư vấn
        Route::prefix('tu-van')->name('tu-van.')->group(function () {
            Route::get('/danh-sach', [TuVanController::class, 'index'])->name('danh-sach');
        });

        // Nhân sự
        Route::prefix('nhan-su')->name('nhan-su.')->group(function () {
            Route::get('/danh-sach', [NhanSuController::class, 'index'])->name('danh-sach.index');
            Route::post('/danh-sach', [NhanSuController::class, 'store'])->name('danh-sach.store');
            Route::put('/danh-sach/{user}', [NhanSuController::class, 'update'])->name('danh-sach.update');
            Route::put('/doi-mat-khau/{user}', [NhanSuController::class, 'doiMatKhau'])->name('doi-mat-khau');
            Route::delete('/danh-sach/{user}', [NhanSuController::class, 'destroy'])->name('danh-sach.destroy');
        });

        // Tài chính — phiếu thu chi
        Route::prefix('tai-chinh')->name('tai-chinh.')->group(function () {
            Route::get('/phieu-thu-chi', [PhieuThuChiController::class, 'index'])->name('phieu-thu-chi.index');
            Route::post('/phieu-thu-chi', [PhieuThuChiController::class, 'store'])->name('phieu-thu-chi.store');
            Route::put('/phieu-thu-chi/{phieuThuChi}', [PhieuThuChiController::class, 'update'])->name('phieu-thu-chi.update');
            Route::put('/phieu-thu-chi/{phieuThuChi}/duyet', [PhieuThuChiController::class, 'duyet'])->name('phieu-thu-chi.duyet');
            Route::put('/phieu-thu-chi/{phieuThuChi}/huy', [PhieuThuChiController::class, 'huy'])->name('phieu-thu-chi.huy');
            Route::delete('/phieu-thu-chi/{phieuThuChi}', [PhieuThuChiController::class, 'destroy'])->name('phieu-thu-chi.destroy');
        });

        // Hệ thống
        Route::prefix('he-thong')->name('he-thong.')->group(function () {
            // web.php đang ẩn menu này, nhưng API vẫn giữ để phục vụ app/đối tác khi cần
            Route::get('/ngan-hang-thanh-toan', [NganHangThanhToanController::class, 'index'])->name('ngan-hang-thanh-toan.index');
            Route::post('/ngan-hang-thanh-toan', [NganHangThanhToanController::class, 'store'])->name('ngan-hang-thanh-toan.store');
            Route::put('/ngan-hang-thanh-toan/{nganHangThanhToan}', [NganHangThanhToanController::class, 'update'])->name('ngan-hang-thanh-toan.update');
            Route::delete('/ngan-hang-thanh-toan/{nganHangThanhToan}', [NganHangThanhToanController::class, 'destroy'])->name('ngan-hang-thanh-toan.destroy');

            Route::get('/phong-ban', [PhongBanController::class, 'index'])->name('phong-ban.index');
            Route::post('/phong-ban', [PhongBanController::class, 'store'])->name('phong-ban.store');
            Route::put('/phong-ban/{phongBan}', [PhongBanController::class, 'update'])->name('phong-ban.update');
            Route::delete('/phong-ban/{phongBan}', [PhongBanController::class, 'destroy'])->name('phong-ban.destroy');

            Route::get('/tai-lieu', [TaiLieuController::class, 'index'])->name('tai-lieu.index');
            Route::post('/tai-lieu', [TaiLieuController::class, 'store'])->name('tai-lieu.store');
            Route::delete('/tai-lieu/{taiLieu}', [TaiLieuController::class, 'destroy'])->name('tai-lieu.destroy');
        });
    });
});
