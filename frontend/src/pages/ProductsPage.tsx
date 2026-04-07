import { useEffect, useMemo, useState } from 'react';
import { useParams } from 'react-router-dom';
import { api, formatApiError } from '../lib/api';
import type { Product } from '../types';
import { ProductCard } from '../components/ProductCard';

export function ProductsPage() {
  const { categoryId } = useParams();
  const [products, setProducts] = useState<Product[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      try {
        setError(null);
        const res = await api.get('/api/products', {
          params: { category_id: categoryId },
        });
        if (!cancelled) {
          setProducts(res.data.data as Product[]);
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
  }, [categoryId]);

  const title = useMemo(() => 'منتجات القسم', []);

  return (
    <div className="mx-auto max-w-lg px-4 pt-4">
      <div className="mb-4 text-lg font-bold text-white">{title}</div>
      {error && (
        <div className="mb-4 rounded-xl border border-red-900 bg-red-950/50 p-3 text-sm text-red-200">
          {error}
        </div>
      )}
      <div className="space-y-3">
        {products.map((p) => (
          <ProductCard key={p._id} product={p} />
        ))}
        {products.length === 0 && <div className="text-sm text-slate-500">لا توجد منتجات في هذا القسم.</div>}
      </div>
    </div>
  );
}
