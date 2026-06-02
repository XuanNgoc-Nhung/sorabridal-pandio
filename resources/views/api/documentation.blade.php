<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sorabridal — Tài liệu tích hợp API</title>
    <meta name="description" content="Tài liệu API chính thức của {{ config('app.name') }}. Tra cứu endpoint, tham số, ví dụ request/response và hướng dẫn tích hợp nhanh.">
    <meta name="keywords" content="api documentation, tài liệu api, wedding studio api, endpoint, tích hợp api">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ request()->url() }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="Sorabridal — Tài liệu API">
    <meta property="og:description" content="Tài liệu API chính thức của {{ config('app.name') }} với danh sách endpoint, mô tả tham số và ví dụ sử dụng.">
    <meta property="og:url" content="{{ request()->url() }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sorabridal — Tài liệu API">
    <meta name="twitter:description" content="Khám phá tài liệu API {{ config('app.name') }}: endpoint, xác thực, tham số và response mẫu.">
    <style>
        :root {
            --bg: #0f1419;
            --surface: #1a2332;
            --border: #2d3a4f;
            --text: #e7ecf3;
            --muted: #8b9cb3;
            --accent: #3b82f6;
            --get: #22c55e;
            --post: #3b82f6;
            --put: #f59e0b;
            --delete: #ef4444;
            --ok: #4ade80;
            --err: #f87171;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 300px;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 1.25rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .main { flex: 1; padding: 1.5rem 2rem 3rem;}
        h1 { font-size: 1.35rem; margin: 0 0 .25rem; }
        .meta { color: var(--muted); font-size: .85rem; margin-bottom: 1rem; }
        .search, .field-input, .field-textarea {
            width: 100%;
            padding: .55rem .75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg);
            color: var(--text);
            font-size: .85rem;
        }
        .field-textarea { font-family: ui-monospace, monospace; min-height: 100px; resize: vertical; }
        .search { margin-bottom: 1rem; }
        .auth-panel {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .85rem;
            margin-bottom: 1rem;
            font-size: .82rem;
        }
        .auth-panel label { display: block; color: var(--muted); margin-bottom: .25rem; font-size: .75rem; }
        .auth-panel .field-input { margin-bottom: .5rem; }
        .auth-panel details { margin-top: .5rem; }
        .auth-panel summary { cursor: pointer; color: var(--accent); }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .5rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            color: #fff;
        }
        .btn-primary { background: var(--accent); }
        .btn-primary:hover { filter: brightness(1.1); }
        .btn-primary:disabled { opacity: .5; cursor: not-allowed; }
        .btn-sm { padding: .35rem .65rem; font-size: .78rem; }
        .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .nav-group { margin-bottom: 1rem; }
        .nav-group h3 {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            margin: 0 0 .4rem;
        }
        .nav-link {
            display: block;
            font-size: .8rem;
            color: var(--muted);
            text-decoration: none;
            padding: .2rem 0;
            word-break: break-all;
        }
        .nav-link:hover { color: var(--accent); }
        .info-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem 1.15rem;
            margin-bottom: 1.5rem;
            font-size: .9rem;
        }
        .info-box code { background: var(--bg); padding: .1rem .35rem; border-radius: 4px; font-size: .82rem; }
        .group-title {
            font-size: 1.1rem;
            margin: 2rem 0 1rem;
            padding-bottom: .35rem;
            border-bottom: 1px solid var(--border);
        }
        .endpoint {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .endpoint-header {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .85rem 1rem;
            cursor: pointer;
            user-select: none;
        }
        .endpoint-header:hover { background: rgba(255,255,255,.03); }
        .method {
            font-size: .72rem;
            font-weight: 700;
            padding: .2rem .45rem;
            border-radius: 4px;
            min-width: 3.2rem;
            text-align: center;
            color: #fff;
        }
        .method-GET { background: var(--get); }
        .method-POST { background: var(--post); }
        .method-PUT { background: var(--put); }
        .method-DELETE { background: var(--delete); }
        .path { font-family: ui-monospace, monospace; font-size: .88rem; flex: 1; }
        .badge {
            font-size: .68rem;
            padding: .15rem .4rem;
            border-radius: 4px;
            background: var(--bg);
            color: var(--muted);
        }
        .badge-auth { color: #fbbf24; border: 1px solid #854d0e; }
        .endpoint-body {
            display: none;
            padding: 0 1rem 1rem;
            border-top: 1px solid var(--border);
        }
        .endpoint.open .endpoint-body { display: block; }
        .summary { color: var(--muted); font-size: .88rem; margin: .75rem 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .82rem;
            margin-top: .5rem;
        }
        th, td {
            text-align: left;
            padding: .45rem .5rem;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        th { color: var(--muted); font-weight: 600; }
        .req { color: #f87171; }
        .rules { color: var(--muted); font-size: .78rem; word-break: break-word; }
        .section-label {
            font-size: .75rem;
            font-weight: 600;
            color: var(--muted);
            margin: .85rem 0 .25rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .json-link { font-size: .85rem; color: var(--accent); }
        .hidden { display: none !important; }
        .try-panel {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border);
        }
        .try-form { margin-top: .5rem; }
        .try-fields {
            display: grid;
            gap: .65rem;
            margin-bottom: .85rem;
        }
        .try-field label {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .78rem;
            color: var(--muted);
            margin-bottom: .25rem;
        }
        .try-field label code { color: var(--text); font-size: .8rem; }
        .try-field .field-hint { font-size: .72rem; color: var(--muted); margin-top: .15rem; }
        .try-actions { display: flex; align-items: center; gap: .65rem; flex-wrap: wrap; }
        .try-url {
            font-family: ui-monospace, monospace;
            font-size: .78rem;
            color: var(--muted);
            word-break: break-all;
            flex: 1;
            min-width: 200px;
        }
        .try-response {
            margin-top: 1rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        .try-response-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .5rem .75rem;
            border-bottom: 1px solid var(--border);
            font-size: .8rem;
        }
        .status-badge {
            font-weight: 700;
            padding: .15rem .45rem;
            border-radius: 4px;
            font-size: .75rem;
        }
        .status-2xx { background: rgba(34,197,94,.2); color: var(--ok); }
        .status-4xx, .status-5xx { background: rgba(248,113,113,.2); color: var(--err); }
        .try-response pre {
            margin: 0;
            padding: .75rem;
            font-size: .78rem;
            line-height: 1.45;
            overflow-x: auto;
            max-height: 420px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .token-saved { font-size: .72rem; color: var(--ok); margin-top: .35rem; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h1>Sorabridal Api</h1>
        <p class="meta">{{ $docs['total'] }} endpoint · Cập nhật {{ \Illuminate\Support\Carbon::parse($docs['generated_at'])->diffForHumans() }}</p>

        <div class="auth-panel">
            <label for="global-token">Bearer token</label>
            <input type="text" id="global-token" class="field-input" placeholder="Dán token hoặc đăng nhập bên dưới" autocomplete="off">
            <p id="token-status" class="token-saved hidden">Đã lưu token (dùng cho mọi request cần auth)</p>
            <details>
                <summary>Đăng nhập lấy token</summary>
                <div style="margin-top:.5rem">
                    <label for="login-email">Email / SĐT</label>
                    <input type="text" id="login-email" class="field-input" placeholder="email@example.com">
                    <label for="login-password" style="margin-top:.35rem">Mật khẩu</label>
                    <input type="password" id="login-password" class="field-input">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-login" style="margin-top:.5rem;width:100%">Đăng nhập</button>
                </div>
            </details>
        </div>

        <input type="search" class="search" id="search" placeholder="Tìm endpoint...">
        <nav id="nav">
            @foreach ($docs['groups'] as $groupName => $items)
                <div class="nav-group" data-nav-group>
                    <h3>{{ $groupName }}</h3>
                    @foreach ($items as $item)
                        <a class="nav-link" href="#{{ \Illuminate\Support\Str::slug($item['name'] ?? $item['uri'].'-'.$item['method']) }}" data-nav-search="{{ strtolower($item['method'].' '.$item['uri'].' '.$item['action']) }}">
                            <span style="color:var(--accent)">{{ $item['method'] }}</span> {{ $item['path'] }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
    </aside>

    <main class="main">
        <p class="meta">
            Base URL: <code>{{ $docs['base_url'] }}</code> ·
            <a class="json-link" href="{{ url('/api/documents?format=json') }}">JSON</a>
        </p>

        <div class="info-box">
            <strong>Xác thực:</strong> {{ $docs['auth']['header'] }} (lấy token qua <code>{{ $docs['auth']['login'] }}</code> hoặc form bên trái)<br>
            <strong>Header:</strong> <code>Accept: application/json</code><br>
            <strong>Danh sách:</strong> {{ $docs['list_response']['description'] }} — <code>{{ $docs['list_response']['response'] }}</code>
        </div>

        @foreach ($docs['groups'] as $groupName => $items)
            <h2 class="group-title">{{ $groupName }}</h2>
            @foreach ($items as $item)
                @php
                    $anchor = \Illuminate\Support\Str::slug($item['name'] ?? $item['uri'].'-'.$item['method']);
                    $hasFiles = collect($item['body_parameters'] ?? [])->contains(fn ($p) => ($p['type'] ?? '') === 'file');
                    $bodyMethods = in_array($item['method'], ['POST', 'PUT', 'PATCH'], true);
                @endphp
                <article
                    class="endpoint"
                    id="{{ $anchor }}"
                    data-search="{{ strtolower($item['method'].' '.$item['uri'].' '.$item['action'].' '.$groupName) }}"
                    data-method="{{ $item['method'] }}"
                    data-uri="{{ $item['uri'] }}"
                    data-auth="{{ $item['requires_auth'] ? '1' : '0' }}"
                >
                    <div class="endpoint-header" onclick="this.parentElement.classList.toggle('open')">
                        <span class="method method-{{ $item['method'] }}">{{ $item['method'] }}</span>
                        <span class="path">{{ $item['uri'] }}</span>
                        @if ($item['requires_auth'])
                            <span class="badge badge-auth">Bearer</span>
                        @endif
                        <span class="badge">{{ $item['controller'] }}@{{ $item['action'] }}</span>
                    </div>
                    <div class="endpoint-body" onclick="event.stopPropagation()">
                        @if ($item['summary'])
                            <p class="summary">{{ $item['summary'] }}</p>
                        @endif

                        @if (!empty($item['path_parameters']))
                            <p class="section-label">Path parameters</p>
                            <table>
                                <thead><tr><th>Tên</th><th>Bắt buộc</th><th>Kiểu</th><th>Mô tả</th></tr></thead>
                                <tbody>
                                @foreach ($item['path_parameters'] as $param)
                                    <tr>
                                        <td><code>{{ $param['name'] }}</code></td>
                                        <td>{{ $param['required'] ? 'Có' : 'Không' }}</td>
                                        <td>{{ $param['type'] }}</td>
                                        <td>{{ $param['description'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (!empty($item['query_parameters']))
                            <p class="section-label">Query parameters</p>
                            <table>
                                <thead><tr><th>Tên</th><th>Bắt buộc</th><th>Kiểu</th><th>Quy tắc</th></tr></thead>
                                <tbody>
                                @foreach ($item['query_parameters'] as $param)
                                    <tr>
                                        <td><code>{{ $param['name'] }}</code> @if($param['required'])<span class="req">*</span>@endif</td>
                                        <td>{{ $param['required'] ? 'Có' : 'Không' }}</td>
                                        <td>{{ $param['type'] }}</td>
                                        <td class="rules">{{ $param['rules'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (!empty($item['body_parameters']))
                            <p class="section-label">Body (JSON hoặc multipart)</p>
                            <table>
                                <thead><tr><th>Tên</th><th>Bắt buộc</th><th>Kiểu</th><th>Quy tắc</th></tr></thead>
                                <tbody>
                                @foreach ($item['body_parameters'] as $param)
                                    <tr>
                                        <td><code>{{ $param['name'] }}</code> @if($param['required'])<span class="req">*</span>@endif</td>
                                        <td>{{ $param['required'] ? 'Có' : 'Không' }}</td>
                                        <td>{{ $param['type'] }}</td>
                                        <td class="rules">{{ $param['rules'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        @if (empty($item['path_parameters']) && empty($item['query_parameters']) && empty($item['body_parameters']))
                            <p class="summary">Không có tham số validation trong controller.</p>
                        @endif

                        <div class="try-panel">
                            <p class="section-label">Thử API</p>
                            <form class="try-form">
                                <div class="try-fields">
                                    @foreach ($item['path_parameters'] ?? [] as $param)
                                        <div class="try-field" data-param-type="path">
                                            <label><code>{{ $param['name'] }}</code> @if($param['required'])<span class="req">*</span>@endif <span>(path)</span></label>
                                            <input
                                                type="text"
                                                class="field-input"
                                                name="{{ $param['name'] }}"
                                                data-in="path"
                                                data-required="{{ $param['required'] ? '1' : '0' }}"
                                                placeholder="vd: 1"
                                                @if($param['type'] === 'integer') inputmode="numeric" @endif
                                            >
                                        </div>
                                    @endforeach

                                    @foreach ($item['query_parameters'] ?? [] as $param)
                                        <div class="try-field" data-param-type="query">
                                            <label><code>{{ $param['name'] }}</code> @if($param['required'])<span class="req">*</span>@endif <span>(query)</span></label>
                                            <input
                                                type="text"
                                                class="field-input"
                                                name="{{ $param['name'] }}"
                                                data-in="query"
                                                data-required="{{ $param['required'] ? '1' : '0' }}"
                                                data-type="{{ $param['type'] }}"
                                                placeholder="{{ $param['rules'] }}"
                                            >
                                        </div>
                                    @endforeach

                                    @if ($bodyMethods && !empty($item['body_parameters']))
                                        @if ($hasFiles)
                                            @foreach ($item['body_parameters'] as $param)
                                                <div class="try-field" data-param-type="body">
                                                    <label><code>{{ $param['name'] }}</code> @if($param['required'])<span class="req">*</span>@endif <span>(body)</span></label>
                                                    @if (($param['type'] ?? '') === 'file')
                                                        <input type="file" class="field-input" name="{{ $param['name'] }}" data-in="body" data-type="file" data-required="{{ $param['required'] ? '1' : '0' }}">
                                                    @else
                                                        <input type="text" class="field-input" name="{{ $param['name'] }}" data-in="body" data-type="{{ $param['type'] }}" data-required="{{ $param['required'] ? '1' : '0' }}" placeholder="{{ $param['rules'] }}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="try-field" data-param-type="body-json">
                                                <label><span>Body JSON</span></label>
                                                <textarea class="field-textarea try-body-json" placeholder='{"ten_dich_vu": "..."}'></textarea>
                                                <p class="field-hint">Hoặc điền từng trường bên dưới</p>
                                            </div>
                                            @foreach ($item['body_parameters'] as $param)
                                                <div class="try-field" data-param-type="body">
                                                    <label><code>{{ $param['name'] }}</code> @if($param['required'])<span class="req">*</span>@endif</label>
                                                    <input
                                                        type="text"
                                                        class="field-input try-body-field"
                                                        name="{{ $param['name'] }}"
                                                        data-in="body"
                                                        data-type="{{ $param['type'] }}"
                                                        data-required="{{ $param['required'] ? '1' : '0' }}"
                                                        placeholder="{{ $param['rules'] }}"
                                                    >
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>

                                <div class="try-actions">
                                    <button type="submit" class="btn btn-primary">Gửi request</button>
                                    <span class="try-url"></span>
                                </div>
                            </form>

                            <div class="try-response hidden">
                                <div class="try-response-header">
                                    <span class="status-badge">—</span>
                                    <span class="try-time"></span>
                                    <button type="button" class="btn btn-ghost btn-sm try-copy">Sao chép</button>
                                </div>
                                <pre class="try-response-body"></pre>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        @endforeach
    </main>
</div>

<script>
(() => {
    const TOKEN_KEY = 'wedding_studio_api_docs_token';
    const tokenInput = document.getElementById('global-token');
    const tokenStatus = document.getElementById('token-status');

    const savedToken = localStorage.getItem(TOKEN_KEY);
    if (savedToken && tokenInput) {
        tokenInput.value = savedToken;
        tokenStatus?.classList.remove('hidden');
    }

    tokenInput?.addEventListener('input', () => {
        const v = tokenInput.value.trim();
        if (v) {
            localStorage.setItem(TOKEN_KEY, v);
            tokenStatus?.classList.remove('hidden');
        } else {
            localStorage.removeItem(TOKEN_KEY);
            tokenStatus?.classList.add('hidden');
        }
    });

    document.getElementById('btn-login')?.addEventListener('click', async () => {
        const email = document.getElementById('login-email')?.value?.trim();
        const password = document.getElementById('login-password')?.value ?? '';
        if (!email || !password) {
            alert('Vui lòng nhập email/sđt và mật khẩu.');
            return;
        }
        const btn = document.getElementById('btn-login');
        btn.disabled = true;
        btn.textContent = 'Đang đăng nhập...';
        try {
            const res = await fetch(@json(url('/api/login')), {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password, device_name: 'api-docs' }),
            });
            const data = await res.json();
            if (data.success && data.data?.token) {
                tokenInput.value = data.data.token;
                localStorage.setItem(TOKEN_KEY, data.data.token);
                tokenStatus?.classList.remove('hidden');
                alert('Đăng nhập thành công. Token đã được lưu.');
            } else {
                alert(data.message || 'Đăng nhập thất bại.');
            }
        } catch (e) {
            alert('Lỗi kết nối: ' + e.message);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Đăng nhập';
        }
    });

    function getToken() {
        return (tokenInput?.value || localStorage.getItem(TOKEN_KEY) || '').trim();
    }

    function buildUrl(uriTemplate, form) {
        let path = uriTemplate;
        form.querySelectorAll('[data-in="path"]').forEach(input => {
            const val = input.value.trim();
            if (val) {
                path = path.replace('{' + input.name + '}', encodeURIComponent(val));
                path = path.replace('{' + input.name + '?}', encodeURIComponent(val));
            }
        });
        const url = new URL(path, window.location.origin);
        form.querySelectorAll('[data-in="query"]').forEach(input => {
            const val = input.value.trim();
            if (val !== '') url.searchParams.set(input.name, val);
        });
        return url;
    }

    function coerceValue(val, type) {
        if (val === '') return null;
        if (type === 'integer') {
            const n = parseInt(val, 10);
            return Number.isNaN(n) ? val : n;
        }
        if (type === 'number') {
            const n = parseFloat(val);
            return Number.isNaN(n) ? val : n;
        }
        if (type === 'boolean') {
            if (val === 'true' || val === '1') return true;
            if (val === 'false' || val === '0') return false;
        }
        return val;
    }

    function buildBody(form, hasFiles) {
        if (hasFiles) {
            const fd = new FormData();
            form.querySelectorAll('[data-in="body"]').forEach(input => {
                if (input.type === 'file') {
                    if (input.files?.[0]) fd.append(input.name, input.files[0]);
                } else if (input.value.trim() !== '') {
                    fd.append(input.name, input.value.trim());
                }
            });
            return { type: 'multipart', data: fd };
        }

        const jsonArea = form.querySelector('.try-body-json');
        if (jsonArea?.value.trim()) {
            try {
                return { type: 'json', data: JSON.parse(jsonArea.value) };
            } catch {
                throw new Error('Body JSON không hợp lệ.');
            }
        }

        const body = {};
        let hasField = false;
        form.querySelectorAll('.try-body-field, [data-in="body"]:not([type="file"])').forEach(input => {
            if (!input.name || input.classList.contains('try-body-json')) return;
            const val = input.value.trim();
            if (val === '') return;
            hasField = true;
            body[input.name] = coerceValue(val, input.dataset.type);
        });
        if (!hasField) return null;
        return { type: 'json', data: body };
    }

    document.querySelectorAll('.try-form').forEach(form => {
        const article = form.closest('.endpoint');
        const method = article?.dataset.method || 'GET';
        const uriTemplate = article?.dataset.uri || '';
        const needsAuth = article?.dataset.auth === '1';
        const hasFiles = !!form.querySelector('[data-type="file"]');
        const urlPreview = form.querySelector('.try-url');
        const responseBox = form.parentElement?.querySelector('.try-response');
        const statusBadge = responseBox?.querySelector('.status-badge');
        const timeEl = responseBox?.querySelector('.try-time');
        const bodyEl = responseBox?.querySelector('.try-response-body');

        const updatePreview = () => {
            try {
                urlPreview.textContent = buildUrl(uriTemplate, form).toString();
            } catch {
                urlPreview.textContent = uriTemplate;
            }
        };
        form.addEventListener('input', updatePreview);
        updatePreview();

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';

            const start = performance.now();
            responseBox?.classList.remove('hidden');

            try {
                const url = buildUrl(uriTemplate, form);
                urlPreview.textContent = url.toString();

                const headers = { 'Accept': 'application/json' };
                const token = getToken();
                if (needsAuth) {
                    if (!token) {
                        throw new Error('Endpoint này cần Bearer token. Nhập token ở sidebar hoặc đăng nhập.');
                    }
                    headers['Authorization'] = 'Bearer ' + token;
                }

                const options = { method, headers };
                const bodyMethods = ['POST', 'PUT', 'PATCH'];

                if (bodyMethods.includes(method)) {
                    const body = buildBody(form, hasFiles);
                    if (body?.type === 'multipart') {
                        options.body = body.data;
                    } else if (body?.type === 'json') {
                        headers['Content-Type'] = 'application/json';
                        options.body = JSON.stringify(body.data);
                    }
                }

                const res = await fetch(url.toString(), options);
                const elapsed = Math.round(performance.now() - start);
                const contentType = res.headers.get('content-type') || '';
                let responseText;
                if (contentType.includes('application/json')) {
                    const json = await res.json();
                    responseText = JSON.stringify(json, null, 2);
                    if (res.ok && json.data?.token && uriTemplate.includes('/login')) {
                        tokenInput.value = json.data.token;
                        localStorage.setItem(TOKEN_KEY, json.data.token);
                        tokenStatus?.classList.remove('hidden');
                    }
                } else {
                    responseText = await res.text();
                }

                const statusClass = res.status >= 200 && res.status < 300 ? 'status-2xx' : (res.status >= 400 ? 'status-4xx' : 'status-5xx');
                statusBadge.textContent = res.status + ' ' + res.statusText;
                statusBadge.className = 'status-badge ' + statusClass;
                timeEl.textContent = elapsed + ' ms';
                bodyEl.textContent = responseText;
            } catch (err) {
                statusBadge.textContent = 'Lỗi';
                statusBadge.className = 'status-badge status-5xx';
                timeEl.textContent = '';
                bodyEl.textContent = err.message || String(err);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi request';
            }
        });

        responseBox?.querySelector('.try-copy')?.addEventListener('click', () => {
            const text = bodyEl?.textContent || '';
            navigator.clipboard.writeText(text).then(() => {
                const btn = responseBox.querySelector('.try-copy');
                const orig = btn.textContent;
                btn.textContent = 'Đã sao chép';
                setTimeout(() => { btn.textContent = orig; }, 1500);
            });
        });
    });

    const search = document.getElementById('search');
    search?.addEventListener('input', () => {
        const q = search.value.trim().toLowerCase();
        document.querySelectorAll('.endpoint').forEach(el => {
            el.classList.toggle('hidden', q !== '' && !el.dataset.search.includes(q));
        });
        document.querySelectorAll('[data-nav-group]').forEach(group => {
            const links = group.querySelectorAll('[data-nav-search]');
            let visible = 0;
            links.forEach(link => {
                const show = q === '' || link.dataset.navSearch.includes(q);
                link.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            group.classList.toggle('hidden', visible === 0);
        });
    });

    if (location.hash) {
        document.querySelector(location.hash)?.classList.add('open');
    }
})();
</script>
</body>
</html>
