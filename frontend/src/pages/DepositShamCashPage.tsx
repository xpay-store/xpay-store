import { useEffect, useState } from 'react';
import { api, formatApiError } from '../lib/api';

export function DepositShamCashPage() {
  const [wallet, setWallet] = useState('');
  const [minUsd, setMinUsd] = useState(1);
  const [rate, setRate] = useState(15000);
  const [amountUsd, setAmountUsd] = useState('10');
  const [tx, setTx] = useState('');
  const [file, setFile] = useState<File | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [done, setDone] = useState(false);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        const res = await api.get('/api/user/profile');
        if (cancelled) {
          return;
        }
        const s = res.data.settings as Record<string, unknown>;
        setWallet(String(s.sham_cash_wallet || ''));
        setMinUsd(Number(s.min_deposit_usd || 1));
        setRate(Number(s.usd_to_syp_rate || 15000));
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

  const usd = Number(amountUsd) || 0;
  const syp = usd * rate;

  async function submit() {
    setLoading(true);
    setError(null);
    try {
      const fd = new FormData();
      fd.append('method', 'sham_cash');
      fd.append('currency', 'USD');
      fd.append('amount_usd', String(usd));
      fd.append('amount_syp', String(syp));
      fd.append('transaction_id', tx);
      if (file) {
        fd.append('proof_image', file);
      }
      await api.post('/api/deposit/create', fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      setDone(true);
    } catch (e) {
      setError(formatApiError(e));
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">إيداع شام كاش</div>

      <div className="mb-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm text-slate-200">
        <div className="text-xs text-slate-400">عنوان المحفظة</div>
        <div className="mt-1 break-all text-white">{wallet || '—'}</div>
      </div>

      <label className="mb-1 block text-xs text-slate-400">المبلغ (USD)</label>
      <input
        value={amountUsd}
        onChange={(e) => setAmountUsd(e.target.value)}
        className="mb-3 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
      />

      <div className="mb-4 text-xs text-slate-500">
        الحد الأدنى: {minUsd} USD · يعادل تقريباً {syp.toFixed(0)} SYP (سعر مرجعي {rate})
      </div>

      <label className="mb-1 block text-xs text-slate-400">رقم العملية</label>
      <input
        value={tx}
        onChange={(e) => setTx(e.target.value)}
        className="mb-3 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
      />

      <label className="mb-1 block text-xs text-slate-400">صورة إثبات التحويل</label>
      <input
        type="file"
        accept="image/*"
        onChange={(e) => setFile(e.target.files?.[0] || null)}
        className="mb-4 w-full text-sm text-slate-300"
      />

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">{error}</div>
      )}
      {done && (
        <div className="mb-4 rounded-xl border border-emerald-900 bg-emerald-950/40 p-3 text-sm text-emerald-100">
          تم إرسال طلب الإيداع بنجاح.
        </div>
      )}

      <button
        type="button"
        disabled={loading || usd < minUsd || tx.trim() === '' || !file}
        onClick={() => void submit()}
        className="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white disabled:opacity-50"
      >
        {loading ? 'جاري الإرسال...' : 'إرسال الطلب'}
      </button>
    </div>
  );
}
