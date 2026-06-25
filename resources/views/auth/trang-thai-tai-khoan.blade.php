<!DOCTYPE html>
<html lang="vi" class="auth-theme-pink">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Wedding Studio</title>
    <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-auth.css" />
    <style>
        :root {
            --accent: {{ ($accent ?? 'warning') === 'danger' ? '#dc2626' : '#d97706' }};
            --accent-soft: {{ ($accent ?? 'warning') === 'danger' ? '#fef2f2' : '#fff7e6' }};
            --accent-border: {{ ($accent ?? 'warning') === 'danger' ? '#fecaca' : '#fcd34d' }};
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #fef7f8 0%, #fce7ec 50%, #fdf2f4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Tahoma, Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 680px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--accent-border);
            border-radius: 1rem;
            box-shadow: 0 10px 24px rgba(236, 72, 153, 0.08);
            padding: 36px 28px;
            text-align: center;
        }

        .badge {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            background: var(--accent-soft);
            color: var(--accent);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 24px;
            color: var(--accent);
        }

        p {
            margin: 0 0 12px;
            color: #6b7280;
            line-height: 1.6;
        }

        .hint {
            margin: 0 0 24px;
            font-size: 13px;
            color: #9ca3af;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-action:hover {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-action-primary {
            border-color: #f9a8d4;
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: #fff;
        }

        .btn-action-primary:hover {
            background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
            color: #fff;
        }

        .btn-logout {
            border: none;
            background: transparent;
            color: #6b7280;
            font-size: 13px;
            cursor: pointer;
            padding: 4px 8px;
            text-decoration: underline;
        }

        .btn-logout:hover {
            color: #374151;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="badge">
            <i class="icon-base ti {{ $icon ?? 'tabler-alert-circle' }}"></i>
        </div>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        <p class="hint">
            @auth
                Sau khi quản trị viên mở lại quyền, bấm «Làm mới trang» để tiếp tục sử dụng hệ thống.
            @else
                Vui lòng đăng nhập lại sau khi quản trị viên đã cập nhật quyền truy cập.
            @endauth
        </p>

        <div class="actions">
            @auth
                <a href="{{ route('admin.index') }}" class="btn-action btn-action-primary">
                    <i class="icon-base ti tabler-refresh"></i>
                    Làm mới trang
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-action btn-action-primary">
                    <i class="icon-base ti tabler-login"></i>
                    Đăng nhập lại
                </a>
            @endauth
            <a href="{{ url('/') }}" class="btn-action">
                <i class="icon-base ti tabler-home"></i>
                Về trang chủ
            </a>
        </div>

        @auth
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn-logout">Đăng xuất</button>
            </form>
        @endauth
    </main>
</body>
</html>
