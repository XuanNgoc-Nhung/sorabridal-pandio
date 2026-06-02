@extends('admin.layouts.app')

@section('content')
@php
    $sapXepTheoMacDinh = \App\Models\TaiLieu::SAP_XEP_MAC_DINH;
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
        <form action="{{ route('admin.he-thong.tai-lieu') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Tên hoặc mô tả tài liệu</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập tên hoặc mô tả...">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="sap_xep_theo">Sắp xếp theo</label>
                    <select class="select2-admin form-select" id="sap_xep_theo" name="sap_xep_theo">
                        @foreach(\App\Models\TaiLieu::SAP_XEP_OPTIONS as $value => $label)
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
                    <a href="{{ route('admin.he-thong.tai-lieu') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách tài liệu</span>
            <button type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalThemTaiLieu">
                <i class="fa-solid fa-plus me-1"></i> Thêm mới
            </button>
        </h5>
        <div class="card-body">
        <div class="table-responsive table-wrapper-bordered">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 48px;">STT</th>
                        <th>Tên tài liệu</th>
                        <th>Mô tả</th>
                        <th>File</th>
                        <th>Thời gian tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSachTaiLieu as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSachTaiLieu->currentPage() - 1) * $danhSachTaiLieu->perPage() + $index + 1 }}</td>
                        <td class="text-wrap">
                            <div class="fw-medium">{{ $item->ten_tai_lieu }}</div>
                        </td>
                        <td class="text-wrap tai-lieu-mo-ta">{{ $item->mo_ta ?: '—' }}</td>
                        <td class="text-wrap tai-lieu-file">
                            <a href="{{ $item->duong_dan }}" target="_blank" class="small d-inline-block mt-1">
                                {{ $item->file ?: '—' }}
                            </a>
                        </td>
                        <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-center">
                            <form id="form-xoa-tai-lieu-{{ $item->id }}" action="{{ route('admin.he-thong.tai-lieu.destroy', $item) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-xoa-tai-lieu"
                                    data-form-id="form-xoa-tai-lieu-{{ $item->id }}"
                                    data-ten="{{ e($item->ten_tai_lieu) }}">
                                <i class="fa-solid fa-trash me-1"></i> Xóa
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có tài liệu nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-info :paginator="$danhSachTaiLieu" label="tài liệu" />
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemTaiLieu" tabindex="-1" aria-labelledby="modalThemTaiLieuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-them-tai-lieu">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalThemTaiLieuLabel">Thêm tài liệu mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form action="{{ route('admin.he-thong.tai-lieu.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="col-12">
                            <label class="form-label" for="tap_tin">Chọn file upload <span class="text-danger">*</span></label>
                            <input type="file"
                                   class="form-control"
                                   id="tap_tin"
                                   name="tap_tin"
                                   required>
                            <div class="form-text">File sẽ được lưu vào thư mục <code>taiLieu</code> trong disk public. Dung lượng tối đa 20MB.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="ten_tai_lieu">Tên tài liệu <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="ten_tai_lieu"
                                   name="ten_tai_lieu"
                                   value="{{ old('ten_tai_lieu') }}"
                                   placeholder="Nhập tên tài liệu"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="mo_ta">Mô tả</label>
                            <textarea class="form-control"
                                      id="mo_ta"
                                      name="mo_ta"
                                      rows="3"
                                      placeholder="Nhập mô tả ngắn cho tài liệu">{{ old('mo_ta') }}</textarea>
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

<div class="modal fade" id="modalXacNhanXoaTaiLieu" tabindex="-1" aria-labelledby="modalXacNhanXoaTaiLieuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm-xoa">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalXacNhanXoaTaiLieuLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa tài liệu <span class="fw-medium" id="tenTaiLieuCanXoa">này</span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="btnXacNhanXoaTaiLieu">
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
    min-width: 1040px;
}

.table-wrapper-bordered .table th,
.table-wrapper-bordered .table td {
    border: 1px solid var(--bs-border-color, #dee2e6);
    vertical-align: middle;
}

.modal-them-tai-lieu {
    max-width: 720px;
}

.modal-confirm-xoa {
    max-width: 420px;
}

.tai-lieu-mo-ta {
    min-width: 220px;
    white-space: normal;
}

.tai-lieu-file {
    min-width: 200px;
}

.tai-lieu-path {
    min-width: 280px;
    word-break: break-all;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function cleanupModalBackdrop() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    var modalThemTaiLieu = document.getElementById('modalThemTaiLieu');
    var modalXacNhanXoaTaiLieu = document.getElementById('modalXacNhanXoaTaiLieu');
    var btnXacNhanXoaTaiLieu = document.getElementById('btnXacNhanXoaTaiLieu');
    var tenTaiLieuCanXoa = document.getElementById('tenTaiLieuCanXoa');
    var formIdCanXoa = null;

    [modalThemTaiLieu, modalXacNhanXoaTaiLieu].forEach(function(modal) {
        if (modal) {
            modal.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
        }
    });

    @if($errors->any())
    if (modalThemTaiLieu) {
        bootstrap.Modal.getOrCreateInstance(modalThemTaiLieu).show();
    }
    @endif

    document.querySelectorAll('.btn-xoa-tai-lieu').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formIdCanXoa = this.getAttribute('data-form-id');

            if (tenTaiLieuCanXoa) {
                tenTaiLieuCanXoa.textContent = this.getAttribute('data-ten') || 'này';
            }

            if (modalXacNhanXoaTaiLieu) {
                bootstrap.Modal.getOrCreateInstance(modalXacNhanXoaTaiLieu).show();
            }
        });
    });

    if (btnXacNhanXoaTaiLieu) {
        btnXacNhanXoaTaiLieu.addEventListener('click', function() {
            if (!formIdCanXoa) {
                return;
            }

            var form = document.getElementById(formIdCanXoa);
            if (form) {
                form.submit();
            }
        });
    }
});
</script>
@endpush
@endsection
