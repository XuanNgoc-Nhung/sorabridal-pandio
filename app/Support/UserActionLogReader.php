<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;

class UserActionLogReader
{
    private const LOG_MARKER = 'User action';

    /**
     * @return list<string> Ngày dạng Y-m-d, mới nhất trước
     */
    public static function availableDates(): array
    {
        $dates = [];

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

        $datedPath = storage_path('logs/laravel-'.$date.'.log');
        if (File::isFile($datedPath)) {
            return $datedPath;
        }

        $singlePath = storage_path('logs/laravel.log');
        if (File::isFile($singlePath)) {
            $fileDate = Carbon::createFromTimestamp(File::lastModified($singlePath))->format('Y-m-d');
            if ($fileDate === $date) {
                return $singlePath;
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
