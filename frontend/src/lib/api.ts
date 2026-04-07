import axios, { AxiosError } from 'axios';
import WebApp from '@twa-dev/sdk';

const baseURL = import.meta.env.VITE_API_URL as string;

export const api = axios.create({
  baseURL: baseURL?.replace(/\/$/, ''),
  timeout: 60000,
});

api.interceptors.request.use((config) => {
  const initData = WebApp.initData;
  if (initData) {
    config.headers['X-Telegram-Init-Data'] = initData;
  }
  return config;
});

export function formatApiError(err: unknown): string {
  if (axios.isAxiosError(err)) {
    const ax = err as AxiosError<{ message?: string }>;
    return ax.response?.data?.message || ax.message || 'Request failed';
  }
  if (err instanceof Error) {
    return err.message;
  }
  return 'Unexpected error';
}
