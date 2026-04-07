import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import { BottomNav } from './components/BottomNav';
import { DepositPage } from './pages/DepositPage';
import { DepositBinancePage } from './pages/DepositBinancePage';
import { DepositHistoryPage } from './pages/DepositHistoryPage';
import { DepositShamCashPage } from './pages/DepositShamCashPage';
import { HomePage } from './pages/HomePage';
import { OrderConfirmPage } from './pages/OrderConfirmPage';
import { OrderDetailPage } from './pages/OrderDetailPage';
import { OrdersPage } from './pages/OrdersPage';
import { ProductsPage } from './pages/ProductsPage';
import { ProfilePage } from './pages/ProfilePage';
import { SupportPage } from './pages/SupportPage';

export default function App() {
  return (
    <BrowserRouter>
      <div className="min-h-screen bg-slate-950 pb-24">
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/category/:categoryId" element={<ProductsPage />} />
          <Route path="/order/confirm/:productId" element={<OrderConfirmPage />} />
          <Route path="/orders" element={<OrdersPage />} />
          <Route path="/orders/:orderId" element={<OrderDetailPage />} />
          <Route path="/deposit" element={<DepositPage />} />
          <Route path="/deposit/sham" element={<DepositShamCashPage />} />
          <Route path="/deposit/binance" element={<DepositBinancePage />} />
          <Route path="/deposit/history" element={<DepositHistoryPage />} />
          <Route path="/support" element={<SupportPage />} />
          <Route path="/profile" element={<ProfilePage />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
        <BottomNav />
      </div>
    </BrowserRouter>
  );
}
