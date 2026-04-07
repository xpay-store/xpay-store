export type MoneyPair = { USD?: number; SYP?: number };

export type User = {
  _id: string;
  telegram_id: number;
  username?: string | null;
  balance: MoneyPair;
  role: string;
};

export type Category = {
  _id: string;
  name: string;
  image?: string;
  order?: number;
};

export type Product = {
  _id: string;
  name: string;
  category_id?: string | null;
  price: MoneyPair;
  available: boolean;
  image?: string;
  product_type?: string;
  qty_values?: number[];
  params?: Record<string, unknown>;
};

export type Order = {
  _id: string;
  order_uuid: string;
  order_number: string;
  status: 'wait' | 'accept' | 'reject';
  total_price: MoneyPair;
  created_at?: string;
  product_id?: string;
  quantity?: number;
};

export type Deposit = {
  _id: string;
  amount: MoneyPair;
  currency: string;
  method: string;
  transaction_id: string;
  proof_image?: string;
  status: string;
  created_at?: string;
};
