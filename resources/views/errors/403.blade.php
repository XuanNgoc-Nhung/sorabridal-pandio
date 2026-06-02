<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 | Không có quyền truy cập</title>
    <style>
        :root {
            color-scheme: light;
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
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .card {
            width: 100%;
            max-width: 560px;
            padding: 40px 32px;
            text-align: center;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        .status {
            margin: 0;
            font-size: 72px;
            line-height: 1;
            font-weight: 700;
            color: #ef4444;
        }

        h1 {
            margin: 16px 0 12px;
            font-size: 28px;
        }

        p {
            margin: 0;
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
        }

        .actions {
            margin-top: 28px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s ease;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="card">
        <p class="status">403</p>
        <h1>Không có quyền truy cập</h1>
        <p>{{ $message ?? 'Bạn đã đăng nhập nhưng không được cấp quyền để truy cập trang này.' }}</p>

        <div class="actions">
            @auth
                <a class="btn btn-primary" href="{{ route('admin.index') }}">Về trang quản trị</a>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">Đăng nhập</a>
            @endauth

            <a class="btn btn-secondary" href="{{ url()->previous() }}">Quay lại</a>
        </div>
    </div>
</body>
</html>
