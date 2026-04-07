import { Link } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { api, formatApiError } from '../lib/api';
import type { Deposit } from '../types';

export function DepositPage() {
  const [items, setItems] = useState<Deposit[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const res = await api.get('/api/deposit/history');
        if (!cancelled) {
          setItems((res.data.data as Deposit[]).slice(0, 5));
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

  const spentUsd = items.reduce((a, d) => a + (d.amount?.USD ?? 0), 0);

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">الإيداع</div>

      <div className="mb-4 grid grid-cols-1 gap-3">
        <Link
          to="/deposit/sham"
          className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-center text-sm font-semibold text-white hover:border-brand-600"
        >
          شام كاش
        </Link>
        <Link
          to="/deposit/binance"
          className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-center text-sm font-semibold text-white hover:border-brand-600"
        >
          Binance Pay
        </Link>
        <Link
          to="/deposit/history"
          className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-center text-sm text-slate-200 hover:border-brand-600"
        >
          سجل الشحن الكامل
        </Link>
      </div>

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">{error}</div>
      )}

      <div className="rounded-2xl border border-slate-800 bg-slate-950 p-4 text-sm text-slate-200">
        <div className="flex justify-between">
          <span>آخر العمليات</span>
          <span className="text-slate-500">{items.length} عمليات</span>
        </div>
        <div className="mt-3 space-y-2">
          {items.map((d) => (
            <div key={d._id} className="flex justify-between text-xs">
              <span className="text-slate-400">{d.transaction_id}</span>
              <span>{d.status}</span>
            </div>
          ))}
          {items.length === 0 && <div className="text-xs text-slate-500">لا يوجد سجل بعد.</div>}
        </div>
        <div className="mt-4 border-t border-slate-800 pt-3 text-xs text-slate-400">
          إجمالي آخر العمليات المعروضة (تقريبي): {spentUsd.toFixed(2)} USD
        </div>
      </div>
    </div>
  );
}
