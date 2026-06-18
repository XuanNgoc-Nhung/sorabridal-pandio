<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController as Admin;
use App\Http\Controllers\Admin\NhanSuController as AdminNhanSu;
use App\Http\Controllers\Admin\DichVuController as AdminDichVu;
use App\Http\Controllers\Admin\KhachHangController as AdminKhachHang;
use App\Http\Controllers\Admin\TrangPhucController as AdminTrangPhuc;
use App\Http\Controllers\Admin\TaiChinhKeToanController as AdminTaiChinhKeToan;
use App\Http\Controllers\Admin\DiemDanhController as AdminDiemDanh;
use App\Http\Controllers\Admin\ConceptController as AdminConcept;
use App\Http\Controllers\Admin\HeThongController as AdminHeThong;
use App\Http\Controllers\Admin\TuVanController as AdminTuVan;
use App\Http\Controllers\Admin\MarketingController as AdminMarketing;
use App\Http\Controllers\Admin\BaoCaoController as AdminBaoCao;

// Trang chủ: chưa đăng nhập → login; đã đăng nhập → admin
Route::get('/', HomeController::class);

// Route trang user (tạm tắt)
// Route::get('/', [UserController::class, 'home'])->name('user.home');
// Route::view('/dich-vu', 'user.dich-vu')->name('user.dich-vu');
// Route::view('/news-concept', 'user.news-concept')->name('user.news-concept');
// Route::view('/trang-phuc', 'user.trang-phuc')->name('user.trang-phuc');
// Route::view('/khach-hang', 'user.khach-hang')->name('user.khach-hang');
// Route::view('/blog-cuoi', 'user.blog-cuoi')->name('user.blog-cuoi');
// Route::get('/dat-lich', [UserController::class, 'showDatLichForm'])->name('user.dat-lich');
// Route::post('/dat-lich', [UserController::class, 'storeDatLich'])->name('user.dat-lich.store');
// Route::view('/sora_bridal_ugc_policy.html', 'user.sora_bridal_ugc_policy')->name('user.sora_bridal_ugc_policy');
// Route::get('/about', function () {
//     return view('user.about');
// });
// Route::get('/information', function () {
//     return view('user.information');
// });
// Đăng nhập / Đăng xuất / Đăng ký (guest mới vào được login, register; auth mới vào được logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/dang-xuat', [AuthController::class, 'logout'])->name('dang-xuat')->middleware('auth');
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'checkRoute']], function () {
    // Tổng quan
    Route::get('/', [Admin::class, 'index'])->name('index');
    Route::view('/chua-duoc-phan-quyen', 'admin.chua-duoc-phan-quyen')->name('chua-duoc-phan-quyen');

    // Hợp đồng cưới (khách hàng)
    Route::group(['prefix' => 'khach-hang'], function () {
        Route::get('/danh-sach-hop-dong-cuoi', [AdminKhachHang::class, 'danhSachHopDongCuoi'])->name('khach-hang.danh-sach-hop-dong-cuoi');
        Route::get('/hop-dong-cuoi/{hopDongCuoi}/dieu-phoi/nhan-vien-theo-ngay', [AdminKhachHang::class, 'nhanVienChoDieuPhoiTheoNgayChup'])->name('khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay');
        Route::put('/hop-dong-cuoi/{hopDongCuoi}/dieu-phoi', [AdminKhachHang::class, 'capNhatDieuPhoiHopDongCuoi'])->name('khach-hang.hop-dong-cuoi.dieu-phoi');
        Route::get('/hop-dong-cuoi/{hopDongCuoi}/thanh-toan', [AdminKhachHang::class, 'thongTinThanhToanHopDongCuoi'])->name('khach-hang.hop-dong-cuoi.thanh-toan');
        Route::post('/hop-dong-cuoi/{hopDongCuoi}/thanh-toan', [AdminKhachHang::class, 'luuThanhToanHopDongCuoi'])->name('khach-hang.hop-dong-cuoi.thanh-toan.luu');
        Route::put('/hop-dong-cuoi/{hopDongCuoi}/huy', [AdminKhachHang::class, 'huyHopDongCuoi'])->name('khach-hang.hop-dong-cuoi.huy');
        Route::get('/hop-dong-cuoi/{hopDongCuoi}/chinh-sua', [AdminKhachHang::class, 'chinhSuaHopDongCuoi'])->name('khach-hang.chinh-sua-hop-dong-cuoi');
        Route::get('/tao-hop-dong-canh-bao', [AdminKhachHang::class, 'taoHopDongCanhBao'])->name('khach-hang.tao-hop-dong-canh-bao');
        Route::get('/tao-hop-dong', [AdminKhachHang::class, 'taoHopDong'])->name('khach-hang.tao-hop-dong');
        Route::put('/hop-dong-cuoi/{hopDongCuoi}/tao-hop-dong-buoc-1', [AdminKhachHang::class, 'capNhatTaoHopDongBuoc1'])->name('khach-hang.tao-hop-dong.cap-nhat-buoc-1');
        Route::put('/hop-dong-cuoi/{hopDongCuoi}/tao-hop-dong-buoc-2', [AdminKhachHang::class, 'capNhatTaoHopDongBuoc2'])->name('khach-hang.tao-hop-dong.cap-nhat-buoc-2');
        Route::put('/hop-dong-cuoi/{hopDongCuoi}/tao-hop-dong-buoc-3', [AdminKhachHang::class, 'capNhatTaoHopDongBuoc3'])->name('khach-hang.tao-hop-dong.cap-nhat-buoc-3');
        Route::post('/hop-dong-cuoi/tao-hop-dong/kiem-tra-ma-giam-gia', [AdminKhachHang::class, 'kiemTraMaGiamGiaTaoHopDong'])->name('khach-hang.tao-hop-dong.kiem-tra-ma-giam-gia');
        Route::post('/hop-dong-cuoi', [AdminKhachHang::class, 'storeHopDongCuoi'])->name('khach-hang.store-hop-dong-cuoi');
    });

    // Trang phục & hợp đồng thuê
    Route::group(['prefix' => 'trang-phuc'], function () {
        Route::get('/san-pham', [AdminTrangPhuc::class, 'sanPham'])->name('trang-phuc.san-pham');
        Route::post('/san-pham', [AdminTrangPhuc::class, 'storeSanPham'])->name('trang-phuc.san-pham.store');
        Route::get('/san-pham/{trangPhuc}/kiem-tra', [AdminTrangPhuc::class, 'kiemTraSuDungSanPham'])->name('trang-phuc.san-pham.kiem-tra');
        Route::put('/san-pham/{trangPhuc}', [AdminTrangPhuc::class, 'updateSanPham'])->name('trang-phuc.san-pham.update');
        Route::patch('/san-pham/{trangPhuc}/trang-thai', [AdminTrangPhuc::class, 'updateSanPhamTrangThai'])->name('trang-phuc.san-pham.update-trang-thai');
        Route::delete('/san-pham/{trangPhuc}', [AdminTrangPhuc::class, 'destroySanPham'])->name('trang-phuc.san-pham.destroy');
        Route::get('/hop-dong/tim-san-pham', [AdminTrangPhuc::class, 'timSanPhamHopDong'])->name('trang-phuc.hop-dong.tim-san-pham');
        Route::get('/hop-dong', [AdminTrangPhuc::class, 'hopDong'])->name('trang-phuc.hop-dong');
        Route::post('/hop-dong', [AdminTrangPhuc::class, 'storeHopDong'])->name('trang-phuc.store-hop-dong');
        Route::put('/hop-dong/{hopDong}', [AdminTrangPhuc::class, 'updateHopDong'])->name('trang-phuc.update-hop-dong');
        Route::patch('/hop-dong/{hopDong}/trang-thai', [AdminTrangPhuc::class, 'updateHopDongTrangThai'])->name('trang-phuc.update-hop-dong-trang-thai');
        Route::delete('/hop-dong/{hopDong}', [AdminTrangPhuc::class, 'destroyHopDong'])->name('trang-phuc.destroy-hop-dong');
    });

    // Lịch làm việc (menu độc lập)
    Route::group(['prefix' => 'lich-lam-viec'], function () {
        Route::get('/', [AdminNhanSu::class, 'lichLamViec'])->name('lich-lam-viec');
        Route::get('/data', [AdminNhanSu::class, 'lichLamViecData'])->name('lich-lam-viec.data');
        Route::get('/danh-sach', [AdminNhanSu::class, 'lichLamViecDanhSach'])->name('lich-lam-viec.danh-sach');
        Route::get('/chi-tiet-ngay', [AdminNhanSu::class, 'lichLamViecChiTietNgay'])->name('lich-lam-viec.chi-tiet-ngay');
        Route::get('/hop-dong-chua-phan-ngay', [AdminNhanSu::class, 'lichLamViecHopDongChuaPhanNgay'])
            ->name('lich-lam-viec.hop-dong-chua-phan-ngay');
        Route::get('/chua-phan-cong', [AdminNhanSu::class, 'lichLamViecChuaPhanCong'])
            ->name('lich-lam-viec.chua-phan-cong');
        Route::get('/hop-dong/{hopDongCuoi}/dieu-phoi-data', [AdminNhanSu::class, 'lichLamViecHopDongDieuPhoiData'])
            ->name('lich-lam-viec.hop-dong-dieu-phoi-data');
        Route::post('/tao-lich', [AdminNhanSu::class, 'lichLamViecTaoLich'])->name('lich-lam-viec.tao-lich');
    });

    // Tư vấn
    Route::group(['prefix' => 'tu-van'], function () {
        Route::get('/danh-sach', [AdminTuVan::class, 'danhSach'])->name('tu-van.danh-sach');
    });

    // Điểm danh & chấm công
    Route::group(['prefix' => 'diem-danh'], function () {
        Route::get('/', [AdminDiemDanh::class, 'diemDanh'])->name('diem-danh.diem-danh');
        Route::get('/cham-cong', [AdminDiemDanh::class, 'chamCong'])->name('diem-danh.cham-cong');
        Route::post('/check-in', [AdminDiemDanh::class, 'checkIn'])->name('diem-danh.check-in');
        Route::post('/check-out', [AdminDiemDanh::class, 'checkOut'])->name('diem-danh.check-out');
    });

    // Nhân sự
    Route::group(['prefix' => 'nhan-su'], function () {
        Route::get('/danh-sach', [AdminNhanSu::class, 'danhSach'])->name('nhan-su.danh-sach');
        Route::post('/danh-sach', [AdminNhanSu::class, 'store'])->name('nhan-su.store');
        Route::put('/danh-sach/{user}', [AdminNhanSu::class, 'update'])->name('nhan-su.update');
        Route::put('/doi-mat-khau/{user}', [AdminNhanSu::class, 'doiMatKhau'])->name('nhan-su.doi-mat-khau');
        Route::delete('/danh-sach/{user}', [AdminNhanSu::class, 'destroy'])->name('nhan-su.destroy');
        Route::get('/cong-viec-cua-toi', [AdminNhanSu::class, 'congViecCuaToi'])->name('nhan-su.cong-viec-cua-toi');
        Route::put('/cong-viec-cua-toi/{hopDongCuoi}/cap-nhat-link', [AdminNhanSu::class, 'capNhatLinkFileCongViec'])
            ->name('nhan-su.cong-viec-cua-toi.cap-nhat-link');
    });

    // Sản phẩm — Concept
    Route::group(['prefix' => 'concept'], function () {
        Route::get('/', [AdminConcept::class, 'concept'])->name('concept.concept');
        Route::post('/', [AdminConcept::class, 'store'])->name('concept.concept.store');
        Route::put('/{concept}', [AdminConcept::class, 'update'])->name('concept.concept.update');
        Route::delete('/{concept}', [AdminConcept::class, 'destroy'])->name('concept.concept.destroy');
    });

    // Sản phẩm — Dịch vụ lẻ & nhóm dịch vụ
    Route::group(['prefix' => 'dich-vu'], function () {
        Route::get('/dich-vu-le', [AdminDichVu::class, 'dichVuLe'])->name('dich-vu.dich-vu-le');
        Route::post('/dich-vu-le', [AdminDichVu::class, 'store'])->name('dich-vu.store');
        Route::put('/dich-vu-le/{dichVu}', [AdminDichVu::class, 'update'])->name('dich-vu.update');
        Route::patch('/dich-vu-le/{dichVu}/trang-thai', [AdminDichVu::class, 'updateDichVuLeTrangThai'])->name('dich-vu.update-trang-thai');
        Route::delete('/dich-vu-le/{dichVu}', [AdminDichVu::class, 'destroy'])->name('dich-vu.destroy');
        Route::get('/dich-vu-le-theo-loai', [AdminDichVu::class, 'listDichVuLeTheoLoai'])->name('dich-vu.list-dich-vu-le-theo-loai');
        Route::get('/nhom-dich-vu', [AdminDichVu::class, 'nhomDichVu'])->name('dich-vu.nhom-dich-vu');
        Route::post('/nhom-dich-vu', [AdminDichVu::class, 'storeNhomDichVu'])->name('dich-vu.store-nhom-dich-vu');
        Route::put('/nhom-dich-vu/{nhomDichVu}', [AdminDichVu::class, 'updateNhomDichVu'])->name('dich-vu.update-nhom-dich-vu');
        Route::patch('/nhom-dich-vu/{nhomDichVu}/trang-thai', [AdminDichVu::class, 'updateNhomDichVuTrangThai'])->name('dich-vu.update-nhom-dich-vu-trang-thai');
        Route::delete('/nhom-dich-vu/{nhomDichVu}', [AdminDichVu::class, 'destroyNhomDichVu'])->name('dich-vu.destroy-nhom-dich-vu');
    });

    // Tài chính kế toán
    Route::group(['prefix' => 'tai-chinh'], function () {
        Route::get('/cong-no', [AdminTaiChinhKeToan::class, 'congNo'])->name('tai-chinh.cong-no');
        Route::post('/cong-no', [AdminTaiChinhKeToan::class, 'storeCongNo'])->name('tai-chinh.store-cong-no');
        Route::put('/cong-no/{congNo}', [AdminTaiChinhKeToan::class, 'updateCongNo'])->name('tai-chinh.update-cong-no');
        Route::delete('/cong-no/{congNo}', [AdminTaiChinhKeToan::class, 'destroyCongNo'])->name('tai-chinh.destroy-cong-no');
        Route::get('/phieu-thu-chi', [AdminTaiChinhKeToan::class, 'phieuThuChi'])->name('tai-chinh.phieu-thu-chi');
        Route::post('/phieu-thu-chi', [AdminTaiChinhKeToan::class, 'storePhieuThuChi'])->name('tai-chinh.store-phieu-thu-chi');
        Route::put('/phieu-thu-chi/{phieuThuChi}', [AdminTaiChinhKeToan::class, 'updatePhieuThuChi'])->name('tai-chinh.update-phieu-thu-chi');
        Route::put('/phieu-thu-chi/{phieuThuChi}/duyet', [AdminTaiChinhKeToan::class, 'duyetPhieuThuChi'])->name('tai-chinh.duyet-phieu-thu-chi');
        Route::put('/phieu-thu-chi/{phieuThuChi}/huy', [AdminTaiChinhKeToan::class, 'huyPhieuThuChi'])->name('tai-chinh.huy-phieu-thu-chi');
        Route::delete('/phieu-thu-chi/{phieuThuChi}', [AdminTaiChinhKeToan::class, 'destroyPhieuThuChi'])->name('tai-chinh.destroy-phieu-thu-chi');
        Route::get('/tinh-luong', [AdminTaiChinhKeToan::class, 'tinhLuong'])->name('tai-chinh.tinh-luong');
        Route::post('/tinh-luong', [AdminTaiChinhKeToan::class, 'storeTinhLuong'])->name('tai-chinh.store-tinh-luong');
        Route::put('/tinh-luong/{tinhLuong}', [AdminTaiChinhKeToan::class, 'updateTinhLuong'])->name('tai-chinh.update-tinh-luong');
        Route::delete('/tinh-luong/{tinhLuong}', [AdminTaiChinhKeToan::class, 'destroyTinhLuong'])->name('tai-chinh.destroy-tinh-luong');
    });

    // Note khách mới
    Route::get('/note-khach-moi', [AdminMarketing::class, 'noteKhachMoi'])->name('note-khach-moi');
    Route::get('/note-khach-moi/tim-hop-dong-theo-sdt', [AdminMarketing::class, 'timHopDongTheoSdt'])->name('note-khach-moi.tim-hop-dong-theo-sdt');
    Route::post('/note-khach-moi', [AdminMarketing::class, 'storeNoteKhachMoi'])->name('note-khach-moi.store');
    Route::put('/note-khach-moi/{noteKhachMoi}', [AdminMarketing::class, 'updateNoteKhachMoi'])->name('note-khach-moi.update');
    Route::delete('/note-khach-moi/{noteKhachMoi}', [AdminMarketing::class, 'destroyNoteKhachMoi'])->name('note-khach-moi.destroy');

    // Báo cáo
    Route::group(['prefix' => 'bao-cao'], function () {
        Route::get('/ads', [AdminBaoCao::class, 'baoCaoAds'])->name('bao-cao.ads');
        Route::post('/ads', [AdminBaoCao::class, 'storeBaoCaoAds'])->name('bao-cao.ads.store');
        Route::put('/ads/{baoCaoAd}', [AdminBaoCao::class, 'updateBaoCaoAds'])->name('bao-cao.ads.update');
        Route::delete('/ads/{baoCaoAd}', [AdminBaoCao::class, 'destroyBaoCaoAds'])->name('bao-cao.ads.destroy');
    });
    Route::get('/bao-cao-ads', fn () => redirect()->route('admin.bao-cao.ads'))->name('bao-cao-ads');

    // Thông tin cá nhân
    Route::get('/thong-tin-ca-nhan', [Admin::class, 'thongTinCaNhan'])->name('thong-tin-ca-nhan');
    Route::put('/thong-tin-ca-nhan', [Admin::class, 'capNhatThongTinCaNhan'])->name('thong-tin-ca-nhan.update');
    Route::put('/doi-mat-khau', [Admin::class, 'doiMatKhau'])->name('doi-mat-khau');

    // Hệ thống
    Route::group(['prefix' => 'he-thong'], function () {
        // Ẩn menu Ngân hàng thanh toán (xem config/admin_menu.php)
        // Route::get('/ngan-hang-thanh-toan', [AdminHeThong::class, 'nganHangThanhToan'])->name('he-thong.ngan-hang-thanh-toan');
        // Route::post('/ngan-hang-thanh-toan', [AdminHeThong::class, 'storeNganHangThanhToan'])->name('he-thong.ngan-hang-thanh-toan.store');
        // Route::put('/ngan-hang-thanh-toan/{nganHangThanhToan}', [AdminHeThong::class, 'updateNganHangThanhToan'])->name('he-thong.ngan-hang-thanh-toan.update');
        // Route::delete('/ngan-hang-thanh-toan/{nganHangThanhToan}', [AdminHeThong::class, 'destroyNganHangThanhToan'])->name('he-thong.ngan-hang-thanh-toan.destroy');
        Route::get('/vai-tro', [AdminHeThong::class, 'vaiTro'])->name('he-thong.vai-tro');
        Route::post('/vai-tro', [AdminHeThong::class, 'storeVaiTro'])->name('he-thong.vai-tro.store');
        Route::put('/vai-tro/{vaiTro}', [AdminHeThong::class, 'updateVaiTro'])->name('he-thong.vai-tro.update');
        Route::patch('/vai-tro/{vaiTro}/dieu-chinh-hop-dong-cuoi', [AdminHeThong::class, 'updateVaiTroDieuChinhHopDongCuoi'])->name('he-thong.vai-tro.update-dieu-chinh-hop-dong-cuoi');
        Route::delete('/vai-tro/{vaiTro}', [AdminHeThong::class, 'destroyVaiTro'])->name('he-thong.vai-tro.destroy');
        Route::get('/vai-tro/{vaiTro}/nguoi-dung', [AdminHeThong::class, 'nguoiDungVaiTro'])->name('he-thong.vai-tro.nguoi-dung');
        Route::get('/phong-ban', [AdminHeThong::class, 'phongBan'])->name('he-thong.phong-ban');
        Route::post('/phong-ban', [AdminHeThong::class, 'storePhongBan'])->name('he-thong.phong-ban.store');
        Route::put('/phong-ban/{phongBan}', [AdminHeThong::class, 'updatePhongBan'])->name('he-thong.phong-ban.update');
        Route::delete('/phong-ban/{phongBan}', [AdminHeThong::class, 'destroyPhongBan'])->name('he-thong.phong-ban.destroy');
        Route::get('/phong-ban/{phongBan}/nhan-vien', [AdminHeThong::class, 'nhanVienPhongBan'])->name('he-thong.phong-ban.nhan-vien');
        Route::get('/tai-lieu', [AdminHeThong::class, 'taiLieu'])->name('he-thong.tai-lieu');
        Route::post('/tai-lieu', [AdminHeThong::class, 'storeTaiLieu'])->name('he-thong.tai-lieu.store');
        Route::delete('/tai-lieu/{taiLieu}', [AdminHeThong::class, 'destroyTaiLieu'])->name('he-thong.tai-lieu.destroy');
        Route::get('/logs', [AdminHeThong::class, 'logs'])->name('he-thong.logs');
        Route::delete('/logs', [AdminHeThong::class, 'destroyLogs'])->name('he-thong.logs.destroy');
    });
});

Route::fallback(function () {
    return view('errors.404');
});