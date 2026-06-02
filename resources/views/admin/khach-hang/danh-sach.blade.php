@extends('admin.layouts.app')

@section('content')
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
        <form action="{{ route('admin.khach-hang.hop-dong') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên hoặc email/SĐT</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập để tìm kiếm...">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.khach-hang.hop-dong') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách khách hàng</span>
            <span data-bs-toggle="tooltip" title="Thêm khách hàng mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemKhachHang">
                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                </button>
            </span>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Họ tên chú rể</th>
                        {{-- <th>Ngày sinh CR</th>
                        <th>Giới tính CR</th> --}}
                        <th>Email/SĐT chú rể</th>
                        {{-- <th>Địa chỉ chú rể</th> --}}
                        <th>Họ tên cô dâu</th>
                        {{-- <th>Ngày sinh CD</th>
                        <th>Giới tính CD</th> --}}
                        {{-- <th>Địa chỉ cô dâu</th> --}}
                        <th>Email/SĐT cô dâu</th>
                        <th>Nguồn khách</th>
                        <th>Ghi chú</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $item->ho_ten_chu_re ?? '—' }}</span></td>
                        {{-- <td>{{ $item->ngay_sinh_chu_re ? $item->ngay_sinh_chu_re->format('d/m/Y') : '—' }}</td>
                        <td>{{ $item->gioi_tinh_chu_re ?? '—' }}</td> --}}
                        <td>{{ $item->email_hoac_sdt_chu_re ?? '—' }}</td>
                        {{-- <td>{{ $item->dia_chi_chu_re ? str($item->dia_chi_chu_re)->limit(30) : '—' }}</td> --}}
                        <td><span class="fw-medium">{{ $item->ho_ten_co_dau ?? '—' }}</span></td>
                        {{-- <td>{{ $item->ngay_sinh_co_dau ? $item->ngay_sinh_co_dau->format('d/m/Y') : '—' }}</td>
                        <td>{{ $item->gioi_tinh_co_dau ?? '—' }}</td> --}}
                        {{-- <td>{{ $item->dia_chi_co_dau ? str($item->dia_chi_co_dau)->limit(30) : '—' }}</td> --}}
                        <td>{{ $item->email_hoac_sdt_co_dau ?? '—' }}</td>
                        <td>{{ $item->nguon_khach ? str($item->nguon_khach)->limit(40) : '—' }}</td>
                        <td>{{ $item->ghi_chu ? str($item->ghi_chu)->limit(30) : '—' }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-sua-khach-hang"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaKhachHang"
                                       data-url="{{ route('admin.khach-hang.update', $item) }}"
                                       data-ho-ten-chu-re="{{ e($item->ho_ten_chu_re ?? '') }}"
                                       {{-- data-ngay-sinh-chu-re="{{ $item->ngay_sinh_chu_re?->format('Y-m-d') ?? '' }}"
                                       data-gioi-tinh-chu-re="{{ e($item->gioi_tinh_chu_re ?? '') }}" --}}
                                       data-email-sdt-chu-re="{{ e($item->email_hoac_sdt_chu_re ?? '') }}"
                                       {{-- data-dia-chi-chu-re="{{ e($item->dia_chi_chu_re ?? '') }}" --}}
                                       data-ho-ten-co-dau="{{ e($item->ho_ten_co_dau ?? '') }}"
                                       {{-- data-ngay-sinh-co-dau="{{ $item->ngay_sinh_co_dau?->format('Y-m-d') ?? '' }}"
                                       data-gioi-tinh-co-dau="{{ e($item->gioi_tinh_co_dau ?? '') }}" --}}
                                       {{-- data-dia-chi-co-dau="{{ e($item->dia_chi_co_dau ?? '') }}" --}}
                                       data-email-sdt-co-dau="{{ e($item->email_hoac_sdt_co_dau ?? '') }}"
                                       data-nguon-khach="{{ e($item->nguon_khach ?? '') }}"
                                       data-ghi-chu="{{ e($item->ghi_chu ?? '') }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-{{ $item->id }}" action="{{ route('admin.khach-hang.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-khach-hang" data-form-id="form-xoa-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có dữ liệu khách hàng.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới khách hàng --}}
<div class="modal fade" id="modalThemKhachHang" tabindex="-1" aria-labelledby="modalThemKhachHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-khach-hang">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemKhachHangLabel">Thêm khách hàng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.khach-hang.store') }}" method="POST" id="formThemKhachHang">
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
                    <h6 class="text-primary mb-2">Thông tin chú rể</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="them_ho_ten_chu_re">Họ tên chú rể</label>
                            <input type="text" class="form-control" id="them_ho_ten_chu_re" name="ho_ten_chu_re" value="{{ old('ho_ten_chu_re') }}" placeholder="Nhập họ tên">
                        </div>
                        {{-- <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_ngay_sinh_chu_re">Ngày sinh</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_sinh_chu_re" name="ngay_sinh_chu_re" value="{{ old('ngay_sinh_chu_re') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_gioi_tinh_chu_re">Giới tính</label>
                            <select class="select2-admin form-select" id="them_gioi_tinh_chu_re" name="gioi_tinh_chu_re" data-placeholder="Chọn giới tính">
                                <option value="">-- Chọn --</option>
                                <option value="nam" {{ old('gioi_tinh_chu_re') == 'nam' ? 'selected' : '' }}>Nam</option>
                                <option value="nu" {{ old('gioi_tinh_chu_re') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                <option value="khac" {{ old('gioi_tinh_chu_re') == 'khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div> --}}
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="them_email_sdt_chu_re">Email hoặc SĐT</label>
                            <input type="text" class="form-control" id="them_email_sdt_chu_re" name="email_hoac_sdt_chu_re" value="{{ old('email_hoac_sdt_chu_re') }}" placeholder="Email hoặc số điện thoại">
                        </div>
                        {{-- <div class="col-12">
                            <label class="form-label" for="them_dia_chi_chu_re">Địa chỉ chú rể</label>
                            <textarea class="form-control" id="them_dia_chi_chu_re" name="dia_chi_chu_re" rows="2" placeholder="Địa chỉ">{{ old('dia_chi_chu_re') }}</textarea>
                        </div> --}}
                    </div>
                    <h6 class="text-primary mb-2">Thông tin cô dâu</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="them_ho_ten_co_dau">Họ tên cô dâu</label>
                            <input type="text" class="form-control" id="them_ho_ten_co_dau" name="ho_ten_co_dau" value="{{ old('ho_ten_co_dau') }}" placeholder="Nhập họ tên">
                        </div>
                        {{-- <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_ngay_sinh_co_dau">Ngày sinh</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_sinh_co_dau" name="ngay_sinh_co_dau" value="{{ old('ngay_sinh_co_dau') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_gioi_tinh_co_dau">Giới tính</label>
                            <select class="select2-admin form-select" id="them_gioi_tinh_co_dau" name="gioi_tinh_co_dau" data-placeholder="Chọn giới tính">
                                <option value="">-- Chọn --</option>
                                <option value="nam" {{ old('gioi_tinh_co_dau') == 'nam' ? 'selected' : '' }}>Nam</option>
                                <option value="nu" {{ old('gioi_tinh_co_dau') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                <option value="khac" {{ old('gioi_tinh_co_dau') == 'khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div> --}}
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="them_email_sdt_co_dau">Email hoặc SĐT</label>
                            <input type="text" class="form-control" id="them_email_sdt_co_dau" name="email_hoac_sdt_co_dau" value="{{ old('email_hoac_sdt_co_dau') }}" placeholder="Email hoặc số điện thoại">
                        </div>
                        {{-- <div class="col-12">
                            <label class="form-label" for="them_dia_chi_co_dau">Địa chỉ cô dâu</label>
                            <textarea class="form-control" id="them_dia_chi_co_dau" name="dia_chi_co_dau" rows="2" placeholder="Địa chỉ">{{ old('dia_chi_co_dau') }}</textarea>
                        </div> --}}
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="them_nguon_khach">Nguồn khách</label>
                        <select class="select2-admin form-select" id="them_nguon_khach" name="nguon_khach" data-placeholder="Chọn nguồn khách">
                            <option value="">-- Chọn --</option>
                            <option value="Insta" {{ old('nguon_khach') == 'Insta' ? 'selected' : '' }}>Insta</option>
                            <option value="Threads" {{ old('nguon_khach') == 'Threads' ? 'selected' : '' }}>Threads</option>
                            <option value="FB" {{ old('nguon_khach') == 'FB' ? 'selected' : '' }}>FB</option>
                            <option value="Tiktok" {{ old('nguon_khach') == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                            <option value="Google" {{ old('nguon_khach') == 'Google' ? 'selected' : '' }}>Google</option>
                            <option value="Bạn giới thiệu" {{ old('nguon_khach') == 'Bạn giới thiệu' ? 'selected' : '' }}>Bạn giới thiệu</option>
                            <option value="Đi qua thì thấy" {{ old('nguon_khach') == 'Đi qua thì thấy' ? 'selected' : '' }}>Đi qua thì thấy</option>
                            <option value="Youtube" {{ old('nguon_khach') == 'Youtube' ? 'selected' : '' }}>Youtube</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                        <textarea class="form-control" id="them_ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú">{{ old('ghi_chu') }}</textarea>
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

{{-- Modal Chỉnh sửa khách hàng --}}
<div class="modal fade" id="modalSuaKhachHang" tabindex="-1" aria-labelledby="modalSuaKhachHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-khach-hang">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaKhachHangLabel">Chỉnh sửa khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaKhachHang" method="POST" action="">
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
                    <h6 class="text-primary mb-2">Thông tin chú rể</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="sua_ho_ten_chu_re">Họ tên chú rể</label>
                            <input type="text" class="form-control" id="sua_ho_ten_chu_re" name="ho_ten_chu_re" placeholder="Nhập họ tên">
                        </div>
                        {{-- <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="sua_ngay_sinh_chu_re">Ngày sinh</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_sinh_chu_re" name="ngay_sinh_chu_re" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="sua_gioi_tinh_chu_re">Giới tính</label>
                            <select class="select2-admin form-select" id="sua_gioi_tinh_chu_re" name="gioi_tinh_chu_re" data-placeholder="Chọn giới tính">
                                <option value="">-- Chọn --</option>
                                <option value="nam">Nam</option>
                                <option value="nu">Nữ</option>
                                <option value="khac">Khác</option>
                            </select>
                        </div> --}}
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="sua_email_sdt_chu_re">Email hoặc SĐT</label>
                            <input type="text" class="form-control" id="sua_email_sdt_chu_re" name="email_hoac_sdt_chu_re" placeholder="Email hoặc số điện thoại">
                        </div>
                        {{-- <div class="col-12">
                            <label class="form-label" for="sua_dia_chi_chu_re">Địa chỉ chú rể</label>
                            <textarea class="form-control" id="sua_dia_chi_chu_re" name="dia_chi_chu_re" rows="2" placeholder="Địa chỉ"></textarea>
                        </div> --}}
                    </div>
                    <h6 class="text-primary mb-2">Thông tin cô dâu</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="sua_ho_ten_co_dau">Họ tên cô dâu</label>
                            <input type="text" class="form-control" id="sua_ho_ten_co_dau" name="ho_ten_co_dau" placeholder="Nhập họ tên">
                        </div>
                        {{-- <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="sua_ngay_sinh_co_dau">Ngày sinh</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_sinh_co_dau" name="ngay_sinh_co_dau" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="sua_gioi_tinh_co_dau">Giới tính</label>
                            <select class="select2-admin form-select" id="sua_gioi_tinh_co_dau" name="gioi_tinh_co_dau" data-placeholder="Chọn giới tính">
                                <option value="">-- Chọn --</option>
                                <option value="nam">Nam</option>
                                <option value="nu">Nữ</option>
                                <option value="khac">Khác</option>
                            </select>
                        </div> --}}
                        <div class="col-12 col-sm-6 col-lg-6">
                            <label class="form-label" for="sua_email_sdt_co_dau">Email hoặc SĐT</label>
                            <input type="text" class="form-control" id="sua_email_sdt_co_dau" name="email_hoac_sdt_co_dau" placeholder="Email hoặc số điện thoại">
                        </div>
                        {{-- <div class="col-12">
                            <label class="form-label" for="sua_dia_chi_co_dau">Địa chỉ cô dâu</label>
                            <textarea class="form-control" id="sua_dia_chi_co_dau" name="dia_chi_co_dau" rows="2" placeholder="Địa chỉ"></textarea>
                        </div> --}}
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="sua_nguon_khach">Nguồn khách</label>
                        <select class="select2-admin form-select" id="sua_nguon_khach" name="nguon_khach" data-placeholder="Chọn nguồn khách">
                            <option value="">-- Chọn --</option>
                            <option value="Insta">Insta</option>
                            <option value="Threads">Threads</option>
                            <option value="FB">FB</option>
                            <option value="Tiktok">Tiktok</option>
                            <option value="Google">Google</option>
                            <option value="Bạn giới thiệu">Bạn giới thiệu</option>
                            <option value="Đi qua thì thấy">Đi qua thì thấy</option>
                            <option value="Youtube">Youtube</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                        <textarea class="form-control" id="sua_ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú"></textarea>
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

{{-- Modal xác nhận xóa khách hàng --}}
<div class="modal fade" id="modalXacNhanXoaKhachHang" tabindex="-1" aria-labelledby="modalXacNhanXoaKhachHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaKhachHangLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa khách hàng này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaKhachHang">
                    <i class="fa-solid fa-trash me-1"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<style>
#modalThemKhachHang .modal-khach-hang,
#modalSuaKhachHang .modal-khach-hang {
    max-width: 90vw;
    width: 900px;
}
@media (min-width: 992px) {
    #modalThemKhachHang .modal-khach-hang,
    #modalSuaKhachHang .modal-khach-hang {
        max-width: 900px;
    }
}
#modalXacNhanXoaKhachHang .modal-confirm-xoa {
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
    min-width: 900px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // Mở modal Thêm khi có lỗi validation (sau redirect)
    @if($errors->any())
    var modalThem = document.getElementById('modalThemKhachHang');
    if (modalThem) {
        var modal = new bootstrap.Modal(modalThem);
        modal.show();
    }
    @endif

    // --- Modal Chỉnh sửa khách hàng ---
    var modalSua = document.getElementById('modalSuaKhachHang');
    var formSua = document.getElementById('formSuaKhachHang');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-khach-hang')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ho_ten_chu_re').value = btn.getAttribute('data-ho-ten-chu-re') || '';
            // if (window.setAdminDateInput) setAdminDateInput('sua_ngay_sinh_chu_re', btn.getAttribute('data-ngay-sinh-chu-re') || ''); else document.getElementById('sua_ngay_sinh_chu_re').value = btn.getAttribute('data-ngay-sinh-chu-re') || '';
            // document.getElementById('sua_gioi_tinh_chu_re').value = btn.getAttribute('data-gioi-tinh-chu-re') || '';
            document.getElementById('sua_email_sdt_chu_re').value = btn.getAttribute('data-email-sdt-chu-re') || '';
            // document.getElementById('sua_dia_chi_chu_re').value = btn.getAttribute('data-dia-chi-chu-re') || '';
            document.getElementById('sua_ho_ten_co_dau').value = btn.getAttribute('data-ho-ten-co-dau') || '';
            // if (window.setAdminDateInput) setAdminDateInput('sua_ngay_sinh_co_dau', btn.getAttribute('data-ngay-sinh-co-dau') || ''); else document.getElementById('sua_ngay_sinh_co_dau').value = btn.getAttribute('data-ngay-sinh-co-dau') || '';
            // document.getElementById('sua_gioi_tinh_co_dau').value = btn.getAttribute('data-gioi-tinh-co-dau') || '';
            // document.getElementById('sua_dia_chi_co_dau').value = btn.getAttribute('data-dia-chi-co-dau') || '';
            document.getElementById('sua_email_sdt_co_dau').value = btn.getAttribute('data-email-sdt-co-dau') || '';
            // Set value cho select Nguồn khách (kể cả khi giá trị cũ không có sẵn trong option)
            var nguonKhach = btn.getAttribute('data-nguon-khach') || '';
            var suaNguonSelect = document.getElementById('sua_nguon_khach');
            if (suaNguonSelect) {
                if (nguonKhach) {
                    var exists = Array.from(suaNguonSelect.options).some(function(opt) { return opt.value === nguonKhach; });
                    if (!exists) {
                        var opt = document.createElement('option');
                        opt.value = nguonKhach;
                        opt.textContent = nguonKhach;
                        suaNguonSelect.appendChild(opt);
                    }
                    suaNguonSelect.value = nguonKhach;
                } else {
                    suaNguonSelect.value = '';
                }

                var $ = window.jQuery || window.$;
                if ($ && $.fn && $.fn.select2 && $(suaNguonSelect).data('select2')) {
                    $(suaNguonSelect).trigger('change');
                }
            }
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
        });
    }

    // Xóa khách hàng: mở modal xác nhận, sau đó submit form
    var modalXoa = document.getElementById('modalXacNhanXoaKhachHang');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaKhachHang');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        document.querySelectorAll('.btn-xoa-khach-hang').forEach(function(btn) {
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
});
</script>
@endpush
@endsection
