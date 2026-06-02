<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\DangKyTuVan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TuVanController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(DangKyTuVan::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]));

        $query = DangKyTuVan::query();

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($q) use ($like) {
                $q->where('ten_co_dau', 'like', $like)
                    ->orWhere('ten_chu_re', 'like', $like)
                    ->orWhere('so_dien_thoai', 'like', $like)
                    ->orWhere('phim_truong_quan_tam', 'like', $like)
                    ->orWhere('goi_dich_vu_quan_tam', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? DangKyTuVan::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, DangKyTuVan::SAP_XEP_OPTIONS)) {
            $sapXepTheo = DangKyTuVan::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            DangKyTuVan::SAP_XEP_NGAY_CUOI => $query
                ->orderByRaw('ngay_cuoi_du_kien IS NULL')
                ->orderBy('ngay_cuoi_du_kien', $thuTu),
            default => $query->orderBy('created_at', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        return $this->apiListFromQuery($query, fn (DangKyTuVan $item) => $this->formatDangKy($item), $request);
    }

    private function formatDangKy(DangKyTuVan $item): array
    {
        return [
            'id' => (int) $item->id,
            'ten_co_dau' => $item->ten_co_dau,
            'ten_chu_re' => $item->ten_chu_re,
            'so_dien_thoai' => $item->so_dien_thoai,
            'phim_truong_quan_tam' => $item->phim_truong_quan_tam,
            'goi_dich_vu_quan_tam' => $item->goi_dich_vu_quan_tam,
            'ghi_chu' => $item->ghi_chu,
            'ngay_cuoi_du_kien' => $item->ngay_cuoi_du_kien?->format('Y-m-d'),
            'created_at' => $item->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
