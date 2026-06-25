<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chặn người dùng đã nghỉ việc hoặc bị giới hạn quyền truy cập
 * trên mọi request web/API có đăng nhập.
 */
class CheckUserStatus
{
    /** @var array<string> */
    private const SKIP_ROUTES = [
        'auth.da-nghi-viec',
        'auth.gioi-han-quyen',
        'logout',
        'dang-xuat',
        'login',
        'login.post',
        'register',
        'register.post',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            return $next($request);
        }

        $user->refresh();

        $routeName = $request->route()?->getName();
        if ($routeName !== null && in_array($routeName, self::SKIP_ROUTES, true)) {
            return $next($request);
        }

        $thongBaoRoute = $user->routeThongBaoTrangThai();
        if ($thongBaoRoute === null) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'success' => false,
                'message' => User::STATUS_OPTIONS[$user->status] ?? 'Tài khoản không được phép truy cập hệ thống.',
            ], 403);
        }

        return redirect()->route($thongBaoRoute);
    }
}
