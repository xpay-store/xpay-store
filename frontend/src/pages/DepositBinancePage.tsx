import { useEffect, useState } from 'react';
import { api, formatApiError } from '../lib/api';

export function DepositBinancePage() {
  const [id, setId] = useState('');
  const [memo, setMemo] = useState('');
  const [minUsd, setMinUsd] = useState(1);
  const [rate, setRate] = useState(15000);
  const [amountUsd, setAmountUsd] = useState('10');
  const [tx, setTx] = useState('');
  const [proofUrl, setProofUrl] = useState('');
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
        setId(String(s.binance_pay_id || ''));
        setMemo(String(s.binance_memo || ''));
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
      await api.post('/api/deposit/create', {
        method: 'binance_pay',
        currency: 'USD',
        amount_usd: usd,
        amount_syp: syp,
        transaction_id: tx,
        proof_url: proofUrl,
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
      <div className="mb-4 text-lg font-bold text-white">إيداع Binance Pay</div>

      <div className="mb-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm text-slate-200">
        <div className="text-xs text-slate-400">Binance Pay ID</div>
        <div className="mt-1 break-all text-white">{id || '—'}</div>
        <div className="mt-3 text-xs text-slate-400">Memo</div>
        <div className="mt-1 break-all text-white">{memo || '—'}</div>
      </div>

      <label className="mb-1 block text-xs text-slate-400">مبلغ الإيداع (USD)</label>
      <input
        value={amountUsd}
        onChange={(e) => setAmountUsd(e.target.value)}
        className="mb-3 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
      />

      <div className="mb-4 text-xs text-slate-500">
        الحد الأدنى: {minUsd} USD · مرجع SYP: {syp.toFixed(0)}
      </div>

      <label className="mb-1 block text-xs text-slate-400">رقم العملية / TXID</label>
      <input
        value={tx}
        onChange={(e) => setTx(e.target.value)}
        className="mb-3 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
      />

      <label className="mb-1 block text-xs text-slate-400">رابط صورة الإثبات (من تخزين Supabase)</label>
      <input
        value={proofUrl}
        onChange={(e) => setProofUrl(e.target.value)}
        className="mb-4 w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm"
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
        disabled={loading || usd < minUsd || tx.trim() === '' || proofUrl.trim() === ''}
        onClick={() => void submit()}
        className="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white disabled:opacity-50"
      >
        {loading ? 'جاري الإرسال...' : 'إرسال الطلب'}
      </button>
    </div>
  );
}
