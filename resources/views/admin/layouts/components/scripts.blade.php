    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="../../assets/vendor/libs/pickr/pickr.js"></script>

    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../../assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../../assets/vendor/libs/i18n/i18n.js"></script>

    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="../../assets/js/admin-menu-spread.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../assets/vendor/libs/swiper/swiper.js"></script>
    <script src="../../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script>
      window.adminMenuSearchItems = @json($quickSearchMenu ?? []);
    </script>
    <script src="../../assets/js/main.js"></script>

    <!-- Admin toast (Notyf) + REST API client -->
    <link rel="stylesheet" href="../../assets/vendor/libs/notyf/notyf.css" />
    <style>
        .notyf { z-index: 1095 !important; }
        .notyf__title { font-weight: 600; margin-bottom: 0.15rem; }
        .notyf__text { font-size: 0.9375rem; line-height: 1.4; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js" crossorigin="anonymous"></script>
    <script src="../../assets/vendor/libs/notyf/notyf.js"></script>
    <script src="../../assets/js/admin-toast.js"></script>
    <script src="../../assets/js/rest_api.js"></script>

    <!-- Select2: thống nhất UX select (tương tự modal phân công công việc) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var $ = window.jQuery || window.$;
        if (!$ || !$.fn.select2) return;
        $('.select2-admin').each(function() {
            var $el = $(this);
            if ($el.data('select2')) return;
            var placeholder = $el.data('placeholder') || 'Chọn...';
            var opts = { placeholder: placeholder, allowClear: true, width: '100%' };
            if ($el.prop('multiple')) {
                opts.closeOnSelect = false;
            }
            var $modal = $el.closest('.modal');
            if ($modal.length) opts.dropdownParent = $modal;
            $el.select2(opts);
        });
    });
    </script>

    <!-- Flatpickr: date/time picker dùng chung cho admin -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr === 'undefined') return;

        function applyAdminFlatpickr(el, opts) {
            var modal = el.closest('.modal');
            if (modal) {
                // Gắn calendar ra body để popup nổi trên modal (không bị footer/overflow che).
                opts.static = false;
                opts.appendTo = document.body;
            }
            return flatpickr(el, opts);
        }

        document.querySelectorAll('input.flatpickr-date-admin').forEach(function(el) {
            if (el._flatpickr) return;
            var opts = {
                altInput: true,
                altFormat: 'd/m/Y',
                dateFormat: 'Y-m-d',
                allowInput: false,
                static: false,
                locale: { firstDayOfWeek: 1 }
            };
            if (el.hasAttribute('data-flatpickr-min-today')) {
                opts.minDate = 'today';
            }
            applyAdminFlatpickr(el, opts);
        });

        // Datetime picker: hiển thị dd/mm/yyyy HH:MM, value gửi lên server Y-m-d H:i
        document.querySelectorAll('input.flatpickr-datetime-admin').forEach(function(el) {
            if (el._flatpickr) return;
            var opts = {
                altInput: true,
                altFormat: 'd/m/Y H:i',
                dateFormat: 'Y-m-d H:i',
                enableTime: true,
                time_24hr: true,
                minuteIncrement: 5,
                allowInput: false,
                static: false,
                locale: { firstDayOfWeek: 1 }
            };
            applyAdminFlatpickr(el, opts);
        });

        // Time picker: hiển thị/submit H:i, dùng cho các giờ lẻ trong form admin.
        document.querySelectorAll('input.flatpickr-time-admin').forEach(function(el) {
            if (el._flatpickr) return;
            var opts = {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                minuteIncrement: 5,
                allowInput: false,
                static: false
            };
            applyAdminFlatpickr(el, opts);
        });
    });
    // Helper: gán giá trị ngày cho input (dùng khi mở modal Sửa). value định dạng Y-m-d hoặc rỗng.
    window.setAdminDateInput = function(idOrEl, value) {
        var el = typeof idOrEl === 'string' ? document.getElementById(idOrEl) : idOrEl;
        if (!el) return;
        if (el._flatpickr) { if (value) el._flatpickr.setDate(value, false); else el._flatpickr.clear(); }
        else el.value = value || '';
    };

    // Helper: gán giá trị datetime cho input (dùng khi mở modal Sửa). value định dạng Y-m-d H:i hoặc rỗng.
    window.setAdminDateTimeInput = function(idOrEl, value) {
        var el = typeof idOrEl === 'string' ? document.getElementById(idOrEl) : idOrEl;
        if (!el) return;
        if (el._flatpickr) { if (value) el._flatpickr.setDate(value, false); else el._flatpickr.clear(); }
        else el.value = value || '';
    };

    // Helper: gán giá trị giờ cho input (định dạng H:i hoặc rỗng).
    window.setAdminTimeInput = function(idOrEl, value) {
        var el = typeof idOrEl === 'string' ? document.getElementById(idOrEl) : idOrEl;
        if (!el) return;
        if (el._flatpickr) { if (value) el._flatpickr.setDate(value, false); else el._flatpickr.clear(); }
        else el.value = value || '';
    };
    </script>

    <!-- Page JS (thêm script riêng qua @push('scripts') trong từng trang nếu cần) -->
    @stack('scripts')
