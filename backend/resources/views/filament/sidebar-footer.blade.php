<div style="padding: 12px; border-top: 1px solid #E5E7EB;">
    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
        @csrf
        <button type="submit" style="width:100%;border:1px solid #E5E7EB;background:#F9FAFB;color:#111827;padding:8px 10px;border-radius:10px;cursor:pointer;">
            تسجيل الخروج
        </button>
    </form>
</div>

