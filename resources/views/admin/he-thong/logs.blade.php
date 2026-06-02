@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
@endpush

@section('title', 'Logs người dùng | Wedding Studio')

@section('content')
@php
    $hasFilter = ($userId ?? null) !== null || filled($search ?? null);
@endphp
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
            <form action="{{ route('admin.he-thong.logs') }}" method="GET">
                <div class="row g-3 align-items-end admin-filter-row">
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label" for="ngay">Ngày</label>
                        <select class="select2-admin form-select" id="ngay" name="ngay">
                            @forelse($availableDates as $date)
                                <option value="{{ $date }}" @selected($ngay === $date)>
                                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                </option>
                            @empty
                                <option value="{{ $ngay }}" selected>
                                    {{ \Carbon\Carbon::parse($ngay)->format('d/m/Y') }}
                                </option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label" for="user_id">ID người dùng</label>
                        <input type="number"
                               class="form-control"
                               id="user_id"
                               name="user_id"
                               min="1"
                               value="{{ $userId }}"
                               placeholder="VD: 1">
                    </div>
                    <div class="col-12 col-md-4 col-lg-3">
                        <label class="form-label" for="search">Tìm trong log</label>
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Đường dẫn, tên, method...">
                    </div>
                    <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                        </button>
                        @if($hasFilter)
                        <a href="{{ route('admin.he-thong.logs', ['ngay' => $ngay]) }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-0">
        {{-- <h5 class="card-header">
            Log hành động người dùng
            <span class="text-muted fw-normal small ms-1">— {{ \Carbon\Carbon::parse($ngay)->format('d/m/Y') }}</span>
        </h5> --}}
        <div class="card-body">
            @if(! $coFileLog)
            <div class="alert alert-warning mb-0" role="alert">
                Không có file log cho ngày đã chọn (<code>laravel-{{ $ngay }}.log</code>).
            </div>
            @else
            <div class="table-responsive table-wrapper-bordered">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 48px;">STT</th>
                            <th style="width: 11rem;">Thời gian</th>
                            <th style="width: 12rem;">Người dùng</th>
                            <th style="width: 5rem;">Method</th>
                            <th>Đường dẫn</th>
                            <th style="width: 14rem;">Phản hồi</th>
                            <th class="text-center" style="width: 4rem;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSach as $index => $entry)
                        @php
                            $collapseId = 'log-detail-'.$danhSach->currentPage().'-'.$index;
                            $user = $entry['user'] ?? null;
                            $requestData = $entry['request'] ?? [];
                            $logDetailJson = json_encode([
                                'user' => $entry['user'] ?? null,
                                'request' => $entry['request'] ?? [],
                                'response' => $entry['response'] ?? [],
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        @endphp
                        <tr>
                            <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                            <td class="text-nowrap small">{{ $entry['logged_at'] ?? '—' }}</td>
                            <td>
                                @if($user)
                                    <div class="fw-medium">{{ $user['name'] ?? '—' }}</div>
                                    <div class="text-muted small">ID: {{ $user['id'] ?? '—' }}</div>
                                @else
                                    <span class="text-muted">Khách</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $requestData['method'] ?? '—' }}</span>
                            </td>
                            <td class="font-monospace small text-break">{{ $requestData['path'] ?? '—' }}</td>
                            <td class="small">{{ $entry['response_summary'] ?? '—' }}</td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}"
                                        aria-expanded="false"
                                        aria-controls="{{ $collapseId }}"
                                        title="Xem chi tiết"
                                        aria-label="Xem chi tiết">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                    <span class="visually-hidden">Xem chi tiết</span>
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="{{ $collapseId }}">
                            <td colspan="7" class="bg-light">
                                <textarea class="form-control font-monospace small"
                                          rows="12"
                                          readonly
                                          aria-label="Chi tiết log">{{ $logDetailJson }}</textarea>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Không có bản ghi <strong>User action</strong> trong ngày này.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination-info :paginator="$danhSach" label="bản ghi log" />
            @endif
        </div>
    </div>
</div>
@endsection
