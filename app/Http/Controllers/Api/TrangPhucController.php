<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\TrangPhuc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrangPhucController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'sap_xep_theo' => 'nullable|string|in:'.implode(',', array_keys(TrangPhuc::SAP_XEP_OPTIONS)),
            'thu_tu' => 'nullable|in:asc,desc',
        ]));

        $query = TrangPhuc::query();

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_san_pham', 'like', $like)
                    ->orWhere('ma_san_pham', 'like', $like)
                    ->orWhere('ngay_nhap', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like);
            });
        }

        $sapXepTheo = $validated['sap_xep_theo'] ?? TrangPhuc::SAP_XEP_MAC_DINH;
        if (! array_key_exists($sapXepTheo, TrangPhuc::SAP_XEP_OPTIONS)) {
            $sapXepTheo = TrangPhuc::SAP_XEP_MAC_DINH;
        }
        $thuTu = strtolower((string) ($validated['thu_tu'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        match ($sapXepTheo) {
            TrangPhuc::SAP_XEP_TEN => $query->orderBy('ten_san_pham', $thuTu),
            TrangPhuc::SAP_XEP_MA => $query->orderBy('ma_san_pham', $thuTu),
            TrangPhuc::SAP_XEP_GIA_TRI => $query->orderBy('gia_tri', $thuTu),
            TrangPhuc::SAP_XEP_TRANG_THAI => $query->orderBy('trang_thai', $thuTu),
            TrangPhuc::SAP_XEP_CREATED_AT => $query->orderBy('created_at', $thuTu),
            default => $query->orderBy('id', $thuTu),
        };

        return $this->apiListFromQuery($query, fn (TrangPhuc $item) => $this->formatTrangPhuc($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'ma_san_pham' => 'required|string|max:255|unique:trang_phuc,ma_san_pham',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'ngay_nhap' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
            'trang_thai' => 'nullable|in:0,1',
            'gia_tri' => 'nullable|numeric|min:0',
        ]);

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('trang-phuc/san-pham', 'public');
        }

        $ngayNhap = trim((string) ($validated['ngay_nhap'] ?? ''));

        $trangPhuc = TrangPhuc::create([
            'ten_san_pham' => $validated['ten_san_pham'],
            'ma_san_pham' => $validated['ma_san_pham'],
            'ngay_nhap' => $ngayNhap !== '' ? $ngayNhap : null,
            'hinh_anh' => $hinhAnhPath,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'trang_thai' => $validated['trang_thai'] ?? 1,
            'gia_tri' => $validated['gia_tri'] ?? 0,
        ]);

        return $this->apiSuccess(
            ['item' => $this->formatTrangPhuc($trangPhuc)],
            'Đã thêm sản phẩm trang phục thành công.',
            201
        );
    }

    public function update(Request $request, TrangPhuc $trangPhuc): JsonResponse
    {
        $validated = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'ma_san_pham' => 'required|string|max:255|unique:trang_phuc,ma_san_pham,'.$trangPhuc->id,
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'ngay_nhap' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:500',
            'trang_thai' => 'nullable|in:0,1',
            'gia_tri' => 'nullable|numeric|min:0',
        ]);

        $ngayNhap = trim((string) ($validated['ngay_nhap'] ?? ''));

        $updateData = [
            'ten_san_pham' => $validated['ten_san_pham'],
            'ma_san_pham' => $validated['ma_san_pham'],
            'ngay_nhap' => $ngayNhap !== '' ? $ngayNhap : null,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
            'trang_thai' => $validated['trang_thai'] ?? 1,
            'gia_tri' => $validated['gia_tri'] ?? 0,
        ];

        if ($request->hasFile('hinh_anh')) {
            $newPath = $request->file('hinh_anh')->store('trang-phuc/san-pham', 'public');
            $updateData['hinh_anh'] = $newPath;

            if (! empty($trangPhuc->hinh_anh) && Storage::disk('public')->exists($trangPhuc->hinh_anh)) {
                Storage::disk('public')->delete($trangPhuc->hinh_anh);
            }
        }

        $trangPhuc->update($updateData);

        return $this->apiSuccess(
            ['item' => $this->formatTrangPhuc($trangPhuc->fresh())],
            'Đã cập nhật sản phẩm trang phục thành công.'
        );
    }

    public function destroy(TrangPhuc $trangPhuc): JsonResponse
    {
        if (! empty($trangPhuc->hinh_anh) && Storage::disk('public')->exists($trangPhuc->hinh_anh)) {
            Storage::disk('public')->delete($trangPhuc->hinh_anh);
        }

        $trangPhuc->delete();

        return $this->apiSuccess(message: 'Đã xóa sản phẩm trang phục thành công.');
    }

    private function formatTrangPhuc(TrangPhuc $trangPhuc): array
    {
        return [
            'id' => (int) $trangPhuc->id,
            'ten_san_pham' => $trangPhuc->ten_san_pham,
            'ma_san_pham' => $trangPhuc->ma_san_pham,
            'ngay_nhap' => $trangPhuc->ngay_nhap,
            'hinh_anh' => $trangPhuc->hinh_anh,
            'hinh_anh_url' => $this->storageUrl($trangPhuc->hinh_anh),
            'ghi_chu' => $trangPhuc->ghi_chu,
            'trang_thai' => (int) $trangPhuc->trang_thai,
            'gia_tri' => (float) $trangPhuc->gia_tri,
            'created_at' => $trangPhuc->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $trangPhuc->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
