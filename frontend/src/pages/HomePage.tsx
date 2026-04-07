import { useEffect, useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { api, formatApiError } from '../lib/api';
import { useAppStore } from '../store/useAppStore';
import type { Category, Product } from '../types';
import { ProductCard } from '../components/ProductCard';

export function HomePage() {
  const [q, setQ] = useState('');
  const [error, setError] = useState<string | null>(null);
  const { setUser, setCategories, setProducts, categories, products, user } = useAppStore();

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        setError(null);
        const [profileRes, catRes, prodRes] = await Promise.all([
          api.get('/api/user/profile'),
          api.get('/api/categories'),
          api.get('/api/products'),
        ]);
        if (cancelled) {
          return;
        }
        setUser(profileRes.data.user);
        setCategories(catRes.data.data as Category[]);
        setProducts(prodRes.data.data as Product[]);
      } catch (e) {
        if (!cancelled) {
          setError(formatApiError(e));
        }
      }
    })();
    return () => {
      cancelled = true;
    };
  }, [setUser, setCategories, setProducts]);

  const filtered = useMemo(() => {
    if (!q.trim()) {
      return products;
    }
    const t = q.trim().toLowerCase();
    return products.filter((p) => p.name.toLowerCase().includes(t));
  }, [products, q]);

  const topSelling = useMemo(() => products.slice(0, 6), [products]);

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <header className="mb-4 flex items-center justify-between gap-3">
        <div>
          <div className="text-xs text-slate-400">رصيدك</div>
          <div className="text-lg font-bold text-white">
            {(user?.balance?.USD ?? 0).toFixed(2)} USD
            <span className="mx-2 text-slate-600">|</span>
            {(user?.balance?.SYP ?? 0).toFixed(0)} SYP
          </div>
        </div>
        <Link
          to="/profile"
          className="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-200"
        >
          حسابي
        </Link>
      </header>

      <div className="mb-4">
        <input
          value={q}
          onChange={(e) => setQ(e.target.value)}
          placeholder="بحث عن منتج..."
          className="w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm outline-none ring-brand-600 focus:ring-2"
        />
      </div>

      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">
          {error}
        </div>
      )}

      <section className="mb-6">
        <div className="mb-2 text-sm font-semibold text-white">التصنيفات</div>
        <div className="grid grid-cols-2 gap-3">
          {categories.map((c) => (
            <Link
              key={c._id}
              to={`/category/${c._id}`}
              className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-center text-sm font-medium text-white hover:border-brand-600"
            >
              {c.name}
            </Link>
          ))}
        </div>
      </section>

      <section className="mb-6">
        <div className="mb-2 flex items-center justify-between">
          <div className="text-sm font-semibold text-white">عروض مميزة</div>
          <span className="text-[10px] text-slate-500">محدثة من المزود</span>
        </div>
        <div className="rounded-2xl border border-dashed border-slate-800 bg-slate-900/40 p-4 text-xs text-slate-400">
          تابع أحدث العروض داخل الأقسام. يتم تحديث الأسعار تلقائياً من السيرفر.
        </div>
      </section>

      <section>
        <div className="mb-2 text-sm font-semibold text-white">
          {q.trim() ? 'نتائج البحث' : 'الأكثر مبيعاً'}
        </div>
        <div className="space-y-3">
          {(q.trim() ? filtered : topSelling).map((p) => (
            <ProductCard key={p._id} product={p} />
          ))}
          {filtered.length === 0 && <div className="text-sm text-slate-500">لا توجد منتجات.</div>}
        </div>
      </section>
    </div>
  );
}
