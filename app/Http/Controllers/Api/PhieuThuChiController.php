<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\PhieuThuChi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhieuThuChiController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'loai_phieu' => 'nullable|in:1,2',
            'trang_thai' => 'nullable|integer',
        ]));

        $query = PhieuThuChi::query()->with(['nguoiTao', 'nguoiDuyet'])->orderByDesc('created_at');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($q) use ($like) {
                $q->where('ly_do', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }
        if ($request->filled('loai_phieu')) {
            $query->where('loai_phieu', (int) $request->input('loai_phieu'));
        }
        if ($request->filled('trang_thai') && $request->input('trang_thai') !== '') {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        return $this->apiListFromQuery($query, fn (PhieuThuChi $item) => $this->formatPhieu($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loai_phieu' => 'required|in:1,2',
            'so_tien' => 'required|numeric|min:0',
            'ly_do' => 'required|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
        ]);

        $phieu = PhieuThuChi::create([
            'loai_phieu' => (int) $validated['loai_phieu'],
            'so_tien' => $validated['so_tien'],
            'ly_do' => $validated['ly_do'],
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'nguoi_tao_id' => $request->user()->id,
            'trang_thai' => PhieuThuChi::TRANG_THAI_CHO_XU_LY,
        ]);

        $phieu->load(['nguoiTao', 'nguoiDuyet']);

        return $this->apiSuccess(
            ['item' => $this->formatPhieu($phieu)],
            'Đã thêm phiếu thu chi thành công.',
            201
        );
    }

    public function update(Request $request, PhieuThuChi $phieuThuChi): JsonResponse
    {
        $validated = $request->validate([
            'loai_phieu' => 'required|in:1,2',
            'so_tien' => 'required|numeric|min:0',
            'ly_do' => 'required|string|max:255',
            'trang_thai' => 'nullable|in:-1,0,1,2',
            'ghi_chu' => 'nullable|string|max:500',
        ]);

        $phieuThuChi->update([
            'loai_phieu' => (int) $validated['loai_phieu'],
            'so_tien' => $validated['so_tien'],
            'ly_do' => $validated['ly_do'],
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? $phieuThuChi->trang_thai),
        ]);

        $phieuThuChi->load(['nguoiTao', 'nguoiDuyet']);

        return $this->apiSuccess(
            ['item' => $this->formatPhieu($phieuThuChi)],
            'Đã cập nhật phiếu thu chi thành công.'
        );
    }

    public function duyet(Request $request, PhieuThuChi $phieuThuChi): JsonResponse
    {
        if ((int) $phieuThuChi->trang_thai !== PhieuThuChi::TRANG_THAI_CHO_XU_LY) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ được duyệt phiếu đang chờ xử lý.',
            ], 422);
        }

        $phieuThuChi->update([
            'trang_thai' => PhieuThuChi::TRANG_THAI_DONG_Y,
            'nguoi_duyet_id' => $request->user()->id,
            'ngay_duyet' => now(),
        ]);

        $phieuThuChi->load(['nguoiTao', 'nguoiDuyet']);

        return $this->apiSuccess(
            ['item' => $this->formatPhieu($phieuThuChi)],
            'Đã duyệt phiếu thu chi.'
        );
    }

    public function huy(Request $request, PhieuThuChi $phieuThuChi): JsonResponse
    {
        if ((int) $phieuThuChi->trang_thai !== PhieuThuChi::TRANG_THAI_CHO_XU_LY) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ được hủy phiếu đang chờ xử lý.',
            ], 422);
        }

        $phieuThuChi->update([
            'trang_thai' => PhieuThuChi::TRANG_THAI_TU_CHOI,
            'nguoi_duyet_id' => $request->user()->id,
            'ngay_duyet' => now(),
        ]);

        $phieuThuChi->load(['nguoiTao', 'nguoiDuyet']);

        return $this->apiSuccess(
            ['item' => $this->formatPhieu($phieuThuChi)],
            'Đã hủy phiếu thu chi.'
        );
    }

    public function destroy(PhieuThuChi $phieuThuChi): JsonResponse
    {
        $phieuThuChi->delete();

        return $this->apiSuccess(message: 'Đã xóa phiếu thu chi thành công.');
    }

    private function formatPhieu(PhieuThuChi $phieu): array
    {
        return [
            'id' => (int) $phieu->id,
            'loai_phieu' => (int) $phieu->loai_phieu,
            'so_tien' => (float) $phieu->so_tien,
            'ly_do' => $phieu->ly_do,
            'trang_thai' => (int) $phieu->trang_thai,
            'ghi_chu' => $phieu->ghi_chu,
            'ngay_duyet' => $phieu->ngay_duyet?->format('d/m/Y H:i:s'),
            'nguoi_tao' => $phieu->nguoiTao ? [
                'id' => (int) $phieu->nguoiTao->id,
                'name' => $phieu->nguoiTao->name,
            ] : null,
            'nguoi_duyet' => $phieu->nguoiDuyet ? [
                'id' => (int) $phieu->nguoiDuyet->id,
                'name' => $phieu->nguoiDuyet->name,
            ] : null,
            'created_at' => $phieu->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $phieu->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
