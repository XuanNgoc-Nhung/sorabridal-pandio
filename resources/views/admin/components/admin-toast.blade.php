{{--
    Thông báo admin (Notyf — giống demo Notyf Notifications).
    Nhúng: @include('admin.components.admin-toast')
    Gọi JS: showAdminToast(message, type, options?)
    type: primary | secondary | success | danger | error | warning | info | dark
    options: { title, delay, dismissible, ripple, position }
--}}
@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/notyf/notyf.css') }}" />
<style>
    .notyf {
        z-index: 1095 !important;
    }
    .notyf__title {
        font-weight: 600;
        margin-bottom: 0.15rem;
    }
    .notyf__text {
        font-size: 0.9375rem;
        line-height: 1.4;
    }
</style>
@endpush
@endonce

@once
@push('scripts')
<script src="{{ asset('assets/vendor/libs/notyf/notyf.js') }}"></script>
<script src="{{ asset('assets/js/admin-toast.js') }}"></script>
@endpush
@endonce
