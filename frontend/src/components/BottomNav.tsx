import { NavLink } from 'react-router-dom';

const items = [
  { to: '/', label: 'الرئيسية' },
  { to: '/orders', label: 'طلباتي' },
  { to: '/deposit', label: 'الإيداع' },
  { to: '/support', label: 'الدعم' },
];

export function BottomNav() {
  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 border-t border-slate-800 bg-slate-950/95 backdrop-blur">
      <div className="mx-auto flex max-w-lg items-center justify-around px-2 py-2">
        {items.map((item) => (
          <NavLink
            key={item.to}
            to={item.to}
            className={({ isActive }) =>
              [
                'rounded-lg px-3 py-2 text-xs font-medium',
                isActive ? 'bg-brand-600 text-white' : 'text-slate-400 hover:text-white',
              ].join(' ')
            }
            end={item.to === '/'}
          >
            {item.label}
          </NavLink>
        ))}
      </div>
    </nav>
  );
}
