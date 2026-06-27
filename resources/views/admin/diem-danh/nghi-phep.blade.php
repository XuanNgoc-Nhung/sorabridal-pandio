@extends('admin.layouts.app')

@section('content')
@php
    $hasFilter = request()->filled('tu_ngay')
        || request()->filled('den_ngay')
        || request()->filled('loai_nghi_phep')
        || request()->filled('trang_thai');
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
        <form action="{{ route('admin.diem-danh.nghi-phep') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_ngay">Từ ngày</label>
                    <input type="text"
                           class="flatpickr-date-admin form-control"
                           id="tu_ngay"
                           name="tu_ngay"
                           value="{{ request('tu_ngay') }}"
                           placeholder="dd/mm/yyyy"
                           autocomplete="off">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="den_ngay">Đến ngày</label>
                    <input type="text"
                           class="flatpickr-date-admin form-control"
                           id="den_ngay"
                           name="den_ngay"
                           value="{{ request('den_ngay') }}"
                           placeholder="dd/mm/yyyy"
                           autocomplete="off">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_loai_nghi_phep">Loại nghỉ phép</label>
                    <select class="select2-admin form-select" id="filter_loai_nghi_phep" name="loai_nghi_phep" data-placeholder="Tất cả">
                        <option value="">Tất cả</option>
                        @foreach(\App\Models\XinNghiPhep::LOAI_NGHI_PHEP_OPTIONS as $value => $label)
                            <option value="{{ $value }}" @selected(request('loai_nghi_phep') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="filter_trang_thai" name="trang_thai" data-placeholder="Tất cả">
                        <option value="">Tất cả</option>
                        @foreach(\App\Models\XinNghiPhep::TRANG_THAI_OPTIONS as $value => $label)
                            <option value="{{ $value }}" @selected(request('trang_thai') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.diem-danh.nghi-phep') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>{{ ($coQuyenDuyet ?? false) ? 'Danh sách xin nghỉ phép' : 'Đơn xin nghỉ phép của tôi' }}</span>
            <button type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalThemNghiPhep">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered nghi-phep-table-wrap">
            <table class="table table-hover table-bordered mb-0 align-middle nghi-phep-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        @if($coQuyenDuyet ?? false)
                        <th class="nghi-phep-sticky nghi-phep-sticky-nhan-vien" style="min-width: 160px;">Nhân viên</th>
                        @endif
                        <th>Loại nghỉ phép</th>
                        <th>Buổi nghỉ</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Lý do</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Người duyệt</th>
                        <th>Thời gian tạo</th>
                        <th class="text-center" style="min-width: 96px;">Hành động</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach as $index => $item)
                    @php
                        $loaiNv = $item->user?->nhanVien?->loai_nhan_vien ?? '';
                        $loaiNvLabel = filled($loaiNv)
                            ? (\App\Models\NhanVien::LOAI_NHAN_VIEN_OPTIONS[$loaiNv] ?? $loaiNv)
                            : '';
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        @if($coQuyenDuyet ?? false)
                        <td class="text-nowrap nghi-phep-sticky nghi-phep-sticky-nhan-vien">
                            <span class="fw-medium d-inline-flex align-items-center flex-wrap gap-1">
                                <span>{{ $item->user?->name ?? '—' }}</span>
                                @if($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_FULL_TIME)
                                    <i class="fa-solid fa-briefcase nghi-phep-loai-nv-icon text-primary"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="{{ $loaiNvLabel }}"
                                       aria-label="{{ $loaiNvLabel }}"></i>
                                @elseif($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_PART_TIME)
                                    <i class="fa-solid fa-clock nghi-phep-loai-nv-icon text-warning"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="{{ $loaiNvLabel }}"
                                       aria-label="{{ $loaiNvLabel }}"></i>
                                @else
                                    <i class="fa-solid fa-circle-exclamation nghi-phep-loai-nv-icon text-danger"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Chưa phân loại"
                                       aria-label="Chưa phân loại"></i>
                                @endif
                            </span>
                        </td>
                        @endif
                        <td class="text-nowrap">{{ $item->loaiNghiPhepLabel() }}</td>
                        <td class="text-nowrap">{{ $item->buoiNghiLabel() }}</td>
                        <td class="text-nowrap">{{ $item->ngay_bat_dau?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-nowrap">{{ $item->ngay_ket_thuc?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-wrap nghi-phep-ly-do">{{ $item->ly_do ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->trangThaiBadgeClass() }}">{{ $item->trangThaiLabel() }}</span>
                        </td>
                        <td class="text-nowrap">{{ $item->nguoiDuyet?->name ?? '—' }}</td>
                        <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-center text-nowrap nghi-phep-action-col">
                            @if(($coQuyenDuyet ?? false) && $item->trang_thai === \App\Models\XinNghiPhep::TRANG_THAI_CHO_DUYET)
                            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                <form id="form-duyet-nghi-phep-{{ $item->id }}"
                                      action="{{ route('admin.diem-danh.nghi-phep.duyet', $item) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-success btn-xac-nhan-nghi-phep"
                                            data-form-id="form-duyet-nghi-phep-{{ $item->id }}"
                                            data-action="duyet"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Duyệt"
                                            aria-label="Duyệt">
                                        <i class="fa-solid fa-check nghi-phep-action-icon" aria-hidden="true"></i>
                                    </button>
                                </form>
                                <form id="form-tu-choi-nghi-phep-{{ $item->id }}"
                                      action="{{ route('admin.diem-danh.nghi-phep.tu-choi', $item) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-danger btn-xac-nhan-nghi-phep"
                                            data-form-id="form-tu-choi-nghi-phep-{{ $item->id }}"
                                            data-action="tu_choi"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Từ chối"
                                            aria-label="Từ chối">
                                        <i class="fa-solid fa-xmark nghi-phep-action-icon" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                            @elseif(!($coQuyenDuyet ?? false) && $item->coTheXoaBoiChuDon())
                            <form id="form-xoa-nghi-phep-{{ $item->id }}"
                                  action="{{ route('admin.diem-danh.nghi-phep.destroy', $item) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-danger btn-xac-nhan-nghi-phep"
                                        data-form-id="form-xoa-nghi-phep-{{ $item->id }}"
                                        data-action="xoa"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Xóa"
                                        aria-label="Xóa">
                                    <i class="fa-solid fa-trash nghi-phep-action-icon" aria-hidden="true"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ ($coQuyenDuyet ?? false) ? 11 : 10 }}" class="text-center py-4 text-muted">Chưa có đơn xin nghỉ phép nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSach" label="đơn xin nghỉ phép" />
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemNghiPhep" tabindex="-1" aria-labelledby="modalThemNghiPhepLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-nghi-phep">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemNghiPhepLabel">Thêm đơn xin nghỉ phép</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form action="{{ route('admin.diem-danh.nghi-phep.store') }}" method="POST">
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
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_loai_nghi_phep">Loại nghỉ phép <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select"
                                    id="them_loai_nghi_phep"
                                    name="loai_nghi_phep"
                                    required
                                    data-placeholder="Chọn loại nghỉ phép">
                                <option value=""></option>
                                @foreach(\App\Models\XinNghiPhep::LOAI_NGHI_PHEP_OPTIONS as $value => $label)
                                    <option value="{{ $value }}" @selected(old('loai_nghi_phep') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 d-none" id="wrap_ngay_don">
                            <label class="form-label" for="them_ngay_bat_dau">
                                <span id="label_ngay_don">Ngày xin phép</span> <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="flatpickr-date-admin form-control"
                                   id="them_ngay_bat_dau"
                                   name="ngay_bat_dau"
                                   value="{{ old('ngay_bat_dau') }}"
                                   placeholder="dd/mm/yyyy"
                                   autocomplete="off">
                        </div>

                        <div class="col-12 col-md-6 d-none" id="wrap_buoi_nghi">
                            <label class="form-label" for="them_buoi_nghi">Buổi nghỉ <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select"
                                    id="them_buoi_nghi"
                                    name="buoi_nghi"
                                    data-placeholder="Chọn buổi nghỉ">
                                <option value=""></option>
                                @foreach(\App\Models\XinNghiPhep::BUOI_NGHI_OPTIONS as $value => $label)
                                    <option value="{{ $value }}" @selected(old('buoi_nghi') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 d-none" id="wrap_ngay_range">
                            <label class="form-label" for="them_ngay_range">Khoảng ngày nghỉ <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="them_ngay_range"
                                   required
                                   placeholder="dd/mm/yyyy → dd/mm/yyyy"
                                   autocomplete="off">
                            <input type="hidden" id="them_ngay_bat_dau_range" value="{{ old('ngay_bat_dau') }}">
                            <input type="hidden" id="them_ngay_ket_thuc_range" value="{{ old('ngay_ket_thuc') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="them_ly_do">Lý do <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="them_ly_do"
                                      name="ly_do"
                                      rows="4"
                                      placeholder="Nhập lý do xin nghỉ phép"
                                      required>{{ old('ly_do') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane me-1"></i> Gửi đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal xác nhận duyệt / từ chối đơn nghỉ phép --}}
<div class="modal fade" id="modalXacNhanNghiPhep" tabindex="-1" aria-labelledby="modalXacNhanNghiPhepLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanNghiPhepLabel">Xác nhận</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="modalXacNhanNghiPhepBody">
                Bạn có chắc muốn thực hiện thao tác này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnXacNhanNghiPhep">
                    <i class="fa-solid fa-check me-1"></i> Xác nhận
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
    min-width: 960px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
    vertical-align: middle;
}
.nghi-phep-table .nghi-phep-sticky {
    position: sticky;
    left: 0;
    z-index: 2;
    background-color: #fff;
    background-clip: padding-box;
}
.nghi-phep-table thead .nghi-phep-sticky {
    z-index: 6;
    background-color: #f8f9fa;
}
.nghi-phep-table.table-hover > tbody > tr:hover > .nghi-phep-sticky {
    background-color: #f5f5f9;
}
[data-bs-theme='dark'] .nghi-phep-table .nghi-phep-sticky {
    background-color: #2f3349;
}
[data-bs-theme='dark'] .nghi-phep-table thead .nghi-phep-sticky {
    background-color: #353a52;
}
[data-bs-theme='dark'] .nghi-phep-table.table-hover > tbody > tr:hover > .nghi-phep-sticky {
    background-color: #3a3f5c;
}
.nghi-phep-table .nghi-phep-sticky-nhan-vien {
    min-width: 160px;
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.08);
}
[data-bs-theme='dark'] .nghi-phep-table .nghi-phep-sticky-nhan-vien {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.35);
}
.modal-nghi-phep {
    max-width: 720px;
}
.nghi-phep-ly-do {
    min-width: 200px;
    white-space: normal;
}
.nghi-phep-action-col .btn-icon .nghi-phep-action-icon {
    font-size: 1.125rem;
    line-height: 1;
}
.nghi-phep-loai-nv-icon { font-size: 0.8rem; cursor: help; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var LOAI_DI_MUON = @json(\App\Models\XinNghiPhep::LOAI_DI_MUON);
    var LOAI_VE_SOM = @json(\App\Models\XinNghiPhep::LOAI_VE_SOM);
    var LOAI_NUA_NGAY = @json(\App\Models\XinNghiPhep::LOAI_NUA_NGAY);
    var LOAI_CA_NGAY = @json(\App\Models\XinNghiPhep::LOAI_CA_NGAY);
    var LOAI_NHIEU_NGAY = @json(\App\Models\XinNghiPhep::LOAI_NHIEU_NGAY);

    var modalThemNghiPhep = document.getElementById('modalThemNghiPhep');
    var formThemNghiPhep = modalThemNghiPhep ? modalThemNghiPhep.querySelector('form') : null;
    var selectLoai = document.getElementById('them_loai_nghi_phep');
    var wrapNgayDon = document.getElementById('wrap_ngay_don');
    var wrapBuoiNghi = document.getElementById('wrap_buoi_nghi');
    var wrapNgayRange = document.getElementById('wrap_ngay_range');
    var labelNgayDon = document.getElementById('label_ngay_don');
    var inputBuoiNghi = document.getElementById('them_buoi_nghi');
    var inputNgayBatDau = document.getElementById('them_ngay_bat_dau');
    var inputNgayRange = document.getElementById('them_ngay_range');
    var inputNgayBatDauRange = document.getElementById('them_ngay_bat_dau_range');
    var inputNgayKetThucRange = document.getElementById('them_ngay_ket_thuc_range');
    var flatpickrRange = null;

    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function formatYmd(date) {
        if (typeof flatpickr !== 'undefined' && flatpickr.formatDate) {
            return flatpickr.formatDate(date, 'Y-m-d');
        }

        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function resetSelect2Value(el) {
        if (!el) {
            return;
        }

        el.value = '';
        if (window.jQuery && jQuery(el).data('select2')) {
            jQuery(el).val('').trigger('change');
        }
    }

    function clearSingleDate() {
        if (!inputNgayBatDau) {
            return;
        }

        inputNgayBatDau.value = '';
        if (inputNgayBatDau._flatpickr) {
            inputNgayBatDau._flatpickr.clear();
        }
    }

    function clearRangeDate() {
        if (inputNgayBatDauRange) {
            inputNgayBatDauRange.value = '';
        }
        if (inputNgayKetThucRange) {
            inputNgayKetThucRange.value = '';
        }
        if (flatpickrRange) {
            flatpickrRange.clear();
        }
    }

    function syncRangeHiddenFields(selectedDates) {
        if (!inputNgayBatDauRange || !inputNgayKetThucRange) {
            return;
        }

        inputNgayBatDauRange.value = selectedDates[0] ? formatYmd(selectedDates[0]) : '';
        inputNgayKetThucRange.value = selectedDates[1] ? formatYmd(selectedDates[1]) : '';
    }

    function initRangePicker() {
        if (!inputNgayRange || flatpickrRange || typeof flatpickr === 'undefined') {
            return;
        }

        flatpickrRange = flatpickr(inputNgayRange, {
            mode: 'range',
            altInput: true,
            altFormat: 'd/m/Y',
            dateFormat: 'Y-m-d',
            allowInput: false,
            locale: { firstDayOfWeek: 1 },
            appendTo: document.body,
            onChange: function(selectedDates) {
                syncRangeHiddenFields(selectedDates);
            }
        });

        var tuNgay = inputNgayBatDauRange ? inputNgayBatDauRange.value : '';
        var denNgay = inputNgayKetThucRange ? inputNgayKetThucRange.value : '';
        if (tuNgay && denNgay) {
            flatpickrRange.setDate([tuNgay, denNgay], false);
        }
    }

    function setRangeFieldsActive(active) {
        if (inputNgayBatDauRange) {
            if (active) {
                inputNgayBatDauRange.setAttribute('name', 'ngay_bat_dau');
            } else {
                inputNgayBatDauRange.removeAttribute('name');
                inputNgayBatDauRange.value = '';
            }
        }

        if (inputNgayKetThucRange) {
            if (active) {
                inputNgayKetThucRange.setAttribute('name', 'ngay_ket_thuc');
            } else {
                inputNgayKetThucRange.removeAttribute('name');
                inputNgayKetThucRange.value = '';
            }
        }

        if (inputNgayRange) {
            inputNgayRange.required = active;
        }
    }

    function capNhatTruongTheoLoai() {
        if (!selectLoai) {
            return;
        }

        var loai = selectLoai.value;
        var isDiMuonVeSom = loai === LOAI_DI_MUON || loai === LOAI_VE_SOM;
        var isNuaNgay = loai === LOAI_NUA_NGAY;
        var isCaNgay = loai === LOAI_CA_NGAY;
        var isNhieuNgay = loai === LOAI_NHIEU_NGAY;
        var hienNgayDon = isDiMuonVeSom || isNuaNgay || isCaNgay;
        var hienBuoi = isNuaNgay;

        if (labelNgayDon) {
            labelNgayDon.textContent = isDiMuonVeSom ? 'Ngày xin phép' : 'Ngày nghỉ';
        }

        if (wrapNgayDon) {
            wrapNgayDon.classList.toggle('d-none', !hienNgayDon);
        }
        if (inputNgayBatDau) {
            inputNgayBatDau.required = hienNgayDon;
            if (hienNgayDon) {
                inputNgayBatDau.setAttribute('name', 'ngay_bat_dau');
            } else {
                inputNgayBatDau.removeAttribute('name');
                clearSingleDate();
            }
        }

        if (wrapBuoiNghi) {
            wrapBuoiNghi.classList.toggle('d-none', !hienBuoi);
        }
        if (inputBuoiNghi) {
            inputBuoiNghi.required = hienBuoi;
            if (!hienBuoi) {
                resetSelect2Value(inputBuoiNghi);
            }
        }

        if (wrapNgayRange) {
            wrapNgayRange.classList.toggle('d-none', !isNhieuNgay);
        }
        setRangeFieldsActive(isNhieuNgay);

        if (isNhieuNgay) {
            if (inputNgayBatDau) {
                inputNgayBatDau.removeAttribute('name');
            }
            clearSingleDate();
            initRangePicker();
        } else {
            clearRangeDate();
        }
    }

    if (selectLoai) {
        selectLoai.addEventListener('change', capNhatTruongTheoLoai);
        if (window.jQuery) {
            jQuery(selectLoai).on('select2:select select2:clear change', capNhatTruongTheoLoai);
        }
        capNhatTruongTheoLoai();
    }

    if (formThemNghiPhep) {
        formThemNghiPhep.addEventListener('submit', function(e) {
            if (!selectLoai || selectLoai.value !== LOAI_NHIEU_NGAY) {
                return;
            }

            var selectedDates = flatpickrRange ? flatpickrRange.selectedDates : [];
            syncRangeHiddenFields(selectedDates);

            if (selectedDates.length < 2) {
                e.preventDefault();
                alert('Vui lòng chọn đủ khoảng ngày nghỉ (từ ngày đến ngày).');
            }
        });
    }

    if (modalThemNghiPhep) {
        modalThemNghiPhep.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        modalThemNghiPhep.addEventListener('shown.bs.modal', capNhatTruongTheoLoai);
    }

    @if($errors->any())
    if (modalThemNghiPhep) {
        bootstrap.Modal.getOrCreateInstance(modalThemNghiPhep).show();
    }
    @endif

    var modalXacNhanNghiPhep = document.getElementById('modalXacNhanNghiPhep');
    var btnXacNhanNghiPhep = document.getElementById('btnXacNhanNghiPhep');
    var modalXacNhanNghiPhepLabel = document.getElementById('modalXacNhanNghiPhepLabel');
    var modalXacNhanNghiPhepBody = document.getElementById('modalXacNhanNghiPhepBody');
    var formIdCanXuLy = null;

    var nghiPhepActionConfig = {
        duyet: {
            title: 'Xác nhận duyệt',
            body: 'Bạn có chắc muốn duyệt đơn này?',
            btnClass: 'btn btn-success',
            btnIcon: 'fa-solid fa-check',
            btnText: 'Duyệt'
        },
        tu_choi: {
            title: 'Xác nhận từ chối',
            body: 'Bạn có chắc muốn từ chối đơn này?',
            btnClass: 'btn btn-danger',
            btnIcon: 'fa-solid fa-xmark',
            btnText: 'Từ chối'
        },
        xoa: {
            title: 'Xác nhận xóa',
            body: 'Bạn có chắc muốn xóa đơn nghỉ phép này?',
            btnClass: 'btn btn-danger',
            btnIcon: 'fa-solid fa-trash',
            btnText: 'Xóa'
        }
    };

    if (modalXacNhanNghiPhep && btnXacNhanNghiPhep) {
        modalXacNhanNghiPhep.addEventListener('hidden.bs.modal', cleanupModalBackdrop);

        document.querySelectorAll('.btn-xac-nhan-nghi-phep').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXuLy = this.getAttribute('data-form-id');
                var action = this.getAttribute('data-action');
                var config = nghiPhepActionConfig[action];
                if (!formIdCanXuLy || !config) {
                    return;
                }

                if (modalXacNhanNghiPhepLabel) {
                    modalXacNhanNghiPhepLabel.textContent = config.title;
                }
                if (modalXacNhanNghiPhepBody) {
                    modalXacNhanNghiPhepBody.textContent = config.body;
                }

                btnXacNhanNghiPhep.className = config.btnClass;
                btnXacNhanNghiPhep.innerHTML = '<i class="' + config.btnIcon + ' me-1"></i> ' + config.btnText;

                bootstrap.Modal.getOrCreateInstance(modalXacNhanNghiPhep).show();
            });
        });

        btnXacNhanNghiPhep.addEventListener('click', function() {
            if (formIdCanXuLy) {
                var form = document.getElementById(formIdCanXuLy);
                if (form) {
                    form.submit();
                }
            }

            var inst = bootstrap.Modal.getInstance(modalXacNhanNghiPhep);
            if (inst) {
                inst.hide();
            }
            formIdCanXuLy = null;
        });
    }

    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush
@endsection
