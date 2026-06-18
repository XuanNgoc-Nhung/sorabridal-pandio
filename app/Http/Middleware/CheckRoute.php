<?php

namespace App\Http\Middleware;

use App\Models\VaiTro;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chỉ kiểm tra ds_menu với các route thuộc sidebar phân quyền (config admin_menu).
 * Route admin khác (CRUD/API phụ không nằm trong menu đó) không bị chặn bởi ds_menu.
 * User có ma_vai_tro admin (mặc định mã 1) được phép truy cập mọi route.
 */
class CheckRoute
{
    private const UNAUTHORIZED_ROUTE = 'admin.chua-duoc-phan-quyen';

    /** Route phụ (API/modal) được phép nếu user đã có route nền tương ứng trong ds_menu. */
    private const EXTRA_ROUTES_BY_BASE = [
        'admin.khach-hang.tao-hop-dong' => [
            'admin.khach-hang.tao-hop-dong-canh-bao',
            'admin.khach-hang.store-hop-dong-cuoi',
            'admin.khach-hang.tao-hop-dong.cap-nhat-buoc-1',
            'admin.khach-hang.tao-hop-dong.cap-nhat-buoc-2',
            'admin.khach-hang.tao-hop-dong.cap-nhat-buoc-3',
            'admin.khach-hang.chinh-sua-hop-dong-cuoi',
        ],
        'admin.khach-hang.danh-sach-hop-dong-cuoi' => [
            'admin.khach-hang.chinh-sua-hop-dong-cuoi',
            'admin.khach-hang.hop-dong-cuoi.dieu-phoi',
            'admin.khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay',
            'admin.khach-hang.hop-dong-cuoi.thanh-toan',
            'admin.khach-hang.hop-dong-cuoi.thanh-toan.luu',
            'admin.khach-hang.tao-hop-dong.cap-nhat-buoc-1',
            'admin.khach-hang.tao-hop-dong.cap-nhat-buoc-2',
            'admin.khach-hang.tao-hop-dong.cap-nhat-buoc-3',
            'admin.khach-hang.tao-hop-dong.kiem-tra-ma-giam-gia',
        ],
        'admin.lich-chup' => [
            'admin.lich-chup.data',
            'admin.lich-chup.danh-sach',
            'admin.lich-chup.chi-tiet-ngay',
            'admin.lich-chup.hop-dong-chua-phan-ngay',
            'admin.lich-chup.chua-phan-cong',
            'admin.lich-chup.hop-dong-dieu-phoi-data',
            'admin.lich-chup.tao-lich',
            'admin.khach-hang.hop-dong-cuoi.dieu-phoi.nhan-vien-theo-ngay',
        ],
        'admin.nhan-su.cong-viec-cua-toi' => [
            'admin.nhan-su.cong-viec-cua-toi.cap-nhat-link',
        ],
        'admin.trang-phuc.hop-dong' => [
            'admin.trang-phuc.hop-dong.tim-san-pham',
            'admin.trang-phuc.update-hop-dong-trang-thai',
        ],
        'admin.concept.concept' => [
            'admin.concept.concept.store',
            'admin.concept.concept.update',
            'admin.concept.concept.destroy',
        ],
        'admin.he-thong.vai-tro' => [
            'admin.he-thong.vai-tro.store',
            'admin.he-thong.vai-tro.update',
            'admin.he-thong.vai-tro.destroy',
            'admin.he-thong.vai-tro.nguoi-dung',
        ],
        'admin.he-thong.phong-ban' => [
            'admin.he-thong.phong-ban.store',
            'admin.he-thong.phong-ban.update',
            'admin.he-thong.phong-ban.destroy',
            'admin.he-thong.phong-ban.nhan-vien',
        ],
        'admin.he-thong.tai-lieu' => [
            'admin.he-thong.tai-lieu.store',
            'admin.he-thong.tai-lieu.destroy',
        ],
        'admin.he-thong.logs' => [
            'admin.he-thong.logs.destroy',
        ],
        'admin.bao-cao.ads' => [
            'admin.bao-cao.ads.store',
            'admin.bao-cao.ads.update',
            'admin.bao-cao.ads.destroy',
        ],
        'admin.note-khach-moi' => [
            'admin.note-khach-moi.store',
            'admin.note-khach-moi.update',
            'admin.note-khach-moi.destroy',
            'admin.note-khach-moi.tim-hop-dong-theo-sdt',
        ],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && VaiTro::isAdminMa($user->role)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if ($routeName === null) {
            return $next($request);
        }
        if ($routeName === self::UNAUTHORIZED_ROUTE) {
            return $next($request);
        }

        $effectiveRouteName = self::resolveRouteAlias($routeName);

        if (! self::isSidebarDsMenuRoute($effectiveRouteName)) {
            return $next($request);
        }

        $ds_menu = $user ? $user->sidebarDsMenuFromVaiTro() : [];

        if ($ds_menu === []) {
            return redirect()->route(self::UNAUTHORIZED_ROUTE);
        }

        $allowed = self::normalizeDsMenu($ds_menu);

        if (! self::isAllowedForDsMenu($effectiveRouteName, $allowed)) {
            return redirect()->route(self::UNAUTHORIZED_ROUTE);
        }

        return $next($request);
    }

    private static function resolveRouteAlias(string $routeName): string
    {
        $aliases = [
            'admin.bao-cao-ads' => 'admin.bao-cao.ads',
        ];

        return $aliases[$routeName] ?? $routeName;
    }

    /** Route có trong config admin_menu (sidebar ds_menu) hoặc route phụ gắn với menu đó. */
    private static function isSidebarDsMenuRoute(string $routeName): bool
    {
        return in_array($routeName, self::sidebarDsMenuRouteNames(), true);
    }

    /**
     * @return array<string>
     */
    private static function sidebarDsMenuRouteNames(): array
    {
        static $routes = null;

        if ($routes !== null) {
            return $routes;
        }

        $routes = [];

        foreach (config('admin_menu', []) as $item) {
            if (($item['type'] ?? '') === 'single' && ! empty($item['route'])) {
                $routes[] = $item['route'];
            }
            if (($item['type'] ?? '') === 'group' && ! empty($item['children'])) {
                foreach ($item['children'] as $child) {
                    if (! empty($child['route'])) {
                        $routes[] = $child['route'];
                    }
                }
            }
        }

        foreach (self::EXTRA_ROUTES_BY_BASE as $base => $extras) {
            $routes[] = $base;
            foreach ($extras as $extra) {
                $routes[] = $extra;
            }
        }

        $routes[] = 'admin.bao-cao-ads';

        return $routes = array_values(array_unique($routes));
    }

    /**
     * @param  array<string>  $allowed
     */
    private static function isAllowedForDsMenu(string $routeName, array $allowed): bool
    {
        if (in_array($routeName, $allowed, true)) {
            return true;
        }

        foreach (self::EXTRA_ROUTES_BY_BASE as $base => $extras) {
            if (in_array($routeName, $extras, true) && in_array($base, $allowed, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string>  $ds_menu
     * @return array<string>
     */
    private static function normalizeDsMenu(array $ds_menu): array
    {
        $aliases = [
            'admin.nhan-su.lich-lam-viec' => 'admin.lich-chup',
            'admin.lich-lam-viec' => 'admin.lich-chup',
            'admin.bao-cao-ads' => 'admin.bao-cao.ads',
        ];

        return array_values(array_unique(array_map(
            fn (string $route) => $aliases[$route] ?? $route,
            $ds_menu
        )));
    }
}
