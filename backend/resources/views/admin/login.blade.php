<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>XPayStore Admin</title>
    <style>
      :root { color-scheme: dark; }
      body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans Arabic", sans-serif; background:#020617; color:#e2e8f0; }
      .wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
      .card { width:100%; max-width:420px; background:rgba(15,23,42,.7); border:1px solid #1f2937; border-radius:16px; padding:20px; }
      .h { font-size:18px; font-weight:800; color:#fff; margin:0 0 6px; }
      .p { margin:0 0 16px; font-size:12px; color:#94a3b8; }
      label { display:block; font-size:12px; color:#cbd5e1; margin-bottom:6px; }
      input { width:100%; padding:12px 14px; border-radius:12px; border:1px solid #334155; background:#0b1220; color:#e2e8f0; outline:none; }
      input:focus { border-color:#22c55e; box-shadow:0 0 0 2px rgba(34,197,94,.2); }
      .btn { margin-top:14px; width:100%; padding:12px 14px; border-radius:12px; border:0; background:#16a34a; color:#fff; font-weight:700; cursor:pointer; }
      .err { margin-top:12px; padding:10px 12px; border-radius:12px; border:1px solid #7f1d1d; background:rgba(127,29,29,.25); color:#fecaca; font-size:12px; }
      .meta { margin-top:14px; font-size:11px; color:#64748b; line-height:1.5; }
      code { background:#0b1220; border:1px solid #1f2937; padding:2px 6px; border-radius:8px; }
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="card">
        <h1 class="h">لوحة تحكم XPayStore</h1>
        <p class="p">تسجيل دخول مدير باستخدام <code>ADMIN_API_TOKEN</code>.</p>

        <form method="POST" action="/admin/login">
          @csrf
          <label for="token">التوكن</label>
          <input id="token" name="token" type="password" autocomplete="current-password" placeholder="Paste ADMIN_API_TOKEN" value="{{ old('token') }}" />

          <button class="btn" type="submit">دخول</button>
        </form>

        @if ($errors->any())
          <div class="err">{{ $errors->first() }}</div>
        @endif

        <div class="meta">
          ملاحظة: هذا الدخول ينشئ جلسة مؤقتة (Session) على نفس الدومين. لا تستخدم هذا الرابط على شبكة غير موثوقة.
        </div>
      </div>
    </div>
  </body>
</html>

