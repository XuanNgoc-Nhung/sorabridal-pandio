<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\DichVuLe;
use App\Models\PhongBan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DichVuLeController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'trang_thai' => 'nullable|in:0,1',
            'phong_ban_id' => 'nullable|integer|exists:phong_ban,id',
            'sap_xep_theo' => 'nullable|string|in:'.DichVuLe::SAP_XEP_TEN.','.DichVuLe::SAP_XEP_GIA,
            'thu_tu' => 'nullable|in:asc,desc',
        ]));

        $query = DichVuLe::query()->with(['nguoiTao']);

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_dich_vu', 'like', $like)
                    ->orWhere('ma_dich_vu', 'like', $like);
            });
        }

        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        if ($request->filled('phong_ban_id')) {
            $phongBanId = (int) $request->input('phong_ban_id');
            $phongBan = PhongBan::find($phongBanId);
            if ($phongBan) {
                $query->coPhongBan($phongBanId, $phongBan->ma_phong_ban);
            }
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? DichVuLe::SAP_XEP_TEN;
        if (! in_array($sapXepTheo, [DichVuLe::SAP_XEP_TEN, DichVuLe::SAP_XEP_GIA], true)) {
            $sapXepTheo = DichVuLe::SAP_XEP_TEN;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        $cotSapXep = $sapXepTheo === DichVuLe::SAP_XEP_GIA ? 'gia_dich_vu' : 'ten_dich_vu';
        $query->orderBy($cotSapXep, $thuTu);

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
            'phong_ban_id' => 'required|array|min:1',
            'phong_ban_id.*' => 'integer|exists:phong_ban,id',
        ]);

        $dichVu = DichVuLe::create([
            'ten_dich_vu' => $validated['ten_dich_vu'],
            'ma_dich_vu' => $validated['ma_dich_vu'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? DichVuLe::TRANG_THAI_HIEN_THI),
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'gia_dich_vu' => $validated['gia_dich_vu'],
            'phong_ban_id' => $this->formatPhongBanId($validated['phong_ban_id']),
            'nguoi_tao_id' => $request->user()?->id,
        ]);

        $dichVu->load(['nguoiTao']);

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
            'phong_ban_id' => 'required|array|min:1',
            'phong_ban_id.*' => 'integer|exists:phong_ban,id',
        ]);

        $dichVuLe->update([
            'ten_dich_vu' => $validated['ten_dich_vu'],
            'ma_dich_vu' => $validated['ma_dich_vu'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? DichVuLe::TRANG_THAI_HIEN_THI),
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'gia_dich_vu' => $validated['gia_dich_vu'],
            'phong_ban_id' => $this->formatPhongBanId($validated['phong_ban_id']),
        ]);

        $dichVuLe->load(['nguoiTao']);

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
        $phongBans = PhongBan::query()->orderBy('ten_phong_ban')->get();
        $phongBanIds = $dichVu->phongBanIdList($phongBans);
        $phongBanItems = $phongBans
            ->whereIn('id', $phongBanIds)
            ->map(fn (PhongBan $pb) => [
                'id' => (int) $pb->id,
                'ten_phong_ban' => $pb->ten_phong_ban,
                'ma_phong_ban' => $pb->ma_phong_ban,
            ])
            ->values()
            ->all();

        return [
            'id' => (int) $dichVu->id,
            'ten_dich_vu' => $dichVu->ten_dich_vu,
            'ma_dich_vu' => $dichVu->ma_dich_vu,
            'slug' => $dichVu->slug,
            'mo_ta' => $dichVu->mo_ta,
            'trang_thai' => (int) $dichVu->trang_thai,
            'ghi_chu' => $dichVu->ghi_chu,
            'gia_dich_vu' => (float) $dichVu->gia_dich_vu,
            'phong_ban_id' => $phongBanIds,
            'phong_ban_text' => $dichVu->tenPhongBanHienThi($phongBans),
            'phong_ban' => $phongBanItems,
            'nguoi_tao' => $dichVu->nguoiTao ? [
                'id' => (int) $dichVu->nguoiTao->id,
                'name' => $dichVu->nguoiTao->name,
            ] : null,
            'created_at' => $dichVu->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $dichVu->updated_at?->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * @param  array<int|string>  $ids
     */
    private function formatPhongBanId(array $ids): ?string
    {
        $normalized = array_values(array_unique(array_filter(array_map('intval', $ids))));

        return $normalized === [] ? null : implode(',', $normalized);
    }
}
