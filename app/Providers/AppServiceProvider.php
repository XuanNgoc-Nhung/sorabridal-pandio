<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Sidebar: danh sách menu đầy đủ lấy từ config (admin_menu).
     * Chỉ hiển thị các mục có route nằm trong vai_tro.ds_menu (theo user.role → ma_vai_tro).
     */
    public function boot(): void
    {
        View::composer('admin.*', function ($view) {
            $view->with(
                'coQuyenDieuChinhHopDongCuoi',
                (bool) auth()->user()?->coQuyenDieuChinhHopDongCuoi()
            );
        });

        View::composer(['admin.layouts.app', 'admin.layouts.components.sidebar'], function ($view) {
            // Log::debug('[Sidebar] View composer bắt đầu', ['view' => $view->name()]);

            $user = auth()->user();
            // ds_menu: danh sách route được phép xem (từ vai_tro theo user.role → ma_vai_tro)
            $ds_menu = [];
            if ($user) {
                $ds_menu = self::normalizeDsMenu($user->sidebarDsMenuFromVaiTro());
            }

            // Log::debug('[Sidebar] User & ds_menu', [
            //     'user_id' => $user?->id,
            //     'ds_menu_count' => count($ds_menu),
            //     'ds_menu' => $ds_menu,
            // ]);

            // Toàn bộ menu từ config, lọc theo ds_menu (chỉ giữ mục có route trong ds_menu)
            $sidebarMenuItems = self::filterMenuByDsMenu($ds_menu);

            // Log::debug('[Sidebar] Kết quả menu sau lọc', [
            //     'sidebar_menu_items_count' => count($sidebarMenuItems),
            // ]);

            $view->with('sidebarDsMenu', $ds_menu);
            $view->with('sidebarMenuItems', $sidebarMenuItems);
            $view->with('quickSearchMenu', self::flattenMenuForSearch($sidebarMenuItems));
        });
    }

    /**
     * Danh sách phẳng cho modal tìm kiếm nhanh (Ctrl+K) — cùng nguồn với sidebar.
     *
     * @param  array<int, array<string, mixed>>  $sidebarMenuItems
     * @return array<int, array{name: string, url: string, icon: string, section: string}>
     */
    private static function flattenMenuForSearch(array $sidebarMenuItems): array
    {
        $items = [];

        foreach ($sidebarMenuItems as $item) {
            if (($item['type'] ?? '') === 'single' && ! empty($item['route'])) {
                $items[] = [
                    'name' => (string) ($item['label'] ?? ''),
                    'url' => route($item['route']),
                    'icon' => self::menuIconForSearch($item['icon'] ?? ''),
                    'section' => 'Menu',
                ];
                continue;
            }

            if (($item['type'] ?? '') === 'group' && ! empty($item['children'])) {
                $section = (string) ($item['label'] ?? 'Menu');
                $icon = self::menuIconForSearch($item['icon'] ?? '');
                foreach ($item['children'] as $child) {
                    if (empty($child['route'])) {
                        continue;
                    }
                    $items[] = [
                        'name' => (string) ($child['label'] ?? ''),
                        'url' => route($child['route']),
                        'icon' => $icon,
                        'section' => $section,
                    ];
                }
            }
        }

        return $items;
    }

    private static function menuIconForSearch(string $icon): string
    {
        if (preg_match('/tabler-[\w-]+/', $icon, $matches)) {
            return $matches[0];
        }

        return 'tabler-circle';
    }

    /**
     * Lấy menu đầy đủ từ config, chỉ giữ lại mục có route nằm trong ds_menu.
     * @param array<string> $ds_menu vai_tro.ds_menu
     */
    private static function filterMenuByDsMenu(array $ds_menu): array
    {
        // Log::debug('[Sidebar] filterMenuByDsMenu', ['ds_menu_count' => count($ds_menu)]);

        $allItems = config('admin_menu', []);
        $result = [];

        foreach ($allItems as $item) {
            if ($item['type'] === 'single') {
                $route = $item['route'];
                if (in_array($route, $ds_menu, true)) {
                    $result[] = $item;
                }
                continue;
            }

            if ($item['type'] === 'group') {
                $children = [];
                foreach ($item['children'] as $child) {
                    $route = $child['route'];
                    if (in_array($route, $ds_menu, true)) {
                        $children[] = $child;
                    }
                }
                if (count($children) > 0) {
                    $result[] = array_merge($item, ['children' => $children]);
                }
            }
        }

        // Log::debug('[Sidebar] filterMenuByDsMenu xong', [
        //     'config_items' => count($allItems),
        //     'result_items' => count($result),
        // ]);

        return $result;
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
