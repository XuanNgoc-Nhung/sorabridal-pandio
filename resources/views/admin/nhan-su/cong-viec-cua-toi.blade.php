@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<style>
    .btn-icon-only {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding-inline: 0.5rem;
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
        min-width: 1100px;
    }
    .table-wrapper-bordered .table th,
    .table-wrapper-bordered .table td {
        border: 1px solid var(--bs-border-color, #dee2e6);
        vertical-align: middle;
    }
    .cong-viec-cua-toi-table .cell-wrap {
        white-space: normal;
    }
    .cvct-work-card {
        height: 100%;
        border: 1px solid var(--bs-border-color, #dee2e6);
    }
    .cvct-work-card .meta-label {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
        color: var(--bs-secondary-color, #6c757d);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .cvct-work-card .meta-value {
        white-space: normal;
        word-break: break-word;
    }
    .cvct-work-card .info-box {
        height: 100%;
        padding: 0.75rem;
        border: 1px solid var(--bs-border-color-translucent, rgba(0, 0, 0, 0.08));
        border-radius: 0.5rem;
        background: var(--bs-tertiary-bg, #f8f9fa);
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column gap-3">
    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form action="{{ route('admin.nhan-su.cong-viec-cua-toi') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên khách hàng hoặc Email/SĐT</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập để tìm kiếm...">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1" aria-hidden="true"></i> Lọc
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.nhan-su.cong-viec-cua-toi') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> Bỏ lọc
                        </a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Công việc của tôi</span>
            <div id="cvct-view-toolbar" role="toolbar" aria-label="Chế độ xem danh sách công việc">
                <div class="btn-group btn-group-sm" role="group" aria-label="Bảng hoặc lưới">
                    <button type="button"
                            class="btn btn-primary active"
                            id="cvct-view-btn-table"
                            title="Xem dạng bảng"
                            aria-pressed="true">
                        <i class="bi bi-table" aria-hidden="true"></i>
                    </button>
                    <button type="button"
                            class="btn btn-outline-secondary"
                            id="cvct-view-btn-grid"
                            title="Xem dạng lưới"
                            aria-pressed="false">
                        <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </h5>
        <div class="card-body">
        @php
            $badgeClassByRole = [
                'Chụp' => 'bg-primary',
                'Make' => 'bg-warning',
                'Edit' => 'bg-success',
            ];
        @endphp

        <div id="cvct-view-table-wrap" class="table-responsive table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 cong-viec-cua-toi-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-nowrap" style="width: 50px;">STT</th>
                        <th>Khách hàng</th>
                        <th>Địa điểm</th>
                        <th class="text-nowrap">Ngày chụp</th>
                        <th class="text-nowrap" style="width: 130px;">Trạng thái chụp</th>
                        <th>Trang phục</th>
                        <th>Ghi chú</th>
                        <th class="text-nowrap" style="width: 150px;">Ngày hẹn trả demo</th>
                        <th class="text-nowrap">Trạng thái edit</th>
                        {{-- <th>Trạng thái HĐ</th> --}}
                        <th class="text-nowrap">Vai trò của tôi</th>
                        <th class="text-nowrap" style="width: 110px;">File chụp</th>
                        <th class="text-nowrap" style="width: 140px;">File hoàn thành</th>
                        <th class="text-nowrap" style="width: 160px;">Hành động</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                        @php
                            $buoiChupLabels = ['sang' => 'Sáng', 'chieu' => 'Chiều', 'ca_ngay' => 'Cả ngày'];

                            $tenKhachHang = collect([$item->ten_chu_re ?? '', $item->ten_co_dau ?? ''])
                                ->filter()
                                ->implode(' / ')
                                ?: '—';

                            $trangPhucNames = collect($item->hopDongCuoiTrangPhuc ?? [])
                                ->map(fn ($row) => $row->trangPhuc?->ten_san_pham)
                                ->filter()
                                ->values();

                            if ($isAdmin) {
                                $roles = [];
                                if ($item->tho_chup_id) {
                                    $roles[] = 'Chụp';
                                }
                                if ($item->tho_make_id) {
                                    $roles[] = 'Make';
                                }
                                if ($item->tho_edit_id) {
                                    $roles[] = 'Edit';
                                }
                            } else {
                                $roles = [];
                                if ((int) ($item->tho_chup_id ?? 0) === (int) $nhanVienId) {
                                    $roles[] = 'Chụp';
                                }
                                if ((int) ($item->tho_make_id ?? 0) === (int) $nhanVienId) {
                                    $roles[] = 'Make';
                                }
                                if ((int) ($item->tho_edit_id ?? 0) === (int) $nhanVienId) {
                                    $roles[] = 'Edit';
                                }
                            }

                            $homNay = now()->startOfDay();
                            $ngayChupNgay = $item->ngay_chup_thuc_te ?? $item->ngay_chup_du_kien;
                            $ngayChup = $ngayChupNgay ? $ngayChupNgay->copy()->startOfDay() : null;
                            $coFileDemo = ! empty($item->link_demo);

                            if ($coFileDemo) {
                                $tinhTrangChup = 'Đã chụp';
                                $tinhTrangChupClass = 'bg-success';
                            } elseif ($ngayChup && $ngayChup->greaterThan($homNay)) {
                                $tinhTrangChup = 'Đợi chụp';
                                $tinhTrangChupClass = 'bg-secondary';
                            } elseif ($ngayChup && $ngayChup->equalTo($homNay)) {
                                $tinhTrangChup = 'Cần chụp';
                                $tinhTrangChupClass = 'bg-warning';
                            } elseif ($ngayChup && $ngayChup->lessThan($homNay)) {
                                $tinhTrangChup = 'Trễ chụp';
                                $tinhTrangChupClass = 'bg-danger';
                            } else {
                                $tinhTrangChup = 'Cần chụp';
                                $tinhTrangChupClass = 'bg-warning';
                            }

                            $rawNgayTraLinkIn = $item->ngay_tra_link_in_du_kien ?? $item->ngay_tra_link_in_chinh_thuc ?? null;
                            if ($rawNgayTraLinkIn instanceof \Carbon\CarbonInterface) {
                                $ngayTraLinkIn = $rawNgayTraLinkIn->copy()->startOfDay();
                            } elseif (! empty($rawNgayTraLinkIn)) {
                                $ngayTraLinkIn = \Carbon\Carbon::parse($rawNgayTraLinkIn)->startOfDay();
                            } else {
                                $ngayTraLinkIn = null;
                            }

                            $coFileIn = ! empty($item->link_in);
                            if ($coFileIn) {
                                $tinhTrangEdit = 'Đã edit';
                                $tinhTrangEditClass = 'bg-success';
                            } elseif ($ngayTraLinkIn && $ngayTraLinkIn->greaterThan($homNay)) {
                                $tinhTrangEdit = 'Đợi edit';
                                $tinhTrangEditClass = 'bg-secondary';
                            } elseif ($ngayTraLinkIn && $ngayTraLinkIn->equalTo($homNay)) {
                                $tinhTrangEdit = 'Cần edit';
                                $tinhTrangEditClass = 'bg-warning';
                            } elseif ($ngayTraLinkIn && $ngayTraLinkIn->lessThan($homNay)) {
                                $tinhTrangEdit = 'Trễ edit';
                                $tinhTrangEditClass = 'bg-danger';
                            } else {
                                $tinhTrangEdit = 'Cần edit';
                                $tinhTrangEditClass = 'bg-warning';
                            }

                            $ngayChupStr = '—';
                            if ($ngayChupNgay) {
                                $ngayChupStr = $ngayChupNgay->format('d/m/Y');
                                $buoiKey = $item->buoi_chup ?? '';
                                if ($buoiKey !== '' && isset($buoiChupLabels[$buoiKey])) {
                                    $ngayChupStr .= ' · '.$buoiChupLabels[$buoiKey];
                                }
                            }

                            $rawNgayTraLinkDemo = $item->ngay_tra_link_demo_du_kien ?? $item->ngay_tra_link_demo_chinh_thuc ?? null;
                            $rawNgayUpLinkDemoGanNhat = $item->ngay_up_link_demo_gan_nhat ?? null;
                            $rawNgayUpLinkInGanNhat = $item->ngay_up_link_in_gan_nhat ?? null;

                            $sdtBrief = (string) (($item->email_sdt_chu_re ?? '') !== ''
                                ? ($item->email_sdt_chu_re ?? '')
                                : ($item->email_sdt_co_dau ?? ''));

                            $ghiChuHienThi = $item->yeu_cau_dac_biet ?: ($item->ghi_chu_sale ?? null);
                        @endphp
                        <tr>
                            <td class="text-center text-nowrap">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                            <td class="cell-wrap" style="min-width: 180px;"><span class="fw-medium">{{ $tenKhachHang }}</span></td>
                            <td class="cell-wrap" style="min-width: 160px;">{{ $item->dia_diem_chup ? str($item->dia_diem_chup)->limit(25) : '—' }}</td>
                            <td class="text-nowrap">{{ $ngayChupStr }}</td>
                            <td class="text-nowrap">
                                <span class="badge {{ $tinhTrangChupClass ?? 'bg-secondary' }}">{{ $tinhTrangChup ?? '—' }}</span>
                            </td>
                            <td class="cell-wrap" style="min-width: 180px;">
                                {{ $trangPhucNames->isNotEmpty()
                                    ? str($trangPhucNames->implode(', '))->limit(30)
                                    : '—' }}
                            </td>
                            <td class="cell-wrap" style="min-width: 220px;">{{ $ghiChuHienThi ? str($ghiChuHienThi)->limit(40) : '—' }}</td>
                            <td class="text-nowrap">
                                @if(! empty($rawNgayTraLinkDemo))
                                    {{ $rawNgayTraLinkDemo instanceof \Carbon\CarbonInterface
                                        ? $rawNgayTraLinkDemo->format('d/m/Y')
                                        : \Carbon\Carbon::parse($rawNgayTraLinkDemo)->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <span class="badge {{ $tinhTrangEditClass ?? 'bg-secondary' }}">{{ $tinhTrangEdit ?? '—' }}</span>
                            </td>
                            {{-- <td>{{ $item->trang_thai_hop_dong ?? '—' }}</td> --}}
                            <td class="text-nowrap">
                                <div class="d-flex gap-1 flex-wrap">
                                    @if(!empty($roles))
                                        @foreach($roles as $role)
                                            <span class="badge {{ $badgeClassByRole[$role] ?? 'bg-secondary' }}">{{ $role }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">
                                @if(!empty($item->link_demo))
                                    <a class="btn btn-sm btn-outline-info btn-icon-only"
                                       href="{{ $item->link_demo }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="Xem file chụp"
                                       aria-label="Xem file chụp">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">Xem file chụp</span>
                                    </a>
                                @endif
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-clock-history me-1" aria-hidden="true" title="Up gần nhất"></i>
                                    <span class="visually-hidden">Up gần nhất:</span>
                                    @if(! empty($rawNgayUpLinkDemoGanNhat))
                                        {{ $rawNgayUpLinkDemoGanNhat instanceof \Carbon\CarbonInterface
                                            ? $rawNgayUpLinkDemoGanNhat->format('d/m/Y H:i')
                                            : \Carbon\Carbon::parse($rawNgayUpLinkDemoGanNhat)->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">
                                @if(!empty($item->link_in))
                                    <a class="btn btn-sm btn-outline-info btn-icon-only"
                                       href="{{ $item->link_in }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="Xem file hoàn thành"
                                       aria-label="Xem file hoàn thành">
                                        <i class="bi bi-printer" aria-hidden="true"></i>
                                        <span class="visually-hidden">Xem file hoàn thành</span>
                                    </a>
                                @endif
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-clock-history me-1" aria-hidden="true" title="Up gần nhất"></i>
                                    <span class="visually-hidden">Up gần nhất:</span>
                                    @if(! empty($rawNgayUpLinkInGanNhat))
                                        {{ $rawNgayUpLinkInGanNhat instanceof \Carbon\CarbonInterface
                                            ? $rawNgayUpLinkInGanNhat->format('d/m/Y H:i')
                                            : \Carbon\Carbon::parse($rawNgayUpLinkInGanNhat)->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex gap-2 flex-wrap">
                                    {{-- In brief: chuyển sang Lịch làm việc (modal Chi tiết công việc)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-dark btn-in-brief btn-icon-only"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalInBrief"
                                            data-khach-hang="{{ e($tenKhachHang) }}"
                                            data-sdt="{{ e($sdtBrief) }}"
                                            data-ngay-chup="{{ e($ngayChupStr) }}"
                                            data-nguoi-chup="{{ e($item->thoChup?->user?->name ?? '—') }}"
                                            data-dia-diem="{{ e($item->dia_diem_chup ?? '—') }}"
                                            data-concept="{{ e(optional($conceptMap->get((int) ($item->concept_id ?? 0)))->ten_concept ?? '—') }}"
                                            data-trang-phuc="{{ e($trangPhucNames->isNotEmpty() ? $trangPhucNames->implode(', ') : '—') }}"
                                            data-ghi-chu="{{ e($ghiChuHienThi ?? '—') }}"
                                            title="In brief"
                                            aria-label="In brief">
                                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                                        <span class="visually-hidden">In brief</span>
                                    </button>
                                    --}}

                                    @if(in_array('Chụp', $roles ?? [], true))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-cap-nhat-link btn-icon-only"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCapNhatLink"
                                                data-url="{{ route('admin.nhan-su.cong-viec-cua-toi.cap-nhat-link', $item) }}"
                                                data-type="demo"
                                                data-title="Cập nhật link file chụp"
                                                data-label="Link file chụp"
                                                data-current="{{ e($item->link_demo ?? '') }}"
                                                title="Up file chụp"
                                                aria-label="Up file chụp">
                                            <i class="bi bi-camera" aria-hidden="true"></i>
                                            <span class="visually-hidden">Up file chụp</span>
                                        </button>
                                    @endif

                                    @if(in_array('Edit', $roles ?? [], true))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success btn-cap-nhat-link btn-icon-only"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCapNhatLink"
                                                data-url="{{ route('admin.nhan-su.cong-viec-cua-toi.cap-nhat-link', $item) }}"
                                                data-type="in"
                                                data-title="Cập nhật link file edit"
                                                data-label="Link file edit"
                                                data-current="{{ e($item->link_in ?? '') }}"
                                                title="Up file edit"
                                                aria-label="Up file edit">
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                            <span class="visually-hidden">Up file edit</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-4 text-muted">Chưa có hợp đồng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="cvct-view-grid-wrap" class="d-none">
            <div class="row g-4">
                @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $buoiChupLabels = ['sang' => 'Sáng', 'chieu' => 'Chiều', 'ca_ngay' => 'Cả ngày'];

                        $tenKhachHang = collect([$item->ten_chu_re ?? '', $item->ten_co_dau ?? ''])
                            ->filter()
                            ->implode(' / ')
                            ?: '—';

                        $trangPhucNames = collect($item->hopDongCuoiTrangPhuc ?? [])
                            ->map(fn ($row) => $row->trangPhuc?->ten_san_pham)
                            ->filter()
                            ->values();

                        if ($isAdmin) {
                            $roles = [];
                            if ($item->tho_chup_id) {
                                $roles[] = 'Chụp';
                            }
                            if ($item->tho_make_id) {
                                $roles[] = 'Make';
                            }
                            if ($item->tho_edit_id) {
                                $roles[] = 'Edit';
                            }
                        } else {
                            $roles = [];
                            if ((int) ($item->tho_chup_id ?? 0) === (int) $nhanVienId) {
                                $roles[] = 'Chụp';
                            }
                            if ((int) ($item->tho_make_id ?? 0) === (int) $nhanVienId) {
                                $roles[] = 'Make';
                            }
                            if ((int) ($item->tho_edit_id ?? 0) === (int) $nhanVienId) {
                                $roles[] = 'Edit';
                            }
                        }

                        $homNay = now()->startOfDay();
                        $ngayChupNgay = $item->ngay_chup_thuc_te ?? $item->ngay_chup_du_kien;
                        $ngayChup = $ngayChupNgay ? $ngayChupNgay->copy()->startOfDay() : null;
                        $coFileDemo = ! empty($item->link_demo);

                        if ($coFileDemo) {
                            $tinhTrangChup = 'Đã chụp';
                            $tinhTrangChupClass = 'bg-success';
                        } elseif ($ngayChup && $ngayChup->greaterThan($homNay)) {
                            $tinhTrangChup = 'Đợi chụp';
                            $tinhTrangChupClass = 'bg-secondary';
                        } elseif ($ngayChup && $ngayChup->equalTo($homNay)) {
                            $tinhTrangChup = 'Cần chụp';
                            $tinhTrangChupClass = 'bg-warning';
                        } elseif ($ngayChup && $ngayChup->lessThan($homNay)) {
                            $tinhTrangChup = 'Trễ chụp';
                            $tinhTrangChupClass = 'bg-danger';
                        } else {
                            $tinhTrangChup = 'Cần chụp';
                            $tinhTrangChupClass = 'bg-warning';
                        }

                        $rawNgayTraLinkIn = $item->ngay_tra_link_in_du_kien ?? $item->ngay_tra_link_in_chinh_thuc ?? null;
                        if ($rawNgayTraLinkIn instanceof \Carbon\CarbonInterface) {
                            $ngayTraLinkIn = $rawNgayTraLinkIn->copy()->startOfDay();
                        } elseif (! empty($rawNgayTraLinkIn)) {
                            $ngayTraLinkIn = \Carbon\Carbon::parse($rawNgayTraLinkIn)->startOfDay();
                        } else {
                            $ngayTraLinkIn = null;
                        }

                        $coFileIn = ! empty($item->link_in);
                        if ($coFileIn) {
                            $tinhTrangEdit = 'Đã edit';
                            $tinhTrangEditClass = 'bg-success';
                        } elseif ($ngayTraLinkIn && $ngayTraLinkIn->greaterThan($homNay)) {
                            $tinhTrangEdit = 'Đợi edit';
                            $tinhTrangEditClass = 'bg-secondary';
                        } elseif ($ngayTraLinkIn && $ngayTraLinkIn->equalTo($homNay)) {
                            $tinhTrangEdit = 'Cần edit';
                            $tinhTrangEditClass = 'bg-warning';
                        } elseif ($ngayTraLinkIn && $ngayTraLinkIn->lessThan($homNay)) {
                            $tinhTrangEdit = 'Trễ edit';
                            $tinhTrangEditClass = 'bg-danger';
                        } else {
                            $tinhTrangEdit = 'Cần edit';
                            $tinhTrangEditClass = 'bg-warning';
                        }

                        $ngayChupStr = '—';
                        if ($ngayChupNgay) {
                            $ngayChupStr = $ngayChupNgay->format('d/m/Y');
                            $buoiKey = $item->buoi_chup ?? '';
                            if ($buoiKey !== '' && isset($buoiChupLabels[$buoiKey])) {
                                $ngayChupStr .= ' · '.$buoiChupLabels[$buoiKey];
                            }
                        }

                        $rawNgayTraLinkDemo = $item->ngay_tra_link_demo_du_kien ?? $item->ngay_tra_link_demo_chinh_thuc ?? null;
                        $rawNgayUpLinkDemoGanNhat = $item->ngay_up_link_demo_gan_nhat ?? null;
                        $rawNgayUpLinkInGanNhat = $item->ngay_up_link_in_gan_nhat ?? null;

                        $sdtBrief = (string) (($item->email_sdt_chu_re ?? '') !== ''
                            ? ($item->email_sdt_chu_re ?? '')
                            : ($item->email_sdt_co_dau ?? ''));

                        $ghiChuHienThi = $item->yeu_cau_dac_biet ?: ($item->ghi_chu_sale ?? null);
                        $conceptName = optional($conceptMap->get((int) ($item->concept_id ?? 0)))->ten_concept ?? '—';
                        $ngayTraDemoStr = ! empty($rawNgayTraLinkDemo)
                            ? ($rawNgayTraLinkDemo instanceof \Carbon\CarbonInterface
                                ? $rawNgayTraLinkDemo->format('d/m/Y')
                                : \Carbon\Carbon::parse($rawNgayTraLinkDemo)->format('d/m/Y'))
                            : '—';
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
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card cvct-work-card shadow-sm">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div style="min-width: 0;">
                                        <div class="small text-muted mb-1">
                                            STT {{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}
                                        </div>
                                        <h5 class="mb-1 text-truncate" title="{{ e($tenKhachHang) }}">{{ $tenKhachHang }}</h5>
                                        <div class="small text-muted text-truncate" title="{{ e($sdtBrief !== '' ? $sdtBrief : '—') }}">
                                            {{ $sdtBrief !== '' ? $sdtBrief : '—' }}
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 flex-wrap justify-content-end">
                                        <span class="badge {{ $tinhTrangChupClass ?? 'bg-secondary' }}">{{ $tinhTrangChup ?? '—' }}</span>
                                        <span class="badge {{ $tinhTrangEditClass ?? 'bg-secondary' }}">{{ $tinhTrangEdit ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 flex-wrap mt-3">
                                    @if(!empty($roles))
                                        @foreach($roles as $role)
                                            <span class="badge {{ $badgeClassByRole[$role] ?? 'bg-secondary' }}">{{ $role }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Chưa có vai trò được gán</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="info-box">
                                            <span class="meta-label">Ngày chụp</span>
                                            <div class="meta-value fw-medium">{{ $ngayChupStr }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-box">
                                            <span class="meta-label">Ngày hẹn trả demo</span>
                                            <div class="meta-value fw-medium">{{ $ngayTraDemoStr }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <span class="meta-label">Địa điểm</span>
                                    <div class="meta-value">{{ $item->dia_diem_chup ?: '—' }}</div>
                                </div>

                                <div>
                                    <span class="meta-label">Trang phục</span>
                                    <div class="meta-value">
                                        {{ $trangPhucNames->isNotEmpty() ? $trangPhucNames->implode(', ') : '—' }}
                                    </div>
                                </div>

                                <div>
                                    <span class="meta-label">Ghi chú</span>
                                    <div class="meta-value">{{ $ghiChuHienThi ?: '—' }}</div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="info-box">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <span class="meta-label">File chụp</span>
                                                    <div class="small text-muted">Up gần nhất: {{ $ngayUpLinkDemoStr }}</div>
                                                </div>
                                                @if(!empty($item->link_demo))
                                                    <a class="btn btn-sm btn-outline-info btn-icon-only"
                                                       href="{{ $item->link_demo }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       title="Xem file chụp"
                                                       aria-label="Xem file chụp">
                                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                                        <span class="visually-hidden">Xem file chụp</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-box">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <span class="meta-label">File hoàn thành</span>
                                                    <div class="small text-muted">Up gần nhất: {{ $ngayUpLinkInStr }}</div>
                                                </div>
                                                @if(!empty($item->link_in))
                                                    <a class="btn btn-sm btn-outline-info btn-icon-only"
                                                       href="{{ $item->link_in }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       title="Xem file hoàn thành"
                                                       aria-label="Xem file hoàn thành">
                                                        <i class="bi bi-printer" aria-hidden="true"></i>
                                                        <span class="visually-hidden">Xem file hoàn thành</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 flex-wrap pt-2 mt-auto border-top">
                                    {{-- In brief: chuyển sang Lịch làm việc (modal Chi tiết công việc)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-dark btn-in-brief"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalInBrief"
                                            data-khach-hang="{{ e($tenKhachHang) }}"
                                            data-sdt="{{ e($sdtBrief) }}"
                                            data-ngay-chup="{{ e($ngayChupStr) }}"
                                            data-nguoi-chup="{{ e($item->thoChup?->user?->name ?? '—') }}"
                                            data-dia-diem="{{ e($item->dia_diem_chup ?? '—') }}"
                                            data-concept="{{ e($conceptName) }}"
                                            data-trang-phuc="{{ e($trangPhucNames->isNotEmpty() ? $trangPhucNames->implode(', ') : '—') }}"
                                            data-ghi-chu="{{ e($ghiChuHienThi ?? '—') }}"
                                            title="In brief"
                                            aria-label="In brief">
                                        <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i> In brief
                                    </button>
                                    --}}

                                    @if(in_array('Chụp', $roles ?? [], true))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-cap-nhat-link"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCapNhatLink"
                                                data-url="{{ route('admin.nhan-su.cong-viec-cua-toi.cap-nhat-link', $item) }}"
                                                data-type="demo"
                                                data-title="Cập nhật link file chụp"
                                                data-label="Link file chụp"
                                                data-current="{{ e($item->link_demo ?? '') }}"
                                                title="Up file chụp"
                                                aria-label="Up file chụp">
                                            <i class="bi bi-camera me-1" aria-hidden="true"></i> Up file chụp
                                        </button>
                                    @endif

                                    @if(in_array('Edit', $roles ?? [], true))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success btn-cap-nhat-link"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCapNhatLink"
                                                data-url="{{ route('admin.nhan-su.cong-viec-cua-toi.cap-nhat-link', $item) }}"
                                                data-type="in"
                                                data-title="Cập nhật link file edit"
                                                data-label="Link file edit"
                                                data-current="{{ e($item->link_in ?? '') }}"
                                                title="Up file edit"
                                                aria-label="Up file edit">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i> Up file edit
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 text-muted border rounded">Chưa có hợp đồng nào.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <x-pagination-info :paginator="$danhSach ?? null" label="hợp đồng" />
        </div>
    </div>
</div>

{{-- Modal cập nhật link file (nhập link, không upload từ thiết bị) --}}
<div class="modal fade" id="modalCapNhatLink" tabindex="-1" aria-labelledby="modalCapNhatLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCapNhatLinkLabel">Cập nhật link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form method="POST" id="formCapNhatLink">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" id="capNhatLinkType" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="capNhatLinkInput" id="capNhatLinkLabel">Link</label>
                        <input type="text"
                               class="form-control"
                               id="capNhatLinkInput"
                               name="link"
                               placeholder="Nhập link file..."
                               autocomplete="off">
                        <div class="form-text">Nhập link file (Google Drive, Dropbox, ...). Không chọn file từ thiết bị.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary btn-icon-only"
                            data-bs-dismiss="modal"
                            title="Hủy"
                            aria-label="Hủy">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        <span class="visually-hidden">Hủy</span>
                    </button>
                    <button type="submit" class="btn btn-primary btn-icon-only" title="Cập nhật" aria-label="Cập nhật">
                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                        <span class="visually-hidden">Cập nhật</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal in brief: chuyển sang Lịch làm việc (modal Chi tiết công việc)
<div class="modal fade" id="modalInBrief" tabindex="-1" aria-labelledby="modalInBriefLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInBriefLabel">Brief lịch chụp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div id="briefCaptureArea" class="p-3" style="background: #fff;">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 170px;">Khách hàng</th>
                                    <td id="briefKhachHang">—</td>
                                </tr>
                                <tr>
                                    <th>Email/SĐT</th>
                                    <td id="briefSdt">—</td>
                                </tr>
                                <tr>
                                    <th style="width: 170px;">Ngày chụp</th>
                                    <td id="briefNgayChup">—</td>
                                </tr>
                                <tr>
                                    <th>Người chụp</th>
                                    <td id="briefNguoiChup">—</td>
                                </tr>
                                <tr>
                                    <th>Địa điểm</th>
                                    <td id="briefDiaDiem">—</td>
                                </tr>
                                <tr>
                                    <th>Concept</th>
                                    <td id="briefConcept">—</td>
                                </tr>
                                <tr>
                                    <th>Trang phục</th>
                                    <td id="briefTrangPhuc">—</td>
                                </tr>
                                <tr>
                                    <th>Ghi chú</th>
                                    <td id="briefGhiChu" style="white-space: pre-wrap;">—</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary btn-icon-only"
                        id="btnTaiBriefPng"
                        title="Tải xuống"
                        aria-label="Tải xuống">
                    <i class="bi bi-download" aria-hidden="true"></i>
                    <span class="visually-hidden">Tải xuống</span>
                </button>
                <button type="button"
                        class="btn btn-outline-secondary btn-icon-only"
                        data-bs-dismiss="modal"
                        title="Đóng"
                        aria-label="Đóng">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                    <span class="visually-hidden">Đóng</span>
                </button>
            </div>
        </div>
    </div>
</div>
--}}

<script>
    (function () {
        var LS_KEY = 'adminCongViecCuaToiView';
        var tableWrap = document.getElementById('cvct-view-table-wrap');
        var gridWrap = document.getElementById('cvct-view-grid-wrap');
        var btnTable = document.getElementById('cvct-view-btn-table');
        var btnGrid = document.getElementById('cvct-view-btn-grid');
        if (!tableWrap || !gridWrap || !btnTable || !btnGrid) return;

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
            } catch (e) {
                // ignore
            }
        }

        var saved = null;
        try {
            saved = localStorage.getItem(LS_KEY);
        } catch (e) {
            // ignore
        }
        setView(saved === 'grid' ? 'grid' : 'table');

        btnTable.addEventListener('click', function () {
            setView('table');
        });
        btnGrid.addEventListener('click', function () {
            setView('grid');
        });
    })();
</script>

<script>
    (function () {
        var modal = document.getElementById('modalCapNhatLink');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) return;

            var url = button.getAttribute('data-url') || '';
            var type = button.getAttribute('data-type') || '';
            var title = button.getAttribute('data-title') || 'Cập nhật link';
            var label = button.getAttribute('data-label') || 'Link';
            var current = button.getAttribute('data-current') || '';

            var form = document.getElementById('formCapNhatLink');
            var titleEl = document.getElementById('modalCapNhatLinkLabel');
            var labelEl = document.getElementById('capNhatLinkLabel');
            var typeEl = document.getElementById('capNhatLinkType');
            var input = document.getElementById('capNhatLinkInput');

            if (form) form.action = url;
            if (titleEl) titleEl.textContent = title;
            if (labelEl) labelEl.textContent = label;
            if (typeEl) typeEl.value = type;
            if (input) input.value = current;
        });

        modal.addEventListener('shown.bs.modal', function () {
            var input = document.getElementById('capNhatLinkInput');
            if (input) input.focus();
        });
    })();
</script>

{{-- In brief (html2canvas + modal): chuyển sang Lịch làm việc
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    (function () {
        var btn = document.getElementById('btnTaiBriefPng');
        if (!btn) return;

        btn.addEventListener('click', async function () {
            var captureEl = document.getElementById('briefCaptureArea');
            if (!captureEl) return;

            if (typeof window.html2canvas !== 'function') {
                alert('Thiếu thư viện tạo ảnh (html2canvas). Vui lòng tải lại trang.');
                return;
            }

            var oldHtml = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = 'Đang tạo ảnh...';

            try {
                var canvas = await window.html2canvas(captureEl, {
                    backgroundColor: '#ffffff',
                    scale: 2,
                    useCORS: true,
                });

                var dataUrl = canvas.toDataURL('image/png');
                var link = document.createElement('a');
                link.href = dataUrl;

                var ngay = (document.getElementById('briefNgayChup')?.textContent || '')
                    .trim()
                    .replace(/[^\d]/g, '');
                link.download = 'brief-lich-chup' + (ngay ? ('-' + ngay) : '') + '.png';

                document.body.appendChild(link);
                link.click();
                link.remove();
            } finally {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        });
    })();
</script>

<script>
    (function () {
        var modal = document.getElementById('modalInBrief');
        if (!modal) return;

        function setText(id, value) {
            var el = document.getElementById(id);
            if (el) el.textContent = (value && String(value).trim()) ? value : '—';
        }

        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) return;

            setText('briefKhachHang', button.getAttribute('data-khach-hang'));
            setText('briefSdt', button.getAttribute('data-sdt'));
            setText('briefNgayChup', button.getAttribute('data-ngay-chup'));
            setText('briefNguoiChup', button.getAttribute('data-nguoi-chup'));
            setText('briefDiaDiem', button.getAttribute('data-dia-diem'));
            setText('briefConcept', button.getAttribute('data-concept'));
            setText('briefTrangPhuc', button.getAttribute('data-trang-phuc'));
            setText('briefGhiChu', button.getAttribute('data-ghi-chu'));
        });
    })();
</script>
--}}
@endsection
