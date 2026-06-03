@extends('admin.layouts.app')

@section('content')
@if(empty($nhanVienId) && empty($isAdmin))
    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning mb-0">
                Tài khoản của bạn chưa có hồ sơ nhân viên nên chưa thể lọc theo thợ chụp/make/edit.
            </div>
        </div>
    </div>
@else
    <div class="card app-calendar-wrapper" id="ws-lich-lam-viec">
        <div class="card shadow-none border-0">
            <div class="card-body pb-0">
                @if(!empty($locTienDoFilters) && is_array($locTienDoFilters))
                    <div class="ws-lich-filters border-bottom pb-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div class="text-muted small mb-0">Lọc theo trạng thái hợp đồng</div>
                            <button type="button" class="btn btn-sm btn-label-secondary d-none" id="wsLichLocClear">Bỏ chọn lọc</button>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($locTienDoFilters as $locKey => $locItem)
                                @if(!empty($locItem['label']))
                                    <div class="form-check mb-0">
                                        <input class="form-check-input ws-lich-loc-filter" type="checkbox"
                                               value="{{ $locKey }}" id="ws_lich_loc_{{ $locKey }}">
                                        <label class="form-check-label" for="ws_lich_loc_{{ $locKey }}">{{ $locItem['label'] }}</label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        {{-- <div class="form-text mt-2 mb-0">Không chọn = hiển thị tất cả hợp đồng (trừ nháp). Chọn nhiều = hiển thị HĐ thỏa ít nhất một điều kiện.</div> --}}
                    </div>
                @endif
                <div id="calendar" class="admin-work-calendar"></div>
                <div id="ws-lich-mobile-month" class="ws-lich-mobile-month d-none" aria-label="Lịch tháng/tuần (mobile)">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm ws-lich-mobile-month-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="ws-lich-mobile-month__col-thu">Thứ</th>
                                    <th scope="col" class="ws-lich-mobile-month__col-ngay">Ngày</th>
                                    <th scope="col" class="ws-lich-mobile-month__col-work">Công việc</th>
                                </tr>
                            </thead>
                            <tbody id="wsLichMobileMonthBody"></tbody>
                        </table>
                    </div>
                </div>
                <div id="ws-lich-list-view" class="ws-lich-list-view d-none" aria-label="Danh sách lịch làm việc">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="text-muted small mb-0" id="wsLichListSummary">Đang tải...</div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 small text-muted" for="wsLichListPerPage">Hiển thị</label>
                            <select class="form-select form-select-sm" id="wsLichListPerPage" style="width: auto; min-width: 4.5rem;">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="50">50</option>
                            </select>
                            <span class="small text-muted">hợp đồng / trang</span>
                        </div>
                    </div>
                    <div id="wsLichListBody" class="ws-lich-list-body">
                        <div class="text-muted small py-4 text-center">Đang tải...</div>
                    </div>
                    <nav class="mt-3" id="wsLichListPagination" aria-label="Phân trang danh sách lịch"></nav>
                </div>
                @if(!empty($tienDoLegend) && is_array($tienDoLegend))
                    <div class="ws-lich-legend border-top pt-3 mt-3 pb-3" aria-label="Chú thích màu tiến độ hợp đồng">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div class="text-muted small mb-0">Chú thích tiến độ hợp đồng <span class="text-muted">(chỉ đổi màu viền trái trên lịch; nền theo config)</span></div>
                            <button type="button" class="btn btn-link btn-sm p-0 text-muted ws-lich-legend__reset" id="wsLichLegendResetColors">Khôi phục màu mặc định</button>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($tienDoLegend as $key => $item)
                                @if(!empty($item['label']))
                                    <div class="ws-lich-legend__item">
                                        <input type="color"
                                               class="ws-lich-legend__swatch ws-day-contract--{{ $key }}"
                                               data-tien-do="{{ $key }}"
                                               value="{{ $item['border'] ?? '#7367f0' }}"
                                               title="Màu viền trái — {{ $item['label'] }}"
                                               aria-label="Màu viền trái {{ $item['label'] }}">
                                        <span class="ws-lich-legend__label">{{ $item['label'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
@endsection

@push('modals')
<div class="modal fade" id="wsWorkDayModal" tabindex="-1" aria-labelledby="wsWorkDayModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wsWorkDayModalTitle">Chi tiết công việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="wsWorkDayModalBody">
                <div class="text-muted small">Đang tải...</div>
            </div>
            <div class="modal-footer">
                @if(!empty($isAdmin))
                <button type="button" class="btn btn-primary" id="wsWorkDayAddBtn">
                    <i class="icon-base ti tabler-plus icon-sm me-1"></i> Thêm lịch
                </button>
                @endif
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="wsAddWorkModal" tabindex="-1" aria-labelledby="wsAddWorkModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wsAddWorkModalTitle">Thêm lịch làm việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="wsAddWorkForm">
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="wsAddWorkError"></div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-5 col-lg-3 ws-add-work-field-hop-dong">
                            <label class="form-label" for="wsAddWorkHopDong">Hợp đồng</label>
                            <select class="select2-admin form-select" id="wsAddWorkHopDong" name="hop_dong_id" required data-placeholder="Chọn hợp đồng" style="width: 100%;">
                                <option value="">— Chọn hợp đồng —</option>
                            </select>
                            <div class="form-text">Chỉ hiển thị HĐ chưa điều phối.</div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-5 col-lg-3 ws-add-work-field-ngay-chup">
                            <label class="form-label" for="wsAddWorkNgayChup">Ngày chụp chính thức</label>
                            <input type="hidden" name="ngay_chup_thuc_te" id="wsAddWorkNgayChupHidden" value="">
                            <input type="text" class="form-control" id="wsAddWorkNgayChup" disabled placeholder="dd/mm/yyyy" autocomplete="off" aria-describedby="wsAddWorkNgayChupHint">
                            <div class="form-text" id="wsAddWorkNgayChupHint">Theo ngày đã chọn trên lịch.</div>
                        </div>

                        <div id="wsAddWorkDieuPhoiFields" class="col-12 d-none">
                            <div class="border rounded p-3">
                                <div class="fw-semibold mb-3" id="wsAddWorkHopDongTitle">Thông tin điều phối</div>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkGioChup">Giờ chụp</label>
                                        <input type="text" class="form-control flatpickr-time-admin" id="wsAddWorkGioChup" name="gio_chup" placeholder="HH:mm" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkNgayCuoi">Ngày cưới chính thức</label>
                                        <input type="text" class="form-control flatpickr-date-admin" id="wsAddWorkNgayCuoi" name="ngay_cuoi_chinh_thuc" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkNgayTraDemo">Ngày trả link demo chính thức</label>
                                        <input type="text" class="form-control flatpickr-date-admin" id="wsAddWorkNgayTraDemo" name="ngay_tra_link_demo_chinh_thuc" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkNgayTraIn">Ngày trả link in chính thức</label>
                                        <input type="text" class="form-control flatpickr-date-admin" id="wsAddWorkNgayTraIn" name="ngay_tra_link_in_chinh_thuc" placeholder="dd/mm/yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-6">
                                        <label class="form-label" for="wsAddWorkDiaDiem">Địa điểm chụp</label>
                                        <input type="text" class="form-control" id="wsAddWorkDiaDiem" name="dia_diem_chup" placeholder="Nhập địa điểm chụp">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkThoChup">Người chụp</label>
                                        <select class="select2-admin form-select" id="wsAddWorkThoChup" name="tho_chup_id" data-placeholder="Chọn người chụp" style="width: 100%;" disabled>
                                            <option value="">—</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkThoMake">Người make</label>
                                        <select class="select2-admin form-select" id="wsAddWorkThoMake" name="tho_make_id" data-placeholder="Chọn người make" style="width: 100%;" disabled>
                                            <option value="">—</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label class="form-label" for="wsAddWorkThoEdit">Người edit</label>
                                        <select class="select2-admin form-select" id="wsAddWorkThoEdit" name="tho_edit_id" data-placeholder="Chọn người edit" style="width: 100%;">
                                            <option value="">—</option>
                                            @foreach($danhSachNhanVien ?? [] as $nv)
                                                <option value="{{ $nv->id }}">{{ $nv->user?->name ?? ('Nhân viên #'.$nv->id) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="wsAddWorkGhiChuSale">Ghi chú (sale)</label>
                                        <textarea class="form-control" id="wsAddWorkGhiChuSale" name="ghi_chu_sale" rows="4" placeholder="Theo cột ghi chú sale…"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="wsAddWorkSubmitBtn">
                        <i class="icon-base ti tabler-plus icon-sm me-1"></i> Tạo lịch
                    </button>
                </div>
                @csrf
            </form>
        </div>
    </div>
</div>

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
                <button type="button" class="btn btn-primary" id="btnTaiBriefPng" title="Tải xuống" aria-label="Tải xuống">
                    <i class="icon-base ti tabler-download icon-sm me-1" aria-hidden="true"></i> Tải xuống
                </button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin-lich-lam-viec.css') }}" />
@endpush

@push('scripts')
@if(!empty($nhanVienId) || !empty($isAdmin))
    @php
        $trangThaiHopDongClass = [
            'nhap' => 'bg-label-secondary',
            'da_huy' => 'bg-label-danger',
            'dang_thuc_hien' => 'bg-label-primary',
            'tre_chup' => 'bg-label-warning',
            'tre_edit' => 'bg-label-warning',
        ];
        $tienDoColorDefaults = [];
        foreach (is_array($tienDoLegend ?? null) ? $tienDoLegend : [] as $tdKey => $tdItem) {
            $tienDoColorDefaults[$tdKey] = [
                'bg' => $tdItem['bg'] ?? '',
                'border' => $tdItem['border'] ?? '',
            ];
        }
    @endphp
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var root = document.getElementById('ws-lich-lam-viec');
            var calendarEl = document.getElementById('calendar');
            if (!root || !calendarEl) return;

            var Calendar = window.Calendar;
            var dayGridPlugin = window.dayGridPlugin;
            var interactionPlugin = window.interactionPlugin;
            if (!Calendar || !dayGridPlugin || !interactionPlugin) return;

            var listViewEl = document.getElementById('ws-lich-list-view');
            var mobileMonthEl = document.getElementById('ws-lich-mobile-month');
            var mobileMonthBodyEl = document.getElementById('wsLichMobileMonthBody');
            var mobileMonthMq = window.matchMedia('(max-width: 767.98px)');
            var listBodyEl = document.getElementById('wsLichListBody');
            var listPaginationEl = document.getElementById('wsLichListPagination');
            var listSummaryEl = document.getElementById('wsLichListSummary');
            var listPerPageEl = document.getElementById('wsLichListPerPage');
            var listModeActive = false;
            var listCurrentPage = 1;
            var calendarViewBeforeList = 'dayGridMonth';
            var listDanhSachUrl = @json(route('admin.lich-lam-viec.danh-sach'));
            var trangThaiHopDongClass = @json($trangThaiHopDongClass);

            var direction = (typeof isRtl !== 'undefined' && isRtl) ? 'rtl' : 'ltr';

            function selectedFilters() {
                return ['chup', 'make', 'edit'];
            }

            function selectedLocFilters() {
                var boxes = root.querySelectorAll('.ws-lich-loc-filter:checked');
                var keys = [];
                boxes.forEach(function (el) {
                    var v = String(el.value || '').trim();
                    if (v) keys.push(v);
                });
                return keys;
            }

            function appendLocParams(params) {
                selectedLocFilters().forEach(function (key) {
                    params.append('loc[]', key);
                });
            }

            function updateLocClearButton() {
                var btn = document.getElementById('wsLichLocClear');
                if (!btn) return;
                if (selectedLocFilters().length) {
                    btn.classList.remove('d-none');
                } else {
                    btn.classList.add('d-none');
                }
            }

            function buildBadgesDom(badges) {
                var filters = selectedFilters();
                var roleKey = { C: 'chup', M: 'make', E: 'edit' };
                var filtered = (badges || []).filter(function (b) {
                    var code = String(b.code || '').toUpperCase();
                    var key = roleKey[code];
                    return key && filters.indexOf(key) !== -1;
                });
                if (!filtered.length) return null;
                var wrap = document.createElement('div');
                wrap.className = 'ws-day-badges';
                filtered.forEach(function (b) {
                    var code = String(b.code || '').toUpperCase();
                    var row = document.createElement('div');
                    row.className = 'ws-role-tag ws-role-tag-' + code.toLowerCase();
                    row.textContent = code + ': ' + String(b.name || '');
                    wrap.appendChild(row);
                });
                return wrap;
            }

            function matchesSelectedRole(contractItem) {
                var filters = selectedFilters();
                if (!filters.length) return false;
                var roles = Array.isArray(contractItem && contractItem.roles) ? contractItem.roles : [];
                if (!roles.length) return true;
                return roles.some(function (role) {
                    return filters.indexOf(String(role || '').toLowerCase()) !== -1;
                });
            }

            function buildContractPeopleText(contractItem, includeEdit) {
                var phanCong = (contractItem && contractItem.phan_cong) ? contractItem.phan_cong : {};
                var names = [];
                var chup = String(phanCong.chup || '').trim();
                var make = String(phanCong.make || '').trim();
                var edit = String(phanCong.edit || '').trim();
                if (chup) names.push(chup);
                if (make) names.push(make);
                if (includeEdit && edit) names.push(edit);
                return names.join('.');
            }

            function buildContractCoupleShortText(contractItem) {
                var short = String(contractItem && contractItem.couple_short || '').trim();
                if (short) return short;
                return String(contractItem && contractItem.couple || '').trim();
            }

            function buildContractTimeCoupleText(contractItem) {
                var timeText = String(contractItem && contractItem.time || '').trim() || '--:--';
                var coupleShort = buildContractCoupleShortText(contractItem);
                return coupleShort ? (timeText + ': ' + coupleShort) : timeText;
            }

            var tienDoKeys = @json(array_keys(is_array($tienDoLegend ?? null) ? $tienDoLegend : []));
            var tienDoColorDefaults = @json($tienDoColorDefaults);
            var TIEN_DO_COLORS_LS = 'ws-lich-lam-viec-tien-do-colors';

            function normalizeHexColor(value) {
                var s = String(value || '').trim();
                if (!/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(s)) return '';
                if (s.length === 4) {
                    return '#' + s[1] + s[1] + s[2] + s[2] + s[3] + s[3];
                }
                return s.toLowerCase();
            }

            function isDarkBsTheme() {
                return document.documentElement.getAttribute('data-bs-theme') === 'dark';
            }

            function tienDoCssVarName(key, role) {
                return '--ws-td-' + key + '-' + role;
            }

            function readTienDoColorsFromStorage() {
                try {
                    var raw = localStorage.getItem(TIEN_DO_COLORS_LS);
                    if (!raw) return null;
                    var parsed = JSON.parse(raw);
                    return (parsed && typeof parsed === 'object') ? parsed : null;
                } catch (e) {
                    return null;
                }
            }

            function writeTienDoColorsToStorage(colorsByKey) {
                try {
                    localStorage.setItem(TIEN_DO_COLORS_LS, JSON.stringify(colorsByKey));
                } catch (e) { /* ignore */ }
            }

            function tienDoDefaultBorder(key) {
                var defaults = (tienDoColorDefaults && tienDoColorDefaults[key]) ? tienDoColorDefaults[key] : {};
                return normalizeHexColor(defaults.border) || '';
            }

            function getCustomTienDoBorder(key) {
                var stored = readTienDoColorsFromStorage();
                if (!stored || !stored[key] || typeof stored[key] !== 'object') return '';
                return normalizeHexColor(stored[key].border) || '';
            }

            function resolveTienDoBorderForLegend(key) {
                return getCustomTienDoBorder(key) || tienDoDefaultBorder(key);
            }

            function getTienDoColorScopes() {
                var scopes = [];
                if (root) scopes.push(root);
                if (document.documentElement) scopes.push(document.documentElement);
                return scopes;
            }

            function paintTienDoContractElement(el, key) {
                if (!el || !key) return;
                var customBorder = getCustomTienDoBorder(key);
                el.style.removeProperty('background');
                if (customBorder) {
                    el.style.setProperty('border-left-color', customBorder);
                } else {
                    el.style.removeProperty('border-left-color');
                }
            }

            function clearTienDoContractElementPaint(el) {
                if (!el) return;
                el.style.removeProperty('border-left-color');
            }

            function syncTienDoDynamicStylesheet() {
                var styleEl = document.getElementById('wsLichTienDoCustomColors');
                if (!styleEl) {
                    styleEl = document.createElement('style');
                    styleEl.id = 'wsLichTienDoCustomColors';
                    document.head.appendChild(styleEl);
                }
                var rules = [];
                tienDoKeys.forEach(function (key) {
                    var customBorder = getCustomTienDoBorder(key);
                    if (!customBorder) return;
                    var selector = [
                        '#ws-lich-lam-viec .admin-work-calendar .ws-day-contract.ws-day-contract--' + key,
                        '#ws-lich-lam-viec .ws-lich-mobile-month__work .ws-day-contract.ws-day-contract--' + key
                    ].join(',');
                    rules.push(selector + '{border-left-color:' + customBorder + ' !important}');
                });
                styleEl.textContent = rules.join('\n');
            }

            function paintAllTienDoContractElements() {
                if (!root) return;
                tienDoKeys.forEach(function (key) {
                    root.querySelectorAll('.ws-day-contract.ws-day-contract--' + key).forEach(function (el) {
                        paintTienDoContractElement(el, key);
                    });
                });
            }

            function applyTienDoColors() {
                var scopes = getTienDoColorScopes();
                if (!scopes.length) return;
                tienDoKeys.forEach(function (key) {
                    var customBorder = getCustomTienDoBorder(key);
                    var borderVar = tienDoCssVarName(key, 'border');
                    var bgVar = tienDoCssVarName(key, 'bg');
                    scopes.forEach(function (scope) {
                        scope.style.removeProperty(bgVar);
                        if (customBorder) {
                            scope.style.setProperty(borderVar, customBorder);
                        } else {
                            scope.style.removeProperty(borderVar);
                        }
                    });
                });
                syncTienDoDynamicStylesheet();
                paintAllTienDoContractElements();
            }

            function syncTienDoColorInputs() {
                root.querySelectorAll('input.ws-lich-legend__swatch[data-tien-do]').forEach(function (input) {
                    var key = String(input.getAttribute('data-tien-do') || '').trim();
                    if (!key) return;
                    var border = resolveTienDoBorderForLegend(key);
                    if (border) input.value = border;
                });
            }

            function saveTienDoBorderColor(key, value) {
                var hex = normalizeHexColor(value);
                if (!hex) return;
                var stored = readTienDoColorsFromStorage() || {};
                if (!stored[key] || typeof stored[key] !== 'object') stored[key] = {};
                stored[key].border = hex;
                delete stored[key].bg;
                writeTienDoColorsToStorage(stored);
                applyTienDoColors();
            }

            function resetTienDoColors() {
                try {
                    localStorage.removeItem(TIEN_DO_COLORS_LS);
                } catch (e) { /* ignore */ }
                getTienDoColorScopes().forEach(function (scope) {
                    tienDoKeys.forEach(function (key) {
                        scope.style.removeProperty(tienDoCssVarName(key, 'bg'));
                        scope.style.removeProperty(tienDoCssVarName(key, 'border'));
                    });
                });
                var styleEl = document.getElementById('wsLichTienDoCustomColors');
                if (styleEl) styleEl.textContent = '';
                if (root) {
                    root.querySelectorAll('.ws-day-contract[class*="ws-day-contract--"]').forEach(clearTienDoContractElementPaint);
                }
                syncTienDoColorInputs();
                applyTienDoColors();
            }

            function migrateTienDoColorStorage() {
                var stored = readTienDoColorsFromStorage();
                if (!stored) return;
                var dirty = false;
                Object.keys(stored).forEach(function (storageKey) {
                    if (stored[storageKey] && stored[storageKey].bg) {
                        delete stored[storageKey].bg;
                        dirty = true;
                    }
                });
                if (dirty) writeTienDoColorsToStorage(stored);
            }

            function initTienDoColorCustomization() {
                migrateTienDoColorStorage();
                applyTienDoColors();
                syncTienDoColorInputs();

                function onLegendSwatchColorChange(input) {
                    var key = String(input.getAttribute('data-tien-do') || '').trim();
                    if (!key) return;
                    saveTienDoBorderColor(key, input.value);
                }

                root.querySelectorAll('input.ws-lich-legend__swatch[data-tien-do]').forEach(function (input) {
                    input.addEventListener('input', function () { onLegendSwatchColorChange(input); });
                    input.addEventListener('change', function () { onLegendSwatchColorChange(input); });
                });

                var resetBtn = document.getElementById('wsLichLegendResetColors');
                if (resetBtn) {
                    resetBtn.addEventListener('click', resetTienDoColors);
                }

                var themeObserver = new MutationObserver(function () {
                    applyTienDoColors();
                });
                themeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['data-bs-theme'],
                });
            }

            initTienDoColorCustomization();

            function contractTienDoKey(contractItem) {
                var key = String(contractItem && contractItem.tien_do || '').trim();
                return (key && tienDoKeys.indexOf(key) !== -1) ? key : '';
            }

            function contractTienDoClass(contractItem) {
                var key = contractTienDoKey(contractItem);
                return key ? ('ws-day-contract--' + key) : '';
            }

            function workDetailCardClass(contractItem) {
                var key = contractTienDoKey(contractItem);
                return key ? ('ws-work-detail-card ws-work-detail-card--' + key) : 'ws-work-detail-card';
            }

            function contractTyLeThanhToan(contractItem) {
                var pct = parseInt(contractItem && contractItem.ty_le_thanh_toan, 10);
                if (isNaN(pct)) return 0;
                return Math.max(0, Math.min(100, pct));
            }

            function buildPaymentBadgeHtml(contractItem) {
                var pct = contractTyLeThanhToan(contractItem);
                var label = 'Thanh toán ' + pct + '%';
                if (pct === 100) {
                    return '<span class="ws-day-contract__pay ws-day-contract__pay--full" aria-label="' + escapeHtml(label) + '" title="' + escapeHtml(label) + '">' +
                        '<i class="icon-base ti tabler-circle-check" aria-hidden="true"></i></span>';
                }
                if (pct === 0) {
                    return '<span class="ws-day-contract__pay ws-day-contract__pay--zero" aria-label="' + escapeHtml(label) + '" title="' + escapeHtml(label) + '">' +
                        '<i class="icon-base ti tabler-check" aria-hidden="true"></i></span>';
                }
                return '<span class="ws-day-contract__pay ws-day-contract__pay--partial" aria-label="' + escapeHtml(label) + '" title="' + escapeHtml(label) + '">' +
                    escapeHtml(String(pct) + '%') + '</span>';
            }

            function appendPaymentBadge(container, contractItem) {
                if (!container) return;
                container.insertAdjacentHTML('beforeend', buildPaymentBadgeHtml(contractItem));
            }

            function buildContractSummaryText(contractItem) {
                var parts = [];
                var goiChup = String(contractItem && contractItem.goi_chup || '').trim();
                var diaDiem = String(contractItem && contractItem.dia_diem || '').trim();
                var ghiChu = String(contractItem && contractItem.ghi_chu || '').trim();
                var maHopDong = String(contractItem && contractItem.ma_hop_dong || '').trim();

                if (goiChup) parts.push(goiChup);
                if (diaDiem) parts.push(diaDiem);
                if (ghiChu) parts.push(ghiChu);
                if (!parts.length && maHopDong) parts.push(maHopDong);

                return parts.join(' - ');
            }

            function isNhapContract(contractItem) {
                return String(contractItem && contractItem.trang_thai_hop_dong || '').toLowerCase() === 'nhap';
            }

            function filterVisibleContracts(contracts) {
                return (contracts || []).filter(function (item) {
                    return !isNhapContract(item) && matchesSelectedRole(item);
                });
            }

            function buildDayContractsDom(contracts, maxVisible) {
                var filtered = filterVisibleContracts(contracts);
                if (!filtered.length) return null;

                var limit = (maxVisible > 0) ? maxVisible : filtered.length;
                var visible = filtered.slice(0, limit);

                var wrap = document.createElement('div');
                wrap.className = 'ws-day-contracts';

                visible.forEach(function (item) {
                    var row = document.createElement('div');
                    var tdClass = contractTienDoClass(item);
                    row.className = 'ws-day-contract' + (tdClass ? (' ' + tdClass) : '');
                    if (item && item.tien_do_label) {
                        row.title = String(item.tien_do_label);
                    }

                    var meta = document.createElement('div');
                    meta.className = 'ws-day-contract__meta';
                    meta.textContent = buildContractTimeCoupleText(item);

                    var summary = document.createElement('div');
                    summary.className = 'ws-day-contract__summary';
                    var peopleText = buildContractPeopleText(item, false);
                    var summaryText = buildContractSummaryText(item);
                    summary.textContent = [peopleText, summaryText].filter(function (part) {
                        return String(part || '').trim() !== '';
                    }).join(' · ');

                    row.appendChild(meta);
                    row.appendChild(summary);
                    appendPaymentBadge(row, item);
                    paintTienDoContractElement(row, contractTienDoKey(item));
                    wrap.appendChild(row);
                });

                if (filtered.length > limit) {
                    var more = document.createElement('div');
                    more.className = 'ws-day-contract__more';
                    more.textContent = '+' + String(filtered.length - limit);
                    wrap.appendChild(more);
                }

                return wrap;
            }

            var weekDayLabels = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

            function formatDateKey(date) {
                var y = date.getFullYear();
                var m = String(date.getMonth() + 1).padStart(2, '0');
                var d = String(date.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + d;
            }

            function shouldUseMobileMonthLayout() {
                return mobileMonthMq.matches
                    && !listModeActive
                    && calendar
                    && calendar.view
                    && (calendar.view.type === 'dayGridMonth' || calendar.view.type === 'dayGridWeek');
            }

            function getContractsForDate(dateStr) {
                if (!calendar) return [];
                var events = calendar.getEvents();
                for (var i = 0; i < events.length; i++) {
                    var ev = events[i];
                    var start = ev.start;
                    if (!start) continue;
                    if (formatDateKey(start) === dateStr) {
                        return (ev.extendedProps || {}).contracts || [];
                    }
                }
                return [];
            }

            function renderMobileMonthTable() {
                if (!mobileMonthBodyEl || !mobileMonthEl || !calendar || !calendar.view) return;

                var view = calendar.view;
                var start = new Date(view.activeStart);
                var end = new Date(view.activeEnd);
                var isMonthView = view.type === 'dayGridMonth';
                var monthRef = view.currentStart ? new Date(view.currentStart) : start;
                var displayMonth = monthRef.getMonth();
                var displayYear = monthRef.getFullYear();

                mobileMonthBodyEl.innerHTML = '';
                var cursor = new Date(start);

                while (cursor < end) {
                    var dateKey = formatDateKey(cursor);
                    var thu = weekDayLabels[cursor.getDay()] || '';
                    var dd = String(cursor.getDate()).padStart(2, '0');
                    var mm = String(cursor.getMonth() + 1).padStart(2, '0');
                    var isOtherMonth = isMonthView
                        && (cursor.getMonth() !== displayMonth || cursor.getFullYear() !== displayYear);

                    var tr = document.createElement('tr');
                    tr.setAttribute('data-date', dateKey);
                    if (isOtherMonth) tr.classList.add('is-other-month');

                    var thuTd = document.createElement('td');
                    thuTd.className = 'ws-lich-mobile-month__thu';
                    thuTd.textContent = thu;

                    var ngayTd = document.createElement('td');
                    ngayTd.className = 'ws-lich-mobile-month__ngay';
                    ngayTd.textContent = dd + '/' + mm;

                    var workTd = document.createElement('td');
                    workTd.className = 'ws-lich-mobile-month__work';
                    var contractsDom = buildDayContractsDom(getContractsForDate(dateKey), 0);
                    if (contractsDom) {
                        workTd.appendChild(contractsDom);
                    } else {
                        var empty = document.createElement('span');
                        empty.className = 'ws-lich-mobile-month__empty';
                        empty.textContent = '—';
                        workTd.appendChild(empty);
                    }

                    tr.appendChild(thuTd);
                    tr.appendChild(ngayTd);
                    tr.appendChild(workTd);
                    mobileMonthBodyEl.appendChild(tr);

                    cursor.setDate(cursor.getDate() + 1);
                }

                mobileMonthBodyEl.querySelectorAll('tr[data-date]').forEach(function (tr) {
                    tr.addEventListener('click', function () {
                        var dateStr = tr.getAttribute('data-date');
                        if (dateStr) openWorkDayModal(dateStr);
                    });
                });
            }

            function refreshCalendarLayout() {
                if (!calendar) return;
                requestAnimationFrame(function () {
                    calendar.updateSize();
                    requestAnimationFrame(function () {
                        calendar.updateSize();
                    });
                });
            }

            function hideMobileMonthLayout() {
                root.classList.remove('is-mobile-month');
                if (mobileMonthEl) mobileMonthEl.classList.add('d-none');
            }

            function syncMobileMonthLayout() {
                if (!mobileMonthEl) return;
                var wasMobileMonth = root.classList.contains('is-mobile-month');
                if (shouldUseMobileMonthLayout()) {
                    root.classList.add('is-mobile-month');
                    mobileMonthEl.classList.remove('d-none');
                    renderMobileMonthTable();
                    applyTienDoColors();
                } else {
                    hideMobileMonthLayout();
                    if (wasMobileMonth) {
                        refreshCalendarLayout();
                    }
                }
            }

            var syncMobileMonthLayoutDebounced = (function () {
                var timer = null;
                return function () {
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(syncMobileMonthLayout, 120);
                };
            })();

            function formatWeekDayHeader(date) {
                var d = date.getDay();
                var label = weekDayLabels[d] || '';
                var dd = String(date.getDate()).padStart(2, '0');
                var mm = String(date.getMonth() + 1).padStart(2, '0');
                return {
                    label: label,
                    dateText: dd + '/' + mm
                };
            }

            var isAdmin = @json(!empty($isAdmin));
            var todayStr = (function () {
                var now = new Date();
                return now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            })();

            function normalizeCalendarEvents(raw) {
                if (!Array.isArray(raw)) return [];
                return raw.map(function (ev) {
                    var props = ev.extendedProps || {};
                    if (!Array.isArray(props.contracts)) return ev;
                    var contracts = filterVisibleContracts(props.contracts);
                    if (!contracts.length) return null;
                    return Object.assign({}, ev, {
                        extendedProps: Object.assign({}, props, { contracts: contracts })
                    });
                }).filter(Boolean);
            }

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, function (c) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]);
                });
            }

            function buildBriefButtonHtml(contractItem) {
                var brief = (contractItem && contractItem.brief) ? contractItem.brief : {};
                var attrs = [
                    ['data-khach-hang', brief.khach_hang],
                    ['data-sdt', brief.sdt],
                    ['data-ngay-chup', brief.ngay_chup],
                    ['data-nguoi-chup', brief.nguoi_chup],
                    ['data-dia-diem', brief.dia_diem],
                    ['data-concept', brief.concept],
                    ['data-trang-phuc', brief.trang_phuc],
                    ['data-ghi-chu', brief.ghi_chu],
                ];
                var dataAttrs = attrs.map(function (pair) {
                    return pair[0] + '="' + escapeHtml(pair[1] || '—') + '"';
                }).join(' ');

                return '<button type="button" class="btn btn-sm btn-outline-dark btn-in-brief" ' +
                    'data-bs-toggle="modal" data-bs-target="#modalInBrief" ' + dataAttrs + ' ' +
                    'title="In brief" aria-label="In brief">' +
                    '<i class="icon-base ti tabler-file-text icon-sm me-1" aria-hidden="true"></i> In brief' +
                    '</button>';
            }

            function buildDieuPhoiButtonHtml(contractItem) {
                if (!isAdmin) return '';
                var hopId = contractItem && contractItem.id != null ? String(contractItem.id) : '';
                if (!hopId) return '';
                return '<button type="button" class="btn btn-sm btn-outline-primary btn-dieu-phoi" ' +
                    'data-hop-id="' + escapeHtml(hopId) + '" ' +
                    'title="Điều phối" aria-label="Điều phối">' +
                    '<i class="icon-base ti tabler-users icon-sm me-1" aria-hidden="true"></i> Điều phối' +
                    '</button>';
            }

            function getCalendarRangeParams() {
                var view = calendar.view;
                if (!view) return null;
                return {
                    start: view.activeStart.toISOString(),
                    end: view.activeEnd.toISOString()
                };
            }

            function setListToolbarActive(active) {
                var listBtn = root.querySelector('.fc-wsList-button');
                if (listBtn) {
                    listBtn.classList.toggle('fc-button-active', active);
                }
            }

            function activateListView() {
                if (calendar && calendar.view && calendar.view.type) {
                    calendarViewBeforeList = calendar.view.type;
                }
                listModeActive = true;
                root.classList.add('is-list-mode');
                syncMobileMonthLayout();
                if (listViewEl) listViewEl.classList.remove('d-none');
                root.querySelectorAll('.fc-dayGridMonth-button, .fc-dayGridWeek-button').forEach(function (btn) {
                    btn.classList.remove('fc-button-active');
                });
                setListToolbarActive(true);
                loadListDanhSach(1);
            }

            function deactivateListView(targetView) {
                if (!listModeActive) return;

                listModeActive = false;
                root.classList.remove('is-list-mode');
                syncMobileMonthLayout();
                if (listViewEl) listViewEl.classList.add('d-none');
                setListToolbarActive(false);

                var view = targetView || calendarViewBeforeList || 'dayGridMonth';
                if (view !== 'dayGridMonth' && view !== 'dayGridWeek') {
                    view = 'dayGridMonth';
                }
                calendar.changeView(view);
                calendar.refetchEvents();
                refreshCalendarLayout();
            }

            function listCardTienDoClass(contract) {
                var key = contractTienDoKey(contract);
                return key ? ('ws-lich-list-card--' + key) : '';
            }

            function buildListPaymentFooterHtml(contract) {
                var pct = contractTyLeThanhToan(contract);
                var cls = 'ws-lich-list-pay';
                if (pct === 100) cls += ' ws-lich-list-pay--full';
                else if (pct === 0) cls += ' ws-lich-list-pay--zero';
                else cls += ' ws-lich-list-pay--partial';
                return '<span class="' + cls + '">Thanh toán: ' + pct + '%</span>';
            }

            function buildListContractCardHtml(contract) {
                var peopleText = buildContractPeopleText(contract, false);
                var summaryText = buildContractSummaryText(contract);
                var detailLine = [peopleText, summaryText].filter(function (part) {
                    return String(part || '').trim() !== '';
                }).join(' · ');
                var tdClass = listCardTienDoClass(contract);
                var coupleText = buildContractCoupleShortText(contract) || String(contract.couple || '').trim() || '—';
                var maHd = String(contract.ma_hop_dong || '').trim();
                var ttKey = String(contract.trang_thai_hop_dong || '').trim();
                var ttLabel = String(contract.trang_thai_hop_dong_label || ttKey || '').trim();
                var ttBadgeClass = trangThaiHopDongClass[ttKey] || 'bg-label-secondary';
                var tienDoLabel = String(contract.tien_do_label || '').trim();
                var titleAttr = tienDoLabel ? (' title="' + escapeHtml(tienDoLabel) + '"') : '';

                var footerParts = [];
                if (ttLabel) {
                    footerParts.push('<span class="badge ' + escapeHtml(ttBadgeClass) + '">' + escapeHtml(ttLabel) + '</span>');
                }
                footerParts.push(buildListPaymentFooterHtml(contract));

                return '<div class="ws-lich-list-card' + (tdClass ? (' ' + tdClass) : '') + '" data-date="' + escapeHtml(contract.ngay || '') + '" role="button" tabindex="0"' + titleAttr + '>' +
                    '<div class="ws-lich-list-card__body">' +
                        '<div class="ws-lich-list-card__head">' +
                            '<div class="ws-lich-list-card__couple">' + escapeHtml(coupleText) + '</div>' +
                            (maHd ? '<span class="badge bg-label-secondary">' + escapeHtml(maHd) + '</span>' : '') +
                        '</div>' +
                        (detailLine ? '<div class="ws-lich-list-card__detail">' + escapeHtml(detailLine) + '</div>' : '') +
                    '</div>' +
                    '<div class="ws-lich-list-card__footer">' + footerParts.join('') + '</div>' +
                '</div>';
            }

            function renderListDanhSach(payload) {
                if (!listBodyEl || !listPaginationEl || !listSummaryEl) return;

                var items = (payload && payload.items) ? payload.items : [];
                var pagination = (payload && payload.pagination) ? payload.pagination : {};
                var total = parseInt(pagination.total, 10) || 0;
                var currentPage = parseInt(pagination.current_page, 10) || 1;
                var lastPage = parseInt(pagination.last_page, 10) || 1;
                var perPage = parseInt(pagination.per_page, 10) || 20;
                var from = parseInt(pagination.from, 10) || 0;
                var to = parseInt(pagination.to, 10) || 0;

                listCurrentPage = currentPage;

                if (!items.length) {
                    listBodyEl.innerHTML = '<div class="alert alert-info mb-0">Không có hợp đồng trong khoảng thời gian này.</div>';
                    listSummaryEl.textContent = '0 hợp đồng';
                    listPaginationEl.innerHTML = '';
                    return;
                }

                var html = '';
                var lastNgay = null;
                items.forEach(function (item) {
                    if (!filterVisibleContracts([item]).length) return;

                    var ngay = String(item.ngay || '');
                    if (ngay !== lastNgay) {
                        if (lastNgay !== null) {
                            html += '</div></div>';
                        }
                        html += '<div class="ws-lich-list-day">' +
                            '<div class="ws-lich-list-day__heading">' + escapeHtml(item.ngay_label || ngay) + '</div>' +
                            '<div class="ws-lich-list-day__items">';
                        lastNgay = ngay;
                    }
                    var timeText = String(item.time || '').trim() || '--:--';
                    html += '<div class="ws-lich-list-item">' +
                        '<div class="ws-lich-list-item__time">' + escapeHtml(timeText) + '</div>' +
                        buildListContractCardHtml(item) +
                    '</div>';
                });
                if (lastNgay !== null) {
                    html += '</div></div>';
                }

                listBodyEl.innerHTML = html;
                listSummaryEl.textContent = (from && to)
                    ? ('Hiển thị ' + from + '–' + to + ' / ' + total + ' hợp đồng · Trang ' + currentPage + '/' + lastPage)
                    : (total + ' hợp đồng');

                listBodyEl.querySelectorAll('.ws-lich-list-card').forEach(function (el) {
                    el.addEventListener('click', function () {
                        var dateStr = el.getAttribute('data-date');
                        if (dateStr) openWorkDayModal(dateStr);
                    });
                    el.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            var dateStr = el.getAttribute('data-date');
                            if (dateStr) openWorkDayModal(dateStr);
                        }
                    });
                });

                var pagHtml = '<ul class="pagination pagination-sm justify-content-center flex-wrap">';
                pagHtml += '<li class="page-item' + (currentPage <= 1 ? ' disabled' : '') + '">' +
                    '<button type="button" class="page-link" data-list-page="' + (currentPage - 1) + '"' + (currentPage <= 1 ? ' disabled' : '') + '>Trước</button></li>';

                var pageStart = Math.max(1, currentPage - 2);
                var pageEnd = Math.min(lastPage, currentPage + 2);
                if (pageStart > 1) {
                    pagHtml += '<li class="page-item"><button type="button" class="page-link" data-list-page="1">1</button></li>';
                    if (pageStart > 2) {
                        pagHtml += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                }
                for (var p = pageStart; p <= pageEnd; p++) {
                    pagHtml += '<li class="page-item' + (p === currentPage ? ' active' : '') + '">' +
                        '<button type="button" class="page-link" data-list-page="' + p + '">' + p + '</button></li>';
                }
                if (pageEnd < lastPage) {
                    if (pageEnd < lastPage - 1) {
                        pagHtml += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                    pagHtml += '<li class="page-item"><button type="button" class="page-link" data-list-page="' + lastPage + '">' + lastPage + '</button></li>';
                }

                pagHtml += '<li class="page-item' + (currentPage >= lastPage ? ' disabled' : '') + '">' +
                    '<button type="button" class="page-link" data-list-page="' + (currentPage + 1) + '"' + (currentPage >= lastPage ? ' disabled' : '') + '>Sau</button></li>';
                pagHtml += '</ul>';

                listPaginationEl.innerHTML = pagHtml;
                listPaginationEl.querySelectorAll('[data-list-page]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        if (btn.disabled) return;
                        var page = parseInt(btn.getAttribute('data-list-page'), 10);
                        if (!isNaN(page) && page >= 1) {
                            loadListDanhSach(page);
                        }
                    });
                });
            }

            function loadListDanhSach(page) {
                if (!listBodyEl) return;
                var range = getCalendarRangeParams();
                if (!range) return;

                listBodyEl.innerHTML = '<div class="text-muted small py-4 text-center">Đang tải...</div>';
                if (listSummaryEl) listSummaryEl.textContent = 'Đang tải...';
                if (listPaginationEl) listPaginationEl.innerHTML = '';

                var perPage = listPerPageEl ? parseInt(listPerPageEl.value, 10) : 20;
                if ([10, 20, 50].indexOf(perPage) === -1) perPage = 20;

                var params = new URLSearchParams({
                    start: range.start,
                    end: range.end,
                    page: String(page || 1),
                    per_page: String(perPage)
                });
                appendLocParams(params);

                fetch(listDanhSachUrl + '?' + params.toString(), { method: 'GET' })
                    .then(function (r) { return r.json(); })
                    .then(function (payload) { renderListDanhSach(payload); })
                    .catch(function () {
                        listBodyEl.innerHTML = '<div class="alert alert-danger mb-0">Không tải được danh sách. Vui lòng thử lại.</div>';
                        if (listSummaryEl) listSummaryEl.textContent = '';
                    });
            }

            var calendar = new Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                plugins: [dayGridPlugin, interactionPlugin],
                height: 'auto',
                firstDay: 1,
                editable: false,
                selectable: false,
                direction: direction,
                navLinks: true,
                fixedWeekCount: false,
                customButtons: {
                    wsList: {
                        text: 'Danh sách',
                        click: function () {
                            activateListView();
                        }
                    }
                },
                buttonText: {
                    today: 'Hôm nay',
                    month: 'Tháng',
                    week: 'Tuần'
                },
                views: {
                    dayGridMonth: { buttonText: 'Tháng', dayMaxEvents: true },
                    dayGridWeek: { buttonText: 'Tuần', dayMaxEvents: false, duration: { weeks: 1 } }
                },
                allDayText: 'Cả ngày',
                moreLinkText: function (n) { return '+ ' + n + ' mục'; },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,wsList'
                },
                dayHeaderContent: function (arg) {
                    var wrap = document.createElement('span');
                    wrap.className = 'ws-week-day-header';
                    var title = document.createElement('span');
                    title.textContent = weekDayLabels[arg.date.getDay()] || '';
                    wrap.appendChild(title);
                    if (arg.view && arg.view.type === 'dayGridWeek') {
                        var h = formatWeekDayHeader(arg.date);
                        title.textContent = h.label;
                        var sub = document.createElement('span');
                        sub.className = 'ws-week-day-header__date';
                        sub.textContent = h.dateText;
                        wrap.appendChild(sub);
                    }
                    return { domNodes: [wrap] };
                },
                events: function (info, successCallback, failureCallback) {
                    var baseUrl = @json(route('admin.lich-lam-viec.data'));
                    var params = new URLSearchParams({
                        start: info.startStr,
                        end: info.endStr
                    });
                    appendLocParams(params);
                    fetch(baseUrl + '?' + params.toString(), { method: 'GET' })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            successCallback(normalizeCalendarEvents(data));
                            requestAnimationFrame(function () {
                                syncMobileMonthLayout();
                                applyTienDoColors();
                            });
                        })
                        .catch(function (e) { failureCallback(e); });
                },
                eventContent: function (arg) {
                    var p = (arg.event.extendedProps || {});
                    var contracts = p.contracts || [];
                    var viewType = arg.view ? arg.view.type : '';
                    if (viewType === 'dayGridMonth' || viewType === 'dayGridWeek') {
                        if (!contracts.length) return { domNodes: [] };
                        var maxVisible = (viewType === 'dayGridWeek') ? 0 : 4;
                        var node = buildDayContractsDom(contracts, maxVisible);
                        if (!node) return { domNodes: [] };
                        return { domNodes: [node] };
                    }
                    return { domNodes: [] };
                },
                dateClick: function (info) {
                    var d = info.date;
                    var y = d.getFullYear();
                    var m = String(d.getMonth() + 1).padStart(2, '0');
                    var day = String(d.getDate()).padStart(2, '0');
                    openWorkDayModal(y + '-' + m + '-' + day);
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    var start = info.event.start;
                    if (!start) return;
                    var y = start.getFullYear();
                    var m = String(start.getMonth() + 1).padStart(2, '0');
                    var day = String(start.getDate()).padStart(2, '0');
                    openWorkDayModal(y + '-' + m + '-' + day);
                },
                viewDidMount: function (arg) {
                    syncMobileMonthLayout();
                    if (listModeActive) return;
                    if (arg.view.type === 'dayGridMonth' || arg.view.type === 'dayGridWeek') {
                        refreshCalendarLayout();
                        if (arg.view.type === 'dayGridWeek') {
                            setTimeout(function () { calendar.updateSize(); }, 0);
                        }
                    }
                },
                datesSet: function () {
                    syncMobileMonthLayout();
                    if (listModeActive) {
                        loadListDanhSach(1);
                    }
                }
            });

            calendar.render();
            syncMobileMonthLayout();

            if (typeof mobileMonthMq.addEventListener === 'function') {
                mobileMonthMq.addEventListener('change', syncMobileMonthLayoutDebounced);
            } else if (typeof mobileMonthMq.addListener === 'function') {
                mobileMonthMq.addListener(syncMobileMonthLayoutDebounced);
            }
            window.addEventListener('resize', syncMobileMonthLayoutDebounced);

            calendarEl.addEventListener('click', function (e) {
                if (!listModeActive) return;
                if (e.target.closest('.fc-dayGridMonth-button')) {
                    deactivateListView('dayGridMonth');
                } else if (e.target.closest('.fc-dayGridWeek-button')) {
                    deactivateListView('dayGridWeek');
                }
            }, true);

            if (listPerPageEl) {
                listPerPageEl.addEventListener('change', function () {
                    if (listModeActive) loadListDanhSach(1);
                });
            }

            root.querySelectorAll('.ws-lich-loc-filter').forEach(function (el) {
                el.addEventListener('change', function () {
                    updateLocClearButton();
                    calendar.refetchEvents();
                    syncMobileMonthLayout();
                    if (listModeActive) loadListDanhSach(1);
                });
            });
            var locClearBtn = document.getElementById('wsLichLocClear');
            if (locClearBtn) {
                locClearBtn.addEventListener('click', function () {
                    root.querySelectorAll('.ws-lich-loc-filter').forEach(function (cb) {
                        cb.checked = false;
                    });
                    updateLocClearButton();
                    calendar.refetchEvents();
                    syncMobileMonthLayout();
                    if (listModeActive) loadListDanhSach(1);
                });
            }
            updateLocClearButton();

            function bindWorkDayAddBtn(dateStr) {
                var modalEl = document.getElementById('wsWorkDayModal');
                var addBtn = document.getElementById('wsWorkDayAddBtn');
                if (!addBtn) return;
                if (modalEl) modalEl.dataset.wsDate = dateStr;
                var isPast = dateStr < todayStr;
                addBtn.disabled = isPast;
                if (isPast) {
                    addBtn.setAttribute('title', 'Không thể thêm lịch cho ngày đã qua');
                } else {
                    addBtn.removeAttribute('title');
                }
            }

            var workDayAddBtn = document.getElementById('wsWorkDayAddBtn');
            if (workDayAddBtn) {
                workDayAddBtn.addEventListener('click', function () {
                    if (workDayAddBtn.disabled) return;
                    var modalEl = document.getElementById('wsWorkDayModal');
                    var dateStr = modalEl && modalEl.dataset.wsDate;
                    if (dateStr) openAddWorkModal(dateStr);
                });
            }

            function openWorkDayModal(dateStr) {
                var modalEl = document.getElementById('wsWorkDayModal');
                var titleEl = document.getElementById('wsWorkDayModalTitle');
                var bodyEl = document.getElementById('wsWorkDayModalBody');
                if (!modalEl || !titleEl || !bodyEl) return;

                titleEl.textContent = 'Chi tiết công việc ngày ' + dateStr;
                bodyEl.classList.add('is-centered');
                bodyEl.innerHTML = '<div class="text-muted small">Đang tải...</div>';
                bindWorkDayAddBtn(dateStr);

                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery(modalEl).modal('show');
                } else {
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                }

                var detailUrl = @json(route('admin.lich-lam-viec.chi-tiet-ngay'));
                var params = new URLSearchParams({ date: dateStr });
                appendLocParams(params);
                fetch(detailUrl + '?' + params.toString(), { method: 'GET' })
                    .then(function (r) { return r.json(); })
                    .then(function (payload) {
                        var items = (payload && payload.items) ? payload.items : [];
                        if (!items.length) {
                            bodyEl.classList.add('is-centered');
                            bodyEl.innerHTML = '<div class="alert alert-info mb-0">Không có công việc trong ngày này.</div>';
                            bindWorkDayAddBtn(dateStr);
                            return;
                        }

                        bodyEl.classList.remove('is-centered');
                        var html = '<div class="d-flex flex-column gap-3">';

                        items.forEach(function (it) {
                            var timeCouple = buildContractTimeCoupleText(it);
                            var maHd = String(it.ma_hop_dong || '').trim();
                            var goiChup = String(it.goi_chup || '').trim();
                            var diaDiem = String(it.dia_diem || '').trim();
                            var ghiChu = String(it.ghi_chu || '').trim();
                            var pc = it.phan_cong || {};
                            var peopleText = buildContractPeopleText(it, false);
                            var editText = String(pc.edit || '').trim();
                            var ngayUpDemo = String(it.ngay_up_link_demo_gan_nhat || '').trim();
                            var ngayUpIn = String(it.ngay_up_link_in_gan_nhat || '').trim();
                            var summaryParts = [goiChup, diaDiem, ghiChu].filter(function (part) {
                                return String(part || '').trim() !== '';
                            });
                            var detailMeta = [peopleText, summaryParts.join(' - ')].filter(function (part) {
                                return String(part || '').trim() !== '';
                            }).join(' · ');
                            var tienDoLabel = String(it.tien_do_label || '').trim();

                            html += '<div class="border rounded p-3 ' + workDetailCardClass(it) + '">' +
                                buildPaymentBadgeHtml(it) +
                                '<div class="d-flex flex-wrap gap-2 align-items-start justify-content-between">' +
                                    '<div class="fw-semibold">' + escapeHtml(timeCouple) + '</div>' +
                                    '<div class="d-flex flex-wrap gap-2">' +
                                        (tienDoLabel ? '<span class="badge bg-label-primary">' + escapeHtml(tienDoLabel) + '</span>' : '') +
                                        (maHd ? '<span class="badge bg-label-secondary">' + escapeHtml(maHd) + '</span>' : '') +
                                    '</div>' +
                                '</div>' +
                                (detailMeta ? '<div class="small mt-2">' + escapeHtml(detailMeta) + '</div>' : '') +
                                (editText ? '<div class="text-muted small mt-2">Edit: ' + escapeHtml(editText) + '</div>' : '') +
                                '<div class="text-muted small mt-2 ws-upload-meta">' +
                                    '<div>Up file chụp: ' + escapeHtml(ngayUpDemo || '—') + '</div>' +
                                    '<div>Up file in: ' + escapeHtml(ngayUpIn || '—') + '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-wrap gap-2 mt-2">' +
                                    buildBriefButtonHtml(it) +
                                    buildDieuPhoiButtonHtml(it) +
                                '</div>' +
                            '</div>';
                        });

                        html += '</div>';
                        bodyEl.innerHTML = html;
                        bindWorkDayAddBtn(dateStr);
                    })
                    .catch(function () {
                        bodyEl.classList.add('is-centered');
                        bodyEl.innerHTML = '<div class="alert alert-danger mb-0">Không tải được dữ liệu chi tiết. Vui lòng thử lại.</div>';
                        bindWorkDayAddBtn(dateStr);
                    });
            }

            var WS_NV_URL_TMPL = @json(route('admin.khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay', ['hopDongCuoi' => '__HDC__']));
            var WS_DIEU_PHOI_DATA_TMPL = @json(route('admin.lich-lam-viec.hop-dong-dieu-phoi-data', ['hopDongCuoi' => '__HDC__']));
            var WS_DIEU_PHOI_PUT_TMPL = @json(route('admin.khach-hang.hop-dong-cuoi.dieu-phoi', ['hopDongCuoi' => '__HDC__']));
            var wsAddWorkHopId = null;

            function wsAddWorkNvUrl(hopId) {
                return WS_NV_URL_TMPL.split('__HDC__').join(String(hopId));
            }

            function wsAddWorkDieuPhoiDataUrl(hopId) {
                return WS_DIEU_PHOI_DATA_TMPL.split('__HDC__').join(String(hopId));
            }

            function wsAddWorkDieuPhoiPutUrl(hopId) {
                return WS_DIEU_PHOI_PUT_TMPL.split('__HDC__').join(String(hopId));
            }

            function wsAddWorkSetMode(mode) {
                var modalEl = document.getElementById('wsAddWorkModal');
                var titleEl = document.getElementById('wsAddWorkModalTitle');
                var submitBtn = document.getElementById('wsAddWorkSubmitBtn');
                var hopDongField = document.querySelector('.ws-add-work-field-hop-dong');
                var hopDongEl = document.getElementById('wsAddWorkHopDong');
                if (!modalEl) return;

                mode = mode === 'edit' ? 'edit' : 'create';
                modalEl.dataset.wsMode = mode;

                if (titleEl) {
                    titleEl.textContent = mode === 'edit' ? 'Điều phối' : 'Thêm lịch làm việc';
                }
                if (submitBtn) {
                    submitBtn.innerHTML = mode === 'edit'
                        ? '<i class="icon-base ti tabler-device-floppy icon-sm me-1"></i> Lưu điều phối'
                        : '<i class="icon-base ti tabler-plus icon-sm me-1"></i> Tạo lịch';
                }
                if (hopDongField) {
                    hopDongField.classList.toggle('d-none', mode === 'edit');
                }
                if (hopDongEl) {
                    if (mode === 'edit') {
                        hopDongEl.removeAttribute('required');
                    } else {
                        hopDongEl.setAttribute('required', '');
                    }
                }
            }

            function wsAddWorkHideDetailModal() {
                var detailEl = document.getElementById('wsWorkDayModal');
                if (!detailEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(detailEl).hide();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery(detailEl).modal('hide');
                }
            }

            function wsAddWorkShowModal() {
                var modalEl = document.getElementById('wsAddWorkModal');
                if (!modalEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery(modalEl).modal('show');
                } else {
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                }
            }

            function wsAddWorkHideModal() {
                var modalEl = document.getElementById('wsAddWorkModal');
                if (!modalEl) return;
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery(modalEl).modal('hide');
                }
            }

            function wsAddWorkSetNgayChup(ymd) {
                var hidden = document.getElementById('wsAddWorkNgayChupHidden');
                var display = document.getElementById('wsAddWorkNgayChup');
                ymd = (ymd || '').trim();
                if (hidden) hidden.value = ymd;
                if (!display) return;
                if (!ymd) {
                    display.value = '';
                    return;
                }
                var parts = ymd.split('-');
                display.value = parts.length === 3
                    ? (parts[2] + '/' + parts[1] + '/' + parts[0])
                    : ymd;
            }

            function wsAddWorkGetNgayChupYmd() {
                var hidden = document.getElementById('wsAddWorkNgayChupHidden');
                if (hidden) return (hidden.value || '').trim();
                return '';
            }

            function wsAddWorkBindSelect2($sel, placeholder) {
                var $ = window.jQuery || window.$;
                if (!$ || !$.fn.select2 || !$sel.length) return;
                var $modal = $sel.closest('.modal');
                function renderOpt(opt) {
                    if (!opt || !opt.element) return opt && opt.text ? opt.text : '';
                    var el = opt.element;
                    var busy = el && el.dataset && el.dataset.busy === '1';
                    if (!busy) return opt.text;
                    var $wrap = $('<span></span>');
                    $wrap.text(opt.text + ' ');
                    $wrap.append('<span class="badge bg-label-warning ms-1">Bận</span>');
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
                if ($sel.data('select2')) $sel.select2('destroy');
                $sel.select2(opts);
            }

            function wsAddWorkSetChupMakeDisabled(disabled) {
                var $ = window.jQuery || window.$;
                if (!$) return;
                ['#wsAddWorkThoChup', '#wsAddWorkThoMake'].forEach(function (sel) {
                    var $el = $(sel);
                    if ($el.length) $el.prop('disabled', disabled);
                });
            }

            function wsAddWorkRebuildChupMake(items, wantChup, wantMake) {
                var $ = window.jQuery || window.$;
                if (!$) return;
                function fill(sel, ph, want) {
                    var $sel = $(sel);
                    if (!$sel.length) return;
                    var prev = want != null && want !== '' ? String(want) : '';
                    if ($sel.data('select2')) $sel.select2('destroy');
                    $sel.empty().append(new Option('—', '', false, false));
                    (items || []).forEach(function (it) {
                        var o = new Option(it.ten, String(it.id), false, false);
                        if (it.disabled) o.dataset.busy = '1';
                        $sel.append(o);
                    });
                    var $match = $sel.find('option').filter(function () {
                        return String($(this).val()) === prev;
                    });
                    var pick = $match.length ? prev : '';
                    wsAddWorkBindSelect2($sel, ph);
                    $sel.val(pick || null).trigger('change');
                }
                fill('#wsAddWorkThoChup', 'Chọn người chụp', wantChup);
                fill('#wsAddWorkThoMake', 'Chọn người make', wantMake);
            }

            function wsAddWorkFetchChupMake(ymd, wantChup, wantMake) {
                if (!wsAddWorkHopId || !ymd) {
                    wsAddWorkSetChupMakeDisabled(true);
                    wsAddWorkRebuildChupMake([], '', '');
                    return;
                }
                var url = wsAddWorkNvUrl(wsAddWorkHopId) + '?ngay=' + encodeURIComponent(ymd);
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        wsAddWorkSetChupMakeDisabled(false);
                        wsAddWorkRebuildChupMake(data.items || [], wantChup, wantMake);
                    })
                    .catch(function () {
                        wsAddWorkSetChupMakeDisabled(true);
                        wsAddWorkRebuildChupMake([], '', '');
                    });
            }

            function wsAddWorkResetDieuPhoiFields() {
                var panel = document.getElementById('wsAddWorkDieuPhoiFields');
                if (panel) panel.classList.add('d-none');
                wsAddWorkHopId = null;
                var titleEl = document.getElementById('wsAddWorkHopDongTitle');
                if (titleEl) titleEl.textContent = 'Thông tin điều phối';
                if (window.setAdminTimeInput) window.setAdminTimeInput('wsAddWorkGioChup', '');
                else {
                    var gioEl = document.getElementById('wsAddWorkGioChup');
                    if (gioEl) gioEl.value = '';
                }
                ['wsAddWorkNgayCuoi', 'wsAddWorkNgayTraDemo', 'wsAddWorkNgayTraIn'].forEach(function (id) {
                    if (window.setAdminDateInput) window.setAdminDateInput(id, '');
                    else {
                        var el = document.getElementById(id);
                        if (el) el.value = '';
                    }
                });
                var diaDiemEl = document.getElementById('wsAddWorkDiaDiem');
                if (diaDiemEl) diaDiemEl.value = '';
                var ghiChuEl = document.getElementById('wsAddWorkGhiChuSale');
                if (ghiChuEl) ghiChuEl.value = '';
                wsAddWorkSetChupMakeDisabled(true);
                wsAddWorkRebuildChupMake([], '', '');
                var $ = window.jQuery || window.$;
                if ($) $('#wsAddWorkThoEdit').val('').trigger('change');
            }

            function wsAddWorkFillDieuPhoiFields(payload, scheduleDate) {
                var panel = document.getElementById('wsAddWorkDieuPhoiFields');
                if (panel) panel.classList.remove('d-none');
                var titleEl = document.getElementById('wsAddWorkHopDongTitle');
                if (titleEl) {
                    var ma = payload.ma_hop_dong ? String(payload.ma_hop_dong) : ('HĐ #' + payload.id);
                    var cd = payload.ten_co_dau ? String(payload.ten_co_dau).trim() : '';
                    var cr = payload.ten_chu_re ? String(payload.ten_chu_re).trim() : '';
                    var ten = (cd || cr) ? (cd + (cd && cr ? ' - ' : '') + cr) : '';
                    titleEl.textContent = 'Điều phối: ' + ma + (ten ? (' — ' + ten) : '');
                }
                var ngayChupYmd = (scheduleDate || payload.ngay_chup_thuc_te || '').trim();
                wsAddWorkSetNgayChup(ngayChupYmd);
                if (window.setAdminTimeInput) window.setAdminTimeInput('wsAddWorkGioChup', payload.gio_chup != null ? String(payload.gio_chup) : '');
                if (window.setAdminDateInput) {
                    window.setAdminDateInput('wsAddWorkNgayCuoi', payload.ngay_cuoi_chinh_thuc || '');
                    window.setAdminDateInput('wsAddWorkNgayTraDemo', payload.ngay_tra_link_demo_chinh_thuc || '');
                    window.setAdminDateInput('wsAddWorkNgayTraIn', payload.ngay_tra_link_in_chinh_thuc || '');
                }
                var diaDiemEl = document.getElementById('wsAddWorkDiaDiem');
                if (diaDiemEl) diaDiemEl.value = payload.dia_diem_chup != null ? String(payload.dia_diem_chup) : '';
                var ghiChuEl = document.getElementById('wsAddWorkGhiChuSale');
                if (ghiChuEl) ghiChuEl.value = payload.ghi_chu_sale != null ? String(payload.ghi_chu_sale) : '';
                var $ = window.jQuery || window.$;
                if ($) {
                    $('#wsAddWorkThoEdit').val(payload.tho_edit_id != null && payload.tho_edit_id !== '' ? String(payload.tho_edit_id) : '').trigger('change');
                }
                wsAddWorkFetchChupMake(ngayChupYmd, payload.tho_chup_id, payload.tho_make_id);
            }

            function wsAddWorkLoadHopDongDieuPhoi(hopId, scheduleDate, capNhat) {
                var errEl = document.getElementById('wsAddWorkError');
                if (!hopId) {
                    wsAddWorkResetDieuPhoiFields();
                    return;
                }
                wsAddWorkHopId = parseInt(hopId, 10);
                if (Number.isNaN(wsAddWorkHopId)) {
                    wsAddWorkHopId = null;
                    wsAddWorkResetDieuPhoiFields();
                    return;
                }
                var dataUrl = wsAddWorkDieuPhoiDataUrl(wsAddWorkHopId);
                if (capNhat) {
                    dataUrl += (dataUrl.indexOf('?') >= 0 ? '&' : '?') + 'cap_nhat=1';
                }
                fetch(dataUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        return r.json().catch(function () { return {}; }).then(function (j) {
                            return { ok: r.ok, json: j };
                        });
                    })
                    .then(function (res) {
                        if (!res.ok) {
                            wsAddWorkResetDieuPhoiFields();
                            if (errEl) {
                                errEl.textContent = (res.json && res.json.message) ? res.json.message : 'Không tải được thông tin hợp đồng.';
                                errEl.classList.remove('d-none');
                            }
                            return;
                        }
                        if (errEl) {
                            errEl.classList.add('d-none');
                            errEl.textContent = '';
                        }
                        wsAddWorkFillDieuPhoiFields(res.json, scheduleDate);
                    })
                    .catch(function () {
                        wsAddWorkResetDieuPhoiFields();
                        if (errEl) {
                            errEl.textContent = 'Không tải được thông tin hợp đồng.';
                            errEl.classList.remove('d-none');
                        }
                    });
            }

            function openAddWorkModal(dateStr) {
                var modalEl = document.getElementById('wsAddWorkModal');
                var formEl = document.getElementById('wsAddWorkForm');
                var hopDongEl = document.getElementById('wsAddWorkHopDong');
                var errEl = document.getElementById('wsAddWorkError');
                if (!modalEl || !formEl || !hopDongEl) return;

                wsAddWorkSetMode('create');
                delete modalEl.dataset.wsEditHopId;
                modalEl.dataset.wsScheduleDate = dateStr;

                wsAddWorkHideDetailModal();

                if (errEl) {
                    errEl.classList.add('d-none');
                    errEl.textContent = '';
                }
                wsAddWorkSetNgayChup(dateStr);
                wsAddWorkResetDieuPhoiFields();
                hopDongEl.innerHTML = '<option value="">— Chọn hợp đồng —</option>';
                hopDongEl.disabled = true;
                var $ = window.jQuery || window.$;
                if ($ && $.fn.select2 && $(hopDongEl).data('select2')) {
                    $(hopDongEl).val('').trigger('change');
                }

                var listUrl = @json(route('admin.lich-lam-viec.hop-dong-chua-phan-ngay'));
                fetch(listUrl, { method: 'GET' })
                    .then(function (r) { return r.json(); })
                    .then(function (payload) {
                        var items = (payload && payload.items) ? payload.items : [];
                        if (!items.length) {
                            hopDongEl.innerHTML = '<option value="">(Không có hợp đồng phù hợp)</option>';
                        } else {
                            var html = '<option value="">— Chọn hợp đồng —</option>';
                            items.forEach(function (it) {
                                var ma = (it.ma_hop_dong && String(it.ma_hop_dong).trim()) ? String(it.ma_hop_dong).trim() : ('HĐ #' + it.id);
                                var cd = (it.ten_co_dau && String(it.ten_co_dau).trim()) ? String(it.ten_co_dau).trim() : '';
                                var cr = (it.ten_chu_re && String(it.ten_chu_re).trim()) ? String(it.ten_chu_re).trim() : '';
                                var ten = (cd || cr) ? (cd + (cd && cr ? ' - ' : '') + cr) : '';
                                var label = ten ? (ma + ' — ' + ten) : ma;
                                html += '<option value="' + String(it.id) + '">' + escapeHtml(label) + '</option>';
                            });
                            hopDongEl.innerHTML = html;
                        }
                        hopDongEl.disabled = false;
                        if ($ && $.fn.select2 && $(hopDongEl).data('select2')) {
                            $(hopDongEl).trigger('change.select2');
                        }
                    })
                    .catch(function () {
                        hopDongEl.innerHTML = '<option value="">(Không tải được danh sách)</option>';
                        hopDongEl.disabled = false;
                    });

                wsAddWorkShowModal();
            }

            function openDieuPhoiWorkModal(hopId, dateStr) {
                var modalEl = document.getElementById('wsAddWorkModal');
                var errEl = document.getElementById('wsAddWorkError');
                if (!modalEl || !hopId) return;

                wsAddWorkSetMode('edit');
                modalEl.dataset.wsEditHopId = String(hopId);
                modalEl.dataset.wsScheduleDate = dateStr || '';

                wsAddWorkHideDetailModal();

                if (errEl) {
                    errEl.classList.add('d-none');
                    errEl.textContent = '';
                }
                wsAddWorkSetNgayChup(dateStr || '');
                wsAddWorkResetDieuPhoiFields();
                wsAddWorkLoadHopDongDieuPhoi(hopId, dateStr || '', true);
                wsAddWorkShowModal();
            }

            var workDayModalBody = document.getElementById('wsWorkDayModalBody');
            if (workDayModalBody) {
                workDayModalBody.addEventListener('click', function (e) {
                    var btn = e.target.closest('.btn-dieu-phoi');
                    if (!btn) return;
                    var hopId = btn.getAttribute('data-hop-id');
                    if (!hopId) return;
                    var detailModal = document.getElementById('wsWorkDayModal');
                    var dateStr = detailModal && detailModal.dataset.wsDate ? detailModal.dataset.wsDate : '';
                    openDieuPhoiWorkModal(hopId, dateStr);
                });
            }

            (function bindAddWorkModalSelect2() {
                var modalEl = document.getElementById('wsAddWorkModal');
                if (!modalEl) return;
                modalEl.addEventListener('shown.bs.modal', function () {
                    var $ = window.jQuery || window.$;
                    if (!$ || !$.fn.select2) return;
                    var $targets = $(modalEl).find('select.select2-admin');
                    $targets.each(function () {
                        var $el = $(this);
                        if ($el.data('select2')) return;
                        wsAddWorkBindSelect2($el, $el.data('placeholder') || 'Chọn...');
                    });
                });
                modalEl.addEventListener('hidden.bs.modal', function () {
                    wsAddWorkHopId = null;
                    wsAddWorkSetMode('create');
                    delete modalEl.dataset.wsEditHopId;
                });
            })();

            (function bindHopDongDieuPhoi() {
                var modalEl = document.getElementById('wsAddWorkModal');
                var hopDongEl = document.getElementById('wsAddWorkHopDong');
                if (!modalEl || !hopDongEl) return;

                function onHopDongSelected() {
                    var scheduleDate = wsAddWorkGetNgayChupYmd() || modalEl.dataset.wsScheduleDate || '';
                    var $ = window.jQuery || window.$;
                    var hopId = ($ && $.fn.select2 && $(hopDongEl).data('select2'))
                        ? ($(hopDongEl).val() || '')
                        : (hopDongEl.value || '');
                    wsAddWorkLoadHopDongDieuPhoi(hopId, scheduleDate);
                }

                var $ = window.jQuery || window.$;
                if ($) {
                    $(hopDongEl).on('change.wsAddWorkHopDong', onHopDongSelected);
                } else {
                    hopDongEl.addEventListener('change', onHopDongSelected);
                }
            })();

            (function bindAddWorkForm() {
                var formEl = document.getElementById('wsAddWorkForm');
                if (!formEl) return;
                formEl.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var errEl = document.getElementById('wsAddWorkError');
                    var modalEl = document.getElementById('wsAddWorkModal');
                    if (errEl) {
                        errEl.classList.add('d-none');
                        errEl.textContent = '';
                    }

                    var fd = new FormData(formEl);
                    var isEdit = modalEl && modalEl.dataset.wsMode === 'edit';
                    var fetchUrl = @json(route('admin.lich-lam-viec.tao-lich'));
                    var fetchOpts = {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': @json(csrf_token()) },
                        body: fd
                    };

                    if (isEdit) {
                        var editHopId = modalEl.dataset.wsEditHopId;
                        if (!editHopId) {
                            if (errEl) {
                                errEl.textContent = 'Không xác định được hợp đồng.';
                                errEl.classList.remove('d-none');
                            }
                            return;
                        }
                        fd.delete('hop_dong_id');
                        fetchUrl = wsAddWorkDieuPhoiPutUrl(editHopId);
                        fetchOpts = {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': @json(csrf_token()),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: fd
                        };
                    }

                    fetch(fetchUrl, fetchOpts)
                        .then(function (r) {
                            return r.json().catch(function () { return { message: 'Có lỗi xảy ra.' }; }).then(function (j) {
                                return { ok: r.ok, status: r.status, json: j };
                            });
                        })
                        .then(function (res) {
                            if (!res.ok) {
                                var defaultMsg = isEdit ? 'Không lưu được điều phối.' : 'Không tạo được lịch.';
                                var msg = (res.json && (res.json.message || res.json.error)) ? (res.json.message || res.json.error) : defaultMsg;
                                if (res.json && res.json.errors) {
                                    var keys = Object.keys(res.json.errors);
                                    if (keys.length && res.json.errors[keys[0]] && res.json.errors[keys[0]][0]) {
                                        msg = res.json.errors[keys[0]][0];
                                    }
                                }
                                if (errEl) {
                                    errEl.textContent = msg;
                                    errEl.classList.remove('d-none');
                                }
                                return;
                            }
                            wsAddWorkHideModal();
                            calendar.refetchEvents();
                            if (listModeActive) loadListDanhSach(listCurrentPage);
                        })
                        .catch(function () {
                            if (errEl) {
                                errEl.textContent = isEdit
                                    ? 'Không lưu được điều phối. Vui lòng thử lại.'
                                    : 'Không tạo được lịch. Vui lòng thử lại.';
                                errEl.classList.remove('d-none');
                            }
                        });
                });
            })();

            (function bindBriefModal() {
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

            (function bindBriefDownload() {
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
        });
    </script>
@endif
@endpush
