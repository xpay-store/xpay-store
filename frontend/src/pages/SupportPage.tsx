import { useEffect, useState } from 'react';
import { api, formatApiError } from '../lib/api';

export function SupportPage() {
  const [wa, setWa] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const res = await api.get('/api/user/profile');
        if (cancelled) {
          return;
        }
        const s = res.data.settings as { support_whatsapp?: string };
        setWa(s.support_whatsapp || null);
      } catch (e) {
        if (!cancelled) {
          setError(formatApiError(e));
        }
      }
    })();
    return () => {
      cancelled = true;
    };
  }, []);

  const href = wa ? `https://wa.me/${wa.replace(/[^\d]/g, '')}` : null;

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">التواصل مع الدعم</div>

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">{error}</div>
      )}

      <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm text-slate-200">
        <div className="text-xs text-slate-400">واتساب</div>
        <div className="mt-2 break-all text-white">{wa || 'غير مضبوط في الإعدادات'}</div>
      </div>

      {href && (
        <a
          href={href}
          target="_blank"
          rel="noreferrer"
          className="mt-6 block w-full rounded-xl bg-brand-600 py-3 text-center text-sm font-semibold text-white"
        >
          فتح واتساب
        </a>
      )}
    </div>
  );
}
