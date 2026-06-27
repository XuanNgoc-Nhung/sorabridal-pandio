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
    $thangLoc = $thang ?? sprintf('%04d-%02d', (int) ($year ?? now()->year), (int) ($month ?? now()->month));
@endphp
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('admin.diem-danh.cham-cong') }}">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="cham_cong_thang">Tháng / năm</label>
                    <input type="text"
                           class="form-control"
                           id="cham_cong_thang"
                           placeholder="Chọn tháng/năm"
                           autocomplete="off"
                           value="">
                    <input type="hidden" name="thang" id="cham_cong_thang_value" value="{{ $thangLoc }}">
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
                    {{-- <a href="{{ route('admin.diem-danh.cham-cong', ['thang' => $thangLoc]) }}" class="btn btn-outline-secondary">Bỏ lọc</a> --}}
                    @endif
                    <a href="{{ route('admin.diem-danh.cham-cong') }}" class="btn btn-outline-secondary">Tháng này</a>
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
        <div class="table-responsive text-nowrap table-wrapper-bordered cham-cong-table-wrap">
            <table class="table table-bordered table-hover mb-0 cham-cong-table">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="text-center align-middle cham-cong-sticky cham-cong-sticky-col-1" style="min-width: 48px;">STT</th>
                        <th rowspan="2" class="align-middle cham-cong-sticky cham-cong-sticky-col-2" style="min-width: 220px;">Nhân viên</th>
                        @php
                            $thuLabel = ['1' => 'T2', '2' => 'T3', '3' => 'T4', '4' => 'T5', '5' => 'T6', '6' => 'T7', '7' => 'CN'];
                        @endphp
                        @foreach(($ngayTrongThang ?? []) as $day)
                            @php
                                $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true);
                            @endphp
                            <th rowspan="2" class="text-center align-middle small text-nowrap cham-cong-ngay-col {{ $isWeekend ? 'cham-cong-weekend' : '' }}" style="min-width: 72px;">
                                {{ $thuLabel[$day->dayOfWeekIso] ?? $day->isoFormat('dd') }} {{ $day->format('j/n') }}
                            </th>
                        @endforeach
                        <th colspan="2" class="text-center text-nowrap cham-cong-tong-col">Tổng công</th>
                    </tr>
                    <tr>
                        <th class="text-center text-nowrap small cham-cong-tong-col" style="min-width: 72px;">Cơ bản</th>
                        <th class="text-center text-nowrap small cham-cong-tong-col" style="min-width: 72px;">Tăng ca</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse(($nhanVien ?? []) as $u)
                        @php
                            $coChamCongTrongThang = collect($bangChamCong ?? [])->contains(fn ($byUser) => isset($byUser[$u->id]));
                            $nvRecord = $u->nhanVien;
                            $loaiNv = $nvRecord?->loai_nhan_vien ?? '';
                            $loaiNvLabel = filled($loaiNv)
                                ? (\App\Models\NhanVien::LOAI_NHAN_VIEN_OPTIONS[$loaiNv] ?? $loaiNv)
                                : '';
                            $coLoaiNvHopLe = \App\Support\TinhLuongDiemDanh::hopLeLoaiNhanVien($nvRecord);
                            $tongCoBan = 0;
                            $tongTangCa = 0.0;
                            foreach (($ngayTrongThang ?? []) as $dayTong) {
                                $dateKeyTong = $dayTong->toDateString();
                                $recordTong = $bangChamCong[$dateKeyTong][$u->id] ?? null;
                                $diemDanhTong = $recordTong?->diemDanh;
                                if ($diemDanhTong === null) {
                                    continue;
                                }
                                $tongTangCa += (float) ($diemDanhTong->gio_lam_tang_ca ?? 0);
                                if ($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_PART_TIME) {
                                    $tongCoBan += (float) ($diemDanhTong->gio_lam_co_ban ?? 0);
                                } elseif ($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_FULL_TIME) {
                                    if ($diemDanhTong->gio_vao && $diemDanhTong->gio_ra) {
                                        $tongCoBan++;
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td class="text-center align-middle cham-cong-sticky cham-cong-sticky-col-1">{{ $loop->iteration }}</td>
                            <td class="cham-cong-sticky cham-cong-sticky-col-2">
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
                                    $isWeekend = in_array($day->dayOfWeekIso, [6, 7], true);
                                    $dangKyCa = ($bangCaLam[$dateKey][(int) $u->id] ?? [])[0] ?? null;
                                    $caLam = $dangKyCa?->caLamViec;
                                    if ($caLam) {
                                        $caLamTen = $caLam->ten_ca ?: '';
                                        $caGioBatDau = $caLam->gio_bat_dau
                                            ? \App\Models\CaLamViec::formatGio($caLam->gio_bat_dau)
                                            : '';
                                        $caGioKetThuc = $caLam->gio_ket_thuc
                                            ? \App\Models\CaLamViec::formatGio($caLam->gio_ket_thuc)
                                            : '';
                                    } else {
                                        $caLamTen = '';
                                        $caGioBatDau = '';
                                        $caGioKetThuc = '';
                                    }
                                    $daCheckIn = $diemDanh && $diemDanh->gio_vao;
                                    $daCheckOut = $diemDanh && $diemDanh->gio_ra;
                                    $laQuaKhu = $day->lt(today()->startOfDay());
                                    $chuaCheckout = $daCheckIn && ! $daCheckOut;
                                    $hienIconSua = ($laQuaKhu && ! $daCheckIn) || $chuaCheckout;
                                    $cheDoSua = $daCheckIn ? 'sua' : 'tao-moi';
                                @endphp
                                <td class="text-center align-middle cham-cong-ngay-col {{ $isWeekend ? 'cham-cong-weekend' : '' }}">
                                    <div class="cham-cong-ngay-cell">
                                        @if($daCheckIn)
                                            <div class="cham-cong-ngay-times small">
                                                <span class="text-success fw-medium cham-cong-gio-vao">{{ $diemDanh->gio_vao->format('H:i') }}</span>
                                                <span class="text-muted mx-1">–</span>
                                                <span class="gio-ra fw-medium cham-cong-gio-ra">{{ $daCheckOut ? $diemDanh->gio_ra->format('H:i') : '—' }}</span>
                                            </div>
                                        @else
                                            <div class="cham-cong-ngay-times small text-muted cham-cong-chua-co">—</div>
                                        @endif
                                        @if($hienIconSua)
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-text-primary btn-sua-diem-danh-ho mt-1"
                                                title="{{ $cheDoSua === 'sua' ? 'Sửa điểm danh' : 'Điểm danh hộ' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalSuaDiemDanhHo"
                                                data-user-id="{{ $u->id }}"
                                                data-user-name="{{ $u->name }}"
                                                data-user-email="{{ $u->email }}"
                                                data-user-phone="{{ $u->phone ?? '' }}"
                                                data-loai-nhan-vien="{{ $loaiNv }}"
                                                data-loai-nhan-vien-label="{{ $loaiNvLabel }}"
                                                data-co-loai-nhan-vien="{{ $coLoaiNvHopLe ? '1' : '0' }}"
                                                data-luong-tang-ca="{{ $nvRecord?->luong_tang_ca ?? '' }}"
                                                data-luong-co-ban="{{ $nvRecord?->luong_co_ban ?? '' }}"
                                                data-ngay="{{ $dateKey }}"
                                                data-ngay-hien-thi="{{ $day->format('d/m/Y') }}"
                                                data-ca-lam-ten="{{ $caLamTen }}"
                                                data-ca-gio-bat-dau="{{ $caGioBatDau }}"
                                                data-ca-gio-ket-thuc="{{ $caGioKetThuc }}"
                                                data-gio-vao="{{ $daCheckIn ? $diemDanh->gio_vao->format('H:i') : '' }}"
                                                data-gio-ra="{{ $daCheckOut ? $diemDanh->gio_ra->format('H:i') : '' }}"
                                                data-che-do="{{ $cheDoSua }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            <td class="text-center align-middle cham-cong-tong-col fw-medium">
                                @if($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_FULL_TIME)
                                    {{ $tongCoBan }} ngày
                                @elseif($loaiNv === \App\Models\NhanVien::LOAI_NHAN_VIEN_PART_TIME)
                                    {{ rtrim(rtrim(number_format($tongCoBan, 2, ',', '.'), '0'), ',') }} giờ
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center align-middle cham-cong-tong-col">
                                @if($tongTangCa > 0)
                                    <span class="fw-medium">{{ rtrim(rtrim(number_format($tongTangCa, 2, ',', '.'), '0'), ',') }} giờ</span>
                                @else
                                    <span class="text-muted">0 giờ</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($ngayTrongThang ?? []) + 2 }}" class="text-center py-4 text-muted">
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

{{-- Modal sửa / điểm danh hộ --}}
<div class="modal fade" id="modalSuaDiemDanhHo" tabindex="-1" aria-labelledby="modalSuaDiemDanhHoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-sua-diem-danh-ho">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaDiemDanhHoLabel">Sửa điểm danh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaDiemDanhHo">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="suaDiemDanhHoTenNv">Nhân viên</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoTenNv" readonly tabindex="-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="suaDiemDanhHoLoaiNv">Loại nhân viên</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoLoaiNv" readonly tabindex="-1" placeholder="Chưa cập nhật">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="suaDiemDanhHoEmail">Email</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoEmail" readonly tabindex="-1" placeholder="—">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="suaDiemDanhHoPhone">Số điện thoại</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoPhone" readonly tabindex="-1" placeholder="—">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="suaDiemDanhHoNgay">Ngày làm</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoNgay" readonly tabindex="-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="suaDiemDanhHoCaLamTen">Ca làm việc</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoCaLamTen" readonly tabindex="-1" placeholder="Chưa phân ca làm việc">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="suaDiemDanhHoCaGioBatDau">Giờ bắt đầu ca</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoCaGioBatDau" readonly tabindex="-1" placeholder="—">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="suaDiemDanhHoCaGioKetThuc">Giờ kết thúc ca</label>
                            <input type="text" class="form-control" id="suaDiemDanhHoCaGioKetThuc" readonly tabindex="-1" placeholder="—">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="suaDiemDanhHoGioVao">Giờ vào (check-in) <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="suaDiemDanhHoGioVao" name="gio_vao" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="suaDiemDanhHoGioRa">Giờ ra (check-out)</label>
                            <input type="time" class="form-control" id="suaDiemDanhHoGioRa" name="gio_ra">
                            {{-- <div class="form-text">Để trống nếu chưa check-out.</div> --}}
                        </div>
                    </div>
                    <div id="suaDiemDanhHoCanhBaoLoaiNv" class="alert alert-warning d-none mt-3 mb-0 small" role="alert">
                        Nhân viên chưa có loại nhân viên (Full-time / Part-time). Vui lòng cập nhật trong Danh sách nhân sự trước khi điểm danh.
                    </div>
                    <div id="suaDiemDanhHoTomTat" class="sua-diem-danh-ho-tom-tat d-none mt-3 mb-0 small" role="status">
                        <div class="tom-tat-tieu-de mb-2">Ước tính khi lưu</div>
                        <div id="suaDiemDanhHoTomTatNoiDung" class="tom-tat-noi-dung"></div>
                    </div>
                    <div id="suaDiemDanhHoLoi" class="alert alert-danger d-none mt-3 mb-0 small" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnLuuDiemDanhHo">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu
                    </button>
                </div>
            </form>
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
.cham-cong-table .cham-cong-sticky {
    position: sticky;
    z-index: 2;
    background-color: #fff;
    background-clip: padding-box;
}
.cham-cong-table thead .cham-cong-sticky {
    z-index: 6;
    background-color: #f8f9fa;
}
.cham-cong-table.table-hover > tbody > tr:hover > .cham-cong-sticky {
    background-color: #f5f5f9;
}
[data-bs-theme='dark'] .cham-cong-table .cham-cong-sticky {
    background-color: #2f3349;
}
[data-bs-theme='dark'] .cham-cong-table thead .cham-cong-sticky {
    background-color: #353a52;
}
[data-bs-theme='dark'] .cham-cong-table.table-hover > tbody > tr:hover > .cham-cong-sticky {
    background-color: #3a3f5c;
}
.cham-cong-table .cham-cong-sticky-col-1 {
    left: 0;
    min-width: 48px;
}
.cham-cong-table .cham-cong-sticky-col-2 {
    left: 48px;
    min-width: 220px;
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.08);
}
[data-bs-theme='dark'] .cham-cong-table .cham-cong-sticky-col-2 {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.35);
}
.cham-cong-table .cham-cong-ngay-col.cham-cong-weekend {
    background-color: rgba(var(--bs-secondary-rgb, 108, 117, 125), 0.15);
}
.cham-cong-table thead .cham-cong-ngay-col.cham-cong-weekend {
    background-color: rgba(var(--bs-secondary-rgb, 108, 117, 125), 0.25);
}
[data-bs-theme='dark'] .cham-cong-table .cham-cong-ngay-col.cham-cong-weekend {
    background-color: rgba(108, 117, 125, 0.12);
}
[data-bs-theme='dark'] .cham-cong-table thead .cham-cong-ngay-col.cham-cong-weekend {
    background-color: rgba(108, 117, 125, 0.2);
}
.cham-cong-table .cham-cong-tong-col {
    background-color: rgba(var(--bs-primary-rgb, 13, 110, 253), 0.08);
}
.cham-cong-table thead .cham-cong-tong-col {
    background-color: rgba(var(--bs-primary-rgb, 13, 110, 253), 0.14);
}
[data-bs-theme='dark'] .cham-cong-table .cham-cong-tong-col {
    background-color: rgba(var(--bs-primary-rgb, 13, 110, 253), 0.12);
}
[data-bs-theme='dark'] .cham-cong-table thead .cham-cong-tong-col {
    background-color: rgba(var(--bs-primary-rgb, 13, 110, 253), 0.18);
}
.gio-ra { color: #e8590c; }
.cham-cong-ngay-cell {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.125rem;
    min-height: 2.5rem;
}
.cham-cong-ngay-cell .btn-sua-diem-danh-ho {
    width: 1.75rem;
    height: 1.75rem;
    padding: 0;
    opacity: 0.55;
    transition: opacity 0.15s ease;
}
.cham-cong-ngay-col:hover .btn-sua-diem-danh-ho,
.cham-cong-ngay-cell .btn-sua-diem-danh-ho:focus {
    opacity: 1;
}
/* Chỉ chọn tháng/năm — ẩn lưới ngày (Bootstrap Daterangepicker) */
.daterangepicker.cham-cong-thang-nam-picker .drp-calendar thead tr:not(:first-child),
.daterangepicker.cham-cong-thang-nam-picker .drp-calendar tbody,
.daterangepicker.cham-cong-thang-nam-picker .drp-calendar thead th.prev,
.daterangepicker.cham-cong-thang-nam-picker .drp-calendar thead th.next,
.daterangepicker.cham-cong-thang-nam-picker .drp-buttons {
    display: none !important;
}
.daterangepicker.cham-cong-thang-nam-picker .drp-calendar {
    border: 0;
}
.daterangepicker.cham-cong-thang-nam-picker .drp-calendar thead th.month {
    width: 100%;
    padding: 0.5rem 0.75rem;
}
.daterangepicker.cham-cong-thang-nam-picker .monthselect,
.daterangepicker.cham-cong-thang-nam-picker .yearselect {
    font-size: 0.9375rem;
    padding: 0.35rem 0.5rem;
}
.modal-sua-diem-danh-ho {
    max-width: 720px;
}
.modal-sua-diem-danh-ho .form-control[readonly] {
    background-color: var(--bs-secondary-bg, #f5f5f9);
    cursor: default;
}
[data-bs-theme='dark'] .modal-sua-diem-danh-ho .form-control[readonly] {
    background-color: rgba(255, 255, 255, 0.05);
}
.sua-diem-danh-ho-tom-tat {
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    background-color: #f5f5f9;
    border: 1px solid #d9dee3;
    color: #566a7f;
}
.sua-diem-danh-ho-tom-tat .tom-tat-tieu-de {
    font-weight: 600;
    color: #384551;
}
.sua-diem-danh-ho-tom-tat .tom-tat-noi-dung > div + div {
    margin-top: 0.35rem;
}
.sua-diem-danh-ho-tom-tat .tom-tat-luong {
    color: #198754;
    font-weight: 600;
}
.sua-diem-danh-ho-tom-tat .tom-tat-phat {
    color: #dc3545;
    font-weight: 600;
}
.sua-diem-danh-ho-tom-tat .tom-tat-tong {
    color: #0f5132;
    font-weight: 700;
}
.sua-diem-danh-ho-tom-tat .tom-tat-tong-am {
    color: #dc3545;
    font-weight: 700;
}
.sua-diem-danh-ho-tom-tat .tom-tat-phu {
    color: #697a8d;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat {
    background-color: #2f3349;
    border-color: #434968;
    color: #b4b7c8;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat .tom-tat-tieu-de {
    color: #dbdee7;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat .tom-tat-luong {
    color: #71dd8a;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat .tom-tat-phat {
    color: #ff6b6b;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat .tom-tat-tong {
    color: #71dd8a;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat .tom-tat-tong-am {
    color: #ff6b6b;
}
[data-bs-theme='dark'] .sua-diem-danh-ho-tom-tat .tom-tat-phu {
    color: #9aa0b8;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script>
(function () {
    if (window.__chamCongThangPickerInit) return;
    window.__chamCongThangPickerInit = true;

    var $ = window.jQuery;
    if (!$ || !$.fn.daterangepicker || typeof moment === 'undefined') return;

    var $inp = $('#cham_cong_thang');
    var $hidden = $('#cham_cong_thang_value');
    var $form = $inp.closest('form');
    if (!$inp.length || !$hidden.length) return;

    function monthFromHidden() {
        var v = ($hidden.val() || '').trim();
        if (!v) return moment().startOf('month');
        var m = moment(v + '-01', 'YYYY-MM-DD', true);
        return m.isValid() ? m.startOf('month') : moment().startOf('month');
    }

    function syncLabel(m) {
        $inp.val(m && m.isValid() ? m.format('MM/YYYY') : '');
    }

    function syncHidden(m) {
        if (m && m.isValid()) {
            $hidden.val(m.clone().startOf('month').format('YYYY-MM'));
        }
    }

    function monthFromPicker(picker) {
        var $container = picker.container;
        var month = parseInt($container.find('.monthselect').val(), 10);
        var year = parseInt($container.find('.yearselect').val(), 10);
        if (Number.isNaN(month) || Number.isNaN(year)) {
            return picker.startDate.clone().startOf('month');
        }
        return moment({ year: year, month: month, day: 1 }).startOf('month');
    }

    function applyMonthYear(picker, closePicker) {
        var m = monthFromPicker(picker);
        picker.setStartDate(m.clone());
        picker.setEndDate(m.clone());
        syncHidden(m);
        syncLabel(m);
        if (closePicker) {
            picker.hide();
        }
    }

    var startMonth = monthFromHidden();

    $inp.daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 2000,
        maxYear: 2100,
        autoApply: true,
        autoUpdateInput: false,
        opens: 'right',
        startDate: startMonth.clone(),
        locale: {
            format: 'MM/YYYY',
            applyLabel: 'Áp dụng',
            cancelLabel: 'Hủy',
            firstDay: 1,
            monthNames: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ]
        }
    });

    syncLabel(startMonth);

    $inp.on('show.daterangepicker', function (ev, picker) {
        picker.container.addClass('cham-cong-thang-nam-picker');
        picker.container.find('.monthselect, .yearselect')
            .off('change.chamCongThang')
            .on('change.chamCongThang', function () {
                applyMonthYear(picker, true);
            });
    });

    $inp.on('apply.daterangepicker', function (ev, picker) {
        applyMonthYear(picker, false);
    });

    if ($form.length) {
        $form.on('submit', function () {
            var drp = $inp.data('daterangepicker');
            if (drp) {
                applyMonthYear(drp, false);
            }
        });
    }
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modalSuaDiemDanhHo');
    var formEl = document.getElementById('formSuaDiemDanhHo');
    var btnLuu = document.getElementById('btnLuuDiemDanhHo');
    var elTitle = document.getElementById('modalSuaDiemDanhHoLabel');
    var inpTenNv = document.getElementById('suaDiemDanhHoTenNv');
    var inpEmail = document.getElementById('suaDiemDanhHoEmail');
    var inpPhone = document.getElementById('suaDiemDanhHoPhone');
    var inpLoaiNv = document.getElementById('suaDiemDanhHoLoaiNv');
    var inpNgay = document.getElementById('suaDiemDanhHoNgay');
    var inpCaLamTen = document.getElementById('suaDiemDanhHoCaLamTen');
    var inpCaGioBatDau = document.getElementById('suaDiemDanhHoCaGioBatDau');
    var inpCaGioKetThuc = document.getElementById('suaDiemDanhHoCaGioKetThuc');
    var inpGioVao = document.getElementById('suaDiemDanhHoGioVao');
    var inpGioRa = document.getElementById('suaDiemDanhHoGioRa');
    var elLoi = document.getElementById('suaDiemDanhHoLoi');
    var elCanhBaoLoaiNv = document.getElementById('suaDiemDanhHoCanhBaoLoaiNv');
    var elTomTat = document.getElementById('suaDiemDanhHoTomTat');
    var elTomTatNoiDung = document.getElementById('suaDiemDanhHoTomTatNoiDung');
    var urlCapNhat = @json(route('admin.diem-danh.cham-cong.diem-danh-ho'));
    var urlThongTinCaLam = @json(route('admin.diem-danh.cham-cong.ca-lam-ngay'));
    var phutTangCaToiThieu = @json(\App\Support\TinhLuongDiemDanh::phutTangCaToiThieu());
    var loaiNvFullTime = @json(\App\Models\NhanVien::LOAI_NHAN_VIEN_FULL_TIME);
    var loaiNvPartTime = @json(\App\Models\NhanVien::LOAI_NHAN_VIEN_PART_TIME);
    var gioChuyenTangCa = @json(config('diem_danh.gio_chuyen_tang_ca', '21:00'));

    if (!modalEl || !formEl) {
        return;
    }

    var state = {
        userId: null,
        ngay: null,
        triggerBtn: null,
        cheDo: 'tao-moi',
        coLoaiNvHopLe: false,
        loaiNhanVien: '',
        luongCoBan: 0,
        luongTangCa: 0,
        caGioBatDau: '',
        caGioKetThuc: ''
    };

    function phutTuChuoiGio(chuoiGio) {
        if (!chuoiGio || typeof chuoiGio !== 'string') {
            return null;
        }
        var parts = chuoiGio.trim().split(':');
        if (parts.length < 2) {
            return null;
        }
        var h = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10);
        if (Number.isNaN(h) || Number.isNaN(m)) {
            return null;
        }
        return h * 60 + m;
    }

    function dinhDangTien(so) {
        return new Intl.NumberFormat('vi-VN').format(so) + ' đ';
    }

    function tinhTienPhatTheoSoPhut(soPhut) {
        if (soPhut <= 0) {
            return 0;
        }
        return soPhut <= 30 ? 50000 : 100000;
    }

    function tinhPhutDiMuon(gioVaoPhut, gioBatDauCaPhut) {
        if (gioVaoPhut === null || gioBatDauCaPhut === null || gioVaoPhut <= gioBatDauCaPhut) {
            return 0;
        }
        return gioVaoPhut - gioBatDauCaPhut;
    }

    function tinhPhutVeSom(gioRaPhut, gioKetThucCaPhut) {
        if (gioRaPhut === null || gioKetThucCaPhut === null || gioRaPhut >= gioKetThucCaPhut) {
            return 0;
        }
        return gioKetThucCaPhut - gioRaPhut;
    }

    function tinhPhutTangCaFullTime(gioVaoPhut, gioRaPhut, gioKetThucCaPhut) {
        if (gioVaoPhut === null || gioRaPhut === null || gioKetThucCaPhut === null || gioRaPhut <= gioKetThucCaPhut) {
            return 0;
        }
        var gioBatDauTangCa = gioVaoPhut > gioKetThucCaPhut ? gioVaoPhut : gioKetThucCaPhut;
        return gioRaPhut - gioBatDauTangCa;
    }

    function tinhLuongPartTime(gioVaoPhut, gioRaPhut) {
        if (gioVaoPhut === null || gioRaPhut === null || gioRaPhut <= gioVaoPhut) {
            return null;
        }

        var gioChuyenPhut = phutTuChuoiGio(gioChuyenTangCa);
        if (gioChuyenPhut === null) {
            return null;
        }

        var phutCoBan = 0;
        var phutTangCa = 0;

        if (gioRaPhut <= gioChuyenPhut) {
            phutCoBan = gioRaPhut - gioVaoPhut;
        } else if (gioVaoPhut >= gioChuyenPhut) {
            phutTangCa = gioRaPhut - gioVaoPhut;
        } else {
            phutCoBan = gioChuyenPhut - gioVaoPhut;
            phutTangCa = gioRaPhut - gioChuyenPhut;
        }

        var gioLamCoBan = Math.round((phutCoBan / 60) * 100) / 100;
        var gioLamTangCa = Math.round((phutTangCa / 60) * 100) / 100;
        var luongCoBan = Math.round(gioLamCoBan * state.luongCoBan);
        var luongTangCa = phutTangCa >= phutTangCaToiThieu
            ? Math.round(gioLamTangCa * state.luongTangCa)
            : 0;

        return {
            phutCoBan: phutCoBan,
            phutTangCa: phutTangCa,
            gioLamCoBan: gioLamCoBan,
            gioLamTangCa: gioLamTangCa,
            luongCoBan: luongCoBan,
            luongTangCa: luongTangCa,
            tongLuong: luongCoBan + luongTangCa
        };
    }

    function capNhatTomTatUocTinh() {
        if (!elTomTat || !elTomTatNoiDung) {
            return;
        }

        var dong = [];
        var gioVaoPhut = inpGioVao && inpGioVao.value ? phutTuChuoiGio(inpGioVao.value) : null;
        var gioRaPhut = inpGioRa && inpGioRa.value ? phutTuChuoiGio(inpGioRa.value) : null;
        var gioBatDauCaPhut = phutTuChuoiGio(state.caGioBatDau);
        var gioKetThucCaPhut = phutTuChuoiGio(state.caGioKetThuc);
        var tienPhatDiMuon = 0;
        var tienPhatVeSom = 0;
        var tongThuNhap = 0;

        function themDong(html) {
            dong.push('<div>' + html + '</div>');
        }

        if (gioVaoPhut !== null) {
            var phutDiMuon = tinhPhutDiMuon(gioVaoPhut, gioBatDauCaPhut);
            tienPhatDiMuon = tinhTienPhatTheoSoPhut(phutDiMuon);
            if (tienPhatDiMuon > 0) {
                themDong(
                    'Phạt đi muộn: <span class="tom-tat-phat">− ' + dinhDangTien(tienPhatDiMuon) + '</span>' +
                    ' <span class="tom-tat-phu">(' + phutDiMuon + ' phút)</span>'
                );
            }
        }

        if (gioRaPhut !== null) {
            var phutVeSom = tinhPhutVeSom(gioRaPhut, gioKetThucCaPhut);
            tienPhatVeSom = tinhTienPhatTheoSoPhut(phutVeSom);
            if (tienPhatVeSom > 0) {
                themDong(
                    'Phạt về sớm: <span class="tom-tat-phat">− ' + dinhDangTien(tienPhatVeSom) + '</span>' +
                    ' <span class="tom-tat-phu">(' + phutVeSom + ' phút)</span>'
                );
            }

            if (state.loaiNhanVien === loaiNvFullTime) {
                var phutTangCa = tinhPhutTangCaFullTime(gioVaoPhut, gioRaPhut, gioKetThucCaPhut);
                var gioTangCa = Math.round((phutTangCa / 60) * 100) / 100;
                var luongTangCa = phutTangCa >= phutTangCaToiThieu
                    ? Math.round(gioTangCa * state.luongTangCa)
                    : 0;
                tongThuNhap += luongTangCa;
                if (luongTangCa > 0) {
                    themDong(
                        'Lương tăng ca: <span class="tom-tat-luong">' + dinhDangTien(luongTangCa) + '</span>' +
                        ' <span class="tom-tat-phu">(' + gioTangCa + ' giờ × ' + dinhDangTien(state.luongTangCa) + '/giờ)</span>'
                    );
                } else if (phutTangCa > 0 && phutTangCa < phutTangCaToiThieu) {
                    themDong('<span class="tom-tat-phu">Tăng ca ' + phutTangCa + ' phút (dưới ngưỡng ' + phutTangCaToiThieu + ' phút, chưa tính lương).</span>');
                }
            } else if (state.loaiNhanVien === loaiNvPartTime) {
                var luongPt = tinhLuongPartTime(gioVaoPhut, gioRaPhut);
                if (luongPt) {
                    tongThuNhap += luongPt.tongLuong;
                    if (luongPt.luongCoBan > 0) {
                        themDong(
                            'Lương cơ bản: <span class="tom-tat-luong">' + dinhDangTien(luongPt.luongCoBan) + '</span>' +
                            ' <span class="tom-tat-phu">(' + luongPt.gioLamCoBan + ' giờ × ' + dinhDangTien(state.luongCoBan) + '/giờ, trước ' + gioChuyenTangCa + ')</span>'
                        );
                    }
                    if (luongPt.luongTangCa > 0) {
                        themDong(
                            'Lương tăng ca: <span class="tom-tat-luong">' + dinhDangTien(luongPt.luongTangCa) + '</span>' +
                            ' <span class="tom-tat-phu">(' + luongPt.gioLamTangCa + ' giờ × ' + dinhDangTien(state.luongTangCa) + '/giờ, từ ' + gioChuyenTangCa + ')</span>'
                        );
                    } else if (luongPt.phutTangCa > 0 && luongPt.phutTangCa < phutTangCaToiThieu) {
                        themDong('<span class="tom-tat-phu">Tăng ca ' + luongPt.phutTangCa + ' phút (dưới ngưỡng ' + phutTangCaToiThieu + ' phút, chưa tính lương).</span>');
                    }
                }
            }
        }

        var tongPhat = tienPhatDiMuon + tienPhatVeSom;
        var tongLuongUocTinh = tongThuNhap - tongPhat;
        if (tongThuNhap > 0 || tongPhat > 0) {
            var lopTong = tongLuongUocTinh < 0 ? 'tom-tat-tong-am' : 'tom-tat-tong';
            var hienThiTong = tongLuongUocTinh < 0
                ? '− ' + dinhDangTien(Math.abs(tongLuongUocTinh))
                : dinhDangTien(tongLuongUocTinh);
            themDong('Tổng lương ước tính: <span class="' + lopTong + '">' + hienThiTong + '</span>');
        }

        if (dong.length === 0) {
            elTomTat.classList.add('d-none');
            elTomTatNoiDung.innerHTML = '';
            return;
        }

        elTomTatNoiDung.innerHTML = dong.join('');
        elTomTat.classList.remove('d-none');
    }

    function anLoi() {
        if (!elLoi) {
            return;
        }
        elLoi.classList.add('d-none');
        elLoi.textContent = '';
    }

    function capNhatTrangThaiNutLuu(coLoaiNvHopLe) {
        state.coLoaiNvHopLe = !!coLoaiNvHopLe;

        if (btnLuu) {
            btnLuu.disabled = !state.coLoaiNvHopLe;
        }
        if (elCanhBaoLoaiNv) {
            elCanhBaoLoaiNv.classList.toggle('d-none', state.coLoaiNvHopLe);
        }
        if (inpLoaiNv) {
            inpLoaiNv.classList.toggle('is-invalid', !state.coLoaiNvHopLe);
        }
    }

    function hienLoi(message) {
        if (!elLoi) {
            return;
        }
        elLoi.textContent = message || 'Không thể lưu điểm danh.';
        elLoi.classList.remove('d-none');
    }

    function datGiaTriReadonly(input, value, placeholder) {
        if (!input) {
            return;
        }
        input.value = value || '';
        input.placeholder = placeholder || '—';
    }

    function coGiaTriCa(value) {
        var text = (value || '').trim();
        return text !== '' && text !== '—';
    }

    function hienThongTinCaLam(tenCa, gioBatDau, gioKetThuc) {
        var chuaPhanCa = !coGiaTriCa(tenCa) && !coGiaTriCa(gioBatDau) && !coGiaTriCa(gioKetThuc);
        datGiaTriReadonly(inpCaLamTen, tenCa, chuaPhanCa ? 'Chưa phân ca làm việc' : '—');
        datGiaTriReadonly(inpCaGioBatDau, gioBatDau);
        datGiaTriReadonly(inpCaGioKetThuc, gioKetThuc);
    }

    function taiThongTinCaLam() {
        if (!state.userId || !state.ngay) {
            return;
        }

        hienThongTinCaLam('', '', '');
        if (inpCaLamTen) {
            inpCaLamTen.placeholder = 'Đang tải ca làm việc...';
        }

        RestApi.get(urlThongTinCaLam + '?' + new URLSearchParams({
            user_id: state.userId,
            ngay_lam: state.ngay
        }).toString())
            .then(function (res) {
                var body = res.data || {};
                if (!res.ok || !body.success) {
                    throw new Error(body.message || 'Không thể tải ca làm việc.');
                }

                var ca = body.ca_lam || null;
                if (ca) {
                    state.caGioBatDau = ca.gio_bat_dau || '';
                    state.caGioKetThuc = ca.gio_ket_thuc || '';
                    hienThongTinCaLam(ca.ten_ca || '', state.caGioBatDau, state.caGioKetThuc);
                } else {
                    state.caGioBatDau = '';
                    state.caGioKetThuc = '';
                    hienThongTinCaLam('', '', '');
                }

                capNhatTomTatUocTinh();
            })
            .catch(function () {
                state.caGioBatDau = '';
                state.caGioKetThuc = '';
                hienThongTinCaLam('', '', '');
            });
    }

    function napModalTuNut(btn) {
        if (!btn) {
            return;
        }

        state.userId = btn.getAttribute('data-user-id');
        state.ngay = btn.getAttribute('data-ngay');
        state.triggerBtn = btn;
        state.cheDo = btn.getAttribute('data-che-do') || 'tao-moi';
        state.loaiNhanVien = btn.getAttribute('data-loai-nhan-vien') || '';
        state.luongCoBan = parseInt(btn.getAttribute('data-luong-co-ban') || '0', 10) || 0;
        state.luongTangCa = parseInt(btn.getAttribute('data-luong-tang-ca') || '0', 10) || 0;

        datGiaTriReadonly(inpTenNv, btn.getAttribute('data-user-name') || '');
        datGiaTriReadonly(inpEmail, btn.getAttribute('data-user-email') || '');
        datGiaTriReadonly(inpPhone, btn.getAttribute('data-user-phone') || '');
        datGiaTriReadonly(
            inpLoaiNv,
            btn.getAttribute('data-loai-nhan-vien-label') || '',
            'Chưa cập nhật'
        );
        datGiaTriReadonly(inpNgay, btn.getAttribute('data-ngay-hien-thi') || '');

        capNhatTrangThaiNutLuu(btn.getAttribute('data-co-loai-nhan-vien') === '1');

        if (inpGioVao) {
            inpGioVao.value = btn.getAttribute('data-gio-vao') || '';
        }
        if (inpGioRa) {
            inpGioRa.value = btn.getAttribute('data-gio-ra') || '';
        }

        if (elTitle) {
            elTitle.textContent = state.cheDo === 'sua' ? 'Sửa điểm danh' : 'Điểm danh hộ';
        }

        anLoi();
        taiThongTinCaLam();
    }

    modalEl.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        if (!btn || !btn.classList.contains('btn-sua-diem-danh-ho')) {
            return;
        }
        napModalTuNut(btn);
    });

    if (inpGioVao) {
        inpGioVao.addEventListener('input', capNhatTomTatUocTinh);
        inpGioVao.addEventListener('change', capNhatTomTatUocTinh);
    }
    if (inpGioRa) {
        inpGioRa.addEventListener('input', capNhatTomTatUocTinh);
        inpGioRa.addEventListener('change', capNhatTomTatUocTinh);
    }

    modalEl.addEventListener('hidden.bs.modal', function () {
        anLoi();
        if (elTomTat) {
            elTomTat.classList.add('d-none');
        }
        if (elTomTatNoiDung) {
            elTomTatNoiDung.innerHTML = '';
        }
        if (elCanhBaoLoaiNv) {
            elCanhBaoLoaiNv.classList.add('d-none');
        }
        if (inpLoaiNv) {
            inpLoaiNv.classList.remove('is-invalid');
        }
    });

    formEl.addEventListener('submit', function (ev) {
        ev.preventDefault();
        anLoi();

        if (!state.coLoaiNvHopLe) {
            hienLoi('Không cho điểm danh. Liên hệ admin hoặc kiểm tra lại.');
            return;
        }

        if (!state.userId || !state.ngay) {
            hienLoi('Thiếu thông tin nhân viên hoặc ngày.');
            return;
        }

        if (!inpGioVao || !inpGioVao.value) {
            hienLoi('Vui lòng nhập giờ vào.');
            return;
        }

        var payload = {
            user_id: parseInt(state.userId, 10),
            ngay_diem_danh: state.ngay,
            gio_vao: inpGioVao.value
        };

        if (inpGioRa && inpGioRa.value) {
            payload.gio_ra = inpGioRa.value;
        }

        if (btnLuu) {
            btnLuu.disabled = true;
        }

        RestApi.put(urlCapNhat, payload)
            .then(function (res) {
                var body = res.data || {};
                if (!res.ok || !body.success) {
                    var msg = body.message;
                    if (!msg && body.errors) {
                        var firstKey = Object.keys(body.errors)[0];
                        msg = firstKey && body.errors[firstKey] ? body.errors[firstKey][0] : null;
                    }
                    throw new Error(msg || 'Không thể lưu điểm danh.');
                }

                if (state.triggerBtn) {
                    var cell = state.triggerBtn.closest('.cham-cong-ngay-cell');
                    if (cell) {
                        var timesEl = cell.querySelector('.cham-cong-ngay-times');
                        var gioVao = body.gio_vao || inpGioVao.value;
                        var gioRa = body.gio_ra || (inpGioRa && inpGioRa.value ? inpGioRa.value : '');

                        if (timesEl) {
                            timesEl.classList.remove('cham-cong-chua-co', 'text-muted');
                            timesEl.innerHTML =
                                '<span class="text-success fw-medium cham-cong-gio-vao">' + gioVao + '</span>' +
                                '<span class="text-muted mx-1">–</span>' +
                                '<span class="gio-ra fw-medium cham-cong-gio-ra">' + (gioRa || '—') + '</span>';
                        }

                        if (gioRa) {
                            state.triggerBtn.remove();
                        } else {
                            state.triggerBtn.setAttribute('data-gio-vao', gioVao);
                            state.triggerBtn.setAttribute('data-gio-ra', '');
                            state.triggerBtn.setAttribute('data-che-do', 'sua');
                            state.triggerBtn.title = 'Sửa điểm danh';
                        }
                    }
                }

                bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            })
            .catch(function (err) {
                hienLoi(err && err.message ? err.message : 'Không thể lưu điểm danh.');
            })
            .finally(function () {
                if (btnLuu) {
                    btnLuu.disabled = !state.coLoaiNvHopLe;
                }
            });
    });
});
</script>
@endpush
@endsection
