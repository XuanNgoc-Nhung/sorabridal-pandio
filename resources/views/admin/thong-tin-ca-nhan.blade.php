@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
      @if(session('success_password'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
          {{ session('success_password') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if($errors->has('password_current') || $errors->has('password_new') || $errors->has('password_new_confirmation'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
          <ul class="mb-0 list-unstyled">
            @foreach(['password_current', 'password_new', 'password_new_confirmation'] as $f)
              @foreach($errors->get($f) as $err)
                <li>{{ $err }}</li>
              @endforeach
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      <div class="nav-align-top">
        <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2" role="tablist">
          <li class="nav-item">
            <a class="nav-link active waves-effect waves-light" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-thong-tin" role="tab" aria-selected="true">
              <i class="icon-base ti tabler-user icon-sm me-1_5"></i> Thông tin cá nhân
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link waves-effect waves-light" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-bao-mat" role="tab" aria-selected="false">
              <i class="icon-base ti tabler-lock icon-sm me-1_5"></i> Bảo mật
            </a>
          </li>
          {{-- <li class="nav-item">
            <a class="nav-link waves-effect waves-light" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-lich-lam-viec" role="tab" aria-selected="false">
              <i class="icon-base ti tabler-calendar icon-sm me-1_5"></i> Lịch làm việc
            </a>
          </li> --}}
          <li class="nav-item">
            <a class="nav-link waves-effect waves-light" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#tab-khac" role="tab" aria-selected="false">
              <i class="icon-base ti tabler-dots icon-sm me-1_5"></i> Khác
            </a>
          </li>
        </ul>

        <div class="tab-content m-0 p-0">
          {{-- Tab Thông tin cá nhân --}}
          <div class="tab-pane fade show active" id="tab-thong-tin" role="tabpanel">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Thông tin cá nhân</h5>
                <p class="my-0 card-subtitle">Cập nhật thông tin hồ sơ của bạn</p>
              </div>
              <div class="card-body">
                @if(session('success'))
                  <div class="alert alert-success alert-dismissible mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                @endif
                @if($errors->any())
                  <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                    <ul class="mb-0 list-unstyled">
                      @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                      @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                @endif

                <form action="{{ route('admin.thong-tin-ca-nhan.update') }}" method="post" enctype="multipart/form-data" id="form-thong-tin-ca-nhan">
                  @csrf
                  @method('PUT')

                  <div class="row align-items-start g-4">
                    {{-- Cột trái: Avatar (chỉ khi có nhanVien) --}}
                    @if($user->nhanVien)
                    <div class="col-12 col-md-4 col-lg-3 text-center">
                      <div class="d-inline-block position-relative">
                        <img id="avatar-preview" src="{{ $user->nhanVien->hinh_anh ? asset('storage/' . $user->nhanVien->hinh_anh) : '' }}" alt="Avatar" class="rounded-circle object-fit-cover border border-2 border-secondary w-100 {{ $user->nhanVien->hinh_anh ? '' : 'd-none' }}" style="max-width:100%;aspect-ratio:1/1;object-fit:cover;" onerror="this.style.display='none'; document.getElementById('avatar-placeholder').classList.remove('d-none');">
                        <div id="avatar-placeholder" class="rounded-circle bg-label-secondary d-flex align-items-center justify-content-center border border-2 border-secondary {{ $user->nhanVien->hinh_anh ? 'd-none' : '' }}" style="width:200px;height:200px;max-width:100%;margin:0 auto;"><i class="icon-base ti tabler-user display-1"></i></div>
                      </div>
                      <input type="file" id="avatar-input" name="hinh_anh" accept="image/*" class="d-none" aria-label="Chọn ảnh đại diện">
                      <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="document.getElementById('avatar-input').click();">
                        <i class="icon-base ti tabler-photo-plus me-1"></i> Thay đổi ảnh
                      </button>
                      @error('hinh_anh')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    @endif

                    {{-- Cột phải: Các input thông tin --}}
                    <div class="col">
                      {{-- Thông tin từ bảng users --}}
                      <h6 class="text-muted border-bottom pb-2 mb-3">Tài khoản (users)</h6>
                      <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                          <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Nhập họ và tên">
                          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Email <span class="text-danger">*</span></label>
                          <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Nhập email">
                          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Số điện thoại</label>
                          <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Nhập số điện thoại">
                          @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Vai trò</label>
                          <input type="text" class="form-control bg-light" value="{{ $user->role_label ?? '' }}" readonly>
                        </div>
                      </div>

                      @if($user->nhanVien)
                      {{-- Thông tin từ bảng nhan_vien (có thể cập nhật) --}}
                      <h6 class="text-muted border-bottom pb-2 mb-3 mt-4">Hồ sơ nhân viên (nhan_vien)</h6>
                      <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Giới tính</label>
                          <select class="select2-admin form-select @error('gioi_tinh') is-invalid @enderror" name="gioi_tinh" data-placeholder="Chọn giới tính">
                            <option value="">— Chọn —</option>
                            <option value="nam" {{ old('gioi_tinh', $user->nhanVien->gioi_tinh) === 'nam' ? 'selected' : '' }}>Nam</option>
                            <option value="nu" {{ old('gioi_tinh', $user->nhanVien->gioi_tinh) === 'nu' ? 'selected' : '' }}>Nữ</option>
                            <option value="khac" {{ old('gioi_tinh', $user->nhanVien->gioi_tinh) === 'khac' ? 'selected' : '' }}>Khác</option>
                          </select>
                          @error('gioi_tinh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Ngày sinh</label>
                          <input type="text" class="flatpickr-date-admin form-control @error('ngay_sinh') is-invalid @enderror" name="ngay_sinh" value="{{ old('ngay_sinh', $user->nhanVien->ngay_sinh?->format('Y-m-d') ?? '') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                          @error('ngay_sinh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">CCCD/CMND</label>
                          <input type="text" class="form-control @error('cccd') is-invalid @enderror" name="cccd" value="{{ old('cccd', $user->nhanVien->cccd ?? '') }}" placeholder="Số CCCD/CMND" maxlength="20">
                          @error('cccd')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Vị trí làm việc</label>
                          <input type="text" class="form-control bg-light" value="{{ $user->nhanVien->vi_tri_lam_viec ?? '—' }}" readonly>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Ngày vào công ty</label>
                          <input type="text" class="form-control bg-light" value="{{ $user->nhanVien->ngay_vao_cong_ty ? $user->nhanVien->ngay_vao_cong_ty->format('d/m/Y') : '—' }}" readonly>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                          <label class="form-label">Ngày ký hợp đồng</label>
                          <input type="text" class="form-control bg-light" value="{{ $user->nhanVien->ngay_ky_hop_dong ? $user->nhanVien->ngay_ky_hop_dong->format('d/m/Y') : '—' }}" readonly>
                        </div>
                      </div>
                      @else
                      <p class="text-muted mb-0 mt-3">Bạn chưa có hồ sơ nhân viên (bảng nhan_vien). Chỉ có thể cập nhật thông tin tài khoản ở trên.</p>
                      @endif

                      <div class="row g-3 mt-3">
                        <div class="col-12">
                          <button type="submit" class="btn btn-primary waves-effect">Lưu thay đổi</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          {{-- Tab Bảo mật --}}
          <div class="tab-pane fade" id="tab-bao-mat" role="tabpanel">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Bảo mật</h5>
                <p class="my-0 card-subtitle">Quản lý mật khẩu và cài đặt bảo mật</p>
              </div>
              <div class="card-body">
                <div id="doi-mat-khau-alert" class="d-none mb-4"></div>
                <form action="{{ route('admin.doi-mat-khau') }}" method="post" id="form-doi-mat-khau">
                  @csrf
                  @method('PUT')
                  <div class="row g-3">
                    <div class="col-12 col-md-4 form-password-toggle">
                      <label class="form-label" for="password_current">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <input type="password" class="form-control @error('password_current') is-invalid @enderror" name="password_current" id="password_current" placeholder="Nhập mật khẩu hiện tại" autocomplete="current-password">
                        <span class="input-group-text cursor-pointer" tabindex="0" role="button" aria-label="Hiện/ẩn mật khẩu"><i class="icon-base ti tabler-eye-off"></i></span>
                      </div>
                      <div class="invalid-feedback" data-field="password_current">@error('password_current'){{ $message }}@enderror</div>
                    </div>
                    <div class="col-12 col-md-4 form-password-toggle">
                      <label class="form-label" for="password_new">Mật khẩu mới <span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <input type="password" class="form-control @error('password_new') is-invalid @enderror" name="password_new" id="password_new" placeholder="Nhập mật khẩu mới" autocomplete="new-password">
                        <span class="input-group-text cursor-pointer" tabindex="0" role="button" aria-label="Hiện/ẩn mật khẩu"><i class="icon-base ti tabler-eye-off"></i></span>
                      </div>
                      <div class="invalid-feedback" data-field="password_new">@error('password_new'){{ $message }}@enderror</div>
                      <small class="text-muted d-block mt-1">Ít nhất 8 ký tự, có số, chữ hoa, chữ thường và ký tự đặc biệt</small>
                    </div>
                    <div class="col-12 col-md-4 form-password-toggle">
                      <label class="form-label" for="password_new_confirmation">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <input type="password" class="form-control @error('password_new_confirmation') is-invalid @enderror" name="password_new_confirmation" id="password_new_confirmation" placeholder="Nhập lại mật khẩu mới" autocomplete="new-password">
                        <span class="input-group-text cursor-pointer" tabindex="0" role="button" aria-label="Hiện/ẩn mật khẩu"><i class="icon-base ti tabler-eye-off"></i></span>
                      </div>
                      <div class="invalid-feedback" data-field="password_new_confirmation">@error('password_new_confirmation'){{ $message }}@enderror</div>
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-primary waves-effect" id="btn-doi-mat-khau">Đổi mật khẩu</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          {{-- Tab Lịch làm việc --}}
          <div class="tab-pane fade" id="tab-lich-lam-viec" role="tabpanel">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Lịch làm việc</h5>
                <p class="my-0 card-subtitle">Xem và quản lý lịch làm việc của bạn</p>
              </div>
              <div class="card-body">
                <p class="text-muted mb-0">Nội dung lịch làm việc sẽ được hiển thị tại đây. Bạn có thể tích hợp lịch, đặt lịch hẹn hoặc xem ca làm việc.</p>
              </div>
            </div>
          </div>

          {{-- Tab Khác --}}
          <div class="tab-pane fade" id="tab-khac" role="tabpanel">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Khác</h5>
                <p class="my-0 card-subtitle">Các cài đặt và tùy chọn khác</p>
              </div>
              <div class="card-body">
                <p class="text-muted mb-0">Các tùy chọn bổ sung sẽ được hiển thị tại đây.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
(function () {
  var input = document.getElementById('avatar-input');
  if (!input) return;
  input.addEventListener('change', function (e) {
    var file = e.target.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    var preview = document.getElementById('avatar-preview');
    var placeholder = document.getElementById('avatar-placeholder');
    if (preview && placeholder) {
      preview.src = URL.createObjectURL(file);
      preview.classList.remove('d-none');
      preview.style.display = '';
      placeholder.classList.add('d-none');
    }
  });
})();

(function () {
  document.querySelectorAll('#tab-bao-mat .form-password-toggle').forEach(function (wrap) {
    var input = wrap.querySelector('input[type="password"], input[type="text"]');
    var toggle = wrap.querySelector('.input-group-text.cursor-pointer');
    var icon = toggle ? toggle.querySelector('.ti') : null;
    if (!input || !toggle) return;
    function toggleVisibility() {
      var isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      if (icon) {
        icon.classList.remove(isPass ? 'tabler-eye-off' : 'tabler-eye');
        icon.classList.add(isPass ? 'tabler-eye' : 'tabler-eye-off');
      }
    }
    toggle.addEventListener('click', toggleVisibility);
    toggle.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleVisibility(); } });
  });
})();

(function () {
  var form = document.getElementById('form-doi-mat-khau');
  if (!form) return;

  function showAlert(type, html) {
    var el = document.getElementById('doi-mat-khau-alert');
    if (!el) return;
    el.className = 'alert alert-' + type + ' alert-dismissible mb-4';
    el.innerHTML = html;
    el.classList.remove('d-none');
  }

  function clearFieldErrors() {
    [].slice.call(form.querySelectorAll('.is-invalid')).forEach(function (el) {
      el.classList.remove('is-invalid');
    });
    form.querySelectorAll('.input-group.is-invalid').forEach(function (el) {
      el.classList.remove('is-invalid');
    });
    form.querySelectorAll('[data-field]').forEach(function (div) {
      div.textContent = '';
    });
  }

  function setFieldError(name, message) {
    var input = form.querySelector('[name="' + name + '"]');
    var fb = form.querySelector('[data-field="' + name + '"]');
    if (input) {
      input.classList.add('is-invalid');
      var group = input.closest('.input-group');
      if (group) group.classList.add('is-invalid');
    }
    if (fb) fb.textContent = message;
  }

  function validatePasswordNew(value) {
    if (!value || value.length < 8) return 'Mật khẩu mới phải có ít nhất 8 ký tự.';
    if (!/[a-z]/.test(value)) return 'Mật khẩu mới phải có ít nhất một chữ thường.';
    if (!/[A-Z]/.test(value)) return 'Mật khẩu mới phải có ít nhất một chữ hoa.';
    if (!/\d/.test(value)) return 'Mật khẩu mới phải có ít nhất một chữ số.';
    if (!/[^A-Za-z0-9]/.test(value)) return 'Mật khẩu mới phải có ít nhất một ký tự đặc biệt.';
    return '';
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var alertEl = document.getElementById('doi-mat-khau-alert');
    if (alertEl) { alertEl.classList.add('d-none'); alertEl.innerHTML = ''; }
    clearFieldErrors();

    var current = (form.querySelector('[name="password_current"]') || {}).value || '';
    var newPass = (form.querySelector('[name="password_new"]') || {}).value || '';
    var confirmPass = (form.querySelector('[name="password_new_confirmation"]') || {}).value || '';

    var hasError = false;
    if (!current.trim()) { setFieldError('password_current', 'Vui lòng nhập mật khẩu hiện tại.'); hasError = true; }
    if (!newPass) { setFieldError('password_new', 'Vui lòng nhập mật khẩu mới.'); hasError = true; }
    else {
      var msg = validatePasswordNew(newPass);
      if (msg) { setFieldError('password_new', msg); hasError = true; }
    }
    if (newPass !== confirmPass) { setFieldError('password_new_confirmation', 'Xác nhận mật khẩu mới không khớp.'); hasError = true; }
    if (hasError) return;

    var btn = document.getElementById('btn-doi-mat-khau');
    if (btn) { btn.disabled = true; btn.textContent = 'Đang xử lý...'; }

    var fd = new FormData(form);
    fd.set('_method', 'PUT');

    fetch(form.action, {
      method: 'POST',
      body: fd,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, status: res.status, data: data };
        }).catch(function () {
          return { ok: res.ok, status: res.status, data: {} };
        });
      })
      .then(function (result) {
        if (btn) { btn.disabled = false; btn.textContent = 'Đổi mật khẩu'; }
        if (result.ok) {
          showAlert('success', result.data.message || 'Đã đổi mật khẩu thành công.');
          form.reset();
          clearFieldErrors();
        } else if (result.status === 422 && result.data.errors) {
          var list = [];
          Object.keys(result.data.errors).forEach(function (key) {
            var msgs = result.data.errors[key];
            if (msgs && msgs[0]) {
              setFieldError(key, msgs[0]);
              list.push(msgs[0]);
            }
          });
          if (list.length) showAlert('danger', '<ul class="mb-0 list-unstyled">' + list.map(function (m) { return '<li>' + m + '</li>'; }).join('') + '</ul>');
        } else {
          showAlert('danger', result.data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
      })
      .catch(function () {
        if (btn) { btn.disabled = false; btn.textContent = 'Đổi mật khẩu'; }
        showAlert('danger', 'Không thể kết nối. Vui lòng thử lại.');
      });
  });
})();
</script>
@endpush
