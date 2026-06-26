<?php

namespace App\Support;

use App\Models\HopDongChoThueTrangPhuc;
use App\Models\NhanVien;
use App\Models\ThanhVienHopDongCuoi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TinhLuongThang
{
    /**
     * Tính hoa hồng HĐ cưới theo tháng cho từng nhân viên (key = nhan_vien.id).
     *
     * @param  Collection<int, NhanVien>  $nhanVienRecords
     * @return array<int, float>
     */
    public static function tinhHoaHongHopDongCuoi(Collection $nhanVienRecords, Carbon $start, Carbon $end): array
    {
        $nhanVienIds = $nhanVienRecords->pluck('id')->filter()->values()->all();
        if ($nhanVienIds === []) {
            return [];
        }

        $startStr = $start->toDateString();
        $endStr = $end->toDateString();

        $thanhVienRecords = ThanhVienHopDongCuoi::query()
            ->with('hopDongCuoi')
            ->whereIn('nhan_vien_id', $nhanVienIds)
            ->whereHas('hopDongCuoi', function ($q) use ($startStr, $endStr) {
                $q->whereBetween('ngay_ky_hop_dong', [$startStr, $endStr])
                    ->whereNotIn('trang_thai_hop_dong', ['nhap', 'da_huy']);
            })
            ->get();

        if ($thanhVienRecords->isEmpty()) {
            return [];
        }

        $hopDongIds = $thanhVienRecords->pluck('hop_dong_id')->unique()->values()->all();
        $soThanhVienTheoHopDong = ThanhVienHopDongCuoi::query()
            ->whereIn('hop_dong_id', $hopDongIds)
            ->selectRaw('hop_dong_id, COUNT(*) as so_nguoi')
            ->groupBy('hop_dong_id')
            ->pluck('so_nguoi', 'hop_dong_id');

        $nhanVienById = $nhanVienRecords->keyBy('id');
        $hoaHongTheoNhanVienId = [];

        foreach ($thanhVienRecords as $tv) {
            $hopDong = $tv->hopDongCuoi;
            if ($hopDong === null) {
                continue;
            }

            $soNguoi = max(1, (int) ($soThanhVienTheoHopDong[$tv->hop_dong_id] ?? 1));
            $nv = $nhanVienById->get($tv->nhan_vien_id);
            $hoaHongRate = (float) ($nv?->hoa_hong_hop_dong_cuoi ?? 0);
            $phan = ((float) ($hopDong->tong_tien ?? 0) / $soNguoi) * $hoaHongRate / 100;

            $hoaHongTheoNhanVienId[$tv->nhan_vien_id] = ($hoaHongTheoNhanVienId[$tv->nhan_vien_id] ?? 0) + $phan;
        }

        return $hoaHongTheoNhanVienId;
    }

    /**
     * Tính hoa hồng HĐ thuê trang phục theo tháng cho từng nhân viên (key = nhan_vien.id).
     *
     * @param  Collection<int, NhanVien>  $nhanVienRecords
     * @return array<int, float>
     */
    public static function tinhHoaHongHopDongTrangPhuc(Collection $nhanVienRecords, Carbon $start, Carbon $end): array
    {
        $userIds = $nhanVienRecords->pluck('user_id')->filter()->values()->all();
        if ($userIds === []) {
            return [];
        }

        $startStr = $start->toDateString();
        $endStr = $end->toDateString();

        $hopDongs = HopDongChoThueTrangPhuc::query()
            ->whereIn('nguoi_cho_thue', $userIds)
            ->where('trang_thai', 1)
            ->whereBetween('ngay_tra_chinh_thuc', [$startStr, $endStr])
            ->get();

        if ($hopDongs->isEmpty()) {
            return [];
        }

        $nhanVienByUserId = $nhanVienRecords->keyBy('user_id');
        $hoaHongTheoNhanVienId = [];

        foreach ($hopDongs as $hopDong) {
            $nv = $nhanVienByUserId->get($hopDong->nguoi_cho_thue);
            if ($nv === null) {
                continue;
            }

            $hoaHongRate = (float) ($nv->hoa_hong_hop_dong_trang_phuc ?? 0);
            $phan = (float) ($hopDong->tong_tien ?? 0) * $hoaHongRate / 100;

            $hoaHongTheoNhanVienId[$nv->id] = ($hoaHongTheoNhanVienId[$nv->id] ?? 0) + $phan;
        }

        return $hoaHongTheoNhanVienId;
    }

    /**
     * @return array{
     *     luong_co_ban: float,
     *     luong_tang_ca: float,
     *     phu_cap: float,
     *     hoa_hong_hop_dong_cuoi: float,
     *     hoa_hong_hop_dong_trang_phuc: float,
     *     tong_luong: float
     * }
     */
    public static function tongHopThang(
        ?NhanVien $nhanVien,
        float $tongLuongCoBanTuDiemDanh,
        float $tongLuongTangCaTuDiemDanh,
        float $hoaHongHopDongCuoi,
        float $hoaHongHopDongTrangPhuc
    ): array {
        $loaiNv = $nhanVien?->loai_nhan_vien ?? '';
        $luongCoBan = $loaiNv === NhanVien::LOAI_NHAN_VIEN_FULL_TIME
            ? (float) ($nhanVien->luong_cung ?? 0)
            : $tongLuongCoBanTuDiemDanh;

        $phuCap = (float) ($nhanVien?->phu_cap ?? 0);

        return [
            'luong_co_ban' => $luongCoBan,
            'luong_tang_ca' => $tongLuongTangCaTuDiemDanh,
            'phu_cap' => $phuCap,
            'hoa_hong_hop_dong_cuoi' => $hoaHongHopDongCuoi,
            'hoa_hong_hop_dong_trang_phuc' => $hoaHongHopDongTrangPhuc,
            'tong_luong' => $luongCoBan + $tongLuongTangCaTuDiemDanh + $phuCap + $hoaHongHopDongCuoi + $hoaHongHopDongTrangPhuc,
        ];
    }
}
