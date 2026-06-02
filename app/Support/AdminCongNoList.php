<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Hằng số bộ lọc / sắp xếp danh sách công nợ (hợp đồng cưới + thuê trang phục).
 */
final class AdminCongNoList
{
    public const KHOANG_NAM_NAY = 'nam_nay';

    public const KHOANG_QUY_NAY = 'quy_nay';

    public const KHOANG_QUY_TRUOC = 'quy_truoc';

    public const KHOANG_THANG_NAY = 'thang_nay';

    public const KHOANG_THANG_TRUOC = 'thang_truoc';

    public const KHOANG_TUAN_NAY = 'tuan_nay';

    public const KHOANG_TUAN_TRUOC = 'tuan_truoc';

    public const KHOANG_BA_THANG = 'ba_thang_gan_day';

    public const KHOANG_HAI_THANG = 'hai_thang_gan_day';

    public const KHOANG_SAU_THANG = 'sau_thang_gan_day';

    /** @var array<string, string> */
    public const KHOANG_NGAY_OPTIONS = [
        self::KHOANG_NAM_NAY => 'Năm nay',
        self::KHOANG_QUY_NAY => 'Quý này',
        self::KHOANG_QUY_TRUOC => 'Quý trước',
        self::KHOANG_THANG_NAY => 'Tháng này',
        self::KHOANG_THANG_TRUOC => 'Tháng trước',
        self::KHOANG_TUAN_NAY => 'Tuần này',
        self::KHOANG_TUAN_TRUOC => 'Tuần trước',
        self::KHOANG_BA_THANG => '3 tháng gần đây',
        self::KHOANG_HAI_THANG => '2 tháng gần đây',
        self::KHOANG_SAU_THANG => '6 tháng gần đây',
    ];

    public const LOAI_HOP_DONG_CUOI = 'hop_dong_cuoi';

    public const LOAI_HOP_DONG_THUE = 'hop_dong_thue_trang_phuc';

    /** @var array<string, string> */
    public const LOAI_HOP_DONG_OPTIONS = [
        self::LOAI_HOP_DONG_CUOI => 'Hợp đồng cưới',
        self::LOAI_HOP_DONG_THUE => 'Thuê trang phục',
    ];

    public const SAP_XEP_CREATED_AT = 'created_at';

    public const SAP_XEP_TEN_KHACH = 'ten_khach_hang';

    public const SAP_XEP_TONG_TIEN = 'tong_tien';

    public const SAP_XEP_DA_THANH_TOAN = 'da_thanh_toan';

    public const SAP_XEP_CON_LAI = 'con_lai';

    public const SAP_XEP_MAC_DINH = self::SAP_XEP_CREATED_AT;

    /** @var array<string, string> */
    public const SAP_XEP_OPTIONS = [
        self::SAP_XEP_CREATED_AT => 'Ngày tạo HĐ',
        self::SAP_XEP_TEN_KHACH => 'Tên khách hàng',
        self::SAP_XEP_TONG_TIEN => 'Tổng tiền',
        self::SAP_XEP_DA_THANH_TOAN => 'Đã thanh toán',
        self::SAP_XEP_CON_LAI => 'Còn lại',
    ];

    /**
     * @return array{tu: string, den: string}|null
     */
    public static function resolveKhoangNgay(string $key, ?Carbon $today = null): ?array
    {
        if (! array_key_exists($key, self::KHOANG_NGAY_OPTIONS)) {
            return null;
        }

        $today = ($today ?? Carbon::today())->copy()->startOfDay();
        $den = $today->copy()->endOfDay();

        $startOfQuarter = self::startOfQuarter($today);
        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);

        [$tu, $denRange] = match ($key) {
            self::KHOANG_NAM_NAY => [
                $today->copy()->startOfYear(),
                $den,
            ],
            self::KHOANG_QUY_NAY => [
                $startOfQuarter->copy(),
                $den,
            ],
            self::KHOANG_QUY_TRUOC => [
                $startOfQuarter->copy()->subMonths(3),
                $startOfQuarter->copy()->subDay()->endOfDay(),
            ],
            self::KHOANG_THANG_NAY => [
                $today->copy()->startOfMonth(),
                $den,
            ],
            self::KHOANG_THANG_TRUOC => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ],
            self::KHOANG_TUAN_NAY => [
                $startOfWeek->copy(),
                $den,
            ],
            self::KHOANG_TUAN_TRUOC => [
                $startOfWeek->copy()->subWeek(),
                $startOfWeek->copy()->subDay()->endOfDay(),
            ],
            self::KHOANG_BA_THANG => [
                $today->copy()->subMonths(3),
                $den,
            ],
            self::KHOANG_HAI_THANG => [
                $today->copy()->subMonths(2),
                $den,
            ],
            self::KHOANG_SAU_THANG => [
                $today->copy()->subMonths(6),
                $den,
            ],
            default => [null, null],
        };

        if ($tu === null || $denRange === null) {
            return null;
        }

        return [
            'tu' => $tu->toDateString(),
            'den' => $denRange->toDateString(),
        ];
    }

    private static function startOfQuarter(Carbon $date): Carbon
    {
        $quarter = (int) ceil($date->month / 3);
        $startMonth = ($quarter - 1) * 3 + 1;

        return $date->copy()->month($startMonth)->startOfMonth();
    }
}
