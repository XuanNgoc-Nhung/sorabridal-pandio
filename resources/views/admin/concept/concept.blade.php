@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\Concept::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'asc');
    $hasFilter = request()->filled('search')
        || request()->filled('trang_thai')
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
        <form action="{{ route('admin.concept.concept') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên concept</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên concept...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="filter_trang_thai" name="trang_thai" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach(\App\Models\Concept::TRANG_THAI_LABELS as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('trang_thai') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\Concept::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.concept.concept') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách concept</span>
            <span data-bs-toggle="tooltip" title="Thêm concept mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemConcept">
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
                        <th style="width: 120px;">Hình ảnh</th>
                        <th>Tên concept</th>
                        <th class="text-center" style="width: 120px;">Đã sử dụng</th>
                        <th class="text-center" style="width: 120px;">Trạng thái</th>
                        <th class="text-center" style="width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                        @php
                            $isActive = (int)($item->trang_thai ?? 0) === \App\Models\Concept::TRANG_THAI_ACTIVE;
                            $trangThaiLabel = $isActive ? 'Đang hoạt động' : 'Ngưng hoạt động';
                            $trangThaiBadge = $isActive ? 'bg-label-success' : 'bg-label-secondary';
                        @endphp
                        <tr>
                            <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                            <td>
                                @if(!empty($item->hinh_anh))
                                    <img src="{{ asset('storage/' . $item->hinh_anh) }}" alt="" class="rounded border" style="width: 100px; height: 70px; object-fit: cover;">
                                @else
                                    <div class="rounded border bg-label-primary d-flex align-items-center justify-content-center" style="width: 100px; height: 70px;">
                                        <span class="text-primary fw-medium">—</span>
                                    </div>
                                @endif
                            </td>
                            <td><span class="fw-medium">{{ $item->ten_concept ?? '—' }}</span></td>
                            <td class="text-center">
                                <span class="fw-medium">{{ (int) ($item->so_luot_su_dung ?? 0) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $trangThaiBadge }}">{{ $trangThaiLabel }}</span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item btn-sua-concept"
                                           href="javascript:void(0);"
                                           data-bs-toggle="modal"
                                           data-bs-target="#modalSuaConcept"
                                       data-url="{{ route('admin.concept.concept.update', $item) }}"
                                           data-ten="{{ e($item->ten_concept ?? '') }}"
                                           data-trang-thai="{{ (int)($item->trang_thai ?? 1) }}"
                                           data-hinh-anh="{{ !empty($item->hinh_anh) ? asset('storage/' . $item->hinh_anh) : '' }}">
                                            <i class="fa-solid fa-pen me-2"></i> Sửa
                                        </a>

                                        <form id="form-xoa-concept-{{ $item->id }}" action="{{ route('admin.concept.concept.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="dropdown-item text-danger btn-xoa-concept" data-form-id="form-xoa-concept-{{ $item->id }}">
                                            <i class="fa-solid fa-trash me-2"></i> Xoá
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu concept.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSach ?? null" label="concept" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới concept --}}
<div class="modal fade" id="modalThemConcept" tabindex="-1" aria-labelledby="modalThemConceptLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-them-concept">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemConceptLabel">Thêm concept mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form action="{{ route('admin.concept.concept.store') }}" method="POST" enctype="multipart/form-data" id="formThemConcept">
                @csrf
                @if($errors->any() && !request()->routeIs('admin.concept.concept.update'))
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
                                <label class="form-label" for="them_hinh_anh">Hình ảnh</label>
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden mb-2">
                                    <div id="them_hinh_anh_placeholder" class="text-center text-muted py-4 px-3">
                                        —
                                    </div>
                                    <img id="them_hinh_anh_preview" src="" alt="Preview" class="rounded w-100 d-none" style="max-height: 240px; object-fit: cover;">
                                </div>
                                <input type="file" class="form-control" id="them_hinh_anh" name="hinh_anh" accept="image/*">
                                <small class="text-muted d-block mt-1">Mọi loại ảnh — tối đa 10MB</small>
                                <div id="them_hinh_anh_error" class="alert alert-danger mt-2 d-none" role="alert"></div>
                            </div>
                        </div>

                        {{-- Cột phải: Tên + Trạng thái --}}
                        <div class="col-12 col-lg-8">
                            <div class="row g-3">
                                <div class="col-12 col-sm-12">
                                    <label class="form-label" for="them_ten_concept">Tên concept <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="them_ten_concept"
                                           name="ten_concept"
                                           value="{{ old('ten_concept') }}"
                                           placeholder="Nhập tên concept"
                                           required>
                                </div>

                                <div class="col-12 col-sm-12">
                                    <label class="form-label" for="them_trang_thai">Trạng thái <span class="text-danger">*</span></label>
                                    @php
                                        $themTrangThai = (int) old('trang_thai', 1);
                                    @endphp
                                    <select class="form-select" id="them_trang_thai" name="trang_thai" required>
                                        <option value="1" {{ $themTrangThai === 1 ? 'selected' : '' }}>
                                            Đang hoạt động
                                        </option>
                                        <option value="0" {{ $themTrangThai === 0 ? 'selected' : '' }}>
                                            Ngưng hoạt động
                                        </option>
                                    </select>
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

{{-- Modal Chỉnh sửa concept --}}
<div class="modal fade" id="modalSuaConcept" tabindex="-1" aria-labelledby="modalSuaConceptLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-sua-concept">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaConceptLabel">Chỉnh sửa concept</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form id="formSuaConcept" method="POST" action="{{ request()->fullUrl() }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($errors->any() && request()->routeIs('admin.concept.concept.update'))
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
                                <div class="text-muted small mb-2">Hình ảnh</div>
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden mb-2">
                                    <div id="sua_hinh_anh_placeholder" class="text-center text-muted py-4 px-3">
                                        —
                                    </div>
                                    <img id="sua_hinh_anh_preview" src="" alt="Preview" class="rounded w-100 d-none" style="max-height: 240px; object-fit: cover;">
                                </div>
                                <label class="form-label" for="sua_hinh_anh">Hình ảnh mới (tuỳ chọn)</label>
                                <input type="file" class="form-control" id="sua_hinh_anh" name="hinh_anh" accept="image/*">
                                <small class="text-muted d-block mt-1">Mọi loại ảnh — tối đa 10MB</small>
                                <div id="sua_hinh_anh_error" class="alert alert-danger mt-2 d-none" role="alert"></div>
                            </div>
                        </div>

                        {{-- Cột phải: Tên + Trạng thái --}}
                        <div class="col-12 col-lg-8">
                            <div class="row g-3">
                                <div class="col-12 col-sm-12">
                                    <label class="form-label" for="sua_ten_concept">Tên concept <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="sua_ten_concept"
                                           name="ten_concept"
                                           value="{{ old('ten_concept') }}"
                                           placeholder="Nhập tên concept"
                                           required>
                                </div>

                                <div class="col-12 col-sm-12">
                                    <label class="form-label" for="sua_trang_thai">Trạng thái <span class="text-danger">*</span></label>
                                    @php
                                        $suaTrangThai = (int) old('trang_thai', 1);
                                    @endphp
                                    <select class="form-select" id="sua_trang_thai" name="trang_thai" required>
                                        <option value="1" {{ $suaTrangThai === 1 ? 'selected' : '' }}>
                                            Đang hoạt động
                                        </option>
                                        <option value="0" {{ $suaTrangThai === 0 ? 'selected' : '' }}>
                                            Ngưng hoạt động
                                        </option>
                                    </select>
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

{{-- Modal xác nhận xóa concept --}}
<div class="modal fade" id="modalXacNhanXoaConcept" tabindex="-1" aria-labelledby="modalXacNhanXoaConceptLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaConceptLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa concept này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaConcept">
                    <i class="fa-solid fa-trash me-1"></i> Xoá
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
    min-width: 800px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // Hiển thị đúng modal khi có lỗi validation
    @if($errors->any())
        @if(request()->routeIs('admin.concept.concept.update'))
            (function() {
                var modalSua = document.getElementById('modalSuaConcept');
                if (!modalSua) return;
                var m = new bootstrap.Modal(modalSua);
                m.show();
            })();
        @else
            (function() {
                var modalThem = document.getElementById('modalThemConcept');
                if (!modalThem) return;
                var m = new bootstrap.Modal(modalThem);
                m.show();
            })();
        @endif
    @endif

    // --- Upload ảnh: Preview cho modal Thêm + Sửa ---
    var MAX_SIZE = 10 * 1024 * 1024; // 10MB

    function isImageFile(file) {
        // Dựa vào MIME type của trình duyệt; nếu rỗng thì để backend kiểm tra tiếp.
        return !!file && (!file.type || file.type.startsWith('image/'));
    }

    // Modal Thêm
    var modalThem = document.getElementById('modalThemConcept');
    var inputThemHinhAnh = document.getElementById('them_hinh_anh');
    var themPreviewImg = document.getElementById('them_hinh_anh_preview');
    var themPlaceholder = document.getElementById('them_hinh_anh_placeholder');
    var themErrorDiv = document.getElementById('them_hinh_anh_error');
    var themObjectUrl = null;

    function clearThemPreview() {
        if (themObjectUrl) {
            URL.revokeObjectURL(themObjectUrl);
            themObjectUrl = null;
        }
        if (themPreviewImg) {
            themPreviewImg.src = '';
            themPreviewImg.classList.add('d-none');
        }
        if (themPlaceholder) themPlaceholder.classList.remove('d-none');
        if (themErrorDiv) {
            themErrorDiv.classList.add('d-none');
            themErrorDiv.textContent = '';
        }
    }

    function processThemFile(file) {
        if (themObjectUrl) {
            URL.revokeObjectURL(themObjectUrl);
            themObjectUrl = null;
        }

        if (themErrorDiv) {
            themErrorDiv.classList.add('d-none');
            themErrorDiv.textContent = '';
        }

        if (!file) {
            clearThemPreview();
            return;
        }

        if (!isImageFile(file)) {
            if (themErrorDiv) {
                themErrorDiv.textContent = 'Vui lòng chọn đúng file ảnh.';
                themErrorDiv.classList.remove('d-none');
            }
            if (themPreviewImg) {
                themPreviewImg.src = '';
                themPreviewImg.classList.add('d-none');
            }
            if (themPlaceholder) themPlaceholder.classList.remove('d-none');
            if (inputThemHinhAnh) inputThemHinhAnh.value = '';
            return;
        }

        if (file.size > MAX_SIZE) {
            if (themErrorDiv) {
                themErrorDiv.textContent = 'Kích thước file không được vượt quá 10MB. File của bạn: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB.';
                themErrorDiv.classList.remove('d-none');
            }
            if (themPreviewImg) {
                themPreviewImg.src = '';
                themPreviewImg.classList.add('d-none');
            }
            if (themPlaceholder) themPlaceholder.classList.remove('d-none');
            if (inputThemHinhAnh) inputThemHinhAnh.value = '';
            return;
        }

        if (themPlaceholder) themPlaceholder.classList.add('d-none');
        if (themPreviewImg) {
            themObjectUrl = URL.createObjectURL(file);
            themPreviewImg.src = themObjectUrl;
            themPreviewImg.classList.remove('d-none');
        }
    }

    if (inputThemHinhAnh) {
        inputThemHinhAnh.addEventListener('change', function () {
            var file = this.files && this.files[0];
            processThemFile(file);
        });
    }

    if (modalThem) {
        modalThem.addEventListener('hidden.bs.modal', function () {
            clearThemPreview();
        });
    }

    // Modal Sửa
    var inputSuaHinhAnh = document.getElementById('sua_hinh_anh');
    var suaPreviewImg = document.getElementById('sua_hinh_anh_preview');
    var suaPlaceholder = document.getElementById('sua_hinh_anh_placeholder');
    var suaErrorDiv = document.getElementById('sua_hinh_anh_error');
    var suaObjectUrl = null;

    function showSuaExistingOrPlaceholder() {
        var existingSrc = (suaPreviewImg && suaPreviewImg.dataset) ? (suaPreviewImg.dataset.existingSrc || '') : '';
        if (!suaPreviewImg || !suaPlaceholder) return;

        if (existingSrc) {
            suaPreviewImg.src = existingSrc;
            suaPreviewImg.classList.remove('d-none');
            suaPlaceholder.classList.add('d-none');
        } else {
            suaPreviewImg.src = '';
            suaPreviewImg.classList.add('d-none');
            suaPlaceholder.classList.remove('d-none');
        }
    }

    function processSuaFile(file) {
        if (suaObjectUrl) {
            URL.revokeObjectURL(suaObjectUrl);
            suaObjectUrl = null;
        }

        if (suaErrorDiv) {
            suaErrorDiv.classList.add('d-none');
            suaErrorDiv.textContent = '';
        }

        // Nếu user bỏ chọn file (file = undefined/null), quay lại ảnh hiện tại
        if (!file) {
            showSuaExistingOrPlaceholder();
            return;
        }

        if (!isImageFile(file)) {
            if (suaErrorDiv) {
                suaErrorDiv.textContent = 'Vui lòng chọn đúng file ảnh.';
                suaErrorDiv.classList.remove('d-none');
            }
            showSuaExistingOrPlaceholder();
            if (inputSuaHinhAnh) inputSuaHinhAnh.value = '';
            return;
        }

        if (file.size > MAX_SIZE) {
            if (suaErrorDiv) {
                suaErrorDiv.textContent = 'Kích thước file không được vượt quá 10MB. File của bạn: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB.';
                suaErrorDiv.classList.remove('d-none');
            }
            showSuaExistingOrPlaceholder();
            if (inputSuaHinhAnh) inputSuaHinhAnh.value = '';
            return;
        }

        if (suaPlaceholder) suaPlaceholder.classList.add('d-none');
        if (suaPreviewImg) {
            suaObjectUrl = URL.createObjectURL(file);
            suaPreviewImg.src = suaObjectUrl;
            suaPreviewImg.classList.remove('d-none');
        }
    }

    if (inputSuaHinhAnh) {
        inputSuaHinhAnh.addEventListener('change', function () {
            var file = this.files && this.files[0];
            processSuaFile(file);
        });
    }

    // Modal Sửa: đổ dữ liệu + gán action
    var modalSua = document.getElementById('modalSuaConcept');
    var formSua = document.getElementById('formSuaConcept');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            if (!btn) return;

            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;

            var ten = btn.getAttribute('data-ten') || '';
            var trangThai = btn.getAttribute('data-trang-thai') || '1';
            document.getElementById('sua_ten_concept').value = ten;
            document.getElementById('sua_trang_thai').value = trangThai;

            var hinhAnh = btn.getAttribute('data-hinh-anh') || '';
            var previewImg = document.getElementById('sua_hinh_anh_preview');
            var placeholder = document.getElementById('sua_hinh_anh_placeholder');

            // Reset trạng thái preview (nếu trước đó user đã chọn file mới)
            if (suaObjectUrl) {
                try { URL.revokeObjectURL(suaObjectUrl); } catch (err) {}
                suaObjectUrl = null;
            }
            if (suaErrorDiv) {
                suaErrorDiv.classList.add('d-none');
                suaErrorDiv.textContent = '';
            }
            if (inputSuaHinhAnh) inputSuaHinhAnh.value = '';

            if (previewImg && placeholder) {
                if (previewImg.dataset) previewImg.dataset.existingSrc = hinhAnh;
                if (hinhAnh) {
                    previewImg.src = hinhAnh;
                    previewImg.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                } else {
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    placeholder.classList.remove('d-none');
                }
            }
        });
    }

    // Xóa: mở modal xác nhận, sau đó submit form
    var modalXoa = document.getElementById('modalXacNhanXoaConcept');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaConcept');
    var formIdCanXoa = null;

    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });

        document.querySelectorAll('.btn-xoa-concept').forEach(function (btn) {
            btn.addEventListener('click', function () {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                var modal = bootstrap.Modal.getOrCreateInstance(modalXoa);
                modal.show();
            });
        });

        btnXacNhanXoa.addEventListener('click', function () {
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

