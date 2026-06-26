<?php

namespace App\Support;

use App\Models\ChamCong;
use App\Models\ChotLuongThang;
use App\Models\NhanVien;
use App\Models\User;
use App\Models\VaiTro;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TinhLuongThangDuLieu
{
    /**
     * @return array{
     *     month: int,
     *     year: int,
     *     start: Carbon,
     *     end: Carbon,
     *     ngayTrongThang: list<Carbon>,
     *     nhanVien: Collection<int, User>,
     *     bangChamCong: array<string, array<int, ChamCong>>,
     *     bangChamCongLuong: array<int, array<string, array{luong_co_ban: float, luong_tang_ca: float}>>,
     *     bangLuongThang: array<int, array<string, float>>,
     *     chiTietHoaHong: array<int, array<string, mixed>>,
     *     chiTietChuyenLuong: array<int, array<string, mixed>>
     * }
     */
    public static function tinh(int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $startStr = $start->format('Y-m-d');
        $endStr = $end->format('Y-m-d');

        $ngayTrongThang = [];
        for ($d = (clone $start); $d->lte($end); $d->addDay()) {
            $ngayTrongThang[] = (clone $d);
        }

        $userIdsCoChamCong = ChamCong::query()
            ->whereBetween('ngay_diem_danh', [$startStr, $endStr])
            ->distinct()
            ->pluck('user_id');

        $nhanVien = User::query()
            ->where(function ($q) use ($userIdsCoChamCong) {
                $q->where('role', (int) VaiTro::MA_NHAN_VIEN)
                    ->orWhereIn('id', $userIdsCoChamCong);
            })
            ->with('nhanVien')
            ->orderBy('name')
            ->get();

        $chamCong = ChamCong::query()
            ->with(['user', 'diemDanh'])
            ->whereBetween('ngay_diem_danh', [$startStr, $endStr])
            ->whereIn('user_id', $nhanVien->pluck('id'))
            ->get();

        $bangChamCong = [];
        $bangChamCongLuong = [];
        $tongLuongTuDiemDanh = [];
        foreach ($nhanVien as $u) {
            $tongLuongTuDiemDanh[$u->id] = [
                'luong_co_ban' => 0,
                'luong_tang_ca' => 0,
            ];
            $bangChamCongLuong[$u->id] = [];
        }
        foreach ($chamCong as $record) {
            $date = $record->ngay_diem_danh;
            $dateKey = $date ? Carbon::parse($date)->format('Y-m-d') : null;
            if (! $dateKey) {
                continue;
            }
            $bangChamCong[$dateKey][$record->user_id] = $record;

            $diemDanh = $record->diemDanh;
            if ($diemDanh) {
                $uid = $record->user_id;
                $luongCoBan = (float) ($diemDanh->luong_co_ban ?? 0);
                $luongTangCa = (float) ($diemDanh->luong_tang_ca ?? 0);
                $tongLuongTuDiemDanh[$uid]['luong_co_ban'] += $luongCoBan;
                $tongLuongTuDiemDanh[$uid]['luong_tang_ca'] += $luongTangCa;
                $bangChamCongLuong[$uid][$dateKey] = [
                    'luong_co_ban' => $luongCoBan,
                    'luong_tang_ca' => $luongTangCa,
                    'tien_phat_di_muon' => (int) ($diemDanh->tien_phat_di_muon ?? 0),
                    'tien_phat_ve_som' => (int) ($diemDanh->tien_phat_ve_som ?? 0),
                    'tong_phat' => (int) ($diemDanh->tien_phat_di_muon ?? 0) + (int) ($diemDanh->tien_phat_ve_som ?? 0),
                ];
            }
        }

        $nhanVienRecords = $nhanVien->pluck('nhanVien')->filter();
        $chiTietHoaHongCuoi = TinhLuongThang::chiTietHoaHongHopDongCuoi($nhanVienRecords, $start, $end);
        $chiTietHoaHongTrangPhuc = TinhLuongThang::chiTietHoaHongHopDongTrangPhuc($nhanVienRecords, $start, $end);

        $chamCongTheoUser = $chamCong->groupBy('user_id');

        $bangLuongThang = [];
        $chiTietHoaHong = [];
        $chiTietChuyenLuong = [];
        foreach ($nhanVien as $u) {
            $nv = $u->nhanVien;
            $nvId = $nv?->id;
            $tongDiemDanh = $tongLuongTuDiemDanh[$u->id] ?? ['luong_co_ban' => 0, 'luong_tang_ca' => 0];
            $hoaHongHopDongCuoi = (float) ($chiTietHoaHongCuoi[$nvId]['tong'] ?? 0);
            $hoaHongHopDongTrangPhuc = (float) ($chiTietHoaHongTrangPhuc[$nvId]['tong'] ?? 0);

            $bangLuongThang[$u->id] = TinhLuongThang::tongHopThang(
                $nv,
                $tongDiemDanh['luong_co_ban'],
                $tongDiemDanh['luong_tang_ca'],
                $hoaHongHopDongCuoi,
                $hoaHongHopDongTrangPhuc
            );

            $chiTietHoaHong[$u->id] = [
                'ten_nhan_vien' => $u->name,
                'hoa_hong_cuoi' => $chiTietHoaHongCuoi[$nvId] ?? ['tong' => 0, 'danh_sach' => []],
                'hoa_hong_trang_phuc' => $chiTietHoaHongTrangPhuc[$nvId] ?? ['tong' => 0, 'danh_sach' => []],
            ];

            $tongHopDiemDanh = TinhLuongThang::tongHopDiemDanhThang($chamCongTheoUser->get($u->id, collect()));
            $bangLuongThang[$u->id] = self::ganTienPhatVaoBangLuong(
                $bangLuongThang[$u->id],
                $tongHopDiemDanh['tien_phat_di_muon'] + $tongHopDiemDanh['tien_phat_ve_som']
            );
            $luong = $bangLuongThang[$u->id];
            $tongPhat = (int) $luong['tien_phat'];
            $loaiNv = $nv?->loai_nhan_vien ?? '';

            $chiTietChuyenLuong[$u->id] = [
                'ten_nhan_vien' => $u->name,
                'loai_nhan_vien' => $loaiNv,
                'loai_nhan_vien_label' => filled($loaiNv)
                    ? (NhanVien::LOAI_NHAN_VIEN_OPTIONS[$loaiNv] ?? $loaiNv)
                    : 'Chưa phân loại',
                'thang' => $month,
                'nam' => $year,
                'tong_gio_lam' => $tongHopDiemDanh['tong_gio_lam_co_ban'],
                'tong_gio_tang_ca' => $tongHopDiemDanh['tong_gio_tang_ca'],
                'tien_phat_di_muon' => $tongHopDiemDanh['tien_phat_di_muon'],
                'tien_phat_ve_som' => $tongHopDiemDanh['tien_phat_ve_som'],
                'tong_phat' => $tongPhat,
                'luong_co_ban' => $luong['luong_co_ban'],
                'luong_tang_ca' => $luong['luong_tang_ca'],
                'phu_cap' => $luong['phu_cap'],
                'hoa_hong_hop_dong_cuoi' => $luong['hoa_hong_hop_dong_cuoi'],
                'hoa_hong_hop_dong_trang_phuc' => $luong['hoa_hong_hop_dong_trang_phuc'],
                'tong_luong_gop' => $luong['tong_luong_gop'],
                'tong_luong' => $luong['tong_luong_gop'],
                'tong_luong_thuc_nhan' => $luong['tong_luong'],
                'ngan_hang' => (string) ($nv?->ngan_hang ?? ''),
                'so_tai_khoan' => (string) ($nv?->so_tai_khoan ?? ''),
                'chu_tai_khoan' => (string) ($nv?->chu_tai_khoan ?? $u->name ?? ''),
            ];
        }

        return [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'ngayTrongThang' => $ngayTrongThang,
            'nhanVien' => $nhanVien,
            'bangChamCong' => $bangChamCong,
            'bangChamCongLuong' => $bangChamCongLuong,
            'bangLuongThang' => $bangLuongThang,
            'chiTietHoaHong' => $chiTietHoaHong,
            'chiTietChuyenLuong' => $chiTietChuyenLuong,
        ];
    }

    /**
     * @param  array{
     *     bangChamCongLuong: array<int, array<string, array{luong_co_ban: float, luong_tang_ca: float}>>,
     *     bangLuongThang: array<int, array<string, float>>,
     *     chiTietHoaHong: array<int, array<string, mixed>>,
     *     chiTietChuyenLuong: array<int, array<string, mixed>>,
     *     nhanVien: Collection<int, User>
     * }  $duLieu
     * @return array<string, mixed>
     */
    public static function taoSnapshot(array $duLieu): array
    {
        return [
            'bangChamCongLuong' => $duLieu['bangChamCongLuong'],
            'bangLuongThang' => $duLieu['bangLuongThang'],
            'chiTietHoaHong' => $duLieu['chiTietHoaHong'],
            'chiTietChuyenLuong' => $duLieu['chiTietChuyenLuong'],
            'nhanVienIds' => $duLieu['nhanVien']->pluck('id')->values()->all(),
            'nhanVienHienThi' => $duLieu['nhanVien']->map(function (User $u): array {
                $loaiNv = $u->nhanVien?->loai_nhan_vien ?? '';

                return [
                    'id' => (int) $u->id,
                    'name' => (string) ($u->name ?? ''),
                    'email' => (string) ($u->email ?? ''),
                    'loai_nhan_vien' => (string) $loaiNv,
                    'loai_nhan_vien_label' => filled($loaiNv)
                        ? (NhanVien::LOAI_NHAN_VIEN_OPTIONS[$loaiNv] ?? $loaiNv)
                        : '',
                ];
            })->values()->all(),
            'da_chuyen_user_ids' => [],
        ];
    }

    /**
     * Dữ liệu hiển thị hoàn toàn từ bản ghi chốt lương (không tính lại từ DB).
     *
     * @return array{
     *     month: int,
     *     year: int,
     *     start: Carbon,
     *     end: Carbon,
     *     ngayTrongThang: list<Carbon>,
     *     nhanVien: Collection<int, object>,
     *     bangChamCong: array<string, array<int, mixed>>,
     *     bangChamCongLuong: array<int, array<string, array{luong_co_ban: float, luong_tang_ca: float}>>,
     *     bangLuongThang: array<int, array<string, float>>,
     *     chiTietHoaHong: array<int, array<string, mixed>>,
     *     chiTietChuyenLuong: array<int, array<string, mixed>>
     * }
     */
    public static function tuChotLuongThang(ChotLuongThang $chotLuong): array
    {
        $month = (int) $chotLuong->thang;
        $year = (int) $chotLuong->nam;
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $ngayTrongThang = [];
        for ($d = (clone $start); $d->lte($end); $d->addDay()) {
            $ngayTrongThang[] = (clone $d);
        }

        $snapshot = $chotLuong->du_lieu ?? [];
        $parsed = self::tuSnapshot($snapshot);
        $bangChamCongLuong = self::normalizeUserIdKeys($parsed['bangChamCongLuong']);
        $bangLuongThang = self::hoanThienBangLuongThang(
            self::normalizeUserIdKeys($parsed['bangLuongThang']),
            $bangChamCongLuong
        );
        $chiTietChuyenLuong = self::dongBoChiTietChuyenLuong(
            self::normalizeUserIdKeys($parsed['chiTietChuyenLuong']),
            $bangLuongThang
        );

        return [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'ngayTrongThang' => $ngayTrongThang,
            'nhanVien' => self::nhanVienTuSnapshot($snapshot),
            'bangChamCong' => [],
            'bangChamCongLuong' => $bangChamCongLuong,
            'bangLuongThang' => $bangLuongThang,
            'chiTietHoaHong' => self::normalizeUserIdKeys($parsed['chiTietHoaHong']),
            'chiTietChuyenLuong' => $chiTietChuyenLuong,
        ];
    }

    /**
     * @param  array<string, float|int>  $bangLuong
     * @return array<string, float|int>
     */
    private static function ganTienPhatVaoBangLuong(array $bangLuong, int $tienPhat): array
    {
        $gross = (float) ($bangLuong['tong_luong'] ?? 0);

        return array_merge($bangLuong, [
            'tien_phat' => $tienPhat,
            'tong_luong_gop' => $gross,
            'tong_luong' => max(0, $gross - $tienPhat),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $bangLuongThang
     * @param  array<int, array<string, array<string, mixed>>>  $bangChamCongLuong
     * @return array<int, array<string, mixed>>
     */
    private static function hoanThienBangLuongThang(array $bangLuongThang, array $bangChamCongLuong): array
    {
        foreach ($bangLuongThang as $userId => $row) {
            $tienPhat = array_key_exists('tien_phat', $row)
                ? (int) $row['tien_phat']
                : self::tongPhatTuBangChamCongLuong($bangChamCongLuong[$userId] ?? []);

            $gross = array_key_exists('tong_luong_gop', $row)
                ? (float) $row['tong_luong_gop']
                : (float) ($row['tong_luong'] ?? 0);

            $bangLuongThang[$userId] = array_merge($row, [
                'tien_phat' => $tienPhat,
                'tong_luong_gop' => $gross,
                'tong_luong' => max(0, $gross - $tienPhat),
            ]);
        }

        return $bangLuongThang;
    }

    /**
     * @param  array<int, array<string, mixed>>  $chiTietChuyenLuong
     * @param  array<int, array<string, mixed>>  $bangLuongThang
     * @return array<int, array<string, mixed>>
     */
    private static function dongBoChiTietChuyenLuong(array $chiTietChuyenLuong, array $bangLuongThang): array
    {
        foreach ($chiTietChuyenLuong as $userId => $row) {
            $luong = $bangLuongThang[$userId] ?? null;
            if ($luong === null) {
                continue;
            }

            $chiTietChuyenLuong[$userId]['tong_phat'] = (int) ($luong['tien_phat'] ?? 0);
            $chiTietChuyenLuong[$userId]['tong_luong_gop'] = (float) ($luong['tong_luong_gop'] ?? 0);
            $chiTietChuyenLuong[$userId]['tong_luong'] = (float) ($luong['tong_luong_gop'] ?? 0);
            $chiTietChuyenLuong[$userId]['tong_luong_thuc_nhan'] = (float) ($luong['tong_luong'] ?? 0);
        }

        return $chiTietChuyenLuong;
    }

    /**
     * @param  array<string, array<string, mixed>>  $bangNgay
     */
    private static function tongPhatTuBangChamCongLuong(array $bangNgay): int
    {
        $tong = 0;
        foreach ($bangNgay as $ngay) {
            $tong += (int) ($ngay['tong_phat'] ?? 0);
        }

        return $tong;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return Collection<int, object>
     */
    private static function nhanVienTuSnapshot(array $snapshot): Collection
    {
        $hienThi = $snapshot['nhanVienHienThi'] ?? [];
        if ($hienThi !== []) {
            return collect($hienThi)->map(function (array $row): object {
                $nv = new \stdClass;
                $nv->loai_nhan_vien = (string) ($row['loai_nhan_vien'] ?? '');

                return (object) [
                    'id' => (int) ($row['id'] ?? 0),
                    'name' => (string) ($row['name'] ?? ''),
                    'email' => (string) ($row['email'] ?? ''),
                    'nhanVien' => $nv,
                ];
            })->values();
        }

        $ids = array_map('intval', $snapshot['nhanVienIds'] ?? []);
        if ($ids === []) {
            return collect();
        }

        $theoId = User::query()
            ->whereIn('id', $ids)
            ->with('nhanVien')
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $theoId->get($id))
            ->filter()
            ->values();
    }

    /**
     * @template T
     *
     * @param  array<int|string, T>  $data
     * @return array<int, T>
     */
    private static function normalizeUserIdKeys(array $data): array
    {
        $ketQua = [];
        foreach ($data as $userId => $value) {
            $ketQua[(int) $userId] = $value;
        }

        return $ketQua;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array{
     *     bangChamCongLuong: array<int, array<string, array{luong_co_ban: float, luong_tang_ca: float}>>,
     *     bangLuongThang: array<int, array<string, float>>,
     *     chiTietHoaHong: array<int, array<string, mixed>>,
     *     chiTietChuyenLuong: array<int, array<string, mixed>>
     * }
     */
    public static function tuSnapshot(array $snapshot): array
    {
        return [
            'bangChamCongLuong' => $snapshot['bangChamCongLuong'] ?? [],
            'bangLuongThang' => $snapshot['bangLuongThang'] ?? [],
            'chiTietHoaHong' => $snapshot['chiTietHoaHong'] ?? [],
            'chiTietChuyenLuong' => $snapshot['chiTietChuyenLuong'] ?? [],
        ];
    }
}
