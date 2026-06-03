@extends('admin.layouts.app')

@section('title', 'Tổng quan | Wedding Studio')

@php
    use App\Models\HopDongCuoi;

    $nhanLabel = static function (HopDongCuoi $hd) use ($nhanVien): string {
        $parts = [];
        if ($nhanVien && (int) $hd->tho_chup_id === (int) $nhanVien->id) {
            $parts[] = 'Chụp';
        }
        if ($nhanVien && (int) $hd->tho_make_id === (int) $nhanVien->id) {
            $parts[] = 'Make';
        }
        if ($nhanVien && (int) $hd->tho_edit_id === (int) $nhanVien->id) {
            $parts[] = 'Edit';
        }

        return $parts !== [] ? implode(' · ', $parts) : 'Sale / HĐ';
    };

    $ngayChupHienThi = static function (HopDongCuoi $hd): string {
        $ngay = $hd->ngay_chup_thuc_te ?? $hd->ngay_chup_du_kien;

        return $ngay ? $ngay->format('d/m/Y') : '—';
    };

    $badgeTrangThai = static function (string $ma): string {
        return match ($ma) {
            'dang_thuc_hien' => 'bg-label-primary',
            'tre_chup', 'tre_edit' => 'bg-label-danger',
            'da_huy' => 'bg-label-secondary',
            default => 'bg-label-warning',
        };
    };
@endphp

@section('content')
<div class="row g-2 mb-2">
    <div class="col-12">
        <div class="card mb-0 border-0 shadow-sm dashboard-hero overflow-hidden">
            <div class="card-body py-3 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <span class="avatar avatar-md rounded bg-info text-white flex-shrink-0 shadow-sm">
                        <i class="ti tabler-user-circle fs-4"></i>
                    </span>
                    <div class="min-w-0">
                        <h5 class="mb-0 text-heading">Xin chào, {{ $user->name }}</h5>
                        <small class="text-muted">
                            {{ $user->role_label }}
                            @if ($tenPhongBan)
                                · {{ $tenPhongBan }}
                            @endif
                            @if ($nhanVien?->vi_tri_lam_viec)
                                · {{ $nhanVien->vi_tri_lam_viec }}
                            @endif
                        </small>
                    </div>
                </div>
                @if (Route::has('admin.nhan-su.cong-viec-cua-toi'))
                <a href="{{ route('admin.nhan-su.cong-viec-cua-toi') }}" class="btn btn-sm btn-primary">
                    <i class="ti tabler-briefcase me-1"></i> Công việc của tôi
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-primary border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-primary bg-opacity-10">
            <div class="card-body py-2 px-3">
                <span class="d-block text-muted" style="font-size: 0.7rem;">HĐ liên quan</span>
                <span class="fw-semibold fs-5 lh-sm d-block">{{ number_format($soHopDongLienQuan) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-success border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-success bg-opacity-10">
            <div class="card-body py-2 px-3">
                <span class="d-block text-muted" style="font-size: 0.7rem;">Đang thực hiện</span>
                <span class="fw-semibold fs-5 lh-sm d-block text-success">{{ number_format($soHopDongDangThucHien) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-danger border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-danger bg-opacity-10">
            <div class="card-body py-2 px-3">
                <span class="d-block text-muted" style="font-size: 0.7rem;">Trễ chụp / edit</span>
                <span class="fw-semibold fs-5 lh-sm d-block text-danger">{{ number_format($soHopDongTreChup + $soHopDongTreEdit) }}</span>
                <small class="text-muted">{{ number_format($soHopDongTreChup) }} chụp · {{ number_format($soHopDongTreEdit) }} edit</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-warning border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-warning bg-opacity-10">
            <div class="card-body py-2 px-3">
                <span class="d-block text-muted" style="font-size: 0.7rem;">Vai trò trên HĐ</span>
                <span class="fw-semibold small lh-sm d-block">{{ number_format($soHopDongChup) }} chụp</span>
                <span class="fw-semibold small lh-sm d-block">{{ number_format($soHopDongMake) }} make · {{ number_format($soHopDongEdit) }} edit</span>
                @if ($soHopDongSale > 0)
                <small class="text-muted">{{ number_format($soHopDongSale) }} sale</small>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-lg-7">
        <div class="card h-100 mb-0 border-primary border-opacity-25 shadow-sm">
            <div class="card-header py-2 px-3 bg-primary bg-opacity-10 border-bottom border-primary border-opacity-20">
                <h6 class="mb-0 text-primary"><i class="ti tabler-calendar-event me-1"></i>Lịch chụp sắp tới (14 ngày)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-primary bg-opacity-10">
                        <tr>
                            <th class="small">Mã / Khách</th>
                            <th class="small">Ngày chụp</th>
                            <th class="small">Vai trò</th>
                            <th class="small">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($hopDongSapChup as $hd)
                        <tr>
                            <td>
                                <span class="fw-medium d-block">{{ $hd->ma_hop_dong ?: ('#'.$hd->id) }}</span>
                                <span class="text-muted">{{ $hd->ten_chu_re }} &amp; {{ $hd->ten_co_dau }}</span>
                            </td>
                            <td>{{ $ngayChupHienThi($hd) }}</td>
                            <td><span class="badge bg-label-secondary">{{ $nhanLabel($hd) }}</span></td>
                            <td>
                                <span class="badge {{ $badgeTrangThai($hd->trang_thai_hop_dong) }}">
                                    {{ $trangThaiCuoiTongQuanLabels[$hd->trang_thai_hop_dong] ?? $hd->trang_thai_hop_dong }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center py-3">Không có lịch chụp trong 14 ngày tới.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100 mb-0 border-danger border-opacity-25 shadow-sm">
            <div class="card-header py-2 px-3 bg-danger bg-opacity-10 border-bottom border-danger border-opacity-20">
                <h6 class="mb-0 text-danger"><i class="ti tabler-alert-triangle me-1"></i>Cần xử lý</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-danger bg-opacity-10">
                        <tr>
                            <th class="small">Hợp đồng</th>
                            <th class="small">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($hopDongCanXuLy as $hd)
                        <tr>
                            <td>
                                <span class="fw-medium d-block">{{ $hd->ma_hop_dong ?: ('#'.$hd->id) }}</span>
                                <span class="text-muted">{{ $hd->ten_chu_re }} &amp; {{ $hd->ten_co_dau }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $badgeTrangThai($hd->trang_thai_hop_dong) }}">
                                    {{ $trangThaiCuoiTongQuanLabels[$hd->trang_thai_hop_dong] ?? $hd->trang_thai_hop_dong }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-muted text-center py-3">Không có hạng mục cần xử lý gấp.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-lg-6">
        <div class="card mb-0 border-info border-opacity-25 shadow-sm">
            <div class="card-header py-2 px-3 bg-info bg-opacity-10">
                <h6 class="mb-0 text-info"><i class="ti tabler-chart-pie me-1"></i>Hợp đồng của tôi theo trạng thái</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <tbody class="small">
                        @foreach ($trangThaiCuoiTongQuanLabels as $ma => $nhan)
                        <tr>
                            <td>{{ $nhan }}</td>
                            <td class="text-end fw-medium">{{ number_format((int) ($thongKeTrangThai[$ma] ?? 0)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if (count($linksNhanh) > 0)
    <div class="col-lg-6">
        <div class="card mb-0 border-0 shadow-sm">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0"><i class="ti tabler-bolt me-1"></i>Truy cập nhanh</h6>
            </div>
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($linksNhanh as $link)
                    <a href="{{ route($link['route']) }}" class="btn btn-sm btn-outline-primary">
                        <i class="{{ $link['icon'] }} me-1"></i>{{ $link['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.dashboard-hero {
    background: linear-gradient(125deg, rgba(0, 207, 232, 0.12) 0%, rgba(115, 103, 240, 0.08) 55%, rgba(40, 199, 111, 0.07) 100%);
    border-left: 4px solid #00cfe8;
}
.dashboard-card-hover {
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.dashboard-card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.35rem 1rem rgba(47, 43, 61, 0.08) !important;
}
</style>
@endsection
