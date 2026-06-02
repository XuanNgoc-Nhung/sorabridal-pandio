
<!doctype html>














  
  
  
  













  
    <!-- =========================================================
* Vuexy - Bootstrap Dashboard PRO | v3.0.0
==============================================================

* Product Page: https://themeforest.net/item/vuexy-vuejs-html-laravel-admin-dashboard-template/23328599
* Created by: Pixinvent

      * License: You must have a valid license purchased in order to legally use the theme for your project.
    
* Copyright Pixinvent (https://pixinvent.com)

=========================================================
 -->
    <!-- beautify ignore:start -->
  


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
    <title>Đăng nhập | Wedding Studio</title>
    <meta name="description" content="Đăng nhập Wedding Studio để quản lý dịch vụ chụp ảnh cưới, album, lịch hẹn và thông tin khách hàng nhanh chóng, chuyên nghiệp." />
    <meta name="keywords" content="wedding studio, ảnh cưới, chụp ảnh cưới, quản lý studio cưới, album cưới, lịch hẹn cưới" />
    <meta name="author" content="Wedding Studio" />
    <link rel="canonical" href="{{ route('login') }}" />
    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Đăng nhập | Wedding Studio" />
    <meta property="og:description" content="Truy cập hệ thống Wedding Studio để theo dõi lịch chụp, đơn hàng và chăm sóc khách hàng hiệu quả." />
    <meta property="og:url" content="{{ route('login') }}" />
    <meta property="og:site_name" content="Wedding Studio" />
    <meta property="og:image" content="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Đăng nhập | Wedding Studio" />
    <meta name="twitter:description" content="Đăng nhập Wedding Studio để quản lý toàn bộ hoạt động studio ảnh cưới." />
    <meta name="twitter:image" content="{{ asset('assets/img/favicon/favicon.ico') }}" />
    
    
      
      <script>
        (function (w, d, s, l, i) {
          w[l] = w[l] || [];
          w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
          var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
          j.async = true;
          j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
          f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5J3LMKC');
      </script>
      
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />

    <!-- Fonts: Tahoma stack — demo.css -->

    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />

    
    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    
    <link rel="stylesheet" href="../../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    
    <!-- Vendors CSS -->
    
      <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    
    <!-- endbuild -->

    <!-- Page CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />

    <!-- Theme đăng nhập: màu hồng nhạt -->
    <style>
      .auth-theme-pink body,
      .auth-theme-pink {
        background: linear-gradient(135deg, #fef7f8 0%, #fce7ec 50%, #fdf2f4 100%) !important;
        min-height: 100vh;
      }
      .auth-theme-pink .authentication-wrapper {
        background: transparent;
      }
      .auth-theme-pink .authentication-inner .card {
        border: 1px solid rgba(236, 72, 153, 0.15);
        box-shadow: 0 4px 24px rgba(236, 72, 153, 0.08), 0 0 0 1px rgba(255, 255, 255, 0.8);
        border-radius: 1rem;
      }
      .auth-theme-pink .authentication-inner .card-body {
        background: rgba(255, 255, 255, 0.85);
        border-radius: 1rem;
      }
      .auth-theme-pink .app-brand-logo .text-primary,
      .auth-theme-pink .app-brand-text.text-heading {
        color: #be185d !important;
      }
      .auth-theme-pink .authentication-inner .card .btn-primary {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        border: none;
        box-shadow: 0 2px 12px rgba(236, 72, 153, 0.35);
      }
      .auth-theme-pink .authentication-inner .card .btn-primary:hover {
        background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
        box-shadow: 0 4px 16px rgba(219, 39, 119, 0.4);
      }
      .auth-theme-pink .form-label {
        color: #831843;
      }
      .auth-theme-pink .form-control:focus {
        border-color: rgba(236, 72, 153, 0.5);
        box-shadow: 0 0 0 0.2rem rgba(236, 72, 153, 0.15);
      }
      .auth-theme-pink .authentication-inner::before,
      .auth-theme-pink .authentication-inner::after {
        background: #f9a8d4 !important;
        opacity: 0.4;
      }
      .auth-theme-pink .alert-danger {
        background: #fdf2f4;
        border-color: rgba(236, 72, 153, 0.3);
        color: #9d174d;
      }
      .auth-theme-pink .input-group-text {
        background: #fdf2f4;
        border-color: #fce7ec;
        color: #be185d;
      }
    </style>

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    
      <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
      <script src="../../assets/vendor/js/template-customizer.js"></script>
    
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    
      <script src="../../assets/js/config.js"></script>
    
  </head>

  <body class="auth-theme-pink">
    
      
      <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5J3LMKC" height="0" width="0" style="display: none; visibility: hidden"></iframe></noscript>
      
    
    <!-- Content -->
  
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6">
        <!-- Login -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-6">
              <a href="index.html" class="app-brand-link">
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
            <!-- /Logo -->
            <h4 class="mb-1">Hệ thống Quản trị viên</h4>
            <p class="mb-6">Vui lòng đăng nhập bằng tài khoản quản trị của bạn.</p>

            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                @foreach ($errors->all() as $err)
                  <div>{{ $err }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            <form id="formAuthentication" class="mb-4" action="{{ route('login.post') }}" method="POST" novalidate>
              @csrf
              <div class="mb-6">
                <label for="email" class="form-label">Email hoặc Tên đăng nhập</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Nhập email hoặc tên đăng nhập" autofocus value="{{ old('email') }}" />
                <div class="invalid-feedback">Vui lòng nhập email hoặc tên đăng nhập (ít nhất 6 ký tự).</div>
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password">Mật khẩu</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
                <div class="invalid-feedback">Vui lòng nhập mật khẩu (ít nhất 6 ký tự).</div>
              </div>
              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">Đăng nhập</button>
              </div>
              <p class="text-center mb-0">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="text-primary fw-semibold">Đăng ký</a>
              </p>
            </form>
          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>
  </div>

  <!-- / Content -->

    
    
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>

    <!-- Page JS: validate form đăng nhập bằng JS thuần -->
    <script>
      (function () {
        var form = document.getElementById('formAuthentication');
        if (!form) return;

        function showError(input, message) {
          input.classList.add('is-invalid');
          var fb = input.closest('.mb-6')?.querySelector('.invalid-feedback') || input.parentElement?.querySelector('.invalid-feedback');
          if (fb) fb.textContent = message;
        }

        function clearError(input) {
          input.classList.remove('is-invalid');
          var fb = input.closest('.mb-6')?.querySelector('.invalid-feedback') || input.parentElement?.querySelector('.invalid-feedback');
          if (fb) fb.textContent = '';
        }

        function validateEmailUsername(value) {
          value = (value || '').trim();
          if (value.length === 0) return 'Vui lòng nhập email hoặc tên đăng nhập.';
          if (value.length < 6) return 'Email hoặc tên đăng nhập phải có ít nhất 6 ký tự.';
          return null;
        }

        function validatePassword(value) {
          value = value || '';
          if (value.length === 0) return 'Vui lòng nhập mật khẩu.';
          if (value.length < 6) return 'Mật khẩu phải có ít nhất 6 ký tự.';
          return null;
        }

        form.querySelectorAll('.form-control').forEach(function (el) {
          el.addEventListener('input', function () { clearError(this); });
          el.addEventListener('blur', function () { clearError(this); });
        });

        form.addEventListener('submit', function (e) {
          e.preventDefault();
          var emailEl = form.querySelector('#email');
          var passwordEl = form.querySelector('#password');
          emailEl.classList.remove('is-invalid');
          passwordEl.classList.remove('is-invalid');

          var errEmail = validateEmailUsername(emailEl.value);
          var errPass = validatePassword(passwordEl.value);

          if (errEmail) showError(emailEl, errEmail);
          if (errPass) {
            passwordEl.classList.add('is-invalid');
            var passFb = passwordEl.closest('.mb-6')?.querySelector('.invalid-feedback');
            if (passFb) passFb.textContent = errPass;
          }

          if (!errEmail && !errPass) form.submit();
        });
      })();
    </script>
  </body>
</html>

  <!-- beautify ignore:end -->

