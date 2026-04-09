<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>XPayStore Admin</title>
    <style>
      :root { color-scheme: dark; }
      body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, "Noto Sans Arabic", sans-serif; background:#020617; color:#e2e8f0; }
      a { color: inherit; text-decoration: none; }
      .top { position: sticky; top:0; z-index:10; background: rgba(2,6,23,.85); backdrop-filter: blur(10px); border-bottom: 1px solid #1f2937; }
      .topin { max-width: 1100px; margin: 0 auto; padding: 14px 16px; display:flex; align-items:center; justify-content: space-between; gap: 12px; }
      .brand { font-weight: 900; color:#fff; }
      .btn { border:1px solid #334155; background:#0b1220; color:#e2e8f0; padding:8px 10px; border-radius:12px; cursor:pointer; font-size:12px; }
      .btn.primary { background:#16a34a; border-color:#16a34a; color:#fff; font-weight:700; }
      .wrap { max-width:1100px; margin:0 auto; padding: 16px; }
      .grid { display:grid; grid-template-columns: 1fr; gap: 12px; }
      @media (min-width: 960px) { .grid { grid-template-columns: 420px 1fr; } }
      .card { border:1px solid #1f2937; background: rgba(15,23,42,.7); border-radius: 16px; padding: 14px; }
      .h { margin:0 0 10px; font-weight:900; color:#fff; font-size:14px; }
      .sub { font-size:12px; color:#94a3b8; margin:0 0 12px; }
      .kpis { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 10px; }
      @media (min-width: 600px) { .kpis { grid-template-columns: repeat(4, minmax(0,1fr)); } }
      .kpi { border:1px solid #1f2937; background:#0b1220; border-radius:14px; padding: 10px; }
      .kpi .t { font-size:11px; color:#94a3b8; }
      .kpi .v { margin-top:6px; font-weight:900; color:#fff; font-size:16px; }
      input { width: 100%; padding: 10px 12px; border-radius: 12px; border:1px solid #334155; background:#0b1220; color:#e2e8f0; outline:none; }
      input:focus { border-color:#22c55e; box-shadow:0 0 0 2px rgba(34,197,94,.2); }
      .row { display:flex; gap:10px; align-items:center; }
      .row > * { flex: 1; }
      table { width:100%; border-collapse: collapse; }
      th, td { text-align:right; padding: 10px 8px; border-top: 1px solid #1f2937; font-size: 12px; vertical-align: top; }
      th { color:#94a3b8; font-weight:700; }
      .pill { display:inline-block; padding:2px 8px; border-radius:999px; border:1px solid #334155; font-size:11px; color:#cbd5e1; }
      .pill.ok { border-color:#14532d; background: rgba(20,83,45,.35); color:#bbf7d0; }
      .pill.bad { border-color:#7f1d1d; background: rgba(127,29,29,.25); color:#fecaca; }
      .actions { display:flex; flex-wrap: wrap; gap:8px; justify-content:flex-start; }
      .muted { color:#94a3b8; font-size:12px; }
      .err { margin-top: 10px; padding:10px 12px; border-radius: 12px; border:1px solid #7f1d1d; background: rgba(127,29,29,.25); color:#fecaca; font-size:12px; }
      .okbox { margin-top: 10px; padding:10px 12px; border-radius: 12px; border:1px solid #14532d; background: rgba(20,83,45,.25); color:#bbf7d0; font-size:12px; }
      .tabs { display:flex; flex-wrap: wrap; gap:8px; margin-bottom: 10px; }
      .tab { border:1px solid #334155; background:#0b1220; color:#e2e8f0; padding:8px 10px; border-radius:999px; cursor:pointer; font-size:12px; }
      .tab.active { background:#16a34a; border-color:#16a34a; color:#fff; font-weight:800; }
      .hide { display:none; }
      .inline { display:inline; }
    </style>
  </head>
  <body>
    <div class="top">
      <div class="topin">
        <div class="brand">XPayStore Admin</div>
        <form method="POST" action="/admin/logout" class="inline">
          @csrf
          <button class="btn" type="submit">تسجيل خروج</button>
        </form>
      </div>
    </div>

    <div class="wrap">
      <div class="card">
        <div class="h">إحصائيات</div>
        <div class="sub">بيانات سريعة عن النظام</div>
        <div class="kpis" id="kpis">
          <div class="kpi"><div class="t">المستخدمون</div><div class="v" id="k_users">—</div></div>
          <div class="kpi"><div class="t">الطلبات</div><div class="v" id="k_orders">—</div></div>
          <div class="kpi"><div class="t">المنتجات</div><div class="v" id="k_products">—</div></div>
          <div class="kpi"><div class="t">إيداعات معلّقة</div><div class="v" id="k_pending">—</div></div>
        </div>
        <div class="muted" style="margin-top:10px">إجمالي الإيداعات المقبولة: <span id="k_dep_usd">—</span> USD · <span id="k_dep_syp">—</span> SYP</div>
        <div id="stats_msg"></div>
      </div>

      <div class="grid" style="margin-top:12px">
        <div class="card">
          <div class="h">الأقسام</div>
          <div class="tabs">
            <button class="tab active" data-tab="users">المستخدمون</button>
            <button class="tab" data-tab="deposits">الإيداعات المعلقة</button>
            <button class="tab" data-tab="products">المنتجات</button>
          </div>
          <div class="muted">يتم جلب البيانات من `/admin/ui/*` (جلسة + CSRF).</div>
        </div>

        <div class="card" id="panel_users">
          <div class="h">المستخدمون</div>
          <div class="row" style="margin-bottom:10px">
            <input id="users_q" placeholder="بحث باليوزر أو Telegram ID..." />
            <button class="btn primary" id="users_reload">تحديث</button>
          </div>
          <div id="users_msg"></div>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr>
                  <th>Telegram</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>USD</th>
                  <th>SYP</th>
                  <th>حظر</th>
                </tr>
              </thead>
              <tbody id="users_body"></tbody>
            </table>
          </div>
        </div>

        <div class="card hide" id="panel_deposits">
          <div class="h">الإيداعات المعلقة</div>
          <div class="row" style="margin-bottom:10px">
            <div class="muted">إظهار آخر 500 عملية معلقة.</div>
            <button class="btn primary" id="dep_reload">تحديث</button>
          </div>
          <div id="dep_msg"></div>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr>
                  <th>المعرف</th>
                  <th>المستخدم</th>
                  <th>USD</th>
                  <th>SYP</th>
                  <th>الطريقة</th>
                  <th>TX</th>
                  <th>إثبات</th>
                  <th>إجراء</th>
                </tr>
              </thead>
              <tbody id="dep_body"></tbody>
            </table>
          </div>
        </div>

        <div class="card hide" id="panel_products">
          <div class="h">المنتجات</div>
          <div class="row" style="margin-bottom:10px">
            <input id="prod_q" placeholder="بحث باسم المنتج..." />
            <button class="btn primary" id="prod_reload">تحديث</button>
          </div>
          <div id="prod_msg"></div>
          <div style="overflow:auto">
            <table>
              <thead>
                <tr>
                  <th>المعرف</th>
                  <th>الاسم</th>
                  <th>USD</th>
                  <th>SYP</th>
                  <th>متاح</th>
                  <th>حفظ</th>
                </tr>
              </thead>
              <tbody id="prod_body"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <script>
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      function el(id) { return document.getElementById(id); }
      function setMsg(targetId, kind, text) {
        const box = el(targetId);
        if (!box) return;
        if (!text) { box.innerHTML = ''; return; }
        box.innerHTML = `<div class="${kind === 'ok' ? 'okbox' : 'err'}">${escapeHtml(text)}</div>`;
      }
      function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
      }
      async function jget(url) {
        const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return await r.json();
      }
      async function jsend(url, method, body) {
        const r = await fetch(url, {
          method,
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
          body: body ? JSON.stringify(body) : undefined,
        });
        const txt = await r.text();
        let json = null;
        try { json = txt ? JSON.parse(txt) : null; } catch { json = null; }
        if (!r.ok) {
          throw new Error(json?.message || `HTTP ${r.status}`);
        }
        return json;
      }

      function wireTabs() {
        document.querySelectorAll('.tab').forEach((b) => {
          b.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach((x) => x.classList.remove('active'));
            b.classList.add('active');
            const t = b.getAttribute('data-tab');
            el('panel_users').classList.toggle('hide', t !== 'users');
            el('panel_deposits').classList.toggle('hide', t !== 'deposits');
            el('panel_products').classList.toggle('hide', t !== 'products');
          });
        });
      }

      async function loadStats() {
        try {
          setMsg('stats_msg', 'ok', '');
          const s = await jget('/admin/ui/stats');
          el('k_users').textContent = s.users;
          el('k_orders').textContent = s.orders;
          el('k_products').textContent = s.products;
          el('k_pending').textContent = s.pending_deposits;
          el('k_dep_usd').textContent = Number(s.approved_deposits_total?.USD || 0).toFixed(2);
          el('k_dep_syp').textContent = Number(s.approved_deposits_total?.SYP || 0).toFixed(0);
        } catch (e) {
          setMsg('stats_msg', 'err', e?.message || 'Failed to load stats');
        }
      }

      async function loadUsers() {
        try {
          setMsg('users_msg', 'ok', '');
          const q = el('users_q').value.trim();
          const data = await jget('/admin/ui/users' + (q ? `?q=${encodeURIComponent(q)}` : ''));
          const body = el('users_body');
          body.innerHTML = '';
          for (const u of (data.data || [])) {
            const usd = Number(u.balance?.USD || 0).toFixed(2);
            const syp = Number(u.balance?.SYP || 0).toFixed(0);
            const banned = !!u.is_banned;
            body.innerHTML += `
              <tr>
                <td>${escapeHtml(u.telegram_id)}</td>
                <td>${escapeHtml(u.username || '')}</td>
                <td><span class="pill">${escapeHtml(u.role || '')}</span></td>
                <td>${usd}</td>
                <td>${syp}</td>
                <td>${banned ? '<span class="pill bad">banned</span>' : '<span class="pill ok">ok</span>'}</td>
              </tr>
            `;
          }
          if ((data.data || []).length === 0) {
            body.innerHTML = `<tr><td colspan="6" class="muted">لا يوجد نتائج.</td></tr>`;
          }
        } catch (e) {
          setMsg('users_msg', 'err', e?.message || 'Failed to load users');
        }
      }

      async function loadPendingDeposits() {
        try {
          setMsg('dep_msg', 'ok', '');
          const data = await jget('/admin/ui/deposits/pending');
          const body = el('dep_body');
          body.innerHTML = '';
          for (const d of (data.data || [])) {
            const usd = Number(d.amount?.USD || 0).toFixed(2);
            const syp = Number(d.amount?.SYP || 0).toFixed(0);
            const proof = d.proof_image ? `<a class="pill" href="${escapeHtml(d.proof_image)}" target="_blank" rel="noreferrer">عرض</a>` : '—';
            const user = d.user ? (d.user.username ? '@'+d.user.username : String(d.user.telegram_id || '')) : d.user_id;
            body.innerHTML += `
              <tr>
                <td>${escapeHtml(d._id)}</td>
                <td>${escapeHtml(user)}</td>
                <td>${usd}</td>
                <td>${syp}</td>
                <td>${escapeHtml(d.method || '')}</td>
                <td>${escapeHtml(d.transaction_id || '')}</td>
                <td>${proof}</td>
                <td>
                  <div class="actions">
                    <button class="btn primary" data-act="approve" data-id="${escapeHtml(d._id)}">قبول</button>
                    <button class="btn" data-act="reject" data-id="${escapeHtml(d._id)}">رفض</button>
                  </div>
                </td>
              </tr>
            `;
          }
          if ((data.data || []).length === 0) {
            body.innerHTML = `<tr><td colspan="8" class="muted">لا توجد إيداعات معلّقة.</td></tr>`;
          }
        } catch (e) {
          setMsg('dep_msg', 'err', e?.message || 'Failed to load deposits');
        }
      }

      async function loadProducts() {
        try {
          setMsg('prod_msg', 'ok', '');
          const q = el('prod_q').value.trim();
          const data = await jget('/admin/ui/products' + (q ? `?q=${encodeURIComponent(q)}` : ''));
          const body = el('prod_body');
          body.innerHTML = '';
          for (const p of (data.data || [])) {
            const usd = Number(p.price?.USD || 0).toFixed(2);
            const syp = Number(p.price?.SYP || 0).toFixed(0);
            body.innerHTML += `
              <tr>
                <td>${escapeHtml(p._id)}</td>
                <td><input data-pid="${escapeHtml(p._id)}" data-field="name" value="${escapeHtml(p.name || '')}" /></td>
                <td><input data-pid="${escapeHtml(p._id)}" data-field="usd" value="${usd}" /></td>
                <td><input data-pid="${escapeHtml(p._id)}" data-field="syp" value="${syp}" /></td>
                <td>
                  <select data-pid="${escapeHtml(p._id)}" data-field="available" class="btn" style="padding:8px 10px">
                    <option value="1" ${p.available ? 'selected' : ''}>نعم</option>
                    <option value="0" ${!p.available ? 'selected' : ''}>لا</option>
                  </select>
                </td>
                <td>
                  <button class="btn primary" data-act="save-product" data-id="${escapeHtml(p._id)}">حفظ</button>
                </td>
              </tr>
            `;
          }
          if ((data.data || []).length === 0) {
            body.innerHTML = `<tr><td colspan="6" class="muted">لا توجد منتجات.</td></tr>`;
          }
        } catch (e) {
          setMsg('prod_msg', 'err', e?.message || 'Failed to load products');
        }
      }

      document.addEventListener('click', async (e) => {
        const t = e.target;
        if (!(t instanceof HTMLElement)) return;
        const act = t.getAttribute('data-act');
        const id = t.getAttribute('data-id');
        if (!act || !id) return;

        try {
          if (act === 'approve' || act === 'reject') {
            t.setAttribute('disabled', 'disabled');
            await jsend(`/admin/ui/deposits/${encodeURIComponent(id)}/${act}`, 'POST');
            await loadPendingDeposits();
            await loadStats();
          }
          if (act === 'save-product') {
            t.setAttribute('disabled', 'disabled');
            const pid = id;
            const name = document.querySelector(`input[data-pid="${CSS.escape(pid)}"][data-field="name"]`)?.value || '';
            const usd = Number(document.querySelector(`input[data-pid="${CSS.escape(pid)}"][data-field="usd"]`)?.value || 0);
            const syp = Number(document.querySelector(`input[data-pid="${CSS.escape(pid)}"][data-field="syp"]`)?.value || 0);
            const available = (document.querySelector(`select[data-pid="${CSS.escape(pid)}"][data-field="available"]`)?.value || '1') === '1';
            await jsend(`/admin/ui/products/${encodeURIComponent(pid)}`, 'PUT', { name, available, price: { USD: usd, SYP: syp } });
            setMsg('prod_msg', 'ok', 'تم حفظ المنتج.');
            await loadStats();
          }
        } catch (err) {
          if (act === 'save-product') setMsg('prod_msg', 'err', err?.message || 'Failed');
          else setMsg('dep_msg', 'err', err?.message || 'Failed');
        } finally {
          t.removeAttribute('disabled');
        }
      });

      el('users_reload').addEventListener('click', () => void loadUsers());
      el('dep_reload').addEventListener('click', () => void loadPendingDeposits());
      el('prod_reload').addEventListener('click', () => void loadProducts());

      wireTabs();
      loadStats();
      loadUsers();
      loadPendingDeposits();
      loadProducts();
    </script>
  </body>
</html>

