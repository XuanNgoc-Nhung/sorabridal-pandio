<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\TaiLieu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaiLieuController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules()));

        $query = TaiLieu::query()->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_tai_lieu', 'like', $like)
                    ->orWhere('file', 'like', $like);
            });
        }

        return $this->apiListFromQuery($query, fn (TaiLieu $item) => $this->formatTaiLieu($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tap_tin' => 'required|file|max:20480',
            'ten_tai_lieu' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
        ]);

        $tapTin = $request->file('tap_tin');
        $duongDanLuuTru = $tapTin->store('taiLieu', 'public');

        try {
            $taiLieu = TaiLieu::create([
                'ten_tai_lieu' => $validated['ten_tai_lieu'],
                'mo_ta' => $validated['mo_ta'] ?? null,
                'file' => $tapTin->getClientOriginalName(),
                'duong_dan' => $duongDanLuuTru,
            ]);
        } catch (\Throwable $th) {
            Storage::disk('public')->delete($duongDanLuuTru);
            throw $th;
        }

        return $this->apiSuccess(
            ['item' => $this->formatTaiLieu($taiLieu)],
            'Đã thêm tài liệu thành công.',
            201
        );
    }

    public function destroy(TaiLieu $taiLieu): JsonResponse
    {
        if (! empty($taiLieu->duong_dan) && Storage::disk('public')->exists($taiLieu->duong_dan)) {
            Storage::disk('public')->delete($taiLieu->duong_dan);
        }

        $taiLieu->delete();

        return $this->apiSuccess(message: 'Đã xóa tài liệu thành công.');
    }

    private function formatTaiLieu(TaiLieu $taiLieu): array
    {
        return [
            'id' => (int) $taiLieu->id,
            'ten_tai_lieu' => $taiLieu->ten_tai_lieu,
            'mo_ta' => $taiLieu->mo_ta,
            'file' => $taiLieu->file,
            'duong_dan' => $taiLieu->duong_dan,
            'file_url' => $this->storageUrl($taiLieu->duong_dan),
            'created_at' => $taiLieu->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $taiLieu->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
