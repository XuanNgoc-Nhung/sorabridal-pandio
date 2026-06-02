<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\NganHangThanhToan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NganHangThanhToanController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'trang_thai' => 'nullable|in:0,1',
        ]));

        $query = NganHangThanhToan::query()->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_ngan_hang', 'like', $like)
                    ->orWhere('so_tai_khoan', 'like', $like)
                    ->orWhere('chu_tai_khoan', 'like', $like);
            });
        }

        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        return $this->apiListFromQuery($query, fn (NganHangThanhToan $item) => $this->formatItem($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hinh_anh_logo' => 'nullable|string|max:500',
            'ten_ngan_hang' => 'required|string|max:150',
            'ten_chi_tiet' => 'nullable|string|max:255',
            'so_tai_khoan' => 'required|string|max:50',
            'chu_tai_khoan' => 'required|string|max:150',
            'chi_nhanh' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ]);

        $item = NganHangThanhToan::create([
            'hinh_anh_logo' => $validated['hinh_anh_logo'] ?? null,
            'ten_ngan_hang' => $validated['ten_ngan_hang'],
            'ten_chi_tiet' => $validated['ten_chi_tiet'] ?? null,
            'so_tai_khoan' => $validated['so_tai_khoan'],
            'chu_tai_khoan' => $validated['chu_tai_khoan'],
            'chi_nhanh' => $validated['chi_nhanh'] ?? null,
            'trang_thai' => (int) ($validated['trang_thai'] ?? 1),
        ]);

        return $this->apiSuccess(
            ['item' => $this->formatItem($item)],
            'Đã thêm ngân hàng thanh toán thành công.',
            201
        );
    }

    public function update(Request $request, NganHangThanhToan $nganHangThanhToan): JsonResponse
    {
        $validated = $request->validate([
            'hinh_anh_logo' => 'nullable|string|max:500',
            'ten_ngan_hang' => 'required|string|max:150',
            'ten_chi_tiet' => 'nullable|string|max:255',
            'so_tai_khoan' => 'required|string|max:50',
            'chu_tai_khoan' => 'required|string|max:150',
            'chi_nhanh' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ]);

        $nganHangThanhToan->update([
            'hinh_anh_logo' => $validated['hinh_anh_logo'] ?? null,
            'ten_ngan_hang' => $validated['ten_ngan_hang'],
            'ten_chi_tiet' => $validated['ten_chi_tiet'] ?? null,
            'so_tai_khoan' => $validated['so_tai_khoan'],
            'chu_tai_khoan' => $validated['chu_tai_khoan'],
            'chi_nhanh' => $validated['chi_nhanh'] ?? null,
            'trang_thai' => (int) $validated['trang_thai'],
        ]);

        return $this->apiSuccess(
            ['item' => $this->formatItem($nganHangThanhToan)],
            'Đã cập nhật ngân hàng thanh toán thành công.'
        );
    }

    public function destroy(NganHangThanhToan $nganHangThanhToan): JsonResponse
    {
        $nganHangThanhToan->delete();

        return $this->apiSuccess(message: 'Đã xoá ngân hàng thanh toán thành công.');
    }

    private function formatItem(NganHangThanhToan $item): array
    {
        return [
            'id' => (int) $item->id,
            'hinh_anh_logo' => $item->hinh_anh_logo,
            'ten_ngan_hang' => $item->ten_ngan_hang,
            'ten_chi_tiet' => $item->ten_chi_tiet,
            'so_tai_khoan' => $item->so_tai_khoan,
            'chu_tai_khoan' => $item->chu_tai_khoan,
            'chi_nhanh' => $item->chi_nhanh,
            'trang_thai' => (int) $item->trang_thai,
            'created_at' => $item->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $item->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
