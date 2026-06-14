@extends('admin.layouts.app')

@section('content')
<div class="card tao-hop-dong-wizard">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h5 class="mb-0 d-flex flex-wrap align-items-center gap-2">
                <span>{{ ($laManChinhSuaHopDong ?? false) ? 'Chỉnh sửa hợp đồng' : 'Tạo hợp đồng' }}</span>
                @if(($hopDongCuoi->trang_thai_hop_dong ?? null) === 'da_huy')
                    <span class="badge bg-danger">Đã huỷ</span>
                @endif
            </h5>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
            @if(($hopDongCuoi->trang_thai_hop_dong ?? null) !== 'da_huy')
                <form
                    id="formHuyHopDongCuoi"
                    method="POST"
                    action="{{ route('admin.khach-hang.hop-dong-cuoi.huy', $hopDongCuoi) }}"
                    class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnMoModalHuyHopDongCuoi">
                        <i class="fa-solid fa-ban me-1"></i> Huỷ hợp đồng
                    </button>
                </form>
            @endif

            @if($laManChinhSuaHopDong ?? false)
            <a href="{{ route('admin.khach-hang.danh-sach-hop-dong-cuoi') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Danh sách hợp đồng
            </a>
            @else
            {{-- <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Trang chủ admin
            </a> --}}
            @endif
        </div>
    </div>
    <div class="card-body">
        {{-- Thanh bước --}}
        <div class="wizard-steps mb-4" role="tablist" aria-label="Các bước tạo hợp đồng">
            <div class="wizard-step active" data-step="1" data-wizard-step-indicator>
                <button type="button" class="wizard-step-btn" data-go-step="1" aria-current="step">
                    <span class="wizard-step-num">1</span>
                    <span class="wizard-step-label">Thông tin khách hàng</span>
                </button>
            </div>
            <div class="wizard-step-line" aria-hidden="true"></div>
            <div class="wizard-step" data-step="2" data-wizard-step-indicator>
                <button type="button" class="wizard-step-btn" data-go-step="2">
                    <span class="wizard-step-num">2</span>
                    <span class="wizard-step-label">Dịch vụ</span>
                </button>
            </div>
            <div class="wizard-step-line" aria-hidden="true"></div>
            <div class="wizard-step" data-step="3" data-wizard-step-indicator>
                <button type="button" class="wizard-step-btn" data-go-step="3">
                    <span class="wizard-step-num">3</span>
                    <span class="wizard-step-label">Xác nhận thanh toán</span>
                </button>
            </div>
        </div>

        <form id="formTaoHopDongWizard"
            action="#"
            method="POST"
            onsubmit="return false;"
            data-save-step1-url="{{ route('admin.khach-hang.tao-hop-dong.cap-nhat-buoc-1', $hopDongCuoi) }}"
            data-save-step2-url="{{ route('admin.khach-hang.tao-hop-dong.cap-nhat-buoc-2', $hopDongCuoi) }}"
            data-save-step3-url="{{ route('admin.khach-hang.tao-hop-dong.cap-nhat-buoc-3', $hopDongCuoi) }}"
            data-after-submit-redirect="{{ route('admin.khach-hang.danh-sach-hop-dong-cuoi') }}"
            @if($laManChinhSuaHopDong ?? false) data-la-chinh-sua="1" @endif
            data-wizard-step2-restore="{{ isset($wizardStep2Restore) && $wizardStep2Restore !== null ? e(json_encode($wizardStep2Restore, JSON_UNESCAPED_UNICODE)) : '' }}"
            data-hop-dong-cuoi='@json($hopDongCuoiData ?? [])'>
            @csrf
            <input type="hidden" name="hop_dong_cuoi_id" value="{{ $hopDongCuoi->id }}">
            <input type="hidden" name="ma_hop_dong" value="{{ $hopDongCuoi->ma_hop_dong }}">
            <input type="hidden" id="wizard_buoi_chup" name="buoi_chup" value="{{ old('buoi_chup', $hopDongCuoi->buoi_chup) }}">

            {{-- Bước 1: Thông tin khách hàng --}}
            <div class="wizard-panel active" data-wizard-panel="1" role="tabpanel">
                <h6 class="text-primary mb-3"><i class="fa-solid fa-user-group me-2"></i>Bước 1 — Thông tin khách hàng</h6>
                <div id="wizard-step1-errors" class="alert alert-danger d-none mb-3" role="alert"></div>
                <div class="row g-3 wizard-step1-grid">
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label text-muted" for="wizard_ma_hop_dong">Mã hợp đồng</label>
                        <input type="text" class="form-control bg-light" id="wizard_ma_hop_dong" value="{{ $hopDongCuoi->ma_hop_dong }}" placeholder="Mã đã gán" readonly tabindex="-1">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_loai_hop_dong">Loại hợp đồng</label>
                        <select class="select2-admin form-select" id="wizard_loai_hop_dong" name="loai_hop_dong" data-placeholder="Chọn loại hợp đồng">
                            <option value="">-- Chọn loại --</option>
                            @foreach (\App\Models\HopDongCuoi::LOAI_HOP_DONG as $value => $label)
                                <option value="{{ $value }}" @selected(old('loai_hop_dong', $hopDongCuoi->loai_hop_dong) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_ten_chu_re">Họ tên chú rể</label>
                        <input type="text" class="form-control" id="wizard_ten_chu_re" name="ten_chu_re" value="{{ old('ten_chu_re', $hopDongCuoi->ten_chu_re) }}" placeholder="Họ và tên" data-wizard-step1-required>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_ten_co_dau">Họ tên cô dâu</label>
                        <input type="text" class="form-control" id="wizard_ten_co_dau" name="ten_co_dau" value="{{ old('ten_co_dau', $hopDongCuoi->ten_co_dau) }}" placeholder="Họ và tên" data-wizard-step1-required>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_email_sdt_chu_re">SĐT chú rể</label>
                        <input type="text" class="form-control" id="wizard_email_sdt_chu_re" name="email_sdt_chu_re" value="{{ old('email_sdt_chu_re', $hopDongCuoi->email_sdt_chu_re) }}" placeholder="Số điện thoại" autocomplete="tel">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_email_sdt_co_dau">SĐT cô dâu</label>
                        <input type="text" class="form-control" id="wizard_email_sdt_co_dau" name="email_sdt_co_dau" value="{{ old('email_sdt_co_dau', $hopDongCuoi->email_sdt_co_dau) }}" placeholder="Số điện thoại" autocomplete="tel">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_ngay_chup_du_kien">Ngày chụp dự kiến</label>
                        <input type="text" class="flatpickr-date-admin form-control" id="wizard_ngay_chup_du_kien" name="ngay_chup_du_kien" value="{{ old('ngay_chup_du_kien', optional($hopDongCuoi->ngay_chup_du_kien)->format('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_ngay_cuoi_du_kien">Ngày cưới dự kiến</label>
                        <input type="text" class="flatpickr-date-admin form-control" id="wizard_ngay_cuoi_du_kien" name="ngay_cuoi_du_kien" value="{{ old('ngay_cuoi_du_kien', optional($hopDongCuoi->ngay_cuoi_du_kien)->format('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label" for="wizard_kenh_tiep_can">Kênh tiếp cận</label>
                        <select class="select2-admin form-select" id="wizard_kenh_tiep_can" name="kenh_tiep_can" data-placeholder="Chọn kênh tiếp cận">
                            <option value="">-- Chọn kênh --</option>
                            <option value="facebook" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'facebook')>Facebook</option>
                            <option value="instagram" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'instagram')>Instagram</option>
                            <option value="tiktok" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'tiktok')>TikTok</option>
                            <option value="zalo" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'zalo')>Zalo</option>
                            <option value="google" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'google')>Google / tìm kiếm</option>
                            <option value="gioi_thieu" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'gioi_thieu')>Giới thiệu</option>
                            <option value="khac" @selected(old('kenh_tiep_can', $hopDongCuoi->kenh_tiep_can) === 'khac')>Khác</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-8 col-lg-3">
                        <label class="form-label" for="wizard_thanh_vien_sale_ids">Thành viên sale</label>
                        <select class="select2-admin form-select" id="wizard_thanh_vien_sale_ids" name="thanh_vien_nhan_vien_ids[]" multiple data-placeholder="Chọn nhân sự (có thể chọn nhiều)">
                            @php
                                $thanhVienDaChon = collect(old('thanh_vien_nhan_vien_ids', $hopDongCuoiData['thanh_vien_nhan_vien_ids'] ?? []))
                                    ->map(fn ($value) => (string) $value)
                                    ->all();
                            @endphp
                            @foreach ($danhSachNhanVien ?? [] as $nv)
                                @php
                                    $tenNv = $nv->user?->name ?? ('Nhân viên #' . $nv->id);
                                @endphp
                                <option value="{{ $nv->id }}" @selected(in_array((string) $nv->id, $thanhVienDaChon, true))>{{ $tenNv }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="wizard_yeu_cau_dac_biet">Yêu cầu đặc biệt</label>
                        <textarea class="form-control" id="wizard_yeu_cau_dac_biet" name="yeu_cau_dac_biet" rows="3" placeholder="Ghi chú yêu cầu riêng của khách như yêu cầu thợ, địa điểm chụp,...">{{ old('yeu_cau_dac_biet', $hopDongCuoi->yeu_cau_dac_biet) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Bước 2: Dịch vụ --}}
            <div class="wizard-panel" data-wizard-panel="2" role="tabpanel" hidden>
                <h6 class="text-primary mb-3"><i class="fa-solid fa-briefcase me-2"></i>Bước 2 — Dịch vụ</h6>
                <div id="wizard-step2-errors" class="alert alert-danger d-none mb-3" role="alert"></div>
                <p class="text-muted small mb-4">Chọn hình thức lên dịch vụ phù hợp cho hợp đồng.</p>

                <div class="nav-align-top">
                    <ul class="nav nav-pills mb-3 nav-fill wizard-service-pills" style="overflow: scroll;" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#wizard-service-combo" aria-controls="wizard-service-combo" aria-selected="true">
                                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-solid fa-box-open"></i>
                                    <span>Combo trọn gói</span>
                                </span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#wizard-service-le" aria-controls="wizard-service-le" aria-selected="false">
                                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <span>Ghép dịch vụ lẻ</span>
                                </span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#wizard-service-nang-cap" aria-controls="wizard-service-nang-cap" aria-selected="false">
                                <span class="d-inline-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                    <span>Combo &amp; nâng cấp</span>
                                </span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="wizard-service-tab-content">
                    <div class="tab-pane fade show active" id="wizard-service-combo" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="wizard-filter-combo" placeholder="Nhập tên combo, thẻ hoặc mô tả..." autocomplete="off">
                                </div>
                                <div class="row g-3" id="wizard-combo-list">
                                    @forelse ($nhomDichVus ?? [] as $nhomDichVu)
                                        @php
                                            $isChecked = (string) old('combo_goi') === (string) $nhomDichVu->id;
                                            $giaTien = is_numeric($nhomDichVu->gia_tien) ? number_format((float) $nhomDichVu->gia_tien, 0, ',', '.') : null;
                                            $radioId = 'wizard_combo_' . $nhomDichVu->id;
                                            $collapseId = 'wizard_combo_services_' . $nhomDichVu->id;
                                        @endphp
                                        <div
                                            class="col-12 col-md-6 col-xl-4 js-combo-item"
                                            data-search-text="{{ mb_strtolower(trim(($nhomDichVu->ten_nhom ?? '') . ' ' . ($nhomDichVu->the ?? '') . ' ' . ($nhomDichVu->mo_ta ?? '') . ' ' . ($nhomDichVu->ghi_chu ?? ''))) }}">
                                            <div class="combo-service-card h-100 w-100">
                                                <input
                                                    class="combo-service-radio"
                                                    type="radio"
                                                    name="combo_goi"
                                                    id="{{ $radioId }}"
                                                    value="{{ $nhomDichVu->id }}"
                                                    data-gia="{{ is_numeric($nhomDichVu->gia_tien) ? (float) $nhomDichVu->gia_tien : 0 }}"
                                                    @checked($isChecked)>
                                                <div class="combo-service-card-body">
                                                    <label class="combo-service-selectable" for="{{ $radioId }}">
                                                        <span class="combo-service-card-head">
                                                            <span class="combo-service-title-wrap">
                                                                <input class="form-check-input combo-service-checkbox" type="checkbox" tabindex="-1" aria-hidden="true" @checked($isChecked) disabled>
                                                                <span class="combo-service-title">{{ $nhomDichVu->ten_nhom }}</span>
                                                            </span>
                                                            @if ($giaTien)
                                                                <span class="combo-service-price">{{ $giaTien }}đ</span>
                                                            @endif
                                                        </span>
                                                        <span class="combo-service-meta">
                                                            {{ $nhomDichVu->dich_vu_le_count ?? 0 }} dịch vụ
                                                            @if (!empty($nhomDichVu->the))
                                                                • {{ $nhomDichVu->the }}
                                                            @endif
                                                        </span>
                                                        @if (!empty($nhomDichVu->mo_ta))
                                                            <span class="combo-service-desc">{{ $nhomDichVu->mo_ta }}</span>
                                                        @elseif (!empty($nhomDichVu->ghi_chu))
                                                            <span class="combo-service-desc">{{ $nhomDichVu->ghi_chu }}</span>
                                                        @else
                                                            <span class="combo-service-desc text-muted">-</span>
                                                        @endif
                                                    </label>
                                                    <div class="combo-service-footer">
                                                        <button
                                                            type="button"
                                                            class="combo-service-toggle"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#{{ $collapseId }}"
                                                            aria-expanded="false"
                                                            aria-controls="{{ $collapseId }}">
                                                            <span>Xem dịch vụ trong combo</span>
                                                            <i class="fa-solid fa-chevron-down combo-service-toggle-icon" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                    <div class="collapse combo-service-collapse" id="{{ $collapseId }}">
                                                        <div class="combo-service-collapse-inner">
                                                            @if (($nhomDichVu->dichVuLe ?? collect())->isNotEmpty())
                                                                <ol class="combo-service-list mb-0">
                                                                    @foreach ($nhomDichVu->dichVuLe as $index => $dichVu)
                                                                        <li>
                                                                            <span class="combo-service-list-index">{{ $index + 1 }}.</span>
                                                                            <span>{{ $dichVu->ten_dich_vu }}</span>
                                                                            @if (!empty($dichVu->ma_dich_vu))
                                                                                <small class="text-muted">({{ $dichVu->ma_dich_vu }})</small>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ol>
                                                            @else
                                                                <p class="combo-service-empty mb-0">Combo chưa có dịch vụ nào.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-0">
                                                Chưa có nhóm dịch vụ hiển thị. Vui lòng tạo nhóm dịch vụ trước khi chọn combo.
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                                <div id="wizard-pagination-combo" class="wizard-list-pagination" data-wizard-pagination="combo" hidden></div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="wizard-service-le" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="wizard-filter-dich-vu-le" placeholder="Nhập tên, mô tả hoặc mã dịch vụ..." autocomplete="off">
                                </div>
                                <label class="form-label">Danh sách dịch vụ lẻ</label>
                                <div class="dich-vu-le-list" id="wizard-dich-vu-le-list">
                                    @forelse ($dichVuLes ?? [] as $dvLe)
                                        @php
                                            $giaLe = $dvLe->gia_dich_vu;
                                            $giaSoLe = is_numeric($giaLe) ? (float) $giaLe : 0;
                                            $giaFmtLe = number_format($giaSoLe, 0, ',', '.');
                                            $searchLe = mb_strtolower(trim(($dvLe->ten_dich_vu ?? '') . ' ' . ($dvLe->mo_ta ?? '') . ' ' . ($dvLe->ma_dich_vu ?? '')));
                                        @endphp
                                        <label class="dich-vu-le-item js-dich-vu-le-item" for="dv_le_{{ $dvLe->id }}" data-search-text="{{ $searchLe }}">
                                            <input
                                                class="form-check-input js-dich-vu-le"
                                                type="checkbox"
                                                id="dv_le_{{ $dvLe->id }}"
                                                value="{{ $dvLe->id }}"
                                                data-id="{{ $dvLe->id }}"
                                                data-ten="{{ $dvLe->ten_dich_vu }}"
                                                data-ma="{{ $dvLe->ma_dich_vu ?? '' }}"
                                                data-gia-goc="{{ $giaSoLe }}">
                                            <span class="dich-vu-le-main">
                                                <span class="dich-vu-le-content">
                                                    <span class="dich-vu-le-title">{{ $dvLe->ten_dich_vu }}</span>
                                                    @if (!empty($dvLe->mo_ta))
                                                        <span class="dich-vu-le-sub">{{ $dvLe->mo_ta }}</span>
                                                    @elseif (!empty($dvLe->ma_dich_vu))
                                                        <span class="dich-vu-le-sub text-muted">{{ $dvLe->ma_dich_vu }}</span>
                                                    @endif
                                                </span>
                                                <span class="dich-vu-le-price">{{ $giaFmtLe }}đ</span>
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-muted small mb-0">Chưa có dịch vụ lẻ hiển thị. Vui lòng thêm dịch vụ lẻ (trạng thái hiển thị) trong quản trị.</p>
                                    @endforelse
                                </div>
                                <div id="wizard-pagination-dich-vu-le" class="wizard-list-pagination" data-wizard-pagination="dich-vu-le" hidden></div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive wizard-dich-vu-table-wrap">
                                    <table class="table table-sm table-bordered align-middle mb-0 wizard-dich-vu-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">STT</th>
                                                <th>Tên dịch vụ</th>
                                                <th>Mã dịch vụ</th>
                                                <th class="text-end">Giá gốc</th>
                                                <th class="text-center">Số lượng</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody id="wizard-table-dich-vu-le-body">
                                            <tr class="table-empty-row">
                                                <td colspan="6" class="text-center text-muted py-3">Chưa chọn dịch vụ lẻ nào.</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-end">Tổng dịch vụ lẻ</th>
                                                <th class="text-end" id="wizard-dich-vu-le-total">0đ</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="wizard-service-nang-cap" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Combo chính</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="wizard-filter-combo-nang-cap" placeholder="Nhập tên combo, thẻ hoặc mô tả..." autocomplete="off">
                                </div>
                                <div class="row g-3" id="wizard-combo-nang-cap-list">
                                    @forelse ($nhomDichVus ?? [] as $nhomDichVu)
                                        @php
                                            $isChecked = (string) old('combo_goi') === (string) $nhomDichVu->id;
                                            $giaTien = is_numeric($nhomDichVu->gia_tien) ? number_format((float) $nhomDichVu->gia_tien, 0, ',', '.') : null;
                                            $radioIdNc = 'wizard_combo_nang_cap_' . $nhomDichVu->id;
                                        @endphp
                                        <div
                                            class="col-12 col-md-6 col-xl-4 js-combo-item-nang-cap"
                                            data-search-text="{{ mb_strtolower(trim(($nhomDichVu->ten_nhom ?? '') . ' ' . ($nhomDichVu->the ?? '') . ' ' . ($nhomDichVu->mo_ta ?? '') . ' ' . ($nhomDichVu->ghi_chu ?? ''))) }}">
                                            <div class="combo-service-card h-100 w-100">
                                                <input
                                                    class="combo-service-radio"
                                                    type="radio"
                                                    name="combo_goi"
                                                    id="{{ $radioIdNc }}"
                                                    value="{{ $nhomDichVu->id }}"
                                                    data-gia="{{ is_numeric($nhomDichVu->gia_tien) ? (float) $nhomDichVu->gia_tien : 0 }}"
                                                    @checked($isChecked)>
                                                <div class="combo-service-card-body">
                                                    <label class="combo-service-selectable" for="{{ $radioIdNc }}">
                                                        <span class="combo-service-card-head">
                                                            <span class="combo-service-title-wrap">
                                                                <input class="form-check-input combo-service-checkbox" type="checkbox" tabindex="-1" aria-hidden="true" @checked($isChecked) disabled>
                                                                <span class="combo-service-title">{{ $nhomDichVu->ten_nhom }}</span>
                                                            </span>
                                                            @if ($giaTien)
                                                                <span class="combo-service-price">{{ $giaTien }}đ</span>
                                                            @endif
                                                        </span>
                                                        <span class="combo-service-meta">
                                                            {{ $nhomDichVu->dich_vu_le_count ?? 0 }} dịch vụ
                                                            @if (!empty($nhomDichVu->the))
                                                                • {{ $nhomDichVu->the }}
                                                            @endif
                                                        </span>
                                                        @if (!empty($nhomDichVu->mo_ta))
                                                            <span class="combo-service-desc">{{ $nhomDichVu->mo_ta }}</span>
                                                        @elseif (!empty($nhomDichVu->ghi_chu))
                                                            <span class="combo-service-desc">{{ $nhomDichVu->ghi_chu }}</span>
                                                        @else
                                                            <span class="combo-service-desc text-muted">-</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-0">
                                                Chưa có nhóm dịch vụ hiển thị. Vui lòng tạo nhóm dịch vụ trước khi chọn combo.
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                                <div id="wizard-pagination-combo-nang-cap" class="wizard-list-pagination" data-wizard-pagination="combo-nang-cap" hidden></div>
                                <div class="mt-3 d-none" id="wizard-nang-cap-combo-dich-vu-outer">
                                    <div class="border rounded p-3 bg-light">
                                        {{-- <label class="form-label mb-1">Dịch vụ trong combo đã chọn</label> --}}
                                        <p class="text-muted small mb-3">Giữ chọn các hạng mục khách sẽ dùng; bỏ chọn nếu không sử dụng.</p>
                                        @foreach ($nhomDichVus ?? [] as $nhomDvNc)
                                            <div class="js-nang-cap-combo-dich-vu-group d-none pt-3" data-nhom-dich-vu-id="{{ $nhomDvNc->id }}">
                                                @if (($nhomDvNc->dichVuLe ?? collect())->isNotEmpty())
                                                    @foreach ($nhomDvNc->dichVuLe as $dichVuNc)
                                                        <div class="form-check mb-2">
                                                            <input
                                                                class="form-check-input js-combo-nang-cap-dich-vu"
                                                                type="checkbox"
                                                                name="dich_vu_trong_combo_nang_cap[]"
                                                                id="nc_combo_{{ $nhomDvNc->id }}_dv_{{ $dichVuNc->id }}"
                                                                value="{{ $dichVuNc->id }}"
                                                                checked
                                                                disabled>
                                                            <label class="form-check-label" for="nc_combo_{{ $nhomDvNc->id }}_dv_{{ $dichVuNc->id }}">
                                                                {{ $dichVuNc->ten_dich_vu }}
                                                                @if (!empty($dichVuNc->ma_dich_vu))
                                                                    <span class="text-muted">({{ $dichVuNc->ma_dich_vu }})</span>
                                                                @endif
                                                                @if (($dichVuNc->pivot->so_luong ?? 1) > 1)
                                                                    <span class="text-muted">× {{ (int) $dichVuNc->pivot->so_luong }}</span>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted small mb-0">Combo này chưa gán dịch vụ.</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Dịch vụ lẻ nâng cấp</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="wizard-filter-dich-vu-le-nang-cap" placeholder="Nhập tên, mô tả hoặc mã dịch vụ..." autocomplete="off">
                                </div>
                                <div class="dich-vu-le-list" id="wizard-dich-vu-le-nang-cap-list">
                                    @forelse ($dichVuLes ?? [] as $dvNcList)
                                        @php
                                            $giaNcList = $dvNcList->gia_dich_vu;
                                            $giaSoNcList = is_numeric($giaNcList) ? (float) $giaNcList : 0;
                                            $giaFmtNcList = number_format($giaSoNcList, 0, ',', '.');
                                            $searchNcList = mb_strtolower(trim(($dvNcList->ten_dich_vu ?? '') . ' ' . ($dvNcList->mo_ta ?? '') . ' ' . ($dvNcList->ma_dich_vu ?? '')));
                                        @endphp
                                        <label class="dich-vu-le-item js-dich-vu-le-nang-cap-item" for="dv_nc_{{ $dvNcList->id }}" data-search-text="{{ $searchNcList }}">
                                            <input
                                                class="form-check-input js-dich-vu-le-nang-cap"
                                                type="checkbox"
                                                id="dv_nc_{{ $dvNcList->id }}"
                                                value="{{ $dvNcList->id }}"
                                                data-id="{{ $dvNcList->id }}"
                                                data-ten="{{ $dvNcList->ten_dich_vu }}"
                                                data-ma="{{ $dvNcList->ma_dich_vu ?? '' }}"
                                                data-gia-goc="{{ $giaSoNcList }}">
                                            <span class="dich-vu-le-main">
                                                <span class="dich-vu-le-content">
                                                    <span class="dich-vu-le-title">{{ $dvNcList->ten_dich_vu }}</span>
                                                    @if (!empty($dvNcList->mo_ta))
                                                        <span class="dich-vu-le-sub">{{ $dvNcList->mo_ta }}</span>
                                                    @elseif (!empty($dvNcList->ma_dich_vu))
                                                        <span class="dich-vu-le-sub text-muted">{{ $dvNcList->ma_dich_vu }}</span>
                                                    @endif
                                                </span>
                                                <span class="dich-vu-le-price">{{ $giaFmtNcList }}đ</span>
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-muted small mb-0">Chưa có dịch vụ lẻ hiển thị. Vui lòng thêm dịch vụ lẻ (trạng thái hiển thị) trong quản trị.</p>
                                    @endforelse
                                </div>
                                <div id="wizard-pagination-dich-vu-le-nang-cap" class="wizard-list-pagination" data-wizard-pagination="dich-vu-le-nang-cap" hidden></div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive wizard-dich-vu-table-wrap">
                                    <table class="table table-sm table-bordered align-middle mb-0 wizard-dich-vu-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 56px;">STT</th>
                                                <th>Tên dịch vụ</th>
                                                <th>Mã dịch vụ</th>
                                                <th class="text-end">Giá gốc</th>
                                                <th class="text-center">Số lượng</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody id="wizard-table-dich-vu-le-nang-cap-body">
                                            <tr class="table-empty-row">
                                                <td colspan="6" class="text-center text-muted py-3">Chưa chọn dịch vụ lẻ nâng cấp nào.</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-end">Tổng dịch vụ lẻ nâng cấp</th>
                                                <th class="text-end" id="wizard-dich-vu-le-nang-cap-total">0đ</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12 col-lg-4">
                        <label class="form-label" for="wizard_tong_tien_dich_vu_hien_thi">Tổng số tiền dịch vụ</label>
                        <input type="text" readonly class="form-control" id="wizard_tong_tien_dich_vu_hien_thi" value="0đ">
                    </div>
                </div>
            </div>

            {{-- Bước 3: Xác nhận & thanh toán --}}
            <div class="wizard-panel" data-wizard-panel="3" role="tabpanel" hidden>
                <h6 class="text-primary mb-3"><i class="fa-solid fa-money-bill-wave me-2"></i>Bước 3 — Xác nhận thanh toán</h6>
                <p class="text-muted small mb-3">Kiểm tra thông tin khách và nhập số liệu thanh toán trước khi lưu.</p>
                <div id="wizard-step3-errors" class="alert alert-danger d-none mb-3" role="alert"></div>

                <div class="border rounded p-3 mb-4 bg-light">
                    <h6 class="small text-uppercase text-muted mb-3">Tóm tắt — Thông tin khách</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0" id="wizard-summary">
                            <tbody>
                                <tr>
                                    <th class="table-light wizard-summary-label">Mã HĐ</th>
                                    <td data-sum="ma_hop_dong">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">Loại hợp đồng</th>
                                    <td data-sum="loai_hop_dong">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">Chú rể / Cô dâu</th>
                                    <td data-sum="ten_cap">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">SĐT chú rể</th>
                                    <td data-sum="email_sdt_chu_re">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">SĐT cô dâu</th>
                                    <td data-sum="email_sdt_co_dau">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">Kênh tiếp cận</th>
                                    <td data-sum="kenh_tiep_can">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">Thành viên sale</th>
                                    <td data-sum="thanh_vien_sale">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">Yêu cầu đặc biệt</th>
                                    <td data-sum="yeu_cau_dac_biet">—</td>
                                </tr>
                                <tr>
                                    <th class="table-light wizard-summary-label">Dịch vụ đã chọn</th>
                                    <td data-sum="dich_vu_da_chon">—</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-lg-4">
                        <label class="form-label" for="wizard_concept">Concept</label>
                        <select class="form-select" id="wizard_concept" name="concept_id" data-placeholder="Chọn concept">
                            <option value="">-- Chọn concept --</option>
                            @foreach ($concepts ?? [] as $concept)
                                <option value="{{ $concept->id }}" @selected((string) old('concept_id', $hopDongCuoi->concept_id) === (string) $concept->id)>
                                    {{ $concept->ten_concept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-8">
                        <label class="form-label" for="wizard_trang_phuc">Trang phục</label>
                        <select class="form-select" id="wizard_trang_phuc" name="trang_phuc[]" multiple data-placeholder="Chọn trang phục">
                            @php
                                $trangPhucDaChon = collect(old('trang_phuc', $hopDongCuoiData['trang_phuc_ids'] ?? []))
                                    ->map(fn ($value) => (string) $value)
                                    ->all();
                            @endphp
                            @foreach ($trangPhucs ?? [] as $trangPhuc)
                                @php
                                    $tenTrangPhuc = trim(($trangPhuc->ten_san_pham ?? '') . (!empty($trangPhuc->ma_san_pham) ? ' (' . $trangPhuc->ma_san_pham . ')' : ''));
                                @endphp
                                <option value="{{ $trangPhuc->id }}" @selected(in_array((string) $trangPhuc->id, $trangPhucDaChon, true))>
                                    {{ $tenTrangPhuc !== '' ? $tenTrangPhuc : ('Trang phục #' . $trangPhuc->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_tong_tien_hien_thi">Tổng tiền (VNĐ)</label>
                        <input type="hidden" id="wizard_tong_tien" name="tong_tien" value="{{ old('tong_tien', $hopDongCuoi->tong_tien) }}">
                        <input type="text" readonly class="form-control text-end bg-body-secondary" id="wizard_tong_tien_hien_thi" value="{{ number_format((float) old('tong_tien', $hopDongCuoi->tong_tien), 0, ',', '.') }}" inputmode="numeric" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_chiet_khau_hien_thi">Chiết khấu (VNĐ)</label>
                        <input type="hidden" id="wizard_chiet_khau" name="chiet_khau" value="{{ old('chiet_khau', $hopDongCuoi->chiet_khau) }}">
                        <input type="text" class="form-control text-end" id="wizard_chiet_khau_hien_thi" placeholder="0" value="{{ number_format((float) old('chiet_khau', $hopDongCuoi->chiet_khau), 0, ',', '.') }}" inputmode="numeric" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_ma_giam_gia">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="wizard_ma_giam_gia" name="ma_giam_gia" value="{{ old('ma_giam_gia') }}" placeholder="Nhập mã hợp đồng hoàn thành">
                            <button type="button" class="btn btn-outline-primary" id="wizard_btn_kiem_tra_ma_giam_gia" title="Kiểm tra mã giảm giá">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1" id="wizard_ma_giam_gia_msg"></small>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_so_tien_giam_gia">Số tiền giảm giá (VNĐ)</label>
                        <input type="text" readonly class="form-control text-end bg-body-secondary" id="wizard_so_tien_giam_gia" name="so_tien_giam_gia_hien_thi" value="{{ number_format((float) old('so_tien_giam_gia_hien_thi', 0), 0, ',', '.') }}" inputmode="numeric" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_so_tien_phai_tra">Số tiền phải trả (VNĐ)</label>
                        <input type="text" readonly class="form-control text-end bg-body-secondary" id="wizard_so_tien_phai_tra" name="so_tien_phai_tra_hien_thi" value="{{ number_format((float) old('so_tien_phai_tra_hien_thi', 0), 0, ',', '.') }}" inputmode="numeric" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_tien_coc_hien_thi">Tiền cọc (VNĐ)</label>
                        <input type="hidden" id="wizard_tien_coc" name="tien_coc" value="{{ old('tien_coc', $hopDongCuoi->tien_coc) }}">
                        <input type="text" class="form-control text-end" id="wizard_tien_coc_hien_thi" placeholder="0" value="{{ number_format((float) old('tien_coc', $hopDongCuoi->tien_coc), 0, ',', '.') }}" inputmode="numeric" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_hinh_thuc_coc">Hình thức cọc</label>
                        <select class="select2-admin form-select" id="wizard_hinh_thuc_coc" name="hinh_thuc_coc" data-placeholder="Chọn hình thức cọc" required>
                            <option value="">-- Chọn hình thức cọc --</option>
                            @foreach (\App\Models\HopDongCuoi::HINH_THUC_COC as $value => $label)
                                <option value="{{ $value }}" @selected((string) old('hinh_thuc_coc', $hopDongCuoi->hinh_thuc_coc) === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_han_thanh_toan_lan2">Hạn thanh toán lần 2 (dự kiến)</label>
                        <input type="text" readonly tabindex="-1" class="form-control bg-body-secondary" id="wizard_han_thanh_toan_lan2" name="han_thanh_toan_lan2" value="{{ old('han_thanh_toan_lan2', optional($hopDongCuoi->han_thanh_toan_lan2)->format('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_han_thanh_toan_lan3">Hạn thanh toán lần 3 (dự kiến)</label>
                        <input type="text" readonly tabindex="-1" class="form-control bg-body-secondary" id="wizard_han_thanh_toan_lan3" name="han_thanh_toan_lan3" value="{{ old('han_thanh_toan_lan3', optional($hopDongCuoi->han_thanh_toan_lan3)->format('Y-m-d')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label class="form-label" for="wizard_ngay_ky_hop_dong">Ngày ký hợp đồng</label>
                        <input type="text" readonly class="form-control" id="wizard_ngay_ky_hop_dong" name="ngay_ky_hop_dong" value="{{ old('ngay_ky_hop_dong', optional($hopDongCuoi->ngay_ky_hop_dong)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="wizard_dong_y" name="dong_y" value="1">
                    <label class="form-check-label" for="wizard_dong_y">Tôi xác nhận thông tin đã nhập là chính xác</label>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" id="wizard-btn-prev" disabled>
                    <i class="fa-solid fa-chevron-left me-1"></i> Quay lại
                </button>
                <div class="text-muted small" id="wizard-step-hint">Bước 1 / 3</div>
                <button type="button" class="btn btn-primary" id="wizard-btn-next" disabled title="Vui lòng điền đủ thông tin bước 1">
                    Tiếp tục <i class="fa-solid fa-chevron-right ms-1"></i>
                </button>
                <button type="submit" class="btn btn-success d-none" id="wizard-btn-submit" disabled title="Vui lòng tick xác nhận">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Hoàn tất &amp; lưu hợp đồng
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal xác nhận huỷ hợp đồng --}}
<div class="modal fade" id="modalXacNhanHuyHopDongCuoi" tabindex="-1" aria-labelledby="modalXacNhanHuyHopDongCuoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanHuyHopDongCuoiLabel">Xác nhận huỷ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn huỷ hợp đồng này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanHuyHopDongCuoi">
                    <i class="fa-solid fa-ban me-1"></i> Huỷ hợp đồng
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<style>
.tao-hop-dong-wizard .wizard-steps {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: nowrap;
    gap: 0;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    -webkit-overflow-scrolling: touch;
}
.tao-hop-dong-wizard .wizard-step {
    flex: 0 0 auto;
    text-align: center;
    min-width: 72px;
}
.tao-hop-dong-wizard .wizard-step-btn {
    border: none;
    background: transparent;
    padding: 0.25rem 0.5rem;
    cursor: pointer;
    color: inherit;
    width: 100%;
    border-radius: 0.5rem;
    transition: background 0.15s ease;
}
.tao-hop-dong-wizard .wizard-step-btn:hover {
    background: rgba(var(--bs-primary-rgb), 0.08);
}
.tao-hop-dong-wizard .wizard-step-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.35rem;
    border: 2px solid var(--bs-border-color);
    color: var(--bs-secondary-color);
    background: var(--bs-body-bg);
}
.tao-hop-dong-wizard .wizard-step.active .wizard-step-num {
    border-color: var(--bs-primary);
    background: var(--bs-primary);
    color: #fff;
}
.tao-hop-dong-wizard .wizard-step.done .wizard-step-num {
    border-color: var(--bs-success);
    background: var(--bs-success);
    color: #fff;
}
.tao-hop-dong-wizard .wizard-step-label {
    display: block;
    font-size: 0.7rem;
    line-height: 1.2;
    max-width: 100px;
    margin: 0 auto;
    color: var(--bs-secondary-color);
}
@media (min-width: 768px) {
    .tao-hop-dong-wizard .wizard-step-label {
        font-size: 0.75rem;
        max-width: 130px;
    }
}
.tao-hop-dong-wizard .wizard-step.active .wizard-step-label {
    color: var(--bs-primary);
    font-weight: 600;
}
.tao-hop-dong-wizard .wizard-step-line {
    flex: 1 1 12px;
    min-width: 8px;
    height: 2px;
    background: var(--bs-border-color);
    margin-top: 1.1rem;
    align-self: flex-start;
}
.tao-hop-dong-wizard .wizard-panel {
    display: none;
    animation: wizardFadeIn 0.2s ease;
}
.tao-hop-dong-wizard .wizard-panel.active {
    display: block;
}
@keyframes wizardFadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
.tao-hop-dong-wizard .wizard-step1-grid .form-label {
    font-size: 14px;
    margin-bottom: 0.25rem;
}
.tao-hop-dong-wizard .wizard-field-stt {
    font-weight: 600;
    margin-right: 0.25rem;
}
/* Bước 3 — cột nhãn tóm tắt: giữ một dòng, không bị co trên mobile */
.tao-hop-dong-wizard #wizard-summary th.wizard-summary-label {
    white-space: nowrap;
    width: 1%;
    vertical-align: top;
}
@media (min-width: 768px) {
    .tao-hop-dong-wizard #wizard-summary th.wizard-summary-label {
        width: 220px;
    }
}
@media (min-width: 1200px) {
    .tao-hop-dong-wizard .wizard-step1-grid .form-control,
    .tao-hop-dong-wizard .wizard-step1-grid .form-select {
        font-size: 14px;
    }
}
/* Select2 (giống demo / hop-dong): full width trong card wizard */
.tao-hop-dong-wizard .select2-container {
    width: 100% !important;
}
.tao-hop-dong-wizard #wizard-service-tab-content .tab-pane {
}
.tao-hop-dong-wizard .wizard-service-pills .nav-link {
    border: 1px solid var(--bs-border-color);
    color: var(--bs-body-color);
    font-weight: 600;
    padding: 0.6rem 0.75rem;
    transition: all 0.15s ease;
}
.tao-hop-dong-wizard .wizard-service-pills .nav-link i {
    font-size: 0.9rem;
    opacity: 0.9;
}
.tao-hop-dong-wizard .wizard-service-pills .nav-link:hover {
    border-color: rgba(var(--bs-primary-rgb), 0.45);
    color: var(--bs-primary);
}
.tao-hop-dong-wizard .wizard-service-pills .nav-link.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: #fff;
    box-shadow: 0 0.35rem 0.85rem rgba(var(--bs-primary-rgb), 0.28);
}
.tao-hop-dong-wizard .wizard-service-pills .nav-link.active i {
    opacity: 1;
}
.tao-hop-dong-wizard .combo-service-card {
    display: block;
    position: relative;
}
.tao-hop-dong-wizard .combo-service-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.tao-hop-dong-wizard .combo-service-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    height: 100%;
    border: 1px solid var(--bs-border-color);
    border-radius: 0.75rem;
    padding: 14px;
    background: var(--bs-body-bg);
    transition: all 0.15s ease;
}
.tao-hop-dong-wizard .combo-service-selectable {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    cursor: pointer;
}
.tao-hop-dong-wizard .combo-service-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
}
.tao-hop-dong-wizard .combo-service-title-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.tao-hop-dong-wizard .combo-service-checkbox {
    margin-top: 0.1rem;
}
.tao-hop-dong-wizard .combo-service-title {
    font-weight: 600;
    color: var(--bs-body-color);
}
.tao-hop-dong-wizard .combo-service-price {
    color: var(--bs-primary);
    font-weight: 700;
    white-space: nowrap;
}
.tao-hop-dong-wizard .combo-service-meta {
    font-size: 14px;
    color: var(--bs-secondary-color);
}
.tao-hop-dong-wizard .combo-service-desc {
    font-size: 14px;
    color: var(--bs-body-color);
    opacity: 0.85;
    min-height: 1.25rem;
}
.tao-hop-dong-wizard .combo-service-card:hover .combo-service-card-body {
    border-color: rgba(var(--bs-primary-rgb), 0.55);
    box-shadow: 0 0.25rem 0.75rem rgba(var(--bs-primary-rgb), 0.12);
}
.tao-hop-dong-wizard .combo-service-radio:checked + .combo-service-card-body {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.07);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.12);
}
.tao-hop-dong-wizard .combo-service-radio:checked + .combo-service-card-body .combo-service-checkbox {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}
.tao-hop-dong-wizard .combo-service-footer {
    margin-top: 0.25rem;
}
.tao-hop-dong-wizard .combo-service-toggle {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    background: transparent;
    border: 0;
    padding: 0.2rem 0;
    color: var(--bs-primary);
    font-size: 0.825rem;
    font-weight: 600;
}
.tao-hop-dong-wizard .combo-service-toggle-icon {
    transition: transform 0.15s ease;
}
.tao-hop-dong-wizard .combo-service-toggle[aria-expanded="true"] .combo-service-toggle-icon {
    transform: rotate(180deg);
}
.tao-hop-dong-wizard .combo-service-collapse-inner {
    margin-top: 0.25rem;
    border-top: 1px dashed var(--bs-border-color);
    padding-top: 0.5rem;
}
.tao-hop-dong-wizard .combo-service-list {
    list-style: none;
    padding-left: 0;
    margin: 0;
    font-size: 0.825rem;
}
.tao-hop-dong-wizard .combo-service-list li {
    display: flex;
    align-items: baseline;
    gap: 0.3rem;
}
.tao-hop-dong-wizard .combo-service-list-index {
    min-width: 1.35rem;
    color: var(--bs-secondary-color);
    font-weight: 600;
}
.tao-hop-dong-wizard .combo-service-list li + li {
    margin-top: 0.2rem;
}
.tao-hop-dong-wizard .combo-service-empty {
    font-size: 14px;
    color: var(--bs-secondary-color);
}
.tao-hop-dong-wizard .dich-vu-le-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.6rem;
}
@media (min-width: 768px) {
    .tao-hop-dong-wizard .dich-vu-le-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
.tao-hop-dong-wizard .dich-vu-le-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    border: 1px solid var(--bs-border-color);
    border-radius: 0.6rem;
    padding: 0.6rem 0.75rem;
    background: var(--bs-body-bg);
    cursor: pointer;
    transition: all 0.15s ease;
}
.tao-hop-dong-wizard .dich-vu-le-item:hover {
    border-color: rgba(var(--bs-primary-rgb), 0.45);
}
.tao-hop-dong-wizard .dich-vu-le-item .form-check-input {
    margin-top: 0;
    flex: 0 0 auto;
}
.tao-hop-dong-wizard .dich-vu-le-main {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
}
.tao-hop-dong-wizard .dich-vu-le-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.tao-hop-dong-wizard .dich-vu-le-title {
    font-weight: 600;
    font-size: 0.9rem;
}
.tao-hop-dong-wizard .dich-vu-le-sub {
    color: var(--bs-secondary-color);
    font-size: 0.76rem;
    line-height: 1.25;
}
.tao-hop-dong-wizard .dich-vu-le-price {
    color: #6b74d6;
    font-weight: 700;
    font-size: 0.9rem;
    white-space: nowrap;
}
.tao-hop-dong-wizard .dich-vu-le-item.is-checked {
    border-color: rgba(var(--bs-primary-rgb), 0.7);
    background: rgba(var(--bs-primary-rgb), 0.08);
}
.tao-hop-dong-wizard #wizard-table-dich-vu-le-body .form-control {
    min-width: 96px;
}
.tao-hop-dong-wizard .wizard-dich-vu-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.tao-hop-dong-wizard .wizard-dich-vu-table {
    margin-bottom: 0;
    width: 100%;
    min-width: 700px;
    table-layout: fixed;
}
.tao-hop-dong-wizard .wizard-dich-vu-table thead th,
.tao-hop-dong-wizard .wizard-dich-vu-table tfoot th {
    white-space: nowrap;
}
.tao-hop-dong-wizard .wizard-page-hidden {
    display: none !important;
}
.tao-hop-dong-wizard .wizard-list-pagination .pagination {
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Huỷ hợp đồng: mở modal xác nhận, sau đó submit form
    var btnMoModalHuy = document.getElementById('btnMoModalHuyHopDongCuoi');
    var modalHuy = document.getElementById('modalXacNhanHuyHopDongCuoi');
    var btnXacNhanHuy = document.getElementById('btnXacNhanHuyHopDongCuoi');
    var formHuy = document.getElementById('formHuyHopDongCuoi');
    if (btnMoModalHuy && modalHuy && btnXacNhanHuy && formHuy) {
        modalHuy.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        btnMoModalHuy.addEventListener('click', function() {
            var modal = bootstrap.Modal.getOrCreateInstance(modalHuy);
            modal.show();
        });
        btnXacNhanHuy.addEventListener('click', function() {
            formHuy.submit();
            var inst = bootstrap.Modal.getInstance(modalHuy);
            if (inst) inst.hide();
        });
    }

    var URL_KIEM_TRA_MA_GIAM_GIA = @json(route('admin.khach-hang.tao-hop-dong.kiem-tra-ma-giam-gia'));
    var THANH_VIEN_SALE_LABELS = @json(
        collect($danhSachNhanVien ?? [])->mapWithKeys(fn ($nv) => [
            (string) $nv->id => $nv->user?->name ?? ('Nhân viên #' . $nv->id),
        ])
    );
    var totalSteps = 3;
    var currentStep = 1;
    var panels = document.querySelectorAll('[data-wizard-panel]');
    var indicators = document.querySelectorAll('[data-wizard-step-indicator]');
    var btnPrev = document.getElementById('wizard-btn-prev');
    var btnNext = document.getElementById('wizard-btn-next');
    var btnSubmit = document.getElementById('wizard-btn-submit');
    var hint = document.getElementById('wizard-step-hint');
    var panel1 = document.querySelector('[data-wizard-panel="1"]');
    var panel2 = document.querySelector('[data-wizard-panel="2"]');
    var panel3 = document.querySelector('[data-wizard-panel="3"]');
    var formWizard = document.getElementById('formTaoHopDongWizard');
    var chkDongY = document.getElementById('wizard_dong_y');
    var maGiamGiaInput = document.getElementById('wizard_ma_giam_gia');
    var btnKiemTraMaGiamGia = document.getElementById('wizard_btn_kiem_tra_ma_giam_gia');
    var maGiamGiaMsg = document.getElementById('wizard_ma_giam_gia_msg');
    var step2Select2Ready = false;
    var step2RestoreApplied = false;
    var step3Select2Ready = false;
    var step3ConceptRestoreApplied = false;
    var discountVoucherValid = false;

    /** Bỏ đánh số trước label; chỉ đánh dấu (*) đỏ đậm cho các field bắt buộc. */
    function applyWizardFieldLabelNumbers() {
        if (!formWizard) return;
        formWizard.querySelectorAll('.wizard-panel').forEach(function(panel) {
            panel.querySelectorAll('label.form-label[for]').forEach(function(label) {
                var id = label.getAttribute('for');
                if (!id) return;
                var el = document.getElementById(id);
                if (!el || !panel.contains(el)) return;
                /* Flatpickr (altInput): input gốc giữ id nhưng bị đặt type=hidden; ô hiển thị là altInput. */
                if (el.type === 'hidden' && !(el._flatpickr && el._flatpickr.altInput)) return;
                var tag = el.tagName;
                if (tag !== 'INPUT' && tag !== 'SELECT' && tag !== 'TEXTAREA') return;

                var prev = label.querySelector('.wizard-field-stt');
                if (prev) prev.remove();
                var prevStar = label.querySelector('.wizard-required-star');
                if (prevStar) prevStar.remove();

                var isReq =
                    el.hasAttribute('required') ||
                    el.hasAttribute('data-wizard-step1-required');

                if (isReq) {
                    var star = document.createElement('span');
                    star.className = 'wizard-required-star text-danger fw-bold';
                    star.textContent = ' *';
                    label.appendChild(star);
                }
            });
        });
    }

    /** Bước 2 ban đầu ẩn — khởi tạo Select2 sau khi panel hiện (tránh width = 0), cùng tùy chọn với layout .select2-admin */
    function ensureStep2Select2() {
        var $ = window.jQuery;
        if (!$ || !$.fn.select2 || step2Select2Ready) return;
        var $targets = $('[data-wizard-panel="2"]').find('select.form-select');
        function baseOpts($el) {
            return {
                placeholder: $el.data('placeholder') || 'Chọn...',
                allowClear: true,
                width: '100%'
            };
        }
        $targets.each(function() {
            var $el = $(this);
            if ($el.data('select2')) return;
            var opts = baseOpts($el);
            if ($el.prop('multiple')) opts.closeOnSelect = false;
            $el.select2(opts);
        });
        step2Select2Ready = true;
        if (currentStep === 2) updateNextButtonState();
    }

    /** Bước 3 ban đầu ẩn — khởi tạo Select2 Concept / Trang phục / Hình thức cọc sau khi panel hiện. */
    function ensureStep3Select2() {
        var $ = window.jQuery;
        if (!$ || !$.fn.select2 || step3Select2Ready) return;
        $('#wizard_concept, #wizard_trang_phuc, #wizard_hinh_thuc_coc').each(function() {
            var $el = $(this);
            if ($el.data('select2')) {
                $el.select2('destroy');
            }
            var opts = {
                placeholder: $el.data('placeholder') || 'Chọn...',
                allowClear: true,
                width: '100%'
            };
            if ($el.prop('multiple')) opts.closeOnSelect = false;
            $el.select2(opts);
        });
        step3Select2Ready = true;
    }

    /** Khôi phục Concept / Trang phục lần đầu vào bước 3 (từ DB, không ghi đè khi quay lại bước 3). */
    function applyWizardStep3ConceptRestore() {
        if (!formWizard || step3ConceptRestoreApplied) return;
        step3ConceptRestoreApplied = true;

        var hopRaw = formWizard.getAttribute('data-hop-dong-cuoi') || '{}';
        var hopData = {};
        try {
            hopData = JSON.parse(hopRaw) || {};
        } catch (eHop) {
            hopData = {};
        }

        var conceptEl = document.getElementById('wizard_concept');
        if (conceptEl && !conceptEl.value && hopData.concept_id != null && hopData.concept_id !== '') {
            conceptEl.value = String(hopData.concept_id);
            if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(conceptEl).data('select2')) {
                window.jQuery(conceptEl).val(String(hopData.concept_id)).trigger('change');
            }
        }

        var tpElRestore = document.getElementById('wizard_trang_phuc');
        var tpSelectedCount = tpElRestore ? (tpElRestore.selectedOptions ? tpElRestore.selectedOptions.length : 0) : 0;
        if (tpElRestore && !tpSelectedCount && Array.isArray(hopData.trang_phuc_ids) && hopData.trang_phuc_ids.length) {
            var tpIdsStr = hopData.trang_phuc_ids.map(function(id) { return String(id); });
            if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(tpElRestore).data('select2')) {
                window.jQuery(tpElRestore).val(tpIdsStr).trigger('change');
            } else {
                Array.from(tpElRestore.options).forEach(function(opt) {
                    opt.selected = tpIdsStr.indexOf(opt.value) !== -1;
                });
            }
        }
    }

    function formatMoneyVnd(v) {
        var n = Number(v) || 0;
        return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + 'đ';
    }

    function toNumber(v) {
        var n = Number(v);
        return Number.isFinite(n) ? n : 0;
    }

    function parseMoneyText(v) {
        var s = String(v || '').replace(/[^\d]/g, '');
        if (!s) return 0;
        return toNumber(s);
    }

    function formatMoneyInputNumber(v) {
        return new Intl.NumberFormat('vi-VN').format(Math.max(0, Math.round(toNumber(v))));
    }

    function tinhSoTienGiamTheoRule(tongTien) {
        var t = Math.max(0, toNumber(tongTien));
        if (t > 300000) return 300000;
        return Math.round(t * 0.5);
    }

    function setMaGiamGiaMessage(text, isError) {
        if (!maGiamGiaMsg) return;
        maGiamGiaMsg.textContent = text || '';
        maGiamGiaMsg.classList.toggle('text-danger', !!isError);
        maGiamGiaMsg.classList.toggle('text-success', !isError && !!text);
        maGiamGiaMsg.classList.toggle('text-muted', !text);
    }

    function resetVoucherDiscount(message) {
        discountVoucherValid = false;
        var soTienGiamGiaEl = document.getElementById('wizard_so_tien_giam_gia');
        if (soTienGiamGiaEl) soTienGiamGiaEl.value = '0';
        if (message) setMaGiamGiaMessage(message, true);
        else setMaGiamGiaMessage('', false);
    }

    function buildDichVuLeTable(checkboxSelector, bodyId, totalId, emptyText, fieldPrefix) {
        fieldPrefix = fieldPrefix || 'dich_vu_chon';
        var body = document.getElementById(bodyId);
        var totalEl = document.getElementById(totalId);
        if (!body || !totalEl) return;

        var checked = document.querySelectorAll(checkboxSelector + ':checked');
        if (!checked.length) {
            body.innerHTML = '<tr class="table-empty-row"><td colspan="6" class="text-center text-muted py-3">' + emptyText + '</td></tr>';
            totalEl.textContent = '0đ';
            totalEl.setAttribute('data-total-number', '0');
            updateTongTienDichVu();
            return;
        }

        var html = '';
        checked.forEach(function(chk, idx) {
            var id = chk.dataset.id || String(idx + 1);
            var ten = chk.dataset.ten || '';
            var ma = chk.dataset.ma || '';
            var giaGoc = toNumber(chk.dataset.giaGoc || 0);
            html += '<tr data-dich-vu-id="' + id + '">';
            html += '<td class="text-center">' + (idx + 1) + '</td>';
            html += '<td>' + ten + '<input type="hidden" name="' + fieldPrefix + '[' + id + '][ten]" value="' + ten + '"></td>';
            html += '<td>' + ma + '<input type="hidden" name="' + fieldPrefix + '[' + id + '][ma_dich_vu]" value="' + ma + '"></td>';
            html += '<td class="text-end">' + formatMoneyVnd(giaGoc) + '<input type="hidden" name="' + fieldPrefix + '[' + id + '][gia_goc]" value="' + giaGoc + '"></td>';
            html += '<input type="hidden" name="' + fieldPrefix + '[' + id + '][gia_thuc]" value="' + giaGoc + '">';
            html += '<td><input type="number" class="form-control form-control-sm js-so-luong" min="1" step="1" value="1" name="' + fieldPrefix + '[' + id + '][so_luong]"></td>';
            html += '<td class="text-end js-thanh-tien">' + formatMoneyVnd(giaGoc) + '<input type="hidden" class="js-thanh-tien-input" name="' + fieldPrefix + '[' + id + '][thanh_tien]" value="' + giaGoc + '"></td>';
            html += '</tr>';
        });
        body.innerHTML = html;
        recalcDichVuLeTotal(bodyId, totalId);
    }

    function recalcDichVuLeTotal(bodyId, totalId) {
        var total = 0;
        var body = document.getElementById(bodyId);
        var totalEl = document.getElementById(totalId);
        if (!body || !totalEl) return;
        body.querySelectorAll('tr[data-dich-vu-id]').forEach(function(row) {
            var gia = toNumber(row.querySelector('input[name*="[gia_goc]"]')?.value || 0);
            var qty = Math.max(1, toNumber(row.querySelector('.js-so-luong')?.value || 1));
            var thanhTien = gia * qty;
            var tt = row.querySelector('.js-thanh-tien');
            var ttInput = row.querySelector('.js-thanh-tien-input');
            var giaThucInput = row.querySelector('input[name*="[gia_thuc]"]');
            if (tt) tt.childNodes[0].nodeValue = formatMoneyVnd(thanhTien);
            if (ttInput) ttInput.value = String(thanhTien);
            if (giaThucInput) giaThucInput.value = String(gia);
            total += thanhTien;
        });
        totalEl.textContent = formatMoneyVnd(total);
        totalEl.setAttribute('data-total-number', String(total));
        updateTongTienDichVu();
    }

    function getTotalFromEl(id) {
        var el = document.getElementById(id);
        if (!el) return 0;
        return toNumber(el.getAttribute('data-total-number') || 0);
    }

    function getSelectedComboPrice() {
        var selectedCombo = document.querySelector('.combo-service-radio:checked');
        if (!selectedCombo) return 0;
        return toNumber(selectedCombo.getAttribute('data-gia') || 0);
    }

    function getActiveServiceTabTarget() {
        var activeTabBtn = document.querySelector('.wizard-service-pills .nav-link.active');
        return activeTabBtn ? activeTabBtn.getAttribute('data-bs-target') : '#wizard-service-combo';
    }

    /** Panel từ enum loai_dich_vu (khớp DB hop_dong_cuoi.loai_dich_vu). */
    function serviceTabTargetFromLoai(loai) {
        if (loai === 'ghep_dich_vu_le') return '#wizard-service-le';
        if (loai === 'combo_va_nang_cap') return '#wizard-service-nang-cap';
        return '#wizard-service-combo';
    }

    /** Khớp với enum loai_dich_vu trên hop_dong_cuoi. */
    function getLoaiDichVuFromActiveTab() {
        var t = getActiveServiceTabTarget();
        if (t === serviceTabTargetFromLoai('ghep_dich_vu_le')) return 'ghep_dich_vu_le';
        if (t === serviceTabTargetFromLoai('combo_va_nang_cap')) return 'combo_va_nang_cap';
        return 'combo_tron_goi';
    }

    /** Bỏ chọn mọi combo (hai tab dùng chung name="combo_goi"). */
    function clearAllComboGoiRadios() {
        document.querySelectorAll('input.combo-service-radio[name="combo_goi"]').forEach(function(r) {
            r.checked = false;
        });
    }

    /** Gán trực tiếp pill + pane (tránh lệ thuộc shown.bs.tab / Tab API không cập nhật nút). */
    function forceWizardServiceTabVisual(target) {
        document.querySelectorAll('.wizard-service-pills .nav-link').forEach(function(link) {
            var match = link.getAttribute('data-bs-target') === target;
            link.classList.toggle('active', match);
            link.setAttribute('aria-selected', match ? 'true' : 'false');
        });
        document.querySelectorAll('#wizard-service-tab-content .tab-pane').forEach(function(p) {
            var match = ('#' + p.id) === target;
            p.classList.toggle('show', match);
            p.classList.toggle('active', match);
        });
    }

    /** Bật pill + pane đúng theo loai_dich_vu (đồng bộ với DB). */
    function activateWizardServiceTabForLoai(loai, onDone) {
        onDone = onDone || function() {};
        var target = serviceTabTargetFromLoai(loai);
        var tabTrigger = document.querySelector('.wizard-service-pills .nav-link[data-bs-target="' + target + '"]');
        if (!tabTrigger) {
            window.requestAnimationFrame(function() { onDone(); });
            return;
        }

        forceWizardServiceTabVisual(target);

        try {
            if (window.bootstrap && window.bootstrap.Tab) {
                window.bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
            }
        } catch (eTab) { /* ignore */ }

        window.requestAnimationFrame(function() {
            forceWizardServiceTabVisual(target);
            onDone();
        });
    }

    /**
     * Khôi phục UI bước 2 từ DB (chỉ lần đầu vào bước 2 trong phiên trang, để không ghi đè chỉnh sửa khi quay lại).
     * — Pill active theo loai_dich_vu; combo chọn theo nhom_dich_vu_id trong đúng panel.
     */
    function applyWizardStep2Restore(done) {
        done = done || function() {};
        if (!formWizard || step2RestoreApplied) {
            done();
            return;
        }

        var rawRestore = formWizard.getAttribute('data-wizard-step2-restore') || '';
        var data = {};
        if (rawRestore.trim()) {
            try {
                data = JSON.parse(rawRestore) || {};
            } catch (err) {
                data = {};
            }
        }

        var hopRaw = formWizard.getAttribute('data-hop-dong-cuoi') || '{}';
        var hopData = {};
        try {
            hopData = JSON.parse(hopRaw) || {};
        } catch (eHop) {
            hopData = {};
        }

        if (!data.loai_dich_vu && hopData.loai_dich_vu) {
            data.loai_dich_vu = hopData.loai_dich_vu;
        }
        if (data.nhom_dich_vu_id == null && hopData.nhom_dich_vu_id != null) {
            var hid = Number(hopData.nhom_dich_vu_id);
            data.nhom_dich_vu_id = hid > 0 ? hid : null;
        }

        /** Dịch vụ lẻ trong hop_dong_cuoi_dich_vu_le (HopDongCuoiDichVuLe): ghép lẻ = tab ghép; combo nâng cấp = dịch vụ lẻ thêm. */
        var dvHopRows = hopData.hop_dong_cuoi_dich_vu_le;
        if (Array.isArray(dvHopRows) && dvHopRows.length) {
            var normalizedDv = dvHopRows.map(function(r) {
                return {
                    id: Number(r.id),
                    so_luong: Math.max(1, Number(r.so_luong) || 1)
                };
            });
            if (data.loai_dich_vu === 'ghep_dich_vu_le') {
                data.dich_vu_le = normalizedDv;
            } else if (data.loai_dich_vu === 'combo_va_nang_cap') {
                data.dich_vu_nang_cap = normalizedDv;
            }
        }

        if (data.loai_dich_vu === 'combo_va_nang_cap' && Array.isArray(hopData.combo_dich_vu_checked_ids)) {
            data.combo_dich_vu_checked_ids = hopData.combo_dich_vu_checked_ids.map(Number);
        }

        if (!data || !data.loai_dich_vu) {
            done();
            return;
        }

        step2RestoreApplied = true;

        function afterTab() {
            clearAllComboGoiRadios();
            syncComboCheckedState();

            var nid = data.nhom_dich_vu_id != null && data.nhom_dich_vu_id !== '' ? String(data.nhom_dich_vu_id) : '';

            if (data.loai_dich_vu === 'combo_tron_goi' && nid) {
                if (wizardPaginators.combo) wizardPaginators.combo.ensureSelectorVisible('input.combo-service-radio[value="' + nid + '"]');
                var rCombo = document.querySelector('#wizard-service-combo input.combo-service-radio[value="' + nid + '"]');
                if (rCombo) rCombo.checked = true;
                syncComboCheckedState();
            }
            if (data.loai_dich_vu === 'ghep_dich_vu_le' && data.dich_vu_le && data.dich_vu_le.length) {
                data.dich_vu_le.forEach(function(row) {
                    if (wizardPaginators.dichVuLe) wizardPaginators.dichVuLe.ensureSelectorVisible('.js-dich-vu-le[data-id="' + row.id + '"]');
                    var cb = document.querySelector('#wizard-service-le .js-dich-vu-le[data-id="' + row.id + '"]');
                    if (cb) cb.checked = true;
                });
                syncDichVuLeCheckedState();
                buildDichVuLeTable('.js-dich-vu-le', 'wizard-table-dich-vu-le-body', 'wizard-dich-vu-le-total', 'Chưa chọn dịch vụ lẻ nào.');
                data.dich_vu_le.forEach(function(row) {
                    var tr = document.querySelector('#wizard-table-dich-vu-le-body tr[data-dich-vu-id="' + row.id + '"]');
                    var inp = tr && tr.querySelector('.js-so-luong');
                    if (inp) inp.value = String(row.so_luong);
                });
                recalcDichVuLeTotal('wizard-table-dich-vu-le-body', 'wizard-dich-vu-le-total');
            }
            if (data.loai_dich_vu === 'combo_va_nang_cap') {
                if (nid) {
                    if (wizardPaginators.comboNangCap) wizardPaginators.comboNangCap.ensureSelectorVisible('input.combo-service-radio[value="' + nid + '"]');
                    var rNc = document.querySelector('#wizard-service-nang-cap input.combo-service-radio[value="' + nid + '"]');
                    if (rNc) rNc.checked = true;
                    syncComboCheckedState();
                }
                updateNangCapComboDichVuPanel();
                var checkedSet = {};
                (data.combo_dich_vu_checked_ids || []).forEach(function(id) {
                    checkedSet[String(id)] = true;
                });
                document.querySelectorAll('#wizard-service-nang-cap .js-nang-cap-combo-dich-vu-group:not(.d-none) .js-combo-nang-cap-dich-vu').forEach(function(inp) {
                    inp.checked = !!checkedSet[inp.value];
                });
                if (data.dich_vu_nang_cap && data.dich_vu_nang_cap.length) {
                    data.dich_vu_nang_cap.forEach(function(row) {
                        if (wizardPaginators.dichVuLeNangCap) wizardPaginators.dichVuLeNangCap.ensureSelectorVisible('.js-dich-vu-le-nang-cap[data-id="' + row.id + '"]');
                        var cbNc = document.querySelector('#wizard-service-nang-cap .js-dich-vu-le-nang-cap[data-id="' + row.id + '"]');
                        if (cbNc) cbNc.checked = true;
                    });
                    syncDichVuLeCheckedState();
                    buildDichVuLeTable('.js-dich-vu-le-nang-cap', 'wizard-table-dich-vu-le-nang-cap-body', 'wizard-dich-vu-le-nang-cap-total', 'Chưa chọn dịch vụ lẻ nâng cấp nào.', 'dich_vu_nang_cap');
                    data.dich_vu_nang_cap.forEach(function(row) {
                        var trNc = document.querySelector('#wizard-table-dich-vu-le-nang-cap-body tr[data-dich-vu-id="' + row.id + '"]');
                        var inpNc = trNc && trNc.querySelector('.js-so-luong');
                        if (inpNc) inpNc.value = String(row.so_luong);
                    });
                    recalcDichVuLeTotal('wizard-table-dich-vu-le-nang-cap-body', 'wizard-dich-vu-le-nang-cap-total');
                }
            }
            updateTongTienDichVu();
            updateNextButtonState();
            done();
        }

        activateWizardServiceTabForLoai(data.loai_dich_vu, afterTab);
    }

    function updateNangCapComboDichVuPanel() {
        var outer = document.getElementById('wizard-nang-cap-combo-dich-vu-outer');
        var activeTarget = getActiveServiceTabTarget();
        var selected = document.querySelector('.combo-service-radio:checked');
        document.querySelectorAll('.js-nang-cap-combo-dich-vu-group').forEach(function(g) {
            g.classList.add('d-none');
            g.querySelectorAll('input.js-combo-nang-cap-dich-vu').forEach(function(inp) {
                inp.disabled = true;
            });
        });
        if (!outer) return;
        if (activeTarget !== '#wizard-service-nang-cap' || !selected) {
            outer.classList.add('d-none');
            return;
        }
        var nhomId = selected.value;
        var group = document.querySelector('.js-nang-cap-combo-dich-vu-group[data-nhom-dich-vu-id="' + nhomId + '"]');
        outer.classList.remove('d-none');
        if (group) {
            group.classList.remove('d-none');
            group.querySelectorAll('input.js-combo-nang-cap-dich-vu').forEach(function(inp) {
                inp.disabled = false;
            });
        }
    }

    function updateTongTienDichVu() {
        var totalInput = document.getElementById('wizard_tong_tien_dich_vu_hien_thi');
        if (!totalInput) return;
        var activeTarget = getActiveServiceTabTarget();
        var comboPrice = getSelectedComboPrice();
        var dichVuLeTotal = getTotalFromEl('wizard-dich-vu-le-total');
        var dichVuLeNangCapTotal = getTotalFromEl('wizard-dich-vu-le-nang-cap-total');
        var tong = 0;

        if (activeTarget === '#wizard-service-le') {
            tong = dichVuLeTotal;
        } else if (activeTarget === '#wizard-service-nang-cap') {
            tong = comboPrice + dichVuLeNangCapTotal;
        } else {
            tong = comboPrice;
        }

        totalInput.value = formatMoneyVnd(tong);
        syncStep3PaymentFields();
    }

    function syncStep3PaymentFields() {
        var tongTienEl = document.getElementById('wizard_tong_tien');
        var tongTienDisplayEl = document.getElementById('wizard_tong_tien_hien_thi');
        var chietKhauEl = document.getElementById('wizard_chiet_khau');
        var chietKhauDisplayEl = document.getElementById('wizard_chiet_khau_hien_thi');
        var soTienGiamGiaEl = document.getElementById('wizard_so_tien_giam_gia');
        var soTienPhaiTraEl = document.getElementById('wizard_so_tien_phai_tra');
        var hanLan2El = document.getElementById('wizard_han_thanh_toan_lan2');
        var hanLan3El = document.getElementById('wizard_han_thanh_toan_lan3');
        var ngayCuoiEl = document.getElementById('wizard_ngay_cuoi_du_kien');
        var ngayKyEl = document.getElementById('wizard_ngay_ky_hop_dong');

        var tongTienDichVu = 0;
        var activeTarget = getActiveServiceTabTarget();
        var comboPrice = getSelectedComboPrice();
        var dichVuLeTotal = getTotalFromEl('wizard-dich-vu-le-total');
        var dichVuLeNangCapTotal = getTotalFromEl('wizard-dich-vu-le-nang-cap-total');
        if (activeTarget === '#wizard-service-le') tongTienDichVu = dichVuLeTotal;
        else if (activeTarget === '#wizard-service-nang-cap') tongTienDichVu = comboPrice + dichVuLeNangCapTotal;
        else tongTienDichVu = comboPrice;

        if (tongTienEl) tongTienEl.value = String(Math.max(0, Math.round(tongTienDichVu)));
        if (tongTienDisplayEl) tongTienDisplayEl.value = formatMoneyInputNumber(tongTienDichVu);

        if (soTienGiamGiaEl && discountVoucherValid) {
            soTienGiamGiaEl.value = formatMoneyInputNumber(tinhSoTienGiamTheoRule(tongTienDichVu));
        }

        if (chietKhauDisplayEl) {
            var chietKhauNumber = parseMoneyText(chietKhauDisplayEl.value);
            chietKhauDisplayEl.value = formatMoneyInputNumber(chietKhauNumber);
            if (chietKhauEl) chietKhauEl.value = String(chietKhauNumber);
        }

        var tienCocDisplayEl = document.getElementById('wizard_tien_coc_hien_thi');
        var tienCocEl = document.getElementById('wizard_tien_coc');
        if (tienCocDisplayEl && tienCocEl) {
            var tienCocNumber = parseMoneyText(tienCocDisplayEl.value);
            tienCocDisplayEl.value = formatMoneyInputNumber(tienCocNumber);
            tienCocEl.value = String(tienCocNumber);
        }

        var chietKhau = toNumber(chietKhauEl?.value || 0);
        var soTienGiamGia = parseMoneyText(soTienGiamGiaEl?.value || 0);
        var soTienPhaiTra = Math.max(0, tongTienDichVu - chietKhau - soTienGiamGia);
        if (soTienPhaiTraEl) soTienPhaiTraEl.value = formatMoneyInputNumber(soTienPhaiTra);

        // Hạn thanh toán lần 2/3: 3 ngày trước ngày cưới (chỉ xem).
        var ngayCuoi = (ngayCuoiEl?.value || '').trim();
        if (ngayCuoi && (hanLan2El || hanLan3El)) {
            var parts = ngayCuoi.split('-');
            if (parts.length === 3) {
                var y = parseInt(parts[0], 10);
                var m = parseInt(parts[1], 10);
                var d = parseInt(parts[2], 10);
                if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
                    var dt = new Date(y, m - 1, d);
                    dt.setDate(dt.getDate() - 3);
                    var mm2 = String(dt.getMonth() + 1).padStart(2, '0');
                    var dd2 = String(dt.getDate()).padStart(2, '0');
                    var ymdDue = dt.getFullYear() + '-' + mm2 + '-' + dd2;
                    if (hanLan2El) hanLan2El.value = ymdDue;
                    if (hanLan3El) hanLan3El.value = ymdDue;
                }
            }
        }
        if (ngayKyEl && !String((ngayKyEl.value || '').trim())) {
            var today = new Date();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');
            ngayKyEl.value = today.getFullYear() + '-' + mm + '-' + dd;
        }
    }

    function kiemTraMaGiamGia() {
        var ma = (maGiamGiaInput?.value || '').trim();
        if (!ma) {
            resetVoucherDiscount('Vui lòng nhập mã giảm giá.');
            syncStep3PaymentFields();
            return;
        }

        var tongTienEl = document.getElementById('wizard_tong_tien');
        var tongTien = Math.max(0, toNumber(tongTienEl?.value || 0));
        var token = formWizard ? formWizard.querySelector('input[name="_token"]') : null;
        var fd = new FormData();
        if (token) fd.append('_token', token.value);
        fd.append('ma_giam_gia', ma);
        fd.append('tong_tien', String(tongTien));

        if (btnKiemTraMaGiamGia) btnKiemTraMaGiamGia.disabled = true;
        setMaGiamGiaMessage('Đang kiểm tra mã...', false);

        fetch(URL_KIEM_TRA_MA_GIAM_GIA, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function(r) {
                return r.json().catch(function() { return {}; }).then(function(body) {
                    return { ok: r.ok, body: body || {} };
                });
            })
            .catch(function() {
                return { ok: false, body: { message: 'Không kết nối được máy chủ.' } };
            })
            .then(function(res) {
                if (btnKiemTraMaGiamGia) btnKiemTraMaGiamGia.disabled = false;
                if (res.ok && res.body && res.body.valid) {
                    discountVoucherValid = true;
                    var soTienGiamGiaEl = document.getElementById('wizard_so_tien_giam_gia');
                    var soGiam = res.body.so_tien_giam_gia != null ? toNumber(res.body.so_tien_giam_gia) : tinhSoTienGiamTheoRule(tongTien);
                    if (soTienGiamGiaEl) soTienGiamGiaEl.value = formatMoneyInputNumber(soGiam);
                    setMaGiamGiaMessage(res.body.message || 'Mã giảm giá hợp lệ.', false);
                } else {
                    resetVoucherDiscount((res.body && res.body.message) ? String(res.body.message) : 'Mã giảm giá không hợp lệ.');
                }
                syncStep3PaymentFields();
            });
    }

    function bindFormattedMoneyInput(displayId, hiddenId) {
        var displayEl = document.getElementById(displayId);
        var hiddenEl = document.getElementById(hiddenId);
        if (!displayEl || !hiddenEl) return;

        function sync() {
            var n = parseMoneyText(displayEl.value);
            hiddenEl.value = String(n);
            displayEl.value = formatMoneyInputNumber(n);
            syncStep3PaymentFields();
        }

        displayEl.addEventListener('input', sync);
        displayEl.addEventListener('blur', sync);
        sync();
    }

    function syncDichVuLeCheckedState() {
        document.querySelectorAll('.dich-vu-le-item').forEach(function(item) {
            var checkbox = item.querySelector('input[type="checkbox"]');
            item.classList.toggle('is-checked', !!(checkbox && checkbox.checked));
        });
    }

    var WIZARD_LIST_PER_PAGE = 6;
    var wizardPaginators = {};

    function createWizardListPaginator(cfg) {
        var listRoot = document.getElementById(cfg.listId);
        var navEl = document.getElementById(cfg.navId);
        var filterInput = cfg.filterInputId ? document.getElementById(cfg.filterInputId) : null;
        var perPage = cfg.perPage || WIZARD_LIST_PER_PAGE;
        var itemSelector = cfg.itemSelector;
        var currentPage = 1;

        function getItems() {
            if (!listRoot) return [];
            return Array.from(listRoot.querySelectorAll(itemSelector));
        }

        function getKeyword() {
            return filterInput ? (filterInput.value || '').trim().toLowerCase() : '';
        }

        function getMatchedItems() {
            var keyword = getKeyword();
            return getItems().filter(function(item) {
                if (!keyword) return true;
                var text = (item.getAttribute('data-search-text') || '').toLowerCase();
                return text.indexOf(keyword) !== -1;
            });
        }

        function renderNav(total, totalPages) {
            if (!navEl) return;
            if (total <= perPage) {
                navEl.hidden = true;
                navEl.innerHTML = '';
                return;
            }
            navEl.hidden = false;
            var from = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
            var to = Math.min(currentPage * perPage, total);
            var prevDisabled = currentPage <= 1;
            var nextDisabled = currentPage >= totalPages;
            navEl.innerHTML =
                '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">' +
                '<div class="text-muted small">Đang hiển thị <strong>' + from + '</strong> đến <strong>' + to + '</strong> của <strong>' + total + '</strong> ' + (cfg.label || 'bản ghi') + '.</div>' +
                '<nav aria-label="Phân trang"><ul class="pagination pagination-sm mb-0">' +
                '<li class="page-item' + (prevDisabled ? ' disabled' : '') + '"><button type="button" class="page-link" data-wizard-page="prev" aria-label="Trang trước"' + (prevDisabled ? ' disabled' : '') + '>&laquo;</button></li>' +
                '<li class="page-item disabled"><span class="page-link">' + currentPage + ' / ' + totalPages + '</span></li>' +
                '<li class="page-item' + (nextDisabled ? ' disabled' : '') + '"><button type="button" class="page-link" data-wizard-page="next" aria-label="Trang sau"' + (nextDisabled ? ' disabled' : '') + '>&raquo;</button></li>' +
                '</ul></nav></div>';
        }

        function refresh() {
            var items = getItems();
            var matched = getMatchedItems();
            var total = matched.length;
            var totalPages = Math.max(1, Math.ceil(total / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            var start = (currentPage - 1) * perPage;
            items.forEach(function(item) {
                item.classList.add('wizard-page-hidden');
            });
            matched.slice(start, start + perPage).forEach(function(item) {
                item.classList.remove('wizard-page-hidden');
            });
            renderNav(total, totalPages);
        }

        function setPage(page) {
            currentPage = Math.max(1, page);
            refresh();
        }

        function ensureItemVisible(item) {
            if (!item) return;
            var matched = getMatchedItems();
            var idx = matched.indexOf(item);
            if (idx === -1) return;
            setPage(Math.floor(idx / perPage) + 1);
        }

        function ensureSelectorVisible(selector) {
            if (!listRoot) return;
            var hit = listRoot.querySelector(selector);
            if (!hit) return;
            var item = hit.closest(itemSelector);
            ensureItemVisible(item || hit);
        }

        if (navEl && !navEl.dataset.wizardPaginationBound) {
            navEl.dataset.wizardPaginationBound = '1';
            navEl.addEventListener('click', function(e) {
                var btn = e.target.closest('[data-wizard-page]');
                if (!btn || btn.disabled) return;
                var action = btn.getAttribute('data-wizard-page');
                if (action === 'prev' && currentPage > 1) setPage(currentPage - 1);
                if (action === 'next' && currentPage < Math.ceil(getMatchedItems().length / perPage)) setPage(currentPage + 1);
            });
        }

        if (filterInput && !filterInput.dataset.wizardPaginationFilterBound) {
            filterInput.dataset.wizardPaginationFilterBound = '1';
            filterInput.addEventListener('input', function() {
                currentPage = 1;
                refresh();
            });
        }

        return {
            refresh: refresh,
            setPage: setPage,
            ensureItemVisible: ensureItemVisible,
            ensureSelectorVisible: ensureSelectorVisible
        };
    }

    function initWizardPaginators() {
        wizardPaginators.combo = createWizardListPaginator({
            listId: 'wizard-combo-list',
            navId: 'wizard-pagination-combo',
            filterInputId: 'wizard-filter-combo',
            itemSelector: '.js-combo-item',
            label: 'combo'
        });
        wizardPaginators.dichVuLe = createWizardListPaginator({
            listId: 'wizard-dich-vu-le-list',
            navId: 'wizard-pagination-dich-vu-le',
            filterInputId: 'wizard-filter-dich-vu-le',
            itemSelector: '.js-dich-vu-le-item',
            label: 'dịch vụ lẻ'
        });
        wizardPaginators.comboNangCap = createWizardListPaginator({
            listId: 'wizard-combo-nang-cap-list',
            navId: 'wizard-pagination-combo-nang-cap',
            filterInputId: 'wizard-filter-combo-nang-cap',
            itemSelector: '.js-combo-item-nang-cap',
            label: 'combo'
        });
        wizardPaginators.dichVuLeNangCap = createWizardListPaginator({
            listId: 'wizard-dich-vu-le-nang-cap-list',
            navId: 'wizard-pagination-dich-vu-le-nang-cap',
            filterInputId: 'wizard-filter-dich-vu-le-nang-cap',
            itemSelector: '.js-dich-vu-le-nang-cap-item',
            label: 'dịch vụ lẻ'
        });
        Object.keys(wizardPaginators).forEach(function(key) {
            if (wizardPaginators[key]) wizardPaginators[key].refresh();
        });
    }

    function refreshWizardPaginators() {
        Object.keys(wizardPaginators).forEach(function(key) {
            if (wizardPaginators[key]) wizardPaginators[key].refresh();
        });
    }

    var buoiLabels = { sang: 'Sáng', chieu: 'Chiều', ca_ngay: 'Cả ngày' };
    var kenhLabels = {
        facebook: 'Facebook',
        instagram: 'Instagram',
        tiktok: 'TikTok',
        zalo: 'Zalo',
        google: 'Google / tìm kiếm',
        gioi_thieu: 'Giới thiệu',
        khac: 'Khác'
    };
    var loaiHopDongLabels = @json(\App\Models\HopDongCuoi::LOAI_HOP_DONG);

    function valByName(name) {
        var el = formWizard.querySelector('[name="' + name + '"]');
        if (!el) return '';
        if (el.tagName === 'SELECT' || el.type === 'date' || el.type === 'number') return (el.value || '').trim();
        return (el.value || '').trim();
    }

    function formatDisplayDate(ymd) {
        if (!ymd) return '—';
        var p = ymd.split('-');
        if (p.length !== 3) return ymd;
        return p[2] + '/' + p[1] + '/' + p[0];
    }

    function getSelectedServicesSummaryText() {
        var activeTarget = getActiveServiceTabTarget();
        var selectedCombo = document.querySelector('.combo-service-radio:checked');
        var parts = [];

        if (selectedCombo) {
            var comboCard = selectedCombo.closest('.combo-service-card');
            var comboTitleEl = comboCard ? comboCard.querySelector('.combo-service-title') : null;
            var comboTitle = (comboTitleEl ? comboTitleEl.textContent : '').trim();
            if (comboTitle) {
                parts.push('Combo: ' + comboTitle);
            }
        }

        if (activeTarget === '#wizard-service-le') {
            var dichVuLeRows = document.querySelectorAll('#wizard-table-dich-vu-le-body tr[data-dich-vu-id]');
            var dichVuLeParts = [];
            dichVuLeRows.forEach(function(row) {
                var tenCell = row.children[1];
                var ten = (tenCell ? tenCell.childNodes[0].textContent : '').trim();
                var qty = Math.max(1, toNumber(row.querySelector('.js-so-luong')?.value || 1));
                if (ten) dichVuLeParts.push(ten + ' x' + qty);
            });
            if (dichVuLeParts.length) {
                parts.push('Dịch vụ lẻ: ' + dichVuLeParts.join(', '));
            }
        }

        if (activeTarget === '#wizard-service-nang-cap') {
            var comboTrongNangCap = [];
            document.querySelectorAll('#wizard-service-nang-cap .js-nang-cap-combo-dich-vu-group:not(.d-none) .js-combo-nang-cap-dich-vu:checked').forEach(function(inp) {
                var label = document.querySelector('label[for="' + inp.id + '"]');
                var txt = (label ? label.textContent : '').replace(/\s+/g, ' ').trim();
                if (txt) comboTrongNangCap.push(txt);
            });
            if (comboTrongNangCap.length) {
                parts.push('Hạng mục trong combo: ' + comboTrongNangCap.join(', '));
            }

            var dichVuNangCapRows = document.querySelectorAll('#wizard-table-dich-vu-le-nang-cap-body tr[data-dich-vu-id]');
            var dichVuNangCapParts = [];
            dichVuNangCapRows.forEach(function(row) {
                var tenCellNc = row.children[1];
                var tenNc = (tenCellNc ? tenCellNc.childNodes[0].textContent : '').trim();
                var qtyNc = Math.max(1, toNumber(row.querySelector('.js-so-luong')?.value || 1));
                if (tenNc) dichVuNangCapParts.push(tenNc + ' x' + qtyNc);
            });
            if (dichVuNangCapParts.length) {
                parts.push('Dịch vụ nâng cấp: ' + dichVuNangCapParts.join(', '));
            }
        }

        return parts.length ? parts.join(' | ') : '—';
    }

    function getThanhVienSaleSummaryText() {
        var sel = document.getElementById('wizard_thanh_vien_sale_ids');
        if (!sel || !sel.options) return '—';
        var parts = [];
        Array.prototype.forEach.call(sel.selectedOptions || [], function(opt) {
            var id = String(opt.value);
            var label = (THANH_VIEN_SALE_LABELS && THANH_VIEN_SALE_LABELS[id]) ? THANH_VIEN_SALE_LABELS[id] : (opt.text || '').trim();
            if (label) parts.push(label);
        });
        return parts.length ? parts.join(', ') : '—';
    }

    function updateSummary() {
        var ma = valByName('ma_hop_dong') || document.getElementById('wizard_ma_hop_dong')?.value?.trim() || '—';
        var tenRe = valByName('ten_chu_re');
        var tenDau = valByName('ten_co_dau');
        var cap = (tenRe || '—') + ' / ' + (tenDau || '—');
        var kenh = valByName('kenh_tiep_can');
        var kenhText = kenhLabels[kenh] || (kenh || '—');
        var loaiHd = valByName('loai_hop_dong');
        var loaiHdText = loaiHopDongLabels[loaiHd] || (loaiHd || '—');

        var map = {
            ma_hop_dong: ma,
            loai_hop_dong: loaiHdText,
            ten_cap: cap,
            email_sdt_chu_re: valByName('email_sdt_chu_re') || '—',
            email_sdt_co_dau: valByName('email_sdt_co_dau') || '—',
            kenh_tiep_can: kenhText,
            thanh_vien_sale: getThanhVienSaleSummaryText(),
            yeu_cau_dac_biet: (formWizard.querySelector('[name="yeu_cau_dac_biet"]')?.value || '').trim() || '—',
            dich_vu_da_chon: getSelectedServicesSummaryText()
        };

        document.querySelectorAll('#wizard-summary [data-sum]').forEach(function(dd) {
            var key = dd.getAttribute('data-sum');
            dd.textContent = map[key] != null ? map[key] : '—';
            dd.classList.toggle('text-muted', dd.textContent === '—');
        });
    }

    function prefillHopDongInputs() {
        if (!formWizard) return;
        var raw = formWizard.getAttribute('data-hop-dong-cuoi') || '{}';
        var data = {};
        try {
            data = JSON.parse(raw);
        } catch (e) {
            data = {};
        }

        Object.keys(data).forEach(function(name) {
            if (name === 'trang_phuc_ids' || name === 'thanh_vien_nhan_vien_ids') return;
            var value = data[name];
            if (value === null || value === undefined) return;
            var el = formWizard.querySelector('[name="' + name + '"]');
            if (!el) return;

            if (el.tagName === 'SELECT') {
                el.value = String(value);
                return;
            }

            if (el.tagName === 'TEXTAREA' || el.tagName === 'INPUT') {
                if (el._flatpickr) {
                    if (value) el._flatpickr.setDate(String(value), false);
                    else el._flatpickr.clear();
                } else {
                    el.value = String(value);
                }
            }
        });

        var tpIds = data.trang_phuc_ids;
        if (Array.isArray(tpIds) && tpIds.length) {
            var tpEl = document.getElementById('wizard_trang_phuc');
            if (tpEl && tpEl.tagName === 'SELECT' && tpEl.multiple) {
                var idsStr = tpIds.map(function(x) { return String(x); });
                if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(tpEl).data('select2')) {
                    window.jQuery(tpEl).val(idsStr).trigger('change');
                } else {
                    Array.from(tpEl.options).forEach(function(opt) {
                        opt.selected = idsStr.indexOf(opt.value) !== -1;
                    });
                }
            }
        }

        var tvIds = data.thanh_vien_nhan_vien_ids;
        if (Array.isArray(tvIds) && tvIds.length) {
            var tvEl = document.getElementById('wizard_thanh_vien_sale_ids');
            if (tvEl && tvEl.tagName === 'SELECT' && tvEl.multiple) {
                var idsStrTv = tvIds.map(function(x) { return String(x); });
                if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(tvEl).data('select2')) {
                    window.jQuery(tvEl).val(idsStrTv).trigger('change');
                } else {
                    Array.from(tvEl.options).forEach(function(opt) {
                        opt.selected = idsStrTv.indexOf(opt.value) !== -1;
                    });
                }
            }
        }
    }

    function isStep1Complete() {
        if (!panel1) return true;
        var fields = panel1.querySelectorAll('[data-wizard-step1-required]');
        for (var i = 0; i < fields.length; i++) {
            var el = fields[i];
            var v = (el.tagName === 'SELECT' ? el.value : (el.value || '').trim());
            if (!v) return false;
        }
        return true;
    }

    function isStep2Complete() {
        if (!panel2) return false;
        var activeTarget = getActiveServiceTabTarget();
        if (activeTarget === '#wizard-service-combo') {
            return !!document.querySelector('#wizard-service-combo .combo-service-radio:checked');
        }
        if (activeTarget === '#wizard-service-le') {
            return document.querySelectorAll('#wizard-service-le .js-dich-vu-le:checked').length > 0;
        }
        if (activeTarget === '#wizard-service-nang-cap') {
            return !!document.querySelector('#wizard-service-nang-cap .combo-service-radio:checked');
        }
        return false;
    }

    function updateNextButtonState() {
        if (!btnNext) return;
        if (currentStep === totalSteps) return;
        if (currentStep === 1) {
            var ok = isStep1Complete();
            btnNext.disabled = !ok;
            btnNext.title = ok ? '' : 'Vui lòng điền đủ thông tin bước 1';
        } else if (currentStep === 2) {
            var ok2 = isStep2Complete();
            btnNext.disabled = !ok2;
            btnNext.title = ok2 ? '' : 'Vui lòng chọn dịch vụ ở bước 2';
        } else {
            btnNext.disabled = true;
            btnNext.title = '';
        }
    }

    function updateSubmitButtonState() {
        if (!btnSubmit || currentStep !== totalSteps) return;
        var ok = chkDongY && chkDongY.checked;
        btnSubmit.disabled = !ok;
        btnSubmit.title = ok ? '' : 'Vui lòng tick xác nhận';
    }

    function buildStep1SaveFormData() {
        var fd = new FormData();
        var token = formWizard.querySelector('input[name="_token"]');
        if (token) fd.append('_token', token.value);
        fd.append('_method', 'PUT');
        if (!panel1) return fd;
        panel1.querySelectorAll('[name]').forEach(function(el) {
            if (!el.name || el.disabled) return;
            if (el.type === 'file') return;
            if (el.type === 'checkbox' && !el.checked) return;
            if (el.type === 'radio' && !el.checked) return;
            if (el.tagName === 'SELECT' && el.multiple) {
                Array.prototype.forEach.call(el.selectedOptions || [], function(opt) {
                    fd.append(el.name, opt.value);
                });
                return;
            }
            fd.append(el.name, el.value);
        });
        return fd;
    }

    function buildStep2SaveFormData() {
        var fd = new FormData();
        var token = formWizard.querySelector('input[name="_token"]');
        if (token) fd.append('_token', token.value);
        fd.append('_method', 'PUT');
        fd.append('loai_dich_vu', getLoaiDichVuFromActiveTab());

        var target = getActiveServiceTabTarget();
        if (target === '#wizard-service-combo') {
            var rCombo = document.querySelector('#wizard-service-combo .combo-service-radio:checked');
            if (rCombo) fd.append('combo_goi', rCombo.value);
        } else if (target === '#wizard-service-le') {
            document.querySelectorAll('#wizard-table-dich-vu-le-body tr[data-dich-vu-id]').forEach(function(row) {
                var id = row.getAttribute('data-dich-vu-id');
                var qtyInp = row.querySelector('.js-so-luong');
                var qty = qtyInp ? String(qtyInp.value || '1') : '1';
                fd.append('dich_vu_chon[' + id + '][so_luong]', qty);
            });
        } else if (target === '#wizard-service-nang-cap') {
            var rNc = document.querySelector('#wizard-service-nang-cap .combo-service-radio:checked');
            if (rNc) fd.append('combo_goi', rNc.value);
            document.querySelectorAll('#wizard-service-nang-cap .js-nang-cap-combo-dich-vu-group:not(.d-none) .js-combo-nang-cap-dich-vu:checked').forEach(function(inp) {
                fd.append('dich_vu_trong_combo_nang_cap[]', inp.value);
            });
            document.querySelectorAll('#wizard-table-dich-vu-le-nang-cap-body tr[data-dich-vu-id]').forEach(function(row) {
                var idNc = row.getAttribute('data-dich-vu-id');
                var qtyNc = row.querySelector('.js-so-luong');
                fd.append('dich_vu_nang_cap[' + idNc + '][so_luong]', qtyNc ? String(qtyNc.value || '1') : '1');
            });
        }

        return fd;
    }

    function buildStep3SaveFormData() {
        var fd = new FormData();
        var token = formWizard.querySelector('input[name="_token"]');
        if (token) fd.append('_token', token.value);
        fd.append('_method', 'PUT');
        if (formWizard.getAttribute('data-la-chinh-sua') === '1') {
            fd.append('chinh_sua_hoan_tat', '1');
        }
        if (!panel3) return fd;
        panel3.querySelectorAll('[name]').forEach(function(el) {
            if (!el.name || el.disabled) return;
            if (el.type === 'file') return;
            if (el.type === 'checkbox' && !el.checked) return;
            if (el.type === 'radio' && !el.checked) return;
            if (el.tagName === 'SELECT' && el.multiple) {
                Array.prototype.forEach.call(el.selectedOptions || [], function(opt) {
                    fd.append(el.name, opt.value);
                });
                return;
            }
            fd.append(el.name, el.value);
        });
        return fd;
    }

    function saveWizardStep2(done) {
        var url = formWizard.getAttribute('data-save-step2-url');
        var errBox = document.getElementById('wizard-step2-errors');
        if (!url) {
            done(null);
            return;
        }
        if (errBox) {
            errBox.classList.add('d-none');
            errBox.innerHTML = '';
        }
        var fd = buildStep2SaveFormData();
        if (btnNext) btnNext.disabled = true;

        fetch(url, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function(r) {
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function(j) {
                        return { ok: r.ok, status: r.status, body: j };
                    });
                }
                return r.text().then(function(t) {
                    return {
                        ok: r.ok,
                        status: r.status,
                        body: { message: t ? 'Lỗi máy chủ (' + r.status + ').' : 'Lỗi máy chủ.' }
                    };
                });
            })
            .catch(function() {
                return { ok: false, status: 0, body: { message: 'Không kết nối được máy chủ.' } };
            })
            .then(function(res) {
                updateNextButtonState();
                if (res.ok) {
                    if (res.body && res.body.tong_tien != null) {
                        var tongInp = document.getElementById('wizard_tong_tien');
                        if (tongInp) tongInp.value = String(res.body.tong_tien);
                    }
                    if (formWizard && res.body) {
                        if (res.body.wizard_step2) {
                            try {
                                var wStep = res.body.wizard_step2;
                                formWizard.setAttribute('data-wizard-step2-restore', JSON.stringify(wStep));
                                var hopSync = JSON.parse(formWizard.getAttribute('data-hop-dong-cuoi') || '{}');
                                if (!hopSync || typeof hopSync !== 'object') hopSync = {};
                                hopSync.loai_dich_vu = wStep.loai_dich_vu;
                                hopSync.nhom_dich_vu_id = wStep.nhom_dich_vu_id != null && Number(wStep.nhom_dich_vu_id) > 0
                                    ? Number(wStep.nhom_dich_vu_id)
                                    : null;
                                if (wStep.loai_dich_vu === 'combo_va_nang_cap') {
                                    hopSync.combo_dich_vu_checked_ids = wStep.combo_dich_vu_checked_ids || [];
                                    hopSync.hop_dong_cuoi_dich_vu_le = (wStep.dich_vu_nang_cap || []).map(function(r) {
                                        return { id: r.id, so_luong: r.so_luong };
                                    });
                                } else if (wStep.loai_dich_vu === 'ghep_dich_vu_le') {
                                    hopSync.combo_dich_vu_checked_ids = [];
                                    hopSync.hop_dong_cuoi_dich_vu_le = (wStep.dich_vu_le || []).map(function(r) {
                                        return { id: r.id, so_luong: r.so_luong };
                                    });
                                } else {
                                    hopSync.combo_dich_vu_checked_ids = [];
                                    hopSync.hop_dong_cuoi_dich_vu_le = [];
                                }
                                formWizard.setAttribute('data-hop-dong-cuoi', JSON.stringify(hopSync));
                            } catch (eSync) { /* ignore */ }
                        } else if (res.body.loai_dich_vu) {
                            try {
                                var rawPatch = formWizard.getAttribute('data-wizard-step2-restore') || '{}';
                                var patch = JSON.parse(rawPatch);
                                if (!patch || typeof patch !== 'object') patch = {};
                                patch.loai_dich_vu = res.body.loai_dich_vu;
                                var nhId = res.body.nhom_dich_vu_id;
                                patch.nhom_dich_vu_id = nhId != null && Number(nhId) > 0 ? Number(nhId) : null;
                                formWizard.setAttribute('data-wizard-step2-restore', JSON.stringify(patch));
                            } catch (ePatch) { /* ignore */ }
                        }
                    }
                    done(null);
                    return;
                }
                var msg = (res.body && res.body.message) ? res.body.message : 'Không lưu được.';
                var errors = (res.body && res.body.errors) ? res.body.errors : {};
                if (errBox) {
                    var html = '<strong>' + String(msg) + '</strong>';
                    var keys = Object.keys(errors);
                    if (keys.length) {
                        html += '<ul class="mb-0 mt-2 small">';
                        keys.forEach(function(k) {
                            (errors[k] || []).forEach(function(line) {
                                html += '<li>' + String(line) + '</li>';
                            });
                        });
                        html += '</ul>';
                    }
                    errBox.innerHTML = html;
                    errBox.classList.remove('d-none');
                    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                done(new Error('validation'));
            });
    }

    function saveWizardStep1(done) {
        var url = formWizard.getAttribute('data-save-step1-url');
        var errBox = document.getElementById('wizard-step1-errors');
        if (!url) {
            done(null);
            return;
        }
        if (errBox) {
            errBox.classList.add('d-none');
            errBox.innerHTML = '';
        }
        var fd = buildStep1SaveFormData();
        btnNext.disabled = true;

        fetch(url, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function(r) {
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function(j) {
                        return { ok: r.ok, status: r.status, body: j };
                    });
                }
                return r.text().then(function(t) {
                    return {
                        ok: r.ok,
                        status: r.status,
                        body: { message: t ? 'Lỗi máy chủ (' + r.status + ').' : 'Lỗi máy chủ.' }
                    };
                });
            })
            .catch(function() {
                return { ok: false, status: 0, body: { message: 'Không kết nối được máy chủ.' } };
            })
            .then(function(res) {
                updateNextButtonState();
                if (res.ok) {
                    done(null);
                    return;
                }
                var msg = (res.body && res.body.message) ? res.body.message : 'Không lưu được.';
                var errors = (res.body && res.body.errors) ? res.body.errors : {};
                if (errBox) {
                    var html = '<strong>' + String(msg) + '</strong>';
                    var keys = Object.keys(errors);
                    if (keys.length) {
                        html += '<ul class="mb-0 mt-2 small">';
                        keys.forEach(function(k) {
                            (errors[k] || []).forEach(function(line) {
                                html += '<li>' + String(line) + '</li>';
                            });
                        });
                        html += '</ul>';
                    }
                    errBox.innerHTML = html;
                    errBox.classList.remove('d-none');
                    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                done(new Error('validation'));
            });
    }

    function saveWizardStep3(done) {
        done = typeof done === 'function' ? done : function() {};
        var url = formWizard ? formWizard.getAttribute('data-save-step3-url') : '';
        var afterRedirect = formWizard ? formWizard.getAttribute('data-after-submit-redirect') : '';
        var errBox = document.getElementById('wizard-step3-errors');
        if (!formWizard || !url) {
            done(null);
            return;
        }
        if (errBox) {
            errBox.classList.add('d-none');
            errBox.innerHTML = '';
        }
        syncStep3PaymentFields();
        var fd = buildStep3SaveFormData();
        if (btnSubmit) btnSubmit.disabled = true;

        fetch(url, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function(r) {
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function(j) {
                        return { ok: r.ok, status: r.status, body: j };
                    });
                }
                return r.text().then(function(t) {
                    return {
                        ok: r.ok,
                        status: r.status,
                        body: { message: t ? 'Lỗi máy chủ (' + r.status + ').' : 'Lỗi máy chủ.' }
                    };
                });
            })
            .catch(function() {
                return { ok: false, status: 0, body: { message: 'Không kết nối được máy chủ.' } };
            })
            .then(function(res) {
                updateSubmitButtonState();
                if (res.ok) {
                    if (res.body && res.body.redirect) {
                        window.location.href = res.body.redirect;
                        return;
                    }
                    if (afterRedirect) {
                        window.location.href = afterRedirect;
                        return;
                    }
                    done(null);
                    return;
                }
                var msg = (res.body && res.body.message) ? res.body.message : 'Không lưu được.';
                var errors = (res.body && res.body.errors) ? res.body.errors : {};
                if (errBox) {
                    var html = '<strong>' + String(msg) + '</strong>';
                    var keys = Object.keys(errors);
                    if (keys.length) {
                        html += '<ul class="mb-0 mt-2 small">';
                        keys.forEach(function(k) {
                            (errors[k] || []).forEach(function(line) {
                                html += '<li>' + String(line) + '</li>';
                            });
                        });
                        html += '</ul>';
                    }
                    errBox.innerHTML = html;
                    errBox.classList.remove('d-none');
                    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                done(new Error('validation'));
            });
    }

    function setStep(n, options) {
        options = options || {};
        if (n < 1 || n > totalSteps) return;
        if (n > currentStep && currentStep === 1 && !isStep1Complete()) {
            return;
        }
        if (n > currentStep && currentStep === 2 && !isStep2Complete()) {
            return;
        }
        if (!options.skipStep1Save && n > currentStep && currentStep === 1 && isStep1Complete()) {
            saveWizardStep1(function(err) {
                if (!err) setStep(n, { skipStep1Save: true });
            });
            return;
        }
        if (!options.skipStep2Save && n > currentStep && currentStep === 2 && isStep2Complete()) {
            saveWizardStep2(function(err) {
                if (!err) setStep(n, { skipStep2Save: true });
            });
            return;
        }
        currentStep = n;
        panels.forEach(function(p) {
            var step = parseInt(p.getAttribute('data-wizard-panel'), 10);
            var isActive = step === currentStep;
            p.classList.toggle('active', isActive);
            if (isActive) p.removeAttribute('hidden');
            else p.setAttribute('hidden', '');
        });
        indicators.forEach(function(ind) {
            var step = parseInt(ind.getAttribute('data-step'), 10);
            ind.classList.remove('active', 'done');
            if (step === currentStep) ind.classList.add('active');
            else if (step < currentStep) ind.classList.add('done');
            var btn = ind.querySelector('.wizard-step-btn');
            if (btn) btn.setAttribute('aria-current', step === currentStep ? 'step' : 'false');
        });
        btnPrev.disabled = currentStep === 1;
        if (currentStep === totalSteps) {
            btnNext.classList.add('d-none');
            btnSubmit.classList.remove('d-none');
            updateSummary();
            updateSubmitButtonState();
        } else {
            btnNext.classList.remove('d-none');
            btnSubmit.classList.add('d-none');
        }
        if (hint) hint.textContent = 'Bước ' + currentStep + ' / ' + totalSteps;
        updateNextButtonState();
        if (currentStep === 2) {
            window.requestAnimationFrame(function() {
                ensureStep2Select2();
                refreshWizardPaginators();
                applyWizardStep2Restore(function() {
                    refreshWizardPaginators();
                    updateNangCapComboDichVuPanel();
                    updateTongTienDichVu();
                    updateNextButtonState();
                });
            });
        }
        if (currentStep === 3) {
            window.requestAnimationFrame(function() {
                ensureStep3Select2();
                applyWizardStep3ConceptRestore();
                syncStep3PaymentFields();
                updateSummary();
            });
        }
    }

    btnPrev.addEventListener('click', function() { setStep(currentStep - 1); });
    btnNext.addEventListener('click', function() { setStep(currentStep + 1); });

    document.querySelectorAll('[data-go-step]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = parseInt(btn.getAttribute('data-go-step'), 10);
            if (!isNaN(target)) setStep(target);
        });
    });

    if (chkDongY) {
        chkDongY.addEventListener('change', updateSubmitButtonState);
    }

    if (formWizard) {
        formWizard.addEventListener('submit', function(e) {
            e.preventDefault();
            if (currentStep !== totalSteps) return;
            if (!chkDongY || !chkDongY.checked) return;
            saveWizardStep3();
        });
    }

    document.querySelectorAll('.js-dich-vu-le').forEach(function(el) {
        el.addEventListener('change', function() {
            syncDichVuLeCheckedState();
            buildDichVuLeTable('.js-dich-vu-le', 'wizard-table-dich-vu-le-body', 'wizard-dich-vu-le-total', 'Chưa chọn dịch vụ lẻ nào.');
            updateNextButtonState();
        });
    });

    document.querySelectorAll('.js-dich-vu-le-nang-cap').forEach(function(el) {
        el.addEventListener('change', function() {
            syncDichVuLeCheckedState();
            buildDichVuLeTable('.js-dich-vu-le-nang-cap', 'wizard-table-dich-vu-le-nang-cap-body', 'wizard-dich-vu-le-nang-cap-total', 'Chưa chọn dịch vụ lẻ nâng cấp nào.', 'dich_vu_nang_cap');
            updateNextButtonState();
        });
    });

    function syncComboCheckedState() {
        document.querySelectorAll('.combo-service-radio').forEach(function(radio) {
            var card = radio.closest('.combo-service-card');
            var checkbox = card ? card.querySelector('.combo-service-checkbox') : null;
            if (checkbox) checkbox.checked = !!radio.checked;
        });
    }

    document.querySelectorAll('.combo-service-radio').forEach(function(el) {
        el.addEventListener('change', function() {
            syncComboCheckedState();
            updateNangCapComboDichVuPanel();
            updateTongTienDichVu();
            updateNextButtonState();
        });
    });

    initWizardPaginators();

    var dichVuTableBody = document.getElementById('wizard-table-dich-vu-le-body');
    if (dichVuTableBody) {
        dichVuTableBody.addEventListener('input', function(e) {
            if (e.target.closest('.js-so-luong')) {
                recalcDichVuLeTotal('wizard-table-dich-vu-le-body', 'wizard-dich-vu-le-total');
                syncStep3PaymentFields();
            }
        });
    }
    var dichVuNangCapTableBody = document.getElementById('wizard-table-dich-vu-le-nang-cap-body');
    if (dichVuNangCapTableBody) {
        dichVuNangCapTableBody.addEventListener('input', function(e) {
            if (e.target.closest('.js-so-luong')) {
                recalcDichVuLeTotal('wizard-table-dich-vu-le-nang-cap-body', 'wizard-dich-vu-le-nang-cap-total');
                syncStep3PaymentFields();
            }
        });
    }

    document.querySelectorAll('#formTaoHopDongWizard [data-bs-toggle="tab"]').forEach(function(tabBtn) {
        tabBtn.addEventListener('shown.bs.tab', function() {
            if (window.jQuery && window.jQuery.fn.select2) {
                window.jQuery('#wizard-service-tab-content .tab-pane.active select.form-select').each(function() {
                    window.jQuery(this).trigger('change.select2');
                });
            }
            updateNangCapComboDichVuPanel();
            updateTongTienDichVu();
            updateNextButtonState();
        });
    });

    if (formWizard && panel1) {
        ['input', 'change'].forEach(function(evt) {
            formWizard.addEventListener(evt, function(e) {
                if (panel1.contains(e.target)) updateNextButtonState();
                if (panel2 && panel2.contains(e.target) && currentStep === 2) updateNextButtonState();
                if (currentStep === totalSteps) updateSummary();
                if (
                    e.target.id === 'wizard_chiet_khau' ||
                    e.target.id === 'wizard_ma_giam_gia' ||
                    e.target.id === 'wizard_ngay_cuoi_du_kien'
                ) {
                    if (e.target.id === 'wizard_ma_giam_gia') resetVoucherDiscount('');
                    syncStep3PaymentFields();
                }
            });
        });
    }

    if (btnKiemTraMaGiamGia) {
        btnKiemTraMaGiamGia.addEventListener('click', function() {
            kiemTraMaGiamGia();
        });
    }
    if (maGiamGiaInput) {
        maGiamGiaInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                kiemTraMaGiamGia();
            }
        });
    }

    bindFormattedMoneyInput('wizard_chiet_khau_hien_thi', 'wizard_chiet_khau');
    bindFormattedMoneyInput('wizard_tien_coc_hien_thi', 'wizard_tien_coc');

    prefillHopDongInputs();
    applyWizardFieldLabelNumbers();
    syncComboCheckedState();
    updateNangCapComboDichVuPanel();
    syncDichVuLeCheckedState();
    buildDichVuLeTable('.js-dich-vu-le', 'wizard-table-dich-vu-le-body', 'wizard-dich-vu-le-total', 'Chưa chọn dịch vụ lẻ nào.');
    buildDichVuLeTable('.js-dich-vu-le-nang-cap', 'wizard-table-dich-vu-le-nang-cap-body', 'wizard-dich-vu-le-nang-cap-total', 'Chưa chọn dịch vụ lẻ nâng cấp nào.', 'dich_vu_nang_cap');
    updateTongTienDichVu();
    syncStep3PaymentFields();
    setStep(1);
});
</script>
@endpush
