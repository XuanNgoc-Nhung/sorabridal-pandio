@php
  $bookingFormContext = $bookingFormContext ?? 'dat-lich';
  $showBookingSuccess = session('booking_form_context') === $bookingFormContext && session('success');
  $showBookingErrors = old('booking_form_context') === $bookingFormContext && $errors->any();
  $ngayCuoiDuKienValue = old('ngay_cuoi_du_kien');

  if (is_string($ngayCuoiDuKienValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $ngayCuoiDuKienValue)) {
      try {
          $ngayCuoiDuKienValue = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $ngayCuoiDuKienValue)->format('d/m/Y');
      } catch (\Throwable $exception) {
          // Keep the original value if it cannot be formatted safely.
      }
  }
@endphp

<div class="booking-form">
  <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:var(--ink);margin-bottom:24px">Đăng ký tư vấn miễn phí</h3>

  @if ($showBookingSuccess)
    <div class="booking-alert booking-alert-success">
      {{ session('success') }}
    </div>
  @endif

  @if ($showBookingErrors)
    <div class="booking-alert booking-alert-error">
      Vui lòng kiểm tra lại các thông tin đang nhập.
    </div>
  @endif

  <form method="POST" action="{{ route('user.dat-lich.store') }}">
    @csrf
    <input type="hidden" name="booking_form_context" value="{{ $bookingFormContext }}">

    <div class="form-row">
      <div class="form-group">
        <label for="{{ $bookingFormContext }}_ten_co_dau">Tên cô dâu *</label>
        <input
          id="{{ $bookingFormContext }}_ten_co_dau"
          name="ten_co_dau"
          type="text"
          placeholder="Nguyễn Thị Mai"
          value="{{ old('ten_co_dau') }}"
          class="@if($showBookingErrors) @error('ten_co_dau') is-invalid @enderror @endif"
          maxlength="150"
          required
        >
        @if ($showBookingErrors)
          @error('ten_co_dau')
            <div class="form-error">{{ $message }}</div>
          @enderror
        @endif
      </div>
      <div class="form-group">
        <label for="{{ $bookingFormContext }}_ten_chu_re">Tên chú rể *</label>
        <input
          id="{{ $bookingFormContext }}_ten_chu_re"
          name="ten_chu_re"
          type="text"
          placeholder="Trần Văn Hùng"
          value="{{ old('ten_chu_re') }}"
          class="@if($showBookingErrors) @error('ten_chu_re') is-invalid @enderror @endif"
          maxlength="150"
          required
        >
        @if ($showBookingErrors)
          @error('ten_chu_re')
            <div class="form-error">{{ $message }}</div>
          @enderror
        @endif
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="{{ $bookingFormContext }}_so_dien_thoai">Số điện thoại *</label>
        <input
          id="{{ $bookingFormContext }}_so_dien_thoai"
          name="so_dien_thoai"
          type="tel"
          placeholder="09xx xxx xxx"
          value="{{ old('so_dien_thoai') }}"
          class="@if($showBookingErrors) @error('so_dien_thoai') is-invalid @enderror @endif"
          maxlength="20"
          required
        >
        @if ($showBookingErrors)
          @error('so_dien_thoai')
            <div class="form-error">{{ $message }}</div>
          @enderror
        @endif
      </div>
      <div class="form-group">
        <label for="{{ $bookingFormContext }}_ngay_cuoi_du_kien">Dự kiến ngày cưới</label>
        <div class="booking-date-input">
          <input
            id="{{ $bookingFormContext }}_ngay_cuoi_du_kien"
            name="ngay_cuoi_du_kien"
            type="text"
            value="{{ $ngayCuoiDuKienValue }}"
            placeholder="dd/mm/yyyy"
            class="booking-datepicker @if($showBookingErrors) @error('ngay_cuoi_du_kien') is-invalid @enderror @endif"
            autocomplete="off"
          >
          <span class="booking-date-icon" aria-hidden="true">
            <i class="far fa-calendar-alt"></i>
          </span>
        </div>
        @if ($showBookingErrors)
          @error('ngay_cuoi_du_kien')
            <div class="form-error">{{ $message }}</div>
          @enderror
        @endif
      </div>
    </div>

    <div class="form-group">
      <label for="{{ $bookingFormContext }}_phim_truong_quan_tam">Phim trường quan tâm</label>
      <select
        id="{{ $bookingFormContext }}_phim_truong_quan_tam"
        name="phim_truong_quan_tam"
        class="booking-select @if($showBookingErrors) @error('phim_truong_quan_tam') is-invalid @enderror @endif"
        data-placeholder="Chọn phim trường bạn quan tâm"
      >
        <option value=""></option>
        @foreach ($phimTruongOptions as $option)
          <option value="{{ $option }}" @selected(old('phim_truong_quan_tam') === $option)>{{ $option }}</option>
        @endforeach
      </select>
      @if ($showBookingErrors)
        @error('phim_truong_quan_tam')
          <div class="form-error">{{ $message }}</div>
        @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="{{ $bookingFormContext }}_goi_dich_vu_quan_tam">Gói dịch vụ quan tâm</label>
      <select
        id="{{ $bookingFormContext }}_goi_dich_vu_quan_tam"
        name="goi_dich_vu_quan_tam"
        class="booking-select @if($showBookingErrors) @error('goi_dich_vu_quan_tam') is-invalid @enderror @endif"
        data-placeholder="Chọn gói dịch vụ bạn quan tâm"
      >
        <option value=""></option>
        @foreach ($goiDichVuOptions as $option)
          <option value="{{ $option }}" @selected(old('goi_dich_vu_quan_tam') === $option)>{{ $option }}</option>
        @endforeach
      </select>
      @if ($showBookingErrors)
        @error('goi_dich_vu_quan_tam')
          <div class="form-error">{{ $message }}</div>
        @enderror
      @endif
    </div>

    <div class="form-group">
      <label for="{{ $bookingFormContext }}_ghi_chu">Ghi chú / Ý tưởng concept</label>
      <textarea
        id="{{ $bookingFormContext }}_ghi_chu"
        name="ghi_chu"
        placeholder="Chia sẻ thêm về phong cách bạn mong muốn..."
        class="@if($showBookingErrors) @error('ghi_chu') is-invalid @enderror @endif"
        rows="4"
      >{{ old('ghi_chu') }}</textarea>
      @if ($showBookingErrors)
        @error('ghi_chu')
          <div class="form-error">{{ $message }}</div>
        @enderror
      @endif
    </div>

    <button type="submit" class="form-submit">Gửi đăng ký tư vấn <i class="fas fa-heart"></i></button>
    <p style="text-align:center;font-size:0.78rem;color:var(--ink-soft);margin-top:14px">Miễn phí · Không ràng buộc · Phản hồi trong 2 giờ</p>
  </form>
</div>

@once
  @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker3.min.css">
    <style>
      .booking-alert {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 14px;
        font-size: 0.95rem;
        line-height: 1.6;
      }

      .booking-alert-success {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.22);
        color: #166534;
      }

      .booking-alert-error {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.18);
        color: #b91c1c;
      }

      .booking-form .form-error {
        margin-top: 8px;
        color: #b91c1c;
        font-size: 0.84rem;
      }

      .booking-form .booking-date-input {
        position: relative;
      }

      .booking-form .booking-date-input input {
        padding-right: 48px;
        cursor: pointer;
        background: #fff;
      }

      .booking-form .booking-date-icon {
        position: absolute;
        top: 50%;
        right: 16px;
        transform: translateY(-50%);
        color: var(--ink-soft);
        pointer-events: none;
        font-size: 0.96rem;
      }

      .booking-form input.is-invalid,
      .booking-form textarea.is-invalid,
      .booking-form select.is-invalid + .select2 .select2-selection--single {
        border-color: rgba(239, 68, 68, 0.65) !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08);
      }

      .booking-form .select2-container {
        width: 100% !important;
      }

      .booking-form .select2-container--default .select2-selection--single {
        min-height: 56px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 14px;
        display: flex;
        align-items: center;
        padding: 0 16px;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
      }

      .booking-form .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        padding-right: 24px;
        line-height: 1.5;
        color: var(--ink);
      }

      .booking-form .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--ink-soft);
      }

      .booking-form .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 14px;
      }

      .booking-form .select2-container--default .select2-selection--single .select2-selection__clear {
        color: var(--ink-soft);
        margin-right: 8px;
      }

      .booking-form .select2-container--default.select2-container--focus .select2-selection--single,
      .booking-form .select2-container--default.select2-container--open .select2-selection--single {
        border-color: rgba(212, 175, 122, 0.85);
        box-shadow: 0 0 0 3px rgba(212, 175, 122, 0.16);
      }

      .select2-dropdown {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.14);
      }

      .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 10px;
        padding: 10px 12px;
      }

      .datepicker-dropdown {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 16px;
        overflow: hidden;
        padding: 10px 12px;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.14);
      }

      .datepicker table {
        width: 100%;
      }

      .datepicker table tr th,
      .datepicker table tr td {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        transition: background-color 0.2s ease, color 0.2s ease;
      }

      .datepicker table tr td.day:hover,
      .datepicker table tr td span:hover {
        background: rgba(212, 175, 122, 0.12);
      }

      .datepicker table tr td.today,
      .datepicker table tr td.today:hover {
        background: rgba(212, 175, 122, 0.18);
        color: #8c6239;
        border-color: transparent;
      }

      .datepicker table tr td.active.active,
      .datepicker table tr td.active.active:hover,
      .datepicker table tr td span.active.active,
      .datepicker table tr td span.active.active:hover {
        background: #d4af7a;
        border-color: #d4af7a;
        background-image: none;
        color: #fff;
      }

      .datepicker .datepicker-switch,
      .datepicker .next,
      .datepicker .prev {
        font-weight: 600;
      }
    </style>
  @endpush

  @push('scripts')
    <script src="{{ asset('user/js/plugins/jquery-3.3.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/locales/bootstrap-datepicker.vi.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var $ = window.jQuery || window.$;
        if (!$) {
          return;
        }

        if ($.fn.select2) {
          $('.booking-select').each(function () {
            var $el = $(this);
            if ($el.data('select2')) {
              return;
            }

            $el.select2({
              placeholder: $el.data('placeholder') || 'Chọn thông tin',
              allowClear: true,
              width: '100%'
            });
          });
        }

        if ($.fn.datepicker) {
          $('.booking-datepicker').each(function () {
            var $el = $(this);
            if ($el.data('datepicker')) {
              return;
            }

            $el.datepicker({
              format: 'dd/mm/yyyy',
              language: 'vi',
              autoclose: true,
              clearBtn: true,
              todayHighlight: true,
              orientation: 'bottom auto'
            });
          });
        }
      });
    </script>
  @endpush
@endonce
