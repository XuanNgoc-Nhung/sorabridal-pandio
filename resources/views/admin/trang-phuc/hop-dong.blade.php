@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\HopDongChoThueTrangPhuc::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('search')
        || $sapXepTheo !== $sapXepTheoMacDinh
        || $thuTu !== 'desc';
@endphp
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
        <form action="{{ route('admin.trang-phuc.hop-dong') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên khách hàng hoặc SĐT</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên hoặc số điện thoại...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\HopDongChoThueTrangPhuc::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.trang-phuc.hop-dong') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách hợp đồng thuê trang phục</span>
            <span data-bs-toggle="tooltip" title="Thêm hợp đồng mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemHopDong">
                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                </button>
            </span>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Tên khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Sản phẩm</th>
                        <th class="text-center" style="width: 90px;">Số ngày thuê</th>
                        <th class="text-end hd-thanh-toan-col">Thanh toán</th>
                        <th>Thời gian thuê</th>
                        <th>Thời gian trả chính thức</th>
                        <th>Ghi chú</th>
                        <th class="text-center" style="width: 100px;">Trạng thái</th>
                        <th>Người cho thuê</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $trangThaiLabels = [
                            0 => 'Đang diễn ra',
                            1 => 'Hoàn thành',
                            2 => 'Đã huỷ',
                        ];
                        $trangThaiBadges = [
                            0 => 'bg-label-warning',
                            1 => 'bg-label-success',
                            2 => 'bg-label-secondary',
                        ];
                        $tt = (int) ($item->trang_thai ?? 0);
                        $trangThaiLabel = $trangThaiLabels[$tt] ?? (string) $tt;
                        $trangThaiBadge = $trangThaiBadges[$tt] ?? 'bg-label-secondary';
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $item->ten_khach_hang ?? '—' }}</span></td>
                        <td>{{ $item->sdt_khach_hang ?? '—' }}</td>
                        @php
                            $hopDongSanPhamIds = ($item->sanPhamChoThue ?? collect())->pluck('san_pham_id')->unique()->sort()->values();
                            $tenSanPhams = ($item->sanPhamChoThue ?? collect())
                                ->map(fn ($spct) => $spct->sanPham?->ten_san_pham)
                                ->filter()
                                ->unique()
                                ->values();
                            $fmtTien = fn ($v) => number_format(max(0, (float) ($v ?? 0)), 0, ',', '.') . ' đ';
                            $tongTienHd = round((float) ($item->tong_tien ?? 0), 2);
                            $tienCocHd = round((float) ($item->tien_coc ?? 0), 2);
                            $daThuHd = min($tongTienHd, max(0, $tienCocHd));
                            $conThieuHd = max(0, round($tongTienHd - $daThuHd, 2));
                            if ($tongTienHd > 0) {
                                $tyLeDaThuHd = min(100, max(0, ($daThuHd / $tongTienHd) * 100));
                                $tyLeThieuHd = min(100, max(0, ($conThieuHd / $tongTienHd) * 100));
                                if (round($tyLeDaThuHd + $tyLeThieuHd, 2) > 100) {
                                    $tyLeThieuHd = max(0, 100 - $tyLeDaThuHd);
                                }
                            } else {
                                $tyLeDaThuHd = 0;
                                $tyLeThieuHd = 0;
                            }
                            $tyLeDaThuCss = rtrim(rtrim(number_format($tyLeDaThuHd, 2, '.', ''), '0'), '.');
                            $tyLeThieuCss = rtrim(rtrim(number_format($tyLeThieuHd, 2, '.', ''), '0'), '.');
                            $tyLeDaThuTooltip = number_format(round($tyLeDaThuHd, 2), 2, ',', '');
                            $hdProgressTooltipHtml = 'Tổng: '.$fmtTien($tongTienHd)
                                .'<br>Đã nhận: '.$fmtTien($daThuHd)
                                .'<br>Còn lại: '.$fmtTien($conThieuHd)
                                .'<br>Tiến độ: '.$tyLeDaThuTooltip.'%';
                        @endphp
                        <td>
                            @if($tenSanPhams->isNotEmpty())
                            <ul class="mb-0 ps-3 small lh-sm">
                                @foreach($tenSanPhams as $tenSp)
                                <li><p>{{ $tenSp }}</p></li>
                                @endforeach
                            </ul>
                            @else
                            —
                            @endif
                        </td>
                        <td class="text-center">{{ $item->so_ngay_thue ?? 0 }}</td>
                        <td class="text-end hd-thanh-toan-cell">
                            <div class="hd-thanh-toan-cell__rows small lh-sm">
                                <div class="d-flex justify-content-between align-items-baseline gap-2">
                                    <span class="text-muted">Tổng</span>
                                    <span class="fw-semibold text-nowrap">{{ $fmtTien($tongTienHd) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-baseline gap-2 mt-1">
                                    <span class="text-muted">Đã nhận</span>
                                    <span class="text-success text-nowrap">{{ $fmtTien($tienCocHd) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-baseline gap-2 mt-1">
                                    <span class="text-muted">Còn lại</span>
                                    <span class="text-danger fw-medium text-nowrap">{{ $fmtTien($conThieuHd) }}</span>
                                </div>
                            </div>
                            <div class="progress hd-thanh-toan-progress mt-2"
                                 data-bs-toggle="tooltip"
                                 data-bs-placement="top"
                                 data-bs-html="true"
                                 data-bs-title="{!! htmlspecialchars($hdProgressTooltipHtml, ENT_QUOTES, 'UTF-8') !!}">
                                @if($tyLeDaThuHd > 0)
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{ $tyLeDaThuCss }}%;"
                                     aria-valuenow="{{ $tyLeDaThuCss }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     aria-label="Đã thu"></div>
                                @endif
                                @if($tyLeThieuHd > 0)
                                <div class="progress-bar bg-danger"
                                     role="progressbar"
                                     style="width: {{ $tyLeThieuCss }}%;"
                                     aria-valuenow="{{ $tyLeThieuCss }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     aria-label="Còn thiếu"></div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item->ngay_thue || $item->ngay_tra_du_kien)
                                {{ $item->ngay_thue?->format('d/m/Y') ?? '—' }}
                                <span class="text-muted">→</span>
                                {{ $item->ngay_tra_du_kien?->format('d/m/Y') ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $item->ngay_tra_chinh_thuc?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->ghi_chu ?? '—', 30) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $trangThaiBadge }}">{{ $trangThaiLabel }}</span>
                        </td>
                        <td>{{ $item->nguoiChoThue?->name ?? '—' }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @if($tt === 0)
                                    <a class="dropdown-item btn-sua-hop-dong"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaHopDong"
                                       data-url="{{ route('admin.trang-phuc.update-hop-dong', $item) }}"
                                       data-ten-khach-hang="{{ e($item->ten_khach_hang ?? '') }}"
                                       data-so-dien-thoai="{{ e($item->sdt_khach_hang ?? '') }}"
                                       data-trang-phuc-ids="{{ $hopDongSanPhamIds->implode(',') }}"
                                       data-so-luong-thue="1"
                                       data-tong-tien="{{ $item->tong_tien ?? '' }}"
                                       data-thoi-gian-bat-dau="{{ $item->ngay_thue?->format('Y-m-d') ?? '' }}"
                                       data-thoi-gian-du-kien-tra="{{ $item->ngay_tra_du_kien?->format('Y-m-d') ?? '' }}"
                                       data-thoi-gian-tra-chinh-thuc="{{ $item->ngay_tra_chinh_thuc?->format('Y-m-d') ?? '' }}"
                                       data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                       data-trang-thai="{{ (int) ($item->trang_thai ?? 0) }}"
                                       data-tien-coc="{{ $item->tien_coc ?? '' }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    @else
                                    <span class="dropdown-item disabled text-muted user-select-none"
                                          title="Không thể sửa hợp đồng đã hoàn thành hoặc đã huỷ.">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </span>
                                    @endif
                                    <a class="dropdown-item btn-cap-nhat-trang-thai-hop-dong"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalCapNhatTrangThaiHopDong"
                                       data-url="{{ route('admin.trang-phuc.update-hop-dong-trang-thai', $item) }}"
                                       data-ten-khach="{{ e($item->ten_khach_hang ?? '') }}"
                                       data-trang-thai="{{ (int) ($item->trang_thai ?? 0) }}"
                                       data-ngay-tra-chinh-thuc="{{ $item->ngay_tra_chinh_thuc?->format('Y-m-d') ?? '' }}"
                                       data-tong-tien="{{ $item->tong_tien ?? '' }}"
                                       data-tien-coc="{{ $item->tien_coc ?? '' }}">
                                        <i class="fa-solid fa-clipboard-check me-2"></i> Cập nhật trạng thái
                                    </a>
                                    <form id="form-xoa-hd-{{ $item->id }}" action="{{ route('admin.trang-phuc.destroy-hop-dong', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-hop-dong" data-form-id="form-xoa-hd-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 text-muted">Chưa có hợp đồng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="hợp đồng" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới hợp đồng --}}
<div class="modal fade" id="modalThemHopDong" tabindex="-1" aria-labelledby="modalThemHopDongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-hop-dong">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemHopDongLabel">Thêm hợp đồng thuê trang phục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.trang-phuc.store-hop-dong') }}" method="POST" id="formThemHopDong" novalidate>
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="them_ten_khach_hang">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ten_khach_hang" name="ten_khach_hang" value="{{ old('ten_khach_hang') }}" placeholder="Nhập tên khách hàng" required autocomplete="off">
                            <div class="invalid-feedback" id="them_fb_ten_khach_hang"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="them_so_dien_thoai">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" placeholder="0912345678" maxlength="20" required autocomplete="off">
                            <div class="invalid-feedback" id="them_fb_so_dien_thoai"></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                            <input type="text"
                                   class="form-control"
                                   id="them_ghi_chu"
                                   name="ghi_chu"
                                   value="{{ old('ghi_chu') }}"
                                   maxlength="500"
                                   placeholder="Ghi chú…"
                                   autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="them_thoi_gian_bat_dau">Ngày thuê (bắt đầu) <span class="text-danger">*</span></label>
                            <input type="text" class="flatpickr-date-admin form-control" id="them_thoi_gian_bat_dau" name="thoi_gian_thue_bat_dau" value="{{ old('thoi_gian_thue_bat_dau') }}" placeholder="dd/mm/yyyy" required autocomplete="off">
                            <div class="invalid-feedback" id="them_fb_thoi_gian_bat_dau"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="them_thoi_gian_du_kien_tra">Ngày dự kiến trả <span class="text-danger">*</span></label>
                            <input type="text" class="flatpickr-date-admin form-control" id="them_thoi_gian_du_kien_tra" name="thoi_gian_du_kien_tra" value="{{ old('thoi_gian_du_kien_tra') }}" placeholder="dd/mm/yyyy" required autocomplete="off">
                            <div class="invalid-feedback" id="them_fb_thoi_gian_du_kien_tra"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3" id="them_wrap_so_ngay_thue">
                            <label class="form-label" for="them_so_ngay_thue_display">Số ngày thuê</label>
                            <input type="text"
                                   class="form-control bg-body-secondary"
                                   id="them_so_ngay_thue_display"
                                   value="0"
                                   readonly
                                   tabindex="-1"
                                   autocomplete="off"
                                   aria-readonly="true"
                                   title="Tự động theo khoảng ngày thuê — dùng tham khảo, không nhân vào tiền thuê">
                        </div>
                        <div class="col-12">
                            @php
                                $oldTrangPhucThem = old('trang_phuc', []);
                                $sanPhamCatalog = [];
                                foreach ($danhSachSanPham ?? [] as $sp) {
                                    $sdDates = $trangPhucSuDungTuHomNay[$sp->id] ?? [];
                                    $hinhPath = $sp->hinh_anh ?? null;
                                    $sanPhamCatalog[] = [
                                        'id' => (int) $sp->id,
                                        'ten' => (string) ($sp->ten_san_pham ?? ''),
                                        'ma' => (string) ($sp->ma_san_pham ?? ''),
                                        'hinh_anh_url' => $hinhPath ? ('/storage/' . ltrim($hinhPath, '/')) : '',
                                        'gia_tri' => $sp->gia_tri !== null ? (float) $sp->gia_tri : null,
                                        'kiem_tra_url' => route('admin.trang-phuc.san-pham.kiem-tra', $sp),
                                        'stock' => (int) ($stockByProduct[$sp->id] ?? 0),
                                        'sdDates' => array_values(array_map('strval', (array) $sdDates)),
                                    ];
                                }
                            @endphp
                            <label class="form-label" for="them_tim_san_pham">Sản phẩm <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="them_tim_san_pham"
                                   autocomplete="off"
                                   placeholder="Nhập tên hoặc mã sản phẩm để lọc…">
                            <div class="them-sp-ket-qua-scroll border rounded p-3 mt-2" id="them_sp_ket_qua_scroll" aria-describedby="them_trang_phuc_err">
                                <div class="row g-3" id="them_sp_ket_qua"></div>
                                <div class="text-center text-muted small py-3 d-none" id="them_sp_khong_co">Không có sản phẩm phù hợp.</div>
                            </div>
                            <div class="mt-2 d-none" id="them_sp_da_chon_wrap">
                                <div class="small text-muted mb-1">Sản phẩm đã chọn</div>
                                <div class="table-responsive border rounded">
                                    <table class="table table-sm mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 84px;">Ảnh</th>
                                                <th>Sản phẩm</th>
                                                <th style="width: 140px;">Mã</th>
                                                <th style="width: 70px;" class="text-end">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="them_sp_da_chon"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="them_trang_phuc_hidden_wrap" class="visually-hidden" aria-hidden="true"></div>
                            <p class="form-text text-danger d-none mb-0 mt-1" id="them_trang_phuc_err" role="alert">Vui lòng chọn ít nhất một sản phẩm.</p>
                            <script type="application/json" id="them-san-pham-catalog-data">@json($sanPhamCatalog)</script>
                            <script type="application/json" id="them-san-pham-search-url">@json(route('admin.trang-phuc.hop-dong.tim-san-pham'))</script>
                            <script type="application/json" id="them-lich-cho-thue-data">@json($lichChoThueHopDong ?? [])</script>
                        </div>
                        @php
                            $themTongTienRaw = old('tong_tien');
                            $themTienCocRaw = old('tien_coc', '0');
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="them_tong_tien_hien_thi">Tiền thuê (đ) <span class="text-danger">*</span></label>
                            <input type="hidden"
                                   id="them_tong_tien"
                                   name="tong_tien"
                                   value="{{ $themTongTienRaw }}">
                            <input type="text"
                                   class="form-control text-end"
                                   id="them_tong_tien_hien_thi"
                                   value="{{ $themTongTienRaw !== null && $themTongTienRaw !== '' ? number_format((float) $themTongTienRaw, 0, ',', '.') : '' }}"
                                   inputmode="numeric"
                                   placeholder="0"
                                   required
                                   autocomplete="off"
                                   title="Nhập trực tiếp tổng tiền thuê của hợp đồng">
                            <div class="invalid-feedback" id="them_fb_tong_tien"></div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="them_tien_coc_hien_thi">Tiền cọc (đ)</label>
                            <input type="hidden"
                                   id="them_tien_coc"
                                   name="tien_coc"
                                   value="{{ $themTienCocRaw }}">
                            <input type="text"
                                   class="form-control text-end"
                                   id="them_tien_coc_hien_thi"
                                   value="{{ number_format((float) $themTienCocRaw, 0, ',', '.') }}"
                                   inputmode="numeric"
                                   placeholder="0"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Chỉnh sửa hợp đồng --}}
<div class="modal fade" id="modalSuaHopDong" tabindex="-1" aria-labelledby="modalSuaHopDongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-hop-dong">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaHopDongLabel">Chỉnh sửa hợp đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaHopDong" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="thoi_gian_tra_hang_chinh_thuc" id="sua_hidden_thoi_gian_tra_chinh_thuc" value="">
                <input type="hidden" name="trang_thai" id="sua_hidden_trang_thai" value="0">
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="sua_ten_khach_hang">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ten_khach_hang" name="ten_khach_hang" placeholder="Nhập tên khách hàng" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="sua_so_dien_thoai">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_so_dien_thoai" name="so_dien_thoai" placeholder="0912345678" maxlength="20" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                            <input type="text"
                                   class="form-control"
                                   id="sua_ghi_chu"
                                   name="ghi_chu"
                                   maxlength="500"
                                   placeholder="Ghi chú…"
                                   autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_tim_san_pham">Sản phẩm <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="sua_tim_san_pham"
                                   autocomplete="off"
                                   placeholder="Nhập tên hoặc mã sản phẩm để lọc…">
                            <div class="them-sp-ket-qua-scroll border rounded p-2 bg-body-secondary mt-2" id="sua_sp_ket_qua_scroll">
                                <div class="row g-2" id="sua_sp_ket_qua"></div>
                                <div class="text-center text-muted small py-3 d-none" id="sua_sp_khong_co">Không có sản phẩm phù hợp.</div>
                            </div>
                            <div class="mt-2 d-none" id="sua_sp_da_chon_wrap">
                                <div class="small text-muted mb-1">Sản phẩm đã chọn</div>
                                <div class="table-responsive border rounded">
                                    <table class="table table-sm mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 84px;">Ảnh</th>
                                                <th>Sản phẩm</th>
                                                <th style="width: 140px;">Mã</th>
                                                <th style="width: 70px;" class="text-end">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sua_sp_da_chon"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="sua_trang_phuc_hidden_wrap" class="visually-hidden" aria-hidden="true"></div>
                            <p class="form-text text-danger d-none mb-0 mt-1" id="sua_trang_phuc_err" role="alert">Vui lòng chọn ít nhất một sản phẩm.</p>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="sua_thoi_gian_bat_dau">Ngày thuê (bắt đầu) <span class="text-danger">*</span></label>
                            <input type="text" class="flatpickr-date-admin form-control" id="sua_thoi_gian_bat_dau" name="thoi_gian_thue_bat_dau" placeholder="dd/mm/yyyy" required autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="sua_thoi_gian_du_kien_tra">Ngày dự kiến trả <span class="text-danger">*</span></label>
                            <input type="text" class="flatpickr-date-admin form-control" id="sua_thoi_gian_du_kien_tra" name="thoi_gian_du_kien_tra" placeholder="dd/mm/yyyy" required autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3" id="sua_wrap_so_ngay_thue">
                            <label class="form-label" for="sua_so_ngay_thue_display">Số ngày thuê</label>
                            <input type="text"
                                   class="form-control bg-body-secondary"
                                   id="sua_so_ngay_thue_display"
                                   value="0"
                                   readonly
                                   tabindex="-1"
                                   autocomplete="off"
                                   aria-readonly="true"
                                   title="Tự động theo khoảng ngày thuê — dùng tham khảo, không nhân vào tiền thuê">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="sua_tong_tien_hien_thi">Tiền thuê (đ) <span class="text-danger">*</span></label>
                            <input type="hidden"
                                   id="sua_tong_tien"
                                   name="tong_tien"
                                   value="">
                            <input type="text"
                                   class="form-control text-end"
                                   id="sua_tong_tien_hien_thi"
                                   value=""
                                   inputmode="numeric"
                                   placeholder="0"
                                   required
                                   autocomplete="off"
                                   title="Nhập trực tiếp tổng tiền thuê của hợp đồng">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <label class="form-label" for="sua_tien_coc_hien_thi">Tiền cọc (đ)</label>
                            <input type="hidden"
                                   id="sua_tien_coc"
                                   name="tien_coc"
                                   value="0">
                            <input type="text"
                                   class="form-control text-end"
                                   id="sua_tien_coc_hien_thi"
                                   value=""
                                   inputmode="numeric"
                                   placeholder="0"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal cập nhật trạng thái (Hoàn thành / Huỷ, ngày trả chính thức) --}}
<div class="modal fade" id="modalCapNhatTrangThaiHopDong" tabindex="-1" aria-labelledby="modalCapNhatTrangThaiHopDongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-cap-nhat-trang-thai-hd">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCapNhatTrangThaiHopDongLabel">Cập nhật trạng thái hợp đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formCapNhatTrangThaiHopDong" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="tt_modal_tong_tien_hien_thi">Tổng tiền (đ)</label>
                            <input type="text"
                                   class="form-control text-end bg-body-secondary"
                                   id="tt_modal_tong_tien_hien_thi"
                                   value=""
                                   readonly
                                   disabled
                                   tabindex="-1"
                                   autocomplete="off"
                                   aria-readonly="true">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="tt_modal_da_thanh_toan_hien_thi">Đã thanh toán (đ)</label>
                            <input type="hidden" id="tt_modal_tien_coc" name="tien_coc" value="0">
                            <input type="text"
                                   class="form-control text-end"
                                   id="tt_modal_da_thanh_toan_hien_thi"
                                   value=""
                                   inputmode="numeric"
                                   placeholder="0"
                                   autocomplete="off"
                                   title="Số tiền khách đã thanh toán (cọc)">
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-stretch tt-modal-trang-thai-row">
                        <div class="flex-fill min-w-0">
                            <label class="form-label" for="tt_modal_select_trang_thai">Trạng thái</label>
                            <select class="form-select select2-admin"
                                    name="trang_thai"
                                    id="tt_modal_select_trang_thai"
                                    data-placeholder="Chọn trạng thái"
                                    required>
                                <option value=""></option>
                                <option value="1">Hoàn thành</option>
                                <option value="2">Huỷ hợp đồng</option>
                            </select>
                        </div>
                        <div class="flex-fill min-w-0 mb-0" id="wrap_tt_modal_ngay_tra">
                            <label class="form-label" for="tt_modal_ngay_tra_chinh_thuc">Ngày trả chính thức</label>
                            <input type="text"
                                   class="form-control flatpickr-date-admin"
                                   name="ngay_tra_chinh_thuc"
                                   id="tt_modal_ngay_tra_chinh_thuc"
                                   autocomplete="off"
                                   placeholder="Chọn ngày">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa --}}
<div class="modal fade" id="modalXacNhanXoaHopDong" tabindex="-1" aria-labelledby="modalXacNhanXoaHopDongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaHopDongLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa hợp đồng này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaHopDong">
                    <i class="fa-solid fa-trash me-1"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal lịch sử sử dụng sản phẩm (từ card chọn SP trong HĐ thuê) --}}
<div class="modal fade" id="modalHopDongKiemTraSp" tabindex="-1" aria-labelledby="modalHopDongKiemTraSpLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 92vw; width: 820px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHopDongKiemTraSpLabel">Lịch sử dụng sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div>
                        <div class="fw-semibold" id="hdKtspTen">—</div>
                        <div class="text-muted small" id="hdKtspMa">—</div>
                    </div>
                    <div class="text-muted small" id="hdKtspStatus"></div>
                </div>
                <div id="hdKtspLoading" class="py-4 text-center text-muted d-none">
                    <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                    Đang tải dữ liệu...
                </div>
                <div id="hdKtspError" class="alert alert-danger d-none" role="alert"></div>
                <div id="hdKtspContent" class="d-none">
                    <div class="fw-semibold mb-1">Lịch sử sử dụng theo ngày</div>
                    {{-- <div class="text-muted small mb-2">Mỗi ngày liệt kê các đơn đang sử dụng sản phẩm.</div> --}}
                    <div id="hdKtspGroupedEmpty" class="text-muted small d-none">Chưa có dữ liệu sử dụng.</div>
                    <div id="hdKtspGroupedWrap" class="accordion accordion-flush"></div>
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
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 1100px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
.hd-thanh-toan-col {
    min-width: 168px;
    width: 168px;
}
.hd-thanh-toan-cell {
    min-width: 168px;
    vertical-align: middle;
}
.hd-thanh-toan-progress {
    height: 0.45rem;
    cursor: help;
}
#modalThemHopDong .modal-hop-dong,
#modalSuaHopDong .modal-hop-dong {
    max-width: 90vw;
}
#modalXacNhanXoaHopDong .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
#modalCapNhatTrangThaiHopDong .modal-cap-nhat-trang-thai-hd {
    max-width: 90vw;
    width: 50%;
}
#modalCapNhatTrangThaiHopDong .tt-modal-trang-thai-row .select2-container {
    width: 100% !important;
}
.them-sp-ket-qua-scroll {
    max-height: 320px;
    overflow-y: auto;
}
#modalThemHopDong #them_sp_ket_qua_scroll {
    background-color: #fffafb;
}
#modalThemHopDong #them_sp_ket_qua {
    --bs-gutter-y: 1rem;
    --bs-gutter-x: 0.75rem;
}
.them-sp-card {
    position: relative;
    cursor: pointer;
    padding-bottom: 0.35rem !important;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}
.them-sp-card__layout {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    gap: 0.5rem;
    min-width: 0;
}
.them-sp-card__details {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    flex: 1 1 auto;
    min-width: 0;
}
.them-sp-card__body {
    flex: 1 1 auto;
    min-width: 0;
}
.them-sp-card__actions {
    position: absolute;
    right: 8px;
    bottom: 8px;
    left: auto;
    top: auto;
    z-index: 2;
    margin: 0;
    padding: 0;
}
.them-sp-btn-lich {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    width: auto;
    max-width: 100%;
    height: auto;
    min-height: 1.75rem;
    padding: 0.2rem 0.45rem;
    line-height: 1.2;
    font-size: 0.6875rem;
    white-space: nowrap;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.them-sp-btn-lich:hover,
.them-sp-btn-lich:focus {
    background-color: #fff;
}
.them-sp-btn-lich__label {
    display: none;
}
/* Modal lịch sử sử dụng SP: làm mờ & tối nền phía sau để nổi bật dialog */
body.modal-hop-dong-ktsp-open .modal-backdrop:last-of-type {
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    opacity: 0.58;
}
#modalHopDongKiemTraSp.modal.show .modal-content {
    box-shadow:
    0 0 40px  0   rgba(33, 37, 41, 0.16),
    0 0 80px  40px rgba(33, 37, 41, 0.14),
    0 0 120px  80px rgba(33, 37, 41, 0.12),
    0 0 160px 140px rgba(33, 37, 41, 0.11),
    0 0 200px 180px rgba(33, 37, 41, 0.10);
}
#modalHopDongKiemTraSp .list-group-item .hd-ktsp-list-info {
    padding: 12px;
}
#modalThemHopDong #them_sp_ket_qua .them-sp-card {
    background-color: #fff5f8;
    border-color: rgba(233, 30, 99, 0.12);
    margin-bottom: 0.125rem;
}
.them-sp-card:hover {
    border-color: var(--bs-primary, #696cff);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
}
#modalThemHopDong #them_sp_ket_qua .them-sp-card:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(233, 30, 99, 0.08);
    background-color: #ffecf2;
}
.them-sp-card.is-selected {
    border-color: var(--bs-primary, #696cff);
    border-width: 2px;
    background-color: rgba(var(--bs-primary-rgb, 105, 108, 255), 0.08);
}
#modalThemHopDong #them_sp_ket_qua .them-sp-card.is-selected {
    background-color: #ffe4ef;
}
.them-sp-card .them-sp-check {
    opacity: 0.35;
}
.them-sp-card.is-selected .them-sp-check {
    opacity: 1;
    color: var(--bs-primary, #696cff);
}
.them-sp-thumb {
    width: 72px;
    flex: 0 0 72px;
}
.them-sp-thumb-img {
    width: 72px;
    height: 96px;
    object-fit: cover;
    display: block;
}
.them-sp-thumb-placeholder {
    width: 72px;
    height: 96px;
    font-size: 1.35rem;
    background-color: #fff;
}

/* Responsive modal thêm / sửa hợp đồng (mobile) */
@media (max-width: 576px) {
    #modalThemHopDong .modal-dialog.modal-hop-dong,
    #modalSuaHopDong .modal-dialog.modal-hop-dong {
        width: 100%;
        margin: 0.5rem;
    }
    #modalThemHopDong .modal-content,
    #modalSuaHopDong .modal-content {
        font-size: 14px;
        font-weight: 400;
    }
    #modalThemHopDong .modal-title,
    #modalSuaHopDong .modal-title {
        font-size: 1rem;
        font-weight: 600;
    }
    #modalThemHopDong .modal-body,
    #modalSuaHopDong .modal-body {
        padding: 0.75rem;
    }
    #modalThemHopDong .modal-header,
    #modalThemHopDong .modal-footer,
    #modalSuaHopDong .modal-header,
    #modalSuaHopDong .modal-footer {
        padding: 0.75rem;
    }
    #modalThemHopDong .form-label,
    #modalSuaHopDong .form-label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    #modalThemHopDong .form-text,
    #modalThemHopDong .small,
    #modalSuaHopDong .form-text,
    #modalSuaHopDong .small {
        font-size: 14px;
    }
    #modalThemHopDong .btn,
    #modalSuaHopDong .btn {
        font-size: 14px;
        padding: 0.375rem 0.6rem;
    }
    #modalThemHopDong .them-sp-ket-qua-scroll,
    #modalSuaHopDong .them-sp-ket-qua-scroll {
        max-height: min(70vh, 420px);
    }
    #modalThemHopDong #them_sp_ket_qua > [class*="col-"],
    #modalSuaHopDong #sua_sp_ket_qua > [class*="col-"] {
        flex: 0 0 100%;
        max-width: 100%;
    }
    #modalThemHopDong .them-sp-card,
    #modalSuaHopDong .them-sp-card {
        padding: 0.5rem 0.5rem 0.5rem !important;
    }
    #modalThemHopDong .them-sp-card__layout,
    #modalSuaHopDong .them-sp-card__layout {
        flex-direction: column;
        gap: 0.5rem;
    }
    #modalThemHopDong .them-sp-thumb,
    #modalSuaHopDong .them-sp-thumb {
        width: 100%;
        flex: 0 0 auto;
        max-width: none;
    }
    #modalThemHopDong .them-sp-thumb-img,
    #modalThemHopDong .them-sp-thumb-placeholder,
    #modalSuaHopDong .them-sp-thumb-img,
    #modalSuaHopDong .them-sp-thumb-placeholder {
        width: 100%;
        height: auto;
        aspect-ratio: 3 / 4;
        max-height: 8rem;
        margin: 0 auto;
    }
    #modalThemHopDong .them-sp-card__details,
    #modalSuaHopDong .them-sp-card__details {
        width: 100%;
    }
    #modalThemHopDong .them-sp-btn-lich,
    #modalSuaHopDong .them-sp-btn-lich {
        font-size: 0.75rem;
        padding: 0.3rem 0.55rem;
    }
    #modalThemHopDong .them-sp-btn-lich__label,
    #modalSuaHopDong .them-sp-btn-lich__label {
        display: inline;
    }

}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    @if($errors->any())
    var modalThem = document.getElementById('modalThemHopDong');
    if (modalThem) {
        var m = new bootstrap.Modal(modalThem);
        m.show();
    }
    @endif

    // Chọn sản phẩm (Thêm HĐ): ô tìm + thẻ card, nhiều SP — đồng bộ hidden trang_phuc[]
    var themCatEl = document.getElementById('them-san-pham-catalog-data');
    var THEM_SP_CATALOG = [];
    if (themCatEl && themCatEl.textContent) {
        try { THEM_SP_CATALOG = JSON.parse(themCatEl.textContent.trim()); } catch (e1) { THEM_SP_CATALOG = []; }
    }
    var themSearchUrlEl = document.getElementById('them-san-pham-search-url');
    var THEM_SP_SEARCH_URL = '';
    if (themSearchUrlEl && themSearchUrlEl.textContent) {
        try {
            var _u = JSON.parse(themSearchUrlEl.textContent.trim());
            THEM_SP_SEARCH_URL = typeof _u === 'string' ? _u : '';
        } catch (eSearchUrl) { THEM_SP_SEARCH_URL = ''; }
    }
    var themLichEl = document.getElementById('them-lich-cho-thue-data');
    var THEM_LICH_CHO_THUE = [];
    if (themLichEl && themLichEl.textContent) {
        try { THEM_LICH_CHO_THUE = JSON.parse(themLichEl.textContent.trim()); } catch (eLich) { THEM_LICH_CHO_THUE = []; }
    }
    if (!Array.isArray(THEM_LICH_CHO_THUE)) THEM_LICH_CHO_THUE = [];
    /** Mã SP đang / sẽ cho thuê trùng khoảng ngày đã chọn (modal thêm HĐ). */
    var ds_san_pham_dang_cho_thue = [];
    var themSpById = {};
    THEM_SP_CATALOG.forEach(function (p) { if (p && p.id != null) themSpById[p.id] = p; });
    var themSpRemoteList = null;
    var themSpSearchTimer = null;
    var themSpSearchReqId = 0;
    var themSpSelected = new Set(@json(array_values(array_map('intval', (array) $oldTrangPhucThem))));
    var themTimInput = document.getElementById('them_tim_san_pham');
    var themKetQua = document.getElementById('them_sp_ket_qua');
    var themKhongCo = document.getElementById('them_sp_khong_co');
    var themHiddenWrap = document.getElementById('them_trang_phuc_hidden_wrap');
    var themDaChonWrap = document.getElementById('them_sp_da_chon_wrap');
    var themDaChon = document.getElementById('them_sp_da_chon'); // tbody
    var themTrangPhucErr = document.getElementById('them_trang_phuc_err');

    function hopDongToNumber(v) {
        var n = Number(v);
        return Number.isFinite(n) ? n : 0;
    }
    function hopDongParseMoneyText(v) {
        var s = String(v || '').replace(/[^\d]/g, '');
        if (!s) return 0;
        return hopDongToNumber(s);
    }
    function hopDongFormatMoneyInputNumber(v) {
        return new Intl.NumberFormat('vi-VN').format(Math.max(0, Math.round(hopDongToNumber(v))));
    }
    function hopDongSyncMoneyField(displayId, hiddenId, emptyHiddenValue) {
        var displayEl = document.getElementById(displayId);
        var hiddenEl = document.getElementById(hiddenId);
        if (!displayEl || !hiddenEl) return;
        var digits = String(displayEl.value || '').replace(/[^\d]/g, '');
        if (!digits) {
            hiddenEl.value = emptyHiddenValue;
            displayEl.value = '';
            return;
        }
        var n = hopDongParseMoneyText(displayEl.value);
        hiddenEl.value = String(n);
        displayEl.value = hopDongFormatMoneyInputNumber(n);
    }
    function hopDongBindFormattedMoneyInput(displayId, hiddenId, emptyHiddenValue) {
        var displayEl = document.getElementById(displayId);
        var hiddenEl = document.getElementById(hiddenId);
        if (!displayEl || !hiddenEl) return;
        function sync() {
            hopDongSyncMoneyField(displayId, hiddenId, emptyHiddenValue);
        }
        displayEl.addEventListener('input', sync);
        displayEl.addEventListener('blur', sync);
        if (String(displayEl.value || '').replace(/[^\d]/g, '')) sync();
    }
    function hopDongSyncThemMoneyInputs() {
        hopDongSyncMoneyField('them_tong_tien_hien_thi', 'them_tong_tien', '');
        hopDongSyncMoneyField('them_tien_coc_hien_thi', 'them_tien_coc', '0');
    }
    function hopDongSyncSuaMoneyInputs() {
        hopDongSyncMoneyField('sua_tong_tien_hien_thi', 'sua_tong_tien', '');
        hopDongSyncMoneyField('sua_tien_coc_hien_thi', 'sua_tien_coc', '0');
    }
    function hopDongSetMoneyFields(displayId, hiddenId, rawValue, emptyHiddenValue) {
        var displayEl = document.getElementById(displayId);
        var hiddenEl = document.getElementById(hiddenId);
        if (!displayEl || !hiddenEl) return;
        var raw = rawValue !== null && rawValue !== undefined ? String(rawValue).trim() : '';
        if (!raw) {
            hiddenEl.value = emptyHiddenValue;
            displayEl.value = '';
            return;
        }
        var n = Math.max(0, Math.round(hopDongToNumber(raw)));
        if (!Number.isFinite(n)) {
            hiddenEl.value = emptyHiddenValue;
            displayEl.value = '';
            return;
        }
        hiddenEl.value = String(n);
        displayEl.value = hopDongFormatMoneyInputNumber(n);
    }
    function hopDongMarkFieldInvalid(inputEl) {
        if (!inputEl) return;
        inputEl.classList.add('is-invalid');
        if (inputEl._flatpickr && inputEl._flatpickr.altInput) {
            inputEl._flatpickr.altInput.classList.add('is-invalid');
        }
    }
    function hopDongClearFieldInvalid(inputEl) {
        if (!inputEl) return;
        inputEl.classList.remove('is-invalid');
        if (inputEl._flatpickr && inputEl._flatpickr.altInput) {
            inputEl._flatpickr.altInput.classList.remove('is-invalid');
        }
    }
    function hopDongSetThemFieldError(inputEl, feedbackId, message) {
        var fb = feedbackId ? document.getElementById(feedbackId) : null;
        if (message) {
            hopDongMarkFieldInvalid(inputEl);
            if (fb) fb.textContent = message;
        } else {
            hopDongClearFieldInvalid(inputEl);
            if (fb) fb.textContent = '';
        }
    }
    function hopDongClearThemFormErrors() {
        var formThemEl = document.getElementById('formThemHopDong');
        if (!formThemEl) return;
        formThemEl.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        [
            'them_fb_ten_khach_hang',
            'them_fb_so_dien_thoai',
            'them_fb_thoi_gian_bat_dau',
            'them_fb_thoi_gian_du_kien_tra',
            'them_fb_tong_tien'
        ].forEach(function (id) {
            var fb = document.getElementById(id);
            if (fb) fb.textContent = '';
        });
        if (themTrangPhucErr) themTrangPhucErr.classList.add('d-none');
        var spScroll = document.getElementById('them_sp_ket_qua_scroll');
        if (spScroll) spScroll.classList.remove('border-danger');
    }
    function hopDongValidateThemHopDongForm() {
        hopDongClearThemFormErrors();
        var hasError = false;
        var firstFocusEl = null;

        function fail(inputEl, feedbackId, message) {
            hopDongSetThemFieldError(inputEl, feedbackId, message);
            if (!firstFocusEl && inputEl) {
                firstFocusEl = (inputEl._flatpickr && inputEl._flatpickr.altInput) ? inputEl._flatpickr.altInput : inputEl;
            }
            hasError = true;
        }

        var tenKh = document.getElementById('them_ten_khach_hang');
        var tenVal = tenKh ? String(tenKh.value || '').trim() : '';
        if (!tenVal) fail(tenKh, 'them_fb_ten_khach_hang', 'Vui lòng nhập tên khách hàng.');

        var sdtEl = document.getElementById('them_so_dien_thoai');
        var sdtVal = sdtEl ? String(sdtEl.value || '').trim() : '';
        if (!sdtVal) fail(sdtEl, 'them_fb_so_dien_thoai', 'Vui lòng nhập số điện thoại.');

        var bdEl = document.getElementById('them_thoi_gian_bat_dau');
        var ktEl = document.getElementById('them_thoi_gian_du_kien_tra');
        var bdRaw = bdEl ? String(bdEl.value || '').trim() : '';
        var ktRaw = ktEl ? String(ktEl.value || '').trim() : '';
        var bdDate = bdRaw ? parseThemHopDongYmd(bdRaw) : null;
        var ktDate = ktRaw ? parseThemHopDongYmd(ktRaw) : null;

        if (!bdRaw) fail(bdEl, 'them_fb_thoi_gian_bat_dau', 'Vui lòng chọn ngày thuê (bắt đầu).');
        else if (!bdDate) fail(bdEl, 'them_fb_thoi_gian_bat_dau', 'Ngày thuê không hợp lệ.');

        if (!ktRaw) fail(ktEl, 'them_fb_thoi_gian_du_kien_tra', 'Vui lòng chọn ngày dự kiến trả.');
        else if (!ktDate) fail(ktEl, 'them_fb_thoi_gian_du_kien_tra', 'Ngày dự kiến trả không hợp lệ.');
        else if (bdDate && ktDate) {
            var t0 = themHopDongUtcMidnight(bdDate.getFullYear(), bdDate.getMonth(), bdDate.getDate());
            var t1 = themHopDongUtcMidnight(ktDate.getFullYear(), ktDate.getMonth(), ktDate.getDate());
            if (t1 < t0) {
                fail(ktEl, 'them_fb_thoi_gian_du_kien_tra', 'Ngày dự kiến trả phải từ ngày thuê trở đi.');
            }
        }

        var coSanPham = themSpSelected.size > 0;
        if (!coSanPham) {
            var spScroll = document.getElementById('them_sp_ket_qua_scroll');
            if (spScroll) spScroll.classList.add('border-danger');
            if (themTrangPhucErr) themTrangPhucErr.classList.remove('d-none');
            if (!firstFocusEl && themTimInput) firstFocusEl = themTimInput;
            hasError = true;
        }

        var tongTienDisp = document.getElementById('them_tong_tien_hien_thi');
        var tongDigits = tongTienDisp ? String(tongTienDisp.value || '').replace(/[^\d]/g, '') : '';
        if (!tongDigits) {
            fail(tongTienDisp, 'them_fb_tong_tien', 'Vui lòng nhập tiền thuê.');
        } else {
            var tongNum = hopDongParseMoneyText(tongTienDisp.value);
            if (tongNum < 0) fail(tongTienDisp, 'them_fb_tong_tien', 'Tiền thuê không hợp lệ.');
        }

        if (hasError && firstFocusEl) {
            firstFocusEl.focus({ preventScroll: true });
            firstFocusEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
        return !hasError;
    }
    function hopDongBindThemFormClearErrors() {
        var map = {
            them_ten_khach_hang: 'them_fb_ten_khach_hang',
            them_so_dien_thoai: 'them_fb_so_dien_thoai',
            them_thoi_gian_bat_dau: 'them_fb_thoi_gian_bat_dau',
            them_thoi_gian_du_kien_tra: 'them_fb_thoi_gian_du_kien_tra',
            them_tong_tien_hien_thi: 'them_fb_tong_tien'
        };
        Object.keys(map).forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            function clearErr() {
                hopDongSetThemFieldError(el, map[id], '');
            }
            el.addEventListener('input', clearErr);
            el.addEventListener('change', clearErr);
        });
    }
    hopDongBindThemFormClearErrors();
    hopDongBindFormattedMoneyInput('them_tong_tien_hien_thi', 'them_tong_tien', '');
    hopDongBindFormattedMoneyInput('them_tien_coc_hien_thi', 'them_tien_coc', '0');
    hopDongBindFormattedMoneyInput('sua_tong_tien_hien_thi', 'sua_tong_tien', '');
    hopDongBindFormattedMoneyInput('sua_tien_coc_hien_thi', 'sua_tien_coc', '0');
    hopDongBindFormattedMoneyInput('tt_modal_da_thanh_toan_hien_thi', 'tt_modal_tien_coc', '0');

    function themSpNorm(s) {
        return String(s || '').toLowerCase().trim();
    }
    function themSpFilter(query) {
        var q = themSpNorm(query);
        var list;
        if (!q) list = THEM_SP_CATALOG.slice();
        else {
            list = THEM_SP_CATALOG.filter(function (p) {
                return themSpNorm(p.ten).indexOf(q) !== -1 || themSpNorm(p.ma).indexOf(q) !== -1;
            });
        }
        return themSpLoaiBoDangChoThue(list);
    }
    function themHopDongRangesOverlapYmd(aStart, aEnd, bStart, bEnd) {
        return aStart <= bEnd && bStart <= aEnd;
    }
    function themSpMaChoThueBusySet() {
        var busy = new Set();
        ds_san_pham_dang_cho_thue.forEach(function (ma) {
            var m = themSpNorm(ma);
            if (m) busy.add(m);
        });
        return busy;
    }
    function themSpLoaiBoDangChoThue(list) {
        var busy = themSpMaChoThueBusySet();
        if (!busy.size) return list;
        return list.filter(function (p) {
            return !busy.has(themSpNorm(p.ma));
        });
    }
    function themSpCapNhatDsDangChoThue() {
        console.log('themSpCapNhatDsDangChoThue');
        ds_san_pham_dang_cho_thue = [];
        var bd = document.getElementById('them_thoi_gian_bat_dau');
        var kt = document.getElementById('them_thoi_gian_du_kien_tra');
        if (!bd || !kt) return;
        var s = (bd.value || '').trim();
        var e = (kt.value || '').trim();
        if (!s || !e || e < s) return;
        var setMa = new Set();
        THEM_LICH_CHO_THUE.forEach(function (hd) {
            if (!hd || !hd.tu || !hd.den) return;
            if (!themHopDongRangesOverlapYmd(s, e, hd.tu, hd.den)) return;
            (hd.ma_san_pham || []).forEach(function (ma) {
                var m = String(ma || '').trim();
                if (m) setMa.add(m);
            });
        });
        ds_san_pham_dang_cho_thue = Array.from(setMa);
        console.log(ds_san_pham_dang_cho_thue);
    }
    function themSpToRelativeUrl(u) {
        if (!u) return '';
        var s = String(u);
        // Nếu backend trả về URL đầy đủ (có domain), chuyển về path tương đối để src không có domain.
        try {
            var url = new URL(s, window.location.origin);
            return url.pathname + url.search + url.hash;
        } catch (e) {
            // Nếu là path tương đối sẵn (vd: /storage/..), giữ nguyên.
            return s;
        }
    }
    function hopDongSpEscHtml(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
    function hopDongSpFormatGiaTri(val) {
        if (val === null || val === undefined || val === '') return '—';
        var n = Number(val);
        if (isNaN(n)) return '—';
        return n.toLocaleString('vi-VN', { maximumFractionDigits: 0 }) + ' đ';
    }
    function hopDongSpCreateProductCard(p, opts) {
        opts = opts || {};
        var pid = parseInt(p && p.id != null ? p.id : '', 10);
        if (isNaN(pid)) return null;
        var selected = !!opts.selected;
        var dataAttr = opts.dataAttr || 'data-sp-id';
        var card = document.createElement('div');
        card.className = 'them-sp-card h-100 p-2 p-md-3' + (selected ? ' is-selected' : '');
        card.setAttribute('role', 'button');
        card.setAttribute('tabindex', '0');
        card.setAttribute(dataAttr, String(pid));

        var layout = document.createElement('div');
        layout.className = 'them-sp-card__layout';

        var thumbWrap = document.createElement('div');
        thumbWrap.className = 'them-sp-thumb flex-shrink-0';
        var hinhUrl = p.hinh_anh_url;
        if (hinhUrl) {
            var imgEl = document.createElement('img');
            imgEl.className = 'them-sp-thumb-img rounded border bg-body';
            imgEl.src = themSpToRelativeUrl(hinhUrl);
            imgEl.alt = '';
            imgEl.loading = 'lazy';
            imgEl.decoding = 'async';
            thumbWrap.appendChild(imgEl);
        } else {
            var ph = document.createElement('div');
            ph.className = 'them-sp-thumb-placeholder rounded border d-flex align-items-center justify-content-center text-muted';
            ph.setAttribute('aria-hidden', 'true');
            ph.innerHTML = '<i class="fa-regular fa-image"></i>';
            thumbWrap.appendChild(ph);
        }
        layout.appendChild(thumbWrap);

        var details = document.createElement('div');
        details.className = 'them-sp-card__details';
        var chk = document.createElement('div');
        chk.className = 'them-sp-check flex-shrink-0 pt-1';
        chk.innerHTML = '<i class="fa-solid fa-circle-check" aria-hidden="true"></i>';
        var body = document.createElement('div');
        body.className = 'them-sp-card__body';
        var t1 = document.createElement('div');
        t1.className = 'fw-semibold text-break';
        t1.textContent = p.ten || '—';
        var t2 = document.createElement('div');
        t2.className = 'small text-muted';
        t2.textContent = 'Mã: ' + (p.ma || '—');
        var t3 = document.createElement('div');
        t3.className = 'small';
        t3.innerHTML = '<span class="text-muted">Giá trị: </span><strong>' + hopDongSpEscHtml(hopDongSpFormatGiaTri(p.gia_tri)) + '</strong>';
        body.appendChild(t1);
        body.appendChild(t2);
        body.appendChild(t3);
        if (p.sdDates && p.sdDates.length) {
            var tw = document.createElement('div');
            tw.className = 'small text-warning text-break';
            tw.textContent = 'SD: ' + p.sdDates.join(', ');
            body.appendChild(tw);
        }
        details.appendChild(chk);
        details.appendChild(body);
        layout.appendChild(details);
        card.appendChild(layout);

        var actions = document.createElement('div');
        actions.className = 'them-sp-card__actions';
        var btnLich = document.createElement('button');
        btnLich.type = 'button';
        btnLich.className = 'btn btn-sm btn-outline-primary them-sp-btn-lich';
        btnLich.setAttribute('data-ten', p.ten || '');
        btnLich.setAttribute('data-ma', p.ma || '');
        btnLich.setAttribute('data-url', p.kiem_tra_url || '');
        btnLich.title = 'Check lịch sử dụng sản phẩm';
        btnLich.setAttribute('aria-label', 'Check lịch sử dụng sản phẩm');
        btnLich.innerHTML = '<i class="fa-regular fa-calendar-days" aria-hidden="true"></i>';
        actions.appendChild(btnLich);
        card.appendChild(actions);
        return card;
    }
    function themSpSyncHidden() {
        if (!themHiddenWrap) return;
        themHiddenWrap.innerHTML = '';
        themSpSelected.forEach(function (id) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'trang_phuc[]';
            inp.value = String(id);
            themHiddenWrap.appendChild(inp);
        });
    }
    function themSpRenderChips() {
        if (!themDaChon || !themDaChonWrap) return;
        themDaChon.innerHTML = '';
        if (!themSpSelected.size) {
            themDaChonWrap.classList.add('d-none');
            return;
        }
        themDaChonWrap.classList.remove('d-none');
        themSpSelected.forEach(function (id) {
            var p = themSpById[id];
            var tr = document.createElement('tr');

            var tdImg = document.createElement('td');
            if (p && p.hinh_anh_url) {
                var img = document.createElement('img');
                img.src = themSpToRelativeUrl(p.hinh_anh_url);
                img.alt = '';
                img.loading = 'lazy';
                img.decoding = 'async';
                img.className = 'rounded border bg-body';
                img.style.width = '72px';
                img.style.height = '96px';
                img.style.objectFit = 'cover';
                tdImg.appendChild(img);
            } else {
                var ph = document.createElement('div');
                ph.className = 'rounded border bg-body-secondary d-flex align-items-center justify-content-center text-muted';
                ph.style.width = '72px';
                ph.style.height = '96px';
                ph.innerHTML = '<i class="fa-regular fa-image"></i>';
                tdImg.appendChild(ph);
            }

            var tdTen = document.createElement('td');
            tdTen.className = 'text-break';
            var ten = p && p.ten ? String(p.ten) : ('#' + id);
            var ma = p && p.ma ? String(p.ma) : '—';
            tdTen.innerHTML =
                '<div class="fw-semibold text-break">' + ten.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>' +
                (p && p.sdDates && p.sdDates.length
                    ? '<div class="small text-warning text-break">SD: ' + p.sdDates.join(', ').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>'
                    : '');

            var tdMa = document.createElement('td');
            tdMa.className = 'text-muted';
            tdMa.textContent = ma;

            var tdAct = document.createElement('td');
            tdAct.className = 'text-end';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.setAttribute('data-sp-remove', String(id));
            btn.innerHTML = '<i class="fa-solid fa-trash" aria-hidden="true"></i>';
            btn.title = 'Bỏ chọn';
            tdAct.appendChild(btn);

            tr.appendChild(tdImg);
            tr.appendChild(tdTen);
            tr.appendChild(tdMa);
            tr.appendChild(tdAct);
            themDaChon.appendChild(tr);
        });
    }
    function themSpRenderCards() {
        if (!themKetQua || !themKhongCo) return;
        var list;
        if (themSpRemoteList !== null) {
            list = themSpLoaiBoDangChoThue(themSpRemoteList);
        } else {
            var q = themTimInput ? themTimInput.value : '';
            list = themSpFilter(q);
        }
        themKetQua.innerHTML = '';
        if (!list.length) {
            themKhongCo.classList.remove('d-none');
            return;
        }
        themKhongCo.classList.add('d-none');
        list.forEach(function (p) {
            var pid = parseInt(p && p.id != null ? p.id : '', 10);
            if (isNaN(pid)) return;
            var col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4 col-xl-3';
            var card = hopDongSpCreateProductCard(p, {
                selected: themSpSelected.has(pid),
                dataAttr: 'data-sp-id'
            });
            if (!card) return;
            col.appendChild(card);
            themKetQua.appendChild(col);
        });
    }
    function themSpToggle(id) {
        id = parseInt(id, 10);
        if (isNaN(id)) return;
        if (themSpSelected.has(id)) themSpSelected.delete(id);
        else themSpSelected.add(id);
        themSpSyncHidden();
        themSpRenderChips();
        themSpRenderCards();
        updateThemTongKho();
        updateThemSoNgayThueDisplay();
        if (themTrangPhucErr) themTrangPhucErr.classList.add('d-none');
        var spScrollPick = document.getElementById('them_sp_ket_qua_scroll');
        if (spScrollPick) spScrollPick.classList.remove('border-danger');
    }
    function parseThemHopDongYmd(value) {
        var p = String(value || '').trim().split('-');
        if (p.length !== 3) return null;
        var y = parseInt(p[0], 10), m = parseInt(p[1], 10) - 1, d = parseInt(p[2], 10);
        if (!isFinite(y) || !isFinite(m) || !isFinite(d)) return null;
        var dt = new Date(y, m, d);
        if (dt.getFullYear() !== y || dt.getMonth() !== m || dt.getDate() !== d) return null;
        return dt;
    }
    /** UTC midnight anchor để đếm ngày không lệch DST */
    function themHopDongUtcMidnight(y, m, d) {
        return Date.UTC(y, m, d);
    }
    /** Chỉ cập nhật số ngày thuê theo hai ngày (tham khảo, không dùng để tính tiền). */
    function updateThemSoNgayThueDisplay() {
        var bd = document.getElementById('them_thoi_gian_bat_dau');
        var kt = document.getElementById('them_thoi_gian_du_kien_tra');
        var dispNgay = document.getElementById('them_so_ngay_thue_display');
        if (!bd || !kt || !dispNgay) return;

        function zeroOut() {
            dispNgay.value = '0';
        }

        var s = bd.value && bd.value.trim();
        var e = kt.value && kt.value.trim();
        if (!s || !e) {
            zeroOut();
        } else {
            var ds = parseThemHopDongYmd(s);
            var de = parseThemHopDongYmd(e);
            if (!ds || !de) {
                zeroOut();
            } else {
                var t0 = themHopDongUtcMidnight(ds.getFullYear(), ds.getMonth(), ds.getDate());
                var t1 = themHopDongUtcMidnight(de.getFullYear(), de.getMonth(), de.getDate());
                if (t1 < t0) {
                    zeroOut();
                } else {
                    var days = Math.floor((t1 - t0) / 86400000) + 1;
                    dispNgay.value = String(days);
                }
            }
        }
        themSpCapNhatDsDangChoThue();
        themSpRenderCards();
    }
    function updateSuaSoNgayThueDisplay() {
        var bd = document.getElementById('sua_thoi_gian_bat_dau');
        var kt = document.getElementById('sua_thoi_gian_du_kien_tra');
        var dispNgay = document.getElementById('sua_so_ngay_thue_display');
        if (!bd || !kt || !dispNgay) return;

        function zeroOut() {
            dispNgay.value = '0';
        }

        var s = bd.value && bd.value.trim();
        var e = kt.value && kt.value.trim();
        if (!s || !e) {
            zeroOut();
            return;
        }
        var ds = parseThemHopDongYmd(s);
        var de = parseThemHopDongYmd(e);
        if (!ds || !de) {
            zeroOut();
            return;
        }
        var t0 = themHopDongUtcMidnight(ds.getFullYear(), ds.getMonth(), ds.getDate());
        var t1 = themHopDongUtcMidnight(de.getFullYear(), de.getMonth(), de.getDate());
        if (t1 < t0) {
            zeroOut();
            return;
        }
        var days = Math.floor((t1 - t0) / 86400000) + 1;
        dispNgay.value = String(days);
    }
    // Cập nhật tồn kho (min theo các SP đã chọn)
    function updateThemTongKho() {
        var span = document.getElementById('them_tong_kho');
        if (!span) return;
        if (!themSpSelected.size) {
            span.textContent = '—';
            return;
        }
        var minStock = null;
        themSpSelected.forEach(function (sid) {
            var p = themSpById[sid];
            var stock = p && typeof p.stock === 'number' ? p.stock : 0;
            if (minStock === null || stock < minStock) minStock = stock;
        });
        if (minStock === null) minStock = 0;
        span.textContent = minStock;
    }
    if (themTimInput && themKetQua && themHiddenWrap) {
        themSpSyncHidden();
        themSpRenderChips();
        themSpRenderCards();
        updateThemTongKho();
        updateThemSoNgayThueDisplay();
        function bindThemHopDongFlatpickrNgay(elId, feedbackId) {
            var el = document.getElementById(elId);
            if (!el || !el._flatpickr) return;
            var fp = el._flatpickr;
            var oc = fp.config.onChange;
            var list = Array.isArray(oc) ? oc.slice() : (typeof oc === 'function' ? [oc] : []);
            list.push(function () {
                updateThemSoNgayThueDisplay();
                hopDongSetThemFieldError(el, feedbackId, '');
            });
            fp.config.onChange = list;
        }
        bindThemHopDongFlatpickrNgay('them_thoi_gian_bat_dau', 'them_fb_thoi_gian_bat_dau');
        bindThemHopDongFlatpickrNgay('them_thoi_gian_du_kien_tra', 'them_fb_thoi_gian_du_kien_tra');
        updateThemSoNgayThueDisplay();
        function themSpScheduleTimKiem() {
            clearTimeout(themSpSearchTimer);
            themSpSearchTimer = setTimeout(function () {
                var raw = themTimInput ? themTimInput.value : '';
                if (!themSpNorm(raw)) {
                    themSpRemoteList = null;
                    themSpRenderCards();
                    return;
                }
                var ax = window.axios;
                if (!ax || !THEM_SP_SEARCH_URL) {
                    themSpRemoteList = null;
                    themSpRenderCards();
                    return;
                }
                var reqId = ++themSpSearchReqId;
                ax.get(THEM_SP_SEARCH_URL, { params: { q: raw.trim() } })
                    .then(function (res) {
                        if (reqId !== themSpSearchReqId) return;
                        var items = (res.data && Array.isArray(res.data.items)) ? res.data.items : [];
                        items.forEach(function (p) {
                            if (p && p.id != null) themSpById[p.id] = p;
                        });
                        themSpRemoteList = items;
                        themSpRenderCards();
                    })
                    .catch(function () {
                        if (reqId !== themSpSearchReqId) return;
                        themSpRemoteList = [];
                        themSpRenderCards();
                    });
            }, 300);
        }
        themTimInput.addEventListener('input', function () {
            themSpScheduleTimKiem();
        });
        themKetQua.addEventListener('click', function (ev) {
            if (ev.target.closest('.them-sp-btn-lich')) return;
            var card = ev.target.closest('[data-sp-id]');
            if (!card || !themKetQua.contains(card)) return;
            themSpToggle(card.getAttribute('data-sp-id'));
        });
        themKetQua.addEventListener('keydown', function (ev) {
            if (ev.target.closest('.them-sp-btn-lich')) return;
            if (ev.key !== 'Enter' && ev.key !== ' ') return;
            var card = ev.target.closest('[data-sp-id]');
            if (!card || !themKetQua.contains(card)) return;
            ev.preventDefault();
            themSpToggle(card.getAttribute('data-sp-id'));
        });
        if (themDaChon) {
            themDaChon.addEventListener('click', function (ev) {
                var btn = ev.target.closest('[data-sp-remove]');
                if (!btn || !themDaChon.contains(btn)) return;
                ev.preventDefault();
                themSpToggle(btn.getAttribute('data-sp-remove'));
            });
        }
        var formThemHopDongEl = document.getElementById('formThemHopDong');
        var themFormSubmitting = false;
        if (formThemHopDongEl) {
            formThemHopDongEl.addEventListener('submit', function (ev) {
                if (themFormSubmitting) return;
                ev.preventDefault();
                hopDongSyncThemMoneyInputs();
                themSpSyncHidden();
                if (!hopDongValidateThemHopDongForm()) return;
                themFormSubmitting = true;
                formThemHopDongEl.submit();
            });
        }
    }
    var suaSpRemoteList = null;
    var suaSpSearchTimer = null;
    var suaSpSearchReqId = 0;
    var suaSpSelected = new Set();
    var suaTimInput = document.getElementById('sua_tim_san_pham');
    var suaKetQua = document.getElementById('sua_sp_ket_qua');
    var suaKhongCo = document.getElementById('sua_sp_khong_co');
    var suaHiddenWrap = document.getElementById('sua_trang_phuc_hidden_wrap');
    var suaDaChonWrap = document.getElementById('sua_sp_da_chon_wrap');
    var suaDaChon = document.getElementById('sua_sp_da_chon');
    var suaTrangPhucErr = document.getElementById('sua_trang_phuc_err');

    function suaSpSyncHidden() {
        if (!suaHiddenWrap) return;
        suaHiddenWrap.innerHTML = '';
        suaSpSelected.forEach(function (id) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'trang_phuc[]';
            inp.value = String(id);
            suaHiddenWrap.appendChild(inp);
        });
    }
    function suaSpRenderChips() {
        if (!suaDaChon || !suaDaChonWrap) return;
        suaDaChon.innerHTML = '';
        if (!suaSpSelected.size) {
            suaDaChonWrap.classList.add('d-none');
            return;
        }
        suaDaChonWrap.classList.remove('d-none');
        suaSpSelected.forEach(function (id) {
            var p = themSpById[id];
            var tr = document.createElement('tr');

            var tdImg = document.createElement('td');
            if (p && p.hinh_anh_url) {
                var img = document.createElement('img');
                img.src = themSpToRelativeUrl(p.hinh_anh_url);
                img.alt = '';
                img.loading = 'lazy';
                img.decoding = 'async';
                img.className = 'rounded border bg-body';
                img.style.width = '72px';
                img.style.height = '96px';
                img.style.objectFit = 'cover';
                tdImg.appendChild(img);
            } else {
                var ph = document.createElement('div');
                ph.className = 'rounded border bg-body-secondary d-flex align-items-center justify-content-center text-muted';
                ph.style.width = '72px';
                ph.style.height = '96px';
                ph.innerHTML = '<i class="fa-regular fa-image"></i>';
                tdImg.appendChild(ph);
            }

            var tdTen = document.createElement('td');
            tdTen.className = 'text-break';
            var ten = p && p.ten ? String(p.ten) : ('#' + id);
            tdTen.innerHTML =
                '<div class="fw-semibold text-break">' + ten.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>' +
                (p && p.sdDates && p.sdDates.length
                    ? '<div class="small text-warning text-break">SD: ' + p.sdDates.join(', ').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>'
                    : '');

            var tdMa = document.createElement('td');
            tdMa.className = 'text-muted';
            tdMa.textContent = p && p.ma ? String(p.ma) : '—';

            var tdAct = document.createElement('td');
            tdAct.className = 'text-end';
            var btnRm = document.createElement('button');
            btnRm.type = 'button';
            btnRm.className = 'btn btn-sm btn-outline-danger';
            btnRm.setAttribute('data-sua-sp-remove', String(id));
            btnRm.innerHTML = '<i class="fa-solid fa-trash" aria-hidden="true"></i>';
            btnRm.title = 'Bỏ chọn';
            tdAct.appendChild(btnRm);

            tr.appendChild(tdImg);
            tr.appendChild(tdTen);
            tr.appendChild(tdMa);
            tr.appendChild(tdAct);
            suaDaChon.appendChild(tr);
        });
    }
    function suaSpRenderCards() {
        if (!suaKetQua || !suaKhongCo) return;
        var list;
        if (suaSpRemoteList !== null) {
            list = suaSpRemoteList;
        } else {
            var q = suaTimInput ? suaTimInput.value : '';
            list = themSpFilter(q);
        }
        suaKetQua.innerHTML = '';
        if (!list.length) {
            suaKhongCo.classList.remove('d-none');
            return;
        }
        suaKhongCo.classList.add('d-none');
        list.forEach(function (p) {
            var pid = parseInt(p && p.id != null ? p.id : '', 10);
            if (isNaN(pid)) return;
            var col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4 col-xl-3';
            var card = hopDongSpCreateProductCard(p, {
                selected: suaSpSelected.has(pid),
                dataAttr: 'data-sua-sp-id'
            });
            if (!card) return;
            col.appendChild(card);
            suaKetQua.appendChild(col);
        });
    }
    function suaSpToggle(id) {
        id = parseInt(id, 10);
        if (isNaN(id)) return;
        if (suaSpSelected.has(id)) suaSpSelected.delete(id);
        else suaSpSelected.add(id);
        suaSpSyncHidden();
        suaSpRenderChips();
        suaSpRenderCards();
        updateSuaSoNgayThueDisplay();
        if (suaTrangPhucErr) suaTrangPhucErr.classList.add('d-none');
    }
    function suaSpScheduleTimKiem() {
        clearTimeout(suaSpSearchTimer);
        suaSpSearchTimer = setTimeout(function () {
            var raw = suaTimInput ? suaTimInput.value : '';
            if (!themSpNorm(raw)) {
                suaSpRemoteList = null;
                suaSpRenderCards();
                return;
            }
            var ax = window.axios;
            if (!ax || !THEM_SP_SEARCH_URL) {
                suaSpRemoteList = null;
                suaSpRenderCards();
                return;
            }
            var reqId = ++suaSpSearchReqId;
            ax.get(THEM_SP_SEARCH_URL, { params: { q: raw.trim() } })
                .then(function (res) {
                    if (reqId !== suaSpSearchReqId) return;
                    var items = (res.data && Array.isArray(res.data.items)) ? res.data.items : [];
                    items.forEach(function (p) {
                        if (p && p.id != null) themSpById[p.id] = p;
                    });
                    suaSpRemoteList = items;
                    suaSpRenderCards();
                })
                .catch(function () {
                    if (reqId !== suaSpSearchReqId) return;
                    suaSpRemoteList = [];
                    suaSpRenderCards();
                });
        }, 300);
    }
    if (suaTimInput && suaKetQua && suaHiddenWrap) {
        suaSpSyncHidden();
        suaSpRenderChips();
        suaSpRenderCards();
        suaTimInput.addEventListener('input', function () {
            suaSpScheduleTimKiem();
        });
        suaKetQua.addEventListener('click', function (ev) {
            if (ev.target.closest('.them-sp-btn-lich')) return;
            var card = ev.target.closest('[data-sua-sp-id]');
            if (!card || !suaKetQua.contains(card)) return;
            suaSpToggle(card.getAttribute('data-sua-sp-id'));
        });
        suaKetQua.addEventListener('keydown', function (ev) {
            if (ev.target.closest('.them-sp-btn-lich')) return;
            if (ev.key !== 'Enter' && ev.key !== ' ') return;
            var card = ev.target.closest('[data-sua-sp-id]');
            if (!card || !suaKetQua.contains(card)) return;
            ev.preventDefault();
            suaSpToggle(card.getAttribute('data-sua-sp-id'));
        });
        if (suaDaChon) {
            suaDaChon.addEventListener('click', function (ev) {
                var btnRm = ev.target.closest('[data-sua-sp-remove]');
                if (!btnRm || !suaDaChon.contains(btnRm)) return;
                ev.preventDefault();
                suaSpToggle(btnRm.getAttribute('data-sua-sp-remove'));
            });
        }
        function bindSuaHopDongFlatpickrNgay(elId) {
            var el = document.getElementById(elId);
            if (!el || !el._flatpickr) return;
            var fp = el._flatpickr;
            var oc = fp.config.onChange;
            var list = Array.isArray(oc) ? oc.slice() : (typeof oc === 'function' ? [oc] : []);
            list.push(function() { updateSuaSoNgayThueDisplay(); });
            fp.config.onChange = list;
            el.addEventListener('change', updateSuaSoNgayThueDisplay);
        }
        bindSuaHopDongFlatpickrNgay('sua_thoi_gian_bat_dau');
        bindSuaHopDongFlatpickrNgay('sua_thoi_gian_du_kien_tra');
    }

    // Modal Sửa: gán data vào form
    var modalSua = document.getElementById('modalSuaHopDong');
    var formSua = document.getElementById('formSuaHopDong');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-hop-dong')) return;
            var ttSua = parseInt(btn.getAttribute('data-trang-thai') || '0', 10);
            if (ttSua !== 0) {
                e.preventDefault();
                return;
            }
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ten_khach_hang').value = btn.getAttribute('data-ten-khach-hang') || '';
            document.getElementById('sua_so_dien_thoai').value = btn.getAttribute('data-so-dien-thoai') || '';
            suaSpSelected.clear();
            var idsRaw = btn.getAttribute('data-trang-phuc-ids') || '';
            idsRaw.split(',').forEach(function (piece) {
                var n = parseInt(String(piece).trim(), 10);
                if (!isNaN(n) && n > 0) suaSpSelected.add(n);
            });
            suaSpRemoteList = null;
            if (suaTimInput) suaTimInput.value = '';
            suaSpSyncHidden();
            suaSpRenderChips();
            suaSpRenderCards();
            var hidTra = document.getElementById('sua_hidden_thoi_gian_tra_chinh_thuc');
            var hidTt = document.getElementById('sua_hidden_trang_thai');
            if (hidTra) hidTra.value = btn.getAttribute('data-thoi-gian-tra-chinh-thuc') || '';
            if (hidTt) hidTt.value = btn.getAttribute('data-trang-thai') || '0';
            if (window.setAdminDateInput) {
                setAdminDateInput('sua_thoi_gian_bat_dau', btn.getAttribute('data-thoi-gian-bat-dau') || '');
                setAdminDateInput('sua_thoi_gian_du_kien_tra', btn.getAttribute('data-thoi-gian-du-kien-tra') || '');
            } else {
                var elBd = document.getElementById('sua_thoi_gian_bat_dau');
                var elKt = document.getElementById('sua_thoi_gian_du_kien_tra');
                if (elBd) elBd.value = btn.getAttribute('data-thoi-gian-bat-dau') || '';
                if (elKt) elKt.value = btn.getAttribute('data-thoi-gian-du-kien-tra') || '';
            }
            updateSuaSoNgayThueDisplay();
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
            var ttRaw = btn.getAttribute('data-tong-tien');
            var tc = btn.getAttribute('data-tien-coc');
            hopDongSetMoneyFields('sua_tong_tien_hien_thi', 'sua_tong_tien', ttRaw, '');
            hopDongSetMoneyFields('sua_tien_coc_hien_thi', 'sua_tien_coc', (tc !== null && tc !== '') ? tc : '0', '0');
            if (suaTrangPhucErr) suaTrangPhucErr.classList.add('d-none');
        });
    }
    if (formSua) {
        formSua.addEventListener('submit', function (ev) {
            hopDongSyncSuaMoneyInputs();
            suaSpSyncHidden();
            var coSanPham = suaSpSelected.size > 0
                || formSua.querySelectorAll('input[name="trang_phuc[]"]').length > 0;
            if (coSanPham) return;
            ev.preventDefault();
            if (suaTrangPhucErr) suaTrangPhucErr.classList.remove('d-none');
        });
    }

    // Modal cập nhật trạng thái hợp đồng
    var modalCapNhatTt = document.getElementById('modalCapNhatTrangThaiHopDong');
    var formCapNhatTt = document.getElementById('formCapNhatTrangThaiHopDong');
    var ttModalTitleDefault = 'Cập nhật trạng thái hợp đồng';
    function ttModalTriggerSelectChange(sel) {
        if (!sel) return;
        var $ = window.jQuery || window.$;
        if ($ && $.fn.select2) $(sel).trigger('change');
        else sel.dispatchEvent(new Event('change', { bubbles: true }));
    }
    function syncCapNhatTrangThaiNgayField() {
        var sel = document.getElementById('tt_modal_select_trang_thai');
        var wrap = document.getElementById('wrap_tt_modal_ngay_tra');
        var inp = document.getElementById('tt_modal_ngay_tra_chinh_thuc');
        if (!wrap || !inp || !sel) return;
        if (sel.value === '2') {
            wrap.classList.add('d-none');
            if (window.setAdminDateInput) setAdminDateInput(inp, '');
            else inp.value = '';
        } else {
            wrap.classList.remove('d-none');
        }
    }
    if (modalCapNhatTt && formCapNhatTt) {
        formCapNhatTt.addEventListener('submit', function () {
            hopDongSyncMoneyField('tt_modal_da_thanh_toan_hien_thi', 'tt_modal_tien_coc', '0');
        });
        modalCapNhatTt.addEventListener('hidden.bs.modal', function() {
            var titleEl = document.getElementById('modalCapNhatTrangThaiHopDongLabel');
            if (titleEl) titleEl.textContent = ttModalTitleDefault;
        });
        modalCapNhatTt.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-cap-nhat-trang-thai-hop-dong')) return;
            var url = btn.getAttribute('data-url');
            if (url) formCapNhatTt.action = url;
            var tenKhach = (btn.getAttribute('data-ten-khach') || '').trim();
            var titleEl = document.getElementById('modalCapNhatTrangThaiHopDongLabel');
            if (titleEl) {
                titleEl.textContent = tenKhach ? (tenKhach + ' — ' + ttModalTitleDefault) : ttModalTitleDefault;
            }
            var tongTienRaw = btn.getAttribute('data-tong-tien');
            var tienCocRaw = btn.getAttribute('data-tien-coc');
            var tongDisp = document.getElementById('tt_modal_tong_tien_hien_thi');
            if (tongDisp) {
                var tongNum = Math.max(0, Math.round(hopDongToNumber(tongTienRaw)));
                tongDisp.value = tongNum > 0 ? hopDongFormatMoneyInputNumber(tongNum) : '0';
            }
            hopDongSetMoneyFields(
                'tt_modal_da_thanh_toan_hien_thi',
                'tt_modal_tien_coc',
                (tienCocRaw !== null && tienCocRaw !== '') ? tienCocRaw : '0',
                '0'
            );
            var tt = parseInt(btn.getAttribute('data-trang-thai') || '0', 10);
            var selTt = document.getElementById('tt_modal_select_trang_thai');
            if (selTt) {
                selTt.value = tt === 2 ? '2' : '1';
                ttModalTriggerSelectChange(selTt);
            }
            var ngay = btn.getAttribute('data-ngay-tra-chinh-thuc') || '';
            if (window.setAdminDateInput) {
                setAdminDateInput('tt_modal_ngay_tra_chinh_thuc', ngay);
            } else {
                var elNgay = document.getElementById('tt_modal_ngay_tra_chinh_thuc');
                if (elNgay) elNgay.value = ngay;
            }
            syncCapNhatTrangThaiNgayField();
        });
        var selTrangThaiModal = document.getElementById('tt_modal_select_trang_thai');
        if (selTrangThaiModal) selTrangThaiModal.addEventListener('change', syncCapNhatTrangThaiNgayField);
    }

    // --- Check lịch / lịch sử sử dụng sản phẩm (card trong modal HĐ) ---
    var modalHdKtsp = document.getElementById('modalHopDongKiemTraSp');
    var hdKtspTen = document.getElementById('hdKtspTen');
    var hdKtspMa = document.getElementById('hdKtspMa');
    var hdKtspStatus = document.getElementById('hdKtspStatus');
    var hdKtspLoading = document.getElementById('hdKtspLoading');
    var hdKtspError = document.getElementById('hdKtspError');
    var hdKtspContent = document.getElementById('hdKtspContent');
    var hdKtspGroupedWrap = document.getElementById('hdKtspGroupedWrap');
    var hdKtspGroupedEmpty = document.getElementById('hdKtspGroupedEmpty');

    function hdKtspResetUI() {
        if (hdKtspStatus) hdKtspStatus.textContent = '';
        if (hdKtspError) {
            hdKtspError.classList.add('d-none');
            hdKtspError.textContent = '';
        }
        if (hdKtspContent) hdKtspContent.classList.add('d-none');
        if (hdKtspLoading) hdKtspLoading.classList.add('d-none');
        if (hdKtspGroupedWrap) hdKtspGroupedWrap.innerHTML = '';
        if (hdKtspGroupedEmpty) hdKtspGroupedEmpty.classList.add('d-none');
    }

    function hdKtspFmtRange(tu, den) {
        if (!tu && !den) return '—';
        if (tu && den && tu !== den) return tu + ' → ' + den;
        return (tu || den);
    }

    function hdKtspAddDaysInclusive(startYmd, endYmd) {
        if (!startYmd || !endYmd) return [];
        var start = new Date(startYmd + 'T00:00:00');
        var end = new Date(endYmd + 'T00:00:00');
        if (isNaN(start.getTime()) || isNaN(end.getTime())) return [];
        if (end < start) return [];
        var out = [];
        for (var d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            out.push(y + '-' + m + '-' + day);
        }
        return out;
    }

    function hdKtspBuildGroupedByDay(thueItems, cuoiItems) {
        var map = new Map();
        function ensure(ymd) {
            if (!map.has(ymd)) map.set(ymd, { thue: [], cuoi: [] });
            return map.get(ymd);
        }
        (Array.isArray(thueItems) ? thueItems : []).forEach(function (row) {
            hdKtspAddDaysInclusive(row.tu_ngay, row.den_ngay).forEach(function (ymd) {
                ensure(ymd).thue.push(row);
            });
        });
        (Array.isArray(cuoiItems) ? cuoiItems : []).forEach(function (row) {
            if (!row.ngay) return;
            ensure(row.ngay).cuoi.push(row);
        });
        var daysSorted = Array.from(map.keys()).sort(function (a, b) { return a.localeCompare(b); });
        return { map: map, days: daysSorted };
    }

    function hdKtspRenderGrouped(thueItems, cuoiItems) {
        var grouped = hdKtspBuildGroupedByDay(thueItems, cuoiItems);
        if (!grouped.days.length) {
            if (hdKtspGroupedEmpty) hdKtspGroupedEmpty.classList.remove('d-none');
            return;
        }
        grouped.days.forEach(function (ymd, idx) {
            var bucket = grouped.map.get(ymd) || { thue: [], cuoi: [] };
            var headId = 'hd-ktsp-day-head-' + idx;
            var collapseId = 'hd-ktsp-day-body-' + idx;
            var total = (bucket.thue?.length || 0) + (bucket.cuoi?.length || 0);
            var item = document.createElement('div');
            item.className = 'accordion-item';
            item.innerHTML =
                '<h2 class="accordion-header" id="' + headId + '">' +
                    '<button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '" aria-expanded="false" aria-controls="' + collapseId + '">' +
                        '<div class="d-flex w-100 align-items-center justify-content-between gap-2">' +
                            '<div class="fw-semibold ms-2">' + hopDongSpEscHtml(ymd) + '</div>' +
                            '<span class="badge bg-label-primary">' + hopDongSpEscHtml(total) + ' đơn</span>' +
                        '</div>' +
                    '</button>' +
                '</h2>' +
                '<div id="' + collapseId + '" class="accordion-collapse collapse" aria-labelledby="' + headId + '" data-bs-parent="#hdKtspGroupedWrap">' +
                    '<div class="px-0"><ul class="list-group list-group-flush" data-hd-ktsp-day-list="1"></ul></div>' +
                '</div>';
            if (hdKtspGroupedWrap) hdKtspGroupedWrap.appendChild(item);
            var ul = item.querySelector('ul[data-hd-ktsp-day-list="1"]');
            if (!ul) return;
            bucket.thue.forEach(function (row) {
                var rangeTxt = hdKtspFmtRange(row.tu_ngay, row.den_ngay);
                var kh = row.khach_hang || {};
                var ten = (kh.ten || '').trim();
                var sdt = (kh.sdt || '').trim();
                var sub = (ten || sdt) ? (hopDongSpEscHtml(ten) + (sdt ? (' • ' + hopDongSpEscHtml(sdt)) : '')) : '';
                var trangThai = (row.trang_thai === 0) ? 'Đang thuê' : ((row.trang_thai === 1) ? 'Hoàn thành' : 'Đã huỷ');
                var badgeCls = (row.trang_thai === 0) ? 'bg-label-warning' : ((row.trang_thai === 1) ? 'bg-label-success' : 'bg-label-danger');
                var li = document.createElement('li');
                li.className = 'list-group-item px-0';
                li.innerHTML =
                    '<div class="d-flex align-items-start justify-content-between gap-2" style="padding-right: 12px;">' +
                        '<div class="min-w-0 hd-ktsp-list-info">' +
                            '<div class="fw-medium text-body text-truncate"><span class="badge bg-label-secondary me-2">Thuê</span>HĐ #' + hopDongSpEscHtml(row.hop_dong_id) + '</div>' +
                            (sub ? ('<div class="text-muted small text-truncate">' + sub + '</div>') : '') +
                            '<div class="text-body small mt-1"><i class="fa-regular fa-calendar me-1 text-muted"></i>' + hopDongSpEscHtml(rangeTxt) + '</div>' +
                        '</div>' +
                        '<span class="badge ' + badgeCls + ' align-self-start">' + hopDongSpEscHtml(trangThai) + '</span>' +
                    '</div>';
                ul.appendChild(li);
            });
            bucket.cuoi.forEach(function (row) {
                var ma = (row.ma_hop_dong || '').trim();
                var capdoi = (row.cap_doi || '').trim();
                var li = document.createElement('li');
                li.className = 'list-group-item px-0';
                li.innerHTML =
                    '<div class="d-flex align-items-start justify-content-between gap-2">' +
                        '<div class="min-w-0 hd-ktsp-list-info">' +
                            '<div class="fw-medium text-body text-truncate"><span class="badge bg-label-info me-2">Cưới</span>HĐ #' + hopDongSpEscHtml(row.hop_dong_id) + (ma ? (' • ' + hopDongSpEscHtml(ma)) : '') + '</div>' +
                            (capdoi ? ('<div class="text-muted small text-truncate">' + hopDongSpEscHtml(capdoi) + '</div>') : '') +
                        '</div>' +
                    '</div>';
                ul.appendChild(li);
            });
        });
    }

    async function hdKtspFetch(url) {
        var res = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        if (!res.ok) {
            var t = await res.text();
            throw new Error('HTTP ' + res.status + ': ' + t);
        }
        return await res.json();
    }

    function hopDongSpOpenKiemTra(btn) {
        if (!modalHdKtsp || !btn) return;
        var ten = btn.getAttribute('data-ten') || '';
        var ma = btn.getAttribute('data-ma') || '';
        var url = btn.getAttribute('data-url') || '';
        if (!url) {
            if (typeof window.showToast === 'function') {
                window.showToast('Không có đường dẫn kiểm tra lịch sản phẩm.', 'danger');
            }
            return;
        }
        if (hdKtspTen) hdKtspTen.textContent = ten || '—';
        if (hdKtspMa) hdKtspMa.textContent = ma ? ('Mã: ' + ma) : '—';
        hdKtspResetUI();
        if (hdKtspLoading) hdKtspLoading.classList.remove('d-none');
        var inst = bootstrap.Modal.getOrCreateInstance(modalHdKtsp);
        inst.show();
        hdKtspFetch(url)
            .then(function (data) {
                if (hdKtspLoading) hdKtspLoading.classList.add('d-none');
                if (hdKtspContent) hdKtspContent.classList.remove('d-none');
                var thue = data && data.thue ? data.thue : [];
                var cuoi = data && data.cuoi ? data.cuoi : [];
                hdKtspRenderGrouped(thue, cuoi);
                if (hdKtspStatus) {
                    hdKtspStatus.textContent = 'Thuê: ' + (Array.isArray(thue) ? thue.length : 0) + ' • Cưới: ' + (Array.isArray(cuoi) ? cuoi.length : 0);
                }
            })
            .catch(function (err) {
                if (hdKtspLoading) hdKtspLoading.classList.add('d-none');
                if (hdKtspError) {
                    hdKtspError.textContent = 'Không tải được dữ liệu lịch sử dụng. ' + (err && err.message ? err.message : '');
                    hdKtspError.classList.remove('d-none');
                }
            });
    }

    document.addEventListener('click', function (ev) {
        var btnLich = ev.target.closest('.them-sp-btn-lich');
        if (!btnLich) return;
        var inThem = themKetQua && themKetQua.contains(btnLich);
        var inSua = suaKetQua && suaKetQua.contains(btnLich);
        if (!inThem && !inSua) return;
        ev.preventDefault();
        ev.stopPropagation();
        hopDongSpOpenKiemTra(btnLich);
    });

    if (modalHdKtsp) {
        modalHdKtsp.addEventListener('shown.bs.modal', function () {
            document.body.classList.add('modal-hop-dong-ktsp-open');
        });
        modalHdKtsp.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-hop-dong-ktsp-open');
            hdKtspResetUI();
        });
    }

    // Xóa: modal xác nhận
    var modalXoa = document.getElementById('modalXacNhanXoaHopDong');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaHopDong');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        document.querySelectorAll('.btn-xoa-hop-dong').forEach(function(btn) {
            btn.addEventListener('click', function() {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                var modal = bootstrap.Modal.getOrCreateInstance(modalXoa);
                modal.show();
            });
        });
        btnXacNhanXoa.addEventListener('click', function() {
            if (formIdCanXoa) {
                var form = document.getElementById(formIdCanXoa);
                if (form) form.submit();
            }
            var inst = bootstrap.Modal.getInstance(modalXoa);
            if (inst) inst.hide();
            formIdCanXoa = null;
        });
    }
});
</script>
@endpush
@endsection
