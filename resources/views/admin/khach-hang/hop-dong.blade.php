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
        <form action="{{ route('admin.khach-hang.hop-dong') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên khách, Email/SĐT hoặc mã hợp đồng</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Tên, SĐT, email hoặc mã HĐ...">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.khach-hang.hop-dong') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách hợp đồng</span>
            <span class="d-flex flex-wrap align-items-center gap-2">
                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalHopDongCuoi">
                    <i class="fa-solid fa-ring me-1"></i> Hợp đồng cưới
                </button>
                <span data-bs-toggle="tooltip" title="Thêm hợp đồng mới">
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalThemHopDong">
                        <i class="fa-solid fa-plus me-1"></i> Thêm mới
                    </button>
                </span>
            </span>
        </h5>
        <div class="card-body">
        @php
            $trangPhucMap = ($danhSachTrangPhuc ?? collect())->keyBy('id');
            $conceptMap = ($danhSachConcept ?? collect())->keyBy('id');
        @endphp

        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Mã HĐ</th>
                        <th>Người tạo</th>
                        <th>Khách hàng</th>
                        <th>Địa điểm</th>
                        <th>Ngày chụp</th>
                        <th>Ngày hẹn trả demo</th>
                        <th>Trang phục</th>
                        <th>Ghi chú</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-end">Thanh toán lần 1</th>
                        <th class="text-end">Thanh toán lần 2</th>
                        <th class="text-end">Thanh toán lần 3</th>
                        <th>Trạng thái chụp</th>
                        <th>Trạng thái edit</th>
                        <th>Trạng thái HĐ</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $conceptHd = trim((string) ($item->concept ?? ''));
                        $tenKhachHang = $conceptHd !== '' ? (string) str($conceptHd)->limit(40) : '—';
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td>{{ $item->ma_hop_dong ?? '—' }}</td>
                        <td>{{ $item->nguoiTao?->name ?? '—' }}</td>
                        <td><span class="fw-medium">{{ $tenKhachHang }}</span></td>
                        <td>{{ $item->dia_diem ? str($item->dia_diem)->limit(25) : '—' }}</td>
                        <td>{{ $item->ngay_chup ? $item->ngay_chup->format('d/m/Y H:i') : '—' }}</td>
                        <td>{{ $item->ngay_hen_tra_hang ? $item->ngay_hen_tra_hang->format('d/m/Y') : '—' }}</td>
                        <td>
                            @php
                                $rawTrangPhuc = (string) ($item->trang_phuc ?? '');
                                $trangPhucIds = array_filter(array_map('trim', explode(',', $rawTrangPhuc)));
                                $trangPhucNames = collect($trangPhucIds)
                                    ->map(fn($id) => $trangPhucMap[(int) $id]->ten_san_pham ?? null)
                                    ->filter()
                                    ->values();
                            @endphp
                            {{ $trangPhucNames->isNotEmpty() ? str($trangPhucNames->implode(', '))->limit(30) : ($item->trang_phuc ? str($item->trang_phuc)->limit(30) : '—') }}
                        </td>
                        <td>{{ $item->ghi_chu_chup ? str($item->ghi_chu_chup)->limit(40) : '—' }}</td>
                        <td class="text-end">
                            {{ $item->tong_tien !== null ? number_format((float)$item->tong_tien, 0, ',', '.') . ' đ' : '—' }}
                        </td>
                        <td class="text-end">
                            @if($item->thanh_toan_lan_1 !== null)
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="flex-grow-1 text-end">
                                        {{ number_format((float)$item->thanh_toan_lan_1, 0, ',', '.') . ' đ' }}
                                    </span>
                                    @if(!empty($item->anh_thanh_toan_1))
                                        <button type="button"
                                                onclick="xemAnhThanhToan('{{ asset('storage/' . $item->anh_thanh_toan_1) }}', 'Ảnh thanh toán lần 1')"
                                                class="btn btn-sm btn-outline-secondary ms-2 btn-xem-anh-thanh-toan">
                                            Xem
                                        </button>
                                    @endif
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">
                            @if($item->thanh_toan_lan_2 !== null)
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="flex-grow-1 text-end">
                                        {{ number_format((float)$item->thanh_toan_lan_2, 0, ',', '.') . ' đ' }}
                                    </span>
                                    @if(!empty($item->anh_thanh_toan_2))
                                        <button type="button"
                                                onclick="xemAnhThanhToan('{{ asset('storage/' . $item->anh_thanh_toan_2) }}', 'Ảnh thanh toán lần 2')"
                                                class="btn btn-sm btn-outline-secondary ms-2 btn-xem-anh-thanh-toan">
                                            Xem
                                        </button>
                                    @endif
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">
                            @if($item->thanh_toan_lan_3 !== null)
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <span class="flex-grow-1 text-end">
                                        {{ number_format((float)$item->thanh_toan_lan_3, 0, ',', '.') . ' đ' }}
                                    </span>
                                    @if(!empty($item->anh_thanh_toan_3))
                                        <button type="button"
                                                onclick="xemAnhThanhToan('{{ asset('storage/' . $item->anh_thanh_toan_3) }}', 'Ảnh thanh toán lần 3')"
                                                class="btn btn-sm btn-outline-secondary ms-2 btn-xem-anh-thanh-toan">
                                            Xem
                                        </button>
                                    @endif
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $item->trang_thai_chup ?? '—' }}</td>
                        <td>{{ $item->trang_thai_edit ?? '—' }}</td>
                        <td>{{ $item->trang_thai_hop_dong ?? '—' }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button type="button"
                                            class="dropdown-item btn-them-anh-thanh-toan"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalAnhThanhToan"
                                            data-hop-dong-id="{{ $item->id }}"
                                            data-thanh-toan-lan-1="{{ $item->thanh_toan_lan_1 !== null ? $item->thanh_toan_lan_1 : '' }}"
                                            data-thanh-toan-lan-2="{{ $item->thanh_toan_lan_2 !== null ? $item->thanh_toan_lan_2 : '' }}"
                                            data-thanh-toan-lan-3="{{ $item->thanh_toan_lan_3 !== null ? $item->thanh_toan_lan_3 : '' }}"
                                            data-anh-thanh-toan-1="{{ !empty($item->anh_thanh_toan_1) ? asset('storage/' . $item->anh_thanh_toan_1) : '' }}"
                                            data-anh-thanh-toan-2="{{ !empty($item->anh_thanh_toan_2) ? asset('storage/' . $item->anh_thanh_toan_2) : '' }}"
                                            data-anh-thanh-toan-3="{{ !empty($item->anh_thanh_toan_3) ? asset('storage/' . $item->anh_thanh_toan_3) : '' }}"
                                            data-upload-url="{{ route('admin.khach-hang.hop-dong.upload-anh-thanh-toan') }}">
                                        <i class="fa-solid fa-image me-2"></i> Thêm ảnh thanh toán
                                    </button>
                                    <a class="dropdown-item btn-sua-hop-dong"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaHopDong"
                                       data-hop-dong-id="{{ $item->id }}"
                                       data-url="{{ route('admin.khach-hang.update-hop-dong', $item) }}"
                                       data-dich-vu-url="{{ route('admin.khach-hang.hop-dong.dich-vu', $item) }}"
                                       data-khach-hang-id="{{ $item->khach_hang_id ?? '' }}"
                                       data-tho-chup-id="{{ $item->tho_chup_id ?? '' }}"
                                       data-tho-make-id="{{ $item->tho_make_id ?? '' }}"
                                       data-tho-edit-id="{{ $item->tho_edit_id ?? '' }}"
                                       data-dia-diem="{{ e($item->dia_diem ?? '') }}"
                                       data-ngay-chup="{{ $item->ngay_chup?->format('Y-m-d H:i') ?? '' }}"
                                       data-trang-phuc="{{ e($item->trang_phuc ?? '') }}"
                                       data-concept="{{ e($item->concept ?? '') }}"
                                       data-concept-label="{{ e(optional($conceptMap->get((int)($item->concept ?? 0)))->ten_concept ?? ($item->concept ?? '')) }}"
                                       data-ghi-chu-chup="{{ e($item->ghi_chu_chup ?? '') }}"
                                       data-trang-thai-chup="{{ e($item->trang_thai_chup ?? '') }}"
                                       data-tong-tien="{{ $item->tong_tien !== null ? $item->tong_tien : '' }}"
                                       data-nguoi-gioi-thieu="{{ $item->nguoi_gioi_thieu ?? '' }}"
                                       data-so-tien-giam-gia="{{ $item->so_tien_giam_gia !== null ? $item->so_tien_giam_gia : '' }}"
                                       data-thanh-toan-lan-1="{{ $item->thanh_toan_lan_1 !== null ? $item->thanh_toan_lan_1 : '' }}"
                                       data-thanh-toan-lan-2="{{ $item->thanh_toan_lan_2 !== null ? $item->thanh_toan_lan_2 : '' }}"
                                       data-thanh-toan-lan-3="{{ $item->thanh_toan_lan_3 !== null ? $item->thanh_toan_lan_3 : '' }}"
                                       data-khoa-thanh-toan-lan-1="{{ !empty($item->anh_thanh_toan_1) ? '1' : '0' }}"
                                       data-trang-thai-hop-dong="{{ e($item->trang_thai_hop_dong ?? '') }}"
                                       data-trang-thai-edit="{{ e($item->trang_thai_edit ?? '') }}"
                                       data-link-file-demo="{{ e($item->link_file_demo ?? '') }}"
                                       data-link-file-in="{{ e($item->link_file_in ?? '') }}"
                                       data-ngay-tra-link-in="{{ $item->ngay_tra_link_in?->format('Y-m-d') ?? '' }}"
                                       data-ngay-hen-tra-hang="{{ $item->ngay_hen_tra_hang?->format('Y-m-d') ?? '' }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-hd-{{ $item->id }}" action="{{ route('admin.khach-hang.destroy-hop-dong', $item) }}" method="POST" class="d-inline">
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
                        <td colspan="16" class="text-center py-4 text-muted">Chưa có hợp đồng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="hợp đồng" />
        </div>
    </div>
</div>

<script>
    // Hàm xem ảnh thanh toán, được đặt ngay dưới bảng để luôn có sẵn cho các nút "Xem"
    function xemAnhThanhToan(url, title) {
        console.log('[XEM ANH] gọi xemAnhThanhToan với:', { url: url, title: title });

        var modal = document.getElementById('modalXemAnhThanhToan');
        var img = document.getElementById('imgXemAnhThanhToan');
        var titleEl = document.getElementById('modalXemAnhThanhToanLabel');

        if (!modal || !img) {
            console.warn('[XEM ANH] Không tìm thấy modal hoặc thẻ img hiển thị ảnh.');
            return;
        }

        if (!url) {
            console.warn('[XEM ANH] URL ảnh không hợp lệ:', url);
            return;
        }

        img.src = url;
        if (titleEl) {
            titleEl.textContent = title || 'Ảnh thanh toán';
        }

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var instance = bootstrap.Modal.getOrCreateInstance(modal);
            instance.show();
        } else {
            console.warn('[XEM ANH] bootstrap.Modal chưa sẵn sàng, không thể mở modal.');
        }
    }
</script>

{{-- Modal Thêm mới hợp đồng --}}
<div class="modal fade" id="modalThemHopDong" tabindex="-1" aria-labelledby="modalThemHopDongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-hop-dong">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemHopDongLabel">Thêm hợp đồng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.khach-hang.store-hop-dong') }}" method="POST" id="formThemHopDong">
                @csrf
                <input type="hidden" name="_form" value="hop_dong">
                @if($errors->any() && old('_form') !== 'hop_dong_cuoi')
                <div class="modal-body py-0">
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Hàng 1: 4 cột (lg), 2 cột (md), 1 cột (xs) --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_khach_hang_id">Khách hàng <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select" id="them_khach_hang_id" name="khach_hang_id" required data-placeholder="Chọn khách hàng">
                                <option value="">-- Chọn khách hàng --</option>
                                @foreach($danhSachKhachHang ?? [] as $kh)
                                <option value="{{ $kh->id }}" {{ (string)old('khach_hang_id') === (string)$kh->id ? 'selected' : '' }}>
                                    {{ $kh->ho_ten_chu_re ?? '' }} / {{ $kh->ho_ten_co_dau ?? '' }} ({{ $kh->email_hoac_sdt_chu_re ?? $kh->email_hoac_sdt_co_dau ?? '—' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_dia_diem">Địa điểm (Dự kiến)</label>
                            <input type="text" class="form-control" id="them_dia_diem" name="dia_diem" value="{{ old('dia_diem') }}" placeholder="Địa điểm chụp">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_ngay_chup">Ngày chụp (Dự kiến)</label>
                            <input type="text" class="flatpickr-datetime-admin form-control" id="them_ngay_chup" name="ngay_chup" value="{{ old('ngay_chup') }}" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="them_ngay_hen_tra_hang">Ngày hẹn trả demo (Dự kiến)</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="them_ngay_hen_tra_hang" name="ngay_hen_tra_hang" value="{{ old('ngay_hen_tra_hang') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        {{-- Tabs: Nhóm dịch vụ & Dịch vụ lẻ (Filled Pills) --}}
                        <div class="col-12 mt-2">
                            <label class="form-label d-block">Tham khảo dịch vụ</label>
                            <div class="nav-align-top">
                                <ul class="nav nav-pills mb-4 nav-fill" id="tabDichVuThemHopDong" role="tablist">
                                    <li class="nav-item mb-1 mb-sm-0" role="presentation">
                                        <button type="button" class="nav-link active" id="tab-nhom-dich-vu-btn" role="tab" data-bs-toggle="tab" data-bs-target="#tab-nhom-dich-vu" aria-controls="tab-nhom-dich-vu" aria-selected="true">Nhóm dịch vụ</button>
                                    </li>
                                    <li class="nav-item mb-1 mb-sm-0" role="presentation">
                                        <button type="button" class="nav-link" id="tab-dich-vu-le-btn" role="tab" data-bs-toggle="tab" data-bs-target="#tab-dich-vu-le" aria-controls="tab-dich-vu-le" aria-selected="false">Dịch vụ lẻ</button>
                                    </li>
                                </ul>
                                {{-- Ô tìm kiếm nhanh: luôn cùng một hàng (flex-nowrap) --}}
                                    <div class="d-flex flex-nowrap align-items-end gap-2 mb-3">
                                        <div class="flex-grow-1 min-w-0">
                                            <label class="form-label d-block mb-1" for="them_tim_dich_vu">Tìm theo tên hoặc mã</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="them_tim_dich_vu"
                                                   placeholder="Nhập tên hoặc mã dịch vụ...">
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button" class="btn btn-outline-secondary" id="btnXoaLocThemDichVu">Bỏ lọc</button>
                                        </div>
                                    </div>
                                <div class="tab-content p-3 bg-light rounded" id="tabDichVuThemHopDongContent">
                                    
                                <div class="tab-pane fade show active" id="tab-nhom-dich-vu" role="tabpanel">
                                    <div class="table-responsive hop-dong-dich-vu-wide">
                                        <table class="table table-sm table-hover table-bordered mb-0" id="tableNhomDichVuThem">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center"></th>
                                                    <th class="text-center" style="width: 50px;">STT</th>
                                                    <th>Tên nhóm</th>
                                                    <th>Mã</th>
                                                    <th class="text-end">Giá tiền</th>
                                                    <th class="text-end">Giá gốc</th>
                                                    <th>Thẻ</th>
                                                    <th>Ghi chú</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($danhSachNhomDichVu ?? [] as $idx => $ndv)
                                                @php
                                                    $dvlList = $ndv->dichVuLe->map(fn($d) => [
                                                        'id' => $d->id,
                                                        'ten_dich_vu' => $d->ten_dich_vu ?? '—',
                                                        'ma_dich_vu' => $d->ma_dich_vu ?? '—',
                                                        'gia_goc' => $d->gia_dich_vu !== null ? (float)$d->gia_dich_vu : 0,
                                                        'gia_dich_vu' => $d->gia_dich_vu !== null ? number_format((float)$d->gia_dich_vu, 0, ',', '.') . ' đ' : '—',
                                                        'ghi_chu' => $d->ghi_chu ? \Illuminate\Support\Str::limit($d->ghi_chu, 40) : '—',
                                                        'so_luong' => $d->pivot->so_luong ?? 1,
                                                    ])->values()->toArray();
                                                @endphp
                                                <tr data-nhom-id="{{ $ndv->id }}"
                                                    data-ten-nhom="{{ e($ndv->ten_nhom ?? '') }}"
                                                    data-dich-vu-le='{{ json_encode($dvlList, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}'>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input cb-nhom-dich-vu" value="{{ $ndv->id }}" aria-label="Chọn nhóm {{ $ndv->ten_nhom }}">
                                                    </td>
                                                    <td class="text-center">{{ $idx + 1 }}</td>
                                                    <td>{{ $ndv->ten_nhom ?? '—' }}</td>
                                                    <td>{{ $ndv->ma_nhom ?? '—' }}</td>
                                                    <td class="text-end">{{ $ndv->gia_tien !== null ? number_format((float)$ndv->gia_tien, 0, ',', '.') . ' đ' : '—' }}</td>
                                                    <td class="text-end">{{ $ndv->gia_goc !== null ? number_format((float)$ndv->gia_goc, 0, ',', '.') . ' đ' : '—' }}</td>
                                                    <td>{{ $ndv->the ?? '—' }}</td>
                                                    <td>{{ $ndv->ghi_chu ? str($ndv->ghi_chu)->limit(40) : '—' }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="8" class="text-center text-muted py-3">Chưa có nhóm dịch vụ.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-dich-vu-le" role="tabpanel">
                                    <div class="table-responsive hop-dong-dich-vu-wide">
                                        <table class="table table-sm table-hover table-bordered mb-0" id="tableDichVuLeThem">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center"></th>
                                                    <th class="text-center" style="width: 50px;">STT</th>
                                                    <th>Tên dịch vụ</th>
                                                    <th>Mã</th>
                                                    <th class="text-end">Giá dịch vụ</th>
                                                    <th>Ghi chú</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($danhSachDichVuLe ?? [] as $idx => $dvl)
                                                @php
                                                    $dvlItem = [
                                                        'id' => $dvl->id,
                                                        'ten_dich_vu' => $dvl->ten_dich_vu ?? '—',
                                                        'ma_dich_vu' => $dvl->ma_dich_vu ?? '—',
                                                        'gia_goc' => $dvl->gia_dich_vu !== null ? (float)$dvl->gia_dich_vu : 0,
                                                        'gia_dich_vu' => $dvl->gia_dich_vu !== null ? number_format((float)$dvl->gia_dich_vu, 0, ',', '.') . ' đ' : '—',
                                                        'ghi_chu' => $dvl->ghi_chu ? \Illuminate\Support\Str::limit($dvl->ghi_chu, 40) : '—',
                                                        'so_luong' => 1,
                                                    ];
                                                @endphp
                                                <tr data-dich-vu-le='{{ json_encode($dvlItem, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}'>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input cb-dich-vu-le" value="{{ $dvl->id }}" aria-label="Chọn dịch vụ {{ $dvl->ten_dich_vu }}">
                                                    </td>
                                                    <td class="text-center">{{ $idx + 1 }}</td>
                                                    <td>{{ $dvl->ten_dich_vu ?? '—' }}</td>
                                                    <td>{{ $dvl->ma_dich_vu ?? '—' }}</td>
                                                    <td class="text-end">{{ $dvl->gia_dich_vu !== null ? number_format((float)$dvl->gia_dich_vu, 0, ',', '.') . ' đ' : '—' }}</td>
                                                    <td>{{ $dvl->ghi_chu ? str($dvl->ghi_chu)->limit(40) : '—' }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="6" class="text-center text-muted py-3">Chưa có dịch vụ lẻ.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        {{-- Bảng riêng: Dịch vụ lẻ của nhóm đã chọn (tách khỏi tabs, trên Trang phục) --}}
                        <div class="col-12 d-none" id="boxDichVuLeTheoNhom">
                            <label class="form-label fw-medium">Dịch vụ lẻ của nhóm đã chọn</label>
                            <div class="table-responsive hop-dong-dich-vu-wide hop-dong-dich-vu-wide--compact border rounded">
                                        <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">Chọn</th>
                                            <th>Nhóm</th>
                                            <th>Tên</th>
                                            <th>Mã</th>
                                            <th class="text-end">Giá gốc</th>
                                            <th class="text-end">Giá thực</th>
                                            <th style="width: 110px;">Số lượng</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDichVuLeTheoNhom">
                                    </tbody>
                                    <tfoot id="tfootDichVuLeTheoNhom" class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end fw-medium">Tổng</td>
                                            <td class="text-end fw-medium" id="tdTongGiaGoc">0 đ</td>
                                            <td class="text-end fw-medium" id="tdTongGiaThuc">0 đ</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_trang_phuc">Trang phục</label>
                            <select class="select2-admin form-select"
                                    id="them_trang_phuc"
                                    name="trang_phuc[]"
                                    multiple
                                    data-placeholder="Chọn trang phục">
                                @php $oldTrangPhucIds = old('trang_phuc', []); @endphp
                                @foreach($danhSachTrangPhuc ?? [] as $sp)
                                    @php
                                        $sdDates = $trangPhucSuDungTuHomNay[$sp->id] ?? [];
                                        $sdLabel = !empty($sdDates) ? ' - (SD: ' . implode(', ', $sdDates) . ')' : '';
                                    @endphp
                                    <option value="{{ $sp->id }}"
                                            data-avatar="{{ !empty($sp->hinh_anh) ? asset('storage/' . $sp->hinh_anh) : '' }}"
                                        {{ is_array($oldTrangPhucIds) && in_array($sp->id, $oldTrangPhucIds) ? 'selected' : '' }}>
                                        {{ $sp->ten_san_pham }}{{ $sdLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_concept">Concept</label>
                            <select class="select2-admin form-select" id="them_concept" name="concept" data-placeholder="Chọn concept">
                                <option value="">-- Chọn concept --</option>
                                @foreach($danhSachConcept ?? [] as $c)
                                <option value="{{ $c->id }}"
                                        data-avatar="{{ !empty($c->hinh_anh) ? asset('storage/' . $c->hinh_anh) : '' }}"
                                    {{ (string)old('concept') === (string)($c->id ?? '') ? 'selected' : '' }}>
                                    {{ $c->ten_concept }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_ghi_chu_chup">Ghi chú chụp</label>
                            <textarea class="form-control" id="them_ghi_chu_chup" name="ghi_chu_chup" rows="2" placeholder="Ghi chú">{{ old('ghi_chu_chup') }}</textarea>
                        </div>
                        {{-- Tổng tiền → mã giới thiệu → giảm giá → cọc → còn lại (1 hàng từ lg) --}}
                        <div class="col-12">
                            <div class="row g-2 row-cols-1 row-cols-sm-2 row-cols-lg-5 hop-dong-tien-row align-items-end">
                                <div class="col">
                                    <label class="form-label mb-1" for="them_tong_tien_display">Tổng tiền</label>
                                    <input type="hidden" name="tong_tien" id="them_tong_tien" value="{{ old('tong_tien', '0') }}">
                                    <input type="text" class="form-control hop-dong-tien-control bg-body-secondary text-end fw-semibold" id="them_tong_tien_display" readonly tabindex="-1" value="0 đ" aria-readonly="true" autocomplete="off">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="them_nguoi_gioi_thieu">Mã giới thiệu (mã HĐ)</label>
                                    <div class="input-group hop-dong-tien-control">
                                        <input type="text" class="form-control" id="them_nguoi_gioi_thieu" name="nguoi_gioi_thieu" value="{{ old('nguoi_gioi_thieu') }}" maxlength="255" placeholder="Mã hợp đồng" autocomplete="off">
                                        <button type="button" class="btn btn-outline-primary" id="btnKiemTraMaGioiThieuThem" title="Kiểm tra mã giới thiệu">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="them_so_tien_giam_gia">Số tiền giảm giá</label>
                                    <input type="number" class="form-control hop-dong-tien-control text-end bg-body-secondary" id="them_so_tien_giam_gia" name="so_tien_giam_gia" value="{{ old('so_tien_giam_gia', '0') }}" min="0" step="0.01" inputmode="decimal" placeholder="0" readonly tabindex="-1" aria-readonly="true" title="Áp dụng tự động sau khi kiểm tra mã giới thiệu">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="them_thanh_toan_lan_1">Tiền cọc</label>
                                    <input type="number" class="form-control hop-dong-tien-control" id="them_thanh_toan_lan_1" name="thanh_toan_lan_1" value="{{ old('thanh_toan_lan_1', '0') }}" min="0" step="0.01">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="them_con_lai">Còn lại</label>
                                    <input type="text" class="form-control hop-dong-tien-control bg-body-secondary" id="them_con_lai" readonly tabindex="-1" value="0 đ" aria-readonly="true" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hợp đồng cưới (bảng hop_dong_cuoi) --}}
<div class="modal fade" id="modalHopDongCuoi" tabindex="-1" aria-labelledby="modalHopDongCuoiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-hop-dong">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHopDongCuoiLabel">Thêm hợp đồng cưới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.khach-hang.store-hop-dong-cuoi') }}" method="POST" id="formHopDongCuoi">
                @csrf
                <input type="hidden" name="_form" value="hop_dong_cuoi">
                @if($errors->any() && old('_form') === 'hop_dong_cuoi')
                <div class="modal-body py-0">
                    <div class="alert alert-danger mb-0 rounded-0 border-0 border-bottom">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase fs-12 mb-0">Thông tin cặp đôi</h6>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="cuoi_ma_hop_dong">Mã hợp đồng</label>
                            <input type="text" class="form-control" id="cuoi_ma_hop_dong" name="ma_hop_dong" value="{{ old('ma_hop_dong') }}" maxlength="30" placeholder="Để trống để hệ thống sinh mã">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="cuoi_ten_co_dau">Tên cô dâu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cuoi_ten_co_dau" name="ten_co_dau" value="{{ old('ten_co_dau') }}" required maxlength="150">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="cuoi_ten_chu_re">Tên chú rể <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cuoi_ten_chu_re" name="ten_chu_re" value="{{ old('ten_chu_re') }}" required maxlength="150">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_email_sdt_co_dau">Email / SĐT cô dâu</label>
                            <textarea class="form-control" id="cuoi_email_sdt_co_dau" name="email_sdt_co_dau" rows="2" placeholder="Email hoặc số điện thoại">{{ old('email_sdt_co_dau') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_email_sdt_chu_re">Email / SĐT chú rể</label>
                            <textarea class="form-control" id="cuoi_email_sdt_chu_re" name="email_sdt_chu_re" rows="2" placeholder="Email hoặc số điện thoại">{{ old('email_sdt_chu_re') }}</textarea>
                        </div>

                        <div class="col-12 mt-2">
                            <h6 class="text-muted text-uppercase fs-12 mb-0">Lịch &amp; địa điểm</h6>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_ngay_chup_du_kien">Ngày chụp dự kiến</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_chup_du_kien" name="ngay_chup_du_kien" value="{{ old('ngay_chup_du_kien') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_ngay_chup_thuc_te">Ngày chụp thực tế</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_chup_thuc_te" name="ngay_chup_thuc_te" value="{{ old('ngay_chup_thuc_te') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_buoi_chup">Buổi chụp</label>
                            <select class="form-select" id="cuoi_buoi_chup" name="buoi_chup">
                                <option value="">—</option>
                                <option value="sang" @selected(old('buoi_chup') === 'sang')>Sáng</option>
                                <option value="chieu" @selected(old('buoi_chup') === 'chieu')>Chiều</option>
                                <option value="ca_ngay" @selected(old('buoi_chup') === 'ca_ngay')>Cả ngày</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_dia_diem_chup">Địa điểm chụp</label>
                            <textarea class="form-control" id="cuoi_dia_diem_chup" name="dia_diem_chup" rows="2" placeholder="Địa điểm">{{ old('dia_diem_chup') }}</textarea>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_ngay_cuoi_du_kien">Ngày cưới dự kiến</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_cuoi_du_kien" name="ngay_cuoi_du_kien" value="{{ old('ngay_cuoi_du_kien') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_ngay_cuoi_chinh_thuc">Ngày cưới chính thức</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_cuoi_chinh_thuc" name="ngay_cuoi_chinh_thuc" value="{{ old('ngay_cuoi_chinh_thuc') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label" for="cuoi_concept">Concept</label>
                            <select class="select2-admin form-select" id="cuoi_concept" name="concept_id" data-placeholder="Chọn concept">
                                <option value="">—</option>
                                @foreach($danhSachConcept ?? [] as $c)
                                <option value="{{ $c->id }}"
                                        data-avatar="{{ !empty($c->hinh_anh) ? asset('storage/' . $c->hinh_anh) : '' }}"
                                        @selected((string) old('concept_id') === (string) ($c->id ?? ''))>
                                    {{ $c->ten_concept }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_kenh_tiep_can">Kênh tiếp cận</label>
                            <input type="text" class="form-control" id="cuoi_kenh_tiep_can" name="kenh_tiep_can" value="{{ old('kenh_tiep_can') }}" maxlength="100">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_yeu_cau_dac_biet">Yêu cầu đặc biệt</label>
                            <textarea class="form-control" id="cuoi_yeu_cau_dac_biet" name="yeu_cau_dac_biet" rows="2">{{ old('yeu_cau_dac_biet') }}</textarea>
                        </div>

                        <div class="col-12 mt-2">
                            <h6 class="text-muted text-uppercase fs-12 mb-0">Tài chính</h6>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="cuoi_tong_tien">Tổng tiền</label>
                            <input type="number" class="form-control text-end" id="cuoi_tong_tien" name="tong_tien" value="{{ old('tong_tien', '0') }}" min="0" step="0.01">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="cuoi_chiet_khau">Chiết khấu</label>
                            <input type="number" class="form-control text-end" id="cuoi_chiet_khau" name="chiet_khau" value="{{ old('chiet_khau', '0') }}" min="0" step="0.01">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="cuoi_tien_coc">Tiền cọc</label>
                            <input type="number" class="form-control text-end" id="cuoi_tien_coc" name="tien_coc" value="{{ old('tien_coc', '0') }}" min="0" step="0.01">
                        </div>

                        <div class="col-12 mt-2">
                            <h6 class="text-muted text-uppercase fs-12 mb-0">Trạng thái &amp; bàn giao file</h6>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_trang_thai_hop_dong">Trạng thái hợp đồng</label>
                            <select class="form-select" id="cuoi_trang_thai_hop_dong" name="trang_thai_hop_dong">
                                @php $tt = old('trang_thai_hop_dong', 'nhap'); @endphp
                                <option value="nhap" @selected($tt === 'nhap')>Nháp</option>
                                <option value="da_huy" @selected($tt === 'da_huy')>Đã huỷ</option>
                                <option value="dang_thuc_hien" @selected($tt === 'dang_thuc_hien')>Đang thực hiện</option>
                                <option value="tre_chup" @selected($tt === 'tre_chup')>Trễ chụp</option>
                                <option value="tre_edit" @selected($tt === 'tre_edit')>Trễ edit</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_ghi_chu_sale">Ghi chú sale</label>
                            <textarea class="form-control" id="cuoi_ghi_chu_sale" name="ghi_chu_sale" rows="2">{{ old('ghi_chu_sale') }}</textarea>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="cuoi_link_demo">Link demo</label>
                            <input type="text" class="form-control" id="cuoi_link_demo" name="link_demo" value="{{ old('link_demo') }}" maxlength="500" placeholder="URL hoặc ghi chú link">
                        </div>
                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="cuoi_ngay_tra_link_demo_du_kien">Ngày trả link demo (dự kiến)</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_tra_link_demo_du_kien" name="ngay_tra_link_demo_du_kien" value="{{ old('ngay_tra_link_demo_du_kien') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="cuoi_ngay_up_link_demo_gan_nhat">Lần up link demo gần nhất</label>
                            <input type="text" class="flatpickr-datetime-admin form-control" id="cuoi_ngay_up_link_demo_gan_nhat" name="ngay_up_link_demo_gan_nhat" value="{{ old('ngay_up_link_demo_gan_nhat') }}" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_nguoi_up_link_demo">Người up link demo</label>
                            <select class="form-select" id="cuoi_nguoi_up_link_demo" name="nguoi_up_link_demo_id">
                                <option value="">—</option>
                                @foreach($danhSachNhanVien ?? [] as $nv)
                                <option value="{{ $nv->id }}" @selected((string) old('nguoi_up_link_demo_id') === (string) $nv->id)>{{ $nv->user?->name ?? ('Nhân viên #'.$nv->id) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="cuoi_link_in">Link in</label>
                            <input type="text" class="form-control" id="cuoi_link_in" name="link_in" value="{{ old('link_in') }}" maxlength="500" placeholder="URL hoặc ghi chú link">
                        </div>
                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="cuoi_ngay_tra_link_in_chinh_thuc">Ngày trả link in (chính thức)</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_tra_link_in_chinh_thuc" name="ngay_tra_link_in_chinh_thuc" value="{{ old('ngay_tra_link_in_chinh_thuc') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="cuoi_ngay_up_link_in_gan_nhat">Lần up link in gần nhất</label>
                            <input type="text" class="flatpickr-datetime-admin form-control" id="cuoi_ngay_up_link_in_gan_nhat" name="ngay_up_link_in_gan_nhat" value="{{ old('ngay_up_link_in_gan_nhat') }}" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="cuoi_nguoi_up_link_in">Người up link in</label>
                            <select class="form-select" id="cuoi_nguoi_up_link_in" name="nguoi_up_link_in_id">
                                <option value="">—</option>
                                @foreach($danhSachNhanVien ?? [] as $nv)
                                <option value="{{ $nv->id }}" @selected((string) old('nguoi_up_link_in_id') === (string) $nv->id)>{{ $nv->user?->name ?? ('Nhân viên #'.$nv->id) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-2">
                            <h6 class="text-muted text-uppercase fs-12 mb-0">Ký &amp; hạn thanh toán</h6>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="cuoi_ngay_ky_hop_dong">Ngày ký hợp đồng</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_ngay_ky_hop_dong" name="ngay_ky_hop_dong" value="{{ old('ngay_ky_hop_dong') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="cuoi_han_thanh_toan_lan2">Hạn thanh toán lần 2</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_han_thanh_toan_lan2" name="han_thanh_toan_lan2" value="{{ old('han_thanh_toan_lan2') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="cuoi_han_thanh_toan_lan3">Hạn thanh toán lần 3</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="cuoi_han_thanh_toan_lan3" name="han_thanh_toan_lan3" value="{{ old('han_thanh_toan_lan3') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">Lưu hợp đồng cưới</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Chỉnh sửa hợp đồng (cấu trúc giống Thêm mới, khách hàng disabled) --}}
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
                                <input type="hidden" id="sua_hop_dong_id" value="">
                @if($errors->any())
                <div class="modal-body py-0">
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Hàng 1: Khách hàng (disabled) + Địa điểm + Ngày chụp + Ngày hẹn trả demo --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="sua_khach_hang_id">Khách hàng <span class="text-danger">*</span></label>
                            <input type="hidden" name="khach_hang_id" id="sua_khach_hang_id_hidden">
                            <select class="select2-admin form-select" id="sua_khach_hang_id" disabled aria-label="Khách hàng (không thể thay đổi)" data-placeholder="Chọn khách hàng">
                                <option value="">-- Chọn khách hàng --</option>
                                @foreach($danhSachKhachHang ?? [] as $kh)
                                <option value="{{ $kh->id }}">{{ $kh->ho_ten_chu_re ?? '' }} / {{ $kh->ho_ten_co_dau ?? '' }} ({{ $kh->email_hoac_sdt_chu_re ?? $kh->email_hoac_sdt_co_dau ?? '—' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="sua_dia_diem">Địa điểm (Dự kiến)</label>
                            <input type="text" class="form-control" id="sua_dia_diem" name="dia_diem" placeholder="Địa điểm chụp">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="sua_ngay_chup">Ngày chụp (Dự kiến)</label>
                            <input type="text" class="flatpickr-datetime-admin form-control" id="sua_ngay_chup" name="ngay_chup" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label" for="sua_ngay_hen_tra_hang">Ngày hẹn trả demo (Dự kiến)</label>
                            <input type="text" class="flatpickr-date-admin form-control" id="sua_ngay_hen_tra_hang" name="ngay_hen_tra_hang" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        {{-- Tabs: Nhóm dịch vụ & Dịch vụ lẻ (Sua) --}}
                        <div class="col-12 mt-2">
                            <label class="form-label d-block">Tham khảo dịch vụ</label>
                            <div class="nav-align-top">
                                <ul class="nav nav-pills mb-4 nav-fill" id="tabDichVuSuaHopDong" role="tablist">
                                    <li class="nav-item mb-1 mb-sm-0" role="presentation">
                                        <button type="button" class="nav-link active" id="tab-nhom-dich-vu-sua-btn" role="tab" data-bs-toggle="tab" data-bs-target="#tab-nhom-dich-vu-sua" aria-controls="tab-nhom-dich-vu-sua" aria-selected="true">Nhóm dịch vụ</button>
                                    </li>
                                    <li class="nav-item mb-1 mb-sm-0" role="presentation">
                                        <button type="button" class="nav-link" id="tab-dich-vu-le-sua-btn" role="tab" data-bs-toggle="tab" data-bs-target="#tab-dich-vu-le-sua" aria-controls="tab-dich-vu-le-sua" aria-selected="false">Dịch vụ lẻ</button>
                                    </li>
                                </ul>
                                <div class="tab-content p-3 bg-light rounded" id="tabDichVuSuaHopDongContent">
                                <div class="tab-pane fade show active" id="tab-nhom-dich-vu-sua" role="tabpanel">
                                    <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                        <table class="table table-sm table-hover table-bordered mb-0" id="tableNhomDichVuSua">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center"></th>
                                                    <th class="text-center" style="width: 50px;">STT</th>
                                                    <th>Tên nhóm</th>
                                                    <th>Mã</th>
                                                    <th class="text-end">Giá tiền</th>
                                                    <th class="text-end">Giá gốc</th>
                                                    <th>Thẻ</th>
                                                    <th>Ghi chú</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($danhSachNhomDichVu ?? [] as $idx => $ndv)
                                                @php
                                                    $dvlList = $ndv->dichVuLe->map(fn($d) => [
                                                        'id' => $d->id,
                                                        'ten_dich_vu' => $d->ten_dich_vu ?? '—',
                                                        'ma_dich_vu' => $d->ma_dich_vu ?? '—',
                                                        'gia_goc' => $d->gia_dich_vu !== null ? (float)$d->gia_dich_vu : 0,
                                                        'gia_dich_vu' => $d->gia_dich_vu !== null ? number_format((float)$d->gia_dich_vu, 0, ',', '.') . ' đ' : '—',
                                                        'ghi_chu' => $d->ghi_chu ? \Illuminate\Support\Str::limit($d->ghi_chu, 40) : '—',
                                                        'so_luong' => $d->pivot->so_luong ?? 1,
                                                    ])->values()->toArray();
                                                @endphp
                                                <tr data-nhom-id="{{ $ndv->id }}"
                                                    data-ten-nhom="{{ e($ndv->ten_nhom ?? '') }}"
                                                    data-dich-vu-le='{{ json_encode($dvlList, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}'>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input cb-nhom-dich-vu-sua" value="{{ $ndv->id }}" aria-label="Chọn nhóm {{ $ndv->ten_nhom }}">
                                                    </td>
                                                    <td class="text-center">{{ $idx + 1 }}</td>
                                                    <td>{{ $ndv->ten_nhom ?? '—' }}</td>
                                                    <td>{{ $ndv->ma_nhom ?? '—' }}</td>
                                                    <td class="text-end">{{ $ndv->gia_tien !== null ? number_format((float)$ndv->gia_tien, 0, ',', '.') . ' đ' : '—' }}</td>
                                                    <td class="text-end">{{ $ndv->gia_goc !== null ? number_format((float)$ndv->gia_goc, 0, ',', '.') . ' đ' : '—' }}</td>
                                                    <td>{{ $ndv->the ?? '—' }}</td>
                                                    <td>{{ $ndv->ghi_chu ? str($ndv->ghi_chu)->limit(40) : '—' }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="8" class="text-center text-muted py-3">Chưa có nhóm dịch vụ.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-dich-vu-le-sua" role="tabpanel">
                                    <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                        <table class="table table-sm table-hover table-bordered mb-0" id="tableDichVuLeSua">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center"></th>
                                                    <th class="text-center" style="width: 50px;">STT</th>
                                                    <th>Tên dịch vụ</th>
                                                    <th>Mã</th>
                                                    <th class="text-end">Giá dịch vụ</th>
                                                    <th>Ghi chú</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($danhSachDichVuLe ?? [] as $idx => $dvl)
                                                @php
                                                    $dvlItem = [
                                                        'id' => $dvl->id,
                                                        'ten_dich_vu' => $dvl->ten_dich_vu ?? '—',
                                                        'ma_dich_vu' => $dvl->ma_dich_vu ?? '—',
                                                        'gia_goc' => $dvl->gia_dich_vu !== null ? (float)$dvl->gia_dich_vu : 0,
                                                        'gia_dich_vu' => $dvl->gia_dich_vu !== null ? number_format((float)$dvl->gia_dich_vu, 0, ',', '.') . ' đ' : '—',
                                                        'ghi_chu' => $dvl->ghi_chu ? \Illuminate\Support\Str::limit($dvl->ghi_chu, 40) : '—',
                                                        'so_luong' => 1,
                                                    ];
                                                @endphp
                                                <tr data-dich-vu-le='{{ json_encode($dvlItem, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) }}'>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input cb-dich-vu-le-sua" value="{{ $dvl->id }}" aria-label="Chọn dịch vụ {{ $dvl->ten_dich_vu }}">
                                                    </td>
                                                    <td class="text-center">{{ $idx + 1 }}</td>
                                                    <td>{{ $dvl->ten_dich_vu ?? '—' }}</td>
                                                    <td>{{ $dvl->ma_dich_vu ?? '—' }}</td>
                                                    <td class="text-end">{{ $dvl->gia_dich_vu !== null ? number_format((float)$dvl->gia_dich_vu, 0, ',', '.') . ' đ' : '—' }}</td>
                                                    <td>{{ $dvl->ghi_chu ? str($dvl->ghi_chu)->limit(40) : '—' }}</td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="6" class="text-center text-muted py-3">Chưa có dịch vụ lẻ.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        {{-- Bảng: Dịch vụ lẻ của nhóm đã chọn (Sua) --}}
                        <div class="col-12 d-none" id="boxDichVuLeTheoNhomSua">
                            <label class="form-label fw-medium">Dịch vụ lẻ của nhóm đã chọn</label>
                            <div class="table-responsive border rounded" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">Chọn</th>
                                            <th>Nhóm</th>
                                            <th>Tên</th>
                                            <th>Mã</th>
                                            <th class="text-end">Giá gốc</th>
                                            <th class="text-end">Giá thực</th>
                                            <th style="width: 110px;">Số lượng</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDichVuLeTheoNhomSua"></tbody>
                                    <tfoot id="tfootDichVuLeTheoNhomSua" class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end fw-medium">Tổng</td>
                                            <td class="text-end fw-medium" id="tdTongGiaGocSua">0 đ</td>
                                            <td class="text-end fw-medium" id="tdTongGiaThucSua">0 đ</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_trang_phuc">Trang phục</label>
                            <select class="select2-admin form-select"
                                    id="sua_trang_phuc"
                                    name="trang_phuc[]"
                                    multiple
                                    data-placeholder="Chọn trang phục">
                                @foreach($danhSachTrangPhuc ?? [] as $sp)
                                    @php
                                        $sdDates = $trangPhucSuDungTuHomNay[$sp->id] ?? [];
                                        $sdLabel = !empty($sdDates) ? '  -  (SD: ' . implode(', ', $sdDates) . ')' : '';
                                    @endphp
                                    <option value="{{ $sp->id }}"
                                            data-avatar="{{ !empty($sp->hinh_anh) ? asset('storage/' . $sp->hinh_anh) : '' }}">
                                        {{ $sp->ten_san_pham }}{{ $sdLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_concept">Concept</label>
                            <select class="select2-admin form-select" id="sua_concept" name="concept" data-placeholder="Chọn concept">
                                <option value="">-- Chọn concept --</option>
                                @foreach($danhSachConcept ?? [] as $c)
                                <option value="{{ $c->id }}"
                                        data-avatar="{{ !empty($c->hinh_anh) ? asset('storage/' . $c->hinh_anh) : '' }}">
                                    {{ $c->ten_concept }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_ghi_chu_chup">Ghi chú chụp</label>
                            <textarea class="form-control" id="sua_ghi_chu_chup" name="ghi_chu_chup" rows="2" placeholder="Ghi chú"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="row g-2 row-cols-1 row-cols-sm-2 row-cols-lg-5 hop-dong-tien-row align-items-end">
                                <div class="col">
                                    <label class="form-label mb-1" for="sua_tong_tien_display">Tổng tiền</label>
                                    <input type="hidden" name="tong_tien" id="sua_tong_tien" value="">
                                    <input type="text" class="form-control hop-dong-tien-control bg-body-secondary text-end fw-semibold fs-5" id="sua_tong_tien_display" readonly tabindex="-1" value="0 đ" aria-readonly="true" autocomplete="off">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="sua_nguoi_gioi_thieu">Mã giới thiệu (mã HĐ)</label>
                                    <div class="input-group hop-dong-tien-control">
                                        <input type="text" class="form-control  hop-dong-tien-control text-end bg-body-secondary" id="sua_nguoi_gioi_thieu" name="nguoi_gioi_thieu" maxlength="255" placeholder="Nhập mã giới thiệu" autocomplete="off">
                                        <button type="button" class="btn btn-outline-primary" id="btnKiemTraMaGioiThieuSua" title="Kiểm tra mã giới thiệu">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="sua_so_tien_giam_gia">Số tiền giảm giá</label>
                                    <input type="number" class="form-control hop-dong-tien-control text-end bg-body-secondary" id="sua_so_tien_giam_gia" name="so_tien_giam_gia" min="0" step="0.01" inputmode="decimal" placeholder="0" readonly tabindex="-1" aria-readonly="true" title="Áp dụng tự động sau khi kiểm tra mã giới thiệu">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="sua_thanh_toan_lan_1">Tiền cọc</label>
                                    <input type="number" class="form-control hop-dong-tien-control" id="sua_thanh_toan_lan_1" name="thanh_toan_lan_1" min="0" step="0.01">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1" for="sua_con_lai">Còn lại</label>
                                    <input type="text" class="form-control hop-dong-tien-control bg-body-secondary" id="sua_con_lai" readonly tabindex="-1" value="0 đ" aria-readonly="true" autocomplete="off">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">Tự tính theo dịch vụ được tích lưu trong bảng.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>
 </div>

{{-- Modal xem ảnh thanh toán --}}
<div class="modal fade" id="modalXemAnhThanhToan" tabindex="-1" aria-labelledby="modalXemAnhThanhToanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXemAnhThanhToanLabel">Ảnh thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imgXemAnhThanhToan"
                     src=""
                     alt="Ảnh thanh toán"
                     class="img-fluid rounded shadow-sm">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal thêm ảnh thanh toán --}}
<div class="modal fade" id="modalAnhThanhToan" tabindex="-1" aria-labelledby="modalAnhThanhToanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAnhThanhToanLabel">Thêm ảnh thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formAnhThanhToan"
                  method="POST"
                  action="{{ route('admin.khach-hang.hop-dong.upload-anh-thanh-toan') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="hop_dong_id" id="anh_thanh_toan_hop_dong_id">
                    <div class="mb-3">
                        <label class="form-label d-block mb-2">Ảnh thanh toán</label>
                        <div id="previewAnhThanhToanWrapper"
                             class="border rounded d-flex align-items-center justify-content-center bg-light"
                             style="width: 100%; max-width: 420px; height: 260px; margin: 0 auto 1rem auto;">
                            <img id="previewAnhThanhToan"
                                 src=""
                                 alt="Preview ảnh thanh toán"
                                 class="img-thumbnail"
                                 style="max-height: 240px; max-width: 100%; object-fit: contain; display: none;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lan_thanh_toan" class="form-label">Lần thanh toán</label>
                        <select name="lan_thanh_toan"
                                id="lan_thanh_toan"
                                class="form-select select2-admin"
                                data-placeholder="Chọn lần thanh toán"
                                onclick="handleLanThanhToanClick()"
                                onchange="handleLanThanhToanClick()">
                            <option value="1">Lần 1</option>
                            <option value="2">Lần 2</option>
                            <option value="3">Lần 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="so_tien_thanh_toan" class="form-label">Số tiền thanh toán</label>
                        <input type="number"
                               name="so_tien_thanh_toan"
                               id="so_tien_thanh_toan"
                               class="form-control"
                               min="0"
                               step="0.01"
                               inputmode="decimal"
                               placeholder="Nhập số tiền thanh toán...">
                    </div>
                    <div class="mb-3">
                        <label for="anh_thanh_toan" class="form-label">Chọn file ảnh</label>
                        <input type="file"
                               name="anh_thanh_toan"
                               id="anh_thanh_toan"
                               class="form-control"
                               accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-1"></i> Lưu ảnh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa hợp đồng --}}
<div class="modal fade" id="modalXacNhanXoaHopDong" tabindex="-1" aria-labelledby="modalXacNhanXoaHopDongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaHopDongLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Bạn có chắc muốn xóa hợp đồng này?</p>
                <p class="text-warning mb-0 mt-2 small">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Toàn bộ bản ghi dịch vụ trong hợp đồng (bảng dịch vụ trong hợp đồng) cũng sẽ bị xóa theo. Thao tác không thể hoàn tác.
                </p>
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

{{-- Toast thông báo --}}
<div class="toast-container position-fixed top-0 end-0 p-3 hop-dong-toast-container">
    <div id="toastHopDong"
         class="toast align-items-center text-bg-success border-0"
         role="alert"
         aria-live="assertive"
         aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastHopDongBody">—</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Đóng"></button>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
<style>
#modalThemHopDong .modal-dialog.modal-hop-dong,
#modalSuaHopDong .modal-dialog.modal-hop-dong,
#modalHopDongCuoi .modal-dialog.modal-hop-dong {
    max-width: 95vw;
    width: 1200px;
    max-height: calc(100vh - 2rem);
}
@media (min-width: 992px) {
    #modalThemHopDong .modal-dialog.modal-hop-dong,
    #modalSuaHopDong .modal-dialog.modal-hop-dong,
    #modalHopDongCuoi .modal-dialog.modal-hop-dong {
        max-width: 1200px;
    }
}
/* Modal thêm/sửa hợp đồng: chỉ phần nội dung form cuộn, footer luôn hiển thị */
#modalThemHopDong .modal-content,
#modalSuaHopDong .modal-content,
#modalHopDongCuoi .modal-content {
    max-height: calc(100vh - 2rem);
    display: flex;
    flex-direction: column;
}
#modalThemHopDong .modal-content .modal-header,
#modalSuaHopDong .modal-content .modal-header,
#modalHopDongCuoi .modal-content .modal-header,
#modalThemHopDong .modal-content .modal-footer,
#modalSuaHopDong .modal-content .modal-footer,
#modalHopDongCuoi .modal-content .modal-footer {
    flex-shrink: 0;
}
#modalThemHopDong .modal-content form,
#modalSuaHopDong .modal-content form,
#modalHopDongCuoi .modal-content form {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
}
#modalThemHopDong .modal-content form .modal-body,
#modalSuaHopDong .modal-content form .modal-body,
#modalHopDongCuoi .modal-content form .modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
}
#modalThemHopDong .modal-content form .modal-body.py-0,
#modalSuaHopDong .modal-content form .modal-body.py-0,
#modalHopDongCuoi .modal-content form .modal-body.py-0 {
    flex: 0 0 auto;
}
#modalXacNhanXoaHopDong .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
.table-wrapper-bordered {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 0.375rem;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 1200px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
/* Modal Thêm HĐ: bảng dịch vụ rộng tối thiểu; tràn ngang màn hình → thanh cuộn ngang */
#modalThemHopDong .table-responsive.hop-dong-dich-vu-wide {
    max-height: 220px;
    overflow-x: auto;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
#modalThemHopDong .table-responsive.hop-dong-dich-vu-wide.hop-dong-dich-vu-wide--compact {
    max-height: 200px;
}
#modalThemHopDong .table-responsive.hop-dong-dich-vu-wide > .table {
    min-width: 1100px;
    width: max-content;
    max-width: none;
}
/* Hàng tổng tiền / mã GT / giảm giá / cọc / còn lại: cùng chiều cao control */
#modalThemHopDong .hop-dong-tien-row,
#modalSuaHopDong .hop-dong-tien-row {
    --hop-dong-tien-ctrl-h: calc(1.5em + 0.75rem + 2px);
}
#modalThemHopDong .hop-dong-tien-row .hop-dong-tien-control.form-control,
#modalSuaHopDong .hop-dong-tien-row .hop-dong-tien-control.form-control {
    min-height: var(--hop-dong-tien-ctrl-h);
}
#modalThemHopDong .hop-dong-tien-row .input-group.hop-dong-tien-control,
#modalSuaHopDong .hop-dong-tien-row .input-group.hop-dong-tien-control {
    min-height: var(--hop-dong-tien-ctrl-h);
    align-items: stretch;
}
#modalThemHopDong .hop-dong-tien-row .input-group.hop-dong-tien-control > .form-control,
#modalSuaHopDong .hop-dong-tien-row .input-group.hop-dong-tien-control > .form-control {
    min-height: 100%;
    align-self: stretch;
}
#modalThemHopDong .hop-dong-tien-row .input-group.hop-dong-tien-control > .btn,
#modalSuaHopDong .hop-dong-tien-row .input-group.hop-dong-tien-control > .btn {
    display: inline-flex;
    align-items: center;
    align-self: stretch;
}
.hop-dong-toast-container {
    z-index: 2500;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || @json(csrf_token());
    var URL_KIEM_TRA_MA_GIOI_THIEU = @json(route('admin.khach-hang.kiem-tra-ma-gioi-thieu'));
    var MIN_ORDER_GIOI_THIEU = 300000;

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // Toast helper (Bootstrap)
    var toastEl = document.getElementById('toastHopDong');
    var toastBodyEl = document.getElementById('toastHopDongBody');
    var toastInstance = (toastEl && window.bootstrap && bootstrap.Toast) ? bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2500 }) : null;
    function showToast(message, variant) {
        if (!toastEl || !toastBodyEl || !toastInstance) {
            alert(message);
            return;
        }
        variant = variant || 'success';
        toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning', 'text-bg-info', 'text-bg-secondary');
        toastEl.classList.add('text-bg-' + variant);
        toastBodyEl.textContent = message || '';
        toastInstance.show();
    }

    // --- Select2 (Concept) render thumbnail kèm ảnh ---
    function renderConceptWithAvatar(option) {
        if (!option.id) return option.text;

        var text = option.text || '';
        var avatar = '';
        try {
            avatar = (option.element && option.element.dataset && option.element.dataset.avatar) ? option.element.dataset.avatar : '';
        } catch (e) {}

        var avatarHtml = avatar
            ? "<img src=\"" + avatar + "\" alt=\"avatar\" class=\"rounded-circle\" style=\"width: 32px; height: 32px; object-fit: cover;\" onerror=\"this.style.visibility='hidden'\" />"
            : "<span class=\"rounded-circle bg-label-secondary d-inline-flex align-items-center justify-content-center\" style=\"width: 32px; height: 32px;\">—</span>";

        return "<div class='d-flex flex-wrap align-items-center'>" +
            "<div class='me-2'>" + avatarHtml + "</div>" +
            "<div>" + text + "</div>" +
            "</div>";
    }

    function initConceptSelect2(selectId) {
        var el = document.getElementById(selectId);
        if (!el) return;

        var jq = window.jQuery || window.$;
        if (!jq || !jq.fn || !jq.fn.select2) return;

        // destroy rồi init lại để gắn template render ảnh (đã init sẵn bởi scripts blade)
        if (jq(el).data('select2')) jq(el).select2('destroy');

        var $modal = jq(el).closest('.modal');
        var placeholder = el.getAttribute('data-placeholder') || 'Chọn...';

        jq(el).select2({
            placeholder: placeholder,
            allowClear: true,
            width: '100%',
            dropdownParent: $modal && $modal.length ? $modal : undefined,
            templateResult: renderConceptWithAvatar,
            templateSelection: renderConceptWithAvatar,
            escapeMarkup: function (es) { return es; }
        });
    }

    initConceptSelect2('them_concept');
    initConceptSelect2('sua_concept');
    initConceptSelect2('cuoi_concept');
    initConceptSelect2('them_trang_phuc');
    initConceptSelect2('sua_trang_phuc');

    @if($errors->any() && old('_form') !== 'hop_dong_cuoi')
    var modalThem = document.getElementById('modalThemHopDong');
    if (modalThem) {
        var m = new bootstrap.Modal(modalThem);
        m.show();
    }
    @endif

    @if($errors->any() && old('_form') === 'hop_dong_cuoi')
    var modalCuoi = document.getElementById('modalHopDongCuoi');
    if (modalCuoi) {
        var mCuoi = new bootstrap.Modal(modalCuoi);
        mCuoi.show();
    }
    @endif

    // Modal Sửa: gán data vào form (cấu trúc giống Thêm mới, khách hàng disabled)
    var modalSua = document.getElementById('modalSuaHopDong');
    var formSua = document.getElementById('formSuaHopDong');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-hop-dong')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            var hdId = btn.getAttribute('data-hop-dong-id') || '';
            var hdInp = document.getElementById('sua_hop_dong_id');
            if (hdInp) hdInp.value = hdId;
            var khachHangId = btn.getAttribute('data-khach-hang-id') || '';
            document.getElementById('sua_khach_hang_id').value = khachHangId;
            document.getElementById('sua_khach_hang_id_hidden').value = khachHangId;
            document.getElementById('sua_dia_diem').value = btn.getAttribute('data-dia-diem') || '';
            if (window.setAdminDateTimeInput && window.setAdminDateInput) { setAdminDateTimeInput('sua_ngay_chup', btn.getAttribute('data-ngay-chup') || ''); setAdminDateInput('sua_ngay_hen_tra_hang', btn.getAttribute('data-ngay-hen-tra-hang') || ''); } else { document.getElementById('sua_ngay_chup').value = btn.getAttribute('data-ngay-chup') || ''; document.getElementById('sua_ngay_hen_tra_hang').value = btn.getAttribute('data-ngay-hen-tra-hang') || ''; }
            // trang_phuc là select (select2) nhiều lựa chọn, nên cần set value + trigger change
            var suaTrangPhucSelect = document.getElementById('sua_trang_phuc');
            if (suaTrangPhucSelect) {
                var rawTrangPhuc = btn.getAttribute('data-trang-phuc') || '';
                var trangPhucIds = [];
                if (rawTrangPhuc) {
                    trangPhucIds = rawTrangPhuc.split(',').map(function (s) { return (s || '').trim(); }).filter(function (x) { return x; });
                }
                var jq = window.jQuery || window.$;
                if (jq && jq.fn && jq.fn.select2) jq(suaTrangPhucSelect).val(trangPhucIds).trigger('change');
                else {
                    Array.from(suaTrangPhucSelect.options).forEach(function (opt) {
                        opt.selected = trangPhucIds.indexOf(String(opt.value)) !== -1;
                    });
                    suaTrangPhucSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
            // concept là select (select2), nên cần set value + trigger change
            var suaConceptSelect = document.getElementById('sua_concept');
            if (suaConceptSelect) {
                var suaConceptVal = btn.getAttribute('data-concept') || '';
                var suaConceptLabel = btn.getAttribute('data-concept-label') || suaConceptVal;
                // Trường hợp concept hiện tại không có trong danh sách option (dữ liệu cũ), tạo option tạm.
                if (suaConceptVal && !Array.from(suaConceptSelect.options).some(function(opt) { return String(opt.value) === String(suaConceptVal); })) {
                    var opt = document.createElement('option');
                    opt.value = suaConceptVal;
                    opt.textContent = suaConceptLabel;
                    suaConceptSelect.appendChild(opt);
                }
                suaConceptSelect.value = suaConceptVal;
                var jq = window.jQuery || window.$;
                if (jq && jq.fn && jq.fn.select2) jq(suaConceptSelect).val(suaConceptVal).trigger('change');
                else suaConceptSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
            document.getElementById('sua_ghi_chu_chup').value = btn.getAttribute('data-ghi-chu-chup') || '';
            var inpNguoiGioiThieu = document.getElementById('sua_nguoi_gioi_thieu');
            if (inpNguoiGioiThieu) inpNguoiGioiThieu.value = btn.getAttribute('data-nguoi-gioi-thieu') || '';
            var inpSuaGiam = document.getElementById('sua_so_tien_giam_gia');
            if (inpSuaGiam) {
                var rawGiam = btn.getAttribute('data-so-tien-giam-gia');
                inpSuaGiam.value = (rawGiam !== null && rawGiam !== '') ? rawGiam : '0';
            }
            window._suaHopDongTongFallback = btn.getAttribute('data-tong-tien') || '0';
            var inpSuaLan1 = document.getElementById('sua_thanh_toan_lan_1');
            if (inpSuaLan1) {
                inpSuaLan1.value = btn.getAttribute('data-thanh-toan-lan-1') || '0';
                var khoaLan1 = btn.getAttribute('data-khoa-thanh-toan-lan-1') === '1';
                inpSuaLan1.disabled = khoaLan1;
                inpSuaLan1.classList.toggle('bg-body-secondary', khoaLan1);
                inpSuaLan1.removeAttribute('name');
                if (khoaLan1) {
                    var h = document.createElement('input');
                    h.type = 'hidden';
                    h.name = 'thanh_toan_lan_1';
                    h.id = 'sua_thanh_toan_lan_1_hidden';
                    h.value = inpSuaLan1.value;
                    var oldH = document.getElementById('sua_thanh_toan_lan_1_hidden');
                    if (oldH) oldH.remove();
                    inpSuaLan1.parentNode.insertBefore(h, inpSuaLan1.nextSibling);
                } else {
                    var oldH2 = document.getElementById('sua_thanh_toan_lan_1_hidden');
                    if (oldH2) oldH2.remove();
                    inpSuaLan1.setAttribute('name', 'thanh_toan_lan_1');
                }
            }
            var tableNhomSua = document.getElementById('tableNhomDichVuSua');
            var tableDichVuLeSua = document.getElementById('tableDichVuLeSua');
            var boxDichVuLeSua = document.getElementById('boxDichVuLeTheoNhomSua');
            var tbodyDichVuLeSua = document.getElementById('tbodyDichVuLeTheoNhomSua');
            if (tableNhomSua) tableNhomSua.querySelectorAll('.cb-nhom-dich-vu-sua').forEach(function(cb) { cb.checked = false; });
            if (tableDichVuLeSua) tableDichVuLeSua.querySelectorAll('.cb-dich-vu-le-sua').forEach(function(cb) { cb.checked = false; });
            if (boxDichVuLeSua) boxDichVuLeSua.classList.add('d-none');
            if (tbodyDichVuLeSua) tbodyDichVuLeSua.innerHTML = '';
            syncSuaTongTienVaConLai();

            // Load dịch vụ đã lưu của hợp đồng từ dich_vu_trong_hop_dong và đổ vào bảng (Sua)
            var dichVuUrl = btn.getAttribute('data-dich-vu-url');
            if (dichVuUrl && tbodyDichVuLeSua && boxDichVuLeSua) {
                fetch(dichVuUrl, { headers: { 'Accept': 'application/json' } })
                    .then(function(res) { return res.json(); })
                    .then(function(json) {
                        var data = (json && json.data) ? json.data : [];
                        tbodyDichVuLeSua.innerHTML = '';
                        if (!Array.isArray(data) || data.length === 0) {
                            boxDichVuLeSua.classList.add('d-none');
                            syncSuaTongTienVaConLai();
                            return;
                        }
                        boxDichVuLeSua.classList.remove('d-none');
                        data.forEach(function(row, idx) {
                            var stt = idx + 1;
                            var soLuong = row.so_luong != null ? Number(row.so_luong) : 1;
                            if (isNaN(soLuong) || soLuong < 0) soLuong = 0;

                            var giaGocTotal = row.gia_goc != null ? Number(row.gia_goc) : 0;
                            var thanhTienTotal = row.thanh_tien != null
                                ? Number(row.thanh_tien)
                                : (row.gia_thuc != null ? Number(row.gia_thuc) : giaGocTotal);

                            // UI đang nhập giá "theo đơn vị" (sẽ nhân với so_luong khi tính thành tiền).
                            var giaGocUnit = soLuong > 0 ? (giaGocTotal / soLuong) : giaGocTotal;
                            var giaThucUnit = soLuong > 0 ? (thanhTienTotal / soLuong) : thanhTienTotal;
                            var tr = document.createElement('tr');
                            tr.setAttribute('data-dich-vu-le-id', row.id_dich_vu);
                            tr.setAttribute('data-gia-goc', String(giaGocUnit));
                            tr.setAttribute('data-tu-nhom', '0');
                            tr.innerHTML =
                                '<td class="text-center"><input type="checkbox" class="form-check-input cb-dich-vu-le-hop-dong" checked value="' + escapeHtml(String(row.id_dich_vu)) + '" aria-label="Chọn lưu dịch vụ"></td>' +
                                '<td>' + escapeHtml('—') + '</td>' +
                                '<td>' + escapeHtml(row.ten_dich_vu) + '</td>' +
                                '<td>' + escapeHtml(row.ma_dich_vu) + '</td>' +
                                '<td class="text-end">' + escapeHtml(formatMoney(giaGocUnit) + ' đ') + '</td>' +
                                '<td class="text-end"><input type="number" class="form-control form-control-sm input-gia-thuc" min="0" step="10" value="' + escapeHtml(String(giaThucUnit)) + '" style="width: 100px; display: inline-block;" placeholder="Tròn chục" inputmode="numeric"></td>' +
                                '<td class="text-end"><input type="number" class="form-control form-control-sm input-so-luong" min="0" step="1" value="' + escapeHtml(String(soLuong)) + '" style="width: 95px; display: inline-block;" inputmode="numeric"></td>' +
                                '<td class="text-end"><span class="thanh-tien-display">' + escapeHtml(formatMoney(thanhTienTotal)) + ' đ</span></td>';
                            tbodyDichVuLeSua.appendChild(tr);
                        });
                        updateTongDichVuLeTheoNhomSua();
                    })
                    .catch(function() {
                        boxDichVuLeSua.classList.add('d-none');
                        tbodyDichVuLeSua.innerHTML = '';
                        syncSuaTongTienVaConLai();
                    });
            }
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

    // Tab Nhóm dịch vụ + Tab Dịch vụ lẻ: gộp vào bảng "Dịch vụ lẻ của nhóm đã chọn"
    var tableNhom = document.getElementById('tableNhomDichVuThem');
    var tableDichVuLe = document.getElementById('tableDichVuLeThem');
    var boxDichVuLe = document.getElementById('boxDichVuLeTheoNhom');
    var tbodyDichVuLe = document.getElementById('tbodyDichVuLeTheoNhom');
    var inpTimDichVuThem = document.getElementById('them_tim_dich_vu');
    var btnXoaLocThemDichVu = document.getElementById('btnXoaLocThemDichVu');

    function stripDiacritics(value) {
        // Giúp search không bị phụ thuộc dấu tiếng Việt.
        return String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function normalizeSearch(value) {
        return stripDiacritics(value).toLowerCase().trim();
    }

    function applayTimKiemDichVuThem() {
        var q = inpTimDichVuThem ? normalizeSearch(inpTimDichVuThem.value) : '';

        if (tableNhom) {
            tableNhom.querySelectorAll('tbody tr[data-nhom-id]').forEach(function (row) {
                if (!q) {
                    row.style.display = '';
                    return;
                }

                var tenNhom = normalizeSearch(row.getAttribute('data-ten-nhom') || '');
                var maNhom = normalizeSearch((row.children[3] && row.children[3].innerText) ? row.children[3].innerText : '');

                var matched = tenNhom.includes(q) || maNhom.includes(q);

                // Nếu chưa khớp theo tên/mã nhóm thì thử khớp theo tên/mã dịch vụ lẻ thuộc nhóm.
                if (!matched) {
                    // Cache danh sách dịch vụ lẻ thuộc nhóm để tránh parse JSON mỗi lần gõ.
                    var list = row.__dichVuLeList;
                    if (!Array.isArray(list)) {
                        var json = row.getAttribute('data-dich-vu-le');
                        list = [];
                        try { list = json ? JSON.parse(json) : []; } catch (e) { list = []; }
                        row.__dichVuLeList = list;
                    }
                    if (Array.isArray(list)) {
                        matched = list.some(function (d) {
                            var tenDv = normalizeSearch(d && d.ten_dich_vu ? d.ten_dich_vu : '');
                            var maDv = normalizeSearch(d && d.ma_dich_vu ? d.ma_dich_vu : '');
                            return tenDv.includes(q) || maDv.includes(q);
                        });
                    }
                }

                row.style.display = matched ? '' : 'none';
            });
        }

        if (tableDichVuLe) {
            tableDichVuLe.querySelectorAll('tbody tr[data-dich-vu-le]').forEach(function (row) {
                if (!q) {
                    row.style.display = '';
                    return;
                }

                // Cache item dịch vụ lẻ để tránh parse JSON mỗi lần gõ.
                var dvl = row.__dichVuLeItem;
                if (!dvl) {
                    var json = row.getAttribute('data-dich-vu-le');
                    try { dvl = json ? JSON.parse(json) : null; } catch (e) { dvl = null; }
                    row.__dichVuLeItem = dvl;
                }

                var ten = dvl && dvl.ten_dich_vu ? dvl.ten_dich_vu : (row.children[2] && row.children[2].innerText ? row.children[2].innerText : '');
                var ma = dvl && dvl.ma_dich_vu ? dvl.ma_dich_vu : (row.children[3] && row.children[3].innerText ? row.children[3].innerText : '');

                var matched = normalizeSearch(ten).includes(q) || normalizeSearch(ma).includes(q);
                row.style.display = matched ? '' : 'none';
            });
        }
    }

    function renderDichVuLeTheoNhom() {
        if (!tbodyDichVuLe || !boxDichVuLe) return;
        var checkedNhomRows = [];
        if (tableNhom) {
            tableNhom.querySelectorAll('tbody tr[data-nhom-id]').forEach(function(row) {
                if (row.querySelector('.cb-nhom-dich-vu') && row.querySelector('.cb-nhom-dich-vu').checked) checkedNhomRows.push(row);
            });
        }
        var checkedDichVuLeRows = [];
        if (tableDichVuLe) {
            tableDichVuLe.querySelectorAll('tbody tr[data-dich-vu-le]').forEach(function(row) {
                if (row.querySelector('.cb-dich-vu-le') && row.querySelector('.cb-dich-vu-le').checked) checkedDichVuLeRows.push(row);
            });
        }
        var allItems = [];
        checkedNhomRows.forEach(function(row) {
            var tenNhom = row.getAttribute('data-ten-nhom') || '—';
            var json = row.getAttribute('data-dich-vu-le');
            var list = [];
            try { list = json ? JSON.parse(json) : []; } catch (e) {}
            list.forEach(function(dvl) {
                allItems.push({ tenNhom: tenNhom, dvl: dvl });
            });
        });
        checkedDichVuLeRows.forEach(function(row) {
            var json = row.getAttribute('data-dich-vu-le');
            var dvl = null;
            try { dvl = json ? JSON.parse(json) : null; } catch (e) {}
            if (dvl) allItems.push({ tenNhom: '—', dvl: dvl });
        });
        var seenMa = {};
        var uniqueItems = [];
        allItems.forEach(function(item) {
            var ma = (item.dvl.ma_dich_vu != null && item.dvl.ma_dich_vu !== '') ? String(item.dvl.ma_dich_vu).trim() : null;
            var key = ma !== null ? ma : '__empty_' + (item.dvl.id != null ? item.dvl.id : uniqueItems.length);
            if (Object.prototype.hasOwnProperty.call(seenMa, key)) {
                // Nếu bị trùng dịch vụ (do chọn thêm dịch vụ lẻ + từ nhóm), cộng dồn số lượng.
                var existing = uniqueItems[seenMa[key]];
                var soLuong1 = existing.dvl.so_luong != null ? Number(existing.dvl.so_luong) : 0;
                var soLuong2 = item.dvl.so_luong != null ? Number(item.dvl.so_luong) : 0;
                if (isNaN(soLuong1) || soLuong1 < 0) soLuong1 = 0;
                if (isNaN(soLuong2) || soLuong2 < 0) soLuong2 = 0;
                existing.dvl.so_luong = soLuong1 + soLuong2;
                return;
            }
            seenMa[key] = uniqueItems.length;
            uniqueItems.push(item);
        });
        tbodyDichVuLe.innerHTML = '';
        if (uniqueItems.length === 0) {
            if (checkedNhomRows.length > 0) {
                boxDichVuLe.classList.remove('d-none');
                var tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="8" class="text-muted">Nhóm đã chọn chưa có dịch vụ lẻ.</td>';
                tbodyDichVuLe.appendChild(tr);
                updateTongDichVuLeTheoNhom();
            } else {
                boxDichVuLe.classList.add('d-none');
                syncThemTongTienVaConLai();
            }
            return;
        }
        boxDichVuLe.classList.remove('d-none');
        uniqueItems.forEach(function(item, idx) {
            var tenNhom = item.tenNhom;
            var dvl = item.dvl;
            var stt = idx + 1;
            var giaGoc = dvl.gia_goc != null ? Number(dvl.gia_goc) : 0;
            var giaThucUnitInit = dvl.gia_thuc != null ? Number(dvl.gia_thuc) : Math.round(giaGoc / 10) * 10;
            if (isNaN(giaThucUnitInit) || giaThucUnitInit < 0) giaThucUnitInit = 0;
            var giaThucTronChuc = Math.round(giaThucUnitInit / 10) * 10;
            var giaGocDisplay = dvl.gia_dich_vu != null && dvl.gia_dich_vu !== '' ? dvl.gia_dich_vu : '—';
            var soLuong = dvl.so_luong != null ? Number(dvl.so_luong) : 1;
            if (isNaN(soLuong) || soLuong < 0) soLuong = 0;
            var thanhTienInit = giaThucTronChuc * soLuong;
            var tr = document.createElement('tr');
            tr.setAttribute('data-dich-vu-le-id', dvl.id);
            tr.setAttribute('data-gia-goc', String(giaGoc));
            tr.setAttribute('data-tu-nhom', tenNhom !== '—' ? '1' : '0');
            tr.innerHTML =
                '<td class="text-center"><input type="checkbox" class="form-check-input cb-dich-vu-le-hop-dong" checked value="' + escapeHtml(String(dvl.id)) + '" aria-label="Chọn lưu dịch vụ"></td>' +
                '<td>' + escapeHtml(tenNhom) + '</td>' +
                '<td>' + escapeHtml(dvl.ten_dich_vu) + '</td>' +
                '<td>' + escapeHtml(dvl.ma_dich_vu) + '</td>' +
                '<td class="text-end">' + escapeHtml(giaGocDisplay) + '</td>' +
                '<td class="text-end"><input type="number" class="form-control form-control-sm input-gia-thuc" min="0" step="10" value="' + escapeHtml(String(giaThucTronChuc)) + '" style="width: 100px; display: inline-block;" placeholder="Tròn chục" inputmode="numeric"></td>' +
                '<td class="text-end"><input type="number" class="form-control form-control-sm input-so-luong" min="0" step="1" value="' + escapeHtml(String(soLuong)) + '" style="width: 95px; display: inline-block;" inputmode="numeric"></td>' +
                '<td class="text-end"><span class="thanh-tien-display">' + escapeHtml(formatMoney(thanhTienInit)) + ' đ</span></td>';
            tbodyDichVuLe.appendChild(tr);
        });
        updateTongDichVuLeTheoNhom();
    }

    function escapeHtml(s) {
        if (s == null || s === '') return '—';
        var div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    function formatMoney(n) {
        var num = parseFloat(n);
        if (isNaN(num)) return '0';
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateTongDichVuLeTheoNhom() {
        var tfoot = document.getElementById('tfootDichVuLeTheoNhom');
        var tdTongGiaGoc = document.getElementById('tdTongGiaGoc');
        var tdTongGiaThuc = document.getElementById('tdTongGiaThuc');
        if (!tbodyDichVuLe || !tdTongGiaGoc || !tdTongGiaThuc) return;
        var rows = tbodyDichVuLe.querySelectorAll('tr[data-dich-vu-le-id]');
        var tongGoc = 0, tongThuc = 0;
        rows.forEach(function(tr) {
            var cb = tr.querySelector('.cb-dich-vu-le-hop-dong');
            if (cb && !cb.checked) return;
            var unitGiaGoc = parseFloat(tr.getAttribute('data-gia-goc')) || 0;
            var inpGia = tr.querySelector('.input-gia-thuc');
            var inpSoLuong = tr.querySelector('.input-so-luong');
            var unitGiaThuc = inpGia ? (parseFloat(inpGia.value) || 0) : 0;
            var soLuong = inpSoLuong ? (parseFloat(inpSoLuong.value) || 0) : 0;
            var thanhTien = unitGiaThuc * soLuong;

            // Update thành tiền hiển thị theo từng dòng
            var elThanhTien = tr.querySelector('.thanh-tien-display');
            if (elThanhTien) elThanhTien.textContent = formatMoney(thanhTien) + ' đ';

            tongGoc += unitGiaGoc * soLuong;
            tongThuc += thanhTien;
        });
        tdTongGiaGoc.textContent = formatMoney(tongGoc) + ' đ';
        tdTongGiaThuc.textContent = formatMoney(tongThuc) + ' đ';
        syncThemTongTienVaConLai();
    }

    function syncThemTongTienVaConLai() {
        var inpTongHidden = document.getElementById('them_tong_tien');
        var inpTongDisplay = document.getElementById('them_tong_tien_display');
        var inpLan1 = document.getElementById('them_thanh_toan_lan_1');
        var inpGiam = document.getElementById('them_so_tien_giam_gia');
        var inpConLai = document.getElementById('them_con_lai');
        if (!inpTongHidden || !inpConLai) return;
        var tong = 0;
        if (tbodyDichVuLe) {
            tbodyDichVuLe.querySelectorAll('tr[data-dich-vu-le-id]').forEach(function(tr) {
                var cb = tr.querySelector('.cb-dich-vu-le-hop-dong');
                if (!cb || !cb.checked) return;
                var inpGia = tr.querySelector('.input-gia-thuc');
                var inpSoLuong = tr.querySelector('.input-so-luong');
                var unitGiaThuc = inpGia ? (parseFloat(inpGia.value) || 0) : 0;
                var soLuong = inpSoLuong ? (parseFloat(inpSoLuong.value) || 0) : 0;
                tong += unitGiaThuc * soLuong;
            });
        }
        var tongRounded = Math.round(tong * 100) / 100;
        inpTongHidden.value = String(tongRounded);
        if (inpTongDisplay) inpTongDisplay.value = formatMoney(tongRounded) + ' đ';
        if (inpGiam) {
            var gv0 = parseFloat(inpGiam.value) || 0;
            if (tongRounded < MIN_ORDER_GIOI_THIEU && gv0 > 0) {
                inpGiam.value = '0';
            }
        }
        var giam = inpGiam ? (parseFloat(inpGiam.value) || 0) : 0;
        if (giam < 0) giam = 0;
        if (giam > tongRounded) giam = tongRounded;
        var sauGiam = Math.max(0, tongRounded - giam);
        var lan1 = inpLan1 ? (parseFloat(inpLan1.value) || 0) : 0;
        var conLai = Math.max(0, sauGiam - lan1);
        inpConLai.value = formatMoney(conLai) + ' đ';
    }

    // Chỉ cho phép chọn 1 nhóm: khi chọn nhóm khác thì bỏ chọn nhóm hiện tại
    if (tableNhom) {
        tableNhom.addEventListener('change', function(e) {
            if (!e.target.classList.contains('cb-nhom-dich-vu')) return;
            if (e.target.checked) {
                tableNhom.querySelectorAll('.cb-nhom-dich-vu').forEach(function(cb) {
                    if (cb !== e.target) cb.checked = false;
                });
            }
            renderDichVuLeTheoNhom();
        });
    }

    // Tab Dịch vụ lẻ: tích chọn → thêm vào bảng "Dịch vụ lẻ của nhóm đã chọn" (có thể chọn nhiều)
    if (tableDichVuLe) {
        tableDichVuLe.addEventListener('change', function(e) {
            if (e.target.classList.contains('cb-dich-vu-le')) renderDichVuLeTheoNhom();
        });
    }

    // Bộ lọc theo tên hoặc mã cho tab Thêm: áp dụng trực tiếp lên 2 bảng.
    if (inpTimDichVuThem) {
        inpTimDichVuThem.addEventListener('input', applayTimKiemDichVuThem);
        if (btnXoaLocThemDichVu) {
            btnXoaLocThemDichVu.addEventListener('click', function () {
                inpTimDichVuThem.value = '';
                applayTimKiemDichVuThem();
            });
        }
        applayTimKiemDichVuThem();
    }

    // Giá thực: chỉ nhập số tròn chục, không thập phân (10, 240, 2450...). Khi blur làm tròn về bội 10.
    function roundToTen(n) {
        var num = parseFloat(n);
        if (isNaN(num) || num < 0) return 0;
        return Math.round(num / 10) * 10;
    }
    if (boxDichVuLe) {
        boxDichVuLe.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-gia-thuc')) {
                var inp = e.target;
                if (inp.value.indexOf('.') !== -1) {
                    inp.value = roundToTen(inp.value);
                }
                updateTongDichVuLeTheoNhom();
                return;
            }
            if (e.target.classList.contains('input-so-luong')) {
                updateTongDichVuLeTheoNhom();
            }
        });
        boxDichVuLe.addEventListener('change', function(e) {
            if (e.target.classList.contains('input-gia-thuc')) {
                var inp = e.target;
                inp.value = roundToTen(inp.value);
                updateTongDichVuLeTheoNhom();
            }
            if (e.target.classList.contains('input-so-luong')) {
                updateTongDichVuLeTheoNhom();
            }
            if (e.target.classList.contains('cb-dich-vu-le-hop-dong')) {
                updateTongDichVuLeTheoNhom();
            }
        });
    }

    var inpThemThanhToanLan1 = document.getElementById('them_thanh_toan_lan_1');
    if (inpThemThanhToanLan1) {
        inpThemThanhToanLan1.addEventListener('input', syncThemTongTienVaConLai);
        inpThemThanhToanLan1.addEventListener('change', syncThemTongTienVaConLai);
    }

    // Reset khi mở lại modal Thêm mới (bỏ chọn và ẩn box)
    var modalThemHopDong = document.getElementById('modalThemHopDong');
    if (modalThemHopDong) {
        modalThemHopDong.addEventListener('show.bs.modal', function() {
            if (tableNhom) tableNhom.querySelectorAll('.cb-nhom-dich-vu').forEach(function(cb) { cb.checked = false; });
            if (tableDichVuLe) tableDichVuLe.querySelectorAll('.cb-dich-vu-le').forEach(function(cb) { cb.checked = false; });
            if (boxDichVuLe) { boxDichVuLe.classList.add('d-none'); }
            if (tbodyDichVuLe) tbodyDichVuLe.innerHTML = '';
            var themConceptSelect = document.getElementById('them_concept');
            if (themConceptSelect) {
                var themConceptOldValue = @json((string) old('concept', ''));
                // Nếu old concept là tên (dữ liệu cũ), tạo option tạm để Select2 hiển thị đúng.
                if (themConceptOldValue && !Array.from(themConceptSelect.options).some(function(opt) { return String(opt.value) === String(themConceptOldValue); })) {
                    var opt = document.createElement('option');
                    opt.value = themConceptOldValue;
                    opt.textContent = themConceptOldValue;
                    themConceptSelect.appendChild(opt);
                }
                themConceptSelect.value = themConceptOldValue || '';
                var jq = window.jQuery || window.$;
                if (jq && jq.fn && jq.fn.select2) jq(themConceptSelect).val(themConceptOldValue || '').trigger('change');
                else themConceptSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
            var themTrangPhucSelect = document.getElementById('them_trang_phuc');
            if (themTrangPhucSelect) {
                var themTrangPhucOldIds = @json(old('trang_phuc', []));
                var idsArr = Array.isArray(themTrangPhucOldIds) ? themTrangPhucOldIds : [];
                var jq2 = window.jQuery || window.$;
                if (jq2 && jq2.fn && jq2.fn.select2) jq2(themTrangPhucSelect).val(idsArr).trigger('change');
                else {
                    Array.from(themTrangPhucSelect.options).forEach(function (opt) {
                        opt.selected = idsArr.indexOf(String(opt.value)) !== -1;
                    });
                    themTrangPhucSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
            var tTong = document.getElementById('them_tong_tien');
            var tTongDisp = document.getElementById('them_tong_tien_display');
            var tLan1 = document.getElementById('them_thanh_toan_lan_1');
            var tGiam = document.getElementById('them_so_tien_giam_gia');
            var tConLai = document.getElementById('them_con_lai');
            if (tTong) tTong.value = '0';
            if (tTongDisp) tTongDisp.value = '0 đ';
            if (tLan1) tLan1.value = '0';
            if (tGiam) tGiam.value = '0';
            if (tConLai) tConLai.value = '0 đ';
        });
    }

    // Submit form Thêm HĐ: chỉ gửi dịch vụ lẻ được tích chọn (kèm giá gốc, giá thực)
    var formThemHopDong = document.getElementById('formThemHopDong');
    if (formThemHopDong && tbodyDichVuLe) {
        formThemHopDong.addEventListener('submit', function() {
            syncThemTongTienVaConLai();
            document.querySelectorAll('#formThemHopDong input[name^="dich_vu_le_hop_dong"]').forEach(function(el) { el.remove(); });
            var index = 0;
            tbodyDichVuLe.querySelectorAll('tr[data-dich-vu-le-id]').forEach(function(tr) {
                var cb = tr.querySelector('.cb-dich-vu-le-hop-dong');
                if (!cb || !cb.checked) return;
                var dichVuLeId = tr.getAttribute('data-dich-vu-le-id');
                var unitGiaGoc = parseFloat(tr.getAttribute('data-gia-goc')) || 0;
                var inputGiaThuc = tr.querySelector('.input-gia-thuc');
                var unitGiaThuc = inputGiaThuc ? (parseFloat(inputGiaThuc.value) || 0) : unitGiaGoc;
                var inputSoLuong = tr.querySelector('.input-so-luong');
                var soLuong = inputSoLuong ? (parseFloat(inputSoLuong.value) || 0) : 0;
                var giaGoc = unitGiaGoc * soLuong;
                var giaThuc = unitGiaThuc * soLuong;
                var prefix = 'dich_vu_le_hop_dong[' + index + ']';
                [ { n: prefix + '[dich_vu_le_id]', v: dichVuLeId }, { n: prefix + '[gia_goc]', v: String(giaGoc) }, { n: prefix + '[gia_thuc]', v: String(giaThuc) }, { n: prefix + '[so_luong]', v: String(soLuong) }, { n: prefix + '[thanh_tien]', v: String(giaThuc) } ].forEach(function(o) {
                    var inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = o.n;
                    inp.value = o.v;
                    formThemHopDong.appendChild(inp);
                });
                index++;
            });
        });
    }

    // --- Modal Sửa: tab Nhóm dịch vụ + Dịch vụ lẻ → bảng "Dịch vụ lẻ của nhóm đã chọn" (logic giống Thêm) ---
    var tableNhomSua = document.getElementById('tableNhomDichVuSua');
    var tableDichVuLeSua = document.getElementById('tableDichVuLeSua');
    var boxDichVuLeSua = document.getElementById('boxDichVuLeTheoNhomSua');
    var tbodyDichVuLeSua = document.getElementById('tbodyDichVuLeTheoNhomSua');

    function renderDichVuLeTheoNhomSua() {
        if (!tbodyDichVuLeSua || !boxDichVuLeSua) return;
        var checkedNhomRows = [];
        if (tableNhomSua) {
            tableNhomSua.querySelectorAll('tbody tr[data-nhom-id]').forEach(function(row) {
                if (row.querySelector('.cb-nhom-dich-vu-sua') && row.querySelector('.cb-nhom-dich-vu-sua').checked) checkedNhomRows.push(row);
            });
        }
        var checkedDichVuLeRows = [];
        if (tableDichVuLeSua) {
            tableDichVuLeSua.querySelectorAll('tbody tr[data-dich-vu-le]').forEach(function(row) {
                if (row.querySelector('.cb-dich-vu-le-sua') && row.querySelector('.cb-dich-vu-le-sua').checked) checkedDichVuLeRows.push(row);
            });
        }
        var allItems = [];
        checkedNhomRows.forEach(function(row) {
            var tenNhom = row.getAttribute('data-ten-nhom') || '—';
            var json = row.getAttribute('data-dich-vu-le');
            var list = [];
            try { list = json ? JSON.parse(json) : []; } catch (e) {}
            list.forEach(function(dvl) {
                allItems.push({ tenNhom: tenNhom, dvl: dvl });
            });
        });
        checkedDichVuLeRows.forEach(function(row) {
            var json = row.getAttribute('data-dich-vu-le');
            var dvl = null;
            try { dvl = json ? JSON.parse(json) : null; } catch (e) {}
            if (dvl) allItems.push({ tenNhom: '—', dvl: dvl });
        });
        var seenMa = {};
        var uniqueItems = [];
        allItems.forEach(function(item) {
            var ma = (item.dvl.ma_dich_vu != null && item.dvl.ma_dich_vu !== '') ? String(item.dvl.ma_dich_vu).trim() : null;
            var key = ma !== null ? ma : '__empty_' + (item.dvl.id != null ? item.dvl.id : uniqueItems.length);
            if (Object.prototype.hasOwnProperty.call(seenMa, key)) {
                // Nếu bị trùng dịch vụ (do chọn thêm dịch vụ lẻ + từ nhóm), cộng dồn số lượng.
                var existing = uniqueItems[seenMa[key]];
                var soLuong1 = existing.dvl.so_luong != null ? Number(existing.dvl.so_luong) : 0;
                var soLuong2 = item.dvl.so_luong != null ? Number(item.dvl.so_luong) : 0;
                if (isNaN(soLuong1) || soLuong1 < 0) soLuong1 = 0;
                if (isNaN(soLuong2) || soLuong2 < 0) soLuong2 = 0;
                existing.dvl.so_luong = soLuong1 + soLuong2;
                return;
            }
            seenMa[key] = uniqueItems.length;
            uniqueItems.push(item);
        });
        tbodyDichVuLeSua.innerHTML = '';
        if (uniqueItems.length === 0) {
            if (checkedNhomRows.length > 0) {
                boxDichVuLeSua.classList.remove('d-none');
                var tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="8" class="text-muted">Nhóm đã chọn chưa có dịch vụ lẻ.</td>';
                tbodyDichVuLeSua.appendChild(tr);
                updateTongDichVuLeTheoNhomSua();
            } else {
                boxDichVuLeSua.classList.add('d-none');
                syncSuaTongTienVaConLai();
            }
            return;
        }
        boxDichVuLeSua.classList.remove('d-none');
        uniqueItems.forEach(function(item, idx) {
            var tenNhom = item.tenNhom;
            var dvl = item.dvl;
            var stt = idx + 1;
            var giaGoc = dvl.gia_goc != null ? Number(dvl.gia_goc) : 0;
            var giaThucTronChuc = Math.round(giaGoc / 10) * 10;
            var giaGocDisplay = dvl.gia_dich_vu != null && dvl.gia_dich_vu !== '' ? dvl.gia_dich_vu : '—';
            var soLuong = dvl.so_luong != null ? Number(dvl.so_luong) : 1;
            if (isNaN(soLuong) || soLuong < 0) soLuong = 0;
            var thanhTienInit = giaThucTronChuc * soLuong;
            var tr = document.createElement('tr');
            tr.setAttribute('data-dich-vu-le-id', dvl.id);
            tr.setAttribute('data-gia-goc', String(giaGoc));
            tr.setAttribute('data-tu-nhom', tenNhom !== '—' ? '1' : '0');
            tr.innerHTML =
                '<td class="text-center"><input type="checkbox" class="form-check-input cb-dich-vu-le-hop-dong" checked value="' + escapeHtml(String(dvl.id)) + '" aria-label="Chọn lưu dịch vụ"></td>' +
                '<td>' + escapeHtml(tenNhom) + '</td>' +
                '<td>' + escapeHtml(dvl.ten_dich_vu) + '</td>' +
                '<td>' + escapeHtml(dvl.ma_dich_vu) + '</td>' +
                '<td class="text-end">' + escapeHtml(giaGocDisplay) + '</td>' +
                '<td class="text-end"><input type="number" class="form-control form-control-sm input-gia-thuc" min="0" step="10" value="' + escapeHtml(String(giaThucTronChuc)) + '" style="width: 100px; display: inline-block;" placeholder="Tròn chục" inputmode="numeric"></td>' +
                '<td class="text-end"><input type="number" class="form-control form-control-sm input-so-luong" min="0" step="1" value="' + escapeHtml(String(soLuong)) + '" style="width: 95px; display: inline-block;" inputmode="numeric"></td>' +
                '<td class="text-end"><span class="thanh-tien-display">' + escapeHtml(formatMoney(thanhTienInit)) + ' đ</span></td>';
            tbodyDichVuLeSua.appendChild(tr);
        });
        updateTongDichVuLeTheoNhomSua();
    }

    function syncSuaTongTienVaConLai() {
        var hidden = document.getElementById('sua_tong_tien');
        var display = document.getElementById('sua_tong_tien_display');
        var lan1El = document.getElementById('sua_thanh_toan_lan_1');
        var giamEl = document.getElementById('sua_so_tien_giam_gia');
        var conLaiEl = document.getElementById('sua_con_lai');
        if (!hidden || !conLaiEl) return;
        var rowEls = tbodyDichVuLeSua ? tbodyDichVuLeSua.querySelectorAll('tr[data-dich-vu-le-id]') : [];
        var tong = 0;
        if (rowEls.length === 0) {
            tong = parseFloat(window._suaHopDongTongFallback || '0') || 0;
        } else {
            rowEls.forEach(function(tr) {
                var cb = tr.querySelector('.cb-dich-vu-le-hop-dong');
                if (!cb || !cb.checked) return;
                var inpGia = tr.querySelector('.input-gia-thuc');
                var inpSoLuong = tr.querySelector('.input-so-luong');
                var unitGiaThuc = inpGia ? (parseFloat(inpGia.value) || 0) : 0;
                var soLuong = inpSoLuong ? (parseFloat(inpSoLuong.value) || 0) : 0;
                tong += unitGiaThuc * soLuong;
            });
        }
        var tongRounded = Math.round(tong * 100) / 100;
        hidden.value = String(tongRounded);
        if (display) display.value = formatMoney(tongRounded) + ' đ';
        if (giamEl) {
            var gv0s = parseFloat(giamEl.value) || 0;
            if (tongRounded < MIN_ORDER_GIOI_THIEU && gv0s > 0) {
                giamEl.value = '0';
            }
        }
        var giam = giamEl ? (parseFloat(giamEl.value) || 0) : 0;
        if (giam < 0) giam = 0;
        if (giam > tongRounded) giam = tongRounded;
        var sauGiam = Math.max(0, tongRounded - giam);
        var l1 = lan1El ? (parseFloat(lan1El.value) || 0) : 0;
        conLaiEl.value = formatMoney(Math.max(0, sauGiam - l1)) + ' đ';
    }

    function updateTongDichVuLeTheoNhomSua() {
        var tdTongGiaGocSua = document.getElementById('tdTongGiaGocSua');
        var tdTongGiaThucSua = document.getElementById('tdTongGiaThucSua');
        if (!tbodyDichVuLeSua || !tdTongGiaGocSua || !tdTongGiaThucSua) return;
        var rows = tbodyDichVuLeSua.querySelectorAll('tr[data-dich-vu-le-id]');
        var tongGoc = 0, tongThuc = 0;
        rows.forEach(function(tr) {
            var cb = tr.querySelector('.cb-dich-vu-le-hop-dong');
            if (cb && !cb.checked) return;
            var unitGiaGoc = parseFloat(tr.getAttribute('data-gia-goc')) || 0;
            var inpGia = tr.querySelector('.input-gia-thuc');
            var inpSoLuong = tr.querySelector('.input-so-luong');
            var unitGiaThuc = inpGia ? (parseFloat(inpGia.value) || 0) : 0;
            var soLuong = inpSoLuong ? (parseFloat(inpSoLuong.value) || 0) : 0;
            var thanhTien = unitGiaThuc * soLuong;

            var elThanhTien = tr.querySelector('.thanh-tien-display');
            if (elThanhTien) elThanhTien.textContent = formatMoney(thanhTien) + ' đ';

            tongGoc += unitGiaGoc * soLuong;
            tongThuc += thanhTien;
        });
        tdTongGiaGocSua.textContent = formatMoney(tongGoc) + ' đ';
        tdTongGiaThucSua.textContent = formatMoney(tongThuc) + ' đ';
        syncSuaTongTienVaConLai();
    }

    if (tableNhomSua) {
        tableNhomSua.addEventListener('change', function(e) {
            if (!e.target.classList.contains('cb-nhom-dich-vu-sua')) return;
            if (e.target.checked) {
                tableNhomSua.querySelectorAll('.cb-nhom-dich-vu-sua').forEach(function(cb) {
                    if (cb !== e.target) cb.checked = false;
                });
            }
            renderDichVuLeTheoNhomSua();
        });
    }
    if (tableDichVuLeSua) {
        tableDichVuLeSua.addEventListener('change', function(e) {
            if (e.target.classList.contains('cb-dich-vu-le-sua')) renderDichVuLeTheoNhomSua();
        });
    }
    if (boxDichVuLeSua) {
        boxDichVuLeSua.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-gia-thuc')) {
                var inp = e.target;
                if (inp.value.indexOf('.') !== -1) inp.value = roundToTen(inp.value);
                updateTongDichVuLeTheoNhomSua();
                return;
            }
            if (e.target.classList.contains('input-so-luong')) {
                updateTongDichVuLeTheoNhomSua();
            }
        });
        boxDichVuLeSua.addEventListener('change', function(e) {
            if (e.target.classList.contains('input-gia-thuc')) {
                e.target.value = roundToTen(e.target.value);
                updateTongDichVuLeTheoNhomSua();
            }
            if (e.target.classList.contains('input-so-luong')) {
                updateTongDichVuLeTheoNhomSua();
            }
            if (e.target.classList.contains('cb-dich-vu-le-hop-dong')) {
                updateTongDichVuLeTheoNhomSua();
            }
        });
    }

    var inpSuaThanhToanLan1 = document.getElementById('sua_thanh_toan_lan_1');
    if (inpSuaThanhToanLan1) {
        inpSuaThanhToanLan1.addEventListener('input', syncSuaTongTienVaConLai);
        inpSuaThanhToanLan1.addEventListener('change', syncSuaTongTienVaConLai);
    }

    function setMaGioiThieuFeedback(prefix, message, variant) {
        if (!message) return;
        var v = (variant === 'success') ? 'success' : (variant === 'danger') ? 'danger' : (variant === 'warning') ? 'warning' : (variant === 'info') ? 'info' : 'info';
        showToast(message, v);
    }

    function goiKiemTraMaGioiThieu(prefix) {
        var inpMa = document.getElementById(prefix + '_nguoi_gioi_thieu');
        var inpGiam = document.getElementById(prefix + '_so_tien_giam_gia');
        var inpTong = document.getElementById(prefix + '_tong_tien');
        if (prefix === 'them') syncThemTongTienVaConLai();
        else syncSuaTongTienVaConLai();
        var tong = inpTong ? (parseFloat(inpTong.value) || 0) : 0;
        var fd = new FormData();
        fd.append('_token', CSRF_TOKEN);
        fd.append('nguoi_gioi_thieu', inpMa ? inpMa.value.trim() : '');
        fd.append('tong_tien', String(tong));
            if (prefix === 'sua') {
                var hopDongIdEl = document.getElementById('sua_hop_dong_id');
                var hopDongId = hopDongIdEl ? (hopDongIdEl.value || '').trim() : '';
                if (hopDongId) fd.append('hop_dong_id', hopDongId);
            }
        fetch(URL_KIEM_TRA_MA_GIOI_THIEU, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
            credentials: 'same-origin'
        })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function(payload) {
                var data = payload.data || {};
                if (!payload.ok) {
                    var msg = (data && data.message) ? data.message : (payload.status === 422 ? 'Dữ liệu không hợp lệ.' : 'Không kiểm tra được mã.');
                    setMaGioiThieuFeedback(prefix, msg, 'danger');
                    if (inpGiam) inpGiam.value = '0';
                    if (prefix === 'them') syncThemTongTienVaConLai();
                    else syncSuaTongTienVaConLai();
                    return;
                }
                var soGiam = data.so_tien_giam_gia != null ? Number(data.so_tien_giam_gia) : 0;
                if (inpGiam) inpGiam.value = isNaN(soGiam) ? '0' : String(soGiam);
                setMaGioiThieuFeedback(prefix, data.message || '', data.matched && soGiam > 0 ? 'success' : (data.matched ? 'info' : 'danger'));
                if (prefix === 'them') syncThemTongTienVaConLai();
                else syncSuaTongTienVaConLai();
            })
            .catch(function() {
                setMaGioiThieuFeedback(prefix, 'Lỗi kết nối, thử lại sau.', 'danger');
                if (inpGiam) inpGiam.value = '0';
                if (prefix === 'them') syncThemTongTienVaConLai();
                else syncSuaTongTienVaConLai();
            });
    }

    var btnKiemTraThem = document.getElementById('btnKiemTraMaGioiThieuThem');
    if (btnKiemTraThem) btnKiemTraThem.addEventListener('click', function() { goiKiemTraMaGioiThieu('them'); });
    var btnKiemTraSua = document.getElementById('btnKiemTraMaGioiThieuSua');
    if (btnKiemTraSua) btnKiemTraSua.addEventListener('click', function() { goiKiemTraMaGioiThieu('sua'); });

    var inpMaGtThem = document.getElementById('them_nguoi_gioi_thieu');
    if (inpMaGtThem) {
        inpMaGtThem.addEventListener('input', function() {
            var g = document.getElementById('them_so_tien_giam_gia');
            if (g) g.value = '0';
            syncThemTongTienVaConLai();
        });
    }
    var inpMaGtSua = document.getElementById('sua_nguoi_gioi_thieu');
    if (inpMaGtSua) {
        inpMaGtSua.addEventListener('input', function() {
            var g = document.getElementById('sua_so_tien_giam_gia');
            if (g) g.value = '0';
            syncSuaTongTienVaConLai();
        });
    }

    // Submit form Sửa HĐ: gửi dịch vụ lẻ được tích chọn (giống Thêm)
    if (formSua && tbodyDichVuLeSua) {
        formSua.addEventListener('submit', function() {
            syncSuaTongTienVaConLai();
            document.querySelectorAll('#formSuaHopDong input[name^="dich_vu_le_hop_dong"]').forEach(function(el) { el.remove(); });
            var index = 0;
            tbodyDichVuLeSua.querySelectorAll('tr[data-dich-vu-le-id]').forEach(function(tr) {
                var cb = tr.querySelector('.cb-dich-vu-le-hop-dong');
                if (!cb || !cb.checked) return;
                var dichVuLeId = tr.getAttribute('data-dich-vu-le-id');
                var unitGiaGoc = parseFloat(tr.getAttribute('data-gia-goc')) || 0;
                var inputGiaThuc = tr.querySelector('.input-gia-thuc');
                var unitGiaThuc = inputGiaThuc ? (parseFloat(inputGiaThuc.value) || 0) : unitGiaGoc;
                var inputSoLuong = tr.querySelector('.input-so-luong');
                var soLuong = inputSoLuong ? (parseFloat(inputSoLuong.value) || 0) : 0;
                var giaGoc = unitGiaGoc * soLuong;
                var giaThuc = unitGiaThuc * soLuong;
                var prefix = 'dich_vu_le_hop_dong[' + index + ']';
                [ { n: prefix + '[dich_vu_le_id]', v: dichVuLeId }, { n: prefix + '[gia_goc]', v: String(giaGoc) }, { n: prefix + '[gia_thuc]', v: String(giaThuc) }, { n: prefix + '[so_luong]', v: String(soLuong) }, { n: prefix + '[thanh_tien]', v: String(giaThuc) } ].forEach(function(o) {
                    var inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = o.n;
                    inp.value = o.v;
                    formSua.appendChild(inp);
                });
                index++;
            });
        });
    }

    // Modal Thêm ảnh thanh toán: select2 + preview ảnh upload
    var modalAnhThanhToan = document.getElementById('modalAnhThanhToan');
    var formAnhThanhToan = document.getElementById('formAnhThanhToan');
    var inputAnhThanhToan = document.getElementById('anh_thanh_toan');
    var inputHopDongIdAnh = document.getElementById('anh_thanh_toan_hop_dong_id');
    var inputSoTienThanhToan = document.getElementById('so_tien_thanh_toan');
    var selectLanThanhToan = document.getElementById('lan_thanh_toan');
    var previewWrapper = document.getElementById('previewAnhThanhToanWrapper');
    var previewImg = document.getElementById('previewAnhThanhToan');
    var modalXemAnhThanhToan = document.getElementById('modalXemAnhThanhToan');
    var imgXemAnhThanhToan = document.getElementById('imgXemAnhThanhToan');

    if (window.jQuery && jQuery.fn.select2) {
        var $lanThanhToan = jQuery('#lan_thanh_toan');
        if ($lanThanhToan.length) {
            $lanThanhToan.select2({
                dropdownParent: jQuery('#modalAnhThanhToan'),
                width: '100%',
                placeholder: $lanThanhToan.data('placeholder') || 'Chọn lần thanh toán',
                minimumResultsForSearch: Infinity
            });
        }
    }

    function getThanhToanMacDinh(btn, lan) {
        if (!btn) return '';
        var key = 'data-thanh-toan-lan-' + String(lan || '');
        return btn.getAttribute(key) || '';
    }

    function getAnhThanhToanMacDinh(btn, lan) {
        if (!btn) return '';
        var key = 'data-anh-thanh-toan-' + String(lan || '');
        return btn.getAttribute(key) || '';
    }

    function setPreviewAnhThanhToan(url) {
        if (!previewImg) return;
        console.log('[ANH_THANH_TOAN] setPreviewAnhThanhToan', { url: url });
        if (!url) {
            previewImg.src = '';
            previewImg.style.display = 'none';
            return;
        }
        previewImg.src = url;
        previewImg.style.display = 'block';
    }

    function fillTheoLanThanhToan(btn) {
        if (!selectLanThanhToan) return;
        var lan = selectLanThanhToan.value || '1';
        console.log('[ANH_THANH_TOAN] fillTheoLanThanhToan:start', {
            lan: lan,
            hasBtn: !!btn,
            btnClasses: btn ? btn.className : null,
            tienAttr: btn ? btn.getAttribute('data-thanh-toan-lan-' + String(lan)) : null,
            anhAttr: btn ? btn.getAttribute('data-anh-thanh-toan-' + String(lan)) : null
        });

        // Tiền: nếu chưa có thì gán 0
        if (inputSoTienThanhToan) {
            var macDinhTien = getThanhToanMacDinh(btn, lan);
            inputSoTienThanhToan.value = (macDinhTien !== null && macDinhTien !== undefined && String(macDinhTien).trim() !== '')
                ? String(macDinhTien)
                : '0';
            console.log('[ANH_THANH_TOAN] set so_tien_thanh_toan', {
                lan: lan,
                macDinhTien: macDinhTien,
                valueSet: inputSoTienThanhToan.value
            });
        } else {
            console.warn('[ANH_THANH_TOAN] missing #so_tien_thanh_toan input');
        }

        // Reset file input khi đổi lần để tránh nhầm preview ảnh mới
        if (inputAnhThanhToan) {
            inputAnhThanhToan.value = '';
        } else {
            console.warn('[ANH_THANH_TOAN] missing #anh_thanh_toan input');
        }

        // Ảnh: nếu đã có ảnh thanh toán lần n thì preview ảnh đó
        var macDinhAnh = getAnhThanhToanMacDinh(btn, lan);
        console.log('[ANH_THANH_TOAN] set preview from existing url', { lan: lan, macDinhAnh: macDinhAnh });
        setPreviewAnhThanhToan(macDinhAnh);

        console.log('[ANH_THANH_TOAN] fillTheoLanThanhToan:done', {
            lan: lan,
            previewVisible: previewImg ? previewImg.style.display : null,
            previewSrc: previewImg ? previewImg.src : null
        });
    }

    // Khi mở modal, gán id hợp đồng
    if (modalAnhThanhToan && formAnhThanhToan) {
        modalAnhThanhToan.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-them-anh-thanh-toan')) return;
            var hopDongId = btn.getAttribute('data-hop-dong-id') || '';
            if (inputHopDongIdAnh) {
                inputHopDongIdAnh.value = hopDongId;
            }

            // Lưu nút kích hoạt để dùng khi đổi "lần thanh toán"
            modalAnhThanhToan._triggerBtn = btn;

            // Khi mở modal, tự gán tiền mặc định theo lần đang chọn (mặc định là lần 1)
            console.log('[ANH_THANH_TOAN] modal show', {
                hopDongId: hopDongId,
                lanSelected: selectLanThanhToan ? selectLanThanhToan.value : null
            });
            fillTheoLanThanhToan(btn);
        });
    }

    window.handleLanThanhToanClick = function () {
        var modal = document.getElementById('modalAnhThanhToan');
        var sel = document.getElementById('lan_thanh_toan');
        var btn = modal ? (modal._triggerBtn || null) : null;
        console.log('[ANH_THANH_TOAN] handleLanThanhToanClick', {
            lanSelected: sel ? sel.value : null,
            hasBtn: !!btn
        });
        fillTheoLanThanhToan(btn);
    };

    if (!selectLanThanhToan) {
        console.warn('[ANH_THANH_TOAN] missing #lan_thanh_toan select');
    }

    if (inputAnhThanhToan && previewWrapper && previewImg) {
        inputAnhThanhToan.addEventListener('change', function() {
            var file = this.files && this.files[0] ? this.files[0] : null;
            if (!file || !file.type || !file.type.startsWith('image/')) {
                setPreviewAnhThanhToan('');
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                setPreviewAnhThanhToan(e.target.result || '');
            };
            reader.readAsDataURL(file);
        });
    }

    if (modalAnhThanhToan) {
        modalAnhThanhToan.addEventListener('hidden.bs.modal', function() {
            if (inputAnhThanhToan) {
                inputAnhThanhToan.value = '';
            }
            if (inputSoTienThanhToan) {
                inputSoTienThanhToan.value = '';
            }
            if (previewImg) {
                setPreviewAnhThanhToan('');
            }
            modalAnhThanhToan._triggerBtn = null;
            var $lanThanhToanReset = window.jQuery ? jQuery('#lan_thanh_toan') : null;
            if ($lanThanhToanReset && $lanThanhToanReset.length) {
                $lanThanhToanReset.val('1').trigger('change');
            } else {
                var selectLan = document.getElementById('lan_thanh_toan');
                if (selectLan) selectLan.value = '1';
            }
        });
    }

    // Form upload ảnh thanh toán dùng submit mặc định (không dùng AJAX)
});
</script>
@endpush
@endsection
