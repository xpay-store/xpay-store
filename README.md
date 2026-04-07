# XPayStore

متجر رقمي لبيع الخدمات والبطاقات الرقمية يعمل كـ **Telegram Mini App** مع لوحة إدارة خارجية. هذا المستودع يحتوي على:

- **`backend/`**: Laravel 11 + MongoDB (`mongodb/laravel-mongodb`)، جاهز للنشر على **Render** (Dockerfile مضمن).
- **`frontend/`**: React + TypeScript + Vite + TailwindCSS + Zustand، جاهز للنشر على **Vercel**.
- **`backend/bots/`**: معالجات Webhook لبوتي Telegram (إيداع + متجر).

## المتطلبات السحابية

- **GitHub**: مصدر الكود.
- **MongoDB Atlas**: قاعدة البيانات (يفضّل طبقة **Replica Set** لدعم المعاملات عند الحاجة).
- **Supabase**: تخزين صور إثبات الإيداع عبر **Storage** (مفتاح Service Role على السيرفر فقط).
- **Render**: تشغيل Laravel + جدولة `php artisan schedule:run`.
- **Vercel**: استضافة الواجهة.
- **Telegram**: BotFather للبوتين وربط **Web App** و **Webhooks**.

## متغيرات البيئة

### Backend (`backend/.env`)

 انسخ من `backend/.env.example` واضبط على الأقل:

| المتغير | الوصف |
|--------|--------|
| `APP_KEY` | من `php artisan key:generate` |
| `APP_URL` | رابط الـ API العام |
| `MONGODB_URI` / `MONGODB_DATABASE` | اتصال Atlas |
| `TELEGRAM_STORE_BOT_TOKEN` / `TELEGRAM_DEPOSIT_BOT_TOKEN` | توكنات البوتات |
| `TELEGRAM_ADMIN_GROUP_ID` | معرف مجموعة إشعارات الإيداع (للبوت الإيداعي) |
| `TELEGRAM_DEPOSIT_WEBHOOK_SECRET` / `TELEGRAM_STORE_WEBHOOK_SECRET` | أسرار عشوائية طويلة لمسارات الـ webhook |
| `ALLOWED_USER_IDS` | معرفات Telegram مسموح لها بالأزرار (مكملة لأدوار admin/agent في MongoDB) |
| `SUPABASE_*` + `SUPABASE_STORAGE_BUCKET` | رفع صور الإيداع من السيرفر |
| `ADMIN_API_TOKEN` | Bearer token لواجهات `/admin/*` |
| `ADMIN_EMAIL` | بريد تقرير المبيعات اليومي |
| `FRONTEND_URL` | أصل Vercel (لـ CORS وزر WebApp في البوت) |
| `MERSAL_API_URL` / `MERSAL_API_TOKEN` | مزامنة افتراضية عبر أمر `sync:products` |

### Frontend (`frontend/.env`)

| المتغير | الوصف |
|--------|--------|
| `VITE_API_URL` | رأس الـ API (مثلاً `https://api.example.com` بدون `/` نهائي) |
| `VITE_TELEGRAM_BOT_USERNAME` | اسم مستخدم بوت المتجر (للتوثيق/الروابط) |

الواجهة ترسل تلقائياً `X-Telegram-Init-Data` من `@twa-dev/sdk` لجميع طلبات `/api/*`.

## API (ملخص)

### عامة (مستخدم Mini App) — مسبوقة بـ `/api`

- `GET /api/user/profile`
- `GET /api/products` · `GET /api/products/search?q=`
- `GET /api/categories`
- `POST /api/order/create` (يتطلب `order_uuid` فريداً)
- `GET /api/order/status/{order_id}`
- `GET /api/orders/my?status=...`
- `POST /api/deposit/create` (multipart أو JSON مع `proof_url`)
- `GET /api/deposit/history`

### إدارة — مسبوقة بـ `/admin` (Bearer `ADMIN_API_TOKEN`)

- `POST /admin/products/import`
- `PUT /admin/products/{id}` · `DELETE /admin/products/{id}`
- `GET /admin/deposits/pending` · `POST /admin/deposits/{id}/approve|reject`
- `GET /admin/reports/sales`
- `PUT /admin/settings/general`
- `POST /admin/categories/order`
- `GET /admin/users` · `POST /admin/users/{id}/balance`

### Webhooks (Telegram)

- `POST /api/webhooks/telegram/deposit/{secret}` — بوت الإيداع (أزرار قبول/رفض).
- `POST /api/webhooks/telegram/store/{secret}` — بوت المتجر (`/start` + زر WebApp).

ضع نفس `{secret}` المستخدم في الرابط عند استدعاء `setWebhook` في Telegram.

## الجدولة (Cron على Render)

على Render أضف **Background Worker** أو **Cron** يشغّل كل دقيقة:

```bash
php artisan schedule:run
```

الأوامر المجدولة في `backend/routes/console.php`:

| Job | تكرار |
|-----|--------|
| `sync:products` | كل 6 ساعات |
| `deposit:timeout` | كل ساعة |
| `report:daily` | يومياً 00:00 |
| `provider:balance` | كل ساعة |
| `logs:clean` | أسبوعياً |

## النشر السريع

### Render (Backend)

1. خدمة **Web** من مجلد `backend` باستخدام **Dockerfile** المرفق.
2. متغيرات البيئة من `.env.example`.
3. بعد أول نشر: توليد `APP_KEY` (Shell على Render): `php artisan key:generate --show` ثم حفظ القيمة في لوحة Render.
4. ربط Webhooks للبوتين على عناوين `/api/webhooks/telegram/.../{secret}`.

### Vercel (Frontend)

1. جذر البناء: `frontend`.
2. أمر البناء: `npm install && npm run build`.
3. مجلد الإخراج: `dist`.
4. ضبط `VITE_API_URL` في إعدادات المشروع.

## الفهارس الموصى بها في MongoDB Atlas

- `users.telegram_id` (فريد)
- `orders.order_uuid` (فريد)
- فهارس اختيارية على `orders.user_id`, `deposits.user_id`, `deposits.status`

## هيكل البوتات

ملفات المعالجة موجودة تحت **`backend/bots/`** وتُحمّل عبر Composer (`"Bots\\": "bots/"`).

## الترخيص

ملكية خاصة للمشروع؛ الاستخدام حسب اتفاق صاحب المشروع.
