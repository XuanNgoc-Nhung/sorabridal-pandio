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

    /** @var array<string, string> Giá trị loai_hop_dong (HopDongCuoi) => loai dịch vụ/nhóm dịch vụ */
    public const LOAI_HOP_DONG_TO_LOAI = [
        'pre_wedding' => self::CUOI,
        'phong_su_cuoi' => self::PHONG_SU,
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

    /**
     * Ánh xạ loai_hop_dong sang loai dịch vụ/nhóm dịch vụ (mặc định cưới nếu chưa chọn).
     */
    public static function tuLoaiHopDong(?string $loaiHopDong): string
    {
        if ($loaiHopDong === null || $loaiHopDong === '') {
            return self::CUOI;
        }

        return self::LOAI_HOP_DONG_TO_LOAI[$loaiHopDong] ?? self::CUOI;
    }
}
