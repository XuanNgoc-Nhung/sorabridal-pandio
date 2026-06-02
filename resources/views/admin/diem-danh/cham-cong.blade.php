@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('admin.diem-danh.cham-cong') }}">
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
                    <a href="{{ route('admin.diem-danh.cham-cong') }}" class="btn btn-outline-secondary">Tháng hiện tại</a>
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
        <h5 class="card-header">Chấm công theo tháng</h5>
        <div class="card-body">
        <p class="small text-muted mb-2">Mỗi ô: giờ vào–giờ ra
             {{-- bên dưới là <strong>giờ làm cơ bản</strong> / <strong>giờ tăng ca</strong> (đơn vị: giờ). --}}
            </p>
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="min-width: 48px;">STT</th>
                        <th style="min-width: 220px;">Nhân viên</th>
                        @php
                            $thuLabel = ['1' => 'T2', '2' => 'T3', '3' => 'T4', '4' => 'T5', '5' => 'T6', '6' => 'T7', '7' => 'CN'];
                        @endphp
                        @foreach(($ngayTrongThang ?? []) as $day)
                            @php
                                $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true);
                            @endphp
                            <th class="text-center {{ $isWeekend ? 'bg-secondary bg-opacity-25' : '' }}" style="min-width: 72px;">
                                <div class="fw-semibold">{{ $day->format('d') }}</div>
                                <div class="small text-muted">{{ $thuLabel[$day->dayOfWeekIso] ?? $day->isoFormat('dd') }}</div>
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
                            @foreach(($ngayTrongThang ?? []) as $day)
                                @php
                                    $dateKey = $day->toDateString();
                                    $record = $bangChamCong[$dateKey][$u->id] ?? null;
                                    $diemDanh = $record?->diemDanh;
                                    $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true); // 6=Thứ 7, 7=Chủ nhật
                                @endphp
                                <td class="text-center align-middle {{ $isWeekend ? 'bg-secondary bg-opacity-25' : '' }}">
                                    @if($record && $diemDanh)
                                        <div class="small">
                                            <span class="text-success fw-medium">{{ $diemDanh->gio_vao ? $diemDanh->gio_vao->format('H:i') : '—' }}</span>
                                            <span class="text-muted mx-1">–</span>
                                            <span class="gio-ra fw-medium">{{ $diemDanh->gio_ra ? $diemDanh->gio_ra->format('H:i') : '—' }}</span>
                                        </div>
                                        {{-- <div class="small mt-1">
                                            <span class="text-primary" title="Giờ làm cơ bản">{{ $diemDanh->gio_lam_co_ban !== null ? number_format($diemDanh->gio_lam_co_ban, 1) . ' h' : '—' }}</span>
                                            <span class="text-muted mx-1">/</span>
                                            <span class="text-warning text-dark" title="Giờ tăng ca">{{ $diemDanh->gio_lam_tang_ca !== null ? number_format($diemDanh->gio_lam_tang_ca, 1) . ' h' : '—' }}</span>
                                        </div> --}}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($ngayTrongThang ?? []) }}" class="text-center py-4 text-muted">
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
.gio-ra { color: #e8590c; }
</style>
@endpush
@endsection