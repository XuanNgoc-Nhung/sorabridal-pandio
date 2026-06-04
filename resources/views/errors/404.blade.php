<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | Không tìm thấy trang — Wedding Studio</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <style>
        :root {
            color-scheme: light;
            --pink-50: #fdf2f4;
            --pink-100: #fce7ec;
            --pink-200: #fbcfe8;
            --pink-500: #ec4899;
            --pink-600: #db2777;
            --pink-700: #be185d;
            --pink-900: #831843;
            --ink: #1f2937;
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Tahoma, "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #fef7f8 0%, #fce7ec 45%, #fdf2f4 100%);
            color: var(--ink);
            overflow-x: hidden;
        }

        .bg-shape {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(60px);
            opacity: 0.45;
        }

        .bg-shape--1 {
            width: 320px;
            height: 320px;
            background: #f9a8d4;
            top: -80px;
            right: -60px;
        }

        .bg-shape--2 {
            width: 280px;
            height: 280px;
            background: #fbcfe8;
            bottom: -60px;
            left: -40px;
        }

        .page {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
            text-decoration: none;
            color: var(--pink-700);
        }

        .brand svg {
            flex-shrink: 0;
        }

        .brand-name {
            font-size: 1.125rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .card {
            text-align: center;
            padding: 48px 36px 40px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(236, 72, 153, 0.15);
            border-radius: 1.25rem;
            box-shadow:
                0 4px 24px rgba(236, 72, 153, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
        }

        .icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            margin-bottom: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pink-50), var(--pink-100));
            border: 1px solid rgba(236, 72, 153, 0.2);
            color: var(--pink-600);
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
        }

        .code {
            margin: 0 0 8px;
            font-size: clamp(4rem, 14vw, 5.5rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, var(--pink-500) 0%, var(--pink-700) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--pink-900);
        }

        .message {
            margin: 0 auto;
            max-width: 360px;
            font-size: 0.95rem;
            line-height: 1.65;
            color: var(--muted);
        }

        .path {
            margin-top: 16px;
            padding: 10px 14px;
            font-size: 0.8rem;
            word-break: break-all;
            color: var(--pink-700);
            background: var(--pink-50);
            border: 1px dashed rgba(236, 72, 153, 0.25);
            border-radius: 0.5rem;
        }

        .path-label {
            display: block;
            margin-bottom: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pink-600);
            opacity: 0.85;
        }

        .actions {
            margin-top: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 140px;
            padding: 12px 22px;
            border-radius: 0.65rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--pink-500) 0%, var(--pink-600) 100%);
            box-shadow: 0 2px 12px rgba(236, 72, 153, 0.35);
            border: none;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 18px rgba(219, 39, 119, 0.4);
        }

        .btn-secondary {
            color: var(--pink-900);
            background: var(--pink-50);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        .btn-secondary:hover {
            background: var(--pink-100);
        }

        .footer-note {
            margin-top: 24px;
            font-size: 0.8rem;
            color: var(--muted);
            opacity: 0.85;
        }
    </style>
</head>
<body>
    <div class="bg-shape bg-shape--1" aria-hidden="true"></div>
    <div class="bg-shape bg-shape--2" aria-hidden="true"></div>

    <main class="page">
        <a class="brand" href="{{ url('/') }}">
            <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="currentColor"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="currentColor"/>
            </svg>
            <span class="brand-name">Wedding Studio</span>
        </a>

        <div class="card">
            <div class="icon-wrap" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M20 20l-3.5-3.5"/>
                    <path d="M8 11h6M11 8v6" opacity="0.35"/>
                </svg>
            </div>

            <p class="code" aria-hidden="true">404</p>
            <h1>Không tìm thấy trang</h1>
            <p class="message">
                {{ $message ?? 'Đường dẫn bạn truy cập không tồn tại hoặc đã được thay đổi. Vui lòng kiểm tra lại địa chỉ hoặc quay về trang chính.' }}
            </p>

            @if(request()->path() && request()->path() !== '/')
                <div class="path">
                    <span class="path-label">Đường dẫn hiện tại</span>
                    /{{ request()->path() }}
                </div>
            @endif

            <div class="actions">
                @auth
                    <a class="btn btn-primary" href="{{ route('admin.index') }}">Về trang quản trị</a>
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">Đăng nhập</a>
                @endauth

                @if(url()->previous() && url()->previous() !== url()->current())
                    <a class="btn btn-secondary" href="{{ url()->previous() }}">Quay lại</a>
                @else
                    <a class="btn btn-secondary" href="{{ url('/') }}">Về trang chủ</a>
                @endif
            </div>

            <p class="footer-note">Mã lỗi 404 — Trang không tồn tại</p>
        </div>
    </main>
</body>
</html>
