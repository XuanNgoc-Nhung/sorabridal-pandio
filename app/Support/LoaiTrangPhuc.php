<?php

namespace App\Support;

/**
 * Loại sản phẩm trang phục: cưới hoặc chụp.
 */
class LoaiTrangPhuc
{
    public const CUOI = 'cuoi';

    public const CHUP = 'chup';

    /** @var array<string, string> */
    public const LABELS = [
        self::CUOI => 'Trang phục cưới',
        self::CHUP => 'Trang phục chụp',
    ];

    /** Giá trị cũ (dùng chung enum cưới/phóng sự) — hiển thị tương đương trang phục chụp. */
    private const LEGACY_ALIASES = [
        'phong_su' => self::CHUP,
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

        $normalized = self::normalize($loai);

        return self::LABELS[$normalized] ?? $loai;
    }

    public static function normalize(?string $loai): string
    {
        if ($loai === null || $loai === '') {
            return self::CUOI;
        }

        return self::LEGACY_ALIASES[$loai] ?? $loai;
    }
}
