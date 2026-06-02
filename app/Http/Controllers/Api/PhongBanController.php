<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\PhongBan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PhongBanController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(PhongBan::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]));

        $query = PhongBan::query()->withCount('nhanViens');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_phong_ban', 'like', $like)
                    ->orWhere('ma_phong_ban', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? PhongBan::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, PhongBan::SAP_XEP_OPTIONS)) {
            $sapXepTheo = PhongBan::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            PhongBan::SAP_XEP_NHAN_VIENS => $query->orderBy('nhan_viens_count', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };
        $query->orderBy('id', $thuTu);

        return $this->apiListFromQuery($query, fn (PhongBan $item) => $this->formatPhongBan($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_phong_ban' => 'required|string|max:255',
            'ma_phong_ban' => ['required', 'string', 'max:50', Rule::unique('phong_ban', 'ma_phong_ban')],
            'mo_ta' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
        ]);

        $phongBan = PhongBan::create($validated);
        $phongBan->loadCount('nhanViens');

        return $this->apiSuccess(
            ['item' => $this->formatPhongBan($phongBan)],
            'Đã thêm phòng ban thành công.',
            201
        );
    }

    public function update(Request $request, PhongBan $phongBan): JsonResponse
    {
        $validated = $request->validate([
            'ten_phong_ban' => 'required|string|max:255',
            'ma_phong_ban' => ['required', 'string', 'max:50', Rule::unique('phong_ban', 'ma_phong_ban')->ignore($phongBan->id)],
            'mo_ta' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
        ]);

        $phongBan->update($validated);
        $phongBan->loadCount('nhanViens');

        return $this->apiSuccess(
            ['item' => $this->formatPhongBan($phongBan)],
            'Đã cập nhật phòng ban thành công.'
        );
    }

    public function destroy(PhongBan $phongBan): JsonResponse
    {
        if ($phongBan->nhanViens()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Đang tồn tại nhân sự trực thuộc phòng ban này. Không thể xoá.',
            ], 422);
        }

        $phongBan->delete();

        return $this->apiSuccess(message: 'Đã xóa phòng ban thành công.');
    }

    private function formatPhongBan(PhongBan $phongBan): array
    {
        return [
            'id' => (int) $phongBan->id,
            'ten_phong_ban' => $phongBan->ten_phong_ban,
            'ma_phong_ban' => $phongBan->ma_phong_ban,
            'mo_ta' => $phongBan->mo_ta,
            'ghi_chu' => $phongBan->ghi_chu,
            'so_nhan_vien' => (int) ($phongBan->nhan_viens_count ?? 0),
            'created_at' => $phongBan->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $phongBan->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
