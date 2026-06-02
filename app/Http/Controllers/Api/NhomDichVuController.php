<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\DichVuLe;
use App\Models\NhomDichVu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NhomDichVuController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules()));

        $query = NhomDichVu::query()->with(['dichVuLe', 'nguoiTao'])->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_nhom', 'like', $like)
                    ->orWhere('ma_nhom', 'like', $like)
                    ->orWhere('the', 'like', $like)
                    ->orWhereHas('dichVuLe', function ($dvq) use ($like) {
                        $dvq->where('ten_dich_vu', 'like', $like);
                    });
            });
        }

        return $this->apiListFromQuery($query, fn (NhomDichVu $item) => $this->formatNhomDichVu($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'ma_nhom' => 'nullable|string|max:50',
            'gia_tien' => 'nullable|numeric|min:0',
            'the' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'dich_vu_le_ids' => 'nullable|array',
            'dich_vu_le_ids.*' => 'integer|exists:dich_vu_le,id',
        ]);

        $ids = array_map('intval', (array) ($validated['dich_vu_le_ids'] ?? []));
        $giaGoc = empty($ids)
            ? 0
            : (float) DichVuLe::whereIn('id', $ids)->sum('gia_dich_vu');

        $nhom = NhomDichVu::create([
            'ten_nhom' => $validated['ten_nhom'],
            'ma_nhom' => $validated['ma_nhom'] ?? null,
            'gia_tien' => $validated['gia_tien'] ?? null,
            'gia_goc' => $giaGoc,
            'the' => $validated['the'] ?? null,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? NhomDichVu::TRANG_THAI_HIEN_THI),
            'nguoi_tao_id' => $request->user()?->id,
        ]);

        if (! empty($ids)) {
            $nhom->dichVuLe()->attach(collect($ids)->mapWithKeys(fn ($id) => [$id => ['so_luong' => 1]])->all());
        }

        $nhom->load(['dichVuLe', 'nguoiTao']);

        return $this->apiSuccess(
            ['item' => $this->formatNhomDichVu($nhom)],
            'Đã thêm nhóm dịch vụ thành công.',
            201
        );
    }

    public function update(Request $request, NhomDichVu $nhomDichVu): JsonResponse
    {
        $validated = $request->validate([
            'ten_nhom' => 'required|string|max:255',
            'ma_nhom' => 'nullable|string|max:50',
            'gia_tien' => 'nullable|numeric|min:0',
            'the' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'nullable|integer|in:0,1',
            'dich_vu_le_ids' => 'nullable|array',
            'dich_vu_le_ids.*' => 'integer|exists:dich_vu_le,id',
        ]);

        $ids = array_map('intval', (array) ($validated['dich_vu_le_ids'] ?? []));
        $giaGoc = empty($ids)
            ? 0
            : (float) DichVuLe::whereIn('id', $ids)->sum('gia_dich_vu');

        $nhomDichVu->update([
            'ten_nhom' => $validated['ten_nhom'],
            'ma_nhom' => $validated['ma_nhom'] ?? null,
            'gia_tien' => $validated['gia_tien'] ?? null,
            'gia_goc' => $giaGoc,
            'the' => $validated['the'] ?? null,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? NhomDichVu::TRANG_THAI_HIEN_THI),
        ]);

        $nhomDichVu->dichVuLe()->sync(collect($ids)->mapWithKeys(fn ($id) => [$id => ['so_luong' => 1]])->all());
        $nhomDichVu->load(['dichVuLe', 'nguoiTao']);

        return $this->apiSuccess(
            ['item' => $this->formatNhomDichVu($nhomDichVu)],
            'Đã cập nhật nhóm dịch vụ thành công.'
        );
    }

    public function destroy(NhomDichVu $nhomDichVu): JsonResponse
    {
        $nhomDichVu->dichVuLe()->detach();
        $nhomDichVu->delete();

        return $this->apiSuccess(message: 'Đã xóa nhóm dịch vụ thành công.');
    }

    private function formatNhomDichVu(NhomDichVu $nhom): array
    {
        return [
            'id' => (int) $nhom->id,
            'ten_nhom' => $nhom->ten_nhom,
            'ma_nhom' => $nhom->ma_nhom,
            'slug' => $nhom->slug,
            'gia_tien' => $nhom->gia_tien !== null ? (float) $nhom->gia_tien : null,
            'gia_goc' => $nhom->gia_goc !== null ? (float) $nhom->gia_goc : null,
            'the' => $nhom->the,
            'ghi_chu' => $nhom->ghi_chu,
            'mo_ta' => $nhom->mo_ta,
            'trang_thai' => (int) $nhom->trang_thai,
            'dich_vu_le' => $nhom->dichVuLe?->map(fn (DichVuLe $dv) => [
                'id' => (int) $dv->id,
                'ten_dich_vu' => $dv->ten_dich_vu,
                'ma_dich_vu' => $dv->ma_dich_vu,
                'gia_dich_vu' => (float) $dv->gia_dich_vu,
                'so_luong' => (int) ($dv->pivot?->so_luong ?? 1),
            ])->values()->all() ?? [],
            'nguoi_tao' => $nhom->nguoiTao ? [
                'id' => (int) $nhom->nguoiTao->id,
                'name' => $nhom->nguoiTao->name,
            ] : null,
            'created_at' => $nhom->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $nhom->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
