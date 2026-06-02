@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<style>
.cong-no-khoang-ngay-radios .form-check-label {
    font-size: 14px;
    white-space: nowrap;
}
</style>
@endpush

@section('content')
@php
    use App\Support\AdminCongNoList;

    $sapXepTheoMacDinh = AdminCongNoList::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $khoangNgayHienTai = request('khoang_ngay', '');
    $hasFilter = request()->filled('tu_ngay')
        || request()->filled('den_ngay')
        || request()->filled('khoang_ngay')
        || request()->filled('trang_thai_tt')
        || request()->filled('search')
        || request()->filled('loai_hop_dong')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc';
@endphp
<div class="d-flex flex-column gap-3">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
    @endif

    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form id="form-cong-no-loc" action="{{ route('admin.tai-chinh.cong-no') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Từ khoá</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="loai_hop_dong">Loại hợp đồng</label>
                    <select class="select2-admin form-select" id="loai_hop_dong" name="loai_hop_dong" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach(AdminCongNoList::LOAI_HOP_DONG_OPTIONS as $value => $label)
                            <option value="{{ $value }}" @selected(request('loai_hop_dong') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="cong_no_date_range">Khoảng ngày</label>
                    <input type="text"
                           class="form-control"
                           id="cong_no_date_range"
                           placeholder="dd/mm/yyyy - dd/mm/yyyy"
                           autocomplete="off"
                           value="">
                    <input type="hidden" name="tu_ngay" id="cong_no_tu_ngay" value="{{ request('tu_ngay') }}">
                    <input type="hidden" name="den_ngay" id="cong_no_den_ngay" value="{{ request('den_ngay') }}">
                </div>
                {{-- <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="trang_thai_tt">Trạng thái thanh toán</label>
                    <select class="select2-admin form-select" id="trang_thai_tt" name="trang_thai_tt" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        <option value="chua" @selected(request('trang_thai_tt') === 'chua')>Chưa thanh toán</option>
                        <option value="da" @selected(request('trang_thai_tt') === 'da')>Đã thanh toán</option>
                    </select>
                </div> --}}
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(AdminCongNoList::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.tai-chinh.cong-no') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
                <div class="col-12">
                    {{-- <span class="form-label d-block mb-2">Chọn nhanh khoảng thời gian</span> --}}
                    <div class="d-flex flex-wrap gap-2 gap-md-3 cong-no-khoang-ngay-radios">
                        @foreach(AdminCongNoList::KHOANG_NGAY_OPTIONS as $value => $label)
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input cong-no-khoang-ngay-radio"
                                   type="radio"
                                   name="khoang_ngay"
                                   id="khoang_ngay_{{ $value }}"
                                   value="{{ $value }}"
                                   @checked($khoangNgayHienTai === $value)>
                            <label class="form-check-label" for="khoang_ngay_{{ $value }}">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- <p class="text-muted small mt-2 mb-0">
                Lọc theo ngày tạo hợp đồng. <strong>Từ khoá</strong> tìm theo tên khách hàng hoặc mã hợp đồng.
                <strong>Chưa thanh toán / Đã thanh toán</strong> dựa trên số tiền còn phải thu
                (`tong_tien - tien_coc`) của từng hợp đồng cưới và hợp đồng thuê trang phục.
            </p> --}}
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header">Danh sách công nợ</h5>
        <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <span class="text-muted d-block mb-1">Số hợp đồng</span>
                        <h5 class="mb-0">{{ number_format((int) ($tongHop['so_hop_dong'] ?? 0), 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <span class="text-muted d-block mb-1">Tổng giá trị</span>
                        <h5 class="mb-0">{{ number_format((float) ($tongHop['tong_tien'] ?? 0), 0, ',', '.') }} đ</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <span class="text-muted d-block mb-1">Đã thanh toán</span>
                        <h5 class="mb-0 text-success">{{ number_format((float) ($tongHop['da_thanh_toan'] ?? 0), 0, ',', '.') }} đ</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border shadow-none h-100">
                    <div class="card-body">
                        <span class="text-muted d-block mb-1">Còn lại</span>
                        <h5 class="mb-0 text-danger">{{ number_format((float) ($tongHop['con_lai'] ?? 0), 0, ',', '.') }} đ</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th class="text-center" style="min-width: 140px;">Loại hợp đồng</th>
                        <th class="text-center" style="min-width: 130px;">Mã hợp đồng</th>
                        <th>Khách hàng</th>
                        <th class="text-center" style="min-width: 118px;">Ngày bắt đầu HĐ</th>
                        <th class="text-center" style="min-width: 118px;">Ngày kết thúc HĐ</th>
                        <th class="text-end" style="min-width: 120px;">Tổng tiền HĐ</th>
                        <th class="text-end" style="min-width: 120px;">Đã thanh toán</th>
                        <th class="text-end" style="min-width: 120px;">Còn lại</th>
                        <th class="text-center" style="width: 150px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSach as $idx => $item)
                    @php
                        $maHd = trim((string) ($item->ma_hop_dong ?? ''));
                        $tenKh = trim((string) ($item->ten_khach_hang ?? '')) ?: '—';
                        $loaiHopDong = trim((string) ($item->loai_hop_dong ?? '')) ?: '—';
                        $tongTien = (float) ($item->tong_tien ?? 0);
                        $daThanhToan = (float) ($item->da_thanh_toan ?? $item->tien_coc ?? 0);
                        $conLaiThuc = (float) ($item->con_lai ?? ($tongTien - $daThanhToan));
                        $conLai = max(0, $conLaiThuc);
                        $daHet = $conLaiThuc <= 0.00001;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $danhSach->firstItem() + $idx }}</td>
                        <td class="text-center">{{ $loaiHopDong }}</td>
                        <td class="text-center fw-semibold">{{ $maHd !== '' ? $maHd : '—' }}</td>
                        <td>{{ $tenKh }}</td>
                        <td class="text-center">{{ $item->ngay_bat_dau_hop_dong?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-center">{{ $item->ngay_ket_thuc_hop_dong?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-end">{{ number_format($tongTien, 0, ',', '.') }} đ</td>
                        <td class="text-end">{{ number_format($daThanhToan, 0, ',', '.') }} đ</td>
                        <td class="text-end fw-medium {{ $daHet ? 'text-success' : 'text-danger' }}">
                            {{ number_format($conLai, 0, ',', '.') }} đ
                        </td>
                        <td class="text-center">
                            @if($daHet)
                            <span class="badge bg-label-success">Đã thanh toán</span>
                            @else
                            <span class="badge bg-label-warning">Chưa thanh toán</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Không có hợp đồng phù hợp bộ lọc.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSach ?? null" label="hợp đồng" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script>
(function () {
    if (window.__congNoDateRangeInit) return;
    window.__congNoDateRangeInit = true;

    var $ = window.jQuery;
    if (!$ || !$.fn.daterangepicker || typeof moment === 'undefined') return;

    var $form = $('#form-cong-no-loc');
    var $inp = $('#cong_no_date_range');
    var $tu = $('#cong_no_tu_ngay');
    var $den = $('#cong_no_den_ngay');
    var $radios = $('.cong-no-khoang-ngay-radio');
    if (!$inp.length || !$form.length) return;

    function submitFilter() {
        $form[0].submit();
    }

    function startOfQuarter(m) {
        var quarter = Math.ceil((m.month() + 1) / 3);
        var startMonth = (quarter - 1) * 3;
        return m.clone().month(startMonth).startOf('month');
    }

    function resolveKhoangNgay(key) {
        var today = moment().startOf('day');
        var den = moment().endOf('day');
        var startOfWeek = today.clone().startOf('isoWeek');
        var startQ = startOfQuarter(today);

        switch (key) {
            case 'nam_nay':
                return { tu: today.clone().startOf('year'), den: den };
            case 'quy_nay':
                return { tu: startQ.clone(), den: den };
            case 'quy_truoc':
                return {
                    tu: startQ.clone().subtract(3, 'months'),
                    den: startQ.clone().subtract(1, 'day').endOf('day')
                };
            case 'thang_nay':
                return { tu: today.clone().startOf('month'), den: den };
            case 'thang_truoc':
                return {
                    tu: today.clone().subtract(1, 'month').startOf('month'),
                    den: today.clone().subtract(1, 'month').endOf('month')
                };
            case 'tuan_nay':
                return { tu: startOfWeek.clone(), den: den };
            case 'tuan_truoc':
                return {
                    tu: startOfWeek.clone().subtract(1, 'week'),
                    den: startOfWeek.clone().subtract(1, 'day').endOf('day')
                };
            case 'ba_thang_gan_day':
                return { tu: today.clone().subtract(3, 'months'), den: den };
            case 'hai_thang_gan_day':
                return { tu: today.clone().subtract(2, 'months'), den: den };
            case 'sau_thang_gan_day':
                return { tu: today.clone().subtract(6, 'months'), den: den };
            default:
                return null;
        }
    }

    function clearKhoangNgayRadios() {
        $radios.prop('checked', false);
    }

    function setDateRange(tu, den) {
        $tu.val(tu.format('YYYY-MM-DD'));
        $den.val(den.format('YYYY-MM-DD'));
        var drp = $inp.data('daterangepicker');
        if (drp) {
            drp.setStartDate(tu.clone());
            drp.setEndDate(den.clone());
        }
        syncLabel();
    }

    var tu = ($tu.val() || '').trim();
    var den = ($den.val() || '').trim();
    var mTu = tu ? moment(tu, 'YYYY-MM-DD', true) : null;
    var mDen = den ? moment(den, 'YYYY-MM-DD', true) : null;

    var opts = {
        opens: 'right',
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Áp dụng',
            cancelLabel: 'Hủy',
            fromLabel: 'Từ',
            toLabel: 'Đến',
            customRangeLabel: 'Tùy chọn',
            firstDay: 1
        }
    };

    if (mTu && mTu.isValid() && mDen && mDen.isValid()) {
        opts.startDate = mTu;
        opts.endDate = mDen;
    } else if (mTu && mTu.isValid()) {
        opts.startDate = mTu;
        opts.endDate = mTu.clone();
    } else if (mDen && mDen.isValid()) {
        opts.startDate = mDen.clone();
        opts.endDate = mDen.clone();
    }

    $inp.daterangepicker(opts);

    function syncLabel() {
        var t = ($tu.val() || '').trim();
        var d = ($den.val() || '').trim();
        var a = t ? moment(t, 'YYYY-MM-DD', true) : null;
        var b = d ? moment(d, 'YYYY-MM-DD', true) : null;
        if (a && a.isValid() && b && b.isValid()) {
            $inp.val(a.format('DD/MM/YYYY') + ' - ' + b.format('DD/MM/YYYY'));
        } else if (a && a.isValid()) {
            $inp.val(a.format('DD/MM/YYYY') + ' - ' + a.format('DD/MM/YYYY'));
        } else if (b && b.isValid()) {
            $inp.val(b.format('DD/MM/YYYY') + ' - ' + b.format('DD/MM/YYYY'));
        } else {
            $inp.val('');
        }
    }

    syncLabel();

    $radios.on('change', function () {
        if (!this.checked) return;
        var range = resolveKhoangNgay(this.value);
        if (!range) return;
        setDateRange(range.tu, range.den);
        submitFilter();
    });

    var checkedRadio = $radios.filter(':checked').get(0);
    if (checkedRadio) {
        var initialRange = resolveKhoangNgay(checkedRadio.value);
        if (initialRange) {
            setDateRange(initialRange.tu, initialRange.den);
        }
    }

    $inp.on('apply.daterangepicker', function (ev, picker) {
        clearKhoangNgayRadios();
        $tu.val(picker.startDate.format('YYYY-MM-DD'));
        $den.val(picker.endDate.format('YYYY-MM-DD'));
        syncLabel();
    });

    $inp.on('cancel.daterangepicker', function () {
        clearKhoangNgayRadios();
        $tu.val('');
        $den.val('');
        $inp.val('');
    });
})();
</script>
@endpush
