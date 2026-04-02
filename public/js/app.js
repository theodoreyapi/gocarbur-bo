/* ============================================================
   AutoPlatform Back-Office — JavaScript global
   ============================================================ */

// ── Sidebar toggle ────────────────────────────────────────────
function toggleSidebar() {
  document.querySelector('.sidebar').classList.toggle('open');
}

// ── Navigation active ─────────────────────────────────────────
function setActiveNav(page) {
  document.querySelectorAll('.nav-link-item').forEach(el => el.classList.remove('active'));
  const active = document.querySelector(`[data-page="${page}"]`);
  if (active) active.classList.add('active');
}

// ── Tabs ──────────────────────────────────────────────────────
document.addEventListener('click', e => {
  const btn = e.target.closest('.tab-btn');
  if (!btn) return;
  const group = btn.closest('[data-tabs]');
  if (!group) return;
  group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  group.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
  btn.classList.add('active');
  const target = group.querySelector(`#${btn.dataset.tab}`);
  if (target) target.classList.add('active');
});

// ── Dropdown ──────────────────────────────────────────────────
document.addEventListener('click', e => {
  if (e.target.closest('[data-toggle="dropdown"]')) {
    const parent = e.target.closest('.dropdown');
    const menu   = parent?.querySelector('.dropdown-menu');
    if (menu) {
      document.querySelectorAll('.dropdown-menu.show').forEach(m => { if (m !== menu) m.classList.remove('show'); });
      menu.classList.toggle('show');
    }
    return;
  }
  document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
});

// ── Modal ─────────────────────────────────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.add('show'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.remove('show'); document.body.style.overflow = ''; }
}
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('show');
    document.body.style.overflow = '';
  }
  if (e.target.closest('[data-modal-open]')) openModal(e.target.closest('[data-modal-open]').dataset.modalOpen);
  if (e.target.closest('[data-modal-close]')) closeModal(e.target.closest('[data-modal-close]').dataset.modalClose);
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.show').forEach(m => { m.classList.remove('show'); document.body.style.overflow = ''; });
  }
});

// ── Toast ─────────────────────────────────────────────────────
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span>${icons[type] || '📢'}</span><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100px)'; toast.style.transition = '.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// ── Confirm dialog ────────────────────────────────────────────
function confirmAction(message, callback) {
  if (confirm(message)) callback();
}

// ── Format number ─────────────────────────────────────────────
function formatNumber(n) {
  return new Intl.NumberFormat('fr-FR').format(n);
}
function formatCurrency(n) {
  return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}
function formatDate(d) {
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
}
function timeAgo(d) {
  const diff = (Date.now() - new Date(d)) / 1000;
  if (diff < 60) return 'À l\'instant';
  if (diff < 3600) return `il y a ${Math.floor(diff/60)} min`;
  if (diff < 86400) return `il y a ${Math.floor(diff/3600)}h`;
  return `il y a ${Math.floor(diff/86400)}j`;
}

// ── Table sort ────────────────────────────────────────────────
function sortTable(table, col, asc) {
  const tbody = table.querySelector('tbody');
  const rows  = Array.from(tbody.querySelectorAll('tr'));
  rows.sort((a, b) => {
    const av = a.cells[col]?.textContent.trim() ?? '';
    const bv = b.cells[col]?.textContent.trim() ?? '';
    return asc ? av.localeCompare(bv, 'fr', { numeric: true }) : bv.localeCompare(av, 'fr', { numeric: true });
  });
  rows.forEach(r => tbody.appendChild(r));
}

// ── Debounce ──────────────────────────────────────────────────
function debounce(fn, delay = 300) {
  let timer;
  return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
}

// ── API helper (fetch wrapper) ────────────────────────────────
const API_BASE = 'https://api.autoplatform.ci/api/v1';

async function apiCall(method, endpoint, data = null) {
  const token = localStorage.getItem('admin_token');
  const opts  = {
    method,
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
  };
  if (token)  opts.headers['Authorization'] = `Bearer ${token}`;
  if (data)   opts.body = JSON.stringify(data);

  try {
    const res  = await fetch(`${API_BASE}${endpoint}`, opts);
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Erreur serveur');
    return json;
  } catch (err) {
    showToast(err.message, 'error');
    throw err;
  }
}

const api = {
  get:    (ep)       => apiCall('GET', ep),
  post:   (ep, data) => apiCall('POST', ep, data),
  put:    (ep, data) => apiCall('PUT', ep, data),
  patch:  (ep, data) => apiCall('PATCH', ep, data),
  delete: (ep)       => apiCall('DELETE', ep),
};

// ── Charts helpers ────────────────────────────────────────────
const CHART_COLORS = {
  primary: '#FF6B35',
  success: '#10B981',
  info:    '#3B82F6',
  warning: '#F59E0B',
  danger:  '#EF4444',
  purple:  '#8B5CF6',
};
