import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api, formatApiError } from '../lib/api';
import type { Order } from '../types';

export function OrderDetailPage() {
  const { orderId } = useParams();
  const [order, setOrder] = useState<Order | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        setError(null);
        const res = await api.get(`/api/order/status/${orderId}`);
        if (!cancelled) {
          setOrder(res.data.order as Order);
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
  }, [orderId]);

  if (error) {
    return <div className="p-4 text-red-300">{error}</div>;
  }

  if (!order) {
    return <div className="p-4 text-slate-400">جاري التحميل...</div>;
  }

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">تفاصيل العملية</div>

      <div className="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
        <Row label="رقم الطلب" value={order.order_number} />
        <Row label="الحالة" value={order.status} />
        <Row label="المبلغ USD" value={(order.total_price?.USD ?? 0).toFixed(2)} />
        <Row label="المبلغ SYP" value={(order.total_price?.SYP ?? 0).toFixed(0)} />
        <Row label="التاريخ" value={order.created_at || '—'} />
      </div>

      <Link
        to="/support"
        className="mt-6 block w-full rounded-xl border border-slate-800 py-3 text-center text-sm text-white"
      >
        تواصل مع الدعم
      </Link>
    </div>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex items-start justify-between gap-4">
      <div className="text-slate-400">{label}</div>
      <div className="text-left text-white">{value}</div>
    </div>
  );
}
