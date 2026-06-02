<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LogUserAction::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\LogUserAction::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'checkRoute' => \App\Http\Middleware\CheckRoute::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ValidationException: giữ xử lý mặc định của Laravel (redirect back với $errors)
        $exceptions->dontReport([
            ValidationException::class,
        ]);

        // Với request web (HTML): trả về redirect kèm thông báo lỗi thay vì trang lỗi
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return null; // để Laravel xử lý JSON response mặc định
            }

            // ValidationException đã được Laravel xử lý (redirect + errors)
            if ($e instanceof ValidationException) {
                return null;
            }

            // Chrome DevTools tự gọi khi mở F12 — không phải lỗi ứng dụng
            if ($request->is('.well-known/appspecific/com.chrome.devtools.json')) {
                return response('', 404);
            }

            if ($e instanceof AuthenticationException) {
                return redirect()->guest(route('login'));
            }

            if ($e instanceof HttpException && $e->getStatusCode() === 403) {
                return response()->view('errors.403', [
                    'message' => $e->getMessage() ?: 'Bạn không có quyền truy cập trang này.',
                ], 403);
            }

            $userMessage = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
            $statusCode = 500;

            if ($e instanceof ModelNotFoundException) {
                $userMessage = 'Không tìm thấy dữ liệu tương ứng.';
                $statusCode = 404;
            } elseif ($e instanceof QueryException) {
                $code = $e->getCode();
                // Ràng buộc khóa ngoại / duplicate / integrity
                if ($code === '23000' || str_contains((string) $e->getMessage(), 'foreign key') || str_contains((string) $e->getMessage(), 'Integrity constraint')) {
                    $userMessage = 'Không thể thực hiện vì dữ liệu đang được sử dụng hoặc bị ràng buộc.';
                } elseif (str_contains((string) $e->getMessage(), 'Duplicate entry')) {
                    $userMessage = 'Dữ liệu trùng lặp. Vui lòng kiểm tra lại.';
                } else {
                    $userMessage = 'Lỗi cơ sở dữ liệu. Vui lòng thử lại sau.';
                }
            } elseif ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
                if ($statusCode === 404) {
                    $userMessage = 'Không tìm thấy trang.';
                } elseif ($statusCode === 403) {
                    $userMessage = 'Bạn không có quyền thực hiện thao tác này.';
                } elseif ($statusCode === 419) {
                    $userMessage = 'Phiên làm việc hết hạn. Vui lòng tải lại trang và thử lại.';
                }
            } elseif (config('app.debug')) {
                // Ở chế độ debug: hiển thị lỗi cụ thể để dễ sửa
                $userMessage = $e->getMessage();
            }

            \Illuminate\Support\Facades\Log::error('Exception trong request web', [
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $request->hasSession()
                ? redirect()->back()->with('error', $userMessage)->withInput($request->except('password', 'password_confirmation'))
                : redirect()->to('/')->with('error', $userMessage);
        });
    })->create();
