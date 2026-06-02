<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chưa được phân quyền | Wedding Studio</title>
    <style>
        :root {
            --warning: #f59e0b;
            --warning-soft: #fff7e6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #fcd34d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #f9fafb;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 680px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            padding: 36px 28px;
            text-align: center;
        }

        .badge {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            background: var(--warning-soft);
            color: var(--warning);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 24px;
            color: var(--warning);
        }

        p {
            margin: 0 0 24px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        button {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background: #f1f5f9;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="badge">!</div>
        <h1>Tài khoản chưa được phân quyền</h1>
        <p>
            Tài khoản của bạn hiện chưa được phân menu sử dụng hệ thống.
            Vui lòng liên hệ quản trị viên để được cấp quyền truy cập.
        </p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Đăng xuất</button>
        </form>
    </main>
</body>
</html>
