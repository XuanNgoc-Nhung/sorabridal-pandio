<?php

namespace App\Services\ApiDocumentation;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

class ApiDocumentationService
{
    public function __construct(
        private readonly ValidationRulesParser $rulesParser,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $endpoints = [];

        foreach (RouteFacade::getRoutes() as $route) {
            $endpoint = $this->describeRoute($route);
            if ($endpoint !== null) {
                $endpoints[] = $endpoint;
            }
        }

        usort($endpoints, function (array $a, array $b): int {
            return [$a['group'], $a['uri'], $a['method']] <=> [$b['group'], $b['uri'], $b['method']];
        });

        $groups = [];
        foreach ($endpoints as $endpoint) {
            $groups[$endpoint['group']][] = $endpoint;
        }

        return [
            'title' => config('app.name').' API',
            'base_url' => url('/api'),
            'generated_at' => now()->toIso8601String(),
            'auth' => [
                'type' => 'Bearer',
                'header' => 'Authorization: Bearer {token}',
                'login' => 'POST /api/login',
            ],
            'common_headers' => [
                'Accept' => 'application/json',
            ],
            'list_response' => [
                'description' => 'Các API danh sách (GET) dùng phân trang offset',
                'query' => [
                    ['name' => 'start', 'type' => 'integer', 'required' => false, 'description' => 'Vị trí bắt đầu (mặc định 0)'],
                    ['name' => 'limit', 'type' => 'integer', 'required' => false, 'description' => 'Số bản ghi (mặc định 15, tối đa 100)'],
                    ['name' => 'tu_khoa', 'type' => 'string', 'required' => false, 'description' => 'Từ khóa tìm kiếm'],
                ],
                'response' => 'data.items, data.total, data.start, data.limit',
            ],
            'success_response' => [
                'success' => true,
                'message' => 'Thành công.',
                'data' => '...',
            ],
            'total' => count($endpoints),
            'groups' => $groups,
            'endpoints' => $endpoints,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function describeRoute(Route $route): ?array
    {
        $uri = '/'.ltrim($route->uri(), '/');
        if (! str_starts_with($uri, '/api/') && $uri !== '/api') {
            return null;
        }

        if ($route->getName() === 'api.documents') {
            return null;
        }

        $action = $route->getAction('controller');
        if (! is_string($action) || ! str_contains($action, '@')) {
            return null;
        }

        [$controllerClass, $method] = explode('@', $action, 2);

        if (! str_starts_with($controllerClass, 'App\\Http\\Controllers\\Api\\')) {
            return null;
        }

        if (str_ends_with($controllerClass, 'DocumentationController')) {
            return null;
        }

        $methods = array_values(array_filter(
            $route->methods(),
            fn (string $m) => ! in_array($m, ['HEAD', 'OPTIONS'], true)
        ));

        if ($methods === []) {
            return null;
        }

        $httpMethod = $methods[0];
        $pathParams = $this->pathParameters($uri);
        $validation = $this->rulesParser->parse($controllerClass, $method, $httpMethod);
        $middleware = $route->gatherMiddleware();
        $requiresAuth = $this->requiresAuth($middleware);
        $group = $this->resolveGroup($uri);
        $shortController = class_basename($controllerClass);

        return [
            'name' => $route->getName(),
            'method' => $httpMethod,
            'uri' => $uri,
            'path' => str_replace('/api', '', $uri) ?: '/',
            'group' => $group,
            'controller' => $shortController,
            'action' => $method,
            'requires_auth' => $requiresAuth,
            'middleware' => $middleware,
            'summary' => $this->rulesParser->methodSummary($controllerClass, $method),
            'path_parameters' => $pathParams,
            'query_parameters' => $validation['query'],
            'body_parameters' => $validation['body'],
            'uses_pagination' => $this->usesPagination($validation['query']),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $queryParameters
     */
    private function usesPagination(array $queryParameters): bool
    {
        $names = array_column($queryParameters, 'name');

        return in_array('start', $names, true) && in_array('limit', $names, true);
    }

    /**
     * @param  array<int, string>  $middleware
     */
    private function requiresAuth(array $middleware): bool
    {
        foreach ($middleware as $item) {
            if (Str::contains($item, 'auth:sanctum') || Str::contains($item, 'Authenticate')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function pathParameters(string $uri): array
    {
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);

        $params = [];
        foreach ($matches[1] as $name) {
            $optional = str_ends_with($name, '?');
            $cleanName = rtrim($name, '?');
            $params[] = [
                'name' => $cleanName,
                'in' => 'path',
                'required' => ! $optional,
                'type' => 'integer',
                'description' => 'ID hoặc khóa route model binding',
            ];
        }

        return $params;
    }

    private function resolveGroup(string $uri): string
    {
        $relative = trim(Str::after($uri, '/api'), '/');
        if ($relative === '') {
            return 'Chung';
        }

        $segments = explode('/', $relative);
        if ($segments[0] === 'admin' && isset($segments[1])) {
            return 'Admin / '.Str::headline(str_replace('-', ' ', $segments[1]));
        }

        return Str::headline(str_replace('-', ' ', $segments[0]));
    }
}
