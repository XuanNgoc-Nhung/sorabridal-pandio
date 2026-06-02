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
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules()));

        $query = DangKyTuVan::query()->orderByDesc('created_at')->orderByDesc('id');

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
            'created_at' => $item->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
