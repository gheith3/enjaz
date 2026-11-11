{{-- OAuth consent screen of AI assistants (MCP, docs/mcp.md), registered in PassportServiceProvider. --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>ربط مساعد ذكي — {{ config('app.name') }}</title>
    <style>
        :root { --bg: #f1f5f9; --card: #fff; --border: #e2e8f0; --text: #0f172a; --muted: #64748b; --note: #eff6ff; --note-text: #334155; --bad: #fef2f2; --bad-text: #b91c1c; --primary: #8a6d2f; --primary-hover: #74591f; }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #0b1120; --card: #111827; --border: #1f2937; --text: #f1f5f9; --muted: #94a3b8; --note: #172033; --note-text: #cbd5e1; --bad: #2a1215; --bad-text: #fca5a5; --primary: #b8954a; --primary-hover: #a3823b; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--bg); color: var(--text); font-family: system-ui, 'Segoe UI', Tahoma, sans-serif; }
        .card { width: 100%; max-width: 28rem; margin: 1rem; padding: 2rem; background: var(--card); border: 1px solid var(--border); border-radius: 1rem; }
        h1 { margin: 0; font-size: 1.25rem; font-weight: 700; }
        .muted { margin-top: .5rem; font-size: .875rem; color: var(--muted); }
        .statement, .denied { margin-top: 1rem; padding: 1rem; border-radius: .75rem; font-size: .9rem; line-height: 1.8; }
        .statement { background: var(--note); color: var(--note-text); }
        .denied { background: var(--bad); color: var(--bad-text); }
        .actions { margin-top: 1.5rem; display: flex; gap: .75rem; }
        form { flex: 1; }
        button { width: 100%; padding: .7rem 1rem; border-radius: .75rem; font: inherit; font-size: .9rem; font-weight: 700; cursor: pointer; }
        .approve { border: 0; background: var(--primary); color: #fff; }
        .approve:hover { background: var(--primary-hover); }
        .deny { border: 1px solid var(--border); background: transparent; color: var(--text); }
    </style>
</head>
<body>
    <main class="card">
        <h1>ربط مساعد ذكي: {{ $client->name }}</h1>
        <p class="muted">الحساب: {{ $user->name }} ({{ $user->email }})</p>

        @if ($user->canUseMcp())
            <p class="statement">
                سيتمكن هذا التطبيق من قراءة الإنجازات والمنجزين وإضافتها وتعديلها وحذفها ورفع صورها باسمك في {{ config('app.name') }}.
                يمكنك إلغاء الربط في أي وقت من إعدادات المساعد.
            </p>

            <div class="actions">
                <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                    @csrf
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="approve">موافقة</button>
                </form>

                <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="deny">رفض</button>
                </form>
            </div>
        @else
            <p class="denied">
                حسابك غير مخوّل بربط أدوات الذكاء الاصطناعي. اطلب من مدير الموقع صلاحية «Use:Mcp».
            </p>
        @endif
    </main>
</body>
</html>
