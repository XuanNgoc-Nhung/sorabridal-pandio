
<!doctype html>
<html
  lang="vi"
  class="layout-wide customizer-hide auth-theme-pink"
  dir="ltr" data-skin="default" data-bs-theme="light"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="index, follow" />
    <title>Đăng ký | Wedding Studio</title>
    <meta name="description" content="Đăng ký tài khoản Wedding Studio để bắt đầu quản lý khách hàng, lịch hẹn chụp ảnh cưới và dịch vụ studio hiệu quả hơn." />
    <meta name="keywords" content="đăng ký wedding studio, studio cưới, quản lý khách hàng cưới, lịch hẹn chụp cưới, dịch vụ ảnh cưới" />
    <meta name="author" content="Wedding Studio" />
    <link rel="canonical" href="{{ route('register') }}" />
    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Đăng ký | Wedding Studio" />
    <meta property="og:description" content="Tạo tài khoản Wedding Studio để quảng bá dịch vụ và quản lý quy trình làm việc chuyên nghiệp." />
    <meta property="og:url" content="{{ route('register') }}" />
    <meta property="og:site_name" content="Wedding Studio" />
    <meta property="og:image" content="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Đăng ký | Wedding Studio" />
    <meta name="twitter:description" content="Đăng ký Wedding Studio để khởi tạo hệ thống quản lý studio cưới nhanh chóng." />
    <meta name="twitter:image" content="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />
    <style>
      .auth-theme-pink body, .auth-theme-pink { background: linear-gradient(135deg, #fef7f8 0%, #fce7ec 50%, #fdf2f4 100%) !important; min-height: 100vh; }
      .auth-theme-pink .authentication-wrapper { background: transparent; }
      .auth-theme-pink .authentication-inner .card { border: 1px solid rgba(236, 72, 153, 0.15); box-shadow: 0 4px 24px rgba(236, 72, 153, 0.08), 0 0 0 1px rgba(255, 255, 255, 0.8); border-radius: 1rem; }
      .auth-theme-pink .authentication-inner .card-body { background: rgba(255, 255, 255, 0.85); border-radius: 1rem; }
      .auth-theme-pink .app-brand-logo .text-primary, .auth-theme-pink .app-brand-text.text-heading { color: #be185d !important; }
      .auth-theme-pink .authentication-inner .card .btn-primary { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border: none; box-shadow: 0 2px 12px rgba(236, 72, 153, 0.35); }
      .auth-theme-pink .authentication-inner .card .btn-primary:hover { background: linear-gradient(135deg, #db2777 0%, #be185d 100%); box-shadow: 0 4px 16px rgba(219, 39, 119, 0.4); }
      .auth-theme-pink .form-label { color: #831843; }
      .auth-theme-pink .form-control:focus { border-color: rgba(236, 72, 153, 0.5); box-shadow: 0 0 0 0.2rem rgba(236, 72, 153, 0.15); }
      .auth-theme-pink .authentication-inner::before, .auth-theme-pink .authentication-inner::after { background: #f9a8d4 !important; opacity: 0.4; }
      .auth-theme-pink .alert-danger { background: #fdf2f4; border-color: rgba(236, 72, 153, 0.3); color: #9d174d; }
      .auth-theme-pink .input-group-text { background: #fdf2f4; border-color: #fce7ec; color: #be185d; }
    </style>
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/vendor/js/template-customizer.js"></script>
    <script src="../../assets/js/config.js"></script>
  </head>
  <body class="auth-theme-pink">
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6">
        <div class="card">
          <div class="card-body">
            <div class="app-brand justify-content-center mb-6">
              <a href="{{ url('/') }}" class="app-brand-link">
                <span class="app-brand-logo demo">
                  <span class="text-primary">
                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="currentColor" />
                      <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                      <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="currentColor" />
                    </svg>
                  </span>
                </span>
                <span class="app-brand-text demo text-heading fw-bold">Wedding Studio</span>
              </a>
            </div>
            <h4 class="mb-1">Đăng ký tài khoản</h4>
            <p class="mb-6">Điền thông tin bên</p>

            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                @foreach ($errors->all() as $err)
                  <div>{{ $err }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            <form id="formRegister" class="mb-4" action="{{ route('register.post') }}" method="POST" novalidate>
              @csrf
              <div class="mb-6">
                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Nguyễn Văn A" value="{{ old('name') }}" />
                <div class="invalid-feedback">Vui lòng nhập họ tên.</div>
              </div>
              <div class="mb-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" />
                <div class="invalid-feedback">Email không hợp lệ.</div>
              </div>
              <div class="mb-6">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="0912345678" value="{{ old('phone') }}" />
                <div class="invalid-feedback">Số điện thoại không hợp lệ (10–11 chữ số).</div>
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password">Mật khẩu <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
                <div class="invalid-feedback">Mật khẩu ít nhất 6 ký tự.</div>
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password_confirmation">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
                <div class="invalid-feedback">Mật khẩu xác nhận không khớp.</div>
              </div>
              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">Đăng ký</button>
              </div>
              <p class="text-center mb-0">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-primary fw-semibold">Đăng nhập</a>
              </p>
            </form>
            <p class="text-center text-muted small mt-4 mb-0">Nhập email hoặc số điện thoại (ít nhất một trong hai).</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../../assets/vendor/libs/popper/popper.js"></script>
  <script src="../../assets/vendor/js/bootstrap.js"></script>
  <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script>
  <script src="../../assets/vendor/js/menu.js"></script>
  <script src="../../assets/js/main.js"></script>
  <script>
    (function () {
      var form = document.getElementById('formRegister');
      if (!form) return;

      function showError(input, message) {
        input.classList.add('is-invalid');
        var fb = input.closest('.mb-6') && input.closest('.mb-6').querySelector('.invalid-feedback');
        if (fb) fb.textContent = message;
      }
      function clearError(input) {
        input.classList.remove('is-invalid');
        var fb = input.closest('.mb-6') && input.closest('.mb-6').querySelector('.invalid-feedback');
        if (fb) fb.textContent = '';
      }

      function isValidEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((v || '').trim());
      }
      function isValidPhone(v) {
        return /^[0-9]{10,11}$/.test((v || '').replace(/\s/g, ''));
      }

      form.querySelectorAll('.form-control').forEach(function (el) {
        el.addEventListener('input', function () { clearError(this); });
      });

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var nameEl = form.querySelector('#name');
        var emailEl = form.querySelector('#email');
        var phoneEl = form.querySelector('#phone');
        var passEl = form.querySelector('#password');
        var passConfirmEl = form.querySelector('#password_confirmation');

        [nameEl, emailEl, phoneEl, passEl, passConfirmEl].forEach(function (el) { el.classList.remove('is-invalid'); });

        var email = (emailEl.value || '').trim();
        var phone = (phoneEl.value || '').replace(/\s/g, '');
        var hasError = false;

        if (!(nameEl.value || '').trim()) {
          showError(nameEl, 'Vui lòng nhập họ tên.');
          hasError = true;
        }
        if (email && !isValidEmail(email)) {
          showError(emailEl, 'Email không hợp lệ.');
          hasError = true;
        }
        if (phone && !isValidPhone(phone)) {
          showError(phoneEl, 'Số điện thoại không hợp lệ (10–11 chữ số).');
          hasError = true;
        }
        if (!email && !phone) {
          showError(emailEl, 'Vui lòng nhập email hoặc số điện thoại.');
          hasError = true;
        }
        if ((passEl.value || '').length < 6) {
          showError(passEl, 'Mật khẩu ít nhất 6 ký tự.');
          hasError = true;
        }
        if (passEl.value !== passConfirmEl.value) {
          showError(passConfirmEl, 'Mật khẩu xác nhận không khớp.');
          hasError = true;
        }
        if (!hasError) form.submit();
      });
    })();
  </script>
  </body>
</html>
