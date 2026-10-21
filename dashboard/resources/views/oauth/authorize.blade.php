<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ربط مساعد ذكي — Connect an AI assistant</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #0f172a; font-family: ui-sans-serif, system-ui, 'Segoe UI', sans-serif; }
        .card { width: 100%; max-width: 28rem; margin: 1rem; padding: 2rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; box-shadow: 0 1px 2px rgba(15, 23, 42, .05); }
        h1 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .muted { margin-top: .5rem; font-size: .875rem; color: #64748b; }
        .statement { margin-top: 1rem; padding: 1rem; border-radius: .75rem; background: #eff6ff; font-size: .875rem; line-height: 1.6; color: #334155; }
        .denied { margin-top: 1rem; padding: 1rem; border-radius: .75rem; background: #fef2f2; font-size: .875rem; line-height: 1.6; color: #b91c1c; }
        .actions { margin-top: 1.5rem; display: flex; gap: .75rem; }
        form { flex: 1; }
        button { width: 100%; padding: .625rem 1rem; border-radius: .75rem; font-size: .875rem; font-weight: 600; cursor: pointer; }
        .approve { border: 0; background: #2563eb; color: #fff; }
        .approve:hover { background: #1d4ed8; }
        .deny { border: 1px solid #cbd5e1; background: #fff; color: #334155; }
        .deny:hover { background: #f8fafc; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $client->name }} — ربط مساعد ذكي / Connect an AI assistant</h1>
        <p class="muted">حسابك / Signed in as: {{ $user->name }}</p>

        @if ($user->status === \App\Enums\UserStatus::Active && $user->can('Use:Mcp'))
            <p class="statement">
                سيستطيع هذا التطبيق قراءة وإدارة محتوى الإنجازات باسمك.<br>
                This application will be able to read and manage Injazat content on your behalf.
            </p>

            <div class="actions">
                <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                    @csrf
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="approve">موافقة / Approve</button>
                </form>

                <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="deny">رفض / Deny</button>
                </form>
            </div>
        @else
            <p class="denied">
                حسابك غير مخوّل لربط أدوات الذكاء الاصطناعي. اطلب من المدير صلاحية Use:Mcp.<br>
                Your account is not allowed to connect AI tools. Ask an administrator for the Use:Mcp permission.
            </p>
        @endif
    </div>
</body>
</html>
