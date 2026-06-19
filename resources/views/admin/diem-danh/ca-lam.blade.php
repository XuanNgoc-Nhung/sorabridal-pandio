@extends('admin.layouts.app')

@section('content')
@php
    $hasFilter = filled($search ?? null)
        || ($tuan ?? now()->startOfWeek()->toDateString()) !== now()->startOfWeek()->toDateString();
    $tuanLoc = $tuan ?? now()->startOfWeek()->toDateString();
    $tuanCarbon = \Illuminate\Support\Carbon::parse($tuanLoc)->startOfWeek();
    $tuanTruoc = (clone $tuanCarbon)->subWeek()->toDateString();
    $tuanToi = (clone $tuanCarbon)->addWeek()->toDateString();
    $laTuanNay = $tuanLoc === now()->startOfWeek()->toDateString();
    $thuLabel = [
        '1' => 'Thứ hai',
        '2' => 'Thứ ba',
        '3' => 'Thứ tư',
        '4' => 'Thứ năm',
        '5' => 'Thứ sáu',
        '6' => 'Thứ bảy',
        '7' => 'Chủ nhật',
    ];
@endphp
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
        <form method="GET" action="{{ route('admin.ca-lam') }}">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-4 col-lg-3">
                    <label class="form-label" for="ca_lam_tuan">Tuần</label>
                    <input type="text"
                           class="form-control"
                           id="ca_lam_tuan"
                           placeholder="Chọn tuần"
                           autocomplete="off"
                           value="">
                    <input type="hidden" name="tuan" id="ca_lam_tuan_value" value="{{ $tuanLoc }}">
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <label class="form-label" for="search">Từ khóa</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Tên hoặc email nhân viên...">
                </div>
                <div class="col-12 col-md-auto d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.ca-lam', array_filter(['tuan' => $tuanTruoc, 'search' => $search ?? null])) }}"
                       class="btn btn-outline-secondary">
                        Tuần trước
                    </a>
                    @if($hasFilter)
                    <a href="{{ route('admin.ca-lam', array_filter(['search' => $search ?? null])) }}"
                       class="btn btn-outline-secondary @if($laTuanNay) active @endif">
                        Tuần này
                    </a>
                    @endif
                    <a href="{{ route('admin.ca-lam', array_filter(['tuan' => $tuanToi, 'search' => $search ?? null])) }}"
                       class="btn btn-outline-secondary">
                        Tuần tới
                    </a>
                </div>
                {{-- <div class="col-12 col-lg-auto ms-lg-auto">
                    <div class="text-muted">
                        Khoảng: <strong>{{ ($tuanBatDau ?? now()->startOfWeek())->format('d/m/Y') }}</strong>
                        → <strong>{{ ($tuanKetThuc ?? now()->endOfWeek())->format('d/m/Y') }}</strong>
                    </div>
                </div> --}}
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Đăng ký ca làm theo tuần</span>
            <span class="badge bg-label-primary fw-normal">{{ count($nhanVien ?? []) }} nhân viên</span>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered ca-lam-table-wrap">
            <table class="table table-bordered table-hover mb-0 ca-lam-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle ca-lam-sticky ca-lam-sticky-col-1" style="min-width: 48px;">STT</th>
                        <th class="ca-lam-sticky ca-lam-sticky-col-2" style="min-width: 220px;">Nhân viên</th>
                        <th style="min-width: 230px;">Ca làm (cả tuần)</th>
                        @foreach(($ngayTrongTuan ?? []) as $day)
                            @php
                                $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true);
                            @endphp
                            <th class="text-center small text-nowrap ca-lam-ngay-col {{ $isWeekend ? 'ca-lam-weekend' : '' }}">
                                {{ $thuLabel[$day->dayOfWeekIso] ?? $day->isoFormat('dddd') }} - {{ $day->format('j/n') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse(($nhanVien ?? []) as $u)
                        @php
                            $caLamDaChon = $caLamTheoNhanVien[$u->id] ?? null;
                        @endphp
                        <tr data-user-id="{{ $u->id }}">
                            <td class="text-center align-middle ca-lam-sticky ca-lam-sticky-col-1">{{ $loop->iteration }}</td>
                            <td class="ca-lam-sticky ca-lam-sticky-col-2">
                                <div class="fw-medium">{{ $u->name }}</div>
                                <div class="small text-muted">{{ $u->email }}</div>
                            </td>
                            <td class="align-middle">
                                <select class="form-select js-ca-lam-select js-ca-lam-tuan-select"
                                        data-user-id="{{ $u->id }}"
                                        data-tuan="{{ $tuanLoc }}"
                                        data-placeholder="Chọn ca làm"
                                        style="width: 100%;">
                                    @include('admin.diem-danh.partials.ca-lam-select-options', ['selected' => $caLamDaChon])
                                </select>
                            </td>
                            @foreach(($ngayTrongTuan ?? []) as $day)
                                @php
                                    $dateKey = $day->toDateString();
                                    $records = $bangCaLam[$dateKey][$u->id] ?? [];
                                    $caIds = collect($records)->pluck('ca_lam_id')->unique()->values();
                                    $caNgayDaChon = $caIds->count() === 1 ? $caIds->first() : null;
                                @endphp
                                <td class="align-middle ca-lam-ngay-cell ca-lam-ngay-col" data-ngay="{{ $dateKey }}">
                                    <select class="form-select js-ca-lam-select js-ca-lam-ngay-select"
                                            data-user-id="{{ $u->id }}"
                                            data-ngay="{{ $dateKey }}"
                                            data-placeholder="Chọn ca"
                                            style="width: 100%;">
                                        @include('admin.diem-danh.partials.ca-lam-select-options', ['selected' => $caNgayDaChon, 'chiTen' => true])
                                    </select>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($ngayTrongTuan ?? []) }}" class="text-center py-4 text-muted">
                                Không có nhân viên phù hợp bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<style>
.table-wrapper-bordered {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
.table-wrapper-bordered .table thead th.ca-lam-weekend {
    background-color: rgba(var(--bs-primary-rgb, 13, 110, 47), 0.9) !important;
}
[data-bs-theme='dark'] .table-wrapper-bordered .table thead th.ca-lam-weekend {
    background-color: rgba(13, 110, 47, 0.08) !important;
}
.ca-lam-table .ca-lam-sticky {
    position: sticky;
    z-index: 2;
    background-color: #fff;
    background-clip: padding-box;
}
.ca-lam-table thead .ca-lam-sticky {
    z-index: 6;
    background-color: #f8f9fa;
}
.ca-lam-table.table-hover > tbody > tr:hover > .ca-lam-sticky {
    background-color: #f5f5f9;
}
[data-bs-theme='dark'] .ca-lam-table .ca-lam-sticky {
    background-color: #2f3349;
}
[data-bs-theme='dark'] .ca-lam-table thead .ca-lam-sticky {
    background-color: #353a52;
}
[data-bs-theme='dark'] .ca-lam-table.table-hover > tbody > tr:hover > .ca-lam-sticky {
    background-color: #3a3f5c;
}
.ca-lam-table .ca-lam-sticky-col-1 {
    left: 0;
    min-width: 48px;
}
.ca-lam-table .ca-lam-sticky-col-2 {
    left: 48px;
    min-width: 220px;
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.08);
}
[data-bs-theme='dark'] .ca-lam-table .ca-lam-sticky-col-2 {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.35);
}
.ca-lam-table .ca-lam-ngay-col {
    width: 130px;
}
.ca-lam-ngay-cell .select2-container {
    width: 100% !important;
    max-width: 100%;
}
.ca-lam-ngay-cell .select2-selection--single {
    min-height: 32px;
}
.ca-lam-ngay-cell .select2-selection__rendered {
    padding-right: 1.5rem;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script>
(function () {
    var $ = window.jQuery;
    if (!$ || !$.fn.select2) return;

    var CAP_NHAT_TUAN_URL = @json(route('admin.ca-lam.cap-nhat-tuan'));
    var CAP_NHAT_NGAY_URL = @json(route('admin.ca-lam.cap-nhat-ngay'));
    var CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') || {}).content || @json(csrf_token());

    function setSelectValue($sel, value) {
        $sel.data('ca-lam-skip', true);
        $sel.val(value || '').trigger('change.select2');
        $sel.data('prev-value', value || '');
        $sel.data('ca-lam-skip', false);
    }

    function syncWeekSelectFromDays($row) {
        var $weekSel = $row.find('.js-ca-lam-tuan-select');
        var values = [];
        $row.find('.js-ca-lam-ngay-select').each(function () {
            values.push($(this).val() || '');
        });

        var first = values.length ? values[0] : '';
        var allSame = values.length > 0 && values.every(function (v) { return v === first && v !== ''; });
        var newWeekVal = allSame ? first : '';

        if (String($weekSel.val() || '') !== String(newWeekVal)) {
            setSelectValue($weekSel, newWeekVal);
        }
    }

    function syncDaySelectsInRow($row, caLamId) {
        $row.find('.js-ca-lam-ngay-select').each(function () {
            setSelectValue($(this), caLamId);
        });
    }

    function postJson(url, payload) {
        return RestApi.post(url, payload);
    }

    function handleSelectChange($sel, url, payload, onSuccess) {
        if ($sel.data('ca-lam-skip') || $sel.data('ca-lam-loading')) {
            return;
        }

        var newValue = $sel.val() || '';
        var prevValue = $sel.data('prev-value');
        if (prevValue === undefined) {
            prevValue = '';
        }
        if (String(prevValue) === String(newValue)) {
            return;
        }

        $sel.data('ca-lam-loading', true).prop('disabled', true);

        postJson(url, payload)
            .then(function (result) {
                if (!result.ok || !result.data.success) {
                    throw new Error((result.data && result.data.message) || 'Không thể cập nhật ca làm.');
                }

                $sel.data('prev-value', newValue);
                if (typeof onSuccess === 'function') {
                    onSuccess(result.data);
                }
            })
            .catch(function () {
                setSelectValue($sel, prevValue);
            })
            .finally(function () {
                $sel.data('ca-lam-loading', false).prop('disabled', false);
            });
    }

    $(function () {
        $('.js-ca-lam-select').each(function () {
            var $el = $(this);
            if ($el.data('select2')) {
                return;
            }

            $el.select2({
                placeholder: $el.data('placeholder') || 'Chọn ca làm',
                allowClear: $el.hasClass('js-ca-lam-tuan-select'),
                width: '100%',
                dropdownParent: $('body'),
            });
            $el.data('prev-value', $el.val() || '');
        });
    });

    $(document).on('change', '.js-ca-lam-tuan-select', function () {
        var $sel = $(this);
        var $row = $sel.closest('tr');
        var caLamId = $sel.val() || '';

        handleSelectChange($sel, CAP_NHAT_TUAN_URL, {
            nguoi_dung_id: $sel.data('user-id'),
            tuan: $sel.data('tuan'),
            ca_lam_id: caLamId || null,
        }, function () {
            syncDaySelectsInRow($row, caLamId);
        });
    });

    $(document).on('change', '.js-ca-lam-ngay-select', function () {
        var $sel = $(this);
        var $row = $sel.closest('tr');
        var caLamId = $sel.val() || '';

        handleSelectChange($sel, CAP_NHAT_NGAY_URL, {
            nguoi_dung_id: $sel.data('user-id'),
            ngay_lam: $sel.data('ngay'),
            ca_lam_id: caLamId || null,
        }, function () {
            syncWeekSelectFromDays($row);
        });
    });
})();
</script>
<script>
(function () {
    if (window.__caLamTuanPickerInit) return;
    window.__caLamTuanPickerInit = true;

    var $ = window.jQuery;
    if (!$ || !$.fn.daterangepicker || typeof moment === 'undefined') return;

    var $inp = $('#ca_lam_tuan');
    var $hidden = $('#ca_lam_tuan_value');
    var $form = $inp.closest('form');
    if (!$inp.length || !$hidden.length) return;

    function weekFromHidden() {
        var v = ($hidden.val() || '').trim();
        if (!v) return moment().startOf('isoWeek');
        var m = moment(v, 'YYYY-MM-DD', true);
        return m.isValid() ? m.startOf('isoWeek') : moment().startOf('isoWeek');
    }

    function syncLabel(start) {
        var end = start.clone().endOf('isoWeek');
        $inp.val(start.format('DD/MM/YYYY') + ' – ' + end.format('DD/MM/YYYY'));
    }

    function syncHidden(start) {
        $hidden.val(start.clone().startOf('isoWeek').format('YYYY-MM-DD'));
    }

    function applyWeek(picker, closePicker) {
        var start = picker.startDate.clone().startOf('isoWeek');
        var end = start.clone().endOf('isoWeek');
        picker.setStartDate(start);
        picker.setEndDate(end);
        syncHidden(start);
        syncLabel(start);
        if (closePicker) {
            picker.hide();
        }
    }

    var startWeek = weekFromHidden();

    $inp.daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        minYear: 2000,
        maxYear: 2100,
        autoApply: true,
        autoUpdateInput: false,
        opens: 'right',
        startDate: startWeek.clone(),
        endDate: startWeek.clone().endOf('isoWeek'),
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Áp dụng',
            cancelLabel: 'Hủy',
            firstDay: 1,
            daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
            monthNames: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ]
        }
    });

    syncLabel(startWeek);

    $inp.on('apply.daterangepicker', function (ev, picker) {
        applyWeek(picker, false);
    });

    if ($form.length) {
        $form.on('submit', function () {
            var drp = $inp.data('daterangepicker');
            if (drp) {
                applyWeek(drp, false);
            }
        });
    }
})();
</script>
@endpush
@endsection
