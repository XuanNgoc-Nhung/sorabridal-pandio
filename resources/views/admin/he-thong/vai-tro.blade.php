@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\VaiTro::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('tu_khoa')
        || request()->filled('search')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc';
    $oldPermissions = old('permissions', []);
    $isEditValidation = old('_method') === 'PUT';
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
        <form action="{{ route('admin.he-thong.vai-tro') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_khoa">Từ khóa</label>
                    <input type="text"
                           class="form-control"
                           id="tu_khoa"
                           name="tu_khoa"
                           value="{{ request('tu_khoa', request('search')) }}"
                           placeholder="Nhập...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\VaiTro::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.he-thong.vai-tro') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách vai trò</span>
            <span data-bs-toggle="tooltip" title="Thêm vai trò mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemVaiTro">
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
                        <th>Mã vai trò</th>
                        <th>Tên vai trò</th>
                        <th class="text-center">Số người dùng</th>
                        <th class="text-center">Số menu</th>
                        <th>Mô tả</th>
                        <th>Ghi chú</th>
                        <th class="text-center">Điều chỉnh hợp đồng cưới</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td>{{ $item->ma_vai_tro ?? '—' }}</td>
                        <td><span class="fw-medium">{{ $item->ten_vai_tro ?? '—' }}</span></td>
                        <td class="text-center fw-medium">{{ number_format((int) ($item->users_count ?? 0)) }}</td>
                        <td class="text-center">{{ count($item->ds_menu ?? []) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->mo_ta ?? '—', 50) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->ghi_chu ?? '—', 40) }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-flex justify-content-center mb-0">
                                <input class="form-check-input switch-dieu-chinh-hop-dong-cuoi"
                                       type="checkbox"
                                       role="switch"
                                       data-url="{{ route('admin.he-thong.vai-tro.update-dieu-chinh-hop-dong-cuoi', $item) }}"
                                       @checked((bool) $item->dieu_chinh_hop_dong_cuoi)>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-ds-nguoi-dung-vai-tro"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalDsNguoiDungVaiTro"
                                       data-url="{{ route('admin.he-thong.vai-tro.nguoi-dung', $item) }}"
                                       data-ten="{{ e($item->ten_vai_tro ?? '') }}"
                                       data-ma="{{ e($item->ma_vai_tro ?? '') }}">
                                        <i class="fa-solid fa-users me-2"></i> Xem DS người dùng
                                    </a>
                                    <a class="dropdown-item btn-sua-vai-tro"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaVaiTro"
                                       data-url="{{ route('admin.he-thong.vai-tro.update', $item) }}"
                                       data-id="{{ $item->id }}"
                                       data-ma="{{ e($item->ma_vai_tro ?? '') }}"
                                       data-ten="{{ e($item->ten_vai_tro ?? '') }}"
                                       data-mo-ta="{{ e($item->mo_ta ?? '') }}"
                                       data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                       data-ds-menu="{{ json_encode($item->ds_menu ?? []) }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-vt-{{ $item->id }}" action="{{ route('admin.he-thong.vai-tro.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-vai-tro" data-form-id="form-xoa-vt-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Chưa có dữ liệu vai trò.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="vai trò" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới vai trò --}}
<div class="modal fade" id="modalThemVaiTro" tabindex="-1" aria-labelledby="modalThemVaiTroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-vai-tro-form">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemVaiTroLabel">Thêm vai trò mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.he-thong.vai-tro.store') }}" method="POST" id="formThemVaiTro">
                @csrf
                @if($errors->any() && ! $isEditValidation)
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
                    <div class="row g-3 vai-tro-form-fields">
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="them_ma_vai_tro">Mã vai trò <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ma_vai_tro" name="ma_vai_tro" value="{{ old('ma_vai_tro') }}" placeholder="Ví dụ: 1, 2, 3" inputmode="numeric" pattern="[0-9]+" title="Mã vai trò phải là số" required>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="them_ten_vai_tro">Tên vai trò <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ten_vai_tro" name="ten_vai_tro" value="{{ old('ten_vai_tro') }}" placeholder="Nhập tên vai trò" required>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="them_mo_ta">Mô tả</label>
                            <input type="text" class="form-control" id="them_mo_ta" name="mo_ta" value="{{ old('mo_ta') }}" placeholder="Mô tả ngắn...">
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                            <input type="text" class="form-control" id="them_ghi_chu" name="ghi_chu" value="{{ old('ghi_chu') }}" placeholder="Ghi chú...">
                        </div>
                        <div class="col-12">
                            @include('admin.components.menu-permissions-list', [
                                'adminGetRoutes' => $adminGetRoutes ?? [],
                                'checkAllId' => 'themPermCheckAll',
                                'checkboxClass' => 'them-perm-checkbox',
                            ])
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

{{-- Modal Chỉnh sửa vai trò --}}
<div class="modal fade" id="modalSuaVaiTro" tabindex="-1" aria-labelledby="modalSuaVaiTroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-vai-tro-form">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaVaiTroLabel">Chỉnh sửa vai trò</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaVaiTro" method="POST" action="{{ $isEditValidation && old('vai_tro_id') ? route('admin.he-thong.vai-tro.update', old('vai_tro_id')) : '' }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="vai_tro_id" id="sua_vai_tro_id" value="{{ old('vai_tro_id') }}">
                @if($errors->any() && $isEditValidation)
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
                    <div class="row g-3 vai-tro-form-fields">
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="sua_ma_vai_tro">Mã vai trò</label>
                            <input type="text" class="form-control bg-light" id="sua_ma_vai_tro" value="{{ $isEditValidation ? old('ma_vai_tro') : '' }}" placeholder="Ví dụ: 1, 2, 3" readonly tabindex="-1" aria-readonly="true" title="Mã vai trò không thể thay đổi khi chỉnh sửa">
                            <div class="form-text">Không thể thay đổi sau khi tạo.</div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="sua_ten_vai_tro">Tên vai trò <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ten_vai_tro" name="ten_vai_tro" value="{{ $isEditValidation ? old('ten_vai_tro') : '' }}" placeholder="Nhập tên vai trò" required>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="sua_mo_ta">Mô tả</label>
                            <input type="text" class="form-control" id="sua_mo_ta" name="mo_ta" value="{{ $isEditValidation ? old('mo_ta') : '' }}" placeholder="Mô tả ngắn...">
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                            <input type="text" class="form-control" id="sua_ghi_chu" name="ghi_chu" value="{{ $isEditValidation ? old('ghi_chu') : '' }}" placeholder="Ghi chú...">
                        </div>
                        <div class="col-12">
                            @include('admin.components.menu-permissions-list', [
                                'adminGetRoutes' => $adminGetRoutes ?? [],
                                'checkAllId' => 'suaPermCheckAll',
                                'checkboxClass' => 'sua-perm-checkbox',
                            ])
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

{{-- Modal danh sách người dùng theo vai trò --}}
<div class="modal fade" id="modalDsNguoiDungVaiTro" tabindex="-1" aria-labelledby="modalDsNguoiDungVaiTroLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-ds-nguoi-dung-vt">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDsNguoiDungVaiTroLabel">Danh sách người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div id="dvvtLoading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Đang tải danh sách người dùng...</p>
                </div>
                <div id="dvvtError" class="alert alert-danger d-none" role="alert"></div>
                <div id="dvvtContent" class="d-none">
                    <div class="table-responsive text-nowrap table-wrapper-bordered">
                        <table class="table table-hover table-bordered mb-0" id="dvvtTable">
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
                                    <th>Phòng ban</th>
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
                            <tbody id="dvvtTableBody"></tbody>
                        </table>
                    </div>
                    <p id="dvvtEmpty" class="text-center text-muted py-4 d-none mb-0">Chưa có người dùng với vai trò này.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa vai trò --}}
<div class="modal fade" id="modalXacNhanXoaVaiTro" tabindex="-1" aria-labelledby="modalXacNhanXoaVaiTroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaVaiTroLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa vai trò này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaVaiTro">
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
    min-width: 780px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
#modalThemVaiTro .modal-dialog.modal-vai-tro-form,
#modalSuaVaiTro .modal-dialog.modal-vai-tro-form {
    max-width: min(960px, 94vw);
}
#modalThemVaiTro .vai-tro-form-fields .form-label,
#modalSuaVaiTro .vai-tro-form-fields .form-label {
    font-size: 0.8125rem;
    margin-bottom: 0.25rem;
}
#modalXacNhanXoaVaiTro .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
#modalThemVaiTro .menu-perm-label,
#modalSuaVaiTro .menu-perm-label {
    cursor: pointer;
}
#modalDsNguoiDungVaiTro .modal-ds-nguoi-dung-vt {
    max-width: 95vw;
    width: 90%;
}
#modalDsNguoiDungVaiTro .table-wrapper-bordered .table {
    min-width: 1400px;
}
#modalDsNguoiDungVaiTro .dvvt-avatar {
    width: 36px;
    height: 36px;
    object-fit: cover;
}
#modalDsNguoiDungVaiTro .dvvt-avatar-placeholder {
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

    var oldPermissions = @json($oldPermissions);

    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function initMenuPermissions(modal, checkAllId, checkboxClass) {
        if (!modal) return;
        var checkAll = document.getElementById(checkAllId);
        if (!checkAll) return;

        function getCheckboxes() {
            return modal.querySelectorAll('.' + checkboxClass);
        }

        function updateCheckAllState() {
            var boxes = getCheckboxes();
            var total = boxes.length;
            var checked = Array.from(boxes).filter(function (cb) { return cb.checked; }).length;
            checkAll.checked = total > 0 && checked === total;
            checkAll.indeterminate = total > 0 && checked > 0 && checked < total;
        }

        function setPermissions(dsMenu) {
            var list = Array.isArray(dsMenu) ? dsMenu : [];
            getCheckboxes().forEach(function (cb) {
                cb.checked = list.indexOf(cb.value) !== -1;
            });
            updateCheckAllState();
        }

        checkAll.addEventListener('change', function () {
            var checked = this.checked;
            getCheckboxes().forEach(function (cb) { cb.checked = checked; });
            checkAll.indeterminate = false;
        });

        modal.addEventListener('change', function (e) {
            if (e.target && e.target.classList.contains(checkboxClass)) {
                updateCheckAllState();
            }
        });

        modal._setMenuPermissions = setPermissions;
        modal._updateMenuCheckAll = updateCheckAllState;
    }

    var modalThem = document.getElementById('modalThemVaiTro');
    var modalSua = document.getElementById('modalSuaVaiTro');
    var modalDsNd = document.getElementById('modalDsNguoiDungVaiTro');
    [modalThem, modalSua, modalDsNd].forEach(function(modal) {
        if (modal) modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
    });

    initMenuPermissions(modalThem, 'themPermCheckAll', 'them-perm-checkbox');
    initMenuPermissions(modalSua, 'suaPermCheckAll', 'sua-perm-checkbox');

    if (modalThem) {
        modalThem.addEventListener('show.bs.modal', function(e) {
            if (e.relatedTarget && e.relatedTarget.getAttribute('data-bs-target') === '#modalThemVaiTro' && modalThem._setMenuPermissions) {
                modalThem._setMenuPermissions([]);
            }
        });
    }

    @if($errors->any() && ! $isEditValidation)
    if (modalThem) {
        if (modalThem._setMenuPermissions) {
            modalThem._setMenuPermissions(oldPermissions);
        }
        bootstrap.Modal.getOrCreateInstance(modalThem).show();
    }
    @endif

    @if($errors->any() && $isEditValidation)
    if (modalSua) {
        if (modalSua._setMenuPermissions) {
            modalSua._setMenuPermissions(oldPermissions);
        }
        bootstrap.Modal.getOrCreateInstance(modalSua).show();
    }
    @endif

    var formSua = document.getElementById('formSuaVaiTro');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-vai-tro')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            var idInput = document.getElementById('sua_vai_tro_id');
            if (idInput) idInput.value = btn.getAttribute('data-id') || '';
            document.getElementById('sua_ma_vai_tro').value = btn.getAttribute('data-ma') || '';
            document.getElementById('sua_ten_vai_tro').value = btn.getAttribute('data-ten') || '';
            document.getElementById('sua_mo_ta').value = btn.getAttribute('data-mo-ta') || '';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
            var dsMenuJson = btn.getAttribute('data-ds-menu') || '[]';
            var dsMenu = [];
            try { dsMenu = JSON.parse(dsMenuJson); } catch (err) {}
            if (modalSua._setMenuPermissions) {
                modalSua._setMenuPermissions(dsMenu);
            }
        });
    }

    // Modal danh sách người dùng theo vai trò
    var dvvtLoading = document.getElementById('dvvtLoading');
    var dvvtError = document.getElementById('dvvtError');
    var dvvtContent = document.getElementById('dvvtContent');
    var dvvtTableBody = document.getElementById('dvvtTableBody');
    var dvvtEmpty = document.getElementById('dvvtEmpty');
    var dvvtTable = document.getElementById('dvvtTable');

    function dvvtEsc(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function dvvtCell(val) {
        var v = val != null && String(val).trim() !== '' ? String(val) : '—';
        return dvvtEsc(v);
    }

    function dvvtReset() {
        if (dvvtError) {
            dvvtError.classList.add('d-none');
            dvvtError.textContent = '';
        }
        if (dvvtContent) dvvtContent.classList.add('d-none');
        if (dvvtLoading) dvvtLoading.classList.add('d-none');
        if (dvvtTableBody) dvvtTableBody.innerHTML = '';
        if (dvvtEmpty) dvvtEmpty.classList.add('d-none');
        if (dvvtTable) dvvtTable.classList.remove('d-none');
    }

    function dvvtRenderAvatar(row) {
        var name = row.name || 'N';
        var initial = dvvtEsc(name.charAt(0).toUpperCase());
        if (row.hinh_anh) {
            return '<img src="' + dvvtEsc(row.hinh_anh) + '" alt="" class="rounded-circle dvvt-avatar">';
        }
        return '<div class="avatar rounded-circle bg-label-primary d-flex align-items-center justify-content-center dvvt-avatar-placeholder">'
            + '<span class="text-primary fw-medium">' + initial + '</span></div>';
    }

    function dvvtRenderRows(items) {
        if (!dvvtTableBody) return;
        dvvtTableBody.innerHTML = '';
        if (!items || !items.length) {
            if (dvvtTable) dvvtTable.classList.add('d-none');
            if (dvvtEmpty) dvvtEmpty.classList.remove('d-none');
            return;
        }
        if (dvvtTable) dvvtTable.classList.remove('d-none');
        if (dvvtEmpty) dvvtEmpty.classList.add('d-none');
        items.forEach(function(row, idx) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="text-center">' + (idx + 1) + '</td>' +
                '<td>' + dvvtRenderAvatar(row) + '</td>' +
                '<td><span class="fw-medium">' + dvvtCell(row.name) + '</span></td>' +
                '<td>' + dvvtCell(row.gioi_tinh) + '</td>' +
                '<td>' + dvvtCell(row.ngay_sinh) + '</td>' +
                '<td>' + dvvtCell(row.cccd) + '</td>' +
                '<td>' + dvvtCell(row.email) + '</td>' +
                '<td>' + dvvtCell(row.phone) + '</td>' +
                '<td>' + dvvtCell(row.phong_ban) + '</td>' +
                '<td>' + dvvtCell(row.vi_tri_lam_viec) + '</td>' +
                '<td>' + dvvtCell(row.ngay_vao_cong_ty) + '</td>' +
                '<td>' + dvvtCell(row.ngay_ky_hop_dong) + '</td>' +
                '<td class="text-end">' + dvvtCell(row.luong_co_ban) + '</td>' +
                '<td class="text-end">' + dvvtCell(row.luong_tang_ca) + '</td>' +
                '<td>' + dvvtCell(row.ngan_hang) + '</td>' +
                '<td>' + dvvtCell(row.chi_nhanh) + '</td>' +
                '<td>' + dvvtCell(row.so_tai_khoan) + '</td>';
            dvvtTableBody.appendChild(tr);
        });
    }

    if (modalDsNd) {
        modalDsNd.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-ds-nguoi-dung-vai-tro')) return;

            var url = btn.getAttribute('data-url') || '';
            var ten = btn.getAttribute('data-ten') || '';

            dvvtReset();
            var titleEl = document.getElementById('modalDsNguoiDungVaiTroLabel');
            if (titleEl) titleEl.textContent = 'Danh sách người dùng — ' + (ten || 'Vai trò');

            if (!url) {
                if (dvvtError) {
                    dvvtError.textContent = 'Không xác định được vai trò.';
                    dvvtError.classList.remove('d-none');
                }
                return;
            }

            if (dvvtLoading) dvvtLoading.classList.remove('d-none');

            RestApi.get(url)
                .then(function(res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    if (dvvtLoading) dvvtLoading.classList.add('d-none');
                    if (dvvtContent) dvvtContent.classList.remove('d-none');
                    var items = res.data && Array.isArray(res.data.items) ? res.data.items : [];
                    dvvtRenderRows(items);
                })
                .catch(function(err) {
                    if (dvvtLoading) dvvtLoading.classList.add('d-none');
                    if (dvvtError) {
                        dvvtError.textContent = 'Không tải được danh sách người dùng. ' + (err && err.message ? err.message : '');
                        dvvtError.classList.remove('d-none');
                    }
                });
        });

        modalDsNd.addEventListener('hidden.bs.modal', dvvtReset);
    }

    var token = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = token ? token.getAttribute('content') : @json(csrf_token());
    document.querySelectorAll('.switch-dieu-chinh-hop-dong-cuoi').forEach(function (switchEl) {
        switchEl.addEventListener('change', function () {
            var target = this;
            var url = target.getAttribute('data-url');
            if (!url) {
                target.checked = !target.checked;
                return;
            }

            var oldValue = !target.checked;
            target.disabled = true;
            RestApi.patch(url, {
                dieu_chinh_hop_dong_cuoi: target.checked ? 1 : 0
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status);
                    }
                })
                .catch(function () {
                    target.checked = oldValue;
                })
                .finally(function () {
                    target.disabled = false;
                });
        });
    });

    var modalXoa = document.getElementById('modalXacNhanXoaVaiTro');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaVaiTro');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        document.querySelectorAll('.btn-xoa-vai-tro').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                bootstrap.Modal.getOrCreateInstance(modalXoa).show();
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
