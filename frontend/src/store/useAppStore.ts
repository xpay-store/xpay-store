import { create } from 'zustand';
import type { Category, Product, User } from '../types';

type State = {
  user: User | null;
  categories: Category[];
  products: Product[];
  setUser: (u: User | null) => void;
  setCategories: (c: Category[]) => void;
  setProducts: (p: Product[]) => void;
};

export const useAppStore = create<State>((set) => ({
  user: null,
  categories: [],
  products: [],
  setUser: (user) => set({ user }),
  setCategories: (categories) => set({ categories }),
  setProducts: (products) => set({ products }),
}));
