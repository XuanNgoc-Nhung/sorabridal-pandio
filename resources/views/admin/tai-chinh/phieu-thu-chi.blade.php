@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\PhieuThuChi::SAP_XEP_MAC_DINH;
    $sapXepTheo = request('sap_xep_theo', $sapXepTheoMacDinh);
    $thuTu = request('thu_tu', 'desc');
    $hasFilter = request()->filled('search')
        || request()->filled('loai_phieu')
        || request()->filled('trang_thai')
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
        <form action="{{ route('admin.tai-chinh.phieu-thu-chi') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Từ khoá</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="loai_phieu">Loại phiếu</label>
                    <select class="select2-admin form-select" id="loai_phieu" name="loai_phieu" data-placeholder="Loại phiếu">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('loai_phieu') === '1' ? 'selected' : '' }}>Thu</option>
                        <option value="2" {{ request('loai_phieu') === '2' ? 'selected' : '' }}>Chi</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="trang_thai">Trạng thái</label>
                    <select class="select2-admin form-select" id="trang_thai" name="trang_thai" data-placeholder="Trạng thái">
                        <option value="">-- Tất cả --</option>
                        <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Đồng ý</option>
                        <option value="-1" {{ request('trang_thai') === '-1' ? 'selected' : '' }}>Từ chối</option>
                        <option value="2" {{ request('trang_thai') === '2' ? 'selected' : '' }}>Hoàn thành</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\PhieuThuChi::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.tai-chinh.phieu-thu-chi') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách phiếu thu chi</span>
            <span data-bs-toggle="tooltip" title="Thêm phiếu thu chi mới">
                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalThemPhieuThuChi">
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
                        <th class="text-center" style="width: 90px;">Loại phiếu</th>
                        <th class="text-end" style="width: 120px;">Số tiền</th>
                        <th>Lý do</th>
                        <th class="text-center" style="width: 100px;">Trạng thái</th>
                        <th>Người tạo</th>
                        <th>Người duyệt</th>
                        <th style="width: 110px;">Ngày duyệt</th>
                        <th>Ghi chú</th>
                        <th class="text-center" style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $loaiLabels = [
                            1 => 'Thu',
                            2 => 'Chi',
                        ];
                        $loaiBadges = [
                            1 => 'bg-label-success',
                            2 => 'bg-label-danger',
                        ];
                        $trangThaiLabels = [
                            -1 => 'Từ chối',
                            0 => 'Chờ xử lý',
                            1 => 'Đồng ý',
                            2 => 'Hoàn thành',
                        ];
                        $trangThaiBadges = [
                            -1 => 'bg-label-danger',
                            0 => 'bg-label-warning',
                            1 => 'bg-label-info',
                            2 => 'bg-label-success',
                        ];
                        $loai = (int)($item->loai_phieu ?? 1);
                        $tt = (int)($item->trang_thai ?? 0);
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td class="text-center">
                            <span class="badge {{ $loaiBadges[$loai] ?? 'bg-label-secondary' }}">{{ $loaiLabels[$loai] ?? '—' }}</span>
                        </td>
                        <td class="text-end">
                            {{ $item->so_tien !== null ? number_format((float)$item->so_tien, 0, ',', '.') . ' đ' : '—' }}
                        </td>
                        <td><span class="fw-medium">{{ $item->ly_do ?? '—' }}</span></td>
                        <td class="text-center">
                            <span class="badge {{ $trangThaiBadges[$tt] ?? 'bg-label-secondary' }}">{{ $trangThaiLabels[$tt] ?? '—' }}</span>
                        </td>
                        <td>{{ $item->nguoiTao?->name ?? '—' }}</td>
                        <td>{{ $item->nguoiDuyet?->name ?? '—' }}</td>
                        <td>{{ $item->ngay_duyet?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->ghi_chu ?? '—', 30) }}</td>
                        <td>
                            @if($tt === 0)
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <form action="{{ route('admin.tai-chinh.huy-phieu-thu-chi', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy phiếu này?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hủy">
                                        <i class="fa-solid fa-times me-1"></i> Huỷ
                                    </button>
                                </form>
                                <form action="{{ route('admin.tai-chinh.duyet-phieu-thu-chi', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn duyệt phiếu này?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Duyệt">
                                        <i class="fa-solid fa-check me-1"></i> Duyệt
                                    </button>
                                </form>
                            </div>
                            @endif
                            <div class="dropdown @if($tt === 0) mt-1 @endif">
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-sua-phieu-thu-chi"
                                       href="javascript:void(0);"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalSuaPhieuThuChi"
                                       data-url="{{ route('admin.tai-chinh.update-phieu-thu-chi', $item) }}"
                                       data-loai-phieu="{{ $item->loai_phieu ?? 1 }}"
                                       data-so-tien="{{ $item->so_tien ?? '' }}"
                                       data-ly-do="{{ e($item->ly_do ?? '') }}"
                                       data-trang-thai="{{ $item->trang_thai ?? 0 }}"
                                       data-ghi-chu="{{ e($item->ghi_chu ?? '') }}">
                                        <i class="fa-solid fa-pen me-2"></i> Sửa
                                    </a>
                                    <form id="form-xoa-ptc-{{ $item->id }}" action="{{ route('admin.tai-chinh.destroy-phieu-thu-chi', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="dropdown-item text-danger btn-xoa-phieu-thu-chi" data-form-id="form-xoa-ptc-{{ $item->id }}">
                                        <i class="fa-solid fa-trash me-2"></i> Xoá
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">Chưa có phiếu thu chi nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="phiếu thu chi" />
        </div>
    </div>
</div>

{{-- Modal Thêm mới phiếu thu chi --}}
<div class="modal fade" id="modalThemPhieuThuChi" tabindex="-1" aria-labelledby="modalThemPhieuThuChiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-phieu-thu-chi">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemPhieuThuChiLabel">Thêm phiếu thu chi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.tai-chinh.store-phieu-thu-chi') }}" method="POST" id="formThemPhieuThuChi">
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
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_loai_phieu">Loại phiếu <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select" id="them_loai_phieu" name="loai_phieu" required data-placeholder="Chọn loại phiếu">
                                <option value="1" {{ old('loai_phieu', '1') == '1' ? 'selected' : '' }}>Thu</option>
                                <option value="2" {{ old('loai_phieu') == '2' ? 'selected' : '' }}>Chi</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="them_so_tien">Số tiền <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="them_so_tien" name="so_tien" value="{{ old('so_tien') }}" min="0" step="0.01" placeholder="0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_ly_do">Lý do <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="them_ly_do" name="ly_do" value="{{ old('ly_do') }}" placeholder="Nhập lý do thu/chi" maxlength="255" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="them_ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="them_ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú...">{{ old('ghi_chu') }}</textarea>
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

{{-- Modal Chỉnh sửa phiếu thu chi --}}
<div class="modal fade" id="modalSuaPhieuThuChi" tabindex="-1" aria-labelledby="modalSuaPhieuThuChiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-phieu-thu-chi">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuaPhieuThuChiLabel">Chỉnh sửa phiếu thu chi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formSuaPhieuThuChi" method="POST" action="">
                @csrf
                @method('PUT')
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
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_loai_phieu">Loại phiếu <span class="text-danger">*</span></label>
                            <select class="select2-admin form-select" id="sua_loai_phieu" name="loai_phieu" required data-placeholder="Chọn loại phiếu">
                                <option value="1">Thu</option>
                                <option value="2">Chi</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_so_tien">Số tiền <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="sua_so_tien" name="so_tien" min="0" step="0.01" placeholder="0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_ly_do">Lý do <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sua_ly_do" name="ly_do" placeholder="Nhập lý do thu/chi" maxlength="255" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sua_trang_thai">Trạng thái</label>
                            <select class="select2-admin form-select" id="sua_trang_thai" name="trang_thai" data-placeholder="Chọn trạng thái">
                                <option value="-1">Từ chối</option>
                                <option value="0">Chờ xử lý</option>
                                <option value="1">Đồng ý</option>
                                <option value="2">Hoàn thành</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="sua_ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="sua_ghi_chu" name="ghi_chu" rows="2" placeholder="Ghi chú..."></textarea>
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
<div class="modal fade" id="modalXacNhanXoaPhieuThuChi" tabindex="-1" aria-labelledby="modalXacNhanXoaPhieuThuChiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaPhieuThuChiLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa phiếu thu chi này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaPhieuThuChi">
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
    min-width: 900px;
}
.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
}
#modalThemPhieuThuChi .modal-phieu-thu-chi,
#modalSuaPhieuThuChi .modal-phieu-thu-chi {
    max-width: 90vw;
    width: 500px;
}
#modalXacNhanXoaPhieuThuChi .modal-confirm-xoa {
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

    @if($errors->any())
    var modalThem = document.getElementById('modalThemPhieuThuChi');
    if (modalThem) {
        var m = new bootstrap.Modal(modalThem);
        m.show();
    }
    @endif

    // Modal Sửa: gán data vào form
    var modalSua = document.getElementById('modalSuaPhieuThuChi');
    var formSua = document.getElementById('formSuaPhieuThuChi');
    if (modalSua && formSua) {
        modalSua.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-sua-phieu-thu-chi')) return;
            var url = btn.getAttribute('data-url');
            if (url) formSua.action = url;
            document.getElementById('sua_loai_phieu').value = btn.getAttribute('data-loai-phieu') || '1';
            document.getElementById('sua_so_tien').value = btn.getAttribute('data-so-tien') || '';
            document.getElementById('sua_ly_do').value = btn.getAttribute('data-ly-do') || '';
            document.getElementById('sua_trang_thai').value = btn.getAttribute('data-trang-thai') ?? '0';
            document.getElementById('sua_ghi_chu').value = btn.getAttribute('data-ghi-chu') || '';
        });
    }

    // Xóa: modal xác nhận
    var modalXoa = document.getElementById('modalXacNhanXoaPhieuThuChi');
    var btnXacNhanXoa = document.getElementById('btnXacNhanXoaPhieuThuChi');
    var formIdCanXoa = null;
    if (modalXoa && btnXacNhanXoa) {
        modalXoa.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
        document.querySelectorAll('.btn-xoa-phieu-thu-chi').forEach(function(btn) {
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
