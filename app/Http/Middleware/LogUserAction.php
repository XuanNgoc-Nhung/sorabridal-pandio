<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LogUserAction
{
    private const MASKED_TEXT = '***';

    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'token',
        '_token',
        'access_token',
        'refresh_token',
        'authorization',
        'secret',
        'api_key',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        $route = $request->route();

        Log::info('User action', [
            'user' => $user
                ? ['id' => $user->id, 'name' => $user->name ?? $user->email]
                : null,
            'request' => [
                'method' => $request->method(),
                'path' => '/'.$request->path(),
                'input' => $this->sanitizeData($request->all()),
            ],
            'response' => $this->describeResponse($request, $response),
        ]);

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    private function describeResponse(Request $request, Response $response): array
    {
        if ($response instanceof JsonResponse) {
            /** @var mixed $data */
            $data = $response->getData(true);

            return [
                'type' => 'json',
                'status' => $response->getStatusCode(),
                'body' => is_array($data) ? $this->sanitizeData($data) : $data,
            ];
        }

        if ($response instanceof RedirectResponse) {
            return [
                'type' => 'redirect',
                'status' => $response->getStatusCode(),
                'to' => $response->getTargetUrl(),
                'flash' => $this->sanitizeData($this->flashMessages($request)),
            ];
        }

        $original = method_exists($response, 'getOriginalContent')
            ? $response->getOriginalContent()
            : null;

        if ($original instanceof View) {
            return [
                'type' => 'view',
                'status' => $response->getStatusCode(),
                'view' => $original->name(),
                'data' => $this->sanitizeData($this->normalizeViewData($original->getData())),
            ];
        }

        return [
            'type' => class_basename($response),
            'status' => $response->getStatusCode(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function flashMessages(Request $request): array
    {
        if (! $request->hasSession()) {
            return [];
        }

        $session = $request->session();
        $keys = array_unique(array_merge(
            $session->get('_flash.new', []),
            $session->get('_flash.old', [])
        ));

        $flash = [];
        foreach ($keys as $key) {
            $flash[$key] = $session->get($key);
        }

        return $flash;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeViewData(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[$key] = $this->normalizeValue($value);
        }

        return $normalized;
    }

    private function normalizeValue(mixed $value, int $depth = 0): mixed
    {
        if ($depth > 4) {
            return '[...]';
        }

        if ($value instanceof Model) {
            return $this->sanitizeData($value->toArray());
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if ($value instanceof Collection) {
            $value = $value->all();
        }

        if (is_array($value)) {
            if (count($value) > 30) {
                return [
                    '_count' => count($value),
                    '_sample' => array_map(
                        fn ($item) => $this->normalizeValue($item, $depth + 1),
                        array_slice($value, 0, 5, true)
                    ),
                ];
            }

            $normalized = [];
            foreach ($value as $k => $item) {
                $normalized[$k] = $this->normalizeValue($item, $depth + 1);
            }

            return $normalized;
        }

        if (is_string($value) && mb_strlen($value) > 500) {
            return mb_substr($value, 0, 500).'...';
        }

        return $value;
    }

    /**
     * @param  array<mixed>  $data
     * @return array<mixed>
     */
    private function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::SENSITIVE_KEYS, true)) {
                $data[$key] = self::MASKED_TEXT;

                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            }
        }

        return $data;
    }
}
