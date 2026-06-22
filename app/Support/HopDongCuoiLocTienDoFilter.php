<?php

namespace App\Support;

use App\Models\HopDongCuoi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Bộ lọc tiến độ HĐ cưới (checkbox loc[]) — dùng chung lịch làm việc & danh sách HĐ.
 */
class HopDongCuoiLocTienDoFilter
{
    /** @return list<string> */
    public static function allowedKeys(): array
    {
        return array_keys(config('lich_lam_viec.loc_tien_do', []));
    }

    /** @return array<string, array{label?: string}> */
    public static function options(): array
    {
        return config('lich_lam_viec.loc_tien_do', []);
    }

    /** @return list<string> */
    public static function parseFromRequest(Request $request): array
    {
        $raw = $request->query('loc', []);
        if (is_string($raw)) {
            $raw = array_filter(array_map('trim', explode(',', $raw)));
        }

        return array_values(array_intersect(self::allowedKeys(), (array) $raw));
    }

    public static function hasChuaPhanCong(array $filters): bool
    {
        return in_array('chua_phan_cong', $filters, true);
    }

    public static function matchesChuaPhanCong(HopDongCuoi $hd): bool
    {
        return ! $hd->daPhanCong();
    }

    /** Chưa phân chụp, make và edit. @param Builder<\App\Models\HopDongCuoi> $query */
    public static function applyChuaPhanCong(Builder $query): void
    {
        self::whereChuaPhanThoChup($query);
        self::whereChuaPhanThoMake($query);
        self::whereChuaPhanThoEdit($query);
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query @param list<string> $filters */
    public static function apply(Builder $query, array $filters): void
    {
        if ($filters === []) {
            return;
        }

        $query->where(function ($outer) use ($filters) {
            foreach ($filters as $key) {
                $outer->orWhere(function ($q) use ($key) {
                    match ($key) {
                        'chua_xep_lich_soft' => $q->whereNull('ngay_tra_link_demo_chinh_thuc'),
                        'chua_phan_cong' => $q->tap(fn ($qq) => self::applyChuaPhanCong($qq)),
                        'phan_chup' => $q->tap(fn ($qq) => self::whereDaPhanThoChup($qq))
                            ->tap(fn ($qq) => self::whereChuaPhanThoMake($qq))
                            ->tap(fn ($qq) => self::whereChuaPhanThoEdit($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkDemo($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkIn($qq)),
                        'phan_make' => $q->tap(fn ($qq) => self::whereDaPhanThoMake($qq))
                            ->tap(fn ($qq) => self::whereChuaPhanThoEdit($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkDemo($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkIn($qq)),
                        'phan_edit' => $q->tap(fn ($qq) => self::whereDaPhanThoEdit($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkDemo($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkIn($qq)),
                        'up_link_demo' => $q->tap(fn ($qq) => self::whereDaUpLinkDemo($qq))
                            ->tap(fn ($qq) => self::whereChuaUpLinkIn($qq)),
                        'up_link_in' => $q->tap(fn ($qq) => self::whereDaUpLinkIn($qq)),
                        'da_nhan_coc' => $q->where('tong_tien', '>', 0)
                            ->where('tien_coc', '>', 0)
                            ->whereColumn('tien_coc', '<', 'tong_tien'),
                        'da_tat_toan' => $q->where('tong_tien', '>', 0)
                            ->whereColumn('tien_coc', '>=', 'tong_tien'),
                        'da_huy' => $q->where('trang_thai_hop_dong', 'da_huy'),
                        default => null,
                    };
                });
            }
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereChuaPhanThoChup(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('tho_chup_id')
                ->where(function ($q2) {
                    $q2->whereNull('tho_chup_freelancer')->orWhere('tho_chup_freelancer', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereChuaPhanThoMake(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('tho_make_id')
                ->where(function ($q2) {
                    $q2->whereNull('tho_make_freelancer')->orWhere('tho_make_freelancer', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereChuaPhanThoEdit(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('tho_edit_id')
                ->where(function ($q2) {
                    $q2->whereNull('tho_edit_freelancer')->orWhere('tho_edit_freelancer', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereDaPhanThoChup(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNotNull('tho_chup_id')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('tho_chup_freelancer')->where('tho_chup_freelancer', '!=', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereDaPhanThoMake(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNotNull('tho_make_id')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('tho_make_freelancer')->where('tho_make_freelancer', '!=', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereDaPhanThoEdit(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNotNull('tho_edit_id')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('tho_edit_freelancer')->where('tho_edit_freelancer', '!=', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereChuaUpLinkDemo(Builder $query): void
    {
        $query->where(function ($qq) {
            $qq->whereNull('ngay_up_link_demo_gan_nhat')
                ->where(function ($q2) {
                    $q2->whereNull('link_demo')->orWhere('link_demo', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereChuaUpLinkIn(Builder $query): void
    {
        $query->where(function ($qq) {
            $qq->whereNull('ngay_up_link_in_gan_nhat')
                ->where(function ($q2) {
                    $q2->whereNull('link_in')->orWhere('link_in', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereDaUpLinkDemo(Builder $query): void
    {
        $query->where(function ($qq) {
            $qq->whereNotNull('ngay_up_link_demo_gan_nhat')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('link_demo')->where('link_demo', '!=', '');
                });
        });
    }

    /** @param Builder<\App\Models\HopDongCuoi> $query */
    private static function whereDaUpLinkIn(Builder $query): void
    {
        $query->where(function ($qq) {
            $qq->whereNotNull('ngay_up_link_in_gan_nhat')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('link_in')->where('link_in', '!=', '');
                });
        });
    }
}
