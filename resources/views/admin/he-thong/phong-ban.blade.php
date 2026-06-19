@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\PhongBan::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('search')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc';
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
        <form action="{{ route('admin.he-thong.phong-ban') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên hoặc mã phòng ban</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên hoặc mã phòng ban...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\PhongBan::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.he-thong.phong-ban') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách phòng ban</span>
            <span data-bs-toggle="tooltip" title="Thêm phòng ban mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemPhongBan">
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
                        <th>Tên phòng ban</th>
                        <th>Mã phòng ban</th>
                        <th class="text-center">Số lượng nhân viên</th>
                        <th>Mô tả</th>
                        <th>Ghi chú</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $item->ten_phong_ban ?? '—' }}</span></td>
                        <td>{{ $item->ma_phong_ban ?? '—' }}</td>
                        <td class="text-center fw-medium">{{ number_format((int) ($item->nhan_viens_count ?? 0)) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->mo_ta ?? '—', 50) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->ghi_chu ?? '—', 40) }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-ds-nhan-vien-phong-ban"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalDsNhanVienPhongBan"
                                       data-url="{{ route('admin.he-thong.phong-ban.nhan-vien', $item) }}"
                                       data-ten="{{ e($item->ten_phong_ban ?? '') }}"
                                       data-ma="{{ e($item->ma_phong_ban ?? '') }}">
                                        <i class="fa-solid fa-users me-2"></i> Xem DS nhân viên
                                    </a>
                                    <a class="dropdown-item btn-sua-phong-ban"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaPhongBan"
                                       data-url="{{ route('admin.he-thong.phong-ban.update', $item) }}"
                                       data-ten="{{ e($item->ten_phong_ban ?? '') }}"
                                       data-ma="{{ e($item->ma_phong_ban ?? '') }}"
                                       data-mo-ta="{{ e($item->mo_ta ?? '') }}"
                                       data-ghi-chu="{{ e($item->ghi_chu ?? '') }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-pb-{{ $item->id }}" action="{{ route('admin.he-thong.phong-ban.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-phong-ban" data-form-id="form-xoa-pb-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có dữ liệu phòng ban.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="phòng ban" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới phòng ban --}}
<div class="modal fade" id="modalThemPhongBan" tabindex="-1" aria-labelledby="modalThemPhongBanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-them-phong-ban">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemPhongBanLabel">Thêm phòng ban mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.he-thong.phong-ban.store') }}" method="POST" id="formThemPhongBan">
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="them_ten_phong_ban">Tên phòng ban <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ten_phong_ban" name="ten_phong_ban" value="{{ old('ten_phong_ban') }}" placeholder="Nhập tên phòng ban" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="them_ma_phong_ban">Mã phòng ban <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ma_phong_ban" name="ma_phong_ban" value="{{ old('ma_phong_ban') }}" placeholder="Ví dụ: PB001" required>
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

{{-- Modal Chỉnh sửa phòng ban --}}
<div class="modal fade" id="modalSuaPhongBan" tabindex="-1" aria-labelledby="modalSuaPhongBanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-them-phong-ban">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaPhongBanLabel">Chỉnh sửa phòng ban</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaPhongBan" method="POST" action="">
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
                            <label class="form-label" for="sua_ten_phong_ban">Tên phòng ban <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ten_phong_ban" name="ten_phong_ban" placeholder="Nhập tên phòng ban" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_ma_phong_ban">Mã phòng ban <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ma_phong_ban" name="ma_phong_ban" placeholder="Ví dụ: PB001" required>
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

{{-- Modal danh sách nhân viên theo phòng ban --}}
<div class="modal fade" id="modalDsNhanVienPhongBan" tabindex="-1" aria-labelledby="modalDsNhanVienPhongBanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-ds-nhan-vien-pb">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDsNhanVienPhongBanLabel">Danh sách nhân viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div id="dnpbLoading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Đang tải danh sách nhân viên...</p>
                </div>
                <div id="dnpbError" class="alert alert-danger d-none" role="alert"></div>
                <div id="dnpbContent" class="d-none">
                    <div class="table-responsive text-nowrap table-wrapper-bordered">
                        <table class="table table-hover table-bordered mb-0" id="dnpbTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 40px;">STT</th>
                                    <th style="width: 56px;">Ảnh</th>
                                    <th>Họ tên</th>
                                    <th>Giới tính</th>
                                    <th>Ngày sinh</th>
                                    <th>CCCD</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Vai trò</th>
                                    <th>Vị trí</th>
                                    <th>Ngày vào CT</th>
                                    <th>Ngày ký HĐ</th>
                                    <th>Lương CB</th>
                                    <th>Lương TC</th>
                                    <th>Ngân hàng</th>
                                    <th>Chi nhánh</th>
                                    <th>STK</th>
                                </tr>
                            </thead>
                            <tbody id="dnpbTableBody"></tbody>
                        </table>
                    </div>
                    <p id="dnpbEmpty" class="text-center text-muted py-4 d-none mb-0">Chưa có nhân viên trong phòng ban này.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa phòng ban --}}
<div class="modal fade" id="modalXacNhanXoaPhongBan" tabindex="-1" aria-labelledby="modalXacNhanXoaPhongBanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaPhongBanLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa phòng ban này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaPhongBan">
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
    min-width: 700px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
#modalThemPhongBan .modal-them-phong-ban,
#modalSuaPhongBan .modal-them-phong-ban {
    max-width: 90vw;
    width: 50%;
}
#modalXacNhanXoaPhongBan .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
#modalDsNhanVienPhongBan .modal-ds-nhan-vien-pb {
    max-width: 95vw;
    width: 90%;
}
#modalDsNhanVienPhongBan .table-wrapper-bordered .table {
    min-width: 1400px;
}
#modalDsNhanVienPhongBan .dnpb-avatar {
    width: 36px;
    height: 36px;
    object-fit: cover;
}
#modalDsNhanVienPhongBan .dnpb-avatar-placeholder {
    width: 36px;
    height: 36px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // Dọn backdrop và trạng thái body khi đóng bất kỳ modal nào
    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }
    [document.getElementById('modalThemPhongBan'), document.getElementById('modalSuaPhongBan'), document.getElementById('modalDsNhanVienPhongBan')].forEach(function(modal) {
        if (modal) modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
    });

    @if($errors->any())
    var modalThem = document.getElementById('modalThemPhongBan');
    if (modalThem) {
        var m = new bootstrap.Modal(modalThem);
        m.show();
    }
    @endif

    // Modal Sửa: gán data vào form
    var modalSua = document.getElementById('modalSuaPhongBan');
    var formSua = document.getElementById('formSuaPhongBan');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-phong-ban')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ten_phong_ban').value = btn.getAttribute('data-ten') || '';
            document.getElementById('sua_ma_phong_ban').value = btn.getAttribute('data-ma') || '';
            document.getElementById('sua_mo_ta').value = btn.getAttribute('data-mo-ta') || '';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
        });
    }

    // Modal danh sách nhân viên theo phòng ban
    var modalDsNv = document.getElementById('modalDsNhanVienPhongBan');
    var dnpbLoading = document.getElementById('dnpbLoading');
    var dnpbError = document.getElementById('dnpbError');
    var dnpbContent = document.getElementById('dnpbContent');
    var dnpbTableBody = document.getElementById('dnpbTableBody');
    var dnpbEmpty = document.getElementById('dnpbEmpty');
    var dnpbTable = document.getElementById('dnpbTable');

    function dnpbEsc(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function dnpbCell(val) {
        var v = val != null && String(val).trim() !== '' ? String(val) : '—';
        return dnpbEsc(v);
    }

    function dnpbReset() {
        if (dnpbError) {
            dnpbError.classList.add('d-none');
            dnpbError.textContent = '';
        }
        if (dnpbContent) dnpbContent.classList.add('d-none');
        if (dnpbLoading) dnpbLoading.classList.add('d-none');
        if (dnpbTableBody) dnpbTableBody.innerHTML = '';
        if (dnpbEmpty) dnpbEmpty.classList.add('d-none');
        if (dnpbTable) dnpbTable.classList.remove('d-none');
    }

    function dnpbRenderAvatar(row) {
        var name = row.name || 'N';
        var initial = dnpbEsc(name.charAt(0).toUpperCase());
        if (row.hinh_anh) {
            return '<img src="' + dnpbEsc(row.hinh_anh) + '" alt="" class="rounded-circle dnpb-avatar">';
        }
        return '<div class="avatar rounded-circle bg-label-primary d-flex align-items-center justify-content-center dnpb-avatar-placeholder">'
            + '<span class="text-primary fw-medium">' + initial + '</span></div>';
    }

    function dnpbRenderRows(items) {
        if (!dnpbTableBody) return;
        dnpbTableBody.innerHTML = '';
        if (!items || !items.length) {
            if (dnpbTable) dnpbTable.classList.add('d-none');
            if (dnpbEmpty) dnpbEmpty.classList.remove('d-none');
            return;
        }
        if (dnpbTable) dnpbTable.classList.remove('d-none');
        if (dnpbEmpty) dnpbEmpty.classList.add('d-none');
        items.forEach(function(row, idx) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="text-center">' + (idx + 1) + '</td>' +
                '<td>' + dnpbRenderAvatar(row) + '</td>' +
                '<td><span class="fw-medium">' + dnpbCell(row.name) + '</span></td>' +
                '<td>' + dnpbCell(row.gioi_tinh) + '</td>' +
                '<td>' + dnpbCell(row.ngay_sinh) + '</td>' +
                '<td>' + dnpbCell(row.cccd) + '</td>' +
                '<td>' + dnpbCell(row.email) + '</td>' +
                '<td>' + dnpbCell(row.phone) + '</td>' +
                '<td>' + dnpbCell(row.role_label) + '</td>' +
                '<td>' + dnpbCell(row.vi_tri_lam_viec) + '</td>' +
                '<td>' + dnpbCell(row.ngay_vao_cong_ty) + '</td>' +
                '<td>' + dnpbCell(row.ngay_ky_hop_dong) + '</td>' +
                '<td class="text-end">' + dnpbCell(row.luong_co_ban) + '</td>' +
                '<td class="text-end">' + dnpbCell(row.luong_tang_ca) + '</td>' +
                '<td>' + dnpbCell(row.ngan_hang) + '</td>' +
                '<td>' + dnpbCell(row.chi_nhanh) + '</td>' +
                '<td>' + dnpbCell(row.so_tai_khoan) + '</td>';
            dnpbTableBody.appendChild(tr);
        });
    }

    if (modalDsNv) {
        modalDsNv.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-ds-nhan-vien-phong-ban')) return;

            var url = btn.getAttribute('data-url') || '';
            var ten = btn.getAttribute('data-ten') || '';

            dnpbReset();
            var titleEl = document.getElementById('modalDsNhanVienPhongBanLabel');
            if (titleEl) titleEl.textContent = 'Danh sách nhân viên — ' + (ten || 'Phòng ban');

            if (!url) {
                if (dnpbError) {
                    dnpbError.textContent = 'Không xác định được phòng ban.';
                    dnpbError.classList.remove('d-none');
                }
                return;
            }

            if (dnpbLoading) dnpbLoading.classList.remove('d-none');

            RestApi.get(url)
                .then(function(res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    if (dnpbLoading) dnpbLoading.classList.add('d-none');
                    if (dnpbContent) dnpbContent.classList.remove('d-none');
                    var items = res.data && Array.isArray(res.data.items) ? res.data.items : [];
                    dnpbRenderRows(items);
                })
                .catch(function(err) {
                    if (dnpbLoading) dnpbLoading.classList.add('d-none');
                    if (dnpbError) {
                        dnpbError.textContent = 'Không tải được danh sách nhân viên. ' + (err && err.message ? err.message : '');
                        dnpbError.classList.remove('d-none');
                    }
                });
        });

        modalDsNv.addEventListener('hidden.bs.modal', dnpbReset);
    }

    // Xóa: mở modal Bootstrap xác nhận, sau đó submit form
    var modalXoaPb = document.getElementById('modalXacNhanXoaPhongBan');
    var btnXacNhanXoaPb = document.getElementById('btnXacNhanXoaPhongBan');
    var formIdCanXoa = null;
    if (modalXoaPb && btnXacNhanXoaPb) {
        modalXoaPb.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        document.querySelectorAll('.btn-xoa-phong-ban').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                var modal = bootstrap.Modal.getOrCreateInstance(modalXoaPb);
                modal.show();
            });
        });
        btnXacNhanXoaPb.addEventListener('click', function() {
            if (formIdCanXoa) {
                var form = document.getElementById(formIdCanXoa);
                if (form) form.submit();
            }
            var inst = bootstrap.Modal.getInstance(modalXoaPb);
            if (inst) inst.hide();
            formIdCanXoa = null;
        });
    }
});
</script>
@endpush
@endsection
