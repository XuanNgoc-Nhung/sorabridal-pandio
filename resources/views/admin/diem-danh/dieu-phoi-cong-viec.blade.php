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
        <form action="{{ route('admin.diem-danh.dieu-phoi-cong-viec') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="search">Mã HĐ, địa điểm, concept hoặc ghi chú</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nhập để tìm kiếm...">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.diem-danh.dieu-phoi-cong-viec') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header">Điều phối công việc</h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Khách hàng</th>
                        <th>Thợ chụp</th>
                        <th>Thợ make</th>
                        <th>Thợ edit</th>
                        <th>Địa điểm</th>
                        <th>Ngày chụp</th>
                        <th>Ngày trả link in</th>
                        <th>Ngày hẹn trả demo</th>
                        {{-- <th>Trang phục</th> --}}
                        {{-- <th>Ghi chú</th> --}}
                        <th class="text-end">Tổng tiền</th>
                        <th>Trạng thái chụp</th>
                        <th>Trạng thái edit</th>
                        <th>Trạng thái HĐ</th>
                        <th class="text-center" style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    @php
                        $maHdDp = trim((string) ($item->ma_hop_dong ?? ''));
                        $tenKhachHang = $maHdDp !== '' ? $maHdDp : '—';

                        $homNay = now()->startOfDay();
                        $ngayChup = $item->ngay_chup ? $item->ngay_chup->copy()->startOfDay() : null;
                        $coFileDemo = !empty($item->link_file_demo);

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

                        $rawNgayTraLinkIn = $item->ngay_tra_link_in ?? null;
                        if ($rawNgayTraLinkIn instanceof \Carbon\CarbonInterface) {
                            $ngayTraLinkIn = $rawNgayTraLinkIn->copy()->startOfDay();
                        } elseif (!empty($rawNgayTraLinkIn)) {
                            $ngayTraLinkIn = \Carbon\Carbon::parse($rawNgayTraLinkIn)->startOfDay();
                        } else {
                            $ngayTraLinkIn = null;
                        }

                        $coFileIn = !empty($item->link_file_in);
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
                    @endphp
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td><span class="fw-medium">{{ $tenKhachHang }}</span></td>
                        <td>{{ $item->thoChup?->user?->name ?? '—' }}</td>
                        <td>{{ $item->thoMake?->user?->name ?? '—' }}</td>
                        <td>{{ $item->thoEdit?->user?->name ?? '—' }}</td>
                        <td>{{ $item->dia_diem ? str($item->dia_diem)->limit(25) : '—' }}</td>
                        <td>{{ $item->ngay_chup ? $item->ngay_chup->format('d/m/Y') : '—' }}</td>
                        <td>{{ $item->ngay_tra_link_in ? $item->ngay_tra_link_in->format('d/m/Y') : '—' }}</td>
                        <td>{{ $item->ngay_hen_tra_hang ? $item->ngay_hen_tra_hang->format('d/m/Y') : '—' }}</td>
                        {{-- <td>{{ $item->trang_phuc ? str($item->trang_phuc)->limit(30) : '—' }}</td> --}}
                        {{-- <td>{{ $item->ghi_chu_chup ? str($item->ghi_chu_chup)->limit(40) : '—' }}</td> --}}
                        <td class="text-end">{{ $item->tong_tien !== null ? number_format((float)$item->tong_tien, 0, ',', '.') . ' đ' : '—' }}</td>
                        <td><span class="badge {{ $tinhTrangChupClass ?? 'bg-secondary' }}">{{ $tinhTrangChup ?? '—' }}</span></td>
                        <td><span class="badge {{ $tinhTrangEditClass ?? 'bg-secondary' }}">{{ $tinhTrangEdit ?? '—' }}</span></td>
                        <td>{{ $item->trang_thai_hop_dong ?? '—' }}</td>
                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary btn-phan-viec"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPhanCong"
                                    data-url="{{ route('admin.diem-danh.phan-cong-cong-viec', $item) }}"
                                    data-ten-khach-hang="{{ e($tenKhachHang) }}"
                                    data-dia-diem="{{ e($item->dia_diem ?? '') }}"
                                    data-ngay-chup="{{ $item->ngay_chup ? $item->ngay_chup->format('Y-m-d H:i') : '' }}"
                                    data-ngay-hen-tra-hang="{{ $item->ngay_hen_tra_hang ? $item->ngay_hen_tra_hang->format('Y-m-d') : '' }}"
                                    data-ngay-tra-link-in="{{ $item->ngay_tra_link_in ? $item->ngay_tra_link_in->format('Y-m-d') : '' }}"
                                    {{-- data-trang-phuc="{{ e($item->trang_phuc ?? '') }}" --}}
                                    data-tong-tien="{{ $item->tong_tien !== null ? number_format((float)$item->tong_tien, 0, ',', '.') . ' đ' : '—' }}"
                                    data-trang-thai-chup="{{ e($item->trang_thai_chup ?? '') }}"
                                    data-trang-thai-edit="{{ e($item->trang_thai_edit ?? '') }}"
                                    data-tho-chup-id="{{ $item->tho_chup_id ?? '' }}"
                                    data-tho-make-id="{{ $item->tho_make_id ?? '' }}"
                                    data-tho-edit-id="{{ $item->tho_edit_id ?? '' }}">
                                <i class="fa-solid fa-user-check me-1"></i> Phân việc
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="16" class="text-center py-4 text-muted">Chưa có dữ liệu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="bản ghi" />
        </div>
    </div>
</div>

{{-- Modal Phân công công việc --}}
<div class="modal fade" id="modalPhanCong" tabindex="-1" aria-labelledby="modalPhanCongLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-phan-cong">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPhanCongLabel">Phân công công việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="formPhanCong" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-ten-khach-hang">Khách hàng</label>
                                <input type="text" class="form-control" id="pv-ten-khach-hang" disabled value="" placeholder="—">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-dia-diem">Địa điểm</label>
                                <input type="text" class="form-control" id="pv-dia-diem" name="dia_diem" value="" placeholder="Nhập địa điểm...">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-ngay-chup">Ngày chụp</label>
                                <input type="text" class="flatpickr-datetime-admin form-control" id="pv-ngay-chup" name="ngay_chup" value="" placeholder="dd/mm/yyyy hh:mm" autocomplete="off">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-ngay-hen-tra-hang">Ngày hẹn trả demo</label>
                                <input type="text" class="flatpickr-date-admin form-control" id="pv-ngay-hen-tra-hang" name="ngay_hen_tra_hang" value="" placeholder="dd/mm/yyyy" autocomplete="off">
                            </div>
                            {{-- <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-trang-phuc">Trang phục</label>
                                <input type="text" class="form-control" id="pv-trang-phuc" name="trang_phuc" value="" placeholder="Nhập trang phục...">
                            </div> --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-tong-tien">Tổng tiền</label>
                                <input type="text" class="form-control" id="pv-tong-tien" disabled value="" placeholder="—">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-trang-thai-chup">Trạng thái chụp</label>
                                <input type="text" class="form-control" id="pv-trang-thai-chup" disabled value="" placeholder="—">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-trang-thai-edit">Trạng thái edit</label>
                                <input type="text" class="form-control" id="pv-trang-thai-edit" disabled value="" placeholder="—">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="phan_cong_tho_chup_id">Thợ chụp</label>
                            <select id="phan_cong_tho_chup_id" name="tho_chup_id" class="select2-admin form-select" data-placeholder="Chọn thợ chụp" style="width: 100%;">
                                <option value="">— Chọn thợ chụp —</option>
                                @foreach($danhSachNhanVien ?? [] as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->user?->name ?? 'Nhân viên #' . $nv->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="phan_cong_tho_make_id">Thợ make</label>
                            <select id="phan_cong_tho_make_id" name="tho_make_id" class="select2-admin form-select" data-placeholder="Chọn thợ make" style="width: 100%;">
                                <option value="">— Chọn thợ make —</option>
                                @foreach($danhSachNhanVien ?? [] as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->user?->name ?? 'Nhân viên #' . $nv->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="phan_cong_tho_edit_id">Thợ chỉnh sửa</label>
                            <select id="phan_cong_tho_edit_id" name="tho_edit_id" class="select2-admin form-select" data-placeholder="Chọn thợ chỉnh sửa" style="width: 100%;">
                                <option value="">— Chọn thợ chỉnh sửa —</option>
                                @foreach($danhSachNhanVien ?? [] as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->user?->name ?? 'Nhân viên #' . $nv->id }}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label" for="pv-ngay-tra-link-in">Ngày trả link in</label>
                                <input type="text" class="flatpickr-date-admin form-control" id="pv-ngay-tra-link-in" name="ngay_tra_link_in" value="" placeholder="dd/mm/yyyy" autocomplete="off">
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
#modalPhanCong .modal-dialog.modal-phan-cong {
    max-width: 95vw;
    width: 992px;
}
@media (max-width: 991.98px) {
    #modalPhanCong .modal-dialog.modal-phan-cong { width: 95vw; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var $ = window.jQuery || window.$;
    var modalPhanCong = document.getElementById('modalPhanCong');
    var formPhanCong = document.getElementById('formPhanCong');

    // Khi mở modal từ nút "Phân việc": điền summary + form action + giá trị select (đã điều phối thì tự chọn)
    if (modalPhanCong) {
        modalPhanCong.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-phan-viec')) return;
            formPhanCong.action = btn.getAttribute('data-url') || '';
            document.getElementById('pv-ten-khach-hang').value = btn.getAttribute('data-ten-khach-hang') || '—';
            document.getElementById('pv-dia-diem').value = btn.getAttribute('data-dia-diem') || '';
            if (window.setAdminDateTimeInput && window.setAdminDateInput) {
                setAdminDateTimeInput('pv-ngay-chup', btn.getAttribute('data-ngay-chup') || '');
                setAdminDateInput('pv-ngay-hen-tra-hang', btn.getAttribute('data-ngay-hen-tra-hang') || '');
                setAdminDateInput('pv-ngay-tra-link-in', btn.getAttribute('data-ngay-tra-link-in') || '');
            } else {
                document.getElementById('pv-ngay-chup').value = btn.getAttribute('data-ngay-chup') || '';
                document.getElementById('pv-ngay-hen-tra-hang').value = btn.getAttribute('data-ngay-hen-tra-hang') || '';
                document.getElementById('pv-ngay-tra-link-in').value = btn.getAttribute('data-ngay-tra-link-in') || '';
            }
            // document.getElementById('pv-trang-phuc').value = btn.getAttribute('data-trang-phuc') || '';
            document.getElementById('pv-tong-tien').value = btn.getAttribute('data-tong-tien') || '—';
            document.getElementById('pv-trang-thai-chup').value = btn.getAttribute('data-trang-thai-chup') || '—';
            document.getElementById('pv-trang-thai-edit').value = btn.getAttribute('data-trang-thai-edit') || '—';
        });
        modalPhanCong.addEventListener('shown.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('btn-phan-viec')) return;
            var thoChupId = (btn.getAttribute('data-tho-chup-id') || '').toString();
            var thoMakeId = (btn.getAttribute('data-tho-make-id') || '').toString();
            var thoEditId = (btn.getAttribute('data-tho-edit-id') || '').toString();
            if ($ && $.fn.select2) {
                $('#phan_cong_tho_chup_id').val(thoChupId || null).trigger('change');
                $('#phan_cong_tho_make_id').val(thoMakeId || null).trigger('change');
                $('#phan_cong_tho_edit_id').val(thoEditId || null).trigger('change');
            }
        });
    }
});
</script>
@endpush
@endsection
