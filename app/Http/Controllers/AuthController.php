<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Thông báo tài khoản đã nghỉ việc.
     */
    public function showDaNghiViecThongBao()
    {
        return view('auth.trang-thai-tai-khoan', [
            'title' => 'Tài khoản đã nghỉ việc',
            'message' => 'Tài khoản của bạn đã được đánh dấu là đã nghỉ việc nên không thể đăng nhập vào hệ thống. Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là nhầm lẫn.',
            'accent' => 'danger',
            'icon' => 'tabler-user-off',
        ]);
    }

    /**
     * Thông báo tài khoản bị giới hạn quyền truy cập.
     */
    public function showGioiHanQuyenThongBao()
    {
        return view('auth.trang-thai-tai-khoan', [
            'title' => 'Tài khoản bị giới hạn quyền truy cập',
            'message' => 'Tài khoản của bạn hiện đang bị giới hạn quyền truy cập hệ thống. Vui lòng liên hệ quản trị viên để được hỗ trợ.',
            'accent' => 'warning',
            'icon' => 'tabler-lock-access',
        ]);
    }

    /**
     * Hiển thị form đăng ký.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký (bằng email hoặc số điện thoại).
     */
    public function register(Request $request)
    {
        $request->merge([
            'phone' => preg_replace('/\s+/', '', (string) $request->input('phone', '')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,11}$/',
                Rule::unique('users', 'phone'),
            ],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.regex' => 'Số điện thoại phải 10 hoặc 11 chữ số.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $email = $request->filled('email') ? $request->input('email') : null;
        $phone = $request->filled('phone') ? $request->input('phone') : null;

        if (! $email && ! $phone) {
            return back()->withErrors(['email' => 'Vui lòng nhập email hoặc số điện thoại.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'phone' => $phone,
            'password' => $validated['password'],
            'role' => (int) VaiTro::maMacDinhNhanVien(),
        ]);

        NhanVien::create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.index'));
    }

    /**
     * Xử lý đăng nhập (bằng email hoặc số điện thoại).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập email hoặc số điện thoại.',
        ]);

        $login = $request->input('email');
        $password = $request->input('password');
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        $user = $isEmail
            ? User::where('email', $login)->first()
            : User::where('phone', preg_replace('/\s+/', '', $login))->first();

        if ($user && Auth::getProvider()->validateCredentials($user, ['password' => $password])) {
            Auth::login($user);
            $request->session()->regenerate();

            $user->refresh();
            $thongBaoRoute = $user->routeThongBaoTrangThai();
            if ($thongBaoRoute !== null) {
                return redirect()->route($thongBaoRoute);
            }

            return redirect()->intended(route('admin.index'));
        }

        return back()->withErrors([
            'email' => 'Email/số điện thoại hoặc mật khẩu không đúng.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
