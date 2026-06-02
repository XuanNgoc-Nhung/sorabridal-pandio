<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class AdminMenuPermissions
{
    /**
     * @return array<int, array{name: string, uri: string, description: string}>
     */
    public static function metadata(): array
    {
        $menuItems = config('admin_menu', []);

        return collect($menuItems)
            ->flatMap(function (array $item) {
                if (($item['type'] ?? null) === 'single' && ! empty($item['route'])) {
                    return [[
                        'name' => $item['route'],
                        'description' => (string) ($item['label'] ?? $item['route']),
                    ]];
                }

                if (($item['type'] ?? null) === 'group' && ! empty($item['children']) && is_array($item['children'])) {
                    return collect($item['children'])
                        ->filter(fn (array $child) => ! empty($child['route']))
                        ->map(function (array $child) use ($item) {
                            $groupLabel = (string) ($item['label'] ?? 'Nhóm');
                            $childLabel = (string) ($child['label'] ?? $child['route']);

                            return [
                                'name' => $child['route'],
                                'description' => $groupLabel.' / '.$childLabel,
                            ];
                        });
                }

                return [];
            })
            ->unique('name')
            ->map(function (array $menuRoute) {
                $name = (string) $menuRoute['name'];
                $route = Route::getRoutes()->getByName($name);

                return [
                    'name' => $name,
                    'uri' => $route ? '/'.ltrim($route->uri(), '/') : '—',
                    'description' => (string) ($menuRoute['description'] ?? $name),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function describedRouteNames(): array
    {
        return collect(self::metadata())
            ->filter(fn (array $route) => ! empty($route['description']))
            ->pluck('name')
            ->all();
    }

    /**
     * @return array<int, array{name: string, uri: string, description: string}>
     */
    public static function routesForForm(): array
    {
        return collect(self::metadata())
            ->filter(fn (array $route) => ! empty($route['description']))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>|null  $permissions
     * @param  array<int, string>|null  $existing
     * @return array<int, string>
     */
    public static function buildDsMenu(?array $permissions, ?array $existing = null): array
    {
        $describedNames = self::describedRouteNames();

        $submitted = collect($permissions ?? [])
            ->filter(fn ($name) => is_string($name) && in_array($name, $describedNames, true))
            ->values()
            ->all();

        $preservedKhongMoTa = collect($existing ?? [])
            ->filter(fn ($name) => is_string($name) && ! in_array($name, $describedNames, true))
            ->values()
            ->all();

        return array_values(array_unique(array_merge($preservedKhongMoTa, $submitted)));
    }
}
