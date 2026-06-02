<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\TrangPhuc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrangPhucController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules()));

        $query = TrangPhuc::query()->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $like = $this->likePattern($tuKhoa);
            $query->where(function ($qb) use ($like) {
                $qb->where('ten_san_pham', 'like', $like)
                    ->orWhere('ma_san_pham', 'like', $like);
            });
        }

        return $this->apiListFromQuery($query, fn (TrangPhuc $item) => $this->formatTrangPhuc($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_san_pham' => 'required|string|max:255',
            'ma_san_pham' => 'required|string|max:255|unique:trang_phuc,ma_san_pham',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'mo_ta' => 'nullable|string|max:500',
            'ghi_chu' => 'nullable|string|max:500',
            'trang_thai' => 'nullable|in:0,1',
            'gia_tri' => 'nullable|numeric|min:0',
        ]);

        $slug = $this->uniqueSlug(Str::slug($validated['ten_san_pham']));

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('trang-phuc/san-pham', 'public');
        }

        $trangPhuc = TrangPhuc::create([
            'ten_san_pham' => $validated['ten_san_pham'],
            'ma_san_pham' => $validated['ma_san_pham'],
            'slug' => $slug,
            'hinh_anh' => $hinhAnhPath,
            'mo_ta' => $validated['mo_ta'] ?? null,
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
            'mo_ta' => 'nullable|string|max:500',
            'ghi_chu' => 'nullable|string|max:500',
            'trang_thai' => 'nullable|in:0,1',
            'gia_tri' => 'nullable|numeric|min:0',
        ]);

        $slug = $this->uniqueSlug(Str::slug($validated['ten_san_pham']), $trangPhuc->id);

        $updateData = [
            'ten_san_pham' => $validated['ten_san_pham'],
            'ma_san_pham' => $validated['ma_san_pham'],
            'slug' => $slug,
            'mo_ta' => $validated['mo_ta'] ?? null,
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
            'slug' => $trangPhuc->slug,
            'hinh_anh' => $trangPhuc->hinh_anh,
            'hinh_anh_url' => $this->storageUrl($trangPhuc->hinh_anh),
            'mo_ta' => $trangPhuc->mo_ta,
            'ghi_chu' => $trangPhuc->ghi_chu,
            'trang_thai' => (int) $trangPhuc->trang_thai,
            'gia_tri' => (float) $trangPhuc->gia_tri,
            'created_at' => $trangPhuc->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $trangPhuc->updated_at?->format('d/m/Y H:i:s'),
        ];
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $i = 2;

        $exists = function (string $candidate) use ($ignoreId): bool {
            return TrangPhuc::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
                ->where('slug', $candidate)
                ->exists();
        };

        while ($exists($slug)) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
