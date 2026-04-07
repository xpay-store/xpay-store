import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { api, formatApiError } from '../lib/api';
import type { Product } from '../types';

export function OrderConfirmPage() {
  const { productId } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState<Product | null>(null);
  const [qty, setQty] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [extra, setExtra] = useState('');

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const res = await api.get('/api/products');
        if (cancelled) {
          return;
        }
        const list = res.data.data as Product[];
        const found = list.find((p) => p._id === productId) || null;
        setProduct(found);
      } catch (e) {
        if (!cancelled) {
          setError(formatApiError(e));
        }
      }
    })();
    return () => {
      cancelled = true;
    };
  }, [productId]);

  const totalUsd = (product?.price?.USD ?? 0) * qty;
  const totalSyp = (product?.price?.SYP ?? 0) * qty;

  async function submit() {
    if (!product) {
      return;
    }
    setLoading(true);
    setError(null);
    try {
      const orderUuid = crypto.randomUUID();
      const params: Record<string, unknown> = {};
      if (extra.trim()) {
        params.note = extra.trim();
      }
      const res = await api.post('/api/order/create', {
        order_uuid: orderUuid,
        product_id: product._id,
        quantity: qty,
        params,
      });
      const id = res.data.order?._id as string;
      navigate(id ? `/orders/${id}` : '/orders');
    } catch (e) {
      setError(formatApiError(e));
    } finally {
      setLoading(false);
    }
  }

  if (!product && !error) {
    return <div className="p-4 text-slate-400">جاري التحميل...</div>;
  }

  if (!product) {
    return <div className="p-4 text-red-300">{error}</div>;
  }

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">تأكيد الطلب</div>

      <div className="mb-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
        <div className="text-sm font-semibold text-white">{product.name}</div>
        <div className="mt-2 text-xs text-slate-400">
          السعر للوحدة: {(product.price?.USD ?? 0).toFixed(2)} USD · {(product.price?.SYP ?? 0).toFixed(0)} SYP
        </div>
      </div>

      <label className="mb-2 block text-xs text-slate-400">الكمية</label>
      <input
        type="number"
        min={1}
        value={qty}
        onChange={(e) => setQty(Math.max(1, Number(e.target.value) || 1))}
        className="mb-4 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
      />

      <label className="mb-2 block text-xs text-slate-400">بيانات إضافية (اختياري)</label>
      <textarea
        value={extra}
        onChange={(e) => setExtra(e.target.value)}
        className="mb-4 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
        rows={3}
      />

      <div className="mb-4 rounded-xl border border-slate-800 bg-slate-950 p-4 text-sm text-slate-200">
        <div className="flex justify-between">
          <span>الإجمالي</span>
          <span>
            {totalUsd.toFixed(2)} USD · {totalSyp.toFixed(0)} SYP
          </span>
        </div>
        <div className="mt-2 text-xs text-slate-500">طريقة الدفع: رصيد المحفظة داخل المتجر</div>
      </div>

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">{error}</div>
      )}

      <button
        type="button"
        onClick={() => void submit()}
        disabled={loading || !product.available}
        className="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white disabled:opacity-50"
      >
        {loading ? 'جاري التنفيذ...' : 'تأكيد الشراء'}
      </button>
    </div>
  );
}
