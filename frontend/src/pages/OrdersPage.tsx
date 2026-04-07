import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, formatApiError } from '../lib/api';
import type { Order } from '../types';

const tabs: { key: 'all' | 'wait' | 'accept' | 'reject'; label: string }[] = [
  { key: 'all', label: 'الكل' },
  { key: 'wait', label: 'قيد التنفيذ' },
  { key: 'accept', label: 'مكتمل' },
  { key: 'reject', label: 'ملغي' },
];

function statusLabel(s: Order['status']) {
  if (s === 'accept') {
    return 'مكتمل';
  }
  if (s === 'wait') {
    return 'قيد التنفيذ';
  }
  return 'ملغي';
}

export function OrdersPage() {
  const [tab, setTab] = useState<(typeof tabs)[number]['key']>('all');
  const [orders, setOrders] = useState<Order[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        setError(null);
        const status = tab === 'all' ? 'all' : tab;
        const res = await api.get('/api/orders/my', {
          params: { status },
        });
        if (!cancelled) {
          setOrders(res.data.data as Order[]);
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
  }, [tab]);

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">طلباتي</div>

      <div className="mb-4 flex flex-wrap gap-2">
        {tabs.map((t) => (
          <button
            key={t.key}
            type="button"
            onClick={() => setTab(t.key)}
            className={[
              'rounded-full px-3 py-1 text-xs',
              tab === t.key ? 'bg-brand-600 text-white' : 'bg-slate-900 text-slate-300',
            ].join(' ')}
          >
            {t.label}
          </button>
        ))}
      </div>

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">{error}</div>
      )}

      <div className="space-y-3">
        {orders.map((o) => (
          <Link
            key={o._id}
            to={`/orders/${o._id}`}
            className="block rounded-2xl border border-slate-800 bg-slate-900/60 p-4"
          >
            <div className="flex items-center justify-between gap-2">
              <div className="min-w-0">
                <div className="truncate text-sm font-semibold text-white">{o.order_number}</div>
                <div className="text-xs text-slate-500">{o.created_at || ''}</div>
              </div>
              <div className="text-xs text-brand-400">{statusLabel(o.status)}</div>
            </div>
            <div className="mt-2 text-xs text-slate-300">
              {(o.total_price?.USD ?? 0).toFixed(2)} USD · {(o.total_price?.SYP ?? 0).toFixed(0)} SYP
            </div>
          </Link>
        ))}
        {orders.length === 0 && <div className="text-sm text-slate-500">لا توجد طلبات.</div>}
      </div>
    </div>
  );
}
