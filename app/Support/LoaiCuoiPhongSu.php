<?php

namespace App\Support;

/**
 * Loại sản phẩm/dịch vụ: cưới hoặc phóng sự.
 */
class LoaiCuoiPhongSu
{
    public const CUOI = 'cuoi';

    public const PHONG_SU = 'phong_su';

    /** @var array<string, string> */
    public const LABELS = [
        self::CUOI => 'Cưới',
        self::PHONG_SU => 'Phóng sự',
    ];

    /** @var list<string> */
    public static function values(): array
    {
        return array_keys(self::LABELS);
    }

    public static function label(?string $loai): string
    {
        if ($loai === null || $loai === '') {
            return '—';
        }

        return self::LABELS[$loai] ?? $loai;
    }
}
