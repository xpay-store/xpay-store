import { Link } from 'react-router-dom';
import type { Product } from '../types';

type Props = {
  product: Product;
};

export function ProductCard({ product }: Props) {
  const usd = product.price?.USD ?? 0;
  const syp = product.price?.SYP ?? 0;

  return (
    <Link
      to={`/order/confirm/${product._id}`}
      className="block rounded-2xl border border-slate-800 bg-slate-900/60 p-4 transition hover:border-brand-600"
    >
      <div className="flex gap-3">
        <div className="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-slate-800">
          {product.image ? (
            <img src={product.image} alt="" className="h-full w-full object-cover" />
          ) : (
            <div className="flex h-full w-full items-center justify-center text-xs text-slate-500">XP</div>
          )}
        </div>
        <div className="min-w-0 flex-1">
          <div className="truncate text-sm font-semibold text-white">{product.name}</div>
          <div className="mt-1 text-xs text-slate-400">
            {usd.toFixed(2)} USD · {syp.toFixed(0)} SYP
          </div>
          {!product.available && (
            <div className="mt-2 inline-block rounded bg-red-900/60 px-2 py-0.5 text-[10px] text-red-200">
              غير متوفر
            </div>
          )}
        </div>
      </div>
    </Link>
  );
}
