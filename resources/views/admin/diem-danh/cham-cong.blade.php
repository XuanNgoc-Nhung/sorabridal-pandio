@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\User::SAP_XEP_HO_TEN;
    $sapXepTheo = $sapXepTheo ?? $sapXepTheoMacDinh;
    $thuTu = $thuTu ?? 'asc';
    $hasFilter = filled($userIdLoc ?? null)
        || filled($trangThai ?? null)
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'asc';
@endphp
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
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label" for="user_id">Nhân viên</label>
                    <select class="select2-admin form-select" id="user_id" name="user_id" data-placeholder="Tất cả nhân viên">
                        <option value="">Tất cả nhân viên</option>
                        @foreach(($danhSachNhanVienLoc ?? []) as $nv)
                            <option value="{{ $nv->id }}" @selected((int)($userIdLoc ?? 0) === (int) $nv->id)>
                                {{ $nv->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label" for="trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="trang_thai" name="trang_thai" data-placeholder="Tất cả">
                        <option value="" @selected(empty($trangThai ?? null))>Tất cả</option>
                        <option value="da_cham" @selected(($trangThai ?? '') === 'da_cham')>Đã chấm công</option>
                        <option value="chua_cham" @selected(($trangThai ?? '') === 'chua_cham')>Chưa chấm công</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(($chamCongSapXepOptions ?? []) as $value => $label)
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
                <div class="col-12 col-md-auto d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.diem-danh.cham-cong', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                    <a href="{{ route('admin.diem-danh.cham-cong') }}" class="btn btn-outline-secondary">Tháng hiện tại</a>
                </div>
                {{-- <div class="col-12 col-lg-auto ms-lg-auto">
                    <div class="text-muted">
                        Khoảng: <strong>{{ ($start ?? now()->startOfMonth())->format('d/m/Y') }}</strong>
                        → <strong>{{ ($end ?? now()->endOfMonth())->format('d/m/Y') }}</strong>
                    </div>
                </div> --}}
            </div>
            {{-- <p class="small text-muted mb-0 mt-2">
                <strong>Trạng thái tháng:</strong> đã chấm công = có ít nhất một ngày điểm danh trong tháng; chưa chấm công = không có ngày nào.
            </p> --}}
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Chấm công theo tháng</span>
            <span class="badge bg-label-primary fw-normal">{{ count($nhanVien ?? []) }} nhân viên</span>
        </h5>
        <div class="card-body">
        {{-- <p class="small text-muted mb-2">Mỗi ô: giờ vào–giờ ra
             bên dưới là <strong>giờ làm cơ bản</strong> / <strong>giờ tăng ca</strong> (đơn vị: giờ).
            </p> --}}
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
                        @php
                            $coChamCongTrongThang = collect($bangChamCong ?? [])->contains(fn ($byUser) => isset($byUser[$u->id]));
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-medium">{{ $u->name }}</div>
                                <div class="small text-muted">{{ $u->email }}</div>
                                @if($coChamCongTrongThang)
                                    <span class="badge bg-label-success mt-1">Đã chấm công</span>
                                @else
                                    <span class="badge bg-label-secondary mt-1">Chưa chấm công</span>
                                @endif
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
