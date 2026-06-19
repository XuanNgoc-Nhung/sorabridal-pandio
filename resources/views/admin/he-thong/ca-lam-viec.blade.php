@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\CaLamViec::SAP_XEP_MAC_DINH;
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
        <form action="{{ route('admin.he-thong.ca-lam-viec') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên ca</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên ca...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\CaLamViec::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.he-thong.ca-lam-viec') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách ca làm việc</span>
            <button type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalThemCaLamViec">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        <th>Tên ca</th>
                        <th>Giờ bắt đầu</th>
                        <th>Giờ kết thúc</th>
                        <th>Thời gian tạo</th>
                        <th class="text-center" style="width: 88px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td class="fw-medium">{{ $item->ten_ca }}</td>
                        <td class="text-nowrap">{{ $item->gioBatDauHienThi() }}</td>
                        <td class="text-nowrap">{{ $item->gioKetThucHienThi() }}</td>
                        <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-center text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-primary btn-sua-ca-lam-viec"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSuaCaLamViec"
                                        data-url="{{ route('admin.he-thong.ca-lam-viec.update', $item) }}"
                                        data-ten="{{ e($item->ten_ca) }}"
                                        data-gio-bat-dau="{{ e($item->gioBatDauHienThi() !== '—' ? $item->gioBatDauHienThi() : '') }}"
                                        data-gio-ket-thuc="{{ e($item->gioKetThucHienThi() !== '—' ? $item->gioKetThucHienThi() : '') }}"
                                        title="Chỉnh sửa"
                                        aria-label="Chỉnh sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <form id="form-xoa-ca-lam-viec-{{ $item->id }}" action="{{ route('admin.he-thong.ca-lam-viec.destroy', $item) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-danger btn-xoa-ca-lam-viec"
                                        data-form-id="form-xoa-ca-lam-viec-{{ $item->id }}"
                                        data-ten="{{ e($item->ten_ca) }}"
                                        title="Xóa"
                                        aria-label="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có ca làm việc nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSach" label="ca làm việc" />
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemCaLamViec" tabindex="-1" aria-labelledby="modalThemCaLamViecLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-ca-lam-viec">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemCaLamViecLabel">Thêm ca làm việc mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form action="{{ route('admin.he-thong.ca-lam-viec.store') }}" method="POST">
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
                        <div class="col-12">
                            <label class="form-label" for="them_ten_ca">Tên ca <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="them_ten_ca"
                                   name="ten_ca"
                                   value="{{ old('ten_ca') }}"
                                   placeholder="Ví dụ: Ca sáng"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_gio_bat_dau">Giờ bắt đầu <span class="text-danger">*</span></label>
                            <div class="ca-lam-viec-time-wrap">
                                <input type="text"
                                       class="form-control js-ca-lam-viec-time"
                                       id="them_gio_bat_dau"
                                       name="gio_bat_dau"
                                       value="{{ old('gio_bat_dau') }}"
                                       placeholder="HH:mm"
                                       autocomplete="off"
                                       required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_gio_ket_thuc">Giờ kết thúc <span class="text-danger">*</span></label>
                            <div class="ca-lam-viec-time-wrap">
                                <input type="text"
                                       class="form-control js-ca-lam-viec-time"
                                       id="them_gio_ket_thuc"
                                       name="gio_ket_thuc"
                                       value="{{ old('gio_ket_thuc') }}"
                                       placeholder="HH:mm"
                                       autocomplete="off"
                                       required>
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

<div class="modal fade" id="modalSuaCaLamViec" tabindex="-1" aria-labelledby="modalSuaCaLamViecLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-ca-lam-viec">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaCaLamViecLabel">Chỉnh sửa ca làm việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form id="formSuaCaLamViec" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="ca_lam_viec_id" id="sua_ca_lam_viec_id" value="{{ old('ca_lam_viec_id') }}">

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
                        <div class="col-12">
                            <label class="form-label" for="sua_ten_ca">Tên ca <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="sua_ten_ca"
                                   name="ten_ca"
                                   placeholder="Ví dụ: Ca sáng"
                                   required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_gio_bat_dau">Giờ bắt đầu <span class="text-danger">*</span></label>
                            <div class="ca-lam-viec-time-wrap">
                                <input type="text"
                                       class="form-control js-ca-lam-viec-time"
                                       id="sua_gio_bat_dau"
                                       name="gio_bat_dau"
                                       placeholder="HH:mm"
                                       autocomplete="off"
                                       required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_gio_ket_thuc">Giờ kết thúc <span class="text-danger">*</span></label>
                            <div class="ca-lam-viec-time-wrap">
                                <input type="text"
                                       class="form-control js-ca-lam-viec-time"
                                       id="sua_gio_ket_thuc"
                                       name="gio_ket_thuc"
                                       placeholder="HH:mm"
                                       autocomplete="off"
                                       required>
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

<div class="modal fade" id="modalXacNhanXoaCaLamViec" tabindex="-1" aria-labelledby="modalXacNhanXoaCaLamViecLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaCaLamViecLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa ca làm việc <span class="fw-medium" id="tenCaLamViecCanXoa">này</span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaCaLamViec">
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
    min-width: 640px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
    vertical-align: middle;
}
.modal-ca-lam-viec {
    max-width: 560px;
}
.ca-lam-viec-time-wrap {
    position: relative;
}
.ca-lam-viec-time-wrap .flatpickr-calendar {
    position: relative !important;
    top: 0 !important;
    left: 0 !important;
    right: auto !important;
    width: 100% !important;
    max-width: 100%;
    margin: 0.375rem 0 0;
    box-shadow: none;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
}
.ca-lam-viec-time-wrap .flatpickr-time {
    max-height: none;
    border-top: none;
}
.modal-confirm-xoa {
    max-width: 420px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function destroyModalTimePickers(modalEl) {
        if (!modalEl) {
            return;
        }

        modalEl.querySelectorAll('.js-ca-lam-viec-time').forEach(function(el) {
            if (el._flatpickr) {
                el._flatpickr.destroy();
            }
        });
    }

    function initModalTimePickers(modalEl) {
        if (!modalEl || typeof flatpickr === 'undefined') {
            return;
        }

        modalEl.querySelectorAll('.js-ca-lam-viec-time').forEach(function(el) {
            if (el._flatpickr) {
                el._flatpickr.destroy();
            }

            flatpickr(el, {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                minuteIncrement: 5,
                allowInput: true,
                disableMobile: true,
                static: true,
                defaultDate: el.value || null
            });
        });
    }

    function bindCaLamViecModalTimePickers(modalEl) {
        if (!modalEl) {
            return;
        }

        modalEl.addEventListener('shown.bs.modal', function() {
            initModalTimePickers(modalEl);
        });
        modalEl.addEventListener('hidden.bs.modal', function() {
            destroyModalTimePickers(modalEl);
        });
    }

    var modalThemCaLamViec = document.getElementById('modalThemCaLamViec');
    var modalSuaCaLamViec = document.getElementById('modalSuaCaLamViec');

    [modalThemCaLamViec, modalSuaCaLamViec].forEach(function(modal) {
        if (modal) {
            modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
            bindCaLamViecModalTimePickers(modal);
        }
    });

    @if($errors->any() && !old('_method'))
    if (modalThemCaLamViec) {
        bootstrap.Modal.getOrCreateInstance(modalThemCaLamViec).show();
    }
    @endif

    @if($errors->any() && old('_method') === 'PUT')
    var formSuaLoi = document.getElementById('formSuaCaLamViec');
    if (modalSuaCaLamViec && formSuaLoi) {
        @if(old('ca_lam_viec_id'))
        formSuaLoi.action = @json(route('admin.he-thong.ca-lam-viec.update', old('ca_lam_viec_id')));
        @endif
        document.getElementById('sua_ten_ca').value = @json(old('ten_ca', ''));
        document.getElementById('sua_gio_bat_dau').value = @json(old('gio_bat_dau', ''));
        document.getElementById('sua_gio_ket_thuc').value = @json(old('gio_ket_thuc', ''));
        bootstrap.Modal.getOrCreateInstance(modalSuaCaLamViec).show();
    }
    @endif

    var formSua = document.getElementById('formSuaCaLamViec');
    if (modalSuaCaLamViec && formSua) {
        modalSuaCaLamViec.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-ca-lam-viec')) {
                return;
            }

            var url = btn.getAttribute('data-url');
            if (url) {
                formSua.action = url;
                var match = url.match(/\/ca-lam-viec\/(\d+)/);
                var idInput = document.getElementById('sua_ca_lam_viec_id');
                if (match && idInput) {
                    idInput.value = match[1];
                }
            }

            document.getElementById('sua_ten_ca').value = btn.getAttribute('data-ten') || '';
            document.getElementById('sua_gio_bat_dau').value = btn.getAttribute('data-gio-bat-dau') || '';
            document.getElementById('sua_gio_ket_thuc').value = btn.getAttribute('data-gio-ket-thuc') || '';
        });
    }

    var modalXoa = document.getElementById('modalXacNhanXoaCaLamViec');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaCaLamViec');
    var tenCaCanXoa = document.getElementById('tenCaLamViecCanXoa');
    var formIdCanXoa = null;

    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', cleanupModalBackdrop);

        document.querySelectorAll('.btn-xoa-ca-lam-viec').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (tenCaCanXoa) {
                    tenCaCanXoa.textContent = this.getAttribute('data-ten') || 'này';
                }
                bootstrap.Modal.getOrCreateInstance(modalXoa).show();
            });
        });

        btnXacNhanXoa.addEventListener('click', function() {
            if (formIdCanXoa) {
                var form = document.getElementById(formIdCanXoa);
                if (form) {
                    form.submit();
                }
            }
            var inst = bootstrap.Modal.getInstance(modalXoa);
            if (inst) {
                inst.hide();
            }
            formIdCanXoa = null;
        });
    }
});
</script>
@endpush
@endsection
