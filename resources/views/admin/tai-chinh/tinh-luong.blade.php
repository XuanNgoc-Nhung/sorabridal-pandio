@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-column gap-3">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
    @endif

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
                <div class="col-6 col-md-6 col-lg-4 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.tai-chinh.tinh-luong') }}" class="btn btn-outline-secondary">Tháng này</a>
                    @if($trongKhungChotLuong ?? false)
                        <a href="{{ route('admin.tai-chinh.tinh-luong', ['month' => $thangChotDuoc, 'year' => $namChotDuoc]) }}"
                           class="btn btn-outline-primary">
                            Tháng cần chốt
                        </a>
                    @endif
                </div>
                <div class="col-12 col-lg-auto ms-lg-auto">
                    <div class="text-muted">
                        Khoảng: <strong>{{ ($start ?? now()->startOfMonth())->format('d/m/Y') }}</strong>
                        → <strong>{{ ($end ?? now()->endOfMonth())->format('d/m/Y') }}</strong>
                    </div>
                    @if(!($daChotLuong ?? false))
                        <div class="small text-muted mt-1">
                            Chốt lương tháng {{ $thangChotDuoc }}/{{ $namChotDuoc }}
                            từ ngày {{ $khungChotLuongLabel }} hàng tháng.
                        </div>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h5 class="mb-0">Tính lương theo tháng</h5>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($daChotLuong ?? false)
                    <span class="badge bg-label-success">
                        <i class="fa-solid fa-lock me-1"></i>
                        Đã chốt lương
                        @if($chotLuong?->ngay_chot)
                            — {{ $chotLuong->ngay_chot->format('d/m/Y H:i') }}
                        @endif
                    </span>
                    <span class="small text-muted">Dữ liệu theo thời điểm chốt lương.</span>
                    @if(($isAdmin ?? false) && $chotLuong)
                        <form id="form-huy-chot-luong"
                              method="POST"
                              action="{{ route('admin.tai-chinh.destroy-chot-luong', $chotLuong) }}"
                              class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        <button type="button"
                                class="btn btn-outline-danger btn-sm btn-tinh-luong-confirm"
                                data-form-id="form-huy-chot-luong"
                                data-confirm-title="Hủy chốt lương"
                                data-confirm-message="Hủy chốt lương tháng {{ $month }}/{{ $year }}? Dữ liệu lương sẽ được tính lại theo thông tin hiện tại."
                                data-confirm-btn-class="btn-danger"
                                data-confirm-btn-text="Hủy chốt lương"
                                data-confirm-btn-icon="fa-lock-open">
                            <i class="fa-solid fa-lock-open me-1"></i> Hủy chốt lương
                        </button>
                    @endif
                @else
                    @if($coTheChotLuong ?? false)
                        <form id="form-chot-luong"
                              method="POST"
                              action="{{ route('admin.tai-chinh.store-tinh-luong') }}"
                              class="d-none">
                            @csrf
                            <input type="hidden" name="thang" value="{{ $month }}">
                            <input type="hidden" name="nam" value="{{ $year }}">
                        </form>
                        <button type="button"
                                class="btn btn-success btn-sm btn-tinh-luong-confirm"
                                data-form-id="form-chot-luong"
                                data-confirm-title="Chốt lương"
                                data-confirm-message="Chốt lương tháng {{ $month }}/{{ $year }}? Sau khi chốt, dữ liệu lương sẽ được lưu lại và không thay đổi khi cập nhật thông tin nhân viên."
                                data-confirm-btn-class="btn-success"
                                data-confirm-btn-text="Chốt lương"
                                data-confirm-btn-icon="fa-lock">
                            <i class="fa-solid fa-lock me-1"></i> Chốt lương
                        </button>
                    @else
                        <span class="small text-muted">
                            @if($trongKhungChotLuong ?? false)
                                Chỉ được chốt lương tháng {{ $thangChotDuoc }}/{{ $namChotDuoc }}.
                            @else
                                Chỉ được chốt lương từ ngày {{ $khungChotLuongLabel }} hàng tháng.
                            @endif
                        </span>
                    @endif
                @endif
            </div>
        </div>
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
                        <th rowspan="2" class="text-center text-nowrap small align-middle" style="min-width: 110px;">Thao tác</th>
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
                                    if ($daChotLuong ?? false) {
                                        $luongNgay = $bangChamCongLuong[$u->id][$dateKey] ?? null;
                                    } else {
                                        $diemDanh = ($bangChamCong[$dateKey][$u->id] ?? null)?->diemDanh;
                                        $luongNgay = $diemDanh ? [
                                            'luong_co_ban' => (float) ($diemDanh->luong_co_ban ?? 0),
                                            'luong_tang_ca' => (float) ($diemDanh->luong_tang_ca ?? 0),
                                        ] : null;
                                    }
                                    $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true); // 6=Thứ 7, 7=Chủ nhật
                                @endphp
                                <td class="text-center align-middle tinh-luong-ngay-col {{ $isWeekend ? 'tinh-luong-weekend' : '' }}">
                                    @if($luongNgay)
                                        <div class="small">
                                            <div class="luong-co-ban" title="Lương cơ bản">{{ number_format((float)($luongNgay['luong_co_ban'] ?? 0), 0, ',', '.') }} đ</div>
                                            <div class="luong-tang-ca" title="Lương tăng ca">{{ number_format((float)($luongNgay['luong_tang_ca'] ?? 0), 0, ',', '.') }} đ</div>
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
                                $daChuyenLuong = in_array($u->id, $daChuyenUserIds ?? [], true);
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
                            <td class="text-end align-middle fw-semibold {{ $daChuyenLuong ? 'text-success tinh-luong-da-chuyen' : '' }}">
                                {{ number_format($luong['tong_luong'], 0, ',', '.') }} đ
                            </td>
                            <td class="text-center align-middle">
                                @if($daChotLuong ?? false)
                                    <button type="button"
                                            class="btn btn-sm text-nowrap tinh-luong-chuyen-btn {{ $daChuyenLuong ? 'btn-success' : 'btn-outline-primary' }}"
                                            data-user-id="{{ $u->id }}"
                                            title="{{ $daChuyenLuong ? 'Đã chuyển lương' : 'Chuyển lương' }}">
                                        <i class="fa-solid {{ $daChuyenLuong ? 'fa-check' : 'fa-money-bill-transfer' }} me-1"></i>
                                    </button>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($ngayTrongThang ?? []) + 7 }}" class="text-center py-4 text-muted">
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

{{-- Modal chuyển lương --}}
<div class="modal fade" id="modalChuyenLuong" tabindex="-1" aria-labelledby="modalChuyenLuongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalChuyenLuongLabel">Chuyển lương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                    <span class="badge bg-label-primary" id="modalChuyenLuongLoaiNv"></span>
                    <span class="text-muted small" id="modalChuyenLuongKy"></span>
                </div>
                <div class="row g-4">
                    <div class="col-lg-9">
                        <div class="row g-3 tinh-luong-chuyen-fields">
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clTongGioLam">Tổng giờ làm</label>
                                <input type="text" class="form-control" id="clTongGioLam" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clTongGioTangCa">Tổng giờ tăng ca</label>
                                <input type="text" class="form-control" id="clTongGioTangCa" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clLuongCoBan">Lương cơ bản</label>
                                <input type="text" class="form-control text-end" id="clLuongCoBan" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clLuongTangCa">Lương tăng ca</label>
                                <input type="text" class="form-control text-end" id="clLuongTangCa" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clTienPhatDiMuon">Tiền phạt đi muộn</label>
                                <input type="text" class="form-control text-end text-danger" id="clTienPhatDiMuon" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clTienPhatVeSom">Tiền phạt về sớm</label>
                                <input type="text" class="form-control text-end text-danger" id="clTienPhatVeSom" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clPhuCap">Phụ cấp</label>
                                <input type="text" class="form-control text-end" id="clPhuCap" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clHoaHongCuoi">Hoa hồng HĐ cưới</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-end" id="clHoaHongCuoi" readonly>
                                    <button type="button"
                                            class="btn btn-outline-primary tinh-luong-hoa-hong-trong-chuyen d-none"
                                            id="clHoaHongCuoiBtn"
                                            data-loai="cuoi"
                                            data-bs-toggle="tooltip"
                                            title="Chi tiết"
                                            aria-label="Chi tiết hoa hồng HĐ cưới">
                                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clHoaHongTrangPhuc">Hoa hồng HĐ trang phục</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-end" id="clHoaHongTrangPhuc" readonly>
                                    <button type="button"
                                            class="btn btn-outline-primary tinh-luong-hoa-hong-trong-chuyen d-none"
                                            id="clHoaHongTrangPhucBtn"
                                            data-loai="trang_phuc"
                                            data-bs-toggle="tooltip"
                                            title="Chi tiết"
                                            aria-label="Chi tiết hoa hồng HĐ trang phục">
                                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clNganHang">Ngân hàng</label>
                                <input type="text" class="form-control" id="clNganHang" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clSoTaiKhoan">Số tài khoản</label>
                                <input type="text" class="form-control" id="clSoTaiKhoan" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clChuTaiKhoan">Chủ tài khoản</label>
                                <input type="text" class="form-control" id="clChuTaiKhoan" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3 d-none" id="clTongLuongGopWrap">
                                <label class="form-label" for="clTongLuongGop">Tổng lương (gộp)</label>
                                <input type="text" class="form-control text-end" id="clTongLuongGop" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3 d-none" id="clTongPhatWrap">
                                <label class="form-label" for="clTongPhat">Trừ tiền phạt</label>
                                <input type="text" class="form-control text-end text-danger" id="clTongPhat" readonly>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label" for="clTongLuong">Tổng lương thực nhận</label>
                                <input type="text" class="form-control text-end fw-semibold text-success tinh-luong-chuyen-tong" id="clTongLuong" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="tinh-luong-qr-wrap text-center h-100 d-flex flex-column justify-content-center" id="modalChuyenLuongQrWrap">
                            <img src="" alt="Mã QR chuyển lương" class="tinh-luong-qr-img" id="modalChuyenLuongQrImg">
                        </div>
                        <div class="alert alert-warning small mb-0 d-none h-100 d-flex align-items-center justify-content-center text-center" id="modalChuyenLuongQrMissing">
                            Chưa có đủ thông tin ngân hàng (tên ngân hàng, số tài khoản) để tạo mã QR.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if(($daChotLuong ?? false) && $chotLuong)
                    <form id="formDaChuyenLuong"
                          method="POST"
                          action="{{ route('admin.tai-chinh.danh-dau-da-chuyen-luong', $chotLuong) }}"
                          class="d-none">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" id="clDaChuyenUserId" value="">
                    </form>
                    <button type="button" class="btn btn-success" id="btnDaChuyenLuong">
                        <i class="fa-solid fa-check me-1"></i> Đã chuyển
                    </button>
                @endif
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận thao tác tính lương --}}
<div class="modal fade" id="modalXacNhanTinhLuong" tabindex="-1" aria-labelledby="modalXacNhanTinhLuongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-tinh-luong">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanTinhLuongLabel">Xác nhận</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="modalXacNhanTinhLuongBody">
                Bạn có chắc muốn thực hiện thao tác này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnXacNhanTinhLuong">
                    <i class="fa-solid fa-check me-1"></i> Xác nhận
                </button>
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
.tinh-luong-chuyen-fields .form-control[readonly] {
    background-color: var(--bs-body-bg);
    cursor: default;
}
.tinh-luong-chuyen-fields .form-label {
    font-size: 0.8125rem;
    margin-bottom: 0.35rem;
}
.tinh-luong-chuyen-tong { font-size: 1.05rem; }
.tinh-luong-da-chuyen { color: #198754 !important; }
.tinh-luong-qr-wrap {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.5rem;
    padding: 1rem;
    background: #fff;
}
.tinh-luong-qr-img { width: 100%; height: auto; display: block; }
.tinh-luong-hoa-hong-trong-chuyen { line-height: 1; }
[data-bs-theme='dark'] .tinh-luong-qr-wrap { background: #2f3349; }
#modalXacNhanTinhLuong .modal-confirm-tinh-luong {
    max-width: 90vw;
    width: 400px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chiTietHoaHong = @json($chiTietHoaHong ?? []);
    var chiTietChuyenLuong = @json($chiTietChuyenLuong ?? []);
    var daChuyenUserIds = @json($daChuyenUserIds ?? []);
    var chuyenLuongUserId = null;
    var modalXacNhanTinhLuong = document.getElementById('modalXacNhanTinhLuong');
    var btnXacNhanTinhLuong = document.getElementById('btnXacNhanTinhLuong');
    var modalXacNhanTinhLuongBody = document.getElementById('modalXacNhanTinhLuongBody');
    var modalXacNhanTinhLuongLabel = document.getElementById('modalXacNhanTinhLuongLabel');
    var formIdCanSubmit = null;
    var moLaiModalChuyenLuongKhiHuy = false;
    var xacNhanTinhLuongDaDongY = false;

    function donDepModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function (el) {
            el.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function moXacNhanTinhLuong(opts) {
        if (!modalXacNhanTinhLuong || !btnXacNhanTinhLuong) {
            return;
        }

        formIdCanSubmit = opts.formId || null;
        moLaiModalChuyenLuongKhiHuy = !!opts.moLaiModalChuyenLuongKhiHuy;
        xacNhanTinhLuongDaDongY = false;

        if (modalXacNhanTinhLuongLabel) {
            modalXacNhanTinhLuongLabel.textContent = opts.title || 'Xác nhận';
        }
        if (modalXacNhanTinhLuongBody) {
            modalXacNhanTinhLuongBody.textContent = opts.message || 'Bạn có chắc muốn thực hiện thao tác này?';
        }

        var btnClass = opts.btnClass || 'btn-primary';
        var btnIcon = opts.btnIcon || 'fa-check';
        var btnText = opts.btnText || 'Xác nhận';
        btnXacNhanTinhLuong.className = 'btn ' + btnClass;
        btnXacNhanTinhLuong.innerHTML = '<i class="fa-solid ' + btnIcon + ' me-1"></i> ' + btnText;

        if (opts.anModalChuyenLuongTruoc) {
            var chuyenModalEl = document.getElementById('modalChuyenLuong');
            if (chuyenModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var chuyenInst = bootstrap.Modal.getInstance(chuyenModalEl);
                if (chuyenInst) {
                    chuyenInst.hide();
                }
            }
        }

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalXacNhanTinhLuong).show();
        }
    }

    function formatMoney(value) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(value)) + ' đ';
    }

    function formatHours(value) {
        return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value) + ' giờ';
    }

    function formatPercent(value) {
        return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value) + '%';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function buildVietQrUrl(data) {
        var params = new URLSearchParams();
        params.set('acc', data.so_tai_khoan || '');
        params.set('bank', data.ngan_hang || '');
        params.set('amount', String(Math.round(data.tong_luong_thuc_nhan || 0)));
        params.set('des', 'Luong T' + data.thang + '/' + data.nam);
        params.set('fullacc', 'true');
        params.set('holder', (data.chu_tai_khoan || '').toUpperCase());
        params.set('showinfo', 'true');
        return 'https://vietqr.app/img?' + params.toString();
    }

    function setInputValue(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.value = value == null ? '' : String(value);
        }
    }

    function toggleEl(id, show) {
        var el = document.getElementById(id);
        if (el) {
            el.classList.toggle('d-none', !show);
        }
    }

    function isDaChuyenLuong(userId) {
        return daChuyenUserIds.indexOf(parseInt(userId, 10)) !== -1;
    }

    function capNhatNutDaChuyen(userId) {
        var btn = document.getElementById('btnDaChuyenLuong');
        var input = document.getElementById('clDaChuyenUserId');
        if (!btn || !input) {
            return;
        }

        var daChuyen = isDaChuyenLuong(userId);
        input.value = userId;
        btn.disabled = daChuyen;
        btn.classList.toggle('btn-success', !daChuyen);
        btn.classList.toggle('btn-outline-success', daChuyen);
        btn.innerHTML = daChuyen
            ? '<i class="fa-solid fa-check me-1"></i> Đã chuyển'
            : '<i class="fa-solid fa-check me-1"></i> Đã chuyển';
    }

    function renderChuyenLuong(userId) {
        var data = chiTietChuyenLuong[userId];
        if (!data) {
            return;
        }

        chuyenLuongUserId = userId;
        var modalEl = document.getElementById('modalChuyenLuong');
        var titleEl = document.getElementById('modalChuyenLuongLabel');
        var loaiEl = document.getElementById('modalChuyenLuongLoaiNv');
        var kyEl = document.getElementById('modalChuyenLuongKy');
        var qrWrapEl = document.getElementById('modalChuyenLuongQrWrap');
        var qrImgEl = document.getElementById('modalChuyenLuongQrImg');
        var qrMissingEl = document.getElementById('modalChuyenLuongQrMissing');
        var hoaHongCuoiBtn = document.getElementById('clHoaHongCuoiBtn');
        var hoaHongTrangPhucBtn = document.getElementById('clHoaHongTrangPhucBtn');
        var coPhat = (data.tong_phat || 0) > 0;

        titleEl.textContent = 'Chuyển lương — ' + (data.ten_nhan_vien || '');
        loaiEl.textContent = data.loai_nhan_vien_label || '—';
        kyEl.textContent = 'Tháng ' + data.thang + '/' + data.nam;

        setInputValue('clTongGioLam', formatHours(data.tong_gio_lam || 0));
        setInputValue('clTongGioTangCa', formatHours(data.tong_gio_tang_ca || 0));
        setInputValue('clLuongCoBan', formatMoney(data.luong_co_ban || 0));
        setInputValue('clLuongTangCa', formatMoney(data.luong_tang_ca || 0));
        setInputValue('clTienPhatDiMuon', formatMoney(data.tien_phat_di_muon || 0));
        setInputValue('clTienPhatVeSom', formatMoney(data.tien_phat_ve_som || 0));
        setInputValue('clPhuCap', formatMoney(data.phu_cap || 0));
        setInputValue('clHoaHongCuoi', formatMoney(data.hoa_hong_hop_dong_cuoi || 0));
        setInputValue('clHoaHongTrangPhuc', formatMoney(data.hoa_hong_hop_dong_trang_phuc || 0));
        setInputValue('clNganHang', data.ngan_hang || '—');
        setInputValue('clSoTaiKhoan', data.so_tai_khoan || '—');
        setInputValue('clChuTaiKhoan', data.chu_tai_khoan || '—');
        setInputValue('clTongLuongGop', formatMoney(data.tong_luong || 0));
        setInputValue('clTongPhat', '− ' + formatMoney(data.tong_phat || 0));
        setInputValue('clTongLuong', formatMoney(coPhat ? (data.tong_luong_thuc_nhan || 0) : (data.tong_luong || 0)));

        toggleEl('clTongLuongGopWrap', coPhat);
        toggleEl('clTongPhatWrap', coPhat);

        if (hoaHongCuoiBtn) {
            hoaHongCuoiBtn.setAttribute('data-user-id', userId);
            hoaHongCuoiBtn.classList.toggle('d-none', !(data.hoa_hong_hop_dong_cuoi > 0));
        }
        if (hoaHongTrangPhucBtn) {
            hoaHongTrangPhucBtn.setAttribute('data-user-id', userId);
            hoaHongTrangPhucBtn.classList.toggle('d-none', !(data.hoa_hong_hop_dong_trang_phuc > 0));
        }

        var coQr = (data.so_tai_khoan || '').trim() !== '' && (data.ngan_hang || '').trim() !== '';
        if (coQr && (data.tong_luong_thuc_nhan || 0) > 0) {
            qrWrapEl.classList.remove('d-none');
            qrMissingEl.classList.add('d-none');
            qrImgEl.src = buildVietQrUrl(data);
            qrImgEl.alt = 'QR chuyển lương ' + (data.ten_nhan_vien || '');
        } else {
            qrWrapEl.classList.add('d-none');
            qrMissingEl.classList.remove('d-none');
            qrImgEl.removeAttribute('src');
        }

        capNhatNutDaChuyen(userId);

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
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
            var chuyenModalEl = document.getElementById('modalChuyenLuong');
            if (chuyenModalEl) {
                var chuyenModal = bootstrap.Modal.getInstance(chuyenModalEl);
                if (chuyenModal) {
                    chuyenModal.hide();
                }
            }
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }

    document.querySelectorAll('.tinh-luong-chuyen-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            renderChuyenLuong(btn.getAttribute('data-user-id'));
        });
    });

    var btnDaChuyenLuong = document.getElementById('btnDaChuyenLuong');
    if (btnDaChuyenLuong) {
        btnDaChuyenLuong.addEventListener('click', function () {
            if (chuyenLuongUserId === null || isDaChuyenLuong(chuyenLuongUserId)) {
                return;
            }
            moXacNhanTinhLuong({
                formId: 'formDaChuyenLuong',
                title: 'Xác nhận chuyển lương',
                message: 'Đánh dấu đã chuyển lương cho nhân viên này?',
                btnClass: 'btn-success',
                btnText: 'Đã chuyển',
                btnIcon: 'fa-check',
                anModalChuyenLuongTruoc: true,
                moLaiModalChuyenLuongKhiHuy: true,
            });
        });
    }

    if (modalXacNhanTinhLuong) {
        modalXacNhanTinhLuong.addEventListener('hidden.bs.modal', function () {
            donDepModalBackdrop();
            if (moLaiModalChuyenLuongKhiHuy && !xacNhanTinhLuongDaDongY && chuyenLuongUserId !== null) {
                var chuyenModalEl = document.getElementById('modalChuyenLuong');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal && chuyenModalEl) {
                    bootstrap.Modal.getOrCreateInstance(chuyenModalEl).show();
                }
            }
            moLaiModalChuyenLuongKhiHuy = false;
            xacNhanTinhLuongDaDongY = false;
            formIdCanSubmit = null;
        });
    }

    if (btnXacNhanTinhLuong) {
        btnXacNhanTinhLuong.addEventListener('click', function () {
            xacNhanTinhLuongDaDongY = true;
            if (formIdCanSubmit) {
                var form = document.getElementById(formIdCanSubmit);
                if (form) {
                    form.submit();
                }
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal && modalXacNhanTinhLuong) {
                var inst = bootstrap.Modal.getInstance(modalXacNhanTinhLuong);
                if (inst) {
                    inst.hide();
                }
            }
        });
    }

    document.querySelectorAll('.btn-tinh-luong-confirm').forEach(function (btn) {
        btn.addEventListener('click', function () {
            moXacNhanTinhLuong({
                formId: btn.getAttribute('data-form-id'),
                title: btn.getAttribute('data-confirm-title'),
                message: btn.getAttribute('data-confirm-message'),
                btnClass: btn.getAttribute('data-confirm-btn-class') || 'btn-primary',
                btnText: btn.getAttribute('data-confirm-btn-text') || 'Xác nhận',
                btnIcon: btn.getAttribute('data-confirm-btn-icon') || 'fa-check',
            });
        });
    });

    document.querySelectorAll('.tinh-luong-hoa-hong-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            renderChiTietHoaHong(btn.getAttribute('data-loai'), btn.getAttribute('data-user-id'));
        });
    });

    document.getElementById('modalChiTietHoaHong').addEventListener('hidden.bs.modal', function () {
        if (chuyenLuongUserId === null) {
            return;
        }
        var chuyenModalEl = document.getElementById('modalChuyenLuong');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && chuyenModalEl) {
            bootstrap.Modal.getOrCreateInstance(chuyenModalEl).show();
        }
    });

    document.getElementById('modalChuyenLuong').addEventListener('click', function (event) {
        var btn = event.target.closest('.tinh-luong-hoa-hong-trong-chuyen');
        if (!btn) {
            return;
        }
        renderChiTietHoaHong(btn.getAttribute('data-loai'), btn.getAttribute('data-user-id'));
    });

    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }
    [].slice.call(document.querySelectorAll('.tinh-luong-table [data-bs-toggle="tooltip"], #modalChuyenLuong [data-bs-toggle="tooltip"]')).forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush
@endsection
