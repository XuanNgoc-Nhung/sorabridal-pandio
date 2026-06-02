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
        <form action="{{ route('admin.he-thong.ngan-hang-thanh-toan') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Từ khoá</label>
                    <input type="text"
                        class="form-control"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nhập tên ngân hàng / số tài khoản / chủ tài khoản...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="trang_thai" name="trang_thai" data-placeholder="Chọn trạng thái">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if(request()->filled('search') || request()->filled('trang_thai'))
                    <a href="{{ route('admin.he-thong.ngan-hang-thanh-toan') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách ngân hàng thanh toán</span>
            <button type="button"
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalThemNganHang">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Logo</th>
                        <th>Tên ngân hàng</th>
                        <th>Tên chi tiết</th>
                        <th>Số tài khoản</th>
                        <th>Chủ tài khoản</th>
                        <th>Chi nhánh</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td>
                            @if(!empty($item->hinh_anh_logo))
                            <img src="{{ $item->hinh_anh_logo }}" style="width: 150px; height: 56px; object-fit: contain;" alt="logo ngân hàng" class="bank-logo-preview">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><span class="fw-medium">{{ $item->ten_ngan_hang ?? '—' }}</span></td>
                        <td>{{ $item->ten_chi_tiet ?? '—' }}</td>
                        <td>{{ $item->so_tai_khoan ?? '—' }}</td>
                        <td>{{ $item->chu_tai_khoan ?? '—' }}</td>
                        <td>{{ $item->chi_nhanh ?? '—' }}</td>
                        <td class="text-center">
                            @if((int) $item->trang_thai === 1)
                            <span class="badge bg-success">Đang hoạt động</span>
                            @else
                            <span class="badge bg-secondary">Ngưng hoạt động</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    {{-- Đẩy dữ liệu hiện tại vào modal sửa --}}
                                    <a class="dropdown-item btn-sua-ngan-hang"
                                        href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSuaNganHang"
                                        data-url="{{ route('admin.he-thong.ngan-hang-thanh-toan.update', $item) }}"
                                        data-hinh-anh-logo="{{ e($item->hinh_anh_logo ?? '') }}"
                                        data-ten-ngan-hang="{{ e($item->ten_ngan_hang ?? '') }}"
                                        data-ten-chi-tiet="{{ e($item->ten_chi_tiet ?? '') }}"
                                        data-so-tai-khoan="{{ e($item->so_tai_khoan ?? '') }}"
                                        data-chu-tai-khoan="{{ e($item->chu_tai_khoan ?? '') }}"
                                        data-chi-nhanh="{{ e($item->chi_nhanh ?? '') }}"
                                        data-trang-thai="{{ (int) ($item->trang_thai ?? 1) }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>

                                    <form id="form-xoa-ngan-hang-{{ $item->id }}" action="{{ route('admin.he-thong.ngan-hang-thanh-toan.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-ngan-hang" data-form-id="form-xoa-ngan-hang-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Chưa có dữ liệu ngân hàng thanh toán.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="ngân hàng thanh toán" />
        </div>
    </div>
</div>

{{-- Modal thêm mới --}}
<div class="modal fade" id="modalThemNganHang" tabindex="-1" aria-labelledby="modalThemNganHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-form-ngan-hang">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemNganHangLabel">Thêm ngân hàng thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.he-thong.ngan-hang-thanh-toan.store') }}" method="POST">
                @csrf
                @if($errors->any())
                <div class="modal-body py-0">
                    <div class="alert alert-danger mb-0">
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
                        <div class="col-12">
                            <label class="form-label">Xem trước logo</label>
                            <div class="logo-preview-box" id="them_logo_preview_box">
                                <img id="them_logo_preview_img" src="{{ old('hinh_anh_logo') }}" alt="logo xem trước" class="logo-preview-img d-none">
                                <div id="them_logo_preview_placeholder" class="logo-preview-placeholder {{ old('hinh_anh_logo') ? 'd-none' : '' }}">
                                    Chưa có ảnh logo
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_hinh_anh_logo">Link hình ảnh logo</label>
                            <input type="text" class="form-control" id="them_hinh_anh_logo" name="hinh_anh_logo" value="{{ old('hinh_anh_logo') }}" placeholder="Ví dụ: https://domain.com/logo-ngan-hang.png">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_ten_ngan_hang">Tên ngân hàng</label>
                            <input type="text" class="form-control" id="them_ten_ngan_hang" name="ten_ngan_hang" value="{{ old('ten_ngan_hang') }}" placeholder="Ví dụ: Vietcombank" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_ten_chi_tiet">Tên chi tiết</label>
                            <input type="text" class="form-control" id="them_ten_chi_tiet" name="ten_chi_tiet" value="{{ old('ten_chi_tiet') }}" placeholder="Ví dụ: Ngân hàng TMCP Ngoại thương Việt Nam">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_so_tai_khoan">Số tài khoản</label>
                            <input type="text" class="form-control" id="them_so_tai_khoan" name="so_tai_khoan" value="{{ old('so_tai_khoan') }}" placeholder="Nhập số tài khoản" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_chu_tai_khoan">Chủ tài khoản</label>
                            <input type="text" class="form-control" id="them_chu_tai_khoan" name="chu_tai_khoan" value="{{ old('chu_tai_khoan') }}" placeholder="Nhập tên chủ tài khoản" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_chi_nhanh">Chi nhánh</label>
                            <input type="text" class="form-control" id="them_chi_nhanh" name="chi_nhanh" value="{{ old('chi_nhanh') }}" placeholder="Ví dụ: Chi nhánh TP.HCM">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select" id="them_trang_thai" name="trang_thai" data-placeholder="Chọn trạng thái" required>
                                <option value="1" {{ old('trang_thai', '1') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ old('trang_thai') === '0' ? 'selected' : '' }}>Ngưng hoạt động</option>
                            </select>
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

{{-- Modal chỉnh sửa --}}
<div class="modal fade" id="modalSuaNganHang" tabindex="-1" aria-labelledby="modalSuaNganHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-form-ngan-hang">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaNganHangLabel">Chỉnh sửa ngân hàng thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaNganHang" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Xem trước logo</label>
                            <div class="logo-preview-box" id="sua_logo_preview_box">
                                <img id="sua_logo_preview_img" src="" alt="logo xem trước" class="logo-preview-img d-none">
                                <div id="sua_logo_preview_placeholder" class="logo-preview-placeholder">
                                    Chưa có ảnh logo
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_hinh_anh_logo">Link hình ảnh logo</label>
                            <input type="text" class="form-control" id="sua_hinh_anh_logo" name="hinh_anh_logo" placeholder="Ví dụ: https://domain.com/logo-ngan-hang.png">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_ten_ngan_hang">Tên ngân hàng</label>
                            <input type="text" class="form-control" id="sua_ten_ngan_hang" name="ten_ngan_hang" placeholder="Ví dụ: Vietcombank" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_ten_chi_tiet">Tên chi tiết</label>
                            <input type="text" class="form-control" id="sua_ten_chi_tiet" name="ten_chi_tiet" placeholder="Ví dụ: Ngân hàng TMCP Ngoại thương Việt Nam">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_so_tai_khoan">Số tài khoản</label>
                            <input type="text" class="form-control" id="sua_so_tai_khoan" name="so_tai_khoan" placeholder="Nhập số tài khoản" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_chu_tai_khoan">Chủ tài khoản</label>
                            <input type="text" class="form-control" id="sua_chu_tai_khoan" name="chu_tai_khoan" placeholder="Nhập tên chủ tài khoản" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_chi_nhanh">Chi nhánh</label>
                            <input type="text" class="form-control" id="sua_chi_nhanh" name="chi_nhanh" placeholder="Ví dụ: Chi nhánh TP.HCM">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select" id="sua_trang_thai" name="trang_thai" data-placeholder="Chọn trạng thái" required>
                                <option value="1">Đang hoạt động</option>
                                <option value="0">Ngưng hoạt động</option>
                            </select>
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

{{-- Modal xác nhận xoá --}}
<div class="modal fade" id="modalXacNhanXoaNganHang" tabindex="-1" aria-labelledby="modalXacNhanXoaNganHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaNganHangLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa ngân hàng thanh toán này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaNganHang">
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
}
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 1100px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
.bank-logo-preview {
    width: 56px;
    height: 56px;
    object-fit: contain;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    padding: 4px;
}
.modal-form-ngan-hang {
    max-width: 90vw;
    width: 820px;
}
#modalXacNhanXoaNganHang .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
.filter-actions {
    gap: 12px;
    flex-wrap: wrap;
}
.filter-actions-left {
    gap: 12px;
    flex-wrap: wrap;
}
.logo-preview-box {
    width: 100%;
    height: 130px;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.logo-preview-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.logo-preview-placeholder {
    color: #64748b;
    font-size: 0.9rem;
}
.field-order-prefix {
    font-weight: 600;
}

</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tooltip bootstrap dùng cho các icon/mục có title.
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    [document.getElementById('modalThemNganHang'), document.getElementById('modalSuaNganHang')].forEach(function (modal) {
        if (modal) modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
    });

    // Khởi tạo Select2 tương tự màn tạo hợp đồng: hỗ trợ placeholder + clear + full width.
    var $ = window.jQuery;
    if ($ && $.fn.select2) {
        function buildSelect2Opts($el) {
            return {
                placeholder: $el.data('placeholder') || 'Chọn...',
                allowClear: true,
                width: '100%'
            };
        }

        // Select ở vùng lọc (ngoài modal).
        var $filterSelect = $('#trang_thai');
        if ($filterSelect.length && !$filterSelect.data('select2')) {
            $filterSelect.select2(buildSelect2Opts($filterSelect));
        }

        // Select trong modal thêm/sửa cần gắn dropdownParent để không bị che bởi z-index của modal.
        var $themTrangThai = $('#them_trang_thai');
        if ($themTrangThai.length && !$themTrangThai.data('select2')) {
            var optsThem = buildSelect2Opts($themTrangThai);
            optsThem.dropdownParent = $('#modalThemNganHang');
            $themTrangThai.select2(optsThem);
        }

        var $suaTrangThai = $('#sua_trang_thai');
        if ($suaTrangThai.length && !$suaTrangThai.data('select2')) {
            var optsSua = buildSelect2Opts($suaTrangThai);
            optsSua.dropdownParent = $('#modalSuaNganHang');
            $suaTrangThai.select2(optsSua);
        }
    }

    // Đồng bộ preview logo khi người dùng nhập URL.
    function bindLogoPreview(inputId, imgId, placeholderId) {
        var inputEl = document.getElementById(inputId);
        var imgEl = document.getElementById(imgId);
        var placeholderEl = document.getElementById(placeholderId);
        if (!inputEl || !imgEl || !placeholderEl) return;

        function resetPreview() {
            imgEl.src = '';
            imgEl.classList.add('d-none');
            placeholderEl.classList.remove('d-none');
        }

        function updatePreview() {
            var url = (inputEl.value || '').trim();
            if (!url) {
                resetPreview();
                return;
            }
            imgEl.onload = function () {
                imgEl.classList.remove('d-none');
                placeholderEl.classList.add('d-none');
            };
            imgEl.onerror = function () {
                resetPreview();
            };
            imgEl.src = url;
        }

        inputEl.addEventListener('input', updatePreview);
        updatePreview();
    }

    bindLogoPreview('them_hinh_anh_logo', 'them_logo_preview_img', 'them_logo_preview_placeholder');
    bindLogoPreview('sua_hinh_anh_logo', 'sua_logo_preview_img', 'sua_logo_preview_placeholder');

    // Đánh số label dạng [1] và thêm [*] đỏ cho input bắt buộc.
    function applyFieldOrderLabels(formId) {
        var form = document.getElementById(formId);
        if (!form) return;

        var idx = 0;
        form.querySelectorAll('.modal-body label.form-label[for]').forEach(function (label) {
            var fieldId = label.getAttribute('for');
            if (!fieldId) return;

            var inputEl = form.querySelector('#' + fieldId);
            if (!inputEl) return;
            if (!['INPUT', 'SELECT', 'TEXTAREA'].includes(inputEl.tagName)) return;
            if (inputEl.type === 'hidden') return;

            idx += 1;

            var oldPrefix = label.querySelector('.field-order-prefix');
            if (oldPrefix) oldPrefix.remove();
            var oldReq = label.querySelector('.field-required-mark');
            if (oldReq) oldReq.remove();

            var prefix = document.createElement('span');
            prefix.className = 'field-order-prefix me-1';
            prefix.textContent = '[' + idx + ']';
            if (inputEl.hasAttribute('required')) {
                prefix.classList.add('text-danger');
            }
            label.insertBefore(prefix, label.firstChild);
        });
    }

    applyFieldOrderLabels('modalThemNganHang');
    applyFieldOrderLabels('formSuaNganHang');

    @if($errors->any())
    // Khi submit form thêm bị lỗi validate, mở lại modal thêm để hiển thị lỗi.
    var modalThem = document.getElementById('modalThemNganHang');
    if (modalThem) {
        var m = new bootstrap.Modal(modalThem);
        m.show();
    }
    @endif

    // Gán data từ dòng đang chọn vào form sửa.
    var modalSua = document.getElementById('modalSuaNganHang');
    var formSua = document.getElementById('formSuaNganHang');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-ngan-hang')) return;

            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;

            document.getElementById('sua_hinh_anh_logo').value = btn.getAttribute('data-hinh-anh-logo') || '';
            document.getElementById('sua_ten_ngan_hang').value = btn.getAttribute('data-ten-ngan-hang') || '';
            document.getElementById('sua_ten_chi_tiet').value = btn.getAttribute('data-ten-chi-tiet') || '';
            document.getElementById('sua_so_tai_khoan').value = btn.getAttribute('data-so-tai-khoan') || '';
            document.getElementById('sua_chu_tai_khoan').value = btn.getAttribute('data-chu-tai-khoan') || '';
            document.getElementById('sua_chi_nhanh').value = btn.getAttribute('data-chi-nhanh') || '';
            var trangThaiValue = btn.getAttribute('data-trang-thai') || '1';
            var suaTrangThaiEl = document.getElementById('sua_trang_thai');
            if (suaTrangThaiEl) {
                // Select đang dùng Select2 nên cần trigger change để UI hiển thị đúng value.
                suaTrangThaiEl.value = trangThaiValue;
                if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(suaTrangThaiEl).data('select2')) {
                    window.jQuery(suaTrangThaiEl).val(trangThaiValue).trigger('change');
                }
            }
            document.getElementById('sua_hinh_anh_logo').dispatchEvent(new Event('input'));
        });
    }

    // Quy trình xóa: bấm xóa -> mở modal xác nhận -> submit form delete.
    var modalXoa = document.getElementById('modalXacNhanXoaNganHang');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaNganHang');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        document.querySelectorAll('.btn-xoa-ngan-hang').forEach(function (btn) {
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
