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
                        <th colspan="2" class="text-center text-nowrap" style="min-width: 1px;">Hoa hồng</th>
                        <th rowspan="2" class="text-center text-nowrap small align-middle" style="min-width: 90px;">Tổng</th>
                    </tr>
                    <tr>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">Cơ bản</th>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">Tăng ca</th>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">Phụ cấp</th>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">HĐ cưới</th>
                        <th class="text-center text-nowrap small" style="min-width: 90px;">HĐ trang phục</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse(($nhanVien ?? []) as $u)
                        @php
                            $loaiNv = $u->nhanVien?->loai_nhan_vien ?? '';
                            $loaiNvLabel = filled($loaiNv)
                                ? (\App\Models\NhanVien::LOAI_NHAN_VIEN_OPTIONS[$loaiNv] ?? $loaiNv)
                                : '';
                        @endphp
                        <tr>
                            <td class="text-center align-middle tinh-luong-sticky tinh-luong-sticky-col-1">{{ $loop->iteration }}</td>
                            <td class="tinh-luong-sticky tinh-luong-sticky-col-2">
                                <div class="fw-medium d-inline-flex align-items-center flex-wrap gap-1">
                                    <span>{{ $u->name }}</span>
                                    @if($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_FULL_TIME)
                                        <i class="fa-solid fa-briefcase tinh-luong-loai-nv-icon text-primary"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{ $loaiNvLabel }}"
                                           aria-label="{{ $loaiNvLabel }}"></i>
                                    @elseif($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_PART_TIME)
                                        <i class="fa-solid fa-clock tinh-luong-loai-nv-icon text-warning"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{ $loaiNvLabel }}"
                                           aria-label="{{ $loaiNvLabel }}"></i>
                                    @else
                                        <i class="fa-solid fa-circle-exclamation tinh-luong-loai-nv-icon text-danger"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Chưa phân loại"
                                           aria-label="Chưa phân loại"></i>
                                    @endif
                                </div>
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
                                $luong = $bangLuongThang[$u->id] ?? [
                                    'luong_co_ban' => 0,
                                    'luong_tang_ca' => 0,
                                    'phu_cap' => 0,
                                    'hoa_hong_hop_dong_cuoi' => 0,
                                    'hoa_hong_hop_dong_trang_phuc' => 0,
                                    'tong_luong' => 0,
                                ];
                            @endphp
                            <td class="text-end align-middle">
                                {{ number_format($luong['luong_co_ban'], 0, ',', '.') }} đ
                            </td>
                            <td class="text-end align-middle">
                                {{ number_format($luong['luong_tang_ca'], 0, ',', '.') }} đ
                            </td>
                            <td class="text-end align-middle">
                                {{ number_format($luong['phu_cap'], 0, ',', '.') }} đ
                            </td>
                            <td class="text-end align-middle">
                                @if($luong['hoa_hong_hop_dong_cuoi'] > 0)
                                    <button type="button"
                                            class="btn btn-link btn-sm p-0 text-decoration-none tinh-luong-hoa-hong-btn"
                                            data-loai="cuoi"
                                            data-user-id="{{ $u->id }}">
                                        {{ number_format($luong['hoa_hong_hop_dong_cuoi'], 0, ',', '.') }} đ
                                    </button>
                                @else
                                    <span class="text-muted">0 đ</span>
                                @endif
                            </td>
                            <td class="text-end align-middle">
                                @if($luong['hoa_hong_hop_dong_trang_phuc'] > 0)
                                    <button type="button"
                                            class="btn btn-link btn-sm p-0 text-decoration-none tinh-luong-hoa-hong-btn"
                                            data-loai="trang_phuc"
                                            data-user-id="{{ $u->id }}">
                                        {{ number_format($luong['hoa_hong_hop_dong_trang_phuc'], 0, ',', '.') }} đ
                                    </button>
                                @else
                                    <span class="text-muted">0 đ</span>
                                @endif
                            </td>
                            <td class="text-end align-middle fw-semibold">
                                {{ number_format($luong['tong_luong'], 0, ',', '.') }} đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($ngayTrongThang ?? []) + 6 }}" class="text-center py-4 text-muted">
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

{{-- Modal chi tiết hoa hồng --}}
<div class="modal fade" id="modalChiTietHoaHong" tabindex="-1" aria-labelledby="modalChiTietHoaHongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalChiTietHoaHongLabel">Chi tiết hoa hồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light" id="modalChiTietHoaHongThead"></thead>
                        <tbody id="modalChiTietHoaHongTbody"></tbody>
                        <tfoot class="table-light" id="modalChiTietHoaHongTfoot"></tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
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
.tinh-luong-loai-nv-icon { font-size: 0.8rem; cursor: help; }
.tinh-luong-hoa-hong-btn { font-weight: 500; color: var(--bs-primary); white-space: nowrap; }
.tinh-luong-hoa-hong-btn:hover { text-decoration: underline !important; color: var(--bs-primary); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chiTietHoaHong = @json($chiTietHoaHong ?? []);

    function formatMoney(value) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(value)) + ' đ';
    }

    function formatPercent(value) {
        return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value) + '%';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function renderChiTietHoaHong(loai, userId) {
        var data = chiTietHoaHong[userId];
        if (!data) {
            return;
        }

        var modalEl = document.getElementById('modalChiTietHoaHong');
        var titleEl = document.getElementById('modalChiTietHoaHongLabel');
        var theadEl = document.getElementById('modalChiTietHoaHongThead');
        var tbodyEl = document.getElementById('modalChiTietHoaHongTbody');
        var tfootEl = document.getElementById('modalChiTietHoaHongTfoot');
        var detail = loai === 'cuoi' ? data.hoa_hong_cuoi : data.hoa_hong_trang_phuc;
        var danhSach = (detail && detail.danh_sach) ? detail.danh_sach : [];
        var tong = (detail && detail.tong) ? detail.tong : 0;

        titleEl.textContent = loai === 'cuoi'
            ? 'Chi tiết hoa hồng HĐ cưới — ' + (data.ten_nhan_vien || '')
            : 'Chi tiết hoa hồng HĐ trang phục — ' + (data.ten_nhan_vien || '');

        if (loai === 'cuoi') {
            theadEl.innerHTML = '<tr>'
                + '<th class="text-center" style="width:48px">STT</th>'
                + '<th>Mã HĐ</th>'
                + '<th>Khách hàng</th>'
                + '<th class="text-center">Ngày ký</th>'
                + '<th class="text-end">Doanh thu</th>'
                + '<th class="text-center">Số người</th>'
                + '<th class="text-end">Tỷ lệ HH</th>'
                + '<th class="text-end">Nhận được</th>'
                + '</tr>';
        } else {
            theadEl.innerHTML = '<tr>'
                + '<th class="text-center" style="width:48px">STT</th>'
                + '<th>Khách hàng</th>'
                + '<th class="text-center">SĐT</th>'
                + '<th class="text-center">Ngày trả</th>'
                + '<th class="text-end">Doanh thu</th>'
                + '<th class="text-end">Tỷ lệ HH</th>'
                + '<th class="text-end">Nhận được</th>'
                + '</tr>';
        }

        if (danhSach.length === 0) {
            var colSpan = loai === 'cuoi' ? 8 : 7;
            tbodyEl.innerHTML = '<tr><td colspan="' + colSpan + '" class="text-center text-muted py-4">Không có hợp đồng trong kỳ.</td></tr>';
        } else if (loai === 'cuoi') {
            tbodyEl.innerHTML = danhSach.map(function (item, index) {
                return '<tr>'
                    + '<td class="text-center">' + (index + 1) + '</td>'
                    + '<td>' + escapeHtml(item.ma_hop_dong || '—') + '</td>'
                    + '<td>' + escapeHtml(item.ten_hop_dong || '—') + '</td>'
                    + '<td class="text-center text-nowrap">' + escapeHtml(item.ngay || '—') + '</td>'
                    + '<td class="text-end text-nowrap">' + formatMoney(item.doanh_thu || 0) + '</td>'
                    + '<td class="text-center">' + escapeHtml(item.so_nguoi_tham_gia || 0) + '</td>'
                    + '<td class="text-end text-nowrap">' + formatPercent(item.ty_le_hoa_hong || 0) + '</td>'
                    + '<td class="text-end text-nowrap fw-medium">' + formatMoney(item.so_tien_nhan || 0) + '</td>'
                    + '</tr>';
            }).join('');
        } else {
            tbodyEl.innerHTML = danhSach.map(function (item, index) {
                return '<tr>'
                    + '<td class="text-center">' + (index + 1) + '</td>'
                    + '<td>' + escapeHtml(item.ten_khach_hang || '—') + '</td>'
                    + '<td class="text-center text-nowrap">' + escapeHtml(item.sdt_khach_hang || '—') + '</td>'
                    + '<td class="text-center text-nowrap">' + escapeHtml(item.ngay || '—') + '</td>'
                    + '<td class="text-end text-nowrap">' + formatMoney(item.doanh_thu || 0) + '</td>'
                    + '<td class="text-end text-nowrap">' + formatPercent(item.ty_le_hoa_hong || 0) + '</td>'
                    + '<td class="text-end text-nowrap fw-medium">' + formatMoney(item.so_tien_nhan || 0) + '</td>'
                    + '</tr>';
            }).join('');
        }

        var footColSpan = loai === 'cuoi' ? 7 : 6;
        tfootEl.innerHTML = '<tr>'
            + '<th colspan="' + footColSpan + '" class="text-end">Tổng hoa hồng</th>'
            + '<th class="text-end text-nowrap">' + formatMoney(tong) + '</th>'
            + '</tr>';

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    document.querySelectorAll('.tinh-luong-hoa-hong-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            renderChiTietHoaHong(btn.getAttribute('data-loai'), btn.getAttribute('data-user-id'));
        });
    });

    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }
    [].slice.call(document.querySelectorAll('.tinh-luong-table [data-bs-toggle="tooltip"]')).forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush
@endsection
