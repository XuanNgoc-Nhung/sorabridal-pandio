<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Models\Concept;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConceptController extends Controller
{
    use RespondsWithJson;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge($this->paginationRules(), $this->tuKhoaRules(), [
            'trang_thai' => 'nullable|in:0,1',
        ]));

        $query = Concept::query()->orderByDesc('id');

        $tuKhoa = $this->trimmedTuKhoa($validated);
        if ($tuKhoa !== '') {
            $query->where('ten_concept', 'like', $this->likePattern($tuKhoa));
        }

        if ($request->filled('trang_thai') && in_array((string) $request->input('trang_thai'), ['0', '1'], true)) {
            $query->where('trang_thai', (int) $request->input('trang_thai'));
        }

        return $this->apiListFromQuery($query, fn (Concept $item) => $this->formatConcept($item), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_concept' => 'required|string|max:255',
            'hinh_anh' => 'nullable|image|max:10240',
            'trang_thai' => 'required|integer|in:0,1',
        ]);

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('concept', 'public');
        }

        $concept = Concept::create([
            'ten_concept' => $validated['ten_concept'],
            'hinh_anh' => $hinhAnhPath,
            'trang_thai' => (int) $validated['trang_thai'],
        ]);

        return $this->apiSuccess(
            ['item' => $this->formatConcept($concept)],
            'Đã thêm concept thành công.',
            201
        );
    }

    public function update(Request $request, Concept $concept): JsonResponse
    {
        $validated = $request->validate([
            'ten_concept' => 'required|string|max:255',
            'hinh_anh' => 'nullable|image|max:10240',
            'trang_thai' => 'required|integer|in:0,1',
        ]);

        $updateData = [
            'ten_concept' => $validated['ten_concept'],
            'trang_thai' => (int) $validated['trang_thai'],
        ];

        if ($request->hasFile('hinh_anh')) {
            $newPath = $request->file('hinh_anh')->store('concept', 'public');

            if (! empty($concept->hinh_anh) && Storage::disk('public')->exists($concept->hinh_anh)) {
                Storage::disk('public')->delete($concept->hinh_anh);
            }

            $updateData['hinh_anh'] = $newPath;
        }

        $concept->update($updateData);

        return $this->apiSuccess(
            ['item' => $this->formatConcept($concept->fresh())],
            'Đã cập nhật concept thành công.'
        );
    }

    public function destroy(Concept $concept): JsonResponse
    {
        if (! empty($concept->hinh_anh) && Storage::disk('public')->exists($concept->hinh_anh)) {
            Storage::disk('public')->delete($concept->hinh_anh);
        }

        $concept->delete();

        return $this->apiSuccess(message: 'Đã xóa concept thành công.');
    }

    private function formatConcept(Concept $concept): array
    {
        return [
            'id' => (int) $concept->id,
            'ten_concept' => $concept->ten_concept,
            'hinh_anh' => $concept->hinh_anh,
            'hinh_anh_url' => $this->storageUrl($concept->hinh_anh),
            'trang_thai' => (int) $concept->trang_thai,
            'created_at' => $concept->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $concept->updated_at?->format('d/m/Y H:i:s'),
        ];
    }
}
