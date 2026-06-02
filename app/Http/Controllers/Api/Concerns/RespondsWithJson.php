<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait RespondsWithJson
{
    protected function apiSuccess(mixed $data = null, string $message = 'Thành công.', int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    /**
     * Phân trang offset: query ?start=0&limit=15
     *
     * @return array{start: int, limit: int}
     */
    protected function paginationFromRequest(?Request $request = null): array
    {
        $request ??= request();

        $start = max(0, (int) $request->input('start', 0));
        $limit = max(1, min((int) $request->input('limit', 15), 100));

        return [
            'start' => $start,
            'limit' => $limit,
        ];
    }

    /** @return array<string, string> */
    protected function paginationRules(): array
    {
        return [
            'start' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:100',
        ];
    }

    /** @return array<string, string> */
    protected function tuKhoaRules(): array
    {
        return [
            'tu_khoa' => 'nullable|string|max:200',
            'search' => 'nullable|string|max:200',
        ];
    }

    protected function trimmedTuKhoa(array $validated): string
    {
        return trim((string) ($validated['tu_khoa'] ?? $validated['search'] ?? ''));
    }

    protected function likePattern(string $tuKhoa): string
    {
        return '%'.addcslashes($tuKhoa, '%_\\').'%';
    }

    /**
     * @param  array<int, mixed>  $items
     */
    protected function apiList(array $items, int $total, int $start, int $limit, string $message = 'Thành công.'): JsonResponse
    {
        return $this->apiSuccess([
            'items' => $items,
            'total' => $total,
            'start' => $start,
            'limit' => $limit,
        ], $message);
    }

    /**
     * @param  Builder  $query
     * @param  callable(mixed): array  $formatter
     */
    protected function apiListFromQuery(Builder $query, callable $formatter, ?Request $request = null): JsonResponse
    {
        $request ??= request();
        ['start' => $start, 'limit' => $limit] = $this->paginationFromRequest($request);

        $total = (clone $query)->count();
        $items = $query->offset($start)->limit($limit)->get()
            ->map($formatter)
            ->values()
            ->all();

        return $this->apiList($items, $total, $start, $limit);
    }

    protected function storageUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
