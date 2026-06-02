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
        <form action="{{ route('admin.nhan-su.phan-quyen') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên hoặc mã nhân sự</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên hoặc mã nhân sự...">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.nhan-su.phan-quyen') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header">Phân quyền nhân sự</h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Họ tên</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $vaiTroLabel = match((int)($item->role ?? 0)) {
                            \App\Models\User::ROLE_ADMIN => 'Admin',
                            \App\Models\User::ROLE_NHAN_VIEN => 'Nhân viên',
                            \App\Models\User::ROLE_NGUOI_DUNG => 'Người dùng',
                            default => '—',
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $item->name ?? '—' }}</span></td>
                        <td>{{ $item->phone ?? '—' }}</td>
                        <td>{{ $item->email ?? '—' }}</td>
                        <td>{{ $vaiTroLabel }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-phan-quyen" href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalPhanQuyen"
                                       data-user-id="{{ $item->id }}"
                                       data-user-name="{{ $item->name ?? '—' }}"
                                       data-ds-menu="{{ json_encode($item->nhanVien->ds_menu ?? []) }}">
                                        <i class="fa-solid fa-user-shield me-2"></i> Phân quyền
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu nhân sự.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" />
        </div>
    </div>
</div>

{{-- Modal Phân quyền --}}
<div class="modal fade" id="modalPhanQuyen" tabindex="-1" aria-labelledby="modalPhanQuyenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPhanQuyenLabel">
                    <i class="fa-solid fa-user-shield me-2"></i> Phân quyền
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.nhan-su.luu-phan-quyen') }}" method="POST" id="formPhanQuyen">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="modalPhanQuyenUserId" name="user_id" value="">
                    <p class="mb-3">
                        <span class="text-muted">Nhân sự:</span>
                        <strong id="modalPhanQuyenUserName">—</strong>
                    </p>
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                        <span class="small text-muted mb-0">Menu</span>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="permCheckAll" title="Chọn/bỏ chọn tất cả">
                            <label class="form-check-label small" for="permCheckAll">Chọn tất cả</label>
                        </div>
                    </div>
                    <div class="list-group list-group-flush border rounded" style="max-height: 60vh; overflow-y: auto;">
                        @forelse($adminGetRoutes ?? [] as $route)
                        <div class="list-group-item d-flex align-items-start gap-2 py-2 px-3">
                            <input class="form-check-input mt-1 flex-shrink-0 perm-checkbox" type="checkbox"
                                   name="permissions[]" value="{{ $route['name'] }}" id="perm-{{ Str::slug($route['name']) }}">
                            <label class="form-check-label flex-grow-1 mb-0 small cursor-pointer" for="perm-{{ Str::slug($route['name']) }}">
                                <span class="d-block fw-medium text-body">{{ $route['description'] }}</span>
                                <span class="d-block text-muted font-monospace" style="font-size: 0.8rem;">{{ $route['uri'] }}</span>
                            </label>
                        </div>
                        @empty
                        <div class="list-group-item text-muted text-center py-3">Chưa có menu nào trong cấu hình sidebar admin.</div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Lưu phân quyền
                    </button>
                </div>
            </form>
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
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
#modalPhanQuyen .cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('modalPhanQuyen');
    if (!modal) return;

    var checkAll = document.getElementById('permCheckAll');

    function getPermCheckboxes() {
        return modal.querySelectorAll('.perm-checkbox');
    }

    function updateCheckAllState() {
        var boxes = getPermCheckboxes();
        var total = boxes.length;
        var checked = Array.from(boxes).filter(function (cb) { return cb.checked; }).length;
        if (checkAll) {
            checkAll.checked = total > 0 && checked === total;
            checkAll.indeterminate = total > 0 && checked > 0 && checked < total;
        }
    }

    function setAll(checked) {
        getPermCheckboxes().forEach(function (cb) { cb.checked = checked; });
        if (checkAll) checkAll.checked = checked;
        if (checkAll) checkAll.indeterminate = false;
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            setAll(this.checked);
        });
    }
    modal.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('perm-checkbox')) {
            updateCheckAllState();
        }
    });

    modal.addEventListener('show.bs.modal', function (e) {
        var trigger = e.relatedTarget;
        if (trigger && trigger.classList.contains('btn-phan-quyen')) {
            document.getElementById('modalPhanQuyenUserId').value = trigger.getAttribute('data-user-id') || '';
            document.getElementById('modalPhanQuyenUserName').textContent = trigger.getAttribute('data-user-name') || '—';
            var dsMenuJson = trigger.getAttribute('data-ds-menu') || '[]';
            var dsMenu = [];
            try { dsMenu = JSON.parse(dsMenuJson); } catch (err) {}
            modal.querySelectorAll('.perm-checkbox').forEach(function (cb) {
                cb.checked = dsMenu.indexOf(cb.value) !== -1;
            });
            updateCheckAllState();
        }
    });
});
</script>
@endpush
@endsection
