<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class UserActionLogReader
{
    private const LOG_MARKER = 'User action';

    /** View trả về bởi Route::fallback() khi URL không khớp route đã khai báo. */
    private const UNDECLARED_ROUTE_VIEW = 'errors.404';

    /**
     * Có nên ghi log User action cho request/response này không.
     * Bỏ qua route fallback (URL chưa khai báo) để giảm tải ghi file log.
     */
    public static function shouldLog(Request $request, Response $response): bool
    {
        if (! $request->route()) {
            return false;
        }

        if ($request->routeIs('admin.he-thong.logs', 'admin.he-thong.logs.destroy')) {
            return false;
        }

        if ('/'.$request->path() === '//') {
            return false;
        }

        if ($response->getStatusCode() === 404 && self::responseUsesView($response, self::UNDECLARED_ROUTE_VIEW)) {
            return false;
        }

        return true;
    }

    private static function responseUsesView(Response $response, string $viewName): bool
    {
        if (! method_exists($response, 'getOriginalContent')) {
            return false;
        }

        $original = $response->getOriginalContent();

        return $original instanceof View && $original->name() === $viewName;
    }

    /**
     * @return list<string> Ngày dạng Y-m-d, mới nhất trước
     */
    public static function availableDates(): array
    {
        $dates = [];

        foreach (File::glob(storage_path('logs/history*.log')) ?: [] as $path) {
            $basename = basename($path);
            if (preg_match('/^history-(\d{2}-\d{2}-\d{4})\.log$/', $basename, $matches)) {
                if (Carbon::hasFormat($matches[1], 'd-m-Y')) {
                    $dates[] = Carbon::createFromFormat('d-m-Y', $matches[1])->format('Y-m-d');
                }

                continue;
            }

            if ($basename === 'history.log') {
                $dates[] = Carbon::createFromTimestamp(File::lastModified($path))->format('Y-m-d');
            }
        }

        foreach (File::glob(storage_path('logs/laravel*.log')) ?: [] as $path) {
            $basename = basename($path);
            if (preg_match('/^laravel-(\d{4}-\d{2}-\d{2})\.log$/', $basename, $matches)) {
                $dates[] = $matches[1];

                continue;
            }

            if ($basename === 'laravel.log') {
                $dates[] = Carbon::createFromTimestamp(File::lastModified($path))->format('Y-m-d');
            }
        }

        $dates = array_values(array_unique($dates));
        rsort($dates);

        return $dates;
    }

    public static function resolveLogPath(string $date): ?string
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }

        $day = Carbon::parse($date);

        $historyPath = storage_path('logs/history-'.$day->format('d-m-Y').'.log');
        if (File::isFile($historyPath)) {
            return $historyPath;
        }

        $historySinglePath = storage_path('logs/history.log');
        if (File::isFile($historySinglePath)) {
            $fileDate = Carbon::createFromTimestamp(File::lastModified($historySinglePath))->format('Y-m-d');
            if ($fileDate === $date) {
                return $historySinglePath;
            }
        }

        $legacyPath = storage_path('logs/laravel-'.$date.'.log');
        if (File::isFile($legacyPath)) {
            return $legacyPath;
        }

        $legacySinglePath = storage_path('logs/laravel.log');
        if (File::isFile($legacySinglePath)) {
            $fileDate = Carbon::createFromTimestamp(File::lastModified($legacySinglePath))->format('Y-m-d');
            if ($fileDate === $date) {
                return $legacySinglePath;
            }
        }

        return null;
    }

    public static function deleteLogFile(string $date): bool
    {
        $path = self::resolveLogPath($date);
        if ($path === null || ! File::isFile($path)) {
            return false;
        }

        return File::delete($path);
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    public static function paginate(
        string $date,
        int $perPage,
        int $page,
        ?int $userId = null,
        ?string $search = null,
        ?int $responseStatus = null,
        ?string $httpMethod = null,
    ): LengthAwarePaginator {
        $entries = collect(self::readEntries($date, $userId, $search, $responseStatus, $httpMethod));

        $total = $entries->count();
        $items = $entries
            ->slice(($page - 1) * $perPage, $perPage)
            ->values()
            ->all();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function readEntries(
        string $date,
        ?int $userId = null,
        ?string $search = null,
        ?int $responseStatus = null,
        ?string $httpMethod = null,
    ): array
    {
        $path = self::resolveLogPath($date);
        if ($path === null) {
            return [];
        }

        $entries = [];
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return [];
        }

        while (($line = fgets($handle)) !== false) {
            $parsed = self::parseLine($line);
            if ($parsed === null) {
                continue;
            }

            if ($userId !== null && (int) ($parsed['user']['id'] ?? 0) !== $userId) {
                continue;
            }

            if ($responseStatus !== null && (int) ($parsed['response_status'] ?? 0) !== $responseStatus) {
                continue;
            }

            if ($httpMethod !== null) {
                $entryMethod = strtoupper((string) ($parsed['request']['method'] ?? ''));
                if ($entryMethod !== $httpMethod) {
                    continue;
                }
            }

            if ($search !== null && $search !== '') {
                $haystack = strtolower(json_encode($parsed, JSON_UNESCAPED_UNICODE) ?: '');
                if (! str_contains($haystack, strtolower($search))) {
                    continue;
                }
            }

            $entries[] = $parsed;
        }

        fclose($handle);

        return array_reverse($entries);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function parseLine(string $line): ?array
    {
        $line = trim($line);
        if ($line === '' || ! str_contains($line, self::LOG_MARKER)) {
            return null;
        }

        if (! preg_match('/^\[([^\]]+)\]\s+\S+\.\S+:\s+User action\s+(.+)$/u', $line, $matches)) {
            return null;
        }

        $payload = json_decode($matches[2], true);
        if (! is_array($payload)) {
            return null;
        }

        $user = is_array($payload['user'] ?? null) ? $payload['user'] : null;
        $request = is_array($payload['request'] ?? null) ? $payload['request'] : [];
        $response = is_array($payload['response'] ?? null) ? $payload['response'] : [];

        $status = $response['status'] ?? null;

        return [
            'logged_at' => $matches[1],
            'user' => $user,
            'request' => $request,
            'response' => $response,
            'response_status' => $status !== null ? (int) $status : null,
            'response_summary' => self::summarizeResponse($response),
        ];
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private static function summarizeResponse(array $response): string
    {
        $type = (string) ($response['type'] ?? '');

        return match ($type) {
            'json' => 'JSON',
            'redirect' => 'Chuyển hướng',
            'view' => 'View: '.($response['view'] ?? '—'),
            default => $type !== '' ? ucfirst($type) : '—',
        };
    }
}
