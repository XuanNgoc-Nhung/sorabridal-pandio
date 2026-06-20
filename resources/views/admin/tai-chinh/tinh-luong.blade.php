@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('admin.tai-chinh.tinh-luong') }}">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="month">Tháng</label>
                    <select class="select2-admin form-select" id="month" name="month" data-placeholder="Chọn tháng">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected((int)($month ?? now()->month) === $m)>
                                Tháng {{ $m }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="year">Năm</label>
                    <input type="number"
                           class="form-control"
                           id="year"
                           name="year"
                           min="2000"
                           max="2100"
                           value="{{ (int)($year ?? now()->year) }}">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.tai-chinh.tinh-luong') }}" class="btn btn-outline-secondary">Tháng hiện tại</a>
                </div>
                <div class="col-12 col-lg-auto ms-lg-auto">
                    <div class="text-muted">
                        Khoảng: <strong>{{ ($start ?? now()->startOfMonth())->format('d/m/Y') }}</strong>
                        → <strong>{{ ($end ?? now()->endOfMonth())->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header">Tính lương theo tháng</h5>
        <div class="card-body">
        <p class="small text-muted mb-2">
            <span class="me-3">Ghi chú:</span>
            <span class="luong-co-ban-dot me-1"></span><span class="text-body-secondary">Lương cơ bản</span>
            <span class="mx-2">·</span>
            <span class="luong-tang-ca-dot me-1"></span><span class="text-body-secondary">Lương tăng ca</span>
        </p>
        <div class="table-responsive text-nowrap table-wrapper-bordered tinh-luong-table-wrap">
            <table class="table table-bordered table-hover mb-0 tinh-luong-table">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center tinh-luong-sticky tinh-luong-sticky-col-1" style="min-width: 48px;">STT</th>
                        <th rowspan="2" class="align-middle tinh-luong-sticky tinh-luong-sticky-col-2" style="min-width: 220px;">Nhân viên</th>
                        @php
                            $thuLabel = ['1' => 'T2', '2' => 'T3', '3' => 'T4', '4' => 'T5', '5' => 'T6', '6' => 'T7', '7' => 'CN'];
                        @endphp
                        @foreach(($ngayTrongThang ?? []) as $day)
                            @php
                                $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true);
                            @endphp
                            <th rowspan="2" class="text-center align-middle small text-nowrap tinh-luong-ngay-col {{ $isWeekend ? 'tinh-luong-weekend' : '' }}" style="min-width: 72px;">
                                {{ $thuLabel[$day->dayOfWeekIso] ?? $day->isoFormat('dd') }} {{ $day->format('j/n') }}
                            </th>
                        @endforeach
                        <th colspan="3" class="text-center text-nowrap" style="min-width: 1px;">Lương</th>
                    </tr>
                    <tr>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">Cơ bản</th>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">Tăng ca</th>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">Tổng</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse(($nhanVien ?? []) as $u)
                        <tr>
                            <td class="text-center align-middle tinh-luong-sticky tinh-luong-sticky-col-1">{{ $loop->iteration }}</td>
                            <td class="tinh-luong-sticky tinh-luong-sticky-col-2">
                                <div class="fw-medium">{{ $u->name }}</div>
                                <div class="small text-muted">{{ $u->email }}</div>
                            </td>
                            @foreach(($ngayTrongThang ?? []) as $day)
                                @php
                                    $dateKey = $day->toDateString();
                                    $record = $bangChamCong[$dateKey][$u->id] ?? null;
                                    $diemDanh = $record?->diemDanh;
                                    $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true); // 6=Thứ 7, 7=Chủ nhật
                                @endphp
                                <td class="text-center align-middle tinh-luong-ngay-col {{ $isWeekend ? 'tinh-luong-weekend' : '' }}">
                                    @if($record && $diemDanh)
                                        <div class="small">
                                            <div class="luong-co-ban" title="Lương cơ bản">{{ number_format((float)($diemDanh->luong_co_ban ?? 0), 0, ',', '.') }} đ</div>
                                            <div class="luong-tang-ca" title="Lương tăng ca">{{ number_format((float)($diemDanh->luong_tang_ca ?? 0), 0, ',', '.') }} đ</div>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                            @php
                                $luong = $bangLuongThang[$u->id] ?? ['luong_co_ban' => 0, 'luong_tang_ca' => 0, 'tong_luong' => 0];
                            @endphp
                            <td class="text-end align-middle">
                                {{ number_format($luong['luong_co_ban'], 0, ',', '.') }} đ
                            </td>
                            <td class="text-end align-middle">
                                {{ number_format($luong['luong_tang_ca'], 0, ',', '.') }} đ
                            </td>
                            <td class="text-end align-middle fw-semibold">
                                {{ number_format($luong['tong_luong'], 0, ',', '.') }} đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($ngayTrongThang ?? []) + 3 }}" class="text-center py-4 text-muted">
                                Chưa có nhân viên để hiển thị.
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
.tinh-luong-table .tinh-luong-sticky {
    position: sticky;
    z-index: 2;
    background-color: #fff;
    background-clip: padding-box;
}
.tinh-luong-table thead .tinh-luong-sticky {
    z-index: 6;
    background-color: #f8f9fa;
}
.tinh-luong-table.table-hover > tbody > tr:hover > .tinh-luong-sticky {
    background-color: #f5f5f9;
}
[data-bs-theme='dark'] .tinh-luong-table .tinh-luong-sticky {
    background-color: #2f3349;
}
[data-bs-theme='dark'] .tinh-luong-table thead .tinh-luong-sticky {
    background-color: #353a52;
}
[data-bs-theme='dark'] .tinh-luong-table.table-hover > tbody > tr:hover > .tinh-luong-sticky {
    background-color: #3a3f5c;
}
.tinh-luong-table .tinh-luong-sticky-col-1 {
    left: 0;
    min-width: 48px;
}
.tinh-luong-table .tinh-luong-sticky-col-2 {
    left: 48px;
    min-width: 220px;
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.08);
}
[data-bs-theme='dark'] .tinh-luong-table .tinh-luong-sticky-col-2 {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.35);
}
.tinh-luong-table .tinh-luong-ngay-col.tinh-luong-weekend {
    background-color: rgba(var(--bs-secondary-rgb, 108, 117, 125), 0.15);
}
.tinh-luong-table thead .tinh-luong-ngay-col.tinh-luong-weekend {
    background-color: rgba(var(--bs-secondary-rgb, 108, 117, 125), 0.25);
}
[data-bs-theme='dark'] .tinh-luong-table .tinh-luong-ngay-col.tinh-luong-weekend {
    background-color: rgba(108, 117, 125, 0.12);
}
[data-bs-theme='dark'] .tinh-luong-table thead .tinh-luong-ngay-col.tinh-luong-weekend {
    background-color: rgba(108, 117, 125, 0.2);
}
/* Lương cơ bản: xanh lá */
.luong-co-ban { color: #0d6e2f; font-weight: 500; }
/* Lương tăng ca: cam */
.luong-tang-ca { color: #c25a0a; font-weight: 500; }
/* Chấm màu cho ghi chú */
.luong-co-ban-dot { display: inline-block; width: 0.6rem; height: 0.6rem; border-radius: 50%; background: #0d6e2f; vertical-align: middle; }
.luong-tang-ca-dot { display: inline-block; width: 0.6rem; height: 0.6rem; border-radius: 50%; background: #c25a0a; vertical-align: middle; }
</style>
@endpush
@endsection
