@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\TrangPhuc::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $tuKhoaHienThi = request('tu_khoa', request('search'));
    $locHinhAnh = request('loc_hinh_anh', '');
    if (! array_key_exists($locHinhAnh, \App\Models\TrangPhuc::LOC_HINH_ANH_OPTIONS)) {
        $locHinhAnh = '';
    }
    $hasFilter = request()->filled('tu_khoa')
        || request()->filled('search')
        || $locHinhAnh !== ''
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc'
        || (int) request('per_page', \App\Models\TrangPhuc::PHAN_TRANG_MAC_DINH) !== \App\Models\TrangPhuc::PHAN_TRANG_MAC_DINH;
@endphp
<div class="d-flex flex-column gap-3">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
    @endif

    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form action="{{ route('admin.trang-phuc.san-pham') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_khoa">
                        Từ khóa
                    </label>
                    <input type="text"
                           class="form-control @error('tu_khoa') is-invalid @enderror"
                           id="tu_khoa"
                           name="tu_khoa"
                           value="{{ old('tu_khoa', $tuKhoaHienThi) }}"
                           placeholder="Tên, mã, ngày nhập, ghi chú...">
                    @error('tu_khoa')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="loc_hinh_anh">Hình ảnh</label>
                    <select class="select2-admin form-select" id="loc_hinh_anh" name="loc_hinh_anh">
                        @foreach(\App\Models\TrangPhuc::LOC_HINH_ANH_OPTIONS as $value => $label)
                            <option value="{{ $value }}" @selected($locHinhAnh === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\TrangPhuc::SAP_XEP_OPTIONS as $value => $label)
                            <option value="{{ $value }}" @selected($sapXepTheo === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="thu_tu">Thứ tự</label>
                    <select class="select2-admin form-select" id="thu_tu" name="thu_tu">
                        <option value="asc" @selected($thuTu === 'asc')>Tăng dần</option>
                        <option value="desc" @selected($thuTu === 'desc')>Giảm dần</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.trang-phuc.san-pham') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách sản phẩm trang phục</span>
            <span class="d-flex flex-wrap align-items-center gap-2"
                  id="sp-view-toolbar"
                  role="toolbar"
                  aria-label="Chế độ xem danh sách">
                <span data-bs-toggle="tooltip" title="Thêm sản phẩm trang phục mới">
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalThemSanPham">
                        <i class="fa-solid fa-plus me-1"></i> Thêm mới
                    </button>
                </span>
                <span data-bs-toggle="tooltip" title="Import danh sách sản phẩm từ JSON">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm text-nowrap"
                            data-bs-toggle="modal"
                            data-bs-target="#modalNhapJsonSanPham">
                        <i class="fa-solid fa-file-import me-1"></i> Nhập JSON
                    </button>
                </span>
                <div class="btn-group btn-group-sm" role="group" aria-label="Bảng hoặc lưới">
                    <button type="button"
                            class="btn btn-outline-secondary sp-view-btn"
                            id="sp-view-btn-table"
                            data-sp-view="table"
                            title="Xem dạng bảng"
                            aria-pressed="false">
                        <i class="bi bi-table" aria-hidden="true"></i>
                    </button>
                    <button type="button"
                            class="btn btn-primary sp-view-btn active"
                            id="sp-view-btn-grid"
                            data-sp-view="grid"
                            title="Xem dạng lưới (card)"
                            aria-pressed="true">
                        <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                    </button>
                </div>
            </span>
        </h5>
        <div class="card-body">
        <div id="sp-view-table-wrap" class="table-responsive text-nowrap table-wrapper-bordered d-none">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        <th style="width: 64px;">Ảnh</th>
                        <th style="min-width: 110px;">Mã SP</th>
                        <th style="min-width: 180px;">Tên sản phẩm</th>
                        <th style="min-width: 120px;">Ngày nhập</th>
                        <th class="text-end" style="min-width: 110px;">Giá trị</th>
                        <th class="text-center" style="min-width: 100px;">Hiển thị</th>
                        <th style="min-width: 140px;">Ghi chú</th>
                        <th class="text-center" style="min-width: 110px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $isVisible = (int)($item->trang_thai ?? 0) === 1;
                        $hasHinh = !empty($item->hinh_anh);
                        $giaTriTxt = $item->gia_tri !== null ? number_format((float)$item->gia_tri, 0, ',', '.') . ' đ' : '—';
                        $maHienThi = filled($item->ma_san_pham) ? $item->ma_san_pham : '—';
                        $ngayNhapHienThi = filled($item->ngay_nhap) ? $item->ngay_nhap : '—';
                        $ghiChuRutGon = filled($item->ghi_chu) ? \Illuminate\Support\Str::limit($item->ghi_chu, 40) : '—';
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td>
                            @if($hasHinh)
                                <img src="{{ asset('storage/' . $item->hinh_anh) }}"
                                     alt="{{ $item->ten_san_pham ?? 'Trang phục' }}"
                                     class="san-pham-table-thumb"
                                     loading="lazy">
                            @else
                                <div class="san-pham-table-thumb san-pham-table-thumb--placeholder d-flex align-items-center justify-content-center text-white-50" aria-label="Chưa có ảnh">
                                    <i class="fa-solid fa-shirt" aria-hidden="true"></i>
                                </div>
                            @endif
                        </td>
                        <td><span class="fw-medium">{{ $maHienThi }}</span></td>
                        <td class="text-wrap"><span class="fw-medium">{{ $item->ten_san_pham ?? '—' }}</span></td>
                        <td class="text-nowrap">{{ $ngayNhapHienThi }}</td>
                        <td class="text-end text-nowrap">{{ $giaTriTxt }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center mb-0">
                                <input type="checkbox"
                                       class="form-check-input switch-trang-thai-san-pham"
                                       role="switch"
                                       id="switch-trang-thai-san-pham-table-{{ $item->id }}"
                                       data-url="{{ route('admin.trang-phuc.san-pham.update-trang-thai', $item) }}"
                                       @checked($isVisible)
                                       title="{{ $isVisible ? 'Hiển thị' : 'Ẩn' }}">
                            </div>
                        </td>
                        <td class="text-wrap small">{{ $ghiChuRutGon }}</td>
                        <td class="text-center text-nowrap">
                            <div class="san-pham-table-actions d-inline-flex align-items-center justify-content-center gap-2" role="toolbar" aria-label="Thao tác sản phẩm">
                                <span data-bs-toggle="tooltip" title="Kiểm tra">
                                    <button type="button"
                                            class="san-pham-action-icon btn-kiem-tra-san-pham"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalKiemTraSanPham"
                                            data-id="{{ (int) $item->id }}"
                                            data-ten="{{ e($item->ten_san_pham ?? '') }}"
                                            data-ma="{{ e($item->ma_san_pham ?? '') }}"
                                            data-url="{{ route('admin.trang-phuc.san-pham.kiem-tra', $item) }}">
                                        <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                                        <span class="visually-hidden">Kiểm tra</span>
                                    </button>
                                </span>
                                <span data-bs-toggle="tooltip" title="Sửa">
                                    <button type="button"
                                            class="san-pham-action-icon btn-sua-san-pham"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSuaSanPham"
                                            data-url="{{ route('admin.trang-phuc.san-pham.update', $item) }}"
                                            data-ten="{{ e($item->ten_san_pham ?? '') }}"
                                            data-ma="{{ e($item->ma_san_pham ?? '') }}"
                                            data-ngay-nhap="{{ e($item->ngay_nhap ?? '') }}"
                                            data-hinh-anh="{{ !empty($item->hinh_anh) ? asset('storage/' . $item->hinh_anh) : '' }}"
                                            data-hinh-anh-duong-dan="{{ e($item->hinh_anh ?? '') }}"
                                            data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                            data-gia-tri="{{ $item->gia_tri ?? '' }}">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        <span class="visually-hidden">Sửa</span>
                                    </button>
                                </span>
                                <span data-bs-toggle="tooltip" title="Xoá">
                                    <button type="button"
                                            class="san-pham-action-icon san-pham-action-icon--danger btn-xoa-san-pham"
                                            data-form-id="form-xoa-sp-table-{{ $item->id }}">
                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        <span class="visually-hidden">Xoá</span>
                                    </button>
                                </span>
                            </div>
                            <form id="form-xoa-sp-table-{{ $item->id }}" action="{{ route('admin.trang-phuc.san-pham.destroy', $item) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Chưa có dữ liệu sản phẩm trang phục.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="sp-view-grid-wrap" class="san-pham-card-grid">
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2 g-sm-3">
                @forelse($danhSach ?? [] as $index => $item)
                @php
                    $isVisible = (int)($item->trang_thai ?? 0) === 1;
                    $hasHinh = !empty($item->hinh_anh);
                    $giaTriTxt = $item->gia_tri !== null ? number_format((float)$item->gia_tri, 0, ',', '.') . ' đ' : '—';
                    $maHienThi = filled($item->ma_san_pham) ? $item->ma_san_pham : '—';
                    $ngayNhapHienThi = filled($item->ngay_nhap) ? $item->ngay_nhap : '—';
                @endphp
                <div class="col">
                    <div class="card h-100 san-pham-card border shadow-sm">
                        <div class="position-relative san-pham-card__media-wrap">
                            <div class="san-pham-card__top-start position-absolute top-0 start-0 m-1 d-flex align-items-center gap-1 flex-wrap">
                                <div class="form-check form-switch san-pham-card__status-switch mb-0"
                                     title="{{ $isVisible ? 'Đang hiển thị' : 'Đang ẩn' }}">
                                    <input type="checkbox"
                                           class="form-check-input switch-trang-thai-san-pham"
                                           role="switch"
                                           id="switch-trang-thai-san-pham-grid-{{ $item->id }}"
                                           data-url="{{ route('admin.trang-phuc.san-pham.update-trang-thai', $item) }}"
                                           @checked($isVisible)
                                           aria-label="Thay đổi trạng thái hiển thị">
                                </div>
                                <span class="badge rounded-2 border-0 san-pham-card__ma-tag {{ $isVisible ? 'bg-success' : 'bg-danger' }} text-white"
                                      title="{{ $isVisible ? 'Đang hiển thị' : 'Đang ẩn' }}">{{ $maHienThi }}</span>
                            </div>
                            @if($hasHinh)
                                <img src="{{ asset('storage/' . $item->hinh_anh) }}"
                                     alt="{{ $item->ten_san_pham ?? 'Trang phục' }}"
                                     class="san-pham-card__img"
                                     loading="lazy">
                            @else
                                <div class="san-pham-card__placeholder d-flex flex-column align-items-center justify-content-center text-white-50" role="img" aria-label="Chưa có ảnh">
                                    <i class="fa-solid fa-shirt san-pham-card__placeholder-icon mb-1 opacity-75"></i>
                                    <span class="san-pham-card__placeholder-label opacity-75 px-2 text-center">Chưa có ảnh</span>
                                </div>
                            @endif
                            <div class="san-pham-card__actions" role="toolbar" aria-label="Thao tác sản phẩm">
                                <span data-bs-toggle="tooltip" title="Kiểm tra">
                                    <button type="button"
                                            class="san-pham-action-icon btn-kiem-tra-san-pham"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalKiemTraSanPham"
                                            data-id="{{ (int) $item->id }}"
                                            data-ten="{{ e($item->ten_san_pham ?? '') }}"
                                            data-ma="{{ e($item->ma_san_pham ?? '') }}"
                                            data-url="{{ route('admin.trang-phuc.san-pham.kiem-tra', $item) }}">
                                        <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                                        <span class="visually-hidden">Kiểm tra</span>
                                    </button>
                                </span>
                                <span data-bs-toggle="tooltip" title="Sửa">
                                    <button type="button"
                                            class="san-pham-action-icon btn-sua-san-pham"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSuaSanPham"
                                            data-url="{{ route('admin.trang-phuc.san-pham.update', $item) }}"
                                            data-ten="{{ e($item->ten_san_pham ?? '') }}"
                                            data-ma="{{ e($item->ma_san_pham ?? '') }}"
                                            data-ngay-nhap="{{ e($item->ngay_nhap ?? '') }}"
                                            data-hinh-anh="{{ !empty($item->hinh_anh) ? asset('storage/' . $item->hinh_anh) : '' }}"
                                            data-hinh-anh-duong-dan="{{ e($item->hinh_anh ?? '') }}"
                                            data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                            data-gia-tri="{{ $item->gia_tri ?? '' }}">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        <span class="visually-hidden">Sửa</span>
                                    </button>
                                </span>
                                <span data-bs-toggle="tooltip" title="Xoá">
                                    <button type="button"
                                            class="san-pham-action-icon san-pham-action-icon--danger btn-xoa-san-pham"
                                            data-form-id="form-xoa-sp-{{ $item->id }}">
                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        <span class="visually-hidden">Xoá</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column san-pham-card__body">
                            <form id="form-xoa-sp-{{ $item->id }}" action="{{ route('admin.trang-phuc.san-pham.destroy', $item) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <p class="san-pham-card__title fw-medium mb-1" title="{{ $item->ten_san_pham ?? '' }}">{{ $item->ten_san_pham ?? '—' }}</p>
                            <div class="san-pham-card__info-row d-flex align-items-center justify-content-between gap-2 border-top border-light pt-1 mt-auto w-100">
                                <span class="visually-hidden">Giá trị:</span>
                                <span class="san-pham-card__gia-tri text-body fw-medium text-start">{{ $giaTriTxt }}</span>
                                <span class="san-pham-card__ngay-nhap text-muted text-end"
                                      title="Ngày nhập">{{ $ngayNhapHienThi }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 mx-auto">
                    <div class="text-center py-5 px-3 rounded border bg-label-secondary bg-opacity-10 text-muted">
                        Chưa có dữ liệu sản phẩm trang phục.
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        <x-pagination-info
            :paginator="$danhSach ?? null"
            label="sản phẩm"
            :per-page-options="\App\Models\TrangPhuc::PHAN_TRANG_OPTIONS"
        />
        </div>
    </div>
</div>

{{-- Modal Nhập JSON sản phẩm --}}
<div class="modal fade" id="modalNhapJsonSanPham" tabindex="-1" aria-labelledby="modalNhapJsonSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNhapJsonSanPhamLabel">Nhập sản phẩm từ JSON</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                {{-- <p class="text-muted small mb-2">
                    Dán mảng JSON gồm các object với các trường:
                    <code>ma_san_pham</code>, <code>ten_san_pham</code>, <code>gia_tri</code>,
                    <code>hinh_anh</code>, <code>ghi_chu</code>, <code>ngay_nhap</code>, <code>trang_thai</code>.
                </p> --}}
                <label class="form-label" for="spImportJson">Dữ liệu JSON</label>
                <textarea id="spImportJson"
                          class="form-control sp-import-json-input font-monospace"
                          rows="12"
                          placeholder='[
  {
    "ma_san_pham": "C07",
    "ten_san_pham": "Váy chụp C07",
    "gia_tri": 1500000,
    "hinh_anh": "trang-phuc/san-pham/C07.jpeg",
    "ghi_chu": "",
    "ngay_nhap": "",
    "trang_thai": 1
  }
]'></textarea>
                <div id="spImportJsonError" class="alert alert-danger mt-3 mb-0 d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnSpSubmitImportJson">
                    <i class="fa-solid fa-file-import me-1"></i> Nhập liệu
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kết quả import JSON --}}
<div class="modal fade" id="modalKetQuaImportSanPham" tabindex="-1" aria-labelledby="modalKetQuaImportSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-ket-qua-import">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKetQuaImportSanPhamLabel">Kết quả import sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body modal-ket-qua-import__body">
                <div id="spImportSummary" class="alert alert-secondary mb-3" role="status"></div>

                <div class="row g-4">
                    <div class="col-12 col-xl-6">
                        <div class="h-100">
                            <h6 class="text-success mb-2">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Import thành công (<span id="spImportSuccessCount">0</span>)
                            </h6>
                            <div class="table-responsive modal-ket-qua-import__table-wrap" id="spImportSuccessWrap">
                                <table class="table table-sm table-bordered mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 48px;">#</th>
                                            <th style="min-width: 100px;">Mã SP</th>
                                            <th>Tên sản phẩm</th>
                                            <th class="text-center" style="width: 72px;">ID</th>
                                        </tr>
                                    </thead>
                                    <tbody id="spImportSuccessBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="h-100">
                            <h6 class="text-danger mb-2">
                                <i class="fa-solid fa-circle-xmark me-1"></i>
                                Import thất bại (<span id="spImportFailedCount">0</span>)
                            </h6>
                            <div class="table-responsive modal-ket-qua-import__table-wrap" id="spImportFailedWrap">
                                <table class="table table-sm table-bordered mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 48px;">#</th>
                                            <th style="min-width: 100px;">Mã SP</th>
                                            <th style="min-width: 140px;">Tên sản phẩm</th>
                                            <th style="min-width: 200px;">Lỗi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="spImportFailedBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary d-none" id="btnSpImportReload">
                    <i class="fa-solid fa-rotate-right me-1"></i> Tải lại trang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kiểm tra sử dụng sản phẩm --}}
<div class="modal fade" id="modalKiemTraSanPham" tabindex="-1" aria-labelledby="modalKiemTraSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 92vw; width: 820px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKiemTraSanPhamLabel">Kiểm tra sử dụng sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div>
                        <div class="fw-semibold" id="ktspTen">—</div>
                        <div class="text-muted small" id="ktspMa">—</div>
                    </div>
                    <div class="text-muted small" id="ktspStatus"></div>
                </div>

                <div id="ktspLoading" class="py-4 text-center text-muted d-none">
                    <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                    Đang tải dữ liệu...
                </div>

                <div id="ktspError" class="alert alert-danger d-none" role="alert"></div>

                <div id="ktspContent" class="d-none">
                    <div class="fw-semibold mb-1">Lịch sử sử dụng theo ngày</div>
                    <div class="text-muted small mb-2">Mỗi ngày liệt kê các đơn đang sử dụng sản phẩm.</div>
                    <div id="ktspGroupedEmpty" class="text-muted small d-none">Chưa có dữ liệu sử dụng.</div>
                    <div id="ktspGroupedWrap" class="accordion accordion-flush"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Thêm mới sản phẩm --}}
<div class="modal fade" id="modalThemSanPham" tabindex="-1" aria-labelledby="modalThemSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-san-pham">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemSanPhamLabel">Thêm sản phẩm trang phục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.trang-phuc.san-pham.store') }}" method="POST" enctype="multipart/form-data" id="formThemSanPham">
                @csrf
                @if($errors->any())
                <div class="modal-body py-0">
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Cột trái: Hình ảnh --}}
                        <div class="col-12 col-lg-4">
                            <div class="sticky-lg-top">
                                <label class="form-label" for="them_sp_hinh_anh">Hình ảnh</label>
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden mb-2" style="min-height: 210px;">
                                    <div id="them_sp_hinh_anh_placeholder" class="text-center text-muted py-4 px-3">
                                        <span class="small">Vui lòng chọn ảnh sản phẩm</span>
                                    </div>
                                    <img id="them_sp_hinh_anh_preview" src="" alt="Preview" class="rounded w-100 d-none" style="max-height: 240px; object-fit: contain;">
                                </div>
                                <input type="file" class="form-control" id="them_sp_hinh_anh" name="hinh_anh" accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-muted d-block mt-1">JPEG, PNG, GIF, WebP — tối đa 5MB</small>
                                <div id="them_sp_hinh_anh_error" class="alert alert-danger mt-2 d-none" role="alert"></div>
                            </div>
                        </div>

                        {{-- Cột phải: Thông tin --}}
                        <div class="col-12 col-lg-8">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="them_ten_san_pham">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="them_ten_san_pham" name="ten_san_pham" value="{{ old('ten_san_pham') }}" placeholder="Nhập tên sản phẩm" required>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="them_ma_san_pham">Mã sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="them_ma_san_pham" name="ma_san_pham" value="{{ old('ma_san_pham') }}" placeholder="Ví dụ: TP001" required>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="them_ngay_nhap">Ngày nhập</label>
                                    <input type="text" class="form-control" id="them_ngay_nhap" name="ngay_nhap" value="{{ old('ngay_nhap') }}" placeholder="Ví dụ: 18/06/2026">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="them_gia_tri">Giá trị</label>
                                    <input type="number" class="form-control" id="them_gia_tri" name="gia_tri" value="{{ old('gia_tri') }}" placeholder="0" min="0" step="0.01">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                                    <textarea class="form-control" id="them_ghi_chu" name="ghi_chu" rows="3" maxlength="500" placeholder="Ghi chú...">{{ old('ghi_chu') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Chỉnh sửa sản phẩm --}}
<div class="modal fade" id="modalSuaSanPham" tabindex="-1" aria-labelledby="modalSuaSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-san-pham">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaSanPhamLabel">Chỉnh sửa sản phẩm trang phục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaSanPham" method="POST" enctype="multipart/form-data" action="">
                @csrf
                @method('PUT')
                @if($errors->any())
                <div class="modal-body py-0">
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Cột trái: Hình ảnh --}}
                        <div class="col-12 col-lg-4">
                            <div class="sticky-lg-top">
                                <label class="form-label" for="sua_hinh_anh_duong_dan">Địa chỉ hình ảnh</label>
                                <input type="text"
                                       class="form-control font-monospace mb-2"
                                       id="sua_hinh_anh_duong_dan"
                                       name="hinh_anh_duong_dan"
                                       placeholder="trang-phuc/san-pham/C07.jpeg"
                                       autocomplete="off">
                                {{-- <small class="text-muted d-block mb-3">Đường dẫn trong storage hoặc URL — ưu tiên thấp hơn khi chọn file tải lên.</small> --}}

                                <label class="form-label" for="sua_sp_hinh_anh">Hình ảnh</label>
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden mb-2" style="min-height: 210px;">
                                    <div id="sua_sp_hinh_anh_placeholder" class="text-center text-muted py-4 px-3">
                                        <span class="small">Ảnh hiện tại hoặc chọn ảnh mới</span>
                                    </div>
                                    <img id="sua_sp_hinh_anh_preview" src="" alt="Preview" class="rounded w-100 d-none" style="max-height: 240px; object-fit: contain;">
                                </div>
                                <input type="file" class="form-control" id="sua_sp_hinh_anh" name="hinh_anh" accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-muted d-block mt-1">JPEG, PNG, GIF, WebP — tối đa 5MB</small>
                                <div id="sua_sp_hinh_anh_error" class="alert alert-danger mt-2 d-none" role="alert"></div>
                            </div>
                        </div>

                        {{-- Cột phải: Thông tin --}}
                        <div class="col-12 col-lg-8">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="sua_ten_san_pham">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sua_ten_san_pham" name="ten_san_pham" placeholder="Nhập tên sản phẩm" required>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="sua_ma_san_pham">Mã sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sua_ma_san_pham" name="ma_san_pham" placeholder="Ví dụ: TP001" required>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="sua_ngay_nhap">Ngày nhập</label>
                                    <input type="text" class="form-control" id="sua_ngay_nhap" name="ngay_nhap" placeholder="Ví dụ: 18/06/2026">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="sua_gia_tri">Giá trị</label>
                                    <input type="number" class="form-control" id="sua_gia_tri" name="gia_tri" placeholder="0" min="0" step="0.01">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                                    <textarea class="form-control" id="sua_ghi_chu" name="ghi_chu" rows="2" maxlength="500" placeholder="Ghi chú..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa sản phẩm --}}
<div class="modal fade" id="modalXacNhanXoaSanPham" tabindex="-1" aria-labelledby="modalXacNhanXoaSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaSanPhamLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa sản phẩm trang phục này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaSanPham">
                    <i class="fa-solid fa-trash me-1"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<style>
.table-wrapper-bordered {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 960px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
    vertical-align: middle;
}
.san-pham-table-thumb {
    width: 72px;
    height: 50px;
    object-fit: fill;
    border-radius: 0.25rem;
    display: block;
}
.san-pham-table-thumb--placeholder {
    background: linear-gradient(145deg, #8592a3 0%, #566a7f 50%, #384551 100%);
    font-size: 1rem;
}
.san-pham-card-grid {
    margin-bottom: 0.5rem;
    margin-top: 0.5rem;
}
.san-pham-card {
    transition: box-shadow 0.2s ease, transform 0.2s ease;
    border-radius: 0.375rem;
}
.san-pham-card:hover {
    box-shadow: 0 0.35rem 1.25rem rgba(67, 89, 113, 0.12) !important;
    transform: translateY(-2px);
}
.san-pham-card__media-wrap {
    background: var(--bs-gray-100, #f5f5f9);
    aspect-ratio: 3 / 4;
    border-radius: 0.375rem 0.375rem 0 0;
    overflow: hidden;
}
.san-pham-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.san-pham-card__placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(145deg, #8592a3 0%, #566a7f 50%, #384551 100%);
}
.san-pham-card__placeholder-icon {
    font-size: clamp(1.25rem, 4vw, 1.85rem);
}
.san-pham-card__placeholder-label {
    font-size: 0.62rem;
    line-height: 1.2;
}
.san-pham-card__ma-tag {
    font-size: 0.65rem;
    font-weight: 200;
    z-index: 2;
    padding: 0.35rem 0.45rem;
    letter-spacing: 0.02em;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 8rem;
}
.san-pham-card__top-start {
    z-index: 3;
    max-width: calc(100% - 0.5rem);
}
.san-pham-card__status-switch {
    display: flex;
    align-items: center;
    min-height: auto;
    padding-left: 0;
    flex-shrink: 0;
}
.san-pham-card__status-switch .form-check-input {
    width: 1.85rem;
    height: 1.05rem;
    margin: 0;
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.35);
    border-color: rgba(255, 255, 255, 0.65);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255,255,255,0.9%29'/%3e%3c/svg%3e");
}
.san-pham-card__status-switch .form-check-input:checked {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}
.san-pham-card__body {
    padding: 0.2rem 0.2rem 0.2rem;
}
.san-pham-card__actions {
    position: absolute;
    right: 4px;
    bottom: 4px;
    z-index: 3;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.4rem 0.5rem;
}
.san-pham-table-actions {
    gap: 0.5rem;
}
.san-pham-action-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    background: transparent;
    color: var(--bs-primary);
    font-size: 1.15rem;
    padding: 0.15rem;
    line-height: 1;
    cursor: pointer;
    transition: color 0.15s ease, transform 0.15s ease, filter 0.15s ease;
}
.san-pham-card__actions .san-pham-action-icon {
    filter: drop-shadow(0 0 1px rgba(255, 255, 255, 0.95)) drop-shadow(0 1px 2px rgba(0, 0, 0, 0.35));
}
.san-pham-action-icon i {
    font-weight: 900;
}
.san-pham-action-icon:hover {
    color: color-mix(in srgb, var(--bs-primary) 82%, #000);
    transform: scale(1.12);
}
.san-pham-card__actions .san-pham-action-icon:hover {
    filter: drop-shadow(0 0 2px rgba(255, 255, 255, 1)) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
}
.san-pham-action-icon--danger {
    color: var(--bs-danger);
}
.san-pham-action-icon--danger:hover {
    color: color-mix(in srgb, var(--bs-danger) 82%, #000);
}
.san-pham-card__actions [data-bs-toggle="tooltip"],
.san-pham-table-actions [data-bs-toggle="tooltip"] {
    display: inline-flex;
    line-height: 0;
}
.san-pham-card__title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
    font-size: 0.78rem;
}
.san-pham-card__info-row {
    font-size: 14px;
    line-height: 1.25;
    border-color: rgba(var(--bs-border-color-rgb, 231, 233, 245), 0.9) !important;
}
.san-pham-card__gia-tri {
    flex: 0 1 auto;
    min-width: 0;
    white-space: nowrap;
}
.san-pham-card__ngay-nhap {
    font-size: 12px;
    font-style: italic;
    line-height: 1.35;
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding: 4px
}
.san-pham-card__info-icon {
    flex-shrink: 0;
    width: 1rem;
    text-align: center;
    font-size: 0.7rem;
    line-height: 1;
}
@media (min-width: 1200px) {
    .san-pham-card__title {
        font-size: 14px;
    }
    .san-pham-card__info-row {
        font-size: 14px;
    }
    .san-pham-card__ngay-nhap {
        font-size: 12px;
    }
}
#modalThemSanPham .modal-san-pham,
#modalSuaSanPham .modal-san-pham {
    max-width: 96vw;
    width: 1080px;
}
#modalXacNhanXoaSanPham .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
.ktsp-day-list {
    padding: 0 12px !important;
}
.sp-import-json-input {
    font-size: 13px;
    resize: vertical;
    min-height: 220px;
}
#modalKetQuaImportSanPham .modal-ket-qua-import {
    max-width: 96vw;
    width: 1200px;
}
#modalKetQuaImportSanPham .modal-ket-qua-import__body {
    max-height: min(78vh, 820px);
    overflow-y: auto;
}
#modalKetQuaImportSanPham .modal-ket-qua-import__table-wrap {
    max-height: min(52vh, 560px);
    overflow: auto;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    (function () {
        var LS_KEY = 'adminSanPhamListView';
        var tableWrap = document.getElementById('sp-view-table-wrap');
        var gridWrap = document.getElementById('sp-view-grid-wrap');
        var btnTable = document.getElementById('sp-view-btn-table');
        var btnGrid = document.getElementById('sp-view-btn-grid');
        if (!tableWrap || !gridWrap || !btnTable || !btnGrid) {
            return;
        }

        function setView(mode) {
            var isGrid = mode === 'grid';
            tableWrap.classList.toggle('d-none', isGrid);
            gridWrap.classList.toggle('d-none', !isGrid);
            btnTable.classList.toggle('btn-primary', !isGrid);
            btnTable.classList.toggle('btn-outline-secondary', isGrid);
            btnTable.classList.toggle('active', !isGrid);
            btnGrid.classList.toggle('btn-primary', isGrid);
            btnGrid.classList.toggle('btn-outline-secondary', !isGrid);
            btnGrid.classList.toggle('active', isGrid);
            btnTable.setAttribute('aria-pressed', (!isGrid).toString());
            btnGrid.setAttribute('aria-pressed', isGrid.toString());
            try {
                localStorage.setItem(LS_KEY, mode);
            } catch (e) { /* ignore */ }
        }

        var saved = null;
        try {
            saved = localStorage.getItem(LS_KEY);
        } catch (e) { /* ignore */ }
        setView(saved === 'table' ? 'table' : 'grid');

        btnTable.addEventListener('click', function () {
            setView('table');
        });
        btnGrid.addEventListener('click', function () {
            setView('grid');
        });
    })();

    // Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    var CSRF_TOKEN = @json(csrf_token());
    var TRANG_THAI_HIEN_THI = {{ \App\Models\TrangPhuc::TRANG_THAI_ACTIVE }};
    var SP_IMPORT_JSON_URL = @json(route('admin.trang-phuc.san-pham.import-json'));

    (function initSpImportJson() {
        var textarea = document.getElementById('spImportJson');
        var btnSubmit = document.getElementById('btnSpSubmitImportJson');
        var modalInputEl = document.getElementById('modalNhapJsonSanPham');
        var modalEl = document.getElementById('modalKetQuaImportSanPham');
        var errorEl = document.getElementById('spImportJsonError');
        var summaryEl = document.getElementById('spImportSummary');
        var successCountEl = document.getElementById('spImportSuccessCount');
        var failedCountEl = document.getElementById('spImportFailedCount');
        var successBodyEl = document.getElementById('spImportSuccessBody');
        var failedBodyEl = document.getElementById('spImportFailedBody');
        var btnReload = document.getElementById('btnSpImportReload');

        if (!textarea || !btnSubmit || !modalInputEl || !modalEl) return;

        function hideImportError() {
            if (!errorEl) return;
            errorEl.textContent = '';
            errorEl.classList.add('d-none');
        }

        function showImportError(message) {
            if (!errorEl) {
                alert(message);
                return;
            }
            errorEl.textContent = message;
            errorEl.classList.remove('d-none');
        }

        function appendImportEmptyRow(tbodyEl, colSpan) {
            if (!tbodyEl) return;
            var tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="' + colSpan + '" class="text-center text-muted small py-3">Không có bản ghi nào.</td>';
            tbodyEl.appendChild(tr);
        }

        function resetResultModal() {
            if (summaryEl) summaryEl.textContent = '';
            if (successCountEl) successCountEl.textContent = '0';
            if (failedCountEl) failedCountEl.textContent = '0';
            if (successBodyEl) successBodyEl.innerHTML = '';
            if (failedBodyEl) failedBodyEl.innerHTML = '';
            if (btnReload) btnReload.classList.add('d-none');
        }

        function renderResult(data) {
            resetResultModal();

            var successItems = Array.isArray(data.import_thanh_cong) ? data.import_thanh_cong : [];
            var failedItems = Array.isArray(data.import_that_bai) ? data.import_that_bai : [];
            var tong = typeof data.tong === 'number' ? data.tong : (successItems.length + failedItems.length);

            if (summaryEl) {
                summaryEl.textContent = 'Đã xử lý ' + tong + ' bản ghi: ' + successItems.length + ' thành công, ' + failedItems.length + ' thất bại.';
                summaryEl.classList.remove('alert-danger', 'alert-success', 'alert-secondary');
                if (failedItems.length === 0 && successItems.length > 0) {
                    summaryEl.classList.add('alert-success');
                } else if (successItems.length === 0 && failedItems.length > 0) {
                    summaryEl.classList.add('alert-danger');
                } else {
                    summaryEl.classList.add('alert-secondary');
                }
            }

            if (successCountEl) successCountEl.textContent = String(successItems.length);
            if (failedCountEl) failedCountEl.textContent = String(failedItems.length);

            successItems.forEach(function (row, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td class="text-center">' + escHtml((row.index ?? idx) + 1) + '</td>' +
                    '<td>' + escHtml(row.ma_san_pham || '—') + '</td>' +
                    '<td>' + escHtml(row.ten_san_pham || '—') + '</td>' +
                    '<td class="text-center">' + escHtml(row.id ?? '—') + '</td>';
                if (successBodyEl) successBodyEl.appendChild(tr);
            });
            if (!successItems.length) {
                appendImportEmptyRow(successBodyEl, 4);
            }

            failedItems.forEach(function (row, idx) {
                var dataRow = row.data || {};
                var errors = Array.isArray(row.errors) ? row.errors : ['Lỗi không xác định.'];
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td class="text-center">' + escHtml((row.index ?? idx) + 1) + '</td>' +
                    '<td>' + escHtml(dataRow.ma_san_pham || '—') + '</td>' +
                    '<td>' + escHtml(dataRow.ten_san_pham || '—') + '</td>' +
                    '<td class="text-danger small">' + escHtml(errors.join('; ')) + '</td>';
                if (failedBodyEl) failedBodyEl.appendChild(tr);
            });
            if (!failedItems.length) {
                appendImportEmptyRow(failedBodyEl, 4);
            }

            if (btnReload && successItems.length > 0) {
                btnReload.classList.remove('d-none');
            }

            function openResultModal() {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }

            var inputModal = bootstrap.Modal.getInstance(modalInputEl);
            if (inputModal && modalInputEl.classList.contains('show')) {
                modalInputEl.addEventListener('hidden.bs.modal', openResultModal, { once: true });
                inputModal.hide();
            } else {
                openResultModal();
            }
        }

        function parseImportJson(raw) {
            var text = (raw || '').trim();
            if (!text) {
                throw new Error('Vui lòng nhập mảng JSON.');
            }

            var parsed;
            try {
                parsed = JSON.parse(text);
            } catch (e) {
                throw new Error('JSON không hợp lệ. Vui lòng kiểm tra cú pháp.');
            }

            if (!Array.isArray(parsed)) {
                throw new Error('Dữ liệu phải là mảng JSON (array).');
            }

            if (!parsed.length) {
                throw new Error('Mảng import phải có ít nhất 1 phần tử.');
            }

            parsed.forEach(function (item, index) {
                if (!item || typeof item !== 'object' || Array.isArray(item)) {
                    throw new Error('Phần tử #' + (index + 1) + ' phải là object JSON.');
                }
            });

            return parsed;
        }

        btnSubmit.addEventListener('click', function () {
            hideImportError();

            var items;
            try {
                items = parseImportJson(textarea.value);
            } catch (err) {
                showImportError(err && err.message ? err.message : 'Dữ liệu JSON không hợp lệ.');
                return;
            }

            var originalHtml = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang import...';

            RestApi.post(SP_IMPORT_JSON_URL, { items: items }, { toast: false })
                .then(function (res) {
                    if (!res.ok) {
                        var msg = res.message || 'Import thất bại.';
                        if (res.errors) {
                            var errs = Object.values(res.errors).flat();
                            if (errs.length) msg = errs.join('; ');
                        }
                        throw new Error(msg);
                    }
                    return res.data;
                })
                .then(function (json) {
                    renderResult(json);
                })
                .catch(function (err) {
                    showImportError(err && err.message ? err.message : 'Không thể import dữ liệu.');
                })
                .finally(function () {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalHtml;
                });
        });

        if (btnReload) {
            btnReload.addEventListener('click', function () {
                window.location.reload();
            });
        }

        modalInputEl.addEventListener('show.bs.modal', function () {
            hideImportError();
        });

        [modalInputEl, modalEl].forEach(function (modalNode) {
            modalNode.addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            });
        });
    })();

    function capNhatMaTagTheoSwitch(switchEl) {
        var card = switchEl.closest('.san-pham-card');
        if (!card) return;
        var maTag = card.querySelector('.san-pham-card__ma-tag');
        if (!maTag) return;
        var isVisible = switchEl.checked;
        maTag.classList.toggle('bg-success', isVisible);
        maTag.classList.toggle('bg-danger', !isVisible);
        maTag.title = isVisible ? 'Đang hiển thị' : 'Đang ẩn';
        var switchWrap = switchEl.closest('.san-pham-card__status-switch');
        if (switchWrap) {
            switchWrap.title = isVisible ? 'Đang hiển thị' : 'Đang ẩn';
        }
    }

    function capNhatTrangThaiSanPham(switchEl) {
        if (!switchEl) return;

        var url = switchEl.getAttribute('data-url');
        if (!url) return;

        var trangThai = switchEl.checked ? TRANG_THAI_HIEN_THI : 0;
        var trangThaiCu = switchEl.checked ? 0 : TRANG_THAI_HIEN_THI;

        switchEl.disabled = true;

        RestApi.patch(url, { trang_thai: trangThai })
            .then(function(res) {
                if (!res.ok || !res.data || !res.data.success) throw new Error('update_failed');
                switchEl.title = switchEl.checked ? 'Hiển thị' : 'Ẩn';
                capNhatMaTagTheoSwitch(switchEl);
            })
            .catch(function() {
                switchEl.checked = trangThaiCu === TRANG_THAI_HIEN_THI;
            })
            .finally(function() {
                switchEl.disabled = false;
            });
    }

    document.querySelectorAll('.switch-trang-thai-san-pham').forEach(function(switchEl) {
        switchEl.addEventListener('change', function() {
            capNhatTrangThaiSanPham(this);
        });
    });

    var modalThem = document.getElementById('modalThemSanPham');

    // Mở modal Thêm khi có lỗi validation (sau redirect)
    @if($errors->any())
    if (modalThem) {
        var m = new bootstrap.Modal(modalThem);
        m.show();
    }
    @endif

    // --- Upload ảnh: Modal Thêm sản phẩm ---
    var MAX_SIZE = 5 * 1024 * 1024; // 5MB
    var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    var inputHinhAnh = document.getElementById('them_sp_hinh_anh');
    var placeholder = document.getElementById('them_sp_hinh_anh_placeholder');
    var previewImg = document.getElementById('them_sp_hinh_anh_preview');
    var errorDiv = document.getElementById('them_sp_hinh_anh_error');
    var currentObjectUrl = null;

    function clearPreview() {
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        if (previewImg) {
            previewImg.src = '';
            previewImg.classList.add('d-none');
        }
        if (placeholder) placeholder.classList.remove('d-none');
        if (errorDiv) {
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';
        }
        if (inputHinhAnh) inputHinhAnh.value = '';
    }

    function processFile(file) {
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        if (placeholder) placeholder.classList.remove('d-none');
        if (previewImg) previewImg.classList.add('d-none');
        if (errorDiv) {
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';
        }
        if (!file) return;

        if (!ALLOWED_TYPES.includes(file.type)) {
            if (errorDiv) {
                errorDiv.textContent = 'Vui lòng chọn file ảnh (JPEG, PNG, GIF hoặc WebP).';
                errorDiv.classList.remove('d-none');
            }
            return;
        }
        if (file.size > MAX_SIZE) {
            if (errorDiv) {
                errorDiv.textContent = 'Kích thước file không được vượt quá 5MB. File của bạn: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB.';
                errorDiv.classList.remove('d-none');
            }
            return;
        }

        if (placeholder) placeholder.classList.add('d-none');
        currentObjectUrl = URL.createObjectURL(file);
        if (previewImg) {
            previewImg.src = currentObjectUrl;
            previewImg.classList.remove('d-none');
        }
    }

    if (inputHinhAnh) {
        inputHinhAnh.addEventListener('change', function() {
            var file = this.files && this.files[0];
            processFile(file);
        });
    }

    if (modalThem) {
        modalThem.addEventListener('hidden.bs.modal', function() {
            clearPreview();
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }

    // Modal Sửa: gán data vào form
    var modalSua = document.getElementById('modalSuaSanPham');
    var formSua = document.getElementById('formSuaSanPham');
    var suaInputHinhAnh = document.getElementById('sua_sp_hinh_anh');
    var suaInputHinhAnhDuongDan = document.getElementById('sua_hinh_anh_duong_dan');
    var suaPlaceholder = document.getElementById('sua_sp_hinh_anh_placeholder');
    var suaPreviewImg = document.getElementById('sua_sp_hinh_anh_preview');
    var suaErrorDiv = document.getElementById('sua_sp_hinh_anh_error');
    var suaObjectUrl = null;

    function suaPreviewUrlFromPath(path) {
        var raw = (path || '').trim();
        if (!raw) return '';
        if (/^https?:\/\//i.test(raw)) return raw;
        return '/storage/' + raw.replace(/^\/+/, '');
    }

    function suaUpdatePreviewFromPathInput() {
        if (!suaInputHinhAnhDuongDan || !suaPreviewImg) return;
        if (suaObjectUrl) return;

        var previewUrl = suaPreviewUrlFromPath(suaInputHinhAnhDuongDan.value);
        if (previewUrl) {
            suaPreviewImg.src = previewUrl;
            suaPreviewImg.classList.remove('d-none');
            if (suaPlaceholder) suaPlaceholder.classList.add('d-none');
        } else {
            suaPreviewImg.src = '';
            suaPreviewImg.classList.add('d-none');
            if (suaPlaceholder) suaPlaceholder.classList.remove('d-none');
        }
    }

    function suaClearPreview(keepExistingImage) {
        if (suaObjectUrl) {
            URL.revokeObjectURL(suaObjectUrl);
            suaObjectUrl = null;
        }
        if (suaErrorDiv) {
            suaErrorDiv.classList.add('d-none');
            suaErrorDiv.textContent = '';
        }
        if (suaInputHinhAnh) suaInputHinhAnh.value = '';
        if (!keepExistingImage && suaInputHinhAnhDuongDan) suaInputHinhAnhDuongDan.value = '';

        if (keepExistingImage && suaPreviewImg && suaPreviewImg.dataset.existingSrc) {
            suaPreviewImg.src = suaPreviewImg.dataset.existingSrc;
            suaPreviewImg.classList.remove('d-none');
            if (suaPlaceholder) suaPlaceholder.classList.add('d-none');
            return;
        }

        if (suaPreviewImg) {
            suaPreviewImg.src = '';
            suaPreviewImg.classList.add('d-none');
            suaPreviewImg.dataset.existingSrc = '';
        }
        if (suaPlaceholder) suaPlaceholder.classList.remove('d-none');
    }

    function suaProcessFile(file) {
        if (suaObjectUrl) {
            URL.revokeObjectURL(suaObjectUrl);
            suaObjectUrl = null;
        }
        if (suaErrorDiv) {
            suaErrorDiv.classList.add('d-none');
            suaErrorDiv.textContent = '';
        }
        if (!file) return;

        if (!ALLOWED_TYPES.includes(file.type)) {
            if (suaErrorDiv) {
                suaErrorDiv.textContent = 'Vui lòng chọn file ảnh (JPEG, PNG, GIF hoặc WebP).';
                suaErrorDiv.classList.remove('d-none');
            }
            if (suaInputHinhAnh) suaInputHinhAnh.value = '';
            return;
        }
        if (file.size > MAX_SIZE) {
            if (suaErrorDiv) {
                suaErrorDiv.textContent = 'Kích thước file không được vượt quá 5MB. File của bạn: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB.';
                suaErrorDiv.classList.remove('d-none');
            }
            if (suaInputHinhAnh) suaInputHinhAnh.value = '';
            return;
        }

        suaObjectUrl = URL.createObjectURL(file);
        if (suaPreviewImg) {
            suaPreviewImg.src = suaObjectUrl;
            suaPreviewImg.classList.remove('d-none');
            if (suaPlaceholder) suaPlaceholder.classList.add('d-none');
        }
    }

    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-san-pham')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ten_san_pham').value = btn.getAttribute('data-ten') || '';
            document.getElementById('sua_ma_san_pham').value = btn.getAttribute('data-ma') || '';
            document.getElementById('sua_ngay_nhap').value = btn.getAttribute('data-ngay-nhap') || '';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
            document.getElementById('sua_gia_tri').value = btn.getAttribute('data-gia-tri') || '';

            var duongDanHinhAnh = btn.getAttribute('data-hinh-anh-duong-dan') || '';
            if (suaInputHinhAnhDuongDan) suaInputHinhAnhDuongDan.value = duongDanHinhAnh;

            // Ảnh hiện tại
            var existingImg = btn.getAttribute('data-hinh-anh') || suaPreviewUrlFromPath(duongDanHinhAnh);
            if (suaPreviewImg) suaPreviewImg.dataset.existingSrc = existingImg;
            suaClearPreview(true);
        });

        modalSua.addEventListener('hidden.bs.modal', function() {
            suaClearPreview(false);
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }

    if (suaInputHinhAnh) {
        suaInputHinhAnh.addEventListener('change', function() {
            var file = this.files && this.files[0];
            suaProcessFile(file);
        });
    }

    if (suaInputHinhAnhDuongDan) {
        suaInputHinhAnhDuongDan.addEventListener('input', function() {
            if (suaObjectUrl) {
                URL.revokeObjectURL(suaObjectUrl);
                suaObjectUrl = null;
            }
            if (suaInputHinhAnh) suaInputHinhAnh.value = '';
            suaUpdatePreviewFromPathInput();
        });
    }

    // Xóa: mở modal xác nhận, sau đó submit form
    var modalXoa = document.getElementById('modalXacNhanXoaSanPham');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaSanPham');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        document.querySelectorAll('.btn-xoa-san-pham').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                var modal = bootstrap.Modal.getOrCreateInstance(modalXoa);
                modal.show();
            });
        });
        btnXacNhanXoa.addEventListener('click', function() {
            if (formIdCanXoa) {
                var form = document.getElementById(formIdCanXoa);
                if (form) form.submit();
            }
            var inst = bootstrap.Modal.getInstance(modalXoa);
            if (inst) inst.hide();
            formIdCanXoa = null;
        });
    }

    // --- Kiểm tra sử dụng sản phẩm ---
    var modalKiemTra = document.getElementById('modalKiemTraSanPham');
    var ktTen = document.getElementById('ktspTen');
    var ktMa = document.getElementById('ktspMa');
    var ktStatus = document.getElementById('ktspStatus');
    var ktLoading = document.getElementById('ktspLoading');
    var ktError = document.getElementById('ktspError');
    var ktContent = document.getElementById('ktspContent');
    var ktGroupedWrap = document.getElementById('ktspGroupedWrap');
    var ktGroupedEmpty = document.getElementById('ktspGroupedEmpty');

    function ktResetUI() {
        if (ktStatus) ktStatus.textContent = '';
        if (ktError) {
            ktError.classList.add('d-none');
            ktError.textContent = '';
        }
        if (ktContent) ktContent.classList.add('d-none');
        if (ktLoading) ktLoading.classList.add('d-none');
        if (ktGroupedWrap) ktGroupedWrap.innerHTML = '';
        if (ktGroupedEmpty) ktGroupedEmpty.classList.add('d-none');
    }

    function escHtml(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function fmtRange(tu, den) {
        if (!tu && !den) return '—';
        if (tu && den && tu !== den) return tu + ' → ' + den;
        return (tu || den);
    }

    function addDaysInclusive(startYmd, endYmd) {
        if (!startYmd || !endYmd) return [];
        var start = new Date(startYmd + 'T00:00:00');
        var end = new Date(endYmd + 'T00:00:00');
        if (isNaN(start.getTime()) || isNaN(end.getTime())) return [];
        if (end < start) return [];
        var out = [];
        for (var d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            out.push(y + '-' + m + '-' + day);
        }
        return out;
    }

    function buildGroupedByDay(thueItems, cuoiItems) {
        var map = new Map(); // ymd -> {thue:[], cuoi:[]}
        function ensure(ymd) {
            if (!map.has(ymd)) map.set(ymd, { thue: [], cuoi: [] });
            return map.get(ymd);
        }

        (Array.isArray(thueItems) ? thueItems : []).forEach(function (row) {
            var days = addDaysInclusive(row.tu_ngay, row.den_ngay);
            days.forEach(function (ymd) {
                ensure(ymd).thue.push(row);
            });
        });

        (Array.isArray(cuoiItems) ? cuoiItems : []).forEach(function (row) {
            if (!row.ngay) return;
            ensure(row.ngay).cuoi.push(row);
        });

        var daysSorted = Array.from(map.keys()).sort(function (a, b) { return a.localeCompare(b); });
        return { map: map, days: daysSorted };
    }

    function renderGrouped(thueItems, cuoiItems) {
        var grouped = buildGroupedByDay(thueItems, cuoiItems);
        if (!grouped.days.length) {
            if (ktGroupedEmpty) ktGroupedEmpty.classList.remove('d-none');
            return;
        }

        grouped.days.forEach(function (ymd, idx) {
            var bucket = grouped.map.get(ymd) || { thue: [], cuoi: [] };
            var headId = 'ktsp-day-head-' + idx;
            var collapseId = 'ktsp-day-body-' + idx;
            var total = (bucket.thue?.length || 0) + (bucket.cuoi?.length || 0);

            var item = document.createElement('div');
            item.className = 'accordion-item';
            item.innerHTML =
                '<h2 class="accordion-header" id="' + headId + '">' +
                    '<button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '" aria-expanded="false" aria-controls="' + collapseId + '">' +
                        '<div class="d-flex w-100 align-items-center justify-content-between gap-2">' +
                            '<div class="fw-semibold ms-2">' + escHtml(ymd) + '</div>' +
                            '<span class="badge bg-label-primary">' + escHtml(total) + ' đơn</span>' +
                        '</div>' +
                    '</button>' +
                '</h2>' +
                '<div id="' + collapseId + '" class="accordion-collapse collapse" aria-labelledby="' + headId + '" data-bs-parent="#ktspGroupedWrap">' +
                    '<div class="px-0">' +
                        '<ul class="list-group list-group-flush ktsp-day-list" data-ktsp-day-list="1"></ul>' +
                    '</div>' +
                '</div>';

            if (ktGroupedWrap) ktGroupedWrap.appendChild(item);

            var ul = item.querySelector('ul[data-ktsp-day-list="1"]');
            if (!ul) return;

            bucket.thue.forEach(function (row) {
                var rangeTxt = fmtRange(row.tu_ngay, row.den_ngay);
                var kh = row.khach_hang || {};
                var ten = (kh.ten || '').trim();
                var sdt = (kh.sdt || '').trim();
                var sub = (ten || sdt) ? (escHtml(ten) + (sdt ? (' • ' + escHtml(sdt)) : '')) : '';
                var trangThai = (row.trang_thai === 0) ? 'Đang thuê' : ((row.trang_thai === 1) ? 'Hoàn thành' : 'Đã huỷ');
                var badgeCls = (row.trang_thai === 0) ? 'bg-label-warning' : ((row.trang_thai === 1) ? 'bg-label-success' : 'bg-label-danger');

                var li = document.createElement('li');
                li.className = 'list-group-item px-0';
                li.innerHTML =
                    '<div class="d-flex align-items-start justify-content-between gap-2">' +
                        '<div class="min-w-0">' +
                            '<div class="fw-medium text-body text-truncate"><span class="badge bg-label-secondary me-2">Thuê</span>HĐ #' + escHtml(row.hop_dong_id) + '</div>' +
                            (sub ? ('<div class="text-muted small text-truncate">' + sub + '</div>') : '') +
                            '<div class="text-body small mt-1"><i class="fa-regular fa-calendar me-1 text-muted"></i>' + escHtml(rangeTxt) + '</div>' +
                        '</div>' +
                        '<span class="badge ' + badgeCls + ' align-self-start">' + escHtml(trangThai) + '</span>' +
                    '</div>';
                ul.appendChild(li);
            });

            bucket.cuoi.forEach(function (row) {
                var ma = (row.ma_hop_dong || '').trim();
                var capdoi = (row.cap_doi || '').trim();

                var li = document.createElement('li');
                li.className = 'list-group-item px-0';
                li.innerHTML =
                    '<div class="d-flex align-items-start justify-content-between gap-2">' +
                        '<div class="min-w-0">' +
                            '<div class="fw-medium text-body text-truncate"><span class="badge bg-label-info me-2">Cưới</span>HĐ #' + escHtml(row.hop_dong_id) + (ma ? (' • ' + escHtml(ma)) : '') + '</div>' +
                            (capdoi ? ('<div class="text-muted small text-truncate">' + escHtml(capdoi) + '</div>') : '') +
                        '</div>' +
                    '</div>';
                ul.appendChild(li);
            });
        });
    }

    async function ktFetch(url) {
        var res = await RestApi.get(url);
        if (!res.ok) {
            throw new Error('HTTP ' + res.status + ': ' + (res.message || ''));
        }
        return res.data;
    }

    if (modalKiemTra) {
        modalKiemTra.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-kiem-tra-san-pham')) return;

            var ten = btn.getAttribute('data-ten') || '';
            var ma = btn.getAttribute('data-ma') || '';
            var url = btn.getAttribute('data-url') || '';

            if (ktTen) ktTen.textContent = ten || '—';
            if (ktMa) ktMa.textContent = ma ? ('Mã: ' + ma) : '—';
            ktResetUI();
            if (ktLoading) ktLoading.classList.remove('d-none');

            ktFetch(url)
                .then(function (data) {
                    if (ktLoading) ktLoading.classList.add('d-none');
                    if (ktContent) ktContent.classList.remove('d-none');

                    var thue = data && data.thue ? data.thue : [];
                    var cuoi = data && data.cuoi ? data.cuoi : [];
                    renderGrouped(thue, cuoi);
                    if (ktStatus) {
                        ktStatus.textContent = 'Thuê: ' + (Array.isArray(thue) ? thue.length : 0) + ' • Cưới: ' + (Array.isArray(cuoi) ? cuoi.length : 0);
                    }
                })
                .catch(function (err) {
                    if (ktLoading) ktLoading.classList.add('d-none');
                    if (ktError) {
                        ktError.textContent = 'Không tải được dữ liệu kiểm tra. ' + (err && err.message ? err.message : '');
                        ktError.classList.remove('d-none');
                    }
                });
        });

        modalKiemTra.addEventListener('hidden.bs.modal', function () {
            ktResetUI();
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }
});
</script>
@endpush
@endsection

