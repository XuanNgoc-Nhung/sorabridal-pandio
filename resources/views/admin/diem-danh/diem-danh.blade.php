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
    @if($showChuaDangKyCaLam ?? false)
    <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">
        Bạn chưa được phân ca làm việc hôm nay. Vui lòng liên hệ quản lý để được phân ca trước khi điểm danh.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
    @endif
    <div id="diemDanhAlert" class="alert alert-dismissible fade show d-none mb-0" role="alert">
        <span id="diemDanhAlertMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>

    <div class="card mb-0">
        <div class="card-body">
        {{-- Bộ lọc --}}
        <form action="{{ route('admin.diem-danh.diem-danh') }}" method="GET">
            <div class="row g-3 align-items-end admin-filter-row">
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="tu_ngay">Từ ngày</label>
                    <input type="text" class="flatpickr-date-admin form-control" id="tu_ngay" name="tu_ngay" value="{{ request('tu_ngay') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label" for="den_ngay">Đến ngày</label>
                    <input type="text" class="flatpickr-date-admin form-control" id="den_ngay" name="den_ngay" value="{{ request('den_ngay') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-6 col-md-3 col-lg-2 d-flex flex-wrap gap-2 align-items-end admin-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Lọc
                    </button>
                    @if(request('tu_ngay') || request('den_ngay'))
                    <a href="{{ route('admin.diem-danh.diem-danh') }}" class="btn btn-outline-secondary">Bỏ lọc</a>
                    @endif
                </div>
            </div>
        </form>
        </div>
    </div>

    <div class="card mb-0">
        <h5 class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Danh sách điểm danh</span>
            <span class="d-flex flex-wrap align-items-center gap-2">
                @if($canCheckIn ?? false)
                    <button type="button"
                            id="btnCheckIn"
                            class="btn btn-success btn-sm"
                            data-url="{{ route('admin.diem-danh.check-in') }}"
                            data-co-ca-lam="{{ ($coDangKyCaLamHomNay ?? false) ? '1' : '0' }}">
                        <i class="fa-solid fa-sign-in-alt me-1"></i> Check in
                    </button>
                @elseif($canCheckOut ?? false)
                    <button type="button" id="btnCheckOut" class="btn btn-warning btn-sm text-dark" data-url="{{ route('admin.diem-danh.check-out') }}">
                        <i class="fa-solid fa-sign-out-alt me-1"></i> Check out
                    </button>
                @endif
                <a href="{{ route('admin.diem-danh.cham-cong') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-clock me-1"></i> Chấm công
                </a>
            </span>
        </h5>
        <div class="card-body">
        <div class="table-responsive text-nowrap table-wrapper-bordered diem-danh-table-wrap">
            <table class="table table-hover table-bordered mb-0 diem-danh-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th class="diem-danh-sticky diem-danh-sticky-ho-ten" style="min-width: 160px;">Họ tên</th>
                        <th>Ca làm</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th class="text-center">Đi muộn</th>
                        <th class="text-center">Về sớm</th>
                        <th class="text-end">Phạt đi muộn</th>
                        <th class="text-end">Phạt về sớm</th>
                        {{-- <th class="text-center">Hợp lệ</th> --}}
                        <th>Lý do</th>
                        {{-- <th class="text-center">Nghỉ phép</th> --}}
                        {{-- <th>Loại phép</th> --}}
                        <th class="text-end">Giờ làm cơ bản</th>
                        <th class="text-end">Giờ làm tăng ca</th>
                        <th class="text-end">Lương cơ bản</th>
                        <th class="text-end">Lương tăng ca</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($danhSach ?? [] as $index => $item)
                    <tr>
                        <td class="text-center">{{ ($danhSach->currentPage() - 1) * $danhSach->perPage() + $index + 1 }}</td>
                        <td class="diem-danh-sticky diem-danh-sticky-ho-ten"><span class="fw-medium">{{ $item->user?->name ?? '—' }}</span></td>
                        <td>
                            @if($item->caLamHomDo ?? null)
                                <span>{{ $item->caLamHomDo->ten_ca }}</span>
                                <span class="text-muted small">({{ $item->caLamHomDo->gioBatDauHienThi() }} – {{ $item->caLamHomDo->gioKetThucHienThi() }})</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $item->gio_vao ? $item->gio_vao->format('d/m/Y H:i') : '—' }}</td>
                        <td>{{ $item->gio_ra ? $item->gio_ra->format('d/m/Y H:i') : '—' }}</td>
                        <td class="text-center">
                            @if(($item->thoi_gian_di_muon ?? 0) > 0)
                                <span class="badge bg-warning">{{ $item->thoi_gian_di_muon }} phút</span>
                            @else
                                <span class="text-muted">Không</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(($item->thoi_gian_ve_som ?? 0) > 0)
                                <span class="badge bg-info">{{ $item->thoi_gian_ve_som }} phút</span>
                            @else
                                <span class="text-muted">Không</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if(($item->tien_phat_di_muon ?? 0) > 0)
                                <span class="text-danger">{{ number_format($item->tien_phat_di_muon, 0, ',', '.') }} đ</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if(($item->tien_phat_ve_som ?? 0) > 0)
                                <span class="text-danger">{{ number_format($item->tien_phat_ve_som, 0, ',', '.') }} đ</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        {{-- <td class="text-center">
                            @if($item->hop_le)
                                <span class="badge bg-success">Có</span>
                            @else
                                <span class="text-muted">Không</span>
                            @endif
                        </td> --}}
                        <td>{{ $item->ly_do ?? '—' }}</td>
                        {{-- <td class="text-center">
                            @if($item->nghi_phep)
                                <span class="badge bg-info">Có</span>
                            @else
                                <span class="text-muted">Không</span>
                            @endif
                        </td> --}}
                        {{-- <td>{{ $item->loai_phep ?? '—' }}</td> --}}
                        <td class="text-end">{{ $item->gio_lam_co_ban !== null ? number_format($item->gio_lam_co_ban, 1) . ' h' : '—' }}</td>
                        <td class="text-end">{{ $item->gio_lam_tang_ca !== null ? number_format($item->gio_lam_tang_ca, 1) . ' h' : '—' }}</td>
                        <td class="text-end">{{ $item->luong_co_ban !== null ? number_format($item->luong_co_ban, 0, ',', '.') : '—' }}</td>
                        <td class="text-end">{{ $item->luong_tang_ca !== null ? number_format($item->luong_tang_ca, 0, ',', '.') : '—' }}</td>
                        <td>{{ Str::limit($item->ghi_chu, 50) ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center py-4 text-muted">Chưa có dữ liệu điểm danh.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$danhSach ?? null" label="bản ghi điểm danh" />
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
.diem-danh-table .diem-danh-sticky {
    position: sticky;
    left: 0;
    z-index: 2;
    background-color: #fff;
    background-clip: padding-box;
}
.diem-danh-table thead .diem-danh-sticky {
    z-index: 6;
    background-color: #f8f9fa;
}
.diem-danh-table.table-hover > tbody > tr:hover > .diem-danh-sticky {
    background-color: #f5f5f9;
}
[data-bs-theme='dark'] .diem-danh-table .diem-danh-sticky {
    background-color: #2f3349;
}
[data-bs-theme='dark'] .diem-danh-table thead .diem-danh-sticky {
    background-color: #353a52;
}
[data-bs-theme='dark'] .diem-danh-table.table-hover > tbody > tr:hover > .diem-danh-sticky {
    background-color: #3a3f5c;
}
.diem-danh-table .diem-danh-sticky-ho-ten {
    min-width: 160px;
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.08);
}
[data-bs-theme='dark'] .diem-danh-table .diem-danh-sticky-ho-ten {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.35);
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    var btnCheckIn = document.getElementById('btnCheckIn');
    var btnCheckOut = document.getElementById('btnCheckOut');
    if (!btnCheckIn && !btnCheckOut) return;

    var alertBox = document.getElementById('diemDanhAlert');
    var alertMessage = document.getElementById('diemDanhAlertMessage');
    var csrfToken = @json(csrf_token());
    var clientIpApiUrl = 'https://ipv4.geojs.io/v1/ip.json';

    function showAlert(type, message) {
        if (!alertBox || !alertMessage) return;
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        alertMessage.textContent = message || '';
    }

    function fetchClientPublicIpFromUrl(url) {
        return fetch(url, {
            method: 'GET',
            headers: { Accept: 'application/json' }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('ip_api_status_' + response.status);
                }
                return response.json();
            })
            .then(function (payload) {
                var ip = payload && typeof payload.ip === 'string' ? payload.ip.trim() : '';
                if (!ip || !/^(?:\d{1,3}\.){3}\d{1,3}$/.test(ip)) {
                    throw new Error('ip_api_not_ipv4');
                }
                return ip;
            });
    }

    function fetchClientPublicIp() {
        return fetchClientPublicIpFromUrl(clientIpApiUrl);
    }

    function submitDiemDanh(btn, options) {
        var url = btn.getAttribute('data-url');
        if (!url) return;

        btn.disabled = true;
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ' + (options.loadingText || 'Đang xử lý...');

        function resetButton() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }

        fetchClientPublicIp()
            .then(function (clientIp) {
                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('client_ip', clientIp);

                return RestApi.post(url, formData);
            })
            .then(function (result) {
                if (options.expectJson) {
                    var body = result.data || {};
                    if (result.ok && body.success) {
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 800);
                        return;
                    }

                    resetButton();
                    return;
                }

                if (result.ok) {
                    window.location.reload();
                    return;
                }

                resetButton();
            })
            .catch(function () {
                showAlert('error', 'Không xác minh được mạng hiện tại. Thử lại sau hoặc liên hệ quản trị.');
                resetButton();
            });
    }

    if (btnCheckIn) {
        btnCheckIn.addEventListener('click', function () {
            // if (btnCheckIn.getAttribute('data-co-ca-lam') !== '1') {
            //     showAlert('error', 'Bạn chưa được phân ca làm việc hôm nay. Không thể điểm danh.');
            //     return;
            // }

            submitDiemDanh(btnCheckIn, {
                loadingText: 'Đang check in...',
                expectJson: true,
                successFallback: 'Check-in thành công.',
                errorFallback: 'Không thể check-in. Vui lòng thử lại.'
            });
        });
    }

    if (btnCheckOut) {
        btnCheckOut.addEventListener('click', function () {
            submitDiemDanh(btnCheckOut, {
                loadingText: 'Đang check out...',
                expectJson: true,
                successFallback: 'Check-out thành công.',
                errorFallback: 'Không thể check-out. Vui lòng thử lại.'
            });
        });
    }
})();
</script>
@endpush
@endsection
