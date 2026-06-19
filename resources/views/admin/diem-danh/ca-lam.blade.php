@extends('admin.layouts.app')

@section('content')
@php
    $hasFilter = filled($search ?? null)
        || ($tuan ?? now()->startOfWeek()->toDateString()) !== now()->startOfWeek()->toDateString();
    $tuanLoc = $tuan ?? now()->startOfWeek()->toDateString();
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
                    @if($hasFilter)
                    <a href="{{ route('admin.ca-lam') }}" class="btn btn-outline-secondary">Tuần này</a>
                    @endif
                </div>
                <div class="col-12 col-lg-auto ms-lg-auto">
                    <div class="text-muted">
                        Khoảng: <strong>{{ ($tuanBatDau ?? now()->startOfWeek())->format('d/m/Y') }}</strong>
                        → <strong>{{ ($tuanKetThuc ?? now()->endOfWeek())->format('d/m/Y') }}</strong>
                    </div>
                </div>
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
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="min-width: 48px;">STT</th>
                        <th style="min-width: 220px;">Nhân viên</th>
                        @foreach(($ngayTrongTuan ?? []) as $day)
                            @php
                                $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true);
                            @endphp
                            <th class="text-center small text-nowrap {{ $isWeekend ? 'ca-lam-weekend' : '' }}" style="min-width: 130px;">
                                {{ $thuLabel[$day->dayOfWeekIso] ?? $day->isoFormat('dddd') }} - {{ $day->format('j/n') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse(($nhanVien ?? []) as $u)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-medium">{{ $u->name }}</div>
                                <div class="small text-muted">{{ $u->email }}</div>
                            </td>
                            @foreach(($ngayTrongTuan ?? []) as $day)
                                @php
                                    $dateKey = $day->toDateString();
                                    $records = $bangCaLam[$dateKey][$u->id] ?? [];
                                @endphp
                                <td class="text-center align-middle">
                                    @if(count($records) > 0)
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($records as $record)
                                                @php
                                                    $ca = $record->caLamViec;
                                                @endphp
                                                @if($ca)
                                                <span class="badge bg-label-primary text-wrap" title="{{ \App\Models\CaLamViec::formatGio($ca->gio_bat_dau) }} – {{ \App\Models\CaLamViec::formatGio($ca->gio_ket_thuc) }}">
                                                    {{ $ca->ten_ca }}
                                                </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($ngayTrongTuan ?? []) }}" class="text-center py-4 text-muted">
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
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
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
