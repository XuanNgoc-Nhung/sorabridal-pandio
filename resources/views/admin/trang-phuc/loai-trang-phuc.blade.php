@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\DanhMucTrangPhuc::SAP_XEP_MAC_DINH;
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
        <form action="{{ route('admin.trang-phuc.loai-trang-phuc') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên hoặc mã danh mục</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên hoặc mã danh mục...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\DanhMucTrangPhuc::SAP_XEP_OPTIONS as $value => $label)
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
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.trang-phuc.loai-trang-phuc') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách loại trang phục</span>
            <span data-bs-toggle="tooltip" title="Thêm loại trang phục mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemDanhMuc">
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
                        <th>Mã danh mục</th>
                        <th>Tên danh mục</th>
                        <th class="text-center" style="width: 120px;">Số lượng trang phục</th>
                        <th>Ghi chú</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $soLuongTrangPhuc = (int) ($item->trang_phucs_count ?? 0);
                        $coTheXoa = $soLuongTrangPhuc === 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td>{{ $item->ma_danh_muc ?? '—' }}</td>
                        <td><span class="fw-medium">{{ $item->ten_danh_muc ?? '—' }}</span></td>
                        <td class="text-center">{{ number_format($soLuongTrangPhuc, 0, ',', '.') }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->ghi_chu ?? '—', 60) }}</td>
                        <td class="text-center text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-primary btn-sua-danh-muc"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSuaDanhMuc"
                                        data-url="{{ route('admin.trang-phuc.loai-trang-phuc.update', $item) }}"
                                        data-ma="{{ e($item->ma_danh_muc ?? '') }}"
                                        data-ten="{{ e($item->ten_danh_muc ?? '') }}"
                                        data-ghi-chu="{{ e($item->ghi_chu ?? '') }}"
                                        title="Chỉnh sửa"
                                        aria-label="Chỉnh sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                @if($coTheXoa)
                                <form id="form-xoa-dm-{{ $item->id }}" action="{{ route('admin.trang-phuc.loai-trang-phuc.destroy', $item) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                        class="btn btn-sm btn-icon btn-text-danger btn-xoa-danh-muc"
                                        data-form-id="form-xoa-dm-{{ $item->id }}"
                                        title="Xóa"
                                        aria-label="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @else
                                <span data-bs-toggle="tooltip" title="Đang có {{ number_format($soLuongTrangPhuc, 0, ',', '.') }} sản phẩm thuộc loại này, không thể xoá">
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary"
                                            disabled
                                            aria-label="Không thể xóa vì còn sản phẩm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu loại trang phục.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="loại trang phục" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới --}}
<div class="modal fade" id="modalThemDanhMuc" tabindex="-1" aria-labelledby="modalThemDanhMucLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-danh-muc-form">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemDanhMucLabel">Thêm loại trang phục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.trang-phuc.loai-trang-phuc.store') }}" method="POST" id="formThemDanhMuc">
                @csrf
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="them_ma_danh_muc">Mã danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ma_danh_muc" name="ma_danh_muc" value="{{ old('ma_danh_muc') }}" placeholder="Ví dụ: DM001" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="them_ten_danh_muc">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ten_danh_muc" name="ten_danh_muc" value="{{ old('ten_danh_muc') }}" placeholder="Nhập tên danh mục" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="them_ghi_chu" name="ghi_chu" rows="3" placeholder="Ghi chú...">{{ old('ghi_chu') }}</textarea>
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

{{-- Modal Sửa --}}
<div class="modal fade" id="modalSuaDanhMuc" tabindex="-1" aria-labelledby="modalSuaDanhMucLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-danh-muc-form">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaDanhMucLabel">Chỉnh sửa loại trang phục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaDanhMuc" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_ma_danh_muc">Mã danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ma_danh_muc" name="ma_danh_muc" placeholder="Ví dụ: DM001" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="sua_ten_danh_muc">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ten_danh_muc" name="ten_danh_muc" placeholder="Nhập tên danh mục" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="sua_ghi_chu" name="ghi_chu" rows="3" placeholder="Ghi chú..."></textarea>
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

{{-- Modal xác nhận xóa --}}
<div class="modal fade" id="modalXacNhanXoaDanhMuc" tabindex="-1" aria-labelledby="modalXacNhanXoaDanhMucLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaDanhMucLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa loại trang phục này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaDanhMuc">
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
    -webkit-overflow-scrolling: touch;
}
.table-wrapper-bordered .table {
    border-collapse: collapse;
    min-width: 600px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
#modalThemDanhMuc .modal-danh-muc-form,
#modalSuaDanhMuc .modal-danh-muc-form {
    max-width: 90vw;
    width: 50%;
}
#modalXacNhanXoaDanhMuc .modal-confirm-xoa {
    max-width: 90vw;
    width: 400px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }
    [document.getElementById('modalThemDanhMuc'), document.getElementById('modalSuaDanhMuc')].forEach(function(modal) {
        if (modal) modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
    });

    @if($errors->any())
    var modalThem = document.getElementById('modalThemDanhMuc');
    if (modalThem) {
        new bootstrap.Modal(modalThem).show();
    }
    @endif

    var modalSua = document.getElementById('modalSuaDanhMuc');
    var formSua = document.getElementById('formSuaDanhMuc');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-danh-muc')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_ma_danh_muc').value = btn.getAttribute('data-ma') || '';
            document.getElementById('sua_ten_danh_muc').value = btn.getAttribute('data-ten') || '';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
        });
    }

    var modalXoa = document.getElementById('modalXacNhanXoaDanhMuc');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaDanhMuc');
    var formXoaId = null;
    document.querySelectorAll('.btn-xoa-danh-muc').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formXoaId = btn.getAttribute('data-form-id');
            if (modalXoa) new bootstrap.Modal(modalXoa).show();
        });
    });
    if (btnXacNhanXoa) {
        btnXacNhanXoa.addEventListener('click', function() {
            if (formXoaId) {
                var form = document.getElementById(formXoaId);
                if (form) form.submit();
            }
        });
    }
});
</script>
@endpush
@endsection
