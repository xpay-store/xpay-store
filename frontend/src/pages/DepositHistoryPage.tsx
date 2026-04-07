import { useEffect, useState } from 'react';
import { api, formatApiError } from '../lib/api';
import type { Deposit } from '../types';

export function DepositHistoryPage() {
  const [items, setItems] = useState<Deposit[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const res = await api.get('/api/deposit/history');
        if (!cancelled) {
          setItems(res.data.data as Deposit[]);
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

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">سجل الشحن</div>

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">{error}</div>
      )}

      <div className="space-y-3">
        {items.map((d) => (
          <div key={d._id} className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
            <div className="flex items-center justify-between gap-2">
              <div className="truncate text-xs text-slate-400">{d.transaction_id}</div>
              <div className="text-xs text-brand-300">{d.status}</div>
            </div>
            <div className="mt-2 text-xs text-slate-300">
              {(d.amount?.USD ?? 0).toFixed(2)} USD · {(d.amount?.SYP ?? 0).toFixed(0)} SYP · {d.method}
            </div>
            <div className="mt-1 text-[10px] text-slate-500">{d.created_at || ''}</div>
          </div>
        ))}
        {items.length === 0 && <div className="text-sm text-slate-500">لا يوجد سجل.</div>}
      </div>
    </div>
  );
}
