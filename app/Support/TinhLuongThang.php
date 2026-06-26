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
     * Tổng hợp giờ làm và tiền phạt từ các bản ghi chấm công trong tháng.
     *
     * @param  iterable<int, \App\Models\ChamCong>  $chamCongRecords
     * @return array{
     *     tong_gio_lam_co_ban: float,
     *     tong_gio_tang_ca: float,
     *     tien_phat_di_muon: int,
     *     tien_phat_ve_som: int
     * }
     */
    public static function tongHopDiemDanhThang(iterable $chamCongRecords): array
    {
        $ketQua = [
            'tong_gio_lam_co_ban' => 0.0,
            'tong_gio_tang_ca' => 0.0,
            'tien_phat_di_muon' => 0,
            'tien_phat_ve_som' => 0,
        ];

        foreach ($chamCongRecords as $record) {
            $diemDanh = $record->diemDanh;
            if ($diemDanh === null) {
                continue;
            }

            $ketQua['tong_gio_lam_co_ban'] += (float) ($diemDanh->gio_lam_co_ban ?? 0);
            $ketQua['tong_gio_tang_ca'] += (float) ($diemDanh->gio_lam_tang_ca ?? 0);
            $ketQua['tien_phat_di_muon'] += (int) ($diemDanh->tien_phat_di_muon ?? 0);
            $ketQua['tien_phat_ve_som'] += (int) ($diemDanh->tien_phat_ve_som ?? 0);
        }

        return $ketQua;
    }

    /**
     * Chi tiết hoa hồng HĐ cưới theo tháng (key = nhan_vien.id).
     *
     * @param  Collection<int, NhanVien>  $nhanVienRecords
     * @return array<int, array{tong: float, danh_sach: list<array<string, mixed>>}>
     */
    public static function chiTietHoaHongHopDongCuoi(Collection $nhanVienRecords, Carbon $start, Carbon $end): array
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
        $ketQua = [];

        foreach ($thanhVienRecords as $tv) {
            $hopDong = $tv->hopDongCuoi;
            if ($hopDong === null) {
                continue;
            }

            $soNguoi = max(1, (int) ($soThanhVienTheoHopDong[$tv->hop_dong_id] ?? 1));
            $nv = $nhanVienById->get($tv->nhan_vien_id);
            $tyLeHoaHong = (float) ($nv?->hoa_hong_hop_dong_cuoi ?? 0);
            $doanhThu = (float) ($hopDong->tong_tien ?? 0);
            $soTienNhan = ($doanhThu / $soNguoi) * $tyLeHoaHong / 100;

            $nhanVienId = (int) $tv->nhan_vien_id;
            if (! isset($ketQua[$nhanVienId])) {
                $ketQua[$nhanVienId] = ['tong' => 0.0, 'danh_sach' => []];
            }

            $ketQua[$nhanVienId]['danh_sach'][] = [
                'hop_dong_id' => (int) $hopDong->id,
                'ma_hop_dong' => (string) ($hopDong->ma_hop_dong ?? ''),
                'ten_hop_dong' => trim(($hopDong->ten_co_dau ?? '').' & '.($hopDong->ten_chu_re ?? '')),
                'ngay' => $hopDong->ngay_ky_hop_dong?->format('d/m/Y') ?? '—',
                'doanh_thu' => $doanhThu,
                'so_nguoi_tham_gia' => $soNguoi,
                'ty_le_hoa_hong' => $tyLeHoaHong,
                'so_tien_nhan' => $soTienNhan,
            ];
            $ketQua[$nhanVienId]['tong'] += $soTienNhan;
        }

        return $ketQua;
    }

    /**
     * Chi tiết hoa hồng HĐ thuê trang phục theo tháng (key = nhan_vien.id).
     *
     * @param  Collection<int, NhanVien>  $nhanVienRecords
     * @return array<int, array{tong: float, danh_sach: list<array<string, mixed>>}>
     */
    public static function chiTietHoaHongHopDongTrangPhuc(Collection $nhanVienRecords, Carbon $start, Carbon $end): array
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
        $ketQua = [];

        foreach ($hopDongs as $hopDong) {
            $nv = $nhanVienByUserId->get($hopDong->nguoi_cho_thue);
            if ($nv === null) {
                continue;
            }

            $tyLeHoaHong = (float) ($nv->hoa_hong_hop_dong_trang_phuc ?? 0);
            $doanhThu = (float) ($hopDong->tong_tien ?? 0);
            $soTienNhan = $doanhThu * $tyLeHoaHong / 100;

            $nhanVienId = (int) $nv->id;
            if (! isset($ketQua[$nhanVienId])) {
                $ketQua[$nhanVienId] = ['tong' => 0.0, 'danh_sach' => []];
            }

            $ketQua[$nhanVienId]['danh_sach'][] = [
                'hop_dong_id' => (int) $hopDong->id,
                'ten_khach_hang' => (string) ($hopDong->ten_khach_hang ?? ''),
                'sdt_khach_hang' => (string) ($hopDong->sdt_khach_hang ?? ''),
                'ngay' => $hopDong->ngay_tra_chinh_thuc?->format('d/m/Y') ?? '—',
                'doanh_thu' => $doanhThu,
                'ty_le_hoa_hong' => $tyLeHoaHong,
                'so_tien_nhan' => $soTienNhan,
            ];
            $ketQua[$nhanVienId]['tong'] += $soTienNhan;
        }

        return $ketQua;
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
