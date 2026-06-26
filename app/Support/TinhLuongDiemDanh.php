<?php

namespace App\Support;

use App\Models\DangKyCaLamViec;
use App\Models\NhanVien;
use Illuminate\Support\Carbon;

class TinhLuongDiemDanh
{
    public const THONG_BAO_LOAI_NHAN_VIEN = 'Không cho điểm danh. Liên hệ admin hoặc kiểm tra lại.';

    public static function phutTangCaToiThieu(): int
    {
        return max(0, (int) config('diem_danh.phut_tang_ca_toi_thieu', 30));
    }

    public static function hopLeLoaiNhanVien(?NhanVien $nhanVien): bool
    {
        if ($nhanVien === null) {
            return false;
        }

        return in_array($nhanVien->loai_nhan_vien, [
            NhanVien::LOAI_NHAN_VIEN_FULL_TIME,
            NhanVien::LOAI_NHAN_VIEN_PART_TIME,
        ], true);
    }

    /**
     * @return array{gio_lam_co_ban: float, gio_lam_tang_ca: float, luong_co_ban: float, luong_tang_ca: float}
     */
    public static function tinh(Carbon $gioVao, Carbon $gioRa, NhanVien $nhanVien): array
    {
        $phutToiThieu = self::phutTangCaToiThieu();
        $donGiaCoBan = (float) $nhanVien->luong_co_ban;
        $donGiaTangCa = (float) $nhanVien->luong_tang_ca;

        if ($nhanVien->loai_nhan_vien === NhanVien::LOAI_NHAN_VIEN_PART_TIME) {
            return self::tinhPartTime($gioVao, $gioRa, $donGiaCoBan, $donGiaTangCa, $phutToiThieu);
        }

        return self::tinhFullTime($gioVao, $gioRa, (int) $nhanVien->user_id, $donGiaTangCa, $phutToiThieu);
    }

    /**
     * @return array{gio_lam_co_ban: float, gio_lam_tang_ca: float, luong_co_ban: float, luong_tang_ca: float}
     */
    private static function tinhPartTime(
        Carbon $gioVao,
        Carbon $gioRa,
        float $donGiaCoBan,
        float $donGiaTangCa,
        int $phutToiThieu
    ): array {
        $gioChuyen = (string) config('diem_danh.gio_chuyen_tang_ca', '21:00');
        if (strlen($gioChuyen) === 5) {
            $gioChuyen .= ':00';
        }
        $cuoiGioCoBan = Carbon::parse($gioVao->toDateString().' '.$gioChuyen);

        if ($gioRa->lte($cuoiGioCoBan)) {
            $phutCoBan = (int) $gioVao->diffInMinutes($gioRa);
            $phutTangCa = 0;
        } elseif ($gioVao->gte($cuoiGioCoBan)) {
            $phutCoBan = 0;
            $phutTangCa = (int) $gioVao->diffInMinutes($gioRa);
        } else {
            $phutCoBan = (int) $gioVao->diffInMinutes($cuoiGioCoBan);
            $phutTangCa = (int) $cuoiGioCoBan->diffInMinutes($gioRa);
        }

        $gioLamCoBan = round($phutCoBan / 60, 2);
        $gioLamTangCa = round($phutTangCa / 60, 2);
        $luongCoBan = round($gioLamCoBan * $donGiaCoBan, 2);
        $luongTangCa = $phutTangCa >= $phutToiThieu
            ? round($gioLamTangCa * $donGiaTangCa, 2)
            : 0.0;

        return [
            'gio_lam_co_ban' => $gioLamCoBan,
            'gio_lam_tang_ca' => $gioLamTangCa,
            'luong_co_ban' => $luongCoBan,
            'luong_tang_ca' => $luongTangCa,
        ];
    }

    /**
     * Full-time: không lương cơ bản theo ngày; tăng ca khi check-in sau giờ kết thúc ca.
     *
     * @return array{gio_lam_co_ban: float, gio_lam_tang_ca: float, luong_co_ban: float, luong_tang_ca: float}
     */
    private static function tinhFullTime(
        Carbon $gioVao,
        Carbon $gioRa,
        int $userId,
        float $donGiaTangCa,
        int $phutToiThieu
    ): array {
        $phutTangCa = self::tinhPhutTangCaFullTime($gioVao, $gioRa, $userId);
        $gioLamTangCa = round($phutTangCa / 60, 2);
        $luongTangCa = $phutTangCa >= $phutToiThieu
            ? round($gioLamTangCa * $donGiaTangCa, 2)
            : 0.0;

        return [
            'gio_lam_co_ban' => 0.0,
            'gio_lam_tang_ca' => $gioLamTangCa,
            'luong_co_ban' => 0.0,
            'luong_tang_ca' => $luongTangCa,
        ];
    }

    private static function tinhPhutTangCaFullTime(Carbon $gioVao, Carbon $gioRa, int $userId): int
    {
        $dangKy = DangKyCaLamViec::query()
            ->with('caLamViec')
            ->where('nguoi_dung_id', $userId)
            ->whereDate('ngay_lam', $gioVao->toDateString())
            ->first();

        $caLam = $dangKy?->caLamViec;
        if ($caLam === null || $caLam->gio_ket_thuc === null || $caLam->gio_ket_thuc === '') {
            return 0;
        }

        $gioKetThucCa = Carbon::parse($gioVao->toDateString().' '.$caLam->gio_ket_thuc);
        if ($gioVao->lte($gioKetThucCa)) {
            return 0;
        }

        return (int) $gioVao->diffInMinutes($gioRa);
    }
}
