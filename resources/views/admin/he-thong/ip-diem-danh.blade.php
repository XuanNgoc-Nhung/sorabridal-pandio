@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\IpDiemDanh::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('search')
        || request()->filled('trang_thai')
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
        <form action="{{ route('admin.he-thong.ip-diem-danh') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên IP, địa chỉ hoặc ghi chú</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập từ khóa tìm kiếm...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_trang_thai">Cho phép điểm danh</label>
                    <select class="select2-admin form-select" id="filter_trang_thai" name="trang_thai" data-placeholder="Tất cả">
                        <option value="">Tất cả</option>
                        @foreach(\App\Models\IpDiemDanh::TRANG_THAI_LABELS as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('trang_thai') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\IpDiemDanh::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.he-thong.ip-diem-danh') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách IP điểm danh</span>
            <button type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalThemIpDiemDanh">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        <th>Tên IP</th>
                        <th>Địa chỉ IP</th>
                        <th>Ghi chú</th>
                        <th class="text-center">Cho phép điểm danh</th>
                        <th>Thời gian tạo</th>
                        <th class="text-center" style="width: 88px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSachIpDiemDanh as $index => $item)
                    @php
                        $choPhepDiemDanh = (int) ($item->trang_thai ?? 0) === \App\Models\IpDiemDanh::TRANG_THAI_DANG_HOAT_DONG;
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSachIpDiemDanh->currentPage() - 1) * $danhSachIpDiemDanh->perPage() + $index + 1 }}</td>
                        <td class="text-wrap">
                            <div class="fw-medium">{{ $item->ten_ip }}</div>
                        </td>
                        <td class="text-nowrap"><code>{{ $item->dia_chi_ip }}</code></td>
                        <td class="text-wrap ip-diem-danh-ghi-chu">{{ $item->ghi_chu ?: '—' }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center mb-0">
                                <input type="checkbox"
                                       class="form-check-input switch-cho-phep-diem-danh-ip"
                                       role="switch"
                                       id="switch-cho-phep-diem-danh-ip-{{ $item->id }}"
                                       data-url="{{ route('admin.he-thong.ip-diem-danh.update-trang-thai', $item) }}"
                                       @checked($choPhepDiemDanh)
                                       title="{{ $choPhepDiemDanh ? 'Cho phép điểm danh' : 'Không cho phép điểm danh' }}">
                            </div>
                        </td>
                        <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-center text-nowrap dpc-action-col">
                            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                <span class="d-inline-flex"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="Chỉnh sửa">
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-primary btn-dpc-edit btn-sua-ip-diem-danh"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSuaIpDiemDanh"
                                            data-url="{{ route('admin.he-thong.ip-diem-danh.update', $item) }}"
                                            data-ten="{{ e($item->ten_ip) }}"
                                            data-dia-chi="{{ e($item->dia_chi_ip) }}"
                                            data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                            aria-label="Chỉnh sửa">
                                        <i class="bi bi-pencil-square dpc-action-icon" aria-hidden="true"></i>
                                    </button>
                                </span>

                                <form id="form-xoa-ip-diem-danh-{{ $item->id }}" action="{{ route('admin.he-thong.ip-diem-danh.destroy', $item) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-danger btn-xoa-ip-diem-danh"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        data-form-id="form-xoa-ip-diem-danh-{{ $item->id }}"
                                        data-ten="{{ e($item->ten_ip) }}"
                                        title="Xóa"
                                        aria-label="Xóa">
                                    <i class="bi bi-x-circle-fill dpc-action-icon" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có IP điểm danh nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSachIpDiemDanh" label="IP điểm danh" />
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemIpDiemDanh" tabindex="-1" aria-labelledby="modalThemIpDiemDanhLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-ip-diem-danh">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemIpDiemDanhLabel">Thêm IP điểm danh mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form action="{{ route('admin.he-thong.ip-diem-danh.store') }}" method="POST">
                @csrf

                @if($errors->any() && !old('_method'))
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
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_ten_ip">Tên IP <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="them_ten_ip"
                                   name="ten_ip"
                                   value="{{ old('ten_ip') }}"
                                   placeholder="Ví dụ: WiFi văn phòng"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_dia_chi_ip">Địa chỉ IP <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="them_dia_chi_ip"
                                   name="dia_chi_ip"
                                   value="{{ old('dia_chi_ip') }}"
                                   placeholder="Ví dụ: 192.168.1.1"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                            <textarea class="form-control"
                                      id="them_ghi_chu"
                                      name="ghi_chu"
                                      rows="3"
                                      placeholder="Nhập ghi chú ngắn">{{ old('ghi_chu') }}</textarea>
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

<div class="modal fade" id="modalSuaIpDiemDanh" tabindex="-1" aria-labelledby="modalSuaIpDiemDanhLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-ip-diem-danh">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaIpDiemDanhLabel">Chỉnh sửa IP điểm danh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form id="formSuaIpDiemDanh" method="POST" action="">
                @csrf
                @method('PUT')

                @if($errors->any() && old('_method') === 'PUT')
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
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_ten_ip">Tên IP <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="sua_ten_ip"
                                   name="ten_ip"
                                   placeholder="Ví dụ: WiFi văn phòng"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_dia_chi_ip">Địa chỉ IP <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="sua_dia_chi_ip"
                                   name="dia_chi_ip"
                                   placeholder="Ví dụ: 192.168.1.1"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                            <textarea class="form-control"
                                      id="sua_ghi_chu"
                                      name="ghi_chu"
                                      rows="3"
                                      placeholder="Nhập ghi chú ngắn"></textarea>
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

<div class="modal fade" id="modalXacNhanXoaIpDiemDanh" tabindex="-1" aria-labelledby="modalXacNhanXoaIpDiemDanhLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaIpDiemDanhLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa IP điểm danh <span class="fw-medium" id="tenIpDiemDanhCanXoa">này</span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaIpDiemDanh">
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
.dpc-action-col .btn-icon .dpc-action-icon {
    font-size: 1.25rem;
    line-height: 1;
}
.dpc-action-col .btn-icon .dpc-action-icon:not([class*="-fill"]) {
    -webkit-text-stroke: 0.35px currentColor;
}
.dpc-action-col .btn-dpc-edit,
.dpc-action-col .btn-dpc-edit .dpc-action-icon {
    color: var(--bs-primary, #696cff);
    opacity: 1;
}
.dpc-action-col .btn-dpc-edit:hover,
.dpc-action-col .btn-dpc-edit:focus,
.dpc-action-col .btn-dpc-edit:hover .dpc-action-icon,
.dpc-action-col .btn-dpc-edit:focus .dpc-action-icon {
    color: var(--bs-primary, #696cff);
    opacity: 0.88;
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
    min-width: 960px;
}

.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
    vertical-align: middle;
}

.modal-ip-diem-danh {
    max-width: 720px;
}

.modal-confirm-xoa {
    max-width: 420px;
}

.ip-diem-danh-ghi-chu {
    min-width: 220px;
    white-space: normal;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var CSRF_TOKEN = @json(csrf_token());
    var TRANG_THAI_CHO_PHEP = {{ \App\Models\IpDiemDanh::TRANG_THAI_DANG_HOAT_DONG }};

    function capNhatTrangThaiIpDiemDanh(switchEl) {
        if (!switchEl) {
            return;
        }

        var url = switchEl.getAttribute('data-url');
        if (!url) {
            return;
        }

        var trangThai = switchEl.checked ? TRANG_THAI_CHO_PHEP : 0;
        var trangThaiCu = switchEl.checked ? 0 : TRANG_THAI_CHO_PHEP;

        switchEl.disabled = true;

        RestApi.patch(url, { trang_thai: trangThai })
            .then(function(res) {
                if (!res.ok || !res.data || !res.data.success) {
                    throw new Error('update_failed');
                }
                switchEl.title = switchEl.checked ? 'Cho phép điểm danh' : 'Không cho phép điểm danh';
            })
            .catch(function() {
                switchEl.checked = trangThaiCu === TRANG_THAI_CHO_PHEP;
            })
            .finally(function() {
                switchEl.disabled = false;
            });
    }

    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    var modalThemIpDiemDanh = document.getElementById('modalThemIpDiemDanh');
    var modalSuaIpDiemDanh = document.getElementById('modalSuaIpDiemDanh');
    var modalXacNhanXoaIpDiemDanh = document.getElementById('modalXacNhanXoaIpDiemDanh');
    var btnXacNhanXoaIpDiemDanh = document.getElementById('btnXacNhanXoaIpDiemDanh');
    var tenIpDiemDanhCanXoa = document.getElementById('tenIpDiemDanhCanXoa');
    var formSuaIpDiemDanh = document.getElementById('formSuaIpDiemDanh');
    var formIdCanXoa = null;

    [modalThemIpDiemDanh, modalSuaIpDiemDanh, modalXacNhanXoaIpDiemDanh].forEach(function(modal) {
        if (modal) {
            modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        }
    });

    @if($errors->any() && !old('_method'))
    if (modalThemIpDiemDanh) {
        bootstrap.Modal.getOrCreateInstance(modalThemIpDiemDanh).show();
    }
    @endif

    @if($errors->any() && old('_method') === 'PUT')
    if (modalSuaIpDiemDanh) {
        bootstrap.Modal.getOrCreateInstance(modalSuaIpDiemDanh).show();
    }
    @endif

    document.querySelectorAll('.btn-sua-ip-diem-danh').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!formSuaIpDiemDanh) {
                return;
            }

            formSuaIpDiemDanh.action = this.getAttribute('data-url') || '';
            document.getElementById('sua_ten_ip').value = this.getAttribute('data-ten') || '';
            document.getElementById('sua_dia_chi_ip').value = this.getAttribute('data-dia-chi') || '';
            document.getElementById('sua_ghi_chu').value = this.getAttribute('data-ghi-chu') || '';
        });
    });

    document.querySelectorAll('.switch-cho-phep-diem-danh-ip').forEach(function(switchEl) {
        switchEl.addEventListener('change', function() {
            capNhatTrangThaiIpDiemDanh(this);
        });
    });

    document.querySelectorAll('.btn-xoa-ip-diem-danh').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formIdCanXoa = this.getAttribute('data-form-id');

            if (tenIpDiemDanhCanXoa) {
                tenIpDiemDanhCanXoa.textContent = this.getAttribute('data-ten') || 'này';
            }

            if (modalXacNhanXoaIpDiemDanh) {
                bootstrap.Modal.getOrCreateInstance(modalXacNhanXoaIpDiemDanh).show();
            }
        });
    });

    if (btnXacNhanXoaIpDiemDanh) {
        btnXacNhanXoaIpDiemDanh.addEventListener('click', function() {
            if (!formIdCanXoa) {
                return;
            }

            var form = document.getElementById(formIdCanXoa);
            if (form) {
                form.submit();
            }
        });
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    }
});
</script>
@endpush
@endsection
