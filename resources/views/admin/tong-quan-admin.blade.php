@extends('admin.layouts.app')

@section('title', 'Tổng quan | Wedding Studio')

@php
    $fmtTien = static fn (?float $v): string => $v !== null
        ? number_format((float) $v, 0, ',', '.') . ' đ'
        : '0 đ';

    $dashRankClass = static fn (int $i): string => match ($i) {
        0 => 'badge rounded-pill bg-warning text-dark fw-semibold px-2',
        1 => 'badge rounded-pill bg-secondary bg-opacity-75 text-white fw-medium px-2',
        2 => 'badge rounded-pill bg-danger bg-opacity-80 text-white fw-medium px-2',
        3 => 'badge rounded-pill bg-label-primary fw-medium px-2',
        default => 'badge rounded-pill bg-label-info fw-medium px-2',
    };
@endphp

@section('content')
<div class="row g-2 mb-2">
    <div class="col-12">
        <div class="card mb-0 border-0 shadow-sm dashboard-hero overflow-hidden">
            <div class="card-body py-3 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <span class="avatar avatar-md rounded bg-primary text-white flex-shrink-0 shadow-sm">
                        <i class="ti tabler-layout-dashboard fs-4"></i>
                    </span>
                    <div class="min-w-0">
                        <h5 class="mb-0 text-heading">Tổng quan quản trị</h5>
                        <small class="text-muted">Thống kê toàn hệ thống — Wedding Studio</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
    <ul class="mb-0 ps-3 small">
        @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
</div>
@endif

<div class="card mb-3 border-primary border-opacity-25 shadow-sm">
    <div class="card-header py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2 bg-primary bg-opacity-10 border-bottom border-primary border-opacity-25">
        <span class="small fw-semibold mb-0 text-primary"><i class="ti tabler-calendar-stats me-1"></i>Lọc kỳ thống kê doanh thu &amp; giá trị</span>
        <small class="text-muted">Mặc định: từ đầu năm đến hôm nay</small>
    </div>
    <div class="card-body py-3">
        <form action="{{ route('admin.index') }}" method="GET" class="row g-2 align-items-end admin-filter-row">
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label small mb-1" for="tu_ngay">Từ ngày</label>
                <input type="text"
                       name="tu_ngay"
                       id="tu_ngay"
                       value="{{ old('tu_ngay', request('tu_ngay', $tuNgay->toDateString())) }}"
                       class="form-control form-control-sm flatpickr-date-admin"
                       autocomplete="off"
                       placeholder="Chọn ngày">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label small mb-1" for="den_ngay">Đến ngày</label>
                <input type="text"
                       name="den_ngay"
                       id="den_ngay"
                       value="{{ old('den_ngay', request('den_ngay', $denNgay->toDateString())) }}"
                       class="form-control form-control-sm flatpickr-date-admin"
                       autocomplete="off"
                       placeholder="Chọn ngày">
            </div>
            <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ti tabler-filter me-1"></i> Áp dụng
                </button>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary btn-sm">Đặt lại</a>
            </div>
        </form>
        <p class="small text-muted mb-0 mt-2">
            <strong>HĐ cưới:</strong> đã thu theo ngày thanh toán.
            <strong>Thuê TP:</strong> giá trị sau giảm, HĐ không huỷ, theo ngày tạo trong kỳ.
        </p>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-primary border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-primary bg-opacity-10">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-start justify-content-between gap-1">
                    <div class="min-w-0">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">HĐ cưới</span>
                        <span class="fw-semibold fs-5 lh-sm d-block">{{ number_format($soHopDongCuoi) }}</span>
                    </div>
                    <span class="avatar avatar-xs rounded bg-label-primary flex-shrink-0"><i class="ti tabler-heart fs-6"></i></span>
                </div>
                <a href="{{ route('admin.khach-hang.danh-sach-hop-dong-cuoi') }}" class="small stretched-link">Chi tiết</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-info border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-info bg-opacity-10">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-start justify-content-between gap-1">
                    <div class="min-w-0">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">HĐ thuê TP</span>
                        <span class="fw-semibold fs-5 lh-sm d-block">{{ number_format($soHopDongThueTrangPhuc) }}</span>
                    </div>
                    <span class="avatar avatar-xs rounded bg-label-info flex-shrink-0"><i class="ti tabler-shirt fs-6"></i></span>
                </div>
                <a href="{{ route('admin.trang-phuc.hop-dong') }}" class="small stretched-link">Chi tiết</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-success border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-success bg-opacity-10">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-start justify-content-between gap-1">
                    <div class="min-w-0">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Khách hàng</span>
                        <span class="fw-semibold fs-5 lh-sm d-block">{{ number_format($soKhachHang) }}</span>
                    </div>
                    <span class="avatar avatar-xs rounded bg-label-success flex-shrink-0"><i class="ti tabler-users fs-6"></i></span>
                </div>
                <span class="small text-muted">Hồ sơ CSDL</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-warning border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-warning bg-opacity-10">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-start justify-content-between gap-1">
                    <div class="min-w-0">
                        <span class="d-block text-muted" style="font-size: 0.7rem;">NV · Trang phục</span>
                        <span class="fw-semibold fs-5 lh-sm d-block">{{ number_format($soNhanVien) }}<span class="text-muted fw-normal fs-6">/</span>{{ number_format($soTrangPhuc) }}</span>
                    </div>
                    <span class="avatar avatar-xs rounded bg-label-warning flex-shrink-0"><i class="ti tabler-building-store fs-6"></i></span>
                </div>
                <a href="{{ route('admin.nhan-su.danh-sach') }}" class="small me-1">Nhân sự</a>
                <a href="{{ route('admin.trang-phuc.san-pham') }}" class="small">SP</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-primary border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-primary bg-opacity-10">
            <div class="card-body py-2 px-3">
                <span class="d-block text-muted" style="font-size: 0.7rem;">Đã thu (cưới) trong kỳ</span>
                <span class="fw-semibold text-primary small lh-sm d-block text-truncate" title="{{ $fmtTien($doanhThuCuoiTrongKy) }}">{{ $fmtTien($doanhThuCuoiTrongKy) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border border-info border-opacity-25 mb-0 shadow-sm dashboard-card-hover bg-info bg-opacity-10">
            <div class="card-body py-2 px-3">
                <span class="d-block text-muted" style="font-size: 0.7rem;">Giá trị thuê (kỳ)</span>
                <span class="fw-semibold text-info small lh-sm d-block text-truncate" title="{{ $fmtTien($doanhThuThueTrongKy) }}">{{ $fmtTien($doanhThuThueTrongKy) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card mb-0 border-dashed border-primary border-opacity-25 dashboard-summary-strip shadow-sm">
            <div class="card-body py-2 px-3 d-flex flex-wrap align-items-baseline gap-3">
                <div>
                    <span class="text-muted small me-2"><i class="ti tabler-file-invoice text-primary me-1"></i>Tổng giá trị HĐ cưới (chưa huỷ)</span>
                    <span class="fw-semibold text-primary">{{ $fmtTien($tongGiaTriHopDongCuoi) }}</span>
                </div>
                <div class="vr d-none d-sm-block opacity-25"></div>
                <div>
                    <span class="text-muted small me-2"><i class="ti tabler-cash text-success me-1"></i>Đã thu lũy kế (cưới)</span>
                    <span class="fw-semibold text-success">{{ $fmtTien($tongDaThuCuoi) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-lg-6">
        <div class="card mb-0 h-100 border-primary border-opacity-20 shadow-sm">
            <div class="card-header py-2 px-3 bg-primary bg-opacity-10 border-bottom border-primary border-opacity-20">
                <h6 class="mb-0 text-primary"><i class="ti tabler-chart-bar me-1"></i>Doanh thu theo tháng</h6>
                <small class="text-muted">6 tháng gần nhất · đã thu cưới vs. giá trị HĐ thuê (theo tháng tạo)</small>
            </div>
            <div class="card-body pt-2 pb-3">
                <div id="chartDoanhThuTongQuan" class="chart-dashboard-half"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-2 border-warning border-opacity-30 shadow-sm">
            <div class="card-header py-2 px-3 bg-warning bg-opacity-10 border-bottom border-warning border-opacity-25">
                <h6 class="mb-0 text-warning"><i class="ti tabler-chart-area-line me-1"></i>Số hợp đồng mới theo tháng</h6>
                <small class="text-muted">Theo ngày tạo HĐ · 6 tháng gần nhất</small>
            </div>
            <div class="card-body pt-2 pb-2">
                <div id="chartSoHopDongTheoThang" class="chart-dashboard-half-sm"></div>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <div class="card mb-0 h-100 border-info border-opacity-25 shadow-sm">
                    <div class="card-header py-2 px-3 bg-info bg-opacity-10 border-bottom border-info border-opacity-20">
                        <h6 class="mb-0 small text-info"><i class="ti tabler-chart-pie me-1"></i>HĐ cưới · trạng thái</h6>
                    </div>
                    <div class="card-body pt-0 pb-2 d-flex justify-content-center">
                        <div id="chartPieCuoi" class="chart-dashboard-pie"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-0 h-100 border-secondary border-opacity-25 shadow-sm">
                    <div class="card-header py-2 px-3 bg-secondary bg-opacity-10 border-bottom border-secondary border-opacity-20">
                        <h6 class="mb-0 small text-secondary"><i class="ti tabler-chart-donut me-1"></i>HĐ thuê TP · trạng thái</h6>
                    </div>
                    <div class="card-body pt-0 pb-2 d-flex justify-content-center">
                        <div id="chartPieThue" class="chart-dashboard-pie"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-12">
        <div class="card mb-0 border-0 dashboard-rank-intro shadow-sm">
            <div class="card-body py-2 px-3">
                <span class="small mb-0"><span class="text-heading fw-semibold"><i class="ti tabler-trophy text-warning me-1"></i>Xếp hạng top 5</span><span class="text-muted"> · chỉ tính hợp đồng cưới <strong class="text-success">chưa huỷ</strong>. Dịch vụ: gộp dịch vụ lẻ và dịch vụ trong nhóm đang bật <em>có dùng</em>; mỗi HĐ chỉ tính một lần cho mỗi dịch vụ.</span></span>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100 mb-0 border-primary border-opacity-30 shadow-sm dashboard-card-hover">
            <div class="card-header py-2 px-3 bg-primary bg-opacity-10 border-bottom border-primary border-opacity-25">
                <h6 class="mb-0 text-primary"><i class="ti tabler-users-group me-1"></i>Top 5 nhân viên sale (nhiều HĐ nhất)</h6>
                <small class="text-muted">Bảng thành viên sale trên HĐ · đếm số hợp đồng khác nhau</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-primary bg-opacity-10">
                        <tr>
                            <th class="small" style="width: 2.5rem;">#</th>
                            <th class="small">Nhân viên</th>
                            <th class="text-end small">Số HĐ</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($topNhanVienSale as $i => $row)
                        <tr>
                            <td><span class="{{ $dashRankClass($i) }}">{{ $i + 1 }}</span></td>
                            <td>{{ $row['ten'] }}</td>
                            <td class="text-end"><span class="badge bg-label-primary">{{ number_format($row['so_luong']) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-3">Chưa có dữ liệu thành viên sale trên HĐ.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100 mb-0 border-success border-opacity-30 shadow-sm dashboard-card-hover">
            <div class="card-header py-2 px-3 bg-success bg-opacity-10 border-bottom border-success border-opacity-25">
                <h6 class="mb-0 text-success"><i class="ti tabler-confetti me-1"></i>Top 5 dịch vụ (nhiều HĐ dùng nhất)</h6>
                <small class="text-muted">Dịch vụ lẻ + trong nhóm (trạng thái có dùng)</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-success bg-opacity-10">
                        <tr>
                            <th class="small" style="width: 2.5rem;">#</th>
                            <th class="small">Dịch vụ</th>
                            <th class="text-end small">Số HĐ</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($topDichVu as $i => $row)
                        <tr>
                            <td><span class="{{ $dashRankClass($i) }}">{{ $i + 1 }}</span></td>
                            <td>{{ $row['ten'] }}</td>
                            <td class="text-end"><span class="badge bg-label-success">{{ number_format($row['so_luong']) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-3">Chưa có dịch vụ gắn HĐ.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100 mb-0 border-info border-opacity-35 shadow-sm dashboard-card-hover">
            <div class="card-header py-2 px-3 bg-info bg-opacity-10 border-bottom border-info border-opacity-25">
                <h6 class="mb-0 text-info"><i class="ti tabler-hanger me-1"></i>Top 5 trang phục (nhiều lần thuê trên HĐ cưới)</h6>
                <small class="text-muted">Số HĐ cưới có chọn trang phục</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-info bg-opacity-10">
                        <tr>
                            <th class="small" style="width: 2.5rem;">#</th>
                            <th class="small">Trang phục</th>
                            <th class="text-end small">Số HĐ</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($topTrangPhucThue as $i => $row)
                        <tr>
                            <td><span class="{{ $dashRankClass($i) }}">{{ $i + 1 }}</span></td>
                            <td>{{ $row['ten'] }}</td>
                            <td class="text-end"><span class="badge bg-label-info">{{ number_format($row['so_luong']) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-3">Chưa có trang phục trên HĐ cưới.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100 mb-0 border-warning border-opacity-35 shadow-sm dashboard-card-hover">
            <div class="card-header py-2 px-3 bg-warning bg-opacity-10 border-bottom border-warning border-opacity-25">
                <h6 class="mb-0 text-warning"><i class="ti tabler-sparkles me-1"></i>Top 5 concept (nhiều HĐ nhất)</h6>
                <small class="text-muted">Gán concept trên hợp đồng cưới</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-warning bg-opacity-10">
                        <tr>
                            <th class="small" style="width: 2.5rem;">#</th>
                            <th class="small">Concept</th>
                            <th class="text-end small">Số HĐ</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($topConcept as $i => $row)
                        <tr>
                            <td><span class="{{ $dashRankClass($i) }}">{{ $i + 1 }}</span></td>
                            <td>{{ $row['ten'] }}</td>
                            <td class="text-end"><span class="badge bg-label-warning text-dark">{{ number_format($row['so_luong']) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center py-3">Chưa có concept trên HĐ.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-lg-6">
        <div class="card h-100 mb-0 border-primary border-opacity-20 shadow-sm">
            <div class="card-header py-2 px-3 bg-primary bg-opacity-10 border-bottom border-primary border-opacity-15">
                <h6 class="mb-0 text-primary"><i class="ti tabler-heart me-1"></i>Hợp đồng cưới theo trạng thái</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-primary bg-opacity-10">
                        <tr>
                            <th class="small">Trạng thái</th>
                            <th class="text-end small">Số lượng</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach ($trangThaiCuoiTongQuanLabels as $ma => $nhan)
                        <tr>
                            <td><span class="badge bg-label-primary me-1"></span>{{ $nhan }}</td>
                            <td class="text-end"><span class="badge bg-label-secondary">{{ number_format((int) ($thongKeTrangThaiCuoi[$ma] ?? 0)) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100 mb-0 border-info border-opacity-25 shadow-sm">
            <div class="card-header py-2 px-3 bg-info bg-opacity-10 border-bottom border-info border-opacity-15">
                <h6 class="mb-0 text-info"><i class="ti tabler-shirt me-1"></i>Hợp đồng thuê trang phục theo trạng thái</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-info bg-opacity-10">
                        <tr>
                            <th class="small">Trạng thái</th>
                            <th class="text-end small">Số lượng</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach ($trangThaiThueLabels as $ma => $nhan)
                        <tr>
                            <td><span class="badge bg-label-info me-1"></span>{{ $nhan }}</td>
                            <td class="text-end"><span class="badge bg-label-secondary">{{ number_format((int) ($thongKeTrangThaiThue[$ma] ?? 0)) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 d-none">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="mb-0">Nhân viên theo phòng ban</h6>
                    <small class="text-muted">Một nhân viên có thể thuộc nhiều phòng ban · tổng theo cột có thể lớn hơn tổng số nhân viên</small>
                </div>
                <a href="{{ route('admin.nhan-su.danh-sach') }}" class="small">Danh sách nhân sự</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">Phòng ban</th>
                            <th class="text-end small">Số nhân viên</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($thongKeNhanVienTheoPhongBan as $pb)
                        <tr>
                            <td><span class="badge bg-label-secondary me-1"></span>{{ $pb->ten_phong_ban }}</td>
                            <td class="text-end fw-medium">{{ number_format((int) $pb->nhan_viens_count) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-muted text-center py-3">Chưa có phòng ban nào trong hệ thống.</td>
                        </tr>
                        @endforelse
                        @if ($soNhanVienChuaGanPhongBan > 0)
                        <tr class="table-light">
                            <td><span class="text-muted">Chưa gán phòng ban</span></td>
                            <td class="text-end fw-medium">{{ number_format((int) $soNhanVienChuaGanPhongBan) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.chart-dashboard-half { min-height: 280px; }
.chart-dashboard-half-sm { min-height: 220px; }
.chart-dashboard-pie { min-height: 220px; width: 100%; max-width: 280px; }

.dashboard-hero {
    background: linear-gradient(125deg, rgba(115, 103, 240, 0.12) 0%, rgba(0, 207, 232, 0.08) 45%, rgba(255, 159, 67, 0.07) 100%);
    border-left: 4px solid #7367f0;
}

.dashboard-summary-strip {
    background: linear-gradient(90deg, rgba(115, 103, 240, 0.06) 0%, rgba(40, 199, 111, 0.05) 100%);
}

.dashboard-rank-intro {
    background: linear-gradient(135deg, rgba(255, 159, 67, 0.12) 0%, rgba(115, 103, 240, 0.07) 55%, rgba(0, 207, 232, 0.06) 100%);
    border-left: 4px solid #ff9f43;
}

.dashboard-card-hover {
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.dashboard-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1.25rem rgba(47, 43, 61, 0.09) !important;
}

thead.table-primary.bg-opacity-10,
thead.table-success.bg-opacity-10,
thead.table-info.bg-opacity-10,
thead.table-warning.bg-opacity-10 {
    --bs-table-bg: transparent;
}
</style>
@endsection

@push('scripts')
<script>
(function() {
    if (typeof ApexCharts === 'undefined') return;

    var fmt = function(val) {
        if (val === null || val === undefined || isNaN(val)) return '0';
        return Math.round(Number(val)).toLocaleString('vi-VN') + ' đ';
    };

    var fmtInt = function(val) {
        if (val === null || val === undefined || isNaN(val)) return '0';
        return Math.round(Number(val)).toLocaleString('vi-VN');
    };

    var labels = @json($chartLabels);
    var dataCuoi = @json($chartDoanhThuCuoi);
    var dataThue = @json($chartDoanhThuThue);
    var dataSoCuoi = @json($chartSoHopDongCuoi);
    var dataSoThue = @json($chartSoHopDongThue);
    var pieCuoiLabels = @json($pieCuoiLabels);
    var pieCuoiSeries = @json($pieCuoiSeries);
    var pieThueLabels = @json($pieThueLabels);
    var pieThueSeries = @json($pieThueSeries);

    var commonGrid = { borderColor: '#e7eef7', strokeDashArray: 4 };
    var commonFont = { fontFamily: 'inherit' };

    var elBar = document.querySelector('#chartDoanhThuTongQuan');
    if (elBar) {
        new ApexCharts(elBar, {
            chart: { type: 'bar', height: 280, toolbar: { show: false }, ...commonFont },
            series: [
                { name: 'HĐ cưới (đã thu)', data: dataCuoi },
                { name: 'Thuê TP (sau giảm)', data: dataThue },
            ],
            plotOptions: {
                bar: { horizontal: false, columnWidth: '58%', borderRadius: 3, dataLabels: { position: 'top' } },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { categories: labels, labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { formatter: function(v) { return fmt(v); } } },
            fill: { opacity: 1 },
            colors: ['#7367f0', '#00cfe8'],
            legend: { position: 'top', horizontalAlign: 'start', fontSize: '12px', offsetY: -4 },
            tooltip: { y: { formatter: function(v) { return fmt(v); } } },
            grid: commonGrid,
        }).render();
    }

    var elLine = document.querySelector('#chartSoHopDongTheoThang');
    if (elLine) {
        new ApexCharts(elLine, {
            chart: { type: 'area', height: 220, toolbar: { show: false }, zoom: { enabled: false }, ...commonFont },
            series: [
                { name: 'HĐ cưới', data: dataSoCuoi },
                { name: 'HĐ thuê TP', data: dataSoThue },
            ],
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 0.35, opacityFrom: 0.35, opacityTo: 0.05 },
            },
            dataLabels: { enabled: false },
            xaxis: { categories: labels, labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { formatter: fmtInt }, min: 0, tickAmount: 4, forceNiceScale: true },
            colors: ['#7367f0', '#ff9f43'],
            legend: { position: 'top', horizontalAlign: 'start', fontSize: '12px', offsetY: -4 },
            tooltip: { y: { formatter: fmtInt } },
            grid: commonGrid,
        }).render();
    }

    function renderPie(elSelector, lbs, series, colors) {
        var el = document.querySelector(elSelector);
        if (!el || !series.length) return;
        var sum = series.reduce(function(a, b) { return a + b; }, 0);
        new ApexCharts(el, {
            chart: { type: 'donut', height: 220, toolbar: { show: false }, ...commonFont },
            series: series,
            labels: lbs,
            colors: colors,
            plotOptions: {
                pie: {
                    donut: {
                        size: '62%',
                        labels: {
                            show: sum > 0,
                            total: {
                                show: true,
                                label: 'Tổng',
                                formatter: function() { return fmtInt(sum); },
                            },
                        },
                    },
                },
            },
            legend: { position: 'bottom', fontSize: '11px', height: 56 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: fmtInt } },
        }).render();
    }

    renderPie('#chartPieCuoi', pieCuoiLabels, pieCuoiSeries, ['#00cfe8', '#28c76f', '#ea5455']);
    renderPie('#chartPieThue', pieThueLabels, pieThueSeries, ['#00cfe8', '#28c76f', '#ea5455']);
})();
</script>
@endpush
