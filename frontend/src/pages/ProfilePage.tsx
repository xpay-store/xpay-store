import { useEffect, useState } from 'react';
import { api, formatApiError } from '../lib/api';
import type { User } from '../types';

export function ProfilePage() {
  const [user, setUser] = useState<User | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const res = await api.get('/api/user/profile');
        if (!cancelled) {
          setUser(res.data.user as User);
        }
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

  if (error) {
    return <div className="p-4 text-red-300">{error}</div>;
  }

  if (!user) {
    return <div className="p-4 text-slate-400">جاري التحميل...</div>;
  }

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">الملف الشخصي</div>

      <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
        <div className="flex justify-between gap-4">
          <span className="text-slate-400">Telegram</span>
          <span className="text-white">{user.username ? `@${user.username}` : user.telegram_id}</span>
        </div>
        <div className="mt-3 flex justify-between gap-4">
          <span className="text-slate-400">الدور</span>
          <span className="text-white">{user.role}</span>
        </div>
        <div className="mt-3 flex justify-between gap-4">
          <span className="text-slate-400">USD</span>
          <span className="text-white">{(user.balance?.USD ?? 0).toFixed(2)}</span>
        </div>
        <div className="mt-3 flex justify-between gap-4">
          <span className="text-slate-400">SYP</span>
          <span className="text-white">{(user.balance?.SYP ?? 0).toFixed(0)}</span>
        </div>
      </div>
    </div>
  );
}
