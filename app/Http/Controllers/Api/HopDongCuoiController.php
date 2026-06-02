<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\HopDongCuoi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HopDongCuoiController extends Controller
{
    use RespondsWithJson;

    /**
     * GET /api/admin/hop-dong-cuoi
     * Danh sách hợp đồng cưới có trạng thái khác "nhap", kèm dữ liệu liên quan.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'trang_thai_hop_dong' => 'nullable|string|max:50',
        ]));

        $query = HopDongCuoi::query()
            ->with([
                'concept',
                'nhomDichVu',
                'thoChup.user',
                'thoChup.phongBans',
                'thoMake.user',
                'thoMake.phongBans',
                'thoEdit.user',
                'thoEdit.phongBans',
                'hopDongCuoiNhomDichVu.dichVuLe.phongBan',
                'hopDongCuoiNhomDichVu.nhomDichVu',
                'hopDongCuoiDichVuLe.dichVuLe.phongBan',
                'hopDongCuoiTrangPhuc.trangPhuc',
                'thanhVienHopDongCuis.nhanVien.user',
                'thanhVienHopDongCuis.nhanVien.phongBans',
            ])
            ->where('trang_thai_hop_dong', '!=', 'nhap')
            ->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($q) use ($like) {
                $q->where('ma_hop_dong', 'like', $like)
                    ->orWhere('ten_co_dau', 'like', $like)
                    ->orWhere('ten_chu_re', 'like', $like)
                    ->orWhere('email_sdt_co_dau', 'like', $like)
                    ->orWhere('email_sdt_chu_re', 'like', $like);
            });
        }

        $trangThai = trim((string) ($validated['trang_thai_hop_dong'] ?? ''));
        if ($trangThai !== '') {
            $query->where('trang_thai_hop_dong', $trangThai);
        }

        ['start' => $start, 'limit' => $limit] = $this->paginationFromRequest($request);
        $total = (clone $query)->count();

        $items = $query->offset($start)->limit($limit)->get()->map(static function (HopDongCuoi $hop): array {
            $formatTenNhom = static function (?string $ten, ?string $nhom): ?string {
                $ten = trim((string) ($ten ?? ''));
                $nhom = trim((string) ($nhom ?? ''));

                if ($ten === '') {
                    return null;
                }

                if ($nhom === '') {
                    return $ten;
                }

                return $ten.' ('.$nhom.')';
            };

            $formatTenNhomNhieu = static function (?string $ten, array $nhoms) use ($formatTenNhom): ?string {
                $nhomStr = collect($nhoms)
                    ->filter(fn ($v) => is_string($v) && trim($v) !== '')
                    ->map(fn ($v) => trim($v))
                    ->unique()
                    ->values()
                    ->implode(', ');

                return $formatTenNhom($ten, $nhomStr);
            };

            $comboDichVu = $hop->hopDongCuoiNhomDichVu
                ->where('trang_thai_su_dung', 1)
                ->values()
                ->map(static function ($r): array {
                    $tenDichVu = $r->dichVuLe?->ten_dich_vu;
                    $tenNhom = $r->nhomDichVu?->ten_nhom;
                    $tenHienThi = null;
                    if (is_string($tenDichVu) && trim($tenDichVu) !== '') {
                        $tenHienThi = trim($tenDichVu).(is_string($tenNhom) && trim($tenNhom) !== '' ? ' ('.trim($tenNhom).')' : '');
                    }

                    return [
                        'id' => (int) $r->dich_vu_le_id,
                        'ten_dich_vu' => $r->dichVuLe?->ten_dich_vu,
                        'ten_dich_vu_hien_thi' => $tenHienThi,
                        'ma_dich_vu' => $r->dichVuLe?->ma_dich_vu,
                        'gia_dich_vu' => $r->dichVuLe?->gia_dich_vu !== null ? (float) $r->dichVuLe->gia_dich_vu : null,
                        'so_luong' => (int) ($r->dichVuLe?->pivot?->so_luong ?? 1),
                    ];
                })
                ->all();

            $dichVuLeThem = $hop->hopDongCuoiDichVuLe
                ->values()
                ->map(static function ($r): array {
                    $tenDichVu = $r->dichVuLe?->ten_dich_vu;
                    $tenPhongBan = $r->dichVuLe?->phongBan?->ten_phong_ban;
                    $tenHienThi = null;
                    if (is_string($tenDichVu) && trim($tenDichVu) !== '') {
                        $tenHienThi = trim($tenDichVu).(is_string($tenPhongBan) && trim($tenPhongBan) !== '' ? ' ('.trim($tenPhongBan).')' : '');
                    }

                    return [
                        'id' => (int) $r->dich_vu_le_id,
                        'ten_dich_vu' => $r->dichVuLe?->ten_dich_vu,
                        'ten_dich_vu_hien_thi' => $tenHienThi,
                        'ma_dich_vu' => $r->dichVuLe?->ma_dich_vu,
                        'gia_dich_vu' => $r->dichVuLe?->gia_dich_vu !== null ? (float) $r->dichVuLe->gia_dich_vu : null,
                        'so_luong' => (int) ($r->so_luong ?? 1),
                    ];
                })
                ->all();

            $trangPhuc = $hop->hopDongCuoiTrangPhuc
                ->values()
                ->map(static function ($r): array {
                    return [
                        'id' => (int) $r->trang_phuc_id,
                        'ten_san_pham' => $r->trangPhuc?->ten_san_pham,
                        'ma_san_pham' => $r->trangPhuc?->ma_san_pham,
                    ];
                })
                ->all();

            $thanhVienSale = $hop->thanhVienHopDongCuis
                ->values()
                ->map(static function ($r): array {
                    $nv = $r->nhanVien;
                    $tenGoc = $nv?->user?->name;
                    $phongBans = $nv?->phongBans?->pluck('ten_phong_ban')?->all() ?? [];
                    $ten = null;
                    if (is_string($tenGoc) && trim($tenGoc) !== '') {
                        $nhomStr = collect($phongBans)
                            ->filter(fn ($v) => is_string($v) && trim($v) !== '')
                            ->map(fn ($v) => trim($v))
                            ->unique()
                            ->values()
                            ->implode(', ');
                        $ten = trim($tenGoc).($nhomStr !== '' ? ' ('.$nhomStr.')' : '');
                    }

                    return [
                        'nhan_vien_id' => (int) $r->nhan_vien_id,
                        'vai_tro' => $r->vai_tro,
                        'ten' => $ten,
                        'ten_goc' => $tenGoc,
                    ];
                })
                ->all();

            $thoChupTen = $formatTenNhomNhieu(
                $hop->thoChup?->user?->name,
                $hop->thoChup?->phongBans?->pluck('ten_phong_ban')?->all() ?? []
            );
            $thoMakeTen = $formatTenNhomNhieu(
                $hop->thoMake?->user?->name,
                $hop->thoMake?->phongBans?->pluck('ten_phong_ban')?->all() ?? []
            );
            $thoEditTen = $formatTenNhomNhieu(
                $hop->thoEdit?->user?->name,
                $hop->thoEdit?->phongBans?->pluck('ten_phong_ban')?->all() ?? []
            );

            $dichVuText = collect([
                ...collect($comboDichVu)->pluck('ten_dich_vu_hien_thi')->filter()->all(),
                ...collect($dichVuLeThem)->pluck('ten_dich_vu_hien_thi')->filter()->all(),
            ])->filter()->values()->implode(', ');

            return [
                'id' => (int) $hop->id,
                'ma_hop_dong' => $hop->ma_hop_dong,
                'ten_co_dau' => $hop->ten_co_dau,
                'ten_chu_re' => $hop->ten_chu_re,
                'email_sdt_co_dau' => $hop->email_sdt_co_dau,
                'email_sdt_chu_re' => $hop->email_sdt_chu_re,
                'ngay_chup_du_kien' => $hop->ngay_chup_du_kien?->format('Y-m-d'),
                'ngay_chup_thuc_te' => $hop->ngay_chup_thuc_te?->format('Y-m-d'),
                'buoi_chup' => $hop->buoi_chup,
                'ngay_cuoi_du_kien' => $hop->ngay_cuoi_du_kien?->format('Y-m-d'),
                'ngay_cuoi_chinh_thuc' => $hop->ngay_cuoi_chinh_thuc?->format('Y-m-d'),
                'dia_diem_chup' => $hop->dia_diem_chup,
                'kenh_tiep_can' => $hop->kenh_tiep_can,
                'yeu_cau_dac_biet' => $hop->yeu_cau_dac_biet,
                'tong_tien' => (float) ($hop->tong_tien ?? 0),
                'chiet_khau' => (float) ($hop->chiet_khau ?? 0),
                'tien_coc' => (float) ($hop->tien_coc ?? 0),
                'trang_thai_hop_dong' => $hop->trang_thai_hop_dong,
                'loai_dich_vu' => $hop->loai_dich_vu,
                'concept_text' => $hop->concept?->ten_concept,
                'nhom_dich_vu_text' => $hop->nhomDichVu?->ten_nhom,
                'nguoi_phu_trach_text' => collect([
                    $thoChupTen ? ($thoChupTen.' [chup]') : null,
                    $thoMakeTen ? ($thoMakeTen.' [make]') : null,
                    $thoEditTen ? ($thoEditTen.' [edit]') : null,
                ])
                    ->filter(fn ($v) => is_string($v) && trim($v) !== '')
                    ->values()
                    ->implode(', '),
                'dich_vu_text' => $dichVuText !== '' ? $dichVuText : null,
                //thời gian tạo đưa về định dạng dd/mm/yyyy hh:mm:ss
                'created_at' => $hop->created_at?->format('d/m/Y H:i:s'),//thời gian tạo đưa về định dạng dd/mm/yyyy hh:mm:ss
                'updated_at' => $hop->updated_at?->format('d/m/Y H:i:s'),//thời gian cập nhật đưa về định dạng dd/mm/yyyy hh:mm:ss
            ];
        })->values()->all();

        return $this->apiList($items, $total, $start, $limit);
    }
}

