<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Đăng nhập bằng email hoặc số điện thoại, trả về Bearer token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'email.required' => 'Vui lòng nhập email hoặc số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $login = $request->input('email');
        $password = $request->input('password');
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        $user = $isEmail
            ? User::where('email', $login)->first()
            : User::where('phone', preg_replace('/\s+/', '', $login))->first();

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $password])) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không đúng.'],
            ]);
        }

        $thongBaoRoute = $user->routeThongBaoTrangThai();
        if ($thongBaoRoute !== null) {
            throw ValidationException::withMessages([
                'email' => [User::STATUS_OPTIONS[$user->status] ?? 'Tài khoản không được phép truy cập hệ thống.'],
            ]);
        }

        $deviceName = $request->input('device_name', 'api-client');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->formatUser($user),
            ],
        ]);
    }

    /**
     * POST /api/logout
     * Thu hồi token hiện tại.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công.',
        ]);
    }

    /**
     * GET /api/me
     * Thông tin người dùng đang đăng nhập.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->formatUser($request->user()),
            ],
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => (int) $user->role,
            'role_label' => $user->role_label,
        ];
    }
}
