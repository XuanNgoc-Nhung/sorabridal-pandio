@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\NhomDichVu::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'asc');
    $hasFilter = request()->filled('search')
        || request()->filled('trang_thai')
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
                        <th>Danh sách dịch vụ</th>
                        <th class="text-end" style="width: 120px;">Giá tiền</th>
                        <th class="text-end" style="width: 120px;">Giá gốc</th>
                        <th>Ghi chú</th>
                        <th>Mô tả</th>
                        <th class="text-center" style="width: 100px;">Trạng thái</th>
                        <th>Người tạo</th>
                        <th>Thẻ</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $trangThaiLabel = \App\Models\NhomDichVu::TRANG_THAI_LABELS[(int)($item->trang_thai ?? 0)] ?? '—';
                        $trangThaiBadge = ($item->trang_thai ?? 0) == \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI ? 'bg-label-success' : 'bg-label-secondary';
                        $dsDichVu = $item->dichVuLe->pluck('ten_dich_vu')->filter()->values();
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $item->ten_nhom ?? '—' }}</span></td>
                        <td>{{ $item->ma_nhom ?? '—' }}</td>
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
                            <span class="badge {{ $trangThaiBadge }}">{{ $trangThaiLabel }}</span>
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
                                       data-trang-thai="{{ (int)($item->trang_thai ?? 0) }}"
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
                        <td colspan="12" class="text-center py-4 text-muted">Chưa có dữ liệu nhóm dịch vụ.</td>
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
                            <label class="form-label" for="them_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select" id="them_trang_thai" name="trang_thai" data-placeholder="Chọn trạng thái">
                                <option value="{{ \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI }}" {{ old('trang_thai', \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI) == \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI ? 'selected' : '' }}>Hiển thị</option>
                                <option value="{{ \App\Models\NhomDichVu::TRANG_THAI_AN }}" {{ old('trang_thai') == \App\Models\NhomDichVu::TRANG_THAI_AN ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_the">Thẻ</label>
                            <input type="text" class="form-control" id="them_the" name="the" value="{{ old('the') }}" placeholder="Từ khóa, thẻ tìm kiếm (cách nhau bởi dấu phẩy)">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Danh sách dịch vụ lẻ</label>
                            <p class="text-muted small mb-2">Chọn các dịch vụ lẻ thuộc nhóm này (có thể chọn nhiều).</p>
                            <div class="border rounded p-3 bg-light" style="max-height: 240px; overflow-y: auto;">
                                @forelse($tatCaDichVuLe ?? [] as $dv)
                                @php $giaDv = (float)($dv->gia_dich_vu ?? 0); @endphp
                                <div class="form-check d-flex align-items-center justify-content-between py-1 them-dich-vu-le-row">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <input class="form-check-input me-2 them-dich-vu-le-cb"
                                               type="checkbox"
                                               name="dich_vu_le_ids[]"
                                               value="{{ $dv->id }}"
                                               id="them_dv_{{ $dv->id }}"
                                               data-price="{{ $giaDv }}"
                                               {{ in_array($dv->id, old('dich_vu_le_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label mb-0" for="them_dv_{{ $dv->id }}">
                                            {{ $dv->ten_dich_vu ?? $dv->ma_dich_vu ?? 'Dịch vụ #' . $dv->id }}
                                            @if($dv->ma_dich_vu)
                                                <span class="text-muted small">({{ $dv->ma_dich_vu }})</span>
                                            @endif
                                        </label>
                                    </div>
                                    <span class="text-end text-nowrap ms-2 fw-medium">{{ $giaDv > 0 ? number_format($giaDv, 0, ',', '.') . ' đ' : '—' }}</span>
                                </div>
                                @empty
                                <p class="text-muted small mb-0">Chưa có dịch vụ lẻ. Vui lòng thêm dịch vụ lẻ trước.</p>
                                @endforelse
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-nhom-dich-vu">
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_ten_nhom">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ten_nhom" name="ten_nhom" placeholder="Nhập tên nhóm" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_ma_nhom">Mã nhóm</label>
                            <input type="text" class="form-control" id="sua_ma_nhom" name="ma_nhom" placeholder="Ví dụ: NDV001">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_gia_tien">Giá tiền</label>
                            <input type="number" class="form-control" id="sua_gia_tien" name="gia_tien" placeholder="0" min="0" step="1000">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select" id="sua_trang_thai" name="trang_thai" data-placeholder="Chọn trạng thái">
                                <option value="{{ \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI }}">Hiển thị</option>
                                <option value="{{ \App\Models\NhomDichVu::TRANG_THAI_AN }}">Ẩn</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_the">Thẻ</label>
                            <input type="text" class="form-control" id="sua_the" name="the" placeholder="Từ khóa, thẻ tìm kiếm (cách nhau bởi dấu phẩy)">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Danh sách dịch vụ lẻ</label>
                            <p class="text-muted small mb-2">Chọn các dịch vụ lẻ thuộc nhóm này (có thể chọn nhiều).</p>
                            <div class="border rounded p-3 bg-light" style="max-height: 240px; overflow-y: auto;">
                                @forelse($tatCaDichVuLe ?? [] as $dv)
                                @php $giaDv = (float)($dv->gia_dich_vu ?? 0); @endphp
                                <div class="form-check d-flex align-items-center justify-content-between py-1 sua-dich-vu-le-row">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <input class="form-check-input me-2 sua-dich-vu-le-cb"
                                               type="checkbox"
                                               name="dich_vu_le_ids[]"
                                               value="{{ $dv->id }}"
                                               id="sua_dv_{{ $dv->id }}"
                                               data-price="{{ $giaDv }}">
                                        <label class="form-check-label mb-0" for="sua_dv_{{ $dv->id }}">
                                            {{ $dv->ten_dich_vu ?? $dv->ma_dich_vu ?? 'Dịch vụ #' . $dv->id }}
                                            @if($dv->ma_dich_vu)
                                                <span class="text-muted small">({{ $dv->ma_dich_vu }})</span>
                                            @endif
                                        </label>
                                    </div>
                                    <span class="text-end text-nowrap ms-2 fw-medium">{{ $giaDv > 0 ? number_format($giaDv, 0, ',', '.') . ' đ' : '—' }}</span>
                                </div>
                                @empty
                                <p class="text-muted small mb-0">Chưa có dịch vụ lẻ.</p>
                                @endforelse
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
#modalThemNhomDichVu .modal-nhom-dich-vu {
    max-width: 90vw;
    width: 1140px;
}
#modalSuaNhomDichVu .modal-nhom-dich-vu {
    max-width: 90vw;
    width: 50%;
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

    function formatTien(num) {
        return Number(num).toLocaleString('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' đ';
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

    document.querySelectorAll('.them-dich-vu-le-cb').forEach(function(cb) {
        cb.addEventListener('change', capNhatTongTienThem);
    });
    document.querySelectorAll('.sua-dich-vu-le-cb').forEach(function(cb) {
        cb.addEventListener('change', capNhatTongTienSua);
    });

    var modalThem = document.getElementById('modalThemNhomDichVu');
    if (modalThem) {
        modalThem.addEventListener('show.bs.modal', capNhatTongTienThem);
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
            document.getElementById('sua_trang_thai').value = btn.getAttribute('data-trang-thai') || '{{ \App\Models\NhomDichVu::TRANG_THAI_HIEN_THI }}';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
            document.getElementById('sua_mo_ta').value = btn.getAttribute('data-mo-ta') || '';
            // Chọn lại danh sách dịch vụ lẻ theo nhóm
            var idsStr = btn.getAttribute('data-dich-vu-le-ids') || '';
            var ids = idsStr ? idsStr.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];
            document.querySelectorAll('.sua-dich-vu-le-cb').forEach(function(cb) {
                cb.checked = ids.indexOf(cb.value) !== -1;
            });
            capNhatTongTienSua();
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
