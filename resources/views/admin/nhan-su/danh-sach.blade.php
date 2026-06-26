@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\User::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'asc');
    $hasFilter = request()->filled('tu_khoa')
        || request()->filled('gioi_tinh')
        || request()->filled('phong_ban_id')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'asc';
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
        <form action="{{ route('admin.nhan-su.danh-sach') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_khoa">Từ khóa</label>
                    <input type="text"
                           class="form-control @error('tu_khoa') is-invalid @enderror"
                           id="tu_khoa"
                           name="tu_khoa"
                           value="{{ old('tu_khoa', request('tu_khoa')) }}"
                           placeholder="Nhập...">
                    @error('tu_khoa')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_gioi_tinh">Giới tính</label>
                    <select class="select2-admin form-select" id="filter_gioi_tinh" name="gioi_tinh" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach(\App\Models\User::GIOI_TINH_OPTIONS as $value => $label)
                            <option value="{{ $value }}" @selected(request('gioi_tinh') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_phong_ban_id">Phòng ban</label>
                    <select class="select2-admin form-select" id="filter_phong_ban_id" name="phong_ban_id" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach($phongBans ?? [] as $pb)
                            <option value="{{ $pb->id }}" @selected((string) request('phong_ban_id') === (string) $pb->id)>{{ $pb->ten_phong_ban }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\User::SAP_XEP_OPTIONS as $value => $label)
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
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.nhan-su.danh-sach') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách nhân sự</span>
            <span data-bs-toggle="tooltip" title="Thêm nhân sự mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemNhanSu">
                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                </button>
            </span>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered nhan-su-table-wrap">
            <table class="table table-hover table-bordered mb-0 nhan-su-table">
                <thead class="table-light">
                    <tr class="nhan-su-thead-group">
                        <th rowspan="2" style="width: 60px;" class="text-center align-middle">STT</th>
                        <th rowspan="2" style="width: 70px;" class="text-center align-middle">Hình ảnh</th>
                        <th rowspan="2" class="nhan-su-sticky nhan-su-sticky-ho-ten align-middle" style="min-width: 160px;">Họ tên</th>
                        <th colspan="5" class="text-center nhan-su-th-group">Thông tin cá nhân</th>
                        <th colspan="7" class="text-center nhan-su-th-group">Thông tin làm việc</th>
                        <th colspan="5" class="text-center nhan-su-th-group">Lương</th>
                        <th colspan="2" class="text-center nhan-su-th-group">Hoa hồng</th>
                        <th colspan="4" class="text-center nhan-su-th-group">Ngân hàng</th>
                        <th rowspan="2" class="text-center align-middle" style="width: 100px;">Thao tác</th>
                    </tr>
                    <tr>
                        <th>Giới tính</th>
                        <th>Ngày sinh</th>
                        <th>CCCD</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Phòng ban</th>
                        <th>Ngày vào công ty</th>
                        <th>Ngày ký hợp đồng</th>
                        <th>Loại nhân viên</th>
                        <th>Loại hợp đồng</th>
                        <th>Trạng thái</th>
                        <th>Lương cứng</th>
                        <th>Lương mềm</th>
                        <th>Phụ cấp</th>
                        <th>Lương cơ bản</th>
                        <th>Lương tăng ca</th>
                        <th>HĐ cưới</th>
                        <th>HĐ trang phục</th>
                        <th>Ngân hàng</th>
                        <th>Chi nhánh</th>
                        <th>Số tài khoản</th>
                        <th>Chủ tài khoản</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $nv = $item->nhanVien;
                        $hinhAnh = $nv?->hinh_anh;
                        $vaiTroLabel = $item->vaiTro?->ten_vai_tro ?? '—';
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td class="text-center">
                            @if(!empty($hinhAnh))
                            <img src="{{ asset('storage/' . $hinhAnh) }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                            <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px;">
                                <span class="text-primary fw-medium">{{ strtoupper(mb_substr($item->name ?? 'N', 0, 1)) }}</span>
                            </div>
                            @endif
                        </td>
                        <td class="nhan-su-sticky nhan-su-sticky-ho-ten"><span class="fw-medium">{{ $item->name ?? '—' }}</span></td>
                        <td>{{ $nv?->gioi_tinh ?? '—' }}</td>
                        <td>{{ $nv?->ngay_sinh ? $nv->ngay_sinh->format('d/m/Y') : '—' }}</td>
                        <td>{{ $nv?->cccd ?? '—' }}</td>
                        <td>{{ $item->email ?? '—' }}</td>
                        <td>{{ $item->phone ?? '—' }}</td>
                        <td>{{ $vaiTroLabel }}</td>
                        <td>{{ $nv?->phongBan?->ten_phong_ban ?? '—' }}</td>
                        <td>{{ $nv?->ngay_vao_cong_ty ? $nv->ngay_vao_cong_ty->format('d/m/Y') : '—' }}</td>
                        <td>{{ $nv?->ngay_ky_hop_dong ? $nv->ngay_ky_hop_dong->format('d/m/Y') : '—' }}</td>
                        <td>{{ $nv?->loai_nhan_vien ? (\App\Models\NhanVien::LOAI_NHAN_VIEN_OPTIONS[$nv->loai_nhan_vien] ?? $nv->loai_nhan_vien) : '—' }}</td>
                        <td>{{ $nv?->loai_hop_dong ? (\App\Models\NhanVien::LOAI_HOP_DONG_OPTIONS[$nv->loai_hop_dong] ?? $nv->loai_hop_dong) : '—' }}</td>
                        <td>{{ \App\Models\User::STATUS_OPTIONS[$item->status] ?? '—' }}</td>
                        <td>{{ $nv?->luong_cung !== null ? number_format($nv->luong_cung) : '—' }}</td>
                        <td>{{ $nv?->luong_mem !== null ? number_format($nv->luong_mem) : '—' }}</td>
                        <td>{{ $nv?->phu_cap !== null ? number_format($nv->phu_cap) : '—' }}</td>
                        <td>{{ $nv?->luong_co_ban !== null ? number_format($nv->luong_co_ban) : '—' }}</td>
                        <td>{{ $nv?->luong_tang_ca !== null ? number_format($nv->luong_tang_ca) : '—' }}</td>
                        <td>{{ $nv?->hoa_hong_hop_dong_cuoi !== null ? number_format((float) $nv->hoa_hong_hop_dong_cuoi, 2, ',', '.') : '—' }}</td>
                        <td>{{ $nv?->hoa_hong_hop_dong_trang_phuc !== null ? number_format((float) $nv->hoa_hong_hop_dong_trang_phuc, 2, ',', '.') : '—' }}</td>
                        <td>{{ $nv?->ngan_hang ?? '—' }}</td>
                        <td>{{ $nv?->chi_nhanh ?? '—' }}</td>
                        <td class="text-nowrap">{{ $nv?->so_tai_khoan ?? '—' }}</td>
                        <td>{{ $nv?->chu_tai_khoan ?? '—' }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-sua-nhan-su"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaNhanSu"
                                       data-url="{{ route('admin.nhan-su.update', $item) }}"
                                       data-name="{{ e($item->name ?? '') }}"
                                       data-email="{{ e($item->email ?? '') }}"
                                       data-phone="{{ e($item->phone ?? '') }}"
                                       data-gioi-tinh="{{ e($nv?->gioi_tinh ?? '') }}"
                                       data-ngay-sinh="{{ $nv?->ngay_sinh?->format('Y-m-d') ?? '' }}"
                                       data-cccd="{{ e($nv?->cccd ?? '') }}"
                                       data-role="{{ $item->role !== null && $item->role !== '' ? (string) $item->role : '' }}"
                                       data-phong-ban-id="{{ $nv?->phongBan?->id ?? '' }}"
                                       data-ngay-vao-cong-ty="{{ $nv?->ngay_vao_cong_ty?->format('Y-m-d') ?? '' }}"
                                       data-ngay-ky-hop-dong="{{ $nv?->ngay_ky_hop_dong?->format('Y-m-d') ?? '' }}"
                                       data-loai-nhan-vien="{{ e($nv?->loai_nhan_vien ?? '') }}"
                                       data-loai-hop-dong="{{ e($nv?->loai_hop_dong ?? '') }}"
                                       data-status="{{ $item->status !== null ? (string) $item->status : '' }}"
                                       data-luong-cung="{{ $nv?->luong_cung ?? '' }}"
                                       data-luong-mem="{{ $nv?->luong_mem ?? '' }}"
                                       data-phu-cap="{{ $nv?->phu_cap ?? '' }}"
                                       data-luong-co-ban="{{ $nv?->luong_co_ban ?? '' }}"
                                       data-luong-tang-ca="{{ $nv?->luong_tang_ca ?? '' }}"
                                       data-hoa-hong-hop-dong-cuoi="{{ $nv?->hoa_hong_hop_dong_cuoi !== null ? $nv->hoa_hong_hop_dong_cuoi : '' }}"
                                       data-hoa-hong-hop-dong-trang-phuc="{{ $nv?->hoa_hong_hop_dong_trang_phuc !== null ? $nv->hoa_hong_hop_dong_trang_phuc : '' }}"
                                       data-ngan-hang="{{ e($nv?->ngan_hang ?? '') }}"
                                       data-chi-nhanh="{{ e($nv?->chi_nhanh ?? '') }}"
                                       data-so-tai-khoan="{{ e($nv?->so_tai_khoan ?? '') }}"
                                       data-chu-tai-khoan="{{ e($nv?->chu_tai_khoan ?? '') }}"
                                       data-hinh-anh="{{ !empty($hinhAnh) ? asset('storage/' . $hinhAnh) : '' }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <a class="dropdown-item btn-doi-mat-khau"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalDoiMatKhau"
                                       data-url="{{ route('admin.nhan-su.doi-mat-khau', $item) }}"
                                       data-name="{{ e($item->name ?? '') }}">
                                        <i class="fa-solid fa-key me-2"></i> Đổi mật khẩu
                                    </a>
                                    <form id="form-xoa-{{ $item->id }}" action="{{ route('admin.nhan-su.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-nhan-su" data-form-id="form-xoa-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="27" class="text-center py-4 text-muted">Chưa có dữ liệu nhân sự.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" />
        </div>
    </div>
</div>

{{-- Modal Đổi mật khẩu --}}
<div class="modal fade" id="modalDoiMatKhau" tabindex="-1" aria-labelledby="modalDoiMatKhauLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-doi-mat-khau">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDoiMatKhauLabel">Đổi mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formDoiMatKhau" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="doiMatKhau_password">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="doiMatKhau_password" name="password" placeholder="Nhập mật khẩu mới" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="doiMatKhau_password_confirmation">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="doiMatKhau_password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu mới" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-key me-1"></i> Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Thêm mới nhân sự --}}
<div class="modal fade" id="modalThemNhanSu" tabindex="-1" aria-labelledby="modalThemNhanSuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-them-nhan-su">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemNhanSuLabel">Thêm nhân sự mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.nhan-su.store') }}" method="POST" enctype="multipart/form-data" id="formThemNhanSu">
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
                    <div class="row g-4 align-items-start">
                        <div class="col-12 col-lg-3">
                            <div class="sticky-lg-top nhan-su-form-avatar">
                                <label class="form-label" for="them_hinh_anh">Hình ảnh</label>
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden mb-2" style="min-height: 200px;">
                                    <div id="them_hinh_anh_placeholder" class="text-center text-muted py-4 px-3">
                                        <span class="small">Vui lòng chọn ảnh đại diện</span>
                                    </div>
                                    <img id="them_hinh_anh_preview" src="" alt="Preview" class="rounded w-100 d-none" style="max-height: 200px; object-fit: cover;">
                                </div>
                                <input type="file" class="form-control" id="them_hinh_anh" name="hinh_anh" accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-muted d-block mt-1">JPEG, PNG, GIF, WebP — tối đa 5MB</small>
                                <div id="them_hinh_anh_error" class="alert alert-danger mt-2 d-none" role="alert"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-9 d-flex flex-column gap-4">
                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Thông tin cá nhân</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_ho_ten">Họ tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="them_ho_ten" name="name" value="{{ old('name') }}" placeholder="Nhập họ tên" required>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_gioi_tinh">Giới tính</label>
                                        <select class="select2-admin form-select" id="them_gioi_tinh" name="gioi_tinh" data-placeholder="Chọn giới tính">
                                            <option value="">-- Chọn --</option>
                                            <option value="nam" {{ old('gioi_tinh') == 'nam' ? 'selected' : '' }}>Nam</option>
                                            <option value="nu" {{ old('gioi_tinh') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                            <option value="khac" {{ old('gioi_tinh') == 'khac' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="them_email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_so_dien_thoai">Số điện thoại</label>
                                        <input type="text" class="form-control" id="them_so_dien_thoai" name="phone" value="{{ old('phone') }}" placeholder="0912345678" maxlength="20">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_ngay_sinh">Ngày sinh</label>
                                        <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_cccd">CCCD</label>
                                        <input type="text" class="form-control" id="them_cccd" name="cccd" value="{{ old('cccd') }}" placeholder="Số CCCD/CMND" maxlength="20">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_mat_khau">Mật khẩu <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="them_mat_khau" name="password" placeholder="Nhập mật khẩu" required>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_mat_khau_xac_nhan">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="them_mat_khau_xac_nhan" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                                    </div>
                                </div>
                            </div>

                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Vị trí làm việc</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_vai_tro">Vai trò</label>
                                        <select class="select2-admin form-select" id="them_vai_tro" name="role" data-placeholder="Chọn vai trò">
                                            @foreach($dsVaiTro ?? [] as $vt)
                                            <option value="{{ $vt->ma_vai_tro }}" {{ (string) old('role', $maVaiTroMacDinh ?? '') === (string) $vt->ma_vai_tro ? 'selected' : '' }}>{{ $vt->ten_vai_tro }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label for="them_phong_ban" class="form-label">Phòng ban <span class="text-danger">*</span></label>
                                        <select id="them_phong_ban" name="phong_ban_id" class="select2-admin form-select" data-placeholder="Chọn phòng ban" required>
                                            <option value="">-- Chọn --</option>
                                            @foreach($phongBans ?? [] as $pb)
                                            <option value="{{ $pb->id }}" @selected((string) old('phong_ban_id') === (string) $pb->id)>{{ $pb->ten_phong_ban }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_ngay_vao_cong_ty">Ngày vào công ty</label>
                                        <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_vao_cong_ty" name="ngay_vao_cong_ty" value="{{ old('ngay_vao_cong_ty') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_ngay_ky_hop_dong">Ngày ký hợp đồng</label>
                                        <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_ky_hop_dong" name="ngay_ky_hop_dong" value="{{ old('ngay_ky_hop_dong') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_loai_nhan_vien">Loại nhân viên <span class="text-danger">*</span></label>
                                        <select class="select2-admin form-select" id="them_loai_nhan_vien" name="loai_nhan_vien" data-placeholder="Chọn loại nhân viên" required>
                                            <option value="">-- Chọn --</option>
                                            @foreach(\App\Models\NhanVien::LOAI_NHAN_VIEN_OPTIONS as $value => $label)
                                            <option value="{{ $value }}" @selected(old('loai_nhan_vien') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_loai_hop_dong">Loại hợp đồng</label>
                                        <select class="select2-admin form-select" id="them_loai_hop_dong" name="loai_hop_dong" data-placeholder="Chọn loại hợp đồng">
                                            <option value="">-- Chọn --</option>
                                            @foreach(\App\Models\NhanVien::LOAI_HOP_DONG_OPTIONS as $value => $label)
                                            <option value="{{ $value }}" @selected(old('loai_hop_dong') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_status">Trạng thái</label>
                                        <select class="select2-admin form-select" id="them_status" name="status" data-placeholder="Chọn trạng thái">
                                            @foreach(\App\Models\User::STATUS_OPTIONS as $value => $label)
                                            <option value="{{ $value }}" @selected((string) old('status', \App\Models\User::STATUS_MAC_DINH) === (string) $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Lương &amp; thưởng</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_luong_cung">Lương cứng (VNĐ)</label>
                                        <input type="number" class="form-control" id="them_luong_cung" name="luong_cung" value="{{ old('luong_cung', 0) }}" placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_luong_mem">Lương mềm (VNĐ)</label>
                                        <input type="number" class="form-control" id="them_luong_mem" name="luong_mem" value="{{ old('luong_mem', 0) }}" placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_phu_cap">Phụ cấp (VNĐ)</label>
                                        <input type="number" class="form-control" id="them_phu_cap" name="phu_cap" value="{{ old('phu_cap', 0) }}" placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_luong_co_ban">Lương cơ bản (VNĐ)</label>
                                        <input type="number" class="form-control" id="them_luong_co_ban" name="luong_co_ban" value="{{ old('luong_co_ban', 50000) }}" placeholder="50000" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_luong_tang_ca">Lương tăng ca (VNĐ)</label>
                                        <input type="number" class="form-control" id="them_luong_tang_ca" name="luong_tang_ca" value="{{ old('luong_tang_ca', 80000) }}" placeholder="80000" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_hoa_hong_hop_dong_cuoi">Hoa hồng HĐ cưới</label>
                                        <input type="number" class="form-control" id="them_hoa_hong_hop_dong_cuoi" name="hoa_hong_hop_dong_cuoi" value="{{ old('hoa_hong_hop_dong_cuoi', 1) }}" placeholder="1,00" min="0" step="0.01">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_hoa_hong_hop_dong_trang_phuc">Hoa hồng HĐ trang phục</label>
                                        <input type="number" class="form-control" id="them_hoa_hong_hop_dong_trang_phuc" name="hoa_hong_hop_dong_trang_phuc" value="{{ old('hoa_hong_hop_dong_trang_phuc', 1) }}" placeholder="1,00" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Ngân hàng</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_ngan_hang">Ngân hàng</label>
                                        <select class="select2-admin form-select" id="them_ngan_hang" name="ngan_hang" data-placeholder="Chọn ngân hàng">
                                            <option value=""></option>
                                            @foreach($dsNganHang ?? [] as $bank)
                                            <option value="{{ $bank['short_name'] }}" @selected(old('ngan_hang') === $bank['short_name'])>{{ $bank['short_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_chi_nhanh">Chi nhánh</label>
                                        <input type="text" class="form-control" id="them_chi_nhanh" name="chi_nhanh" value="{{ old('chi_nhanh') }}" placeholder="Chi nhánh ngân hàng" maxlength="255">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_so_tai_khoan">Số tài khoản</label>
                                        <input type="text" class="form-control" id="them_so_tai_khoan" name="so_tai_khoan" value="{{ old('so_tai_khoan') }}" placeholder="Nhập số tài khoản" maxlength="50">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="them_chu_tai_khoan">Chủ tài khoản</label>
                                        <input type="text" class="form-control" id="them_chu_tai_khoan" name="chu_tai_khoan" value="{{ old('chu_tai_khoan') }}" placeholder="Tên chủ tài khoản" maxlength="150">
                                    </div>
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

{{-- Modal Chỉnh sửa nhân sự --}}
<div class="modal fade" id="modalSuaNhanSu" tabindex="-1" aria-labelledby="modalSuaNhanSuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-them-nhan-su">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaNhanSuLabel">Chỉnh sửa nhân sự</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaNhanSu" method="POST" enctype="multipart/form-data" action="">
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
                    <div class="row g-4 align-items-start">
                        <div class="col-12 col-lg-3">
                            <div class="sticky-lg-top nhan-su-form-avatar">
                                <label class="form-label" for="sua_hinh_anh">Hình ảnh</label>
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden mb-2" style="min-height: 200px;">
                                    <div id="sua_hinh_anh_placeholder" class="text-center text-muted py-4 px-3">
                                        <span class="small">Ảnh hiện tại hoặc chọn ảnh mới</span>
                                    </div>
                                    <img id="sua_hinh_anh_preview" src="" alt="Preview" class="rounded w-100 d-none" style="max-height: 200px; object-fit: cover;">
                                </div>
                                <input type="file" class="form-control" id="sua_hinh_anh" name="hinh_anh" accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-muted d-block mt-1">JPEG, PNG, GIF, WebP — tối đa 5MB</small>
                                <div id="sua_hinh_anh_error" class="alert alert-danger mt-2 d-none" role="alert"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-9 d-flex flex-column gap-4">
                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Thông tin cá nhân</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_ho_ten">Họ tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="sua_ho_ten" name="name" placeholder="Nhập họ tên" required>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_gioi_tinh">Giới tính</label>
                                        <select class="select2-admin form-select" id="sua_gioi_tinh" name="gioi_tinh" data-placeholder="Chọn giới tính">
                                            <option value="">-- Chọn --</option>
                                            <option value="nam">Nam</option>
                                            <option value="nu">Nữ</option>
                                            <option value="khac">Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="sua_email" name="email" placeholder="email@example.com" required>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_so_dien_thoai">Số điện thoại</label>
                                        <input type="text" class="form-control" id="sua_so_dien_thoai" name="phone" placeholder="0912345678" maxlength="20">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_ngay_sinh">Ngày sinh</label>
                                        <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_sinh" name="ngay_sinh" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_cccd">CCCD</label>
                                        <input type="text" class="form-control" id="sua_cccd" name="cccd" placeholder="Số CCCD/CMND" maxlength="20">
                                    </div>
                                </div>
                            </div>

                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Vị trí làm việc</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_vai_tro">Vai trò</label>
                                        <select class="select2-admin form-select" id="sua_vai_tro" name="role" data-placeholder="Chọn vai trò">
                                            @foreach($dsVaiTro ?? [] as $vt)
                                            <option value="{{ $vt->ma_vai_tro }}">{{ $vt->ten_vai_tro }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_phong_ban">Phòng ban <span class="text-danger">*</span></label>
                                        <select id="sua_phong_ban" name="phong_ban_id" class="select2-admin form-select" data-placeholder="Chọn phòng ban" required>
                                            <option value="">-- Chọn --</option>
                                            @foreach($phongBans ?? [] as $pb)
                                            <option value="{{ $pb->id }}">{{ $pb->ten_phong_ban }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_ngay_vao_cong_ty">Ngày vào công ty</label>
                                        <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_vao_cong_ty" name="ngay_vao_cong_ty" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_ngay_ky_hop_dong">Ngày ký hợp đồng</label>
                                        <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_ky_hop_dong" name="ngay_ky_hop_dong" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_loai_nhan_vien">Loại nhân viên <span class="text-danger">*</span></label>
                                        <select class="select2-admin form-select" id="sua_loai_nhan_vien" name="loai_nhan_vien" data-placeholder="Chọn loại nhân viên" required>
                                            <option value="">-- Chọn --</option>
                                            @foreach(\App\Models\NhanVien::LOAI_NHAN_VIEN_OPTIONS as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_loai_hop_dong">Loại hợp đồng</label>
                                        <select class="select2-admin form-select" id="sua_loai_hop_dong" name="loai_hop_dong" data-placeholder="Chọn loại hợp đồng">
                                            <option value="">-- Chọn --</option>
                                            @foreach(\App\Models\NhanVien::LOAI_HOP_DONG_OPTIONS as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_status">Trạng thái</label>
                                        <select class="select2-admin form-select" id="sua_status" name="status" data-placeholder="Chọn trạng thái">
                                            @foreach(\App\Models\User::STATUS_OPTIONS as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Lương &amp; thưởng</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_luong_cung">Lương cứng (VNĐ)</label>
                                        <input type="number" class="form-control" id="sua_luong_cung" name="luong_cung" placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_luong_mem">Lương mềm (VNĐ)</label>
                                        <input type="number" class="form-control" id="sua_luong_mem" name="luong_mem" placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_phu_cap">Phụ cấp (VNĐ)</label>
                                        <input type="number" class="form-control" id="sua_phu_cap" name="phu_cap" placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_luong_co_ban">Lương cơ bản (VNĐ)</label>
                                        <input type="number" class="form-control" id="sua_luong_co_ban" name="luong_co_ban" placeholder="50000" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_luong_tang_ca">Lương tăng ca (VNĐ)</label>
                                        <input type="number" class="form-control" id="sua_luong_tang_ca" name="luong_tang_ca" placeholder="80000" min="0" step="1000">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_hoa_hong_hop_dong_cuoi">Hoa hồng HĐ cưới</label>
                                        <input type="number" class="form-control" id="sua_hoa_hong_hop_dong_cuoi" name="hoa_hong_hop_dong_cuoi" placeholder="1,00" min="0" step="0.01">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_hoa_hong_hop_dong_trang_phuc">Hoa hồng HĐ trang phục</label>
                                        <input type="number" class="form-control" id="sua_hoa_hong_hop_dong_trang_phuc" name="hoa_hong_hop_dong_trang_phuc" placeholder="1,00" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <div class="nhan-su-form-section">
                                <h6 class="nhan-su-form-section-title">Ngân hàng</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_ngan_hang">Ngân hàng</label>
                                        <select class="select2-admin form-select" id="sua_ngan_hang" name="ngan_hang" data-placeholder="Chọn ngân hàng">
                                            <option value=""></option>
                                            @foreach($dsNganHang ?? [] as $bank)
                                            <option value="{{ $bank['short_name'] }}">{{ $bank['short_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_chi_nhanh">Chi nhánh</label>
                                        <input type="text" class="form-control" id="sua_chi_nhanh" name="chi_nhanh" placeholder="Chi nhánh ngân hàng" maxlength="255">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_so_tai_khoan">Số tài khoản</label>
                                        <input type="text" class="form-control" id="sua_so_tai_khoan" name="so_tai_khoan" placeholder="Nhập số tài khoản" maxlength="50">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <label class="form-label" for="sua_chu_tai_khoan">Chủ tài khoản</label>
                                        <input type="text" class="form-control" id="sua_chu_tai_khoan" name="chu_tai_khoan" placeholder="Tên chủ tài khoản" maxlength="150">
                                    </div>
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

{{-- Modal xác nhận xóa nhân sự --}}
<div class="modal fade" id="modalXacNhanXoaNhanSu" tabindex="-1" aria-labelledby="modalXacNhanXoaNhanSuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaNhanSuLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa nhân sự này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaNhanSu">
                    <i class="fa-solid fa-trash me-1"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<style>
#modalThemNhanSu .modal-them-nhan-su,
#modalSuaNhanSu .modal-them-nhan-su {
    max-width: 96vw;
    width: 1400px;
}
@media (min-width: 1400px) {
    #modalThemNhanSu .modal-them-nhan-su,
    #modalSuaNhanSu .modal-them-nhan-su {
        max-width: 1400px;
    }
}
.nhan-su-form-section .form-label {
    font-size: 0.8125rem;
    margin-bottom: 0.35rem;
}
.nhan-su-form-avatar {
    top: 1rem;
}
#modalDoiMatKhau .modal-doi-mat-khau {
    max-width: 90vw;
    width: 600px;
}
@media (min-width: 576px) {
    #modalDoiMatKhau .modal-doi-mat-khau {
        max-width: 600px;
    }
}
#modalXacNhanXoaNhanSu .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
.table-wrapper-bordered {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 1900px;
}
.nhan-su-form-section-title {
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: var(--bs-secondary-color);
    margin-bottom: 0.75rem;
    padding-bottom: 0.4rem;
    border-bottom: 1px solid var(--bs-border-color, #dee2e6);
}
.nhan-su-form-section:last-child {
    margin-bottom: 0;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
.nhan-su-table thead .nhan-su-th-group {
    border-bottom-width: 1px;
}
.nhan-su-table thead tr.nhan-su-thead-group th {
    vertical-align: middle;
}
[data-bs-theme='dark'] .nhan-su-table thead .nhan-su-th-group {
    background-color: #353a52;
}
.nhan-su-table .nhan-su-sticky {
    position: sticky;
    left: 0;
    z-index: 2;
    background-color: #fff;
    background-clip: padding-box;
}
.nhan-su-table thead .nhan-su-sticky {
    z-index: 6;
    background-color: #f8f9fa;
}
.nhan-su-table.table-hover > tbody > tr:hover > .nhan-su-sticky {
    background-color: #f5f5f9;
}
[data-bs-theme='dark'] .nhan-su-table .nhan-su-sticky {
    background-color: #2f3349;
}
[data-bs-theme='dark'] .nhan-su-table thead .nhan-su-sticky {
    background-color: #353a52;
}
[data-bs-theme='dark'] .nhan-su-table.table-hover > tbody > tr:hover > .nhan-su-sticky {
    background-color: #3a3f5c;
}
.nhan-su-table .nhan-su-sticky-ho-ten {
    min-width: 160px;
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.08);
}
[data-bs-theme='dark'] .nhan-su-table .nhan-su-sticky-ho-ten {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.35);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var MAX_SIZE = 5 * 1024 * 1024; // 5MB
    var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    var inputHinhAnh = document.getElementById('them_hinh_anh');
    var placeholder = document.getElementById('them_hinh_anh_placeholder');
    var previewImg = document.getElementById('them_hinh_anh_preview');
    var errorDiv = document.getElementById('them_hinh_anh_error');
    var currentObjectUrl = null;

    function clearPreview() {
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        previewImg.src = '';
        previewImg.classList.add('d-none');
        if (placeholder) placeholder.classList.remove('d-none');
        errorDiv.classList.add('d-none');
        errorDiv.textContent = '';
        if (inputHinhAnh) inputHinhAnh.value = '';
    }

    function processFile(file) {
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        if (placeholder) placeholder.classList.remove('d-none');
        previewImg.classList.add('d-none');
        errorDiv.classList.add('d-none');
        errorDiv.textContent = '';
        if (!file) return;

        if (!ALLOWED_TYPES.includes(file.type)) {
            errorDiv.textContent = 'Vui lòng chọn file ảnh (JPEG, PNG, GIF hoặc WebP).';
            errorDiv.classList.remove('d-none');
            return;
        }
        if (file.size > MAX_SIZE) {
            errorDiv.textContent = 'Kích thước file không được vượt quá 5MB. File của bạn: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB.';
            errorDiv.classList.remove('d-none');
            return;
        }

        if (placeholder) placeholder.classList.add('d-none');
        currentObjectUrl = URL.createObjectURL(file);
        previewImg.src = currentObjectUrl;
        previewImg.classList.remove('d-none');
    }

    if (inputHinhAnh) {
        inputHinhAnh.addEventListener('change', function() {
            var file = this.files && this.files[0];
            processFile(file);
        });
    }

    var modalEl = document.getElementById('modalThemNhanSu');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function() {
            clearPreview();
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }

    // Khởi tạo tooltip cho nút Thêm mới
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
    // Mở modal khi có lỗi validation (sau redirect)
    @if($errors->any())
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
    @endif

    // --- Modal Chỉnh sửa nhân sự ---
    var modalSua = document.getElementById('modalSuaNhanSu');
    var formSua = document.getElementById('formSuaNhanSu');
    var suaPreview = document.getElementById('sua_hinh_anh_preview');
    var suaPlaceholder = document.getElementById('sua_hinh_anh_placeholder');
    var suaInputFile = document.getElementById('sua_hinh_anh');
    var suaErrorDiv = document.getElementById('sua_hinh_anh_error');
    var suaObjectUrl = null;

    function setAdminSelect2Value(selectId, value) {
        var el = document.getElementById(selectId);
        if (!el) return;
        var v = value || '';
        el.value = v;
        if ($ && $.fn.select2 && $(el).data('select2')) {
            $(el).val(v || null).trigger('change');
        }
    }

    if (modalSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-nhan-su')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ho_ten').value = btn.getAttribute('data-name') || '';
            document.getElementById('sua_email').value = btn.getAttribute('data-email') || '';
            document.getElementById('sua_so_dien_thoai').value = btn.getAttribute('data-phone') || '';
            setAdminSelect2Value('sua_gioi_tinh', btn.getAttribute('data-gioi-tinh'));
            if (window.setAdminDateInput) setAdminDateInput('sua_ngay_sinh', btn.getAttribute('data-ngay-sinh') || ''); else document.getElementById('sua_ngay_sinh').value = btn.getAttribute('data-ngay-sinh') || '';
            document.getElementById('sua_cccd').value = btn.getAttribute('data-cccd') || '';
            setAdminSelect2Value('sua_vai_tro', btn.getAttribute('data-role'));
            setAdminSelect2Value('sua_phong_ban', btn.getAttribute('data-phong-ban-id'));
            if (window.setAdminDateInput) { setAdminDateInput('sua_ngay_vao_cong_ty', btn.getAttribute('data-ngay-vao-cong-ty') || ''); setAdminDateInput('sua_ngay_ky_hop_dong', btn.getAttribute('data-ngay-ky-hop-dong') || ''); } else { document.getElementById('sua_ngay_vao_cong_ty').value = btn.getAttribute('data-ngay-vao-cong-ty') || ''; document.getElementById('sua_ngay_ky_hop_dong').value = btn.getAttribute('data-ngay-ky-hop-dong') || ''; }
            setAdminSelect2Value('sua_loai_nhan_vien', btn.getAttribute('data-loai-nhan-vien'));
            setAdminSelect2Value('sua_loai_hop_dong', btn.getAttribute('data-loai-hop-dong'));
            setAdminSelect2Value('sua_status', btn.getAttribute('data-status'));
            document.getElementById('sua_luong_cung').value = btn.getAttribute('data-luong-cung') || '';
            document.getElementById('sua_luong_mem').value = btn.getAttribute('data-luong-mem') || '';
            document.getElementById('sua_phu_cap').value = btn.getAttribute('data-phu-cap') || '';
            document.getElementById('sua_luong_co_ban').value = btn.getAttribute('data-luong-co-ban') || '';
            document.getElementById('sua_luong_tang_ca').value = btn.getAttribute('data-luong-tang-ca') || '';
            document.getElementById('sua_hoa_hong_hop_dong_cuoi').value = btn.getAttribute('data-hoa-hong-hop-dong-cuoi') || '1';
            document.getElementById('sua_hoa_hong_hop_dong_trang_phuc').value = btn.getAttribute('data-hoa-hong-hop-dong-trang-phuc') || '1';
            setAdminSelect2Value('sua_ngan_hang', btn.getAttribute('data-ngan-hang'));
            document.getElementById('sua_chi_nhanh').value = btn.getAttribute('data-chi-nhanh') || '';
            document.getElementById('sua_so_tai_khoan').value = btn.getAttribute('data-so-tai-khoan') || '';
            document.getElementById('sua_chu_tai_khoan').value = btn.getAttribute('data-chu-tai-khoan') || '';
            var imgSrc = btn.getAttribute('data-hinh-anh') || '';
            if (imgSrc) {
                suaPreview.src = imgSrc;
                suaPreview.classList.remove('d-none');
                if (suaPlaceholder) suaPlaceholder.classList.add('d-none');
            } else {
                suaPreview.src = '';
                suaPreview.classList.add('d-none');
                if (suaPlaceholder) suaPlaceholder.classList.remove('d-none');
            }
            if (suaInputFile) suaInputFile.value = '';
            if (suaErrorDiv) { suaErrorDiv.classList.add('d-none'); suaErrorDiv.textContent = ''; }
            if (suaObjectUrl) { URL.revokeObjectURL(suaObjectUrl); suaObjectUrl = null; }
        });
        modalSua.addEventListener('hidden.bs.modal', function() {
            if (suaObjectUrl) {
                URL.revokeObjectURL(suaObjectUrl);
                suaObjectUrl = null;
            }
            if (suaInputFile) suaInputFile.value = '';
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }

    if (suaInputFile) {
        suaInputFile.addEventListener('change', function() {
            var file = this.files && this.files[0];
            if (suaErrorDiv) { suaErrorDiv.classList.add('d-none'); suaErrorDiv.textContent = ''; }
            if (!file) return;
            if (!ALLOWED_TYPES.includes(file.type)) {
                suaErrorDiv.textContent = 'Vui lòng chọn file ảnh (JPEG, PNG, GIF hoặc WebP).';
                suaErrorDiv.classList.remove('d-none');
                return;
            }
            if (file.size > MAX_SIZE) {
                suaErrorDiv.textContent = 'Kích thước file không được vượt quá 5MB.';
                suaErrorDiv.classList.remove('d-none');
                return;
            }
            if (suaObjectUrl) URL.revokeObjectURL(suaObjectUrl);
            suaObjectUrl = URL.createObjectURL(file);
            suaPreview.src = suaObjectUrl;
            suaPreview.classList.remove('d-none');
            if (suaPlaceholder) suaPlaceholder.classList.add('d-none');
        });
    }

    // Modal Đổi mật khẩu
    var modalDoiMatKhau = document.getElementById('modalDoiMatKhau');
    var formDoiMatKhau = document.getElementById('formDoiMatKhau');
    if (modalDoiMatKhau && formDoiMatKhau) {
        modalDoiMatKhau.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-doi-mat-khau')) return;
            var url = btn.getAttribute('data-url');
            if (url) formDoiMatKhau.action = url;
            formDoiMatKhau.reset();
        });
    }

    // Xóa nhân sự: mở modal Bootstrap xác nhận, sau đó submit form
    var modalXoaNs = document.getElementById('modalXacNhanXoaNhanSu');
    var btnXacNhanXoaNs = document.getElementById('btnXacNhanXoaNhanSu');
    var formIdCanXoa = null;
    if (modalXoaNs && btnXacNhanXoaNs) {
        modalXoaNs.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        document.querySelectorAll('.btn-xoa-nhan-su').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                var modal = bootstrap.Modal.getOrCreateInstance(modalXoaNs);
                modal.show();
            });
        });
        btnXacNhanXoaNs.addEventListener('click', function() {
            if (formIdCanXoa) {
                var form = document.getElementById(formIdCanXoa);
                if (form) form.submit();
            }
            var inst = bootstrap.Modal.getInstance(modalXoaNs);
            if (inst) inst.hide();
            formIdCanXoa = null;
        });
    }
});
</script>
@endpush
@endsection
