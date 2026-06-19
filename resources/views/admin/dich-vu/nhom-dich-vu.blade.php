@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\NhomDichVu::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'asc');
    $hasFilter = request()->filled('search')
        || request()->filled('trang_thai')
        || request()->filled('loai')
        || request()->filled('dich_vu_le_id')
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
        <form action="{{ route('admin.dich-vu.nhom-dich-vu') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Từ khoá</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Tên, mã, ghi chú, mô tả, thẻ...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="filter_trang_thai" name="trang_thai" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach(\App\Models\NhomDichVu::TRANG_THAI_LABELS as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('trang_thai') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_loai">Loại</label>
                    <select class="select2-admin form-select" id="filter_loai" name="loai" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach(\App\Support\LoaiCuoiPhongSu::LABELS as $value => $label)
                            <option value="{{ $value }}" @selected(request('loai') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_dich_vu_le_id">Dịch vụ</label>
                    <select class="select2-admin form-select" id="filter_dich_vu_le_id" name="dich_vu_le_id" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach($tatCaDichVuLe ?? [] as $dv)
                            <option value="{{ $dv->id }}" @selected((string) request('dich_vu_le_id') === (string) $dv->id)>
                                {{ $dv->ten_dich_vu ?? $dv->ma_dich_vu ?? 'Dịch vụ #' . $dv->id }}
                                @if($dv->ma_dich_vu)
                                    ({{ $dv->ma_dich_vu }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\NhomDichVu::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.dich-vu.nhom-dich-vu') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách nhóm dịch vụ</span>
            <span data-bs-toggle="tooltip" title="Thêm nhóm dịch vụ mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemNhomDichVu">
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
                        <th>Tên nhóm</th>
                        <th>Mã nhóm</th>
                        <th class="text-center" style="width: 90px;">Loại</th>
                        <th>Danh sách dịch vụ</th>
                        <th class="text-end" style="width: 120px;">Giá tiền</th>
                        <th class="text-end" style="width: 120px;">Giá gốc</th>
                        <th>Ghi chú</th>
                        <th>Mô tả</th>
                        <th class="text-center" style="width: 120px;">Hiển thị</th>
                        <th>Người tạo</th>
                        <th>Thẻ</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $dsDichVu = $item->dichVuLe->pluck('ten_dich_vu')->filter()->values();
                        $loaiLabel = \App\Support\LoaiCuoiPhongSu::label($item->loai ?? \App\Support\LoaiCuoiPhongSu::CUOI);
                        $dangHienThi = ($item->trang_thai ?? 0) == \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI;
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $item->ten_nhom ?? '—' }}</span></td>
                        <td>{{ $item->ma_nhom ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-label-primary">{{ $loaiLabel }}</span>
                        </td>
                        <td>
                            @if($dsDichVu->isNotEmpty())
                                {{ $dsDichVu->take(5)->implode(', ') }}{{ $dsDichVu->count() > 5 ? '...' : '' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">{{ $item->gia_tien !== null ? number_format($item->gia_tien, 0, ',', '.') . ' đ' : '—' }}</td>
                        <td class="text-end">{{ $item->gia_goc !== null ? number_format($item->gia_goc, 0, ',', '.') . ' đ' : '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->ghi_chu ?? '—', 40) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->mo_ta ?? '—', 50) }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center mb-0">
                                <input type="checkbox"
                                       class="form-check-input switch-trang-thai-nhom-dich-vu"
                                       role="switch"
                                       id="switch-trang-thai-nhom-dich-vu-{{ $item->id }}"
                                       data-url="{{ route('admin.dich-vu.update-nhom-dich-vu-trang-thai', $item) }}"
                                       @checked($dangHienThi)
                                       title="{{ $dangHienThi ? 'Hiển thị' : 'Ẩn' }}">
                                       {{-- <label class="form-check-label" for="switch-trang-thai-nhom-dich-vu-{{ $item->id }}">Hiển thị</label> --}}
                            </div>
                        </td>
                        <td>{{ $item->nguoiTao?->name ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->the ?? '—', 50) }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-sua-nhom-dich-vu"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaNhomDichVu"
                                       data-url="{{ route('admin.dich-vu.update-nhom-dich-vu', $item) }}"
                                       data-ten="{{ e($item->ten_nhom ?? '') }}"
                                       data-ma="{{ e($item->ma_nhom ?? '') }}"
                                       data-gia-tien="{{ $item->gia_tien ?? '' }}"
                                       data-the="{{ e($item->the ?? '') }}"
                                       data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                       data-mo-ta="{{ e($item->mo_ta ?? '') }}"
                                       data-loai="{{ e($item->loai ?? \App\Support\LoaiCuoiPhongSu::CUOI) }}"
                                       data-dich-vu-le-ids="{{ $item->dichVuLe->pluck('id')->implode(',') }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-nhom-{{ $item->id }}" action="{{ route('admin.dich-vu.destroy-nhom-dich-vu', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-nhom-dich-vu" data-form-id="form-xoa-nhom-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-4 text-muted">Chưa có dữ liệu nhóm dịch vụ.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="nhóm dịch vụ" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới nhóm dịch vụ --}}
<div class="modal fade" id="modalThemNhomDichVu" tabindex="-1" aria-labelledby="modalThemNhomDichVuLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-nhom-dich-vu">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemNhomDichVuLabel">Thêm nhóm dịch vụ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.dich-vu.store-nhom-dich-vu') }}" method="POST" id="formThemNhomDichVu">
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
                        {{-- 4 input trên 1 hàng (lg), giảm dần: md 3 cột, sm 2 cột, xs 1 cột --}}
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_ten_nhom">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ten_nhom" name="ten_nhom" value="{{ old('ten_nhom') }}" placeholder="Nhập tên nhóm" required>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_ma_nhom">Mã nhóm</label>
                            <input type="text" class="form-control" id="them_ma_nhom" name="ma_nhom" value="{{ old('ma_nhom') }}" placeholder="Ví dụ: NDV001">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_gia_tien">Giá tiền</label>
                            <input type="number" class="form-control" id="them_gia_tien" name="gia_tien" value="{{ old('gia_tien') }}" placeholder="0" min="0" step="1000">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_loai">Loại dịch vụ <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select" id="them_loai" name="loai" required data-placeholder="Chọn loại">
                                <option value="" @selected(old('loai') === null || old('loai') === '')>-- Chọn loại --</option>
                                @foreach(\App\Support\LoaiCuoiPhongSu::LABELS as $value => $label)
                                    <option value="{{ $value }}" @selected(old('loai') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_the">Thẻ</label>
                            <input type="text" class="form-control" id="them_the" name="the" value="{{ old('the') }}" placeholder="Từ khóa, thẻ tìm kiếm (cách nhau bởi dấu phẩy)">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Danh sách dịch vụ lẻ</label>
                            <p class="text-muted small mb-2">Chọn loại dịch vụ trước, sau đó chọn các dịch vụ lẻ thuộc nhóm này (có thể chọn nhiều).</p>
                            <div class="border rounded p-3 bg-light" id="them_dich_vu_le_container" style="max-height: 240px; overflow-y: auto;">
                                <p class="text-muted small mb-0" id="them_dich_vu_le_placeholder">Vui lòng chọn loại dịch vụ để xem danh sách dịch vụ lẻ.</p>
                                <div id="them_dich_vu_le_list"></div>
                            </div>
                            <div class="mt-2 pt-2 border-top border-light">
                                <strong>Tổng tiền dịch vụ đã chọn:</strong> <span id="them_tong_tien_dv" class="text-primary fw-semibold">0 đ</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_mo_ta">Mô tả</label>
                            <textarea class="form-control" id="them_mo_ta" name="mo_ta" rows="2" placeholder="Mô tả ngắn...">{{ old('mo_ta') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="them_ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú...">{{ old('ghi_chu') }}</textarea>
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

{{-- Modal Chỉnh sửa nhóm dịch vụ --}}
<div class="modal fade" id="modalSuaNhomDichVu" tabindex="-1" aria-labelledby="modalSuaNhomDichVuLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-nhom-dich-vu">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaNhomDichVuLabel">Chỉnh sửa nhóm dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaNhomDichVu" method="POST" action="">
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
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_ten_nhom">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ten_nhom" name="ten_nhom" placeholder="Nhập tên nhóm" required>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_ma_nhom">Mã nhóm</label>
                            <input type="text" class="form-control" id="sua_ma_nhom" name="ma_nhom" placeholder="Ví dụ: NDV001">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_gia_tien">Giá tiền</label>
                            <input type="number" class="form-control" id="sua_gia_tien" name="gia_tien" placeholder="0" min="0" step="1000">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_loai">Loại dịch vụ <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select" id="sua_loai" name="loai" required data-placeholder="Chọn loại">
                                <option value="">-- Chọn loại --</option>
                                @foreach(\App\Support\LoaiCuoiPhongSu::LABELS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_the">Thẻ</label>
                            <input type="text" class="form-control" id="sua_the" name="the" placeholder="Từ khóa, thẻ tìm kiếm (cách nhau bởi dấu phẩy)">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Danh sách dịch vụ lẻ</label>
                            <p class="text-muted small mb-2">Chọn loại dịch vụ trước, sau đó chọn các dịch vụ lẻ thuộc nhóm này (có thể chọn nhiều).</p>
                            <div class="border rounded p-3 bg-light" id="sua_dich_vu_le_container" style="max-height: 240px; overflow-y: auto;">
                                <p class="text-muted small mb-0" id="sua_dich_vu_le_placeholder">Vui lòng chọn loại dịch vụ để xem danh sách dịch vụ lẻ.</p>
                                <div id="sua_dich_vu_le_list"></div>
                            </div>
                            <div class="mt-2 pt-2 border-top border-light">
                                <strong>Tổng tiền dịch vụ đã chọn:</strong> <span id="sua_tong_tien_dv" class="text-primary fw-semibold">0 đ</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_mo_ta">Mô tả</label>
                            <textarea class="form-control" id="sua_mo_ta" name="mo_ta" rows="2" placeholder="Mô tả ngắn..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="sua_ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú..."></textarea>
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

{{-- Modal xác nhận xóa nhóm dịch vụ --}}
<div class="modal fade" id="modalXacNhanXoaNhomDichVu" tabindex="-1" aria-labelledby="modalXacNhanXoaNhomDichVuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaNhomDichVuLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa nhóm dịch vụ này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaNhomDichVu">
                    <i class="fa-solid fa-trash me-1"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
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
    min-width: 1000px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
#modalThemNhomDichVu .modal-nhom-dich-vu,
#modalSuaNhomDichVu .modal-nhom-dich-vu {
    max-width: 90vw;
    width: 1140px;
}
#modalXacNhanXoaNhomDichVu .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    var DICH_VU_LE_THEO_LOAI_URL = @json(route('admin.dich-vu.list-dich-vu-le-theo-loai'));
    var CSRF_TOKEN = @json(csrf_token());
    var TRANG_THAI_HIEN_THI = {{ \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI }};
    var oldDichVuLeIds = @json(array_map('intval', old('dich_vu_le_ids', [])));

    function capNhatTrangThaiNhomDichVu(switchEl) {
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
            })
            .catch(function() {
                switchEl.checked = trangThaiCu === TRANG_THAI_HIEN_THI;
            })
            .finally(function() {
                switchEl.disabled = false;
            });
    }

    document.querySelectorAll('.switch-trang-thai-nhom-dich-vu').forEach(function(switchEl) {
        switchEl.addEventListener('change', function() {
            capNhatTrangThaiNhomDichVu(this);
        });
    });

    function formatTien(num) {
        return Number(num).toLocaleString('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' đ';
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function capNhatTongTienThem() {
        var total = 0;
        document.querySelectorAll('.them-dich-vu-le-cb:checked').forEach(function(cb) {
            total += parseFloat(cb.getAttribute('data-price') || 0) || 0;
        });
        var el = document.getElementById('them_tong_tien_dv');
        if (el) el.textContent = formatTien(total);
    }

    function capNhatTongTienSua() {
        var total = 0;
        document.querySelectorAll('.sua-dich-vu-le-cb:checked').forEach(function(cb) {
            total += parseFloat(cb.getAttribute('data-price') || 0) || 0;
        });
        var el = document.getElementById('sua_tong_tien_dv');
        if (el) el.textContent = formatTien(total);
    }

    var themDvLeConfig = {
        listId: 'them_dich_vu_le_list',
        placeholderId: 'them_dich_vu_le_placeholder',
        rowClass: 'them-dich-vu-le-row',
        cbClass: 'them-dich-vu-le-cb',
        idPrefix: 'them_dv_',
        onTotalUpdate: capNhatTongTienThem,
        emptyLoaiMessage: 'Vui lòng chọn loại dịch vụ để xem danh sách dịch vụ lẻ.',
    };

    var suaDvLeConfig = {
        listId: 'sua_dich_vu_le_list',
        placeholderId: 'sua_dich_vu_le_placeholder',
        rowClass: 'sua-dich-vu-le-row',
        cbClass: 'sua-dich-vu-le-cb',
        idPrefix: 'sua_dv_',
        onTotalUpdate: capNhatTongTienSua,
        emptyLoaiMessage: 'Vui lòng chọn loại dịch vụ để xem danh sách dịch vụ lẻ.',
    };

    function renderDichVuLeList(items, selectedIds, config) {
        var listEl = document.getElementById(config.listId);
        var placeholderEl = document.getElementById(config.placeholderId);
        if (!listEl) return;

        selectedIds = selectedIds || [];
        listEl.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {
            if (placeholderEl) {
                placeholderEl.textContent = 'Chưa có dịch vụ lẻ cho loại này. Vui lòng thêm dịch vụ lẻ trước.';
                placeholderEl.classList.remove('d-none');
            }
            config.onTotalUpdate();
            return;
        }

        if (placeholderEl) placeholderEl.classList.add('d-none');

        items.forEach(function(dv) {
            var giaDv = parseFloat(dv.gia_dich_vu || 0) || 0;
            var ten = dv.ten_dich_vu || dv.ma_dich_vu || ('Dịch vụ #' + dv.id);
            var maHtml = dv.ma_dich_vu
                ? ' <span class="text-muted small">(' + escapeHtml(dv.ma_dich_vu) + ')</span>'
                : '';
            var giaText = giaDv > 0 ? formatTien(giaDv) : '—';
            var checked = selectedIds.indexOf(Number(dv.id)) !== -1 ? ' checked' : '';

            var row = document.createElement('div');
            row.className = 'form-check d-flex align-items-center justify-content-between py-1 ' + config.rowClass;
            row.setAttribute('data-loai', dv.loai || '');
            row.innerHTML =
                '<div class="d-flex align-items-center flex-grow-1">' +
                    '<input class="form-check-input me-2 ' + config.cbClass + '" type="checkbox" name="dich_vu_le_ids[]" value="' + dv.id + '" id="' + config.idPrefix + dv.id + '" data-price="' + giaDv + '"' + checked + '>' +
                    '<label class="form-check-label mb-0" for="' + config.idPrefix + dv.id + '">' + escapeHtml(ten) + maHtml + '</label>' +
                '</div>' +
                '<span class="text-end text-nowrap ms-2 fw-medium">' + giaText + '</span>';

            var cb = row.querySelector('.' + config.cbClass);
            if (cb) cb.addEventListener('change', config.onTotalUpdate);
            listEl.appendChild(row);
        });

        config.onTotalUpdate();
    }

    function taiDichVuLeTheoLoai(loai, selectedIds, config) {
        var listEl = document.getElementById(config.listId);
        var placeholderEl = document.getElementById(config.placeholderId);
        if (!listEl) return;

        if (!loai) {
            listEl.innerHTML = '';
            if (placeholderEl) {
                placeholderEl.textContent = config.emptyLoaiMessage;
                placeholderEl.classList.remove('d-none');
            }
            config.onTotalUpdate();
            return;
        }

        if (placeholderEl) {
            placeholderEl.textContent = 'Đang tải danh sách dịch vụ lẻ...';
            placeholderEl.classList.remove('d-none');
        }
        listEl.innerHTML = '';

        RestApi.get(DICH_VU_LE_THEO_LOAI_URL, { loai: loai })
            .then(function(res) {
                if (!res.ok) throw new Error('fetch_failed');
                renderDichVuLeList((res.data && res.data.items) ? res.data.items : [], selectedIds || [], config);
            })
            .catch(function() {
                listEl.innerHTML = '';
                if (placeholderEl) {
                    placeholderEl.textContent = 'Không thể tải danh sách dịch vụ lẻ. Vui lòng thử lại.';
                    placeholderEl.classList.remove('d-none');
                }
                config.onTotalUpdate();
            });
    }

    function taiThemDichVuLeTheoLoai(loai, selectedIds) {
        taiDichVuLeTheoLoai(loai, selectedIds, themDvLeConfig);
    }

    function taiSuaDichVuLeTheoLoai(loai, selectedIds) {
        taiDichVuLeTheoLoai(loai, selectedIds, suaDvLeConfig);
    }

    function ganGiaTriSelect2(selectEl, value) {
        if (!selectEl) return;
        if (window.jQuery && jQuery(selectEl).data('select2')) {
            jQuery(selectEl).val(value || null).trigger('change.select2');
        } else {
            selectEl.value = value || '';
        }
    }

    var themLoaiSelect = document.getElementById('them_loai');
    if (themLoaiSelect) {
        var onThemLoaiChange = function() {
            taiThemDichVuLeTheoLoai(themLoaiSelect.value || '', []);
        };
        if (window.jQuery) {
            jQuery(themLoaiSelect).on('change', onThemLoaiChange);
        } else {
            themLoaiSelect.addEventListener('change', onThemLoaiChange);
        }
        if (themLoaiSelect.value) {
            taiThemDichVuLeTheoLoai(themLoaiSelect.value, oldDichVuLeIds);
        }
    }

    var suaLoaiSelect = document.getElementById('sua_loai');
    var skipSuaLoaiChange = false;
    if (suaLoaiSelect) {
        var onSuaLoaiChange = function() {
            if (skipSuaLoaiChange) return;
            taiSuaDichVuLeTheoLoai(suaLoaiSelect.value || '', []);
        };
        if (window.jQuery) {
            jQuery(suaLoaiSelect).on('change', onSuaLoaiChange);
        } else {
            suaLoaiSelect.addEventListener('change', onSuaLoaiChange);
        }
    }

    var modalThem = document.getElementById('modalThemNhomDichVu');
    if (modalThem) {
        modalThem.addEventListener('show.bs.modal', function() {
            @if(!$errors->any())
            if (themLoaiSelect) {
                if (window.jQuery && jQuery(themLoaiSelect).data('select2')) {
                    jQuery(themLoaiSelect).val(null).trigger('change');
                } else {
                    themLoaiSelect.value = '';
                    taiThemDichVuLeTheoLoai('', []);
                }
            }
            @else
            capNhatTongTienThem();
            @endif
        });
        @if($errors->any())
        var m = new bootstrap.Modal(modalThem);
        m.show();
        @endif
    }

    // Modal Sửa: gán data vào form
    var modalSua = document.getElementById('modalSuaNhomDichVu');
    var formSua = document.getElementById('formSuaNhomDichVu');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-nhom-dich-vu')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ten_nhom').value = btn.getAttribute('data-ten') || '';
            document.getElementById('sua_ma_nhom').value = btn.getAttribute('data-ma') || '';
            document.getElementById('sua_gia_tien').value = btn.getAttribute('data-gia-tien') || '';
            document.getElementById('sua_the').value = btn.getAttribute('data-the') || '';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
            document.getElementById('sua_mo_ta').value = btn.getAttribute('data-mo-ta') || '';
            var loai = btn.getAttribute('data-loai') || '';
            var idsStr = btn.getAttribute('data-dich-vu-le-ids') || '';
            var ids = idsStr ? idsStr.split(',').map(function(s) { return parseInt(s.trim(), 10); }).filter(function(n) { return !isNaN(n); }) : [];
            skipSuaLoaiChange = true;
            ganGiaTriSelect2(suaLoaiSelect, loai);
            skipSuaLoaiChange = false;
            taiSuaDichVuLeTheoLoai(loai, ids);
        });
    }

    // Xóa: mở modal Bootstrap xác nhận, sau đó submit form
    var modalXoa = document.getElementById('modalXacNhanXoaNhomDichVu');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaNhomDichVu');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        document.querySelectorAll('.btn-xoa-nhom-dich-vu').forEach(function(btn) {
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
