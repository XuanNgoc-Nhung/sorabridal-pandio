<meta charset="utf-8" />
<base href="{{ asset('') }}" />
<meta
  name="viewport"
  content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />
<title>@yield('title', 'Admin | Wedding Studio')</title>

<meta name="description" content="" />

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="../../assets/img/favicon/heart.png?t=1" />

<!-- Fonts: Tahoma stack qua demo.css / Bootstrap — không tải Public Sans -->

<link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />

<script src="../../assets/vendor/libs/@algolia/autocomplete-js.js"></script>

<!-- Core CSS -->
<link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" />
<link rel="stylesheet" href="../../assets/vendor/libs/pickr/pickr-themes.css" />
<link rel="stylesheet" href="../../assets/vendor/css/core.css" />
<link rel="stylesheet" href="../../assets/css/demo.css" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
<link rel="stylesheet" href="../../assets/vendor/libs/swiper/swiper.css" />
<link rel="stylesheet" href="../../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<link rel="stylesheet" href="../../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<link rel="stylesheet" href="../../assets/vendor/fonts/flag-icons.css" />

<!-- Page CSS -->
<link rel="stylesheet" href="../../assets/vendor/css/pages/cards-advance.css" />

<!-- Helpers -->
<script src="../../assets/vendor/js/helpers.js"></script>
<script src="../../assets/vendor/js/template-customizer.js"></script>
<script src="../../assets/js/template-customizer-menu-spread.js"></script>
<script>
(function () {
  try {
    var template = document.documentElement.getAttribute('data-template');
    var menuSpread = localStorage.getItem('templateCustomizer-' + template + '--MenuSpread');
    if (menuSpread === null || menuSpread === '' || menuSpread === 'true') {
      document.documentElement.classList.add('layout-menu-spread');
    }
  } catch (e) {}
})();
</script>
<script src="../../assets/js/config.js"></script>

<!-- Select2 (dùng chung cho các select trong admin) -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />

<!-- Flatpickr: date picker hiển thị dd/mm/yyyy (giống Single Picker trong demo) -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<style>
/* Văn bản thường: 14px (desktop); tablet/mobile co theo admin-responsive.css */
:root {
  --bs-root-font-size: 14px;
  --bs-body-font-size: 1rem;
}
html {
  font-size: 14px;
}
body,
.layout-wrapper {
  font-size: 1rem;
}
.form-control,
.form-select,
.input-group-text,
.btn,
.table,
.dropdown-menu,
.dropdown-item,
.modal,
.card,
.breadcrumb,
.menu-link,
.pagination,
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
  font-size: 1rem;
}

/* Calendar Flatpickr: popup (appendTo body) phải cao hơn modal Bootstrap (~1055) */
body > .flatpickr-calendar,
body > .flatpickr-calendar.open,
.modal .flatpickr-calendar,
.modal .flatpickr-calendar.open,
.flatpickr-calendar.open {
  z-index: 1090 !important;
}
/* Ngày không chọn được: theme mặc định dùng pointer-events:none nên không hover được — bật lại để cursor + nền hover thể hiện disabled */
.flatpickr-calendar .flatpickr-days .flatpickr-day.flatpickr-disabled,
.flatpickr-calendar .flatpickr-days .flatpickr-day.disabled {
  pointer-events: auto !important;
  cursor: not-allowed !important;
}
.flatpickr-calendar .flatpickr-days .flatpickr-day.flatpickr-disabled:hover,
.flatpickr-calendar .flatpickr-days .flatpickr-day.disabled:hover,
.flatpickr-calendar .flatpickr-days .flatpickr-day.flatpickr-disabled.today:hover {
  cursor: not-allowed !important;
  background: rgba(var(--bs-secondary-rgb), 0.14) !important;
  color: var(--bs-secondary-color) !important;
}
.flatpickr-calendar .flatpickr-days .flatpickr-day.notAllowed {
  pointer-events: auto !important;
  cursor: not-allowed !important;
}
.flatpickr-calendar .flatpickr-days .flatpickr-day.notAllowed:hover {
  cursor: not-allowed !important;
  background: rgba(var(--bs-secondary-rgb), 0.14) !important;
}
.modal .modal-footer{
  padding: 1.2rem !important;
  border-top: 1px solid #cdcdcd !important;
}
.modal .modal-header{
  padding: 1.2rem !important;
  border-bottom: 1px solid #cdcdcd !important;
}
.modal .modal-body{
  max-height: calc(100vh - 200px);
}
.select2-selection__choice__remove{

    color: red !important;
    font-size: 1rem;
    font-weight: bold;
    margin-left: 12px
}
.select2-container .select2-search--inline .select2-search__field{
}
.fc-h-event{
  background-color: #fff !important;
}
.menu-vertical .menu-header{
  padding: 0.5rem 0.5rem 0 1.3rem !important;
}
/* Menu con: bỏ chấm tròn mặc định (::before), chỉ giữ icon Tabler trong menu-link */
.menu-vertical .menu-sub > .menu-item > .menu-link::before {
  display: none !important;
}
.menu-vertical .menu-sub .menu-link {
  padding-inline-start: 1.75rem !important;
}
</style>

<!-- Admin: responsive — font/table gọn trên tablet & mobile -->
<link rel="stylesheet" href="{{ asset('assets/css/admin-responsive.css') }}" />

@stack('styles')
