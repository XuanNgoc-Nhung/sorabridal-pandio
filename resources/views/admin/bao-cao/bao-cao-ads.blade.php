@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\BaoCaoAds::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('tu_khoa')
        || request()->filled('tu_ngay')
        || request()->filled('den_ngay')
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
        <form action="{{ route('admin.bao-cao.ads') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_khoa">Từ khoá</label>
                    <input type="text"
                           class="form-control"
                           id="tu_khoa"
                           name="tu_khoa"
                           value="{{ request('tu_khoa') }}"
                           placeholder="Nhập...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_ngay">Từ ngày</label>
                    <input type="text"
                           class="form-control flatpickr-date-admin"
                           id="tu_ngay"
                           name="tu_ngay"
                           value="{{ request('tu_ngay') }}"
                           placeholder="Chọn ngày"
                           autocomplete="off">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="den_ngay">Đến ngày</label>
                    <input type="text"
                           class="form-control flatpickr-date-admin"
                           id="den_ngay"
                           name="den_ngay"
                           value="{{ request('den_ngay') }}"
                           placeholder="Chọn ngày"
                           autocomplete="off">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\BaoCaoAds::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.bao-cao.ads') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Quảng cáo</span>
            <button type="button"
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalThemBaoCaoAds">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Ngày</th>
                        <th>CPQC TikTok</th>
                        <th>CPQC Facebook</th>
                        <th>CPQC Google</th>
                        <th>Khách mới</th>
                        <th>Lịch hẹn</th>
                        <th>CPL</th>
                        <th>ROAS</th>
                        <th>Tỷ lệ hẹn / khách</th>
                        <th>Khách đến cửa hàng</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td>{{ $item->ngay ?? '—' }}</td>
                        <td>{{ $item->ads_tiktok ?? '—' }}</td>
                        <td>{{ $item->ads_fb ?? '—' }}</td>
                        <td>{{ $item->cpqc_google ?? '—' }}</td>
                        <td>{{ $item->khach_moi ?? '—' }}</td>
                        <td>{{ $item->lich_hen ?? '—' }}</td>
                        <td>{{ $item->cpl ?? '—' }}</td>
                        <td>{{ $item->roas ?? '—' }}</td>
                        <td>{{ $item->ty_le_hen_tren_khach ?? '—' }}</td>
                        <td>{{ $item->khach_den_cua_hang ?? '—' }}</td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-sua-bao-cao-ads"
                                        href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSuaBaoCaoAds"
                                        data-url="{{ route('admin.bao-cao.ads.update', $item) }}"
                                        data-ngay="{{ e($item->ngay ?? '') }}"
                                        data-ads-tiktok="{{ e($item->ads_tiktok ?? '') }}"
                                        data-ads-fb="{{ e($item->ads_fb ?? '') }}"
                                        data-cpqc-google="{{ e($item->cpqc_google ?? '') }}"
                                        data-khach-moi="{{ e($item->khach_moi ?? '') }}"
                                        data-lich-hen="{{ e($item->lich_hen ?? '') }}"
                                        data-cpl="{{ e($item->cpl ?? '') }}"
                                        data-roas="{{ e($item->roas ?? '') }}"
                                        data-ty-le-hen-tren-khach="{{ e($item->ty_le_hen_tren_khach ?? '') }}"
                                        data-khach-den-cua-hang="{{ e($item->khach_den_cua_hang ?? '') }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-bao-cao-ads-{{ $item->id }}" action="{{ route('admin.bao-cao.ads.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-bao-cao-ads" data-form-id="form-xoa-bao-cao-ads-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 text-muted">Chưa có dữ liệu quảng cáo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="quảng cáo" />
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemBaoCaoAds" tabindex="-1" aria-labelledby="modalThemBaoCaoAdsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-form-bao-cao-ads">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemBaoCaoAdsLabel">Thêm quảng cáo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.bao-cao.ads.store') }}" method="POST">
                @csrf
                @if($errors->any())
                <div class="modal-body py-0">
                    <div class="alert alert-danger mb-0">
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
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_ngay">Ngày</label>
                            <input type="text" class="form-control" id="them_ngay" name="ngay" value="{{ old('ngay') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_ads_tiktok">CPQC TikTok</label>
                            <input type="text" class="form-control" id="them_ads_tiktok" name="ads_tiktok" value="{{ old('ads_tiktok') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_ads_fb">CPQC Facebook</label>
                            <input type="text" class="form-control" id="them_ads_fb" name="ads_fb" value="{{ old('ads_fb') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_cpqc_google">CPQC Google</label>
                            <input type="text" class="form-control" id="them_cpqc_google" name="cpqc_google" value="{{ old('cpqc_google') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_khach_moi">Khách mới</label>
                            <input type="text" class="form-control" id="them_khach_moi" name="khach_moi" value="{{ old('khach_moi') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_lich_hen">Lịch hẹn</label>
                            <input type="text" class="form-control" id="them_lich_hen" name="lich_hen" value="{{ old('lich_hen') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_cpl">CPL</label>
                            <input type="text" class="form-control" id="them_cpl" name="cpl" value="{{ old('cpl') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_roas">ROAS</label>
                            <input type="text" class="form-control" id="them_roas" name="roas" value="{{ old('roas') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_ty_le_hen_tren_khach">Tỷ lệ hẹn / khách</label>
                            <input type="text" class="form-control" id="them_ty_le_hen_tren_khach" name="ty_le_hen_tren_khach" value="{{ old('ty_le_hen_tren_khach') }}" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="them_khach_den_cua_hang">Khách đến cửa hàng</label>
                            <input type="text" class="form-control" id="them_khach_den_cua_hang" name="khach_den_cua_hang" value="{{ old('khach_den_cua_hang') }}" placeholder="Nhập">
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

<div class="modal fade" id="modalSuaBaoCaoAds" tabindex="-1" aria-labelledby="modalSuaBaoCaoAdsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-form-bao-cao-ads">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaBaoCaoAdsLabel">Chỉnh sửa quảng cáo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaBaoCaoAds" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_ngay">Ngày</label>
                            <input type="text" class="form-control" id="sua_ngay" name="ngay" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_ads_tiktok">CPQC TikTok</label>
                            <input type="text" class="form-control" id="sua_ads_tiktok" name="ads_tiktok" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_ads_fb">CPQC Facebook</label>
                            <input type="text" class="form-control" id="sua_ads_fb" name="ads_fb" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_cpqc_google">CPQC Google</label>
                            <input type="text" class="form-control" id="sua_cpqc_google" name="cpqc_google" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_khach_moi">Khách mới</label>
                            <input type="text" class="form-control" id="sua_khach_moi" name="khach_moi" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_lich_hen">Lịch hẹn</label>
                            <input type="text" class="form-control" id="sua_lich_hen" name="lich_hen" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_cpl">CPL</label>
                            <input type="text" class="form-control" id="sua_cpl" name="cpl" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_roas">ROAS</label>
                            <input type="text" class="form-control" id="sua_roas" name="roas" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_ty_le_hen_tren_khach">Tỷ lệ hẹn / khách</label>
                            <input type="text" class="form-control" id="sua_ty_le_hen_tren_khach" name="ty_le_hen_tren_khach" placeholder="Nhập">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label" for="sua_khach_den_cua_hang">Khách đến cửa hàng</label>
                            <input type="text" class="form-control" id="sua_khach_den_cua_hang" name="khach_den_cua_hang" placeholder="Nhập">
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

<div class="modal fade" id="modalXacNhanXoaBaoCaoAds" tabindex="-1" aria-labelledby="modalXacNhanXoaBaoCaoAdsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaBaoCaoAdsLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa bản ghi quảng cáo này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaBaoCaoAds">
                    <i class="fa-solid fa-trash me-1"></i> Xóa
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
}
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 1200px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
.modal-form-bao-cao-ads {
    max-width: 95vw;
}
#modalXacNhanXoaBaoCaoAds .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    [document.getElementById('modalThemBaoCaoAds'), document.getElementById('modalSuaBaoCaoAds')].forEach(function (modal) {
        if (modal) modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
    });

    @if($errors->any())
    var modalThem = document.getElementById('modalThemBaoCaoAds');
    if (modalThem) {
        bootstrap.Modal.getOrCreateInstance(modalThem).show();
    }
    @endif

    var fieldMap = [
        ['ngay', 'ngay'],
        ['ads-tiktok', 'ads_tiktok'],
        ['ads-fb', 'ads_fb'],
        ['cpqc-google', 'cpqc_google'],
        ['khach-moi', 'khach_moi'],
        ['lich-hen', 'lich_hen'],
        ['cpl', 'cpl'],
        ['roas', 'roas'],
        ['ty-le-hen-tren-khach', 'ty_le_hen_tren_khach'],
        ['khach-den-cua-hang', 'khach_den_cua_hang'],
    ];

    var modalSua = document.getElementById('modalSuaBaoCaoAds');
    var formSua = document.getElementById('formSuaBaoCaoAds');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-bao-cao-ads')) return;

            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;

            fieldMap.forEach(function (pair) {
                var input = document.getElementById('sua_' + pair[1]);
                if (input) {
                    input.value = btn.getAttribute('data-' + pair[0]) || '';
                }
            });
        });
    }

    var modalXoa = document.getElementById('modalXacNhanXoaBaoCaoAds');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaBaoCaoAds');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        document.querySelectorAll('.btn-xoa-bao-cao-ads').forEach(function (btn) {
            btn.addEventListener('click', function () {
                formIdCanXoa = this.getAttribute('data-form-id');
                if (!formIdCanXoa) return;
                bootstrap.Modal.getOrCreateInstance(modalXoa).show();
            });
        });
        btnXacNhanXoa.addEventListener('click', function () {
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
