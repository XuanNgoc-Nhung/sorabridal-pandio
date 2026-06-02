<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\DichVuLe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DichVuLeController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules()));

        $query = DichVuLe::query()->with(['nguoiTao', 'phongBan'])->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_dich_vu', 'like', $like)
                    ->orWhere('ma_dich_vu', 'like', $like);
            });
        }

        return $this->apiListFromQuery($query, fn (DichVuLe $item) => $this->formatDichVuLe($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'ma_dich_vu' => ['required', 'string', 'max:50', Rule::unique('dich_vu_le', 'ma_dich_vu')],
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'ghi_chu' => 'nullable|string',
            'gia_dich_vu' => 'required|numeric|min:0',
            'phong_ban_id' => 'required|integer|exists:phong_ban,id',
        ]);

        $dichVu = DichVuLe::create([
            'ten_dich_vu' => $validated['ten_dich_vu'],
            'ma_dich_vu' => $validated['ma_dich_vu'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? DichVuLe::TRANG_THAI_HIEN_THI),
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'gia_dich_vu' => $validated['gia_dich_vu'],
            'phong_ban_id' => (int) $validated['phong_ban_id'],
            'nguoi_tao_id' => $request->user()?->id,
        ]);

        $dichVu->load(['nguoiTao', 'phongBan']);

        return $this->apiSuccess(
            ['item' => $this->formatDichVuLe($dichVu)],
            'Đã thêm dịch vụ lẻ thành công.',
            201
        );
    }

    public function update(Request $request, DichVuLe $dichVuLe): JsonResponse
    {
        $validated = $request->validate([
            'ten_dich_vu' => 'required|string|max:255',
            'ma_dich_vu' => ['required', 'string', 'max:50', Rule::unique('dich_vu_le', 'ma_dich_vu')->ignore($dichVuLe->id)],
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'ghi_chu' => 'nullable|string',
            'gia_dich_vu' => 'required|numeric|min:0',
            'phong_ban_id' => 'required|integer|exists:phong_ban,id',
        ]);

        $dichVuLe->update([
            'ten_dich_vu' => $validated['ten_dich_vu'],
            'ma_dich_vu' => $validated['ma_dich_vu'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? DichVuLe::TRANG_THAI_HIEN_THI),
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'gia_dich_vu' => $validated['gia_dich_vu'],
            'phong_ban_id' => (int) $validated['phong_ban_id'],
        ]);

        $dichVuLe->load(['nguoiTao', 'phongBan']);

        return $this->apiSuccess(
            ['item' => $this->formatDichVuLe($dichVuLe)],
            'Đã cập nhật dịch vụ lẻ thành công.'
        );
    }

    public function destroy(DichVuLe $dichVuLe): JsonResponse
    {
        $dichVuLe->delete();

        return $this->apiSuccess(message: 'Đã xóa dịch vụ lẻ thành công.');
    }

    private function formatDichVuLe(DichVuLe $dichVu): array
    {
        return [
            'id' => (int) $dichVu->id,
            'ten_dich_vu' => $dichVu->ten_dich_vu,
            'ma_dich_vu' => $dichVu->ma_dich_vu,
            'slug' => $dichVu->slug,
            'mo_ta' => $dichVu->mo_ta,
            'trang_thai' => (int) $dichVu->trang_thai,
            'ghi_chu' => $dichVu->ghi_chu,
            'gia_dich_vu' => (float) $dichVu->gia_dich_vu,
            'phong_ban_id' => $dichVu->phong_ban_id ? (int) $dichVu->phong_ban_id : null,
            'phong_ban' => $dichVu->phongBan ? [
                'id' => (int) $dichVu->phongBan->id,
                'ten_phong_ban' => $dichVu->phongBan->ten_phong_ban,
                'ma_phong_ban' => $dichVu->phongBan->ma_phong_ban,
            ] : null,
            'nguoi_tao' => $dichVu->nguoiTao ? [
                'id' => (int) $dichVu->nguoiTao->id,
                'name' => $dichVu->nguoiTao->name,
            ] : null,
            'created_at' => $dichVu->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $dichVu->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
