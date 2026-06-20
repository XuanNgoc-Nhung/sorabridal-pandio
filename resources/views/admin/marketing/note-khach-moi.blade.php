@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\NoteKhachMoi::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('tu_khoa')
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

    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form action="{{ route('admin.note-khach-moi') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_khoa">Từ khoá</label>
                    <input type="text"
                           class="form-control"
                           id="tu_khoa"
                           name="tu_khoa"
                           value="{{ request('tu_khoa') }}"
                           placeholder="Tên KH, SĐT, nguồn, lý do...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="trang_thai" name="trang_thai" data-placeholder="Tất cả">
                        <option value="">— Tất cả —</option>
                        @foreach($trangThaiLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('trang_thai') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\NoteKhachMoi::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.note-khach-moi') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Note khách mới</span>
            <button type="button"
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalThemNoteKhachMoi">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Tên khách</th>
                        <th style="width: 120px;">SĐT</th>
                        <th style="width: 140px;">Phụ trách sale</th>
                        <th style="width: 195px;">Ngày hẹn lịch</th>
                        <th style="width: 165px;">Ngày đến thực tế</th>
                        <th style="width: 130px;">Hình thức đặt cọc</th>
                        <th style="width: 120px;">Nguồn khách</th>
                        <th style="width: 130px;">Người tạo</th>
                        <th style="width: 115px;">Ngày tạo</th>
                        <th style="width: 120px;">Trạng thái</th>
                        <th>Lý do không chốt</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td class="text-wrap">{{ $item->ten_khach ?: '—' }}</td>
                        <td>
                            @if($item->so_dien_thoai)
                            <a href="tel:{{ $item->so_dien_thoai }}" class="text-body">{{ $item->so_dien_thoai }}</a>
                            @else
                            —
                            @endif
                        </td>
                        <td>
                            @php
                                $tenSales = $item->phuTrachSales->pluck('name')->filter()->unique()->values();
                                if ($tenSales->isEmpty() && $item->phuTrachSale) {
                                    $tenSales = collect([$item->phuTrachSale->name]);
                                }
                            @endphp
                            {{ $tenSales->isNotEmpty() ? $tenSales->join(', ') : '—' }}
                        </td>
                        <td>{{ \App\Models\NoteKhachMoi::formatNgayGioCoThu($item->ngay_hen_lich) }}</td>
                        <td>{{ \App\Models\NoteKhachMoi::formatNgayCoThu($item->ngay_den_thuc_te) }}</td>
                        <td>
                            @php
                                $sdtChuan = \App\Models\HopDongCuoi::normalizeContactPhone($item->so_dien_thoai);
                                $hinhThucCoc = $sdtChuan ? ($hinhThucCocTheoSdt[$sdtChuan] ?? null) : null;
                            @endphp
                            {{ $hinhThucCoc ?: '—' }}
                        </td>
                        <td>{{ $item->nguon_khach ?: '—' }}</td>
                        <td>{{ $item->nguoiTao?->name ?? '—' }}</td>
                        <td>{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            @if($item->trang_thai)
                            <span class="badge {{ $item->trang_thai_badge_class }}">{{ $item->trang_thai_label }}</span>
                            @else
                            —
                            @endif
                        </td>
                        <td class="text-wrap note-khach-moi-ly-do">{{ $item->ly_do_khong_chot ?: '—' }}</td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Thao tác
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button type="button"
                                            class="dropdown-item btn-sua-note-khach-moi"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSuaNoteKhachMoi"
                                            data-url="{{ route('admin.note-khach-moi.update', $item) }}"
                                            data-ten-khach="{{ e($item->ten_khach ?? '') }}"
                                            data-so-dien-thoai="{{ e($item->so_dien_thoai ?? '') }}"
                                            data-phu-trach-sale-nhan-vien-ids="{{ e(json_encode($item->phuTrachSaleNhanVienIds())) }}"
                                            data-ngay-hen-lich="{{ $item->ngay_hen_lich?->format('Y-m-d H:i') }}"
                                            data-ngay-den-thuc-te="{{ $item->ngay_den_thuc_te?->format('Y-m-d') }}"
                                            data-nguon-khach="{{ e($item->nguon_khach ?? '') }}"
                                            data-nguon-khach-key="{{ e(\App\Models\HopDongCuoi::nguonKhachToKenhKey($item->nguon_khach) ?? '') }}"
                                            data-trang-thai="{{ e($item->trang_thai ?? '') }}"
                                            data-ly-do-khong-chot="{{ e($item->ly_do_khong_chot ?? '') }}">
                                            <i class="fa-solid fa-pen me-1"></i> Sửa
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form id="form-xoa-note-khach-moi-{{ $item->id }}" action="{{ route('admin.note-khach-moi.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="dropdown-item text-danger btn-xoa-note-khach-moi" data-form-id="form-xoa-note-khach-moi-{{ $item->id }}">
                                            <i class="fa-solid fa-trash me-1"></i> Xóa
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-4 text-muted">Chưa có note khách mới nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSach" label="note khách mới" />
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemNoteKhachMoi" tabindex="-1" aria-labelledby="modalThemNoteKhachMoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemNoteKhachMoiLabel">Thêm note khách mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.note-khach-moi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_so_dien_thoai">Số điện thoại</label>
                            <input type="text" class="form-control" id="them_so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" placeholder="Nhập SĐT" autocomplete="tel">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_ten_khach">Tên khách</label>
                            <input type="text" class="form-control" id="them_ten_khach" name="ten_khach" value="{{ old('ten_khach') }}" placeholder="Nhập tên khách">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_nguon_khach">Nguồn khách</label>
                            <select class="select2-admin form-select" id="them_nguon_khach" name="nguon_khach" data-placeholder="Chọn nguồn khách">
                                <option value="">— Chọn nguồn —</option>
                                @foreach($kenhTiepCanLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('nguon_khach') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_ma_hop_dong">Mã hợp đồng</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   id="them_ma_hop_dong"
                                   readonly
                                   tabindex="-1"
                                   placeholder="Tự động khi khớp SĐT">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_phu_trach_sale_ids">Phụ trách sale</label>
                            <select class="select2-admin form-select"
                                    id="them_phu_trach_sale_ids"
                                    name="phu_trach_sale_nhan_vien_ids[]"
                                    multiple
                                    data-placeholder="Chọn nhân sự (có thể chọn nhiều)">
                                @php
                                    $themSaleDaChon = collect(old('phu_trach_sale_nhan_vien_ids', []))
                                        ->map(fn ($id) => (string) $id)
                                        ->all();
                                @endphp
                                @foreach($danhSachNhanVien as $nv)
                                <option value="{{ $nv->id }}" @selected(in_array((string) $nv->id, $themSaleDaChon, true))>
                                    {{ $nv->user?->name ?? ('Nhân viên #' . $nv->id) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_ngay_hen_lich">Ngày hẹn lịch</label>
                            <input type="text" class="flatpickr-datetime-admin form-control" id="them_ngay_hen_lich" name="ngay_hen_lich" value="{{ old('ngay_hen_lich') }}" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_ngay_den_thuc_te">Ngày đến thực tế</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_den_thuc_te" name="ngay_den_thuc_te" value="{{ old('ngay_den_thuc_te') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="them_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select js-trang-thai-note" id="them_trang_thai" name="trang_thai" data-ly-do-prefix="them" data-placeholder="Chọn trạng thái">
                                <option value="">— Chọn trạng thái —</option>
                                @foreach($trangThaiLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('trang_thai') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-9 js-ly-do-khong-chot-wrap d-none" data-ly-do-prefix="them">
                            <label class="form-label" for="them_ly_do_khong_chot">Lý do không chốt <span class="text-danger js-ly-do-required-mark">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="them_ly_do_khong_chot"
                                   name="ly_do_khong_chot"
                                   value="{{ old('ly_do_khong_chot') }}"
                                   placeholder="Nhập lý do không chốt..."
                                   maxlength="5000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSuaNoteKhachMoi" tabindex="-1" aria-labelledby="modalSuaNoteKhachMoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaNoteKhachMoiLabel">Chỉnh sửa note khách mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaNoteKhachMoi" action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_so_dien_thoai">Số điện thoại</label>
                            <input type="text" class="form-control" id="sua_so_dien_thoai" name="so_dien_thoai" placeholder="Nhập SĐT" autocomplete="tel">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_ten_khach">Tên khách</label>
                            <input type="text" class="form-control" id="sua_ten_khach" name="ten_khach" placeholder="Nhập tên khách">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_nguon_khach">Nguồn khách</label>
                            <select class="select2-admin form-select" id="sua_nguon_khach" name="nguon_khach" data-placeholder="Chọn nguồn khách">
                                <option value="">— Chọn nguồn —</option>
                                @foreach($kenhTiepCanLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_ma_hop_dong">Mã hợp đồng</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   id="sua_ma_hop_dong"
                                   readonly
                                   tabindex="-1"
                                   placeholder="Tự động khi khớp SĐT">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_phu_trach_sale_ids">Phụ trách sale</label>
                            <select class="select2-admin form-select"
                                    id="sua_phu_trach_sale_ids"
                                    name="phu_trach_sale_nhan_vien_ids[]"
                                    multiple
                                    data-placeholder="Chọn nhân sự (có thể chọn nhiều)">
                                @foreach($danhSachNhanVien as $nv)
                                <option value="{{ $nv->id }}">
                                    {{ $nv->user?->name ?? ('Nhân viên #' . $nv->id) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_ngay_hen_lich">Ngày hẹn lịch</label>
                            <input type="text" class="flatpickr-datetime-admin form-control" id="sua_ngay_hen_lich" name="ngay_hen_lich" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_ngay_den_thuc_te">Ngày đến thực tế</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_den_thuc_te" name="ngay_den_thuc_te" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" for="sua_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select js-trang-thai-note" id="sua_trang_thai" name="trang_thai" data-ly-do-prefix="sua" data-placeholder="Chọn trạng thái">
                                <option value="">— Chọn trạng thái —</option>
                                @foreach($trangThaiLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-9 js-ly-do-khong-chot-wrap d-none" data-ly-do-prefix="sua">
                            <label class="form-label" for="sua_ly_do_khong_chot">Lý do không chốt <span class="text-danger js-ly-do-required-mark">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="sua_ly_do_khong_chot"
                                   name="ly_do_khong_chot"
                                   placeholder="Nhập lý do không chốt..."
                                   maxlength="5000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalXacNhanXoaNoteKhachMoi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa note khách mới này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaNoteKhachMoi">Xóa</button>
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
}
.table-wrapper-bordered .table {
    min-width: 1550px;
    border-collapse: collapse;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
.note-khach-moi-ly-do {
    max-width: 220px;
    white-space: pre-wrap;
}
#modalXacNhanXoaNoteKhachMoi .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var TRANG_THAI_KHONG_CHOT = @json(\App\Models\NoteKhachMoi::TRANG_THAI_KHONG_CHOT);
    var TRANG_THAI_CAN_LY_DO = @json(\App\Models\NoteKhachMoi::trangThaiCanLyDoKhongChot());
    var URL_TIM_HOP_DONG_THEO_SDT = @json(route('admin.note-khach-moi.tim-hop-dong-theo-sdt'));
    var KENH_TIEP_CAN_LABELS = @json($kenhTiepCanLabels);
    var timHopDongAbort = null;

    function nguonKhachToKenhKey(val) {
        if (!val) return '';
        if (Object.prototype.hasOwnProperty.call(KENH_TIEP_CAN_LABELS, val)) return val;
        var keys = Object.keys(KENH_TIEP_CAN_LABELS);
        var lower = String(val).toLowerCase();
        for (var i = 0; i < keys.length; i++) {
            if (KENH_TIEP_CAN_LABELS[keys[i]] === val) return keys[i];
            if (String(KENH_TIEP_CAN_LABELS[keys[i]]).toLowerCase() === lower) return keys[i];
        }
        return '';
    }

    function removeDynamicSelectOptions(selectEl) {
        if (!selectEl) return;
        selectEl.querySelectorAll('option[data-dynamic-option="1"]').forEach(function (opt) {
            opt.remove();
        });
    }

    function setNoteSelect2Value(selectEl, value) {
        if (!selectEl) return;
        var val = value || '';
        var $ = window.jQuery || window.$;
        if ($ && $.fn && $.fn.select2 && $(selectEl).data('select2')) {
            $(selectEl).val(val || null).trigger('change');
        } else {
            selectEl.value = val;
        }
    }

  /** Gán nguồn khách: ưu tiên key kênh, không thêm option trùng nhãn. */
    function setNguonKhachSelect(selectEl, kenhKey, storedLabel) {
        if (!selectEl) return;
        removeDynamicSelectOptions(selectEl);

        var key = kenhKey || nguonKhachToKenhKey(storedLabel) || '';
        if (key && Array.from(selectEl.options).some(function (opt) { return opt.value === key; })) {
            setNoteSelect2Value(selectEl, key);
            return;
        }

        var legacy = (storedLabel || '').trim();
        if (legacy) {
            var exists = Array.from(selectEl.options).some(function (opt) {
                return opt.value === legacy || opt.textContent === legacy;
            });
            if (!exists) {
                var opt = document.createElement('option');
                opt.value = legacy;
                opt.textContent = legacy;
                opt.setAttribute('data-dynamic-option', '1');
                selectEl.appendChild(opt);
            }
            setNoteSelect2Value(selectEl, legacy);
            return;
        }

        setNoteSelect2Value(selectEl, '');
    }

    function timHopDongTheoSdt(soDienThoai, onDone) {
        var sdt = (soDienThoai || '').trim();
        if (sdt.length < 9) {
            onDone(null);
            return;
        }
        if (timHopDongAbort) {
            timHopDongAbort.abort();
        }
        timHopDongAbort = new AbortController();
        RestApi.get(URL_TIM_HOP_DONG_THEO_SDT, { so_dien_thoai: sdt }, { signal: timHopDongAbort.signal })
            .then(function (res) {
                if (res.message === 'Request cancelled.') return;
                onDone(res.ok ? res.data : null);
            })
            .finally(function () { timHopDongAbort = null; });
    }

    function setNoteSelect2Multi(selectEl, values) {
        if (!selectEl) return;
        var vals = (values || []).map(String);
        Array.from(selectEl.options).forEach(function (opt) {
            opt.selected = vals.includes(String(opt.value));
        });
        var $ = window.jQuery || window.$;
        if ($ && $.fn && $.fn.select2 && $(selectEl).data('select2')) {
            $(selectEl).val(vals).trigger('change');
        }
    }

    function syncSelect2OnSubmit(selectEls) {
        var $ = window.jQuery || window.$;
        if (!$ || !$.fn || !$.fn.select2) return;
        selectEls.forEach(function (el) {
            if (el && $(el).data('select2')) {
                $(el).trigger('change');
            }
        });
    }

    function initHopDongLookup(prefix, modalId) {
        var inputSdt = document.getElementById(prefix + '_so_dien_thoai');
        if (!inputSdt) return null;

        var inputMaHd = document.getElementById(prefix + '_ma_hop_dong');
        var inputTenKhach = document.getElementById(prefix + '_ten_khach');
        var selectNguon = document.getElementById(prefix + '_nguon_khach');
        var selectSale = document.getElementById(prefix + '_phu_trach_sale_ids');
        var debounceTimer = null;

        function xoaMaHopDong() {
            if (inputMaHd) inputMaHd.value = '';
        }

        function capNhatTuHopDong(onlyMaHopDong) {
            timHopDongTheoSdt(inputSdt.value, function (data) {
                if (!data || !data.ma_hop_dong) {
                    xoaMaHopDong();
                    return;
                }
                if (inputMaHd) inputMaHd.value = data.ma_hop_dong;
                if (onlyMaHopDong) return;
                if (inputTenKhach && data.ten_khach) {
                    inputTenKhach.value = data.ten_khach;
                }
                if (selectNguon && data.kenh_tiep_can) {
                    setNguonKhachSelect(selectNguon, data.kenh_tiep_can, '');
                }
                if (selectSale && data.phu_trach_sale_nhan_vien_ids && data.phu_trach_sale_nhan_vien_ids.length) {
                    setNoteSelect2Multi(selectSale, data.phu_trach_sale_nhan_vien_ids);
                }
            });
        }

        inputSdt.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            if (!inputSdt.value.trim()) {
                xoaMaHopDong();
                return;
            }
            debounceTimer = setTimeout(capNhatTuHopDong, 400);
        });

        var modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                clearTimeout(debounceTimer);
                xoaMaHopDong();
            });
            var form = modal.querySelector('form');
            if (form) {
                form.addEventListener('submit', function () {
                    syncSelect2OnSubmit([selectNguon, selectSale]);
                });
            }
        }

        return {
            capNhatTuHopDong: capNhatTuHopDong,
            xoaMaHopDong: xoaMaHopDong,
            resetNguonSelect: function () {
                if (selectNguon) {
                    removeDynamicSelectOptions(selectNguon);
                    setNoteSelect2Value(selectNguon, '');
                }
            },
        };
    }

    var lookupThem = initHopDongLookup('them', 'modalThemNoteKhachMoi');
    if (lookupThem && document.getElementById('them_so_dien_thoai').value.trim()) {
        lookupThem.capNhatTuHopDong();
    }
    var lookupSua = initHopDongLookup('sua', 'modalSuaNoteKhachMoi');

    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function getTrangThaiValue(selectEl) {
        if (!selectEl) return '';
        var $ = window.jQuery || window.$;
        if ($ && $.fn && $.fn.select2 && $(selectEl).data('select2')) {
            return $(selectEl).val() || '';
        }
        return selectEl.value || '';
    }

    function toggleLyDoKhongChot(prefix) {
        var select = document.getElementById(prefix + '_trang_thai');
        var wrap = document.querySelector('.js-ly-do-khong-chot-wrap[data-ly-do-prefix="' + prefix + '"]');
        var lyDoInput = document.getElementById(prefix + '_ly_do_khong_chot');
        if (!select || !wrap) return;

        var trangThai = getTrangThaiValue(select);
        var showLyDo = TRANG_THAI_CAN_LY_DO.indexOf(trangThai) !== -1;
        var requireLyDo = trangThai === TRANG_THAI_KHONG_CHOT;
        wrap.classList.toggle('d-none', !showLyDo);
        var requiredMark = wrap.querySelector('.js-ly-do-required-mark');
        if (requiredMark) {
            requiredMark.classList.toggle('d-none', !requireLyDo);
        }
        if (lyDoInput) {
            lyDoInput.required = requireLyDo;
            if (!showLyDo) {
                lyDoInput.value = '';
            }
        }
    }

    function bindTrangThaiNoteSelect2() {
        document.querySelectorAll('.js-trang-thai-note').forEach(function (sel) {
            var prefix = sel.getAttribute('data-ly-do-prefix');
            if (!prefix || sel.dataset.lyDoBound === '1') return;
            sel.dataset.lyDoBound = '1';

            sel.addEventListener('change', function () {
                toggleLyDoKhongChot(prefix);
            });

            var $ = window.jQuery || window.$;
            if ($ && $.fn && $.fn.select2) {
                $(sel).on('select2:select select2:clear change', function () {
                    toggleLyDoKhongChot(prefix);
                });
            }
        });
    }

    bindTrangThaiNoteSelect2();

    ['them', 'sua'].forEach(function (prefix) {
        toggleLyDoKhongChot(prefix);
    });

    [document.getElementById('modalThemNoteKhachMoi'), document.getElementById('modalSuaNoteKhachMoi')].forEach(function (modal) {
        if (modal) modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
    });

    @if($errors->any())
    var modalThem = document.getElementById('modalThemNoteKhachMoi');
    if (modalThem) {
        bootstrap.Modal.getOrCreateInstance(modalThem).show();
        toggleLyDoKhongChot('them');
    }
    @endif

    var modalSua = document.getElementById('modalSuaNoteKhachMoi');
    var formSua = document.getElementById('formSuaNoteKhachMoi');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-note-khach-moi')) return;

            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;

            document.getElementById('sua_so_dien_thoai').value = btn.getAttribute('data-so-dien-thoai') || '';
            document.getElementById('sua_ten_khach').value = btn.getAttribute('data-ten-khach') || '';
            setNguonKhachSelect(
                document.getElementById('sua_nguon_khach'),
                btn.getAttribute('data-nguon-khach-key') || '',
                btn.getAttribute('data-nguon-khach') || ''
            );
            var saleIdsRaw = btn.getAttribute('data-phu-trach-sale-nhan-vien-ids') || '[]';
            try {
                setNoteSelect2Multi(document.getElementById('sua_phu_trach_sale_ids'), JSON.parse(saleIdsRaw));
            } catch (e) {
                setNoteSelect2Multi(document.getElementById('sua_phu_trach_sale_ids'), []);
            }
            setNoteSelect2Value(document.getElementById('sua_trang_thai'), btn.getAttribute('data-trang-thai') || '');
            if (window.setAdminDateTimeInput && window.setAdminDateInput) {
                setAdminDateTimeInput('sua_ngay_hen_lich', btn.getAttribute('data-ngay-hen-lich') || '');
                setAdminDateInput('sua_ngay_den_thuc_te', btn.getAttribute('data-ngay-den-thuc-te') || '');
            } else {
                document.getElementById('sua_ngay_hen_lich').value = btn.getAttribute('data-ngay-hen-lich') || '';
                document.getElementById('sua_ngay_den_thuc_te').value = btn.getAttribute('data-ngay-den-thuc-te') || '';
            }
            document.getElementById('sua_ly_do_khong_chot').value = btn.getAttribute('data-ly-do-khong-chot') || '';
            toggleLyDoKhongChot('sua');
            if (lookupSua) {
                if (document.getElementById('sua_so_dien_thoai').value.trim()) {
                    lookupSua.capNhatTuHopDong(true);
                } else {
                    lookupSua.xoaMaHopDong();
                }
            }
        });
        modalSua.addEventListener('hidden.bs.modal', function () {
            if (lookupSua && lookupSua.resetNguonSelect) {
                lookupSua.resetNguonSelect();
            }
        });
    }

    var modalXoa = document.getElementById('modalXacNhanXoaNoteKhachMoi');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaNoteKhachMoi');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        document.querySelectorAll('.btn-xoa-note-khach-moi').forEach(function (btn) {
            btn.addEventListener('click', function () {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                bootstrap.Modal.getOrCreateInstance(modalXoa).show();
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
