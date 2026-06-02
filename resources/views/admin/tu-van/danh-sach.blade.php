@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\DangKyTuVan::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('tu_khoa')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc';
@endphp
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form action="{{ route('admin.tu-van.danh-sach') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_khoa">Từ khóa</label>
                    <input type="text"
                           class="form-control @error('tu_khoa') is-invalid @enderror"
                           id="tu_khoa"
                           name="tu_khoa"
                           value="{{ old('tu_khoa', request('tu_khoa')) }}"
                           placeholder="Nhập...">
                    @error('tu_khoa')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\DangKyTuVan::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.tu-van.danh-sach') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header">Danh sách khách hàng đăng ký tư vấn</h5>
        <div class="card-body">
        <div class="table-responsive table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        <th>Cặp đôi</th>
                        <th>SĐT</th>
                        <th>Ngày cưới</th>
                        <th>Phim trường</th>
                        <th>Gói dịch vụ</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($dangKyTuVans as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($dangKyTuVans->currentPage() - 1) * $dangKyTuVans->perPage() + $index + 1 }}</td>
                        <td class="text-wrap">
                            <div class="fw-medium">{{ $item->ten_co_dau ?: '—' }}</div>
                            <div class="small text-muted mt-1">{{ $item->ten_chu_re ?: '—' }}</div>
                        </td>
                        <td>
                            @if($item->so_dien_thoai)
                            <a href="tel:{{ $item->so_dien_thoai }}" class="text-body">{{ $item->so_dien_thoai }}</a>
                            @else
                            —
                            @endif
                        </td>
                        <td>{{ $item->ngay_cuoi_du_kien?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-wrap">{{ $item->phim_truong_quan_tam ?: '—' }}</td>
                        <td class="text-wrap">{{ $item->goi_dich_vu_quan_tam ?: '—' }}</td>
                        <td class="text-wrap">
                            <div class="tu-van-note">{{ $item->ghi_chu ?: '—' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có khách hàng nào đăng ký tư vấn.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$dangKyTuVans" label="lượt đăng ký" />
        </div>
    </div>
</div>

@push('styles')
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
}

.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
    vertical-align: middle;
}

.tu-van-note {
    white-space: normal;
    min-width: 220px;
}
</style>
@endpush
@endsection
