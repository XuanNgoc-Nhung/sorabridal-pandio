@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<style>
    .dpc-busy-flag {
        font-size: 0.72rem;
        line-height: 1.1;
        vertical-align: middle;
    }
    .dpc-project-card .client-info {
        font-size: 0.9375rem;
    }
    .dpc-date-col .dpc-date-icon {
        width: 1.15rem;
        text-align: center;
        flex-shrink: 0;
    }
    .dpc-date-col > .dpc-date-row + .dpc-date-row {
        margin-top: 0.2rem;
    }
    .dpc-date-col .dpc-date-row {
        line-height: 1.35;
    }
    th.dpc-date-col-th .dpc-date-icon {
        font-size: 0.95rem;
    }
    .dpc-ekip-col .dpc-ekip-row {
        white-space: nowrap;
        line-height: 1.35;
    }
    .dpc-ekip-col .dpc-ekip-row + .dpc-ekip-row {
        margin-top: 0.2rem;
    }
    .dpc-date-col .dpc-date-row--chup {
        white-space: nowrap;
    }
    /* Modal thanh toán: cuộn phần nội dung khi vượt chiều cao màn hình */
    #modalThanhToanHopDongCuoi .modal-dialog {
        max-height: calc(100vh - 2rem);
    }
    #modalThanhToanHopDongCuoi .modal-content {
        max-height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
    }
    #modalThanhToanHopDongCuoi .modal-header,
    #modalThanhToanHopDongCuoi .modal-footer {
        flex-shrink: 0;
    }
    #modalThanhToanHopDongCuoi .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
@php
    $trangThaiNhan = [
        'nhap' => 'Nháp',
        'da_huy' => 'Đã huỷ',
        'dang_thuc_hien' => 'Đang thực hiện',
        'tre_chup' => 'Trễ chụp',
        'tre_edit' => 'Trễ edit',
    ];
    $trangThaiClass = [
        'nhap' => 'bg-label-secondary',
        'da_huy' => 'bg-label-danger',
        'dang_thuc_hien' => 'bg-label-primary',
        'tre_chup' => 'bg-label-warning',
        'tre_edit' => 'bg-label-warning',
    ];
    $loaiDichVuNhan = [
        'combo_tron_goi' => 'Combo trọn gói',
        'ghep_dich_vu_le' => 'Ghép dịch vụ lẻ',
        'combo_va_nang_cap' => 'Combo & nâng cấp',
    ];
    $buoiChupNhan = [
        'sang' => 'Sáng',
        'chieu' => 'Chiều',
        'ca_ngay' => 'Cả ngày',
    ];
    $locFilters = $locFilters ?? [];
    $locTienDoFilters = $locTienDoFilters ?? config('lich_lam_viec.loc_tien_do', []);
    $loaiHopDongNhan = \App\Models\HopDongCuoi::LOAI_HOP_DONG;
    $sapXepTheoMacDinh = \App\Models\HopDongCuoi::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('tu_khoa')
        || request()->filled('ngay_cuoi_tu')
        || request()->filled('ngay_cuoi_den')
        || request()->filled('loai_hop_dong')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc'
        || ! empty($locFilters);
    /** Gọn số tiền: từ 1.000.000 hiển thị dạng 1,5M / 1,241M (dấu phẩy là phần thập phân của triệu). */
    $tienGonMega = function (float $v): string {
        $v = (float) round($v);
        $abs = abs($v);
        if ($abs < 1_000_000) {
            return number_format($v, 0, ',', '.') . ' đ';
        }
        $m = $v / 1_000_000;
        $s = number_format($m, 3, ',', '');
        $s = rtrim(rtrim($s, '0'), ',');

        return $s . 'M';
    };
    $routeTtGet = route('admin.khach-hang.hop-dong-cuoi.thanh-toan', ['hopDongCuoi' => 999999999]);
    $routeTtPost = route('admin.khach-hang.hop-dong-cuoi.thanh-toan.luu', ['hopDongCuoi' => 999999999]);
@endphp
<div class="d-flex flex-column gap-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form action="{{ route('admin.khach-hang.danh-sach-hop-dong-cuoi') }}" method="GET">
            @foreach($locFilters as $locKey)
                <input type="hidden" name="loc[]" value="{{ $locKey }}">
            @endforeach
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
                    <label class="form-label" for="hop_dong_cuoi_ngay_cuoi_range">Ngày cưới (khoảng)</label>
                    <input type="text"
                           class="form-control @error('ngay_cuoi_tu') is-invalid @enderror @error('ngay_cuoi_den') is-invalid @enderror"
                           id="hop_dong_cuoi_ngay_cuoi_range"
                           placeholder="dd/mm/yyyy - dd/mm/yyyy"
                           autocomplete="off"
                           value="">
                    <input type="hidden"
                           name="ngay_cuoi_tu"
                           id="hop_dong_cuoi_ngay_cuoi_tu"
                           value="{{ old('ngay_cuoi_tu', request('ngay_cuoi_tu')) }}">
                    <input type="hidden"
                           name="ngay_cuoi_den"
                           id="hop_dong_cuoi_ngay_cuoi_den"
                           value="{{ old('ngay_cuoi_den', request('ngay_cuoi_den')) }}">
                    @error('ngay_cuoi_tu')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('ngay_cuoi_den')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="filter_loai_hop_dong">Loại hợp đồng</label>
                    <select class="select2-admin form-select" id="filter_loai_hop_dong" name="loai_hop_dong" data-placeholder="Tất cả">
                        <option value="">-- Tất cả --</option>
                        @foreach($loaiHopDongNhan as $value => $label)
                            <option value="{{ $value }}" @selected(request('loai_hop_dong') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\HopDongCuoi::SAP_XEP_OPTIONS as $value => $label)
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
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.khach-hang.danh-sach-hop-dong-cuoi') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>

        @if(!empty($locTienDoFilters) && is_array($locTienDoFilters))
        <div class="pt-3" id="dpc-list-toolbar-area">
            <div class="dpc-loc-filters">
                {{-- <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="text-muted small mb-0">Lọc theo trạng thái hợp đồng</div>
                    <button type="button"
                            class="btn btn-sm btn-label-secondary {{ empty($locFilters) ? 'd-none' : '' }}"
                            id="dpcLocClear">Bỏ chọn lọc</button>
                </div> --}}
                <div class="d-flex flex-wrap gap-3">
                    @foreach($locTienDoFilters as $locKey => $locItem)
                        @if(!empty($locItem['label']))
                            <div class="form-check mb-0">
                                <input class="form-check-input dpc-loc-filter"
                                       type="checkbox"
                                       value="{{ $locKey }}"
                                       id="dpc_loc_{{ $locKey }}"
                                       @checked(in_array($locKey, $locFilters, true))>
                                <label class="form-check-label" for="dpc_loc_{{ $locKey }}">{{ $locItem['label'] }}</label>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách hợp đồng cưới</span>
            <span class="d-flex flex-wrap align-items-center gap-2"
                  id="dpc-view-toolbar"
                  role="toolbar"
                  aria-label="Chế độ xem danh sách">
                <a href="{{ route('admin.khach-hang.tao-hop-dong') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Tạo mới
                </a>
                <div class="btn-group btn-group-sm" role="group" aria-label="Bảng hoặc lưới">
                    <button type="button"
                            class="btn btn-primary dpc-view-btn active"
                            id="dpc-view-btn-table"
                            data-dpc-view="table"
                            title="Xem dạng bảng"
                            aria-pressed="true">
                        <i class="bi bi-table" aria-hidden="true"></i>
                    </button>
                    <button type="button"
                            class="btn btn-outline-secondary dpc-view-btn"
                            id="dpc-view-btn-grid"
                            data-dpc-view="grid"
                            title="Xem dạng lưới (card)"
                            aria-pressed="false">
                        <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                    </button>
                </div>
            </span>
        </h5>
        <div class="card-body">
        <div id="dpc-view-table-wrap" class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        <th style="min-width: 200px;">Cặp đôi</th>
                        <th style="min-width: 110px;">Mã HĐ</th>
                        <th style="min-width: 120px;">Loại HĐ</th>
                        <th class="dpc-date-col-th" style="min-width: 132px;">Thời gian</th>
                        <th style="min-width: 200px;">Dịch vụ</th>
                        <th style="min-width: 100px;">Trạng thái</th>
                        <th style="min-width: 150px;">Ekip</th>
                        <th style="min-width: 140px;">Thành viên</th>
                        <th style="min-width: 160px;">Thanh toán</th>
                        <th class="text-center" style="min-width: 108px;">Hành động</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($hopDongCuois as $index => $item)
                    @php
                        $tt = $item->trang_thai_hop_dong ?? 'nhap';
                        $ngayChupHien = $item->ngay_chup_thuc_te ?? $item->ngay_chup_du_kien;
                        $ngayCuoiHien = $item->ngay_cuoi_chinh_thuc ?? $item->ngay_cuoi_du_kien;
                        $gioChupTxt = $item->gio_chup ? substr((string) $item->gio_chup, 0, 5) : '';
                        $tenThoChup = $item->thoChup?->user?->name ?? '—';
                        $tenDichVu = $item->nhomDichVu?->ten_nhom
                            ?? $item->concept?->ten_concept
                            ?? ($loaiDichVuNhan[$item->loai_dich_vu] ?? null);
                        if ($tenDichVu === null && $item->loai_dich_vu) {
                            $tenDichVu = (string) str($item->loai_dich_vu)->replace('_', ' ');
                        }
                        $tenLoaiHopDong = $loaiHopDongNhan[$item->loai_hop_dong] ?? ($item->loai_hop_dong ?: '—');
                        $tongTien = (float) ($item->tong_tien ?? 0);
                        $daThanhToan = (float) ($item->tien_coc ?? 0);
                        $tyLeThanhToan = $tongTien > 0 ? min(100, (int) round($daThanhToan / $tongTien * 100)) : 0;
                        $rawNgayUpLinkDemoGanNhat = $item->ngay_up_link_demo_gan_nhat ?? null;
                        $rawNgayUpLinkInGanNhat = $item->ngay_up_link_in_gan_nhat ?? null;
                        $ngayUpLinkDemoStr = ! empty($rawNgayUpLinkDemoGanNhat)
                            ? ($rawNgayUpLinkDemoGanNhat instanceof \Carbon\CarbonInterface
                                ? $rawNgayUpLinkDemoGanNhat->format('d/m/Y H:i')
                                : \Carbon\Carbon::parse($rawNgayUpLinkDemoGanNhat)->format('d/m/Y H:i'))
                            : '—';
                        $ngayUpLinkInStr = ! empty($rawNgayUpLinkInGanNhat)
                            ? ($rawNgayUpLinkInGanNhat instanceof \Carbon\CarbonInterface
                                ? $rawNgayUpLinkInGanNhat->format('d/m/Y H:i')
                                : \Carbon\Carbon::parse($rawNgayUpLinkInGanNhat)->format('d/m/Y H:i'))
                            : '—';
                        $dieuPhoiPayload = [
                            'url' => route('admin.khach-hang.hop-dong-cuoi.dieu-phoi', $item),
                            'hop_dong_cuoi_id' => $item->id,
                            'ma_hop_dong' => $item->ma_hop_dong,
                            'ngay_chup_thuc_te' => $item->ngay_chup_thuc_te?->format('Y-m-d'),
                            'gio_chup' => $gioChupTxt,
                            'ngay_cuoi_chinh_thuc' => $item->ngay_cuoi_chinh_thuc?->format('Y-m-d'),
                            'dia_diem_chup' => $item->dia_diem_chup ?? '',
                            'ngay_tra_link_demo_chinh_thuc' => $item->ngay_tra_link_demo_chinh_thuc?->format('Y-m-d'),
                            'ngay_tra_link_in_chinh_thuc' => $item->ngay_tra_link_in_chinh_thuc?->format('Y-m-d'),
                            'tho_chup_id' => $item->tho_chup_id,
                            'tho_make_id' => $item->tho_make_id,
                            'tho_edit_id' => $item->tho_edit_id,
                            'ghi_chu_sale' => $item->ghi_chu_sale ?? '',
                        ];
                        /** Một lần htmlspecialchars cho attribute; KHÔNG dùng {{ }} vì Blade sẽ escape thêm → JSON.parse lỗi. */
                        $dieuPhoiPayloadAttr = htmlspecialchars(
                            json_encode($dieuPhoiPayload, JSON_UNESCAPED_UNICODE),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        $thanhVienSaleRows = $item->thanhVienHopDongCuis
                            ->map(static fn ($tv) => [
                                'ten' => $tv->nhanVien?->user?->name,
                                'vai_tro' => \App\Models\ThanhVienHopDongCuoi::vaiTroLabel($tv->vai_tro),
                            ])
                            ->values()
                            ->all();
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($hopDongCuois->currentPage() - 1) * $hopDongCuois->perPage() + $index + 1 }}</td>
                        <td class="text-wrap">
                            <div class="fw-medium">
                                {{ $item->ten_chu_re ?: '—' }}
                                <span class="text-muted fw-normal">&amp;</span>
                                {{ $item->ten_co_dau ?: '—' }}
                            </div>
                            <div class="small text-muted mt-1 lh-sm">
                                @if($item->email_sdt_chu_re)
                                <div>CR: {{ str($item->email_sdt_chu_re)->limit(42) }}</div>
                                @endif
                                @if($item->email_sdt_co_dau)
                                <div>CD: {{ str($item->email_sdt_co_dau)->limit(42) }}</div>
                                @endif
                                @if(! $item->email_sdt_chu_re && ! $item->email_sdt_co_dau)
                                <div>—</div>
                                @endif
                            </div>
                        </td>
                        <td><span class="fw-medium">{{ $item->ma_hop_dong ?? '—' }}</span></td>
                        <td class="text-wrap"><span>{{ $tenLoaiHopDong }}</span></td>
                        <td class="dpc-date-col small text-wrap">
                            <div class="dpc-date-row dpc-date-row--chup d-flex align-items-center gap-2">
                                <i class="bi bi-camera-fill dpc-date-icon text-muted" title="Thời gian chụp" aria-hidden="true"></i>
                                <span>
                                    <span>{{ $ngayChupHien ? $ngayChupHien->format('d/m/Y') : '—' }}</span>
                                    @if($gioChupTxt)<span class="text-muted"> {{ $gioChupTxt }}</span>@endif
                                </span>
                            </div>
                            <div class="dpc-date-row d-flex align-items-start gap-2">
                                <i class="bi bi-heart-fill dpc-date-icon text-muted" title="Ngày cưới" aria-hidden="true"></i>
                                <div class="text-wrap">
                                    @if($ngayCuoiHien)
                                    <span>{{ $ngayCuoiHien->format('d/m/Y') }}</span>
                                    {{-- @if($item->ngay_cuoi_chinh_thuc && $item->ngay_cuoi_du_kien && $item->ngay_cuoi_chinh_thuc->format('Y-m-d') !== $item->ngay_cuoi_du_kien->format('Y-m-d'))
                                    <span class="d-block text-muted">DK {{ $item->ngay_cuoi_du_kien->format('d/m/Y') }}</span>
                                    @endif --}}
                                    @else
                                    <span>—</span>
                                    @endif
                                </div>
                            </div>
                            <div class="dpc-date-row d-flex align-items-center gap-2">
                                <i class="bi bi-play-btn dpc-date-icon text-muted" title="Ngày up link demo" aria-hidden="true"></i>
                                <span class="text-nowrap">{{ $ngayUpLinkDemoStr }}</span>
                            </div>
                            <div class="dpc-date-row d-flex align-items-center gap-2">
                                <i class="bi bi-printer dpc-date-icon text-muted" title="Ngày up link in" aria-hidden="true"></i>
                                <span class="text-nowrap">{{ $ngayUpLinkInStr }}</span>
                            </div>
                        </td>
                        <td class="text-wrap">
                            <div>
                                <span class="fw-medium">{{ $tenDichVu ?: '—' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $trangThaiClass[$tt] ?? 'bg-label-secondary' }}">
                                {{ $trangThaiNhan[$tt] ?? $tt }}
                            </span>
                        </td>
                        <td class="dpc-ekip-col small">
                            <div class="dpc-ekip-row"><span class="text-muted">Chụp:</span> {{ $tenThoChup }}</div>
                            <div class="dpc-ekip-row"><span class="text-muted">Make:</span> {{ $item->thoMake?->user?->name ?? '—' }}</div>
                            <div class="dpc-ekip-row"><span class="text-muted">Edit:</span> {{ $item->thoEdit?->user?->name ?? '—' }}</div>
                        </td>
                        <td class="dpc-ekip-col small">
                            @forelse($thanhVienSaleRows as $saleRow)
                            <div class="dpc-ekip-row">
                                {{ $saleRow['ten'] ?: '—' }}
                                <span class="text-muted">({{ $saleRow['vai_tro'] }})</span>
                            </div>
                            @empty
                            —
                            @endforelse
                        </td>
                        <td class="text-wrap" style="min-width: 150px;">
                            <div class="fw-semibold">{{ number_format((float) ($item->tong_tien ?? 0), 0, ',', '.') }} đ</div>
                            <div class="progress mt-2" style="height: 0.45rem;">
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{ $tyLeThanhToan }}%;"
                                     aria-valuenow="{{ $tyLeThanhToan }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="small text-muted mt-1">
                                <span class="text-body">{{ $tienGonMega($daThanhToan) }}</span>
                                <span class="text-muted"> / </span>
                                <span>{{ $tienGonMega($tongTien) }}</span>
                            </div>
                        </td>
                        <td class="text-center text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                @if(($item->trang_thai_hop_dong ?? '') !== 'da_huy')
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalThanhToanHopDongCuoi"
                                            data-hop-id="{{ $item->id }}"
                                            title="Thanh toán"
                                            aria-label="Thanh toán">
                                        <i class="bi bi-cash-coin fs-5" aria-hidden="true"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDieuPhoiHopDongCuoi"
                                            data-payload="{!! $dieuPhoiPayloadAttr !!}"
                                            title="Điều phối công việc"
                                            aria-label="Điều phối công việc">
                                        <i class="bi bi-sliders2 fs-5" aria-hidden="true"></i>
                                    </button>
                                    <a href="{{ route('admin.khach-hang.chinh-sua-hop-dong-cuoi', $item) }}"
                                       class="btn btn-sm btn-icon btn-text-primary"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Chỉnh sửa hợp đồng"
                                       aria-label="Chỉnh sửa hợp đồng">
                                        <i class="bi bi-pencil-square fs-5" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary"
                                            disabled
                                            title="Hợp đồng đã huỷ"
                                            aria-label="Hợp đồng đã huỷ">
                                        <i class="bi bi-cash-coin fs-5" aria-hidden="true"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary"
                                            disabled
                                            title="Hợp đồng đã huỷ"
                                            aria-label="Hợp đồng đã huỷ">
                                        <i class="bi bi-sliders2 fs-5" aria-hidden="true"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary"
                                            disabled
                                            title="Hợp đồng đã huỷ"
                                            aria-label="Hợp đồng đã huỷ">
                                        <i class="bi bi-pencil-square fs-5" aria-hidden="true"></i>
                                    </button>
                                @endif
                                @if(($item->trang_thai_hop_dong ?? '') !== 'da_huy')
                                <form id="form-huy-hdc-{{ $item->id }}"
                                      action="{{ route('admin.khach-hang.hop-dong-cuoi.huy', $item) }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-danger btn-huy-hop-dong-cuoi"
                                        data-form-id="form-huy-hdc-{{ $item->id }}"
                                        data-ma="{{ e($item->ma_hop_dong ?? '') }}"
                                        title="Huỷ hợp đồng"
                                        aria-label="Huỷ hợp đồng">
                                    <i class="bi bi-x-circle fs-5" aria-hidden="true"></i>
                                </button>
                                @else
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-secondary"
                                        disabled
                                        title="Hợp đồng đã huỷ"
                                        aria-label="Hợp đồng đã huỷ">
                                    <i class="bi bi-x-circle fs-5" aria-hidden="true"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4 text-muted">Không có hợp đồng cưới phù hợp bộ lọc.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="dpc-view-grid-wrap" class="d-none">
            @if($hopDongCuois->isEmpty())
                <div class="text-center py-5 text-muted border rounded">Không có hợp đồng cưới phù hợp bộ lọc.</div>
            @else
                <div class="row g-4">
                    @foreach($hopDongCuois as $index => $item)
                    @php
                        $tt = $item->trang_thai_hop_dong ?? 'nhap';
                        $ngayChupHien = $item->ngay_chup_thuc_te ?? $item->ngay_chup_du_kien;
                        $ngayCuoiHien = $item->ngay_cuoi_chinh_thuc ?? $item->ngay_cuoi_du_kien;
                        $gioChupTxt = $item->gio_chup ? substr((string) $item->gio_chup, 0, 5) : '';
                        $tenThoChup = $item->thoChup?->user?->name ?? '—';
                        $tenDichVu = $item->nhomDichVu?->ten_nhom
                            ?? $item->concept?->ten_concept
                            ?? ($loaiDichVuNhan[$item->loai_dich_vu] ?? null);
                        if ($tenDichVu === null && $item->loai_dich_vu) {
                            $tenDichVu = (string) str($item->loai_dich_vu)->replace('_', ' ');
                        }
                        $tenLoaiHopDong = $loaiHopDongNhan[$item->loai_hop_dong] ?? ($item->loai_hop_dong ?: '—');
                        $tongTien = (float) ($item->tong_tien ?? 0);
                        $daThanhToan = (float) ($item->tien_coc ?? 0);
                        $tyLeThanhToan = $tongTien > 0 ? min(100, (int) round($daThanhToan / $tongTien * 100)) : 0;
                        $ngayChupTxt = $ngayChupHien ? $ngayChupHien->format('d/m/Y') : '—';
                        $ngayCuoiTxt = $ngayCuoiHien ? $ngayCuoiHien->format('d/m/Y') : '—';
                        $ngayCuoiTitle = $ngayCuoiHien ? $ngayCuoiHien->format('d/m/Y') : 'Chưa có ngày cưới';
                        if ($item->ngay_cuoi_chinh_thuc && $item->ngay_cuoi_du_kien && $item->ngay_cuoi_chinh_thuc->format('Y-m-d') !== $item->ngay_cuoi_du_kien->format('Y-m-d')) {
                            $ngayCuoiTitle .= ' (DK ' . $item->ngay_cuoi_du_kien->format('d/m/Y') . ')';
                        }
                        $tenCr = $item->ten_chu_re ?: '—';
                        $tenCd = $item->ten_co_dau ?: '—';
                        $coupleTitle = $tenCr . ' & ' . $tenCd;
                        $initialHd = '';
                        if ($item->ten_chu_re !== null && $item->ten_chu_re !== '') {
                            $initialHd .= mb_strtoupper(mb_substr($item->ten_chu_re, 0, 1));
                        }
                        if ($item->ten_co_dau !== null && $item->ten_co_dau !== '') {
                            $initialHd .= mb_strtoupper(mb_substr($item->ten_co_dau, 0, 1));
                        }
                        if ($initialHd === '') {
                            $initialHd = 'HD';
                        }
                        $lienHeParts = [];
                        if ($item->email_sdt_chu_re) {
                            $lienHeParts[] = 'CR ' . (string) str($item->email_sdt_chu_re)->limit(28);
                        }
                        if ($item->email_sdt_co_dau) {
                            $lienHeParts[] = 'CD ' . (string) str($item->email_sdt_co_dau)->limit(28);
                        }
                        $lienHeGon = count($lienHeParts) ? implode(' · ', $lienHeParts) : '—';
                        $lienHeTitle = trim(
                            ($item->email_sdt_chu_re ? 'CR: ' . $item->email_sdt_chu_re : '')
                            . ($item->email_sdt_chu_re && $item->email_sdt_co_dau ? ' · ' : '')
                            . ($item->email_sdt_co_dau ? 'CD: ' . $item->email_sdt_co_dau : '')
                        ) ?: '—';
                        $lienHeCr = $item->email_sdt_chu_re ? $item->email_sdt_chu_re : '—';
                        $lienHeCd = $item->email_sdt_co_dau ? $item->email_sdt_co_dau : '—';
                        $rawNgayUpLinkDemoGanNhat = $item->ngay_up_link_demo_gan_nhat ?? null;
                        $rawNgayUpLinkInGanNhat = $item->ngay_up_link_in_gan_nhat ?? null;
                        $ngayUpLinkDemoStr = ! empty($rawNgayUpLinkDemoGanNhat)
                            ? ($rawNgayUpLinkDemoGanNhat instanceof \Carbon\CarbonInterface
                                ? $rawNgayUpLinkDemoGanNhat->format('d/m/Y H:i')
                                : \Carbon\Carbon::parse($rawNgayUpLinkDemoGanNhat)->format('d/m/Y H:i'))
                            : '—';
                        $ngayUpLinkInStr = ! empty($rawNgayUpLinkInGanNhat)
                            ? ($rawNgayUpLinkInGanNhat instanceof \Carbon\CarbonInterface
                                ? $rawNgayUpLinkInGanNhat->format('d/m/Y H:i')
                                : \Carbon\Carbon::parse($rawNgayUpLinkInGanNhat)->format('d/m/Y H:i'))
                            : '—';
                        $dieuPhoiPayload = [
                            'url' => route('admin.khach-hang.hop-dong-cuoi.dieu-phoi', $item),
                            'hop_dong_cuoi_id' => $item->id,
                            'ma_hop_dong' => $item->ma_hop_dong,
                            'ngay_chup_thuc_te' => $item->ngay_chup_thuc_te?->format('Y-m-d'),
                            'gio_chup' => $gioChupTxt,
                            'ngay_cuoi_chinh_thuc' => $item->ngay_cuoi_chinh_thuc?->format('Y-m-d'),
                            'dia_diem_chup' => $item->dia_diem_chup ?? '',
                            'ngay_tra_link_demo_chinh_thuc' => $item->ngay_tra_link_demo_chinh_thuc?->format('Y-m-d'),
                            'ngay_tra_link_in_chinh_thuc' => $item->ngay_tra_link_in_chinh_thuc?->format('Y-m-d'),
                            'tho_chup_id' => $item->tho_chup_id,
                            'tho_make_id' => $item->tho_make_id,
                            'tho_edit_id' => $item->tho_edit_id,
                            'ghi_chu_sale' => $item->ghi_chu_sale ?? '',
                        ];
                        $dieuPhoiPayloadAttr = htmlspecialchars(
                            json_encode($dieuPhoiPayload, JSON_UNESCAPED_UNICODE),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        $ekipCardRows = [
                            ['short' => 'Chụp', 'name' => $item->thoChup?->user?->name, 'bg' => 'bg-label-primary'],
                            ['short' => 'Make', 'name' => $item->thoMake?->user?->name, 'bg' => 'bg-label-info'],
                            ['short' => 'Edit', 'name' => $item->thoEdit?->user?->name, 'bg' => 'bg-label-success'],
                        ];
                        $thanhVienSaleRows = $item->thanhVienHopDongCuis
                            ->map(static fn ($tv) => [
                                'ten' => $tv->nhanVien?->user?->name,
                                'vai_tro' => \App\Models\ThanhVienHopDongCuoi::vaiTroLabel($tv->vai_tro),
                            ])
                            ->values()
                            ->all();
                        $saleCardRows = collect($thanhVienSaleRows)->map(static fn (array $row) => [
                            'short' => $row['vai_tro'],
                            'name' => $row['ten'],
                            'bg' => 'bg-label-warning',
                        ])->all();
                    @endphp
                    <div class="col-12 col-md-6 col-lg-6 col-xl-4">
                        <div class="card h-100 dpc-project-card">
                            <div class="card-header pb-4">
                                <div class="d-flex align-items-start">
                                    <div class="d-flex align-items-center min-w-0 flex-grow-1">
                                        <div class="avatar me-3 flex-shrink-0">
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ $initialHd }}</span>
                                        </div>
                                        <div class="me-2 min-w-0">
                                            <h5 class="mb-0 text-truncate" title="{{ e($coupleTitle) }}">
                                                <span class="text-heading">{{ $tenCr }}<span class="text-muted fw-normal"> &amp; </span>{{ $tenCd }}</span>
                                            </h5>
                                            <div class="client-info text-body text-truncate">
                                                <span class="fw-medium">Mã HĐ: </span><span>{{ $item->ma_hop_dong ?? '—' }}</span>
                                            </div>
                                            <div class="client-info text-body-secondary small text-truncate">
                                                <span class="fw-medium">Loại HĐ: </span><span>{{ $tenLoaiHopDong }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-auto flex-shrink-0">
                                        <div class="dropdown z-2">
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="Thao tác hợp đồng">
                                                <i class="icon-base ti tabler-dots-vertical text-body-secondary"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if($tt !== 'da_huy')
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="dropdown-item d-flex align-items-center"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalThanhToanHopDongCuoi"
                                                            data-hop-id="{{ $item->id }}">
                                                            <i class="icon-base ti tabler-cash me-2"></i>
                                                            Thanh toán
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="dropdown-item d-flex align-items-center"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDieuPhoiHopDongCuoi"
                                                            data-payload="{!! $dieuPhoiPayloadAttr !!}">
                                                            <i class="icon-base ti tabler-adjustments me-2"></i>
                                                            Điều phối
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.khach-hang.chinh-sua-hop-dong-cuoi', $item) }}">
                                                            <i class="icon-base ti tabler-edit me-2"></i>
                                                            Chỉnh sửa
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider" /></li>
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="dropdown-item text-danger d-flex align-items-center btn-huy-hop-dong-cuoi"
                                                            data-form-id="form-huy-hdc-grid-{{ $item->id }}"
                                                            data-ma="{{ e($item->ma_hop_dong ?? '') }}">
                                                            <i class="icon-base ti tabler-ban me-2"></i>
                                                            Huỷ hợp đồng
                                                        </button>
                                                    </li>
                                                @else
                                                    <li><span class="dropdown-item-text text-muted small">Hợp đồng đã huỷ</span></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="me-auto mb-4">
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">CR: </span>
                                            <span>{{ $lienHeCr }}</span>
                                        </p>
                                        <p class="mb-1">
                                            <span class="text-heading fw-medium">CD: </span>
                                            <span>{{ $lienHeCd }}</span>
                                        </p>
                                    </div>
                                    <div class="dpc-date-col small mb-4" style="min-width: 0;">
                                        <div class="dpc-date-row dpc-date-row--chup d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-camera-fill dpc-date-icon text-muted" title="Thời gian chụp" aria-hidden="true"></i>
                                            <span>
                                                {{ $ngayChupTxt }}
                                                @if($gioChupTxt)<span class="text-body-secondary"> {{ $gioChupTxt }}</span>@endif
                                            </span>
                                        </div>
                                        <div class="dpc-date-row d-flex align-items-start gap-2 mb-1" title="{{ e($ngayCuoiTitle) }}">
                                            <i class="bi bi-heart-fill dpc-date-icon text-muted" title="Ngày cưới" aria-hidden="true"></i>
                                            <span>{{ $ngayCuoiTxt }}</span>
                                        </div>
                                        <div class="dpc-date-row d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-play-btn dpc-date-icon text-muted" title="Ngày up link demo" aria-hidden="true"></i>
                                            <span>{{ $ngayUpLinkDemoStr }}</span>
                                        </div>
                                        <div class="dpc-date-row d-flex align-items-center gap-2">
                                            <i class="bi bi-printer dpc-date-icon text-muted" title="Ngày up link in" aria-hidden="true"></i>
                                            <span>{{ $ngayUpLinkInStr }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-1 text-body-secondary" title="{{ e($tenDichVu ?: '') }}">{{ str($tenDichVu ?: '—')->limit(120) }}</p>
                            </div>
                            <div class="card-body border-top">
                                <div class="d-flex align-items-center mb-4">
                                    <p class="mb-0"><span class="text-heading fw-medium">Thanh toán: </span>{{ $tyLeThanhToan }}%</p>
                                    <span class="badge {{ $trangThaiClass[$tt] ?? 'bg-label-secondary' }} ms-auto">{{ $trangThaiNhan[$tt] ?? $tt }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-body">Đã thu / Tổng</small>
                                    <small class="text-body">{{ $tyLeThanhToan }}% hoàn thành</small>
                                </div>
                                <div class="progress mb-4 rounded" style="height: 8px;">
                                    <div
                                        class="progress-bar rounded bg-success"
                                        role="progressbar"
                                        style="width: {{ $tyLeThanhToan }}%;"
                                        aria-valuenow="{{ $tyLeThanhToan }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="dpc-ekip-col small mb-1">
                                    <div class="dpc-ekip-row"><span class="text-muted">Chụp:</span> {{ $tenThoChup }}</div>
                                    <div class="dpc-ekip-row"><span class="text-muted">Make:</span> {{ $item->thoMake?->user?->name ?? '—' }}</div>
                                    <div class="dpc-ekip-row"><span class="text-muted">Edit:</span> {{ $item->thoEdit?->user?->name ?? '—' }}</div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2 flex-wrap">
                                        <li class="pr-12"><small class="text-body-secondary">Ekip:</small></li>
                                        @foreach($ekipCardRows as $row)
                                            @php
                                                $nm = $row['name'];
                                                $ini = ($nm !== null && $nm !== '') ? mb_strtoupper(mb_substr($nm, 0, 1)) : '?';
                                            @endphp
                                            <li
                                                class="avatar avatar-sm pull-up"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ $row['short'] }}: {{ e($nm ?: '—') }}">
                                                <span class="avatar-initial rounded-circle {{ $row['bg'] }}">{{ $ini }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if(! empty($saleCardRows))
                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0 z-2 flex-wrap">
                                        <li class="pr-12"><small class="text-body-secondary">Thành viên:</small></li>
                                        @foreach($saleCardRows as $row)
                                            @php
                                                $nm = $row['name'];
                                                $ini = ($nm !== null && $nm !== '') ? mb_strtoupper(mb_substr($nm, 0, 1)) : '?';
                                            @endphp
                                            <li
                                                class="avatar avatar-sm pull-up"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ $row['short'] }}: {{ e($nm ?: '—') }}">
                                                <span class="avatar-initial rounded-circle {{ $row['bg'] }}">{{ $ini }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($tt !== 'da_huy')
                            <form id="form-huy-hdc-grid-{{ $item->id }}"
                                  action="{{ route('admin.khach-hang.hop-dong-cuoi.huy', $item) }}"
                                  method="POST"
                                  class="d-none">
                                @csrf
                                @method('PUT')
                            </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <x-pagination-info :paginator="$hopDongCuois" label="hợp đồng" />
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="modalDieuPhoiHopDongCuoi" tabindex="-1" aria-labelledby="modalDieuPhoiHopDongCuoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDieuPhoiHopDongCuoiLabel">
                    Điều phối hợp đồng
                    <span class="text-muted fw-normal ms-1" id="dpc-modal-ma">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formDieuPhoiHopDongCuoi" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_ngay_chup_thuc_te">Ngày chụp chính thức</label>
                            <input type="text"
                                   class="form-control flatpickr-date-admin"
                                   id="dpc_ngay_chup_thuc_te"
                                   name="ngay_chup_thuc_te"
                                   value=""
                                   placeholder="dd/mm/yyyy"
                                   autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_gio_chup">Giờ chụp</label>
                            <input type="text"
                                   class="form-control flatpickr-time-admin"
                                   id="dpc_gio_chup"
                                   name="gio_chup"
                                   value=""
                                   placeholder="HH:mm"
                                   autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_ngay_cuoi_chinh_thuc">Ngày cưới chính thức</label>
                            <input type="text"
                                   class="form-control flatpickr-date-admin"
                                   id="dpc_ngay_cuoi_chinh_thuc"
                                   name="ngay_cuoi_chinh_thuc"
                                   value=""
                                   placeholder="dd/mm/yyyy"
                                   autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_dia_diem_chup">Địa điểm chụp</label>
                            <input type="text"
                                   class="form-control"
                                   id="dpc_dia_diem_chup"
                                   name="dia_diem_chup"
                                   value=""
                                   placeholder="Nhập địa điểm chụp">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_ngay_tra_link_demo_chinh_thuc">Ngày trả link demo chính thức</label>
                            <input type="text"
                                   class="form-control flatpickr-date-admin"
                                   id="dpc_ngay_tra_link_demo_chinh_thuc"
                                   name="ngay_tra_link_demo_chinh_thuc"
                                   value=""
                                   placeholder="dd/mm/yyyy"
                                   autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_ngay_tra_link_in_chinh_thuc">Ngày trả link in chính thức</label>
                            <input type="text"
                                   class="form-control flatpickr-date-admin"
                                   id="dpc_ngay_tra_link_in_chinh_thuc"
                                   name="ngay_tra_link_in_chinh_thuc"
                                   value=""
                                   placeholder="dd/mm/yyyy"
                                   autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_tho_chup_id">Người chụp</label>
                            <select id="dpc_tho_chup_id" name="tho_chup_id" class="select2-admin form-select" data-placeholder="Chọn người chụp" style="width: 100%;" disabled>
                                <option value="">—</option>
                            </select>
                            {{-- <div class="form-text" id="dpc_tho_chup_hint">Chọn ngày chụp chính thức để phân nhân sự.</div> --}}
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_tho_make_id">Người make</label>
                            <select id="dpc_tho_make_id" name="tho_make_id" class="select2-admin form-select" data-placeholder="Chọn người make" style="width: 100%;" disabled>
                                <option value="">—</option>
                            </select>
                            {{-- <div class="form-text" id="dpc_tho_make_hint">Chọn ngày chụp chính thức để phân nhân sự.</div> --}}
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="dpc_tho_edit_id">Người edit</label>
                            <select id="dpc_tho_edit_id" name="tho_edit_id" class="select2-admin form-select" data-placeholder="Chọn người edit" style="width: 100%;">
                                <option value="">—</option>
                                @foreach($danhSachNhanVien ?? [] as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->user?->name ?? 'Nhân viên #' . $nv->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="dpc_ghi_chu_sale">Ghi chú (sale)</label>
                            <textarea class="form-control"
                                      id="dpc_ghi_chu_sale"
                                      name="ghi_chu_sale"
                                      rows="4"
                                      placeholder="Theo cột ghi chú sale…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu điều phối</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalThanhToanHopDongCuoi" tabindex="-1" aria-labelledby="modalThanhToanHopDongCuoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThanhToanHopDongCuoiLabel">
                    Thanh toán hợp đồng
                    <span class="text-muted fw-normal ms-1" id="tt-modal-ma">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-4">
                        <div class="border rounded p-3 h-100 bg-label-secondary bg-opacity-10">
                            <div class="small text-muted">Tổng tiền</div>
                            <div class="fw-semibold fs-5" id="tt-phi-thu">—</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded p-3 h-100 bg-label-info bg-opacity-10">
                            <div class="small text-muted">Đã thanh toán</div>
                            <div class="fw-semibold fs-5" id="tt-da-tt">—</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded p-3 h-100 bg-label-warning bg-opacity-10">
                            <div class="small text-muted">Còn phải thanh toán</div>
                            <div class="fw-semibold fs-5" id="tt-con-lai">—</div>
                        </div>
                    </div>
                </div>

                <div id="tt-alert" class="alert alert-danger d-none" role="alert"></div>

                <form id="formThanhToanHopDongCuoi" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="tt_so_tien">Số tiền <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="tt_so_tien"
                                   name="so_tien"
                                   min="0.01"
                                   step="0.01"
                                   required
                                   placeholder="Ví dụ: 5000000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tt_hinh_thuc">Hình thức <span class="text-danger">*</span></label>
                            <select class="form-select select2-admin" id="tt_hinh_thuc" name="hinh_thuc_thanh_toan" required>
                                <option value="chuyen_khoan">Chuyển khoản</option>
                                <option value="tien_mat">Tiền mặt</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="tt_ghi_chu">Ghi chú</label>
                            <input type="text" class="form-control" id="tt_ghi_chu" name="ghi_chu" maxlength="2000" placeholder="Tuỳ chọn">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="tt_proof_images">Ảnh chứng từ (tuỳ chọn)</label>
                            <input type="file"
                                   class="form-control"
                                   id="tt_proof_images"
                                   name="proof_images[]"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   multiple>
                            <div class="form-text">JPEG, PNG, WebP hoặc GIF, tối đa 5MB mỗi ảnh.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" id="tt-btn-submit">Ghi nhận thanh toán</button>
                        <span class="small text-muted align-self-center" id="tt-hint-con-lai"></span>
                    </div>
                </form>

                <hr class="my-4">
                <h6 class="mb-2">Lịch sử ghi nhận</h6>
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lần</th>
                                <th>Ngày</th>
                                <th class="text-end">Số tiền</th>
                                <th>Hình thức</th>
                                <th>Ghi chú</th>
                                <th style="min-width: 120px;">Ảnh chứng từ</th>
                            </tr>
                        </thead>
                        <tbody id="tt-lich-su-body">
                            <tr><td colspan="6" class="text-center text-muted py-3">Đang tải…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalXacNhanHuyHopDongCuoi" tabindex="-1" aria-labelledby="modalXacNhanHuyHopDongCuoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanHuyHopDongCuoiLabel">Xác nhận huỷ hợp đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn huỷ hợp đồng <span class="fw-semibold" id="huy-hdc-ma">—</span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Không</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanHuyHopDongCuoi">Huỷ hợp đồng</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script>
(function () {
    if (window.__hopDongCuoiNgayCuoiRangeInit) return;
    window.__hopDongCuoiNgayCuoiRangeInit = true;

    var $ = window.jQuery;
    if (!$ || !$.fn.daterangepicker || typeof moment === 'undefined') return;

    var $inp = $('#hop_dong_cuoi_ngay_cuoi_range');
    var $tu = $('#hop_dong_cuoi_ngay_cuoi_tu');
    var $den = $('#hop_dong_cuoi_ngay_cuoi_den');
    if (!$inp.length) return;

    var tu = ($tu.val() || '').trim();
    var den = ($den.val() || '').trim();
    var mTu = tu ? moment(tu, 'YYYY-MM-DD', true) : null;
    var mDen = den ? moment(den, 'YYYY-MM-DD', true) : null;

    var opts = {
        opens: 'right',
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Áp dụng',
            cancelLabel: 'Hủy',
            fromLabel: 'Từ',
            toLabel: 'Đến',
            customRangeLabel: 'Tùy chọn',
            firstDay: 1
        }
    };

    if (mTu && mTu.isValid() && mDen && mDen.isValid()) {
        opts.startDate = mTu;
        opts.endDate = mDen;
    } else if (mTu && mTu.isValid()) {
        opts.startDate = mTu;
        opts.endDate = mTu.clone();
    } else if (mDen && mDen.isValid()) {
        opts.startDate = mDen.clone();
        opts.endDate = mDen.clone();
    }

    $inp.daterangepicker(opts);

    function syncLabel() {
        var t = ($tu.val() || '').trim();
        var d = ($den.val() || '').trim();
        var a = t ? moment(t, 'YYYY-MM-DD', true) : null;
        var b = d ? moment(d, 'YYYY-MM-DD', true) : null;
        if (a && a.isValid() && b && b.isValid()) {
            $inp.val(a.format('DD/MM/YYYY') + ' - ' + b.format('DD/MM/YYYY'));
        } else if (a && a.isValid()) {
            $inp.val(a.format('DD/MM/YYYY') + ' - ' + a.format('DD/MM/YYYY'));
        } else if (b && b.isValid()) {
            $inp.val(b.format('DD/MM/YYYY') + ' - ' + b.format('DD/MM/YYYY'));
        } else {
            $inp.val('');
        }
    }

    syncLabel();

    $inp.on('apply.daterangepicker', function (ev, picker) {
        $tu.val(picker.startDate.format('YYYY-MM-DD'));
        $den.val(picker.endDate.format('YYYY-MM-DD'));
        syncLabel();
    });

    $inp.on('cancel.daterangepicker', function () {
        $tu.val('');
        $den.val('');
        $inp.val('');
    });
})();

document.addEventListener('DOMContentLoaded', function () {
    (function () {
        var root = document.getElementById('dpc-list-toolbar-area');
        if (!root) {
            return;
        }

        function selectedLocFilters() {
            var keys = [];
            root.querySelectorAll('.dpc-loc-filter:checked').forEach(function (el) {
                var v = String(el.value || '').trim();
                if (v) {
                    keys.push(v);
                }
            });
            return keys;
        }

        function updateLocClearButton() {
            var btn = document.getElementById('dpcLocClear');
            if (!btn) {
                return;
            }
            btn.classList.toggle('d-none', selectedLocFilters().length === 0);
        }

        function navigateWithLocFilters() {
            var url = new URL(window.location.href);
            url.searchParams.delete('loc');
            url.searchParams.delete('loc[]');
            url.searchParams.delete('page');
            selectedLocFilters().forEach(function (key) {
                url.searchParams.append('loc[]', key);
            });
            window.location.assign(url.toString());
        }

        root.querySelectorAll('.dpc-loc-filter').forEach(function (el) {
            el.addEventListener('change', navigateWithLocFilters);
        });

        var locClearBtn = document.getElementById('dpcLocClear');
        if (locClearBtn) {
            locClearBtn.addEventListener('click', function () {
                root.querySelectorAll('.dpc-loc-filter').forEach(function (cb) {
                    cb.checked = false;
                });
                navigateWithLocFilters();
            });
        }

        updateLocClearButton();
    })();

    (function () {
        var LS_KEY = 'adminHopDongCuoiListView';
        var tableWrap = document.getElementById('dpc-view-table-wrap');
        var gridWrap = document.getElementById('dpc-view-grid-wrap');
        var btnTable = document.getElementById('dpc-view-btn-table');
        var btnGrid = document.getElementById('dpc-view-btn-grid');
        if (!tableWrap || !gridWrap || !btnTable || !btnGrid) {
            return;
        }

        function setView(mode) {
            var isGrid = mode === 'grid';
            tableWrap.classList.toggle('d-none', isGrid);
            gridWrap.classList.toggle('d-none', !isGrid);
            btnTable.classList.toggle('btn-primary', !isGrid);
            btnTable.classList.toggle('btn-outline-secondary', isGrid);
            btnTable.classList.toggle('active', !isGrid);
            btnGrid.classList.toggle('btn-primary', isGrid);
            btnGrid.classList.toggle('btn-outline-secondary', !isGrid);
            btnGrid.classList.toggle('active', isGrid);
            btnTable.setAttribute('aria-pressed', (!isGrid).toString());
            btnGrid.setAttribute('aria-pressed', isGrid.toString());
            try {
                localStorage.setItem(LS_KEY, mode);
            } catch (e) { /* ignore */ }
        }

        var saved = null;
        try {
            saved = localStorage.getItem(LS_KEY);
        } catch (e) { /* ignore */ }
        setView(saved === 'grid' ? 'grid' : 'table');

        btnTable.addEventListener('click', function () {
            setView('table');
        });
        btnGrid.addEventListener('click', function () {
            setView('grid');
        });
    })();

    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }

    // Huỷ hợp đồng cưới: mở modal xác nhận, sau đó submit form PUT
    (function () {
        var modalEl = document.getElementById('modalXacNhanHuyHopDongCuoi');
        var btnConfirm = document.getElementById('btnXacNhanHuyHopDongCuoi');
        var maEl = document.getElementById('huy-hdc-ma');
        if (!modalEl || !btnConfirm) return;

        var formId = null;
        document.querySelectorAll('.btn-huy-hop-dong-cuoi').forEach(function (btn) {
            btn.addEventListener('click', function () {
                formId = this.getAttribute('data-form-id');
                var ma = this.getAttribute('data-ma') || '—';
                if (maEl) maEl.textContent = ma;
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });

        btnConfirm.addEventListener('click', function () {
            if (formId) {
                var form = document.getElementById(formId);
                if (form) form.submit();
            }
            var inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) inst.hide();
            formId = null;
        });
    })();

    var $ = window.jQuery;
    if (!$ || !$.fn.select2) {
        console.warn('[DPC] Bỏ qua script điều phối: jQuery hoặc Select2 không có ($=', !!$, ', select2=', !!($ && $.fn && $.fn.select2), ')');
        return;
    }

    function dpcLog(step, detail) {
        if (detail !== undefined) {
            console.log('[DPC]', step, detail);
        } else {
            console.log('[DPC]', step);
        }
    }

    dpcLog('1 DOMContentLoaded: khởi tạo module điều phối modal');

    var DPC_NV_URL_TMPL = @json(route('admin.khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay', ['hopDongCuoi' => '__HDC__']));
    var dpcHopId = null;

    function dpcNvUrl(hopId) {
        return DPC_NV_URL_TMPL.split('__HDC__').join(String(hopId));
    }

    function dpcBindSelect2($sel, placeholder) {
        var $modal = $sel.closest('.modal');
        function renderOpt(opt) {
            if (!opt || !opt.element) return opt && opt.text ? opt.text : '';
            var el = opt.element;
            var busy = el && el.dataset && el.dataset.busy === '1';
            if (!busy) return opt.text;
            var $wrap = $('<span></span>');
            $wrap.text(opt.text + ' ');
            $wrap.append('<span class="badge bg-label-warning dpc-busy-flag ms-1">Bận</span>');
            return $wrap;
        }
        var opts = {
            placeholder: placeholder || 'Chọn...',
            allowClear: true,
            width: '100%',
            templateResult: renderOpt,
            templateSelection: renderOpt,
            escapeMarkup: function (m) { return m; }
        };
        if ($modal.length) opts.dropdownParent = $modal;
        if ($sel.data('select2')) {
            $sel.select2('destroy');
        }
        $sel.select2(opts);
    }

    function dpcSetChupMakeDisabled(disabled) {
        ['#dpc_tho_chup_id', '#dpc_tho_make_id'].forEach(function (sel) {
            var $el = $(sel);
            if (!$el.length) return;
            $el.prop('disabled', disabled);
        });
        var hint = 'Chọn ngày chụp chính thức để phân nhân sự.';
        var busyHint = 'Nhân viên đã có lịch cùng ngày vẫn chọn được (có cờ "Bận").';
        // $('#dpc_tho_chup_hint').text(disabled ? hint : busyHint);
        // $('#dpc_tho_make_hint').text(disabled ? hint : busyHint);
    }

    function dpcRebuildChupMake(items, wantChup, wantMake) {
        function fill($sel, ph, want) {
            var prev = want != null && want !== '' ? String(want) : '';
            if ($sel.data('select2')) {
                $sel.select2('destroy');
            }
            $sel.empty().append(new Option('—', '', false, false));
            (items || []).forEach(function (it) {
                var o = new Option(it.ten, String(it.id), false, false);
                // Cho phép chọn nhân viên bận, chỉ gắn cờ để nhận biết.
                if (it.disabled) o.dataset.busy = '1';
                $sel.append(o);
            });
            var $match = $sel.find('option').filter(function () {
                return String($(this).val()) === prev;
            });
            var pick = $match.length ? prev : '';
            dpcBindSelect2($sel, ph);
            $sel.val(pick || null).trigger('change');
        }
        fill($('#dpc_tho_chup_id'), 'Chọn người chụp', wantChup);
        fill($('#dpc_tho_make_id'), 'Chọn người make', wantMake);
    }

    function dpcFetchChupMake(ymd, wantChup, wantMake) {
        if (!dpcHopId || !ymd) {
            dpcLog('7a dpcFetchChupMake: bỏ qua (thiếu hopId hoặc ngày)', { dpcHopId: dpcHopId, ymd: ymd });
            dpcSetChupMakeDisabled(true);
            dpcRebuildChupMake([], '', '');
            return;
        }
        var url = dpcNvUrl(dpcHopId) + '?ngay=' + encodeURIComponent(ymd);
        dpcLog('7b dpcFetchChupMake: bắt đầu fetch', { url: url, wantChup: wantChup, wantMake: wantMake });
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (r) {
                dpcLog('7c dpcFetchChupMake: HTTP response', { ok: r.ok, status: r.status, statusText: r.statusText });
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                dpcLog('7d dpcFetchChupMake: JSON OK', { soNhanVien: (data.items || []).length });
                dpcSetChupMakeDisabled(false);
                dpcRebuildChupMake(data.items || [], wantChup, wantMake);
            })
            .catch(function (err) {
                console.error('[DPC] 7e dpcFetchChupMake: lỗi', err);
                dpcSetChupMakeDisabled(true);
                dpcRebuildChupMake([], '', '');
            });
    }

    function dpcSyncChupMakeTheoNgay(ymd, wantChup, wantMake) {
        var argsN = arguments.length;
        ymd = (ymd || '').trim();
        dpcLog('6 dpcSyncChupMakeTheoNgay: vào hàm', {
            ymd: ymd,
            dpcHopId: dpcHopId,
            argsCount: argsN,
            wantChupArg: argsN >= 2 ? wantChup : '(lấy từ select)',
            wantMakeArg: argsN >= 3 ? wantMake : '(lấy từ select)'
        });
        if (!ymd || !dpcHopId) {
            dpcLog('6a dpcSyncChupMakeTheoNgay: dừng — chưa có ngày hoặc chưa có hop_dong_cuoi_id', { ymd: ymd, dpcHopId: dpcHopId });
            dpcSetChupMakeDisabled(true);
            dpcRebuildChupMake([], '', '');
            return;
        }
        if (arguments.length < 2) {
            wantChup = $('#dpc_tho_chup_id').val();
        }
        if (arguments.length < 3) {
            wantMake = $('#dpc_tho_make_id').val();
        }
        dpcLog('6b dpcSyncChupMakeTheoNgay: gọi fetch', { wantChup: wantChup, wantMake: wantMake });
        dpcFetchChupMake(ymd, wantChup, wantMake);
    }

    /**
     * Flatpickr đã gắn onChange lúc khởi tạo; fp.set('onChange') sau đó thường không chạy.
     * Flatpickr có dispatch sự kiện native "change" trên input gốc — lắng nghe để gọi API.
     */
    function dpcAttachNgayChupThucTeListenersOnce() {
        var el = document.getElementById('dpc_ngay_chup_thuc_te');
        if (!el) {
            dpcLog('3 dpcAttachNgayChupThucTeListenersOnce: không tìm thấy #dpc_ngay_chup_thuc_te');
            return;
        }
        if (el._dpcNgayChupNativeBound) {
            console.debug('[DPC] 3 dpcAttachNgayChupThucTeListenersOnce: đã gắn listener trước đó (bình thường khi mở lại modal)');
            return;
        }
        el._dpcNgayChupNativeBound = true;
        dpcLog('3 dpcAttachNgayChupThucTeListenersOnce: gắn change/input', {
            coFlatpickr: !!el._flatpickr,
            classList: el.className
        });
        var deb;
        function scheduleSync(ev) {
            dpcLog('4 scheduleSync (từ ' + (ev && ev.type ? ev.type : '?') + '): value tại chỗ', {
                value: el.value,
                dpcHopId: dpcHopId
            });
            clearTimeout(deb);
            deb = setTimeout(function () {
                deb = null;
                dpcLog('5 scheduleSync debounce 20ms: gọi dpcSyncChupMakeTheoNgay', (el.value || '').trim());
                dpcSyncChupMakeTheoNgay((el.value || '').trim());
            }, 20);
        }
        el.addEventListener('change', scheduleSync);
        el.addEventListener('input', scheduleSync);
    }

    var modalDieuPhoi = document.getElementById('modalDieuPhoiHopDongCuoi');
    if (modalDieuPhoi) {
        dpcLog('2 Tìm thấy #modalDieuPhoiHopDongCuoi, gắn listener ngày chụp');
        dpcAttachNgayChupThucTeListenersOnce();

        modalDieuPhoi.addEventListener('shown.bs.modal', function (event) {
            dpcLog('8 shown.bs.modal: mở modal');
            dpcAttachNgayChupThucTeListenersOnce();
            var btn = event.relatedTarget;
            if (!btn || !btn.getAttribute('data-payload')) {
                dpcLog('8a shown.bs.modal: không có relatedTarget hoặc data-payload — thoát (API không chạy từ đây)');
                return;
            }
            var p;
            try {
                p = JSON.parse(btn.getAttribute('data-payload'));
            } catch (e) {
                console.error('[DPC] 8b JSON payload lỗi', e);
                return;
            }
            dpcLog('8c Payload đã parse', { hop_dong_cuoi_id: p.hop_dong_cuoi_id, ma: p.ma_hop_dong, ngay_chup_thuc_te: p.ngay_chup_thuc_te });
            var form = document.getElementById('formDieuPhoiHopDongCuoi');
            if (!form || !p.url) {
                dpcLog('8d Thiếu form hoặc p.url — thoát');
                return;
            }
            form.action = p.url;
            dpcHopId = p.hop_dong_cuoi_id != null && p.hop_dong_cuoi_id !== ''
                ? parseInt(p.hop_dong_cuoi_id, 10)
                : null;
            if (dpcHopId === null || Number.isNaN(dpcHopId)) {
                dpcHopId = null;
            }
            dpcLog('8e Đã gán dpcHopId', dpcHopId);
            var maEl = document.getElementById('dpc-modal-ma');
            if (maEl) maEl.textContent = p.ma_hop_dong ? '(' + p.ma_hop_dong + ')' : '—';
            function setFp(id, ymd) {
                var el = document.getElementById(id);
                if (!el) {
                    dpcLog('8f setFp: không có element', id);
                    return;
                }
                if (el._flatpickr) {
                    dpcLog('8f setFp: flatpickr.setDate/clear', { id: id, ymd: ymd || '(clear)' });
                    if (ymd) el._flatpickr.setDate(ymd, false);
                    else el._flatpickr.clear();
                } else {
                    dpcLog('8f setFp: không có _flatpickr, gán value thủ công', { id: id, ymd: ymd });
                    el.value = ymd || '';
                }
            }
            setFp('dpc_ngay_chup_thuc_te', p.ngay_chup_thuc_te || '');
            setFp('dpc_ngay_cuoi_chinh_thuc', p.ngay_cuoi_chinh_thuc || '');
            setFp('dpc_ngay_tra_link_demo_chinh_thuc', p.ngay_tra_link_demo_chinh_thuc || '');
            setFp('dpc_ngay_tra_link_in_chinh_thuc', p.ngay_tra_link_in_chinh_thuc || '');
            var gioChupEl = document.getElementById('dpc_gio_chup');
            if (gioChupEl) {
                if (window.setAdminTimeInput) window.setAdminTimeInput(gioChupEl, p.gio_chup != null ? String(p.gio_chup) : '');
                else gioChupEl.value = p.gio_chup != null ? String(p.gio_chup) : '';
            }
            var diaDiemChupEl = document.getElementById('dpc_dia_diem_chup');
            if (diaDiemChupEl) diaDiemChupEl.value = p.dia_diem_chup != null ? String(p.dia_diem_chup) : '';

            var nChup = document.getElementById('dpc_ngay_chup_thuc_te');
            var ymd = (nChup && nChup.value ? nChup.value : '').trim();
            dpcLog('8g Sau setFp ngày chụp: value input', { ymd: ymd, nChupExists: !!nChup });
            dpcSyncChupMakeTheoNgay(ymd, p.tho_chup_id, p.tho_make_id);

            $('#dpc_tho_edit_id').val(p.tho_edit_id != null && p.tho_edit_id !== '' ? String(p.tho_edit_id) : '').trigger('change');
            var gc = document.getElementById('dpc_ghi_chu_sale');
            if (gc) gc.value = p.ghi_chu_sale != null ? String(p.ghi_chu_sale) : '';
            dpcLog('8h shown.bs.modal: xong một vòng');
        });

        modalDieuPhoi.addEventListener('hidden.bs.modal', function () {
            dpcLog('9 hidden.bs.modal: reset dpcHopId');
            dpcHopId = null;
        });
    } else {
        console.warn('[DPC] 2 Không có #modalDieuPhoiHopDongCuoi trong DOM — không gắn điều phối');
    }
});

(function () {
    if (window.__hopDongCuoiThanhToanInit) return;
    window.__hopDongCuoiThanhToanInit = true;

    var TT_GET_TMPL = @json($routeTtGet);
    var TT_POST_TMPL = @json($routeTtPost);
    var CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || @json(csrf_token());

    function ttUrl(id) {
        return TT_GET_TMPL.split('999999999').join(String(id));
    }
    function ttPostUrl(id) {
        return TT_POST_TMPL.split('999999999').join(String(id));
    }

    function fmtMoney(n) {
        var x = Number(n);
        if (Number.isNaN(x)) return '—';
        return new Intl.NumberFormat('vi-VN').format(Math.round(x)) + ' đ';
    }

    var hinhThucLabel = { chuyen_khoan: 'Chuyển khoản', tien_mat: 'Tiền mặt' };

    var ttHopId = null;
    var ttSubmitting = false;
    var modalThanhToan = document.getElementById('modalThanhToanHopDongCuoi');
    if (!modalThanhToan) return;

    function ttHideAlert() {
        var el = document.getElementById('tt-alert');
        if (el) {
            el.classList.add('d-none');
            el.textContent = '';
        }
    }

    function ttShowAlert(msg) {
        var el = document.getElementById('tt-alert');
        if (el) {
            el.textContent = msg;
            el.classList.remove('d-none');
        }
    }

    function ttFillFromJson(data) {
        var ma = document.getElementById('tt-modal-ma');
        if (ma) ma.textContent = data.ma_hop_dong ? '(' + data.ma_hop_dong + ')' : '—';
        var p1 = document.getElementById('tt-phi-thu');
        var p2 = document.getElementById('tt-da-tt');
        var p3 = document.getElementById('tt-con-lai');
        if (p1) p1.textContent = fmtMoney(data.phai_thu);
        if (p2) p2.textContent = fmtMoney(data.da_thanh_toan);
        if (p3) p3.textContent = fmtMoney(data.con_lai);

        var hint = document.getElementById('tt-hint-con-lai');
        var btn = document.getElementById('tt-btn-submit');
        var soTienInp = document.getElementById('tt_so_tien');
        var conLai = Number(data.con_lai);
        if (conLai <= 0) {
            if (hint) hint.textContent = 'Khách hàng đã thanh toán đầy đủ';
            if (btn) btn.disabled = true;
            if (soTienInp) {
                soTienInp.removeAttribute('max');
                soTienInp.disabled = true;
            }
        } else {
            if (hint) hint.textContent = 'Tối đa: ' + fmtMoney(conLai);
            if (btn) btn.disabled = false;
            if (soTienInp) {
                soTienInp.disabled = false;
                soTienInp.max = String(conLai);
            }
        }

        var tbody = document.getElementById('tt-lich-su-body');
        if (!tbody) return;
        tbody.innerHTML = '';
        if (!data.lich_su || !data.lich_su.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Chưa có lần ghi nhận qua chức năng này.</td></tr>';
            return;
        }
        data.lich_su.forEach(function (row) {
            var tr = document.createElement('tr');
            var ngay = row.ngay_thanh_toan || '';
            if (ngay && ngay.indexOf('-') === 4) {
                var p = ngay.split('-');
                ngay = p[2] + '/' + p[1] + '/' + p[0];
            }
            var td1 = document.createElement('td');
            td1.textContent = String(row.lan_thanh_toan != null ? row.lan_thanh_toan : '');
            var td2 = document.createElement('td');
            td2.textContent = ngay;
            var td3 = document.createElement('td');
            td3.className = 'text-end';
            td3.textContent = fmtMoney(row.so_tien);
            var td4 = document.createElement('td');
            td4.textContent = hinhThucLabel[row.hinh_thuc_thanh_toan] || row.hinh_thuc_thanh_toan || '—';
            var td5 = document.createElement('td');
            td5.textContent = row.ghi_chu ? String(row.ghi_chu) : '—';
            var td6 = document.createElement('td');
            var pub = row.proof_urls_public || [];
            if (pub.length) {
                td6.className = 'small';
                pub.forEach(function (url) {
                    var a = document.createElement('a');
                    a.href = url;
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    a.title = 'Xem ảnh';
                    var img = document.createElement('img');
                    img.src = url;
                    img.alt = '';
                    img.className = 'rounded border me-1 mb-1';
                    img.style.width = '44px';
                    img.style.height = '44px';
                    img.style.objectFit = 'cover';
                    a.appendChild(img);
                    td6.appendChild(a);
                });
            } else {
                td6.textContent = '—';
            }
            tr.appendChild(td1);
            tr.appendChild(td2);
            tr.appendChild(td3);
            tr.appendChild(td4);
            tr.appendChild(td5);
            tr.appendChild(td6);
            tbody.appendChild(tr);
        });
    }

    function ttLoad(id) {
        ttHideAlert();
        fetch(ttUrl(id), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(ttFillFromJson)
            .catch(function (e) {
                console.error(e);
                ttShowAlert('Không tải được dữ liệu thanh toán.');
            });
    }

    modalThanhToan.addEventListener('shown.bs.modal', function (ev) {
        var b = ev.relatedTarget;
        if (!b || !b.getAttribute('data-hop-id')) return;
        ttHopId = parseInt(b.getAttribute('data-hop-id'), 10);
        if (Number.isNaN(ttHopId)) return;
        ttLoad(ttHopId);
        var st = document.getElementById('tt_so_tien');
        if (st) {
            st.value = '';
            st.disabled = false;
        }
        var ht = document.getElementById('tt_hinh_thuc');
        if (ht) ht.value = 'chuyen_khoan';
        var gc = document.getElementById('tt_ghi_chu');
        if (gc) gc.value = '';
        var proofInp = document.getElementById('tt_proof_images');
        if (proofInp) proofInp.value = '';
    });

    modalThanhToan.addEventListener('hidden.bs.modal', function () {
        ttHopId = null;
    });

    var formTt = document.getElementById('formThanhToanHopDongCuoi');
    if (formTt) {
        formTt.addEventListener('submit', function (ev) {
            ev.preventDefault();
            if (!ttHopId) return;
            if (ttSubmitting) return;
            ttSubmitting = true;
            ttHideAlert();
            var submitBtn = document.getElementById('tt-btn-submit');
            if (submitBtn) submitBtn.disabled = true;
            var fd = new FormData(formTt);
            fd.append('_token', CSRF_TOKEN);
            fetch(ttPostUrl(ttHopId), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: fd,
                credentials: 'same-origin'
            })
                .then(function (r) {
                    if (r.status === 422) {
                        return r.json().then(function (j) {
                            throw j;
                        });
                    }
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(ttFillFromJson)
                .then(function () {
                    formTt.reset();
                    var ht = document.getElementById('tt_hinh_thuc');
                    if (ht) ht.value = 'chuyen_khoan';
                    var proofInp = document.getElementById('tt_proof_images');
                    if (proofInp) proofInp.value = '';
                })
                .catch(function (err) {
                    if (err && err.errors) {
                        var msgs = [];
                        Object.keys(err.errors).forEach(function (k) {
                            (err.errors[k] || []).forEach(function (m) {
                                msgs.push(m);
                            });
                        });
                        if (msgs.length) {
                            ttShowAlert(msgs.join(' '));
                            return;
                        }
                    }
                    if (err && err.errors && err.errors.so_tien && err.errors.so_tien[0]) {
                        ttShowAlert(err.errors.so_tien[0]);
                    } else if (err && err.message && typeof err.message === 'string') {
                        ttShowAlert(err.message);
                    } else {
                        ttShowAlert('Lưu không thành công.');
                    }
                })
                .finally(function () {
                    ttSubmitting = false;
                    var btn = document.getElementById('tt-btn-submit');
                    if (btn) {
                        var soTien = document.getElementById('tt_so_tien');
                        btn.disabled = !!(soTien && soTien.disabled);
                    }
                });
        });
    }
})();
</script>
@endpush
