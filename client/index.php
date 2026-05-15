<?php
// BASE_URL sudah diset oleh front controller root. Fallback jika dipanggil langsung:
if (!defined('BASE_URL')) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
            || (strpos($host, '.ngrok-free.dev') !== false)
            || (strpos($host, '.ngrok.io') !== false)
            || (strpos($host, '.ngrok-free.app') !== false);
    $protocol = $isHttps ? 'https' : 'http';
    $folder   = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    define('BASE_URL', $protocol . '://' . $host . $folder);
}

// Routing sederhana
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $route = trim($_GET['page'], '/');
} else {
    $basepath = dirname($_SERVER['SCRIPT_NAME']);
    $uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri      = substr($uri, strlen($basepath));
    $route    = trim($uri, '/');
}
if ($route === '') $route = 'dashboard';

$parts    = explode('/', $route);
$page     = basename($parts[0]);
$param_id = $parts[1] ?? null;
if ($param_id !== null) $_GET['id'] = htmlspecialchars($param_id);
$_GET['page'] = $page;

// Auth logic (cookie-based)
require_once __DIR__ . '/includes/auth_logic.php';
require_once __DIR__ . '/includes/components.php';

$allowed = ['dashboard', 'lowongan', 'form_lowongan', 'pelamar', 'pelamar_detail', 'login'];
if (!in_array($page, $allowed, true)) $page = 'dashboard';

// Auth guard: belum login → paksa ke login
if (!$is_logged_in && $page !== 'login') {
    header('Location: ' . BASE_URL . '/client/login');
    exit;
}
// Sudah login tapi buka login → redirect dashboard
if ($is_logged_in && $page === 'login') {
    header('Location: ' . BASE_URL . '/client/dashboard');
    exit;
}

// Set page title
$titles = [
    'dashboard'      => ['Dashboard',       'Ringkasan aktivitas rekrutmen'],
    'lowongan'       => ['Kelola Lowongan', 'Daftar semua lowongan yang tersedia'],
    'form_lowongan'  => ['Form Lowongan',   'Buat atau edit lowongan pekerjaan'],
    'pelamar'        => ['Kelola Pelamar',  'Semua kandidat yang masuk'],
    'pelamar_detail' => ['Detail Pelamar',  'Informasi lengkap pelamar'],
    'login'          => ['Login Admin',     ''],
];
[$pageTitle, $pageSubtitle] = $titles[$page] ?? ['Admin', ''];

// Font Awesome: lokal jika sudah didownload, fallback CDN
$faLocalCss = __DIR__ . '/../assets/fontawesome/css/all.min.css';
$faLocalExists = file_exists($faLocalCss) && filesize($faLocalCss) > 5000;
$faCssUrl = $faLocalExists
    ? BASE_URL . '/assets/fontawesome/css/all.min.css'
    : 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rekrutmen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif'] },
            colors: { brand: { 50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' } },
            animation: { 'fade-in-up': 'fadeInUp 0.4s ease-out' },
            keyframes: { fadeInUp: { '0%': { opacity:0, transform:'translateY(12px)' }, '100%': { opacity:1, transform:'translateY(0)' } } }
        }}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $faCssUrl ?>">
    <style>
        body { font-family:'Plus Jakarta Sans', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg,#6366f1 0%,#8b5cf6 50%,#ec4899 100%); }
        .gradient-text  { background: linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899); -webkit-background-clip:text; background-clip:text; color:transparent; }
        ::-webkit-scrollbar { width:8px; height:8px; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }
        ::-webkit-scrollbar-thumb:hover { background:#94a3b8; }
        .label { display:block; font-size:0.8125rem; font-weight:600; color:#334155; margin-bottom:0.375rem; }
        .input { width:100%; padding:0.625rem 0.875rem; border-radius:0.625rem; background:#f8fafc; border:1px solid #e2e8f0; font-size:0.875rem; transition:all .2s; }
        .input:focus { outline:none; background:#fff; border-color:#6366f1; box-shadow:0 0 0 3px #e0e7ff; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">


<?php if ($page === 'login'): ?>
<!-- ========== LOGIN PAGE (JS FETCH, seperti monbis) ========== -->
<div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 gradient-brand"></div>
    <div class="absolute top-10 right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 left-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

    <div class="relative z-10 w-full max-w-md">
        <div class="text-center mb-6">
            <a href="<?= BASE_URL ?>" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm font-semibold">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke situs utama
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 animate-fade-in-up">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-2xl gradient-brand flex items-center justify-center text-white text-2xl shadow-lg shadow-indigo-500/30 mb-4">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">Admin Panel</h1>
                <p class="text-sm text-slate-500 mt-1">Masuk dengan akun SSO BKK Jateng</p>
            </div>

            <!-- Error box -->
            <div id="errBox" class="hidden mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span id="errMsg"></span>
            </div>

            <form id="formLogin" class="space-y-4">
                <div>
                    <label class="label">ID Pegawai</label>
                    <div class="relative">
                        <i class="fa-solid fa-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="id_peg" required autofocus class="input pl-10" placeholder="102-119">
                    </div>
                </div>
                <div>
                    <label class="label">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="password" type="password" required class="input pl-10 pr-10" placeholder="••••••••">
                        <button type="button" onclick="const i=document.getElementById('password');i.type=i.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button id="btnLogin" type="submit" class="w-full py-3 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <svg id="spin" class="hidden animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    <span id="btnText">Masuk</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <p class="text-[11px] text-slate-400 text-center mt-6">
                Login menggunakan akun SSO BKK Jateng
            </p>
        </div>
    </div>
</div>

<script>
// ===== SSO LOGIN LOGIC (seperti monbis) =====
const BASE_APP = '<?= BASE_URL ?>/client';
const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const isBkkDomain = window.location.hostname.endsWith('.bkkjateng.co.id');
const API_SSO_BASE = isLocal ? 'http://localhost/rest_api_sso' : 'https://apisso.bkkjateng.co.id';
const API_LOGIN  = `${API_SSO_BASE}/api/auth/login`;
const API_WHOAMI = `${API_SSO_BASE}/api/auth/whoami`;

// Fallback: local API login (DB admin table + SSO proxy)
const API_LOCAL_LOGIN = '<?= BASE_URL ?>/api/auth/login';
function setSSOCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    // Hanya set domain cookie kalau di *.bkkjateng.co.id
    const isBkkDomain = window.location.hostname.endsWith('.bkkjateng.co.id');
    const domainStr = isBkkDomain ? "domain=.bkkjateng.co.id;" : "";
    const secureStr = window.location.protocol === 'https:' ? " Secure;" : "";
    document.cookie = name + "=" + (value || "") + expires + "; path=/; " + domainStr + secureStr + " SameSite=Lax";
}

const saveToken = (t) => {
    localStorage.setItem('rekrutmen_token', t);
    setSSOCookie('sso_token', t, 1);
};
const saveUser = (u) => localStorage.setItem('rekrutmen_user', JSON.stringify(u));

// Login form handler
document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn     = document.getElementById('btnLogin');
    const spin    = document.getElementById('spin');
    const btnText = document.getElementById('btnText');
    const errBox  = document.getElementById('errBox');
    const errMsg  = document.getElementById('errMsg');

    errBox.classList.add('hidden');
    btn.disabled = true;
    spin.classList.remove('hidden');
    btnText.textContent = 'Memeriksa...';

    const empId = document.getElementById('id_peg').value.trim();
    const pass  = document.getElementById('password').value;

    let loginSuccess = false;

    // Tentukan endpoint: langsung SSO hanya kalau di localhost atau domain bkkjateng
    // Via ngrok/domain lain → pakai local API (yang proxy ke SSO di backend)
    const useDirectSSO = isLocal || isBkkDomain;

    // === STEP 1: Coba SSO dulu (hanya jika direct access) ===
    if (useDirectSSO) {
        try {
            const res = await fetch(API_LOGIN, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_peg: empId, password: pass, app: "sipatuh" })
            });

            if (res.ok) {
                const json = await res.json();
                if (json?.status === 200 && json?.data?.token) {
                    saveToken(json.data.token);
                    try {
                        const r2 = await fetch(API_WHOAMI, {
                            headers: { 'Authorization': `Bearer ${json.data.token}` }
                        });
                        if (r2.ok) {
                            const j2 = await r2.json();
                            if (j2?.data) {
                                let userData = j2.data;
                                const unitLower = (userData.unit_kerja || userData.job_position || '').toLowerCase();
                                if (unitLower === 'divisi operasional' || unitLower === 'divisi sdm dan umum') {
                                    userData.role = 'superadmin';
                                } else {
                                    userData.role = 'admin';
                                }
                                saveUser(userData);
                            }
                        }
                    } catch (err) { console.error("Error whoami:", err); }
                    loginSuccess = true;
                }
            }
        } catch (ssoErr) {
            console.warn("SSO login gagal, coba fallback lokal...", ssoErr.message);
        }
    }

    // === STEP 2: Fallback ke local API (DB admin) ===
    if (!loginSuccess) {
        try {
            const res2 = await fetch(API_LOCAL_LOGIN, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: empId, password: pass })
            });

            const json2 = await res2.json();

            if (json2?.status === 200 && json2?.data?.token) {
                // Local login berhasil!
                saveToken(json2.data.token);
                if (json2.data.user) {
                    saveUser(json2.data.user);
                }
                loginSuccess = true;
            } else {
                throw new Error(json2?.message || 'ID Pegawai atau Password salah.');
            }
        } catch (localErr) {
            errMsg.textContent = localErr.message.includes("Failed to fetch")
                ? "Gagal terhubung ke server. Pastikan API berjalan."
                : localErr.message;
            errBox.classList.remove('hidden');
            btn.disabled = false;
            spin.classList.add('hidden');
            btnText.textContent = 'Masuk';
            return;
        }
    }

    // === STEP 3: Redirect ke dashboard ===
    if (loginSuccess) {
        // Simpan token ke PHP session juga (fallback jika cookie belum ke-set)
        try {
            await fetch('<?= BASE_URL ?>/api/session/set-token', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    token: localStorage.getItem('rekrutmen_token'),
                    user: JSON.parse(localStorage.getItem('rekrutmen_user') || 'null')
                })
            });
        } catch(e) { console.warn('Session save failed:', e); }

        location.href = `${BASE_APP}/dashboard`;
    }
});
</script>
<?php else: ?>

<!-- ========== ADMIN SHELL ========== -->
<div class="flex min-h-screen">
    <?php renderSidebar($page, $admin_user); ?>

    <div id="main-content" class="flex-1 flex flex-col min-w-0 lg:ml-[260px] transition-all duration-300">
        <?php renderTopbar($pageTitle, $pageSubtitle); ?>

        <script>
// ===== GLOBAL CONFIG =====
const BASE_APP = '<?= BASE_URL ?>/client';
const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const API_SSO_BASE = isLocal ? 'http://localhost/rest_api_sso' : 'https://apisso.bkkjateng.co.id';
const API_WHOAMI = `${API_SSO_BASE}/api/auth/whoami`;

// ===== USER DATA DARI LOCALSTORAGE =====
window.getUser = function() {
    if (window.__USER) return window.__USER;
    try {
        const raw = localStorage.getItem('rekrutmen_user');
        if (raw) return JSON.parse(raw);
    } catch(e) {}
    return null;
};

// ===== GLOBAL ADMIN HELPER =====
window.ADMIN = {
    token: (function() {
        const match = document.cookie.match(/(^| )sso_token=([^;]+)/);
        if (match) return match[2];
        return localStorage.getItem('rekrutmen_token') || '';
    })(),
    baseUrl: '<?= BASE_URL ?>',
    apiUrl:  '<?= BASE_URL ?>/api',

    async api(path, options = {}) {
        const opts = { headers: { 'Authorization': 'Bearer ' + this.token }, ...options };
        if (options.headers) opts.headers = { ...opts.headers, ...options.headers };
        if (!(opts.body instanceof FormData) && opts.body) {
            opts.headers['Content-Type'] = 'application/json';
        }
        try {
            const res  = await fetch(this.apiUrl + path, opts);
            const data = await res.json().catch(() => ({}));
            if (data.status === 401) {
                this.toast('Sesi habis, silakan login lagi', 'error');
                setTimeout(() => logoutSSO(), 1000);
            }
            return data;
        } catch (e) {
            return { status: 0, message: 'Tidak dapat terhubung ke server' };
        }
    },

    formatRupiah(n) { if (n === null || n === undefined || n === '') return '-'; return 'Rp ' + Number(n).toLocaleString('id-ID'); },
    formatGaji(min, max) { if (!min && !max) return 'Negotiable'; if (min && !max) return 'Mulai ' + this.formatRupiah(min); if (!min && max) return 'Hingga ' + this.formatRupiah(max); return this.formatRupiah(min) + ' – ' + this.formatRupiah(max); },
    formatTipe(t) { return { full_time:'Full Time', part_time:'Part Time', kontrak:'Kontrak', magang:'Magang', freelance:'Freelance' }[t] || t; },
    formatLevel(l) { return { fresh_graduate:'Fresh Graduate', junior:'Junior', middle:'Middle', senior:'Senior', lead:'Lead', manager:'Manager' }[l] || l; },
    formatDate(s) { if (!s) return '-'; const d = new Date(s); if (isNaN(d)) return s; return d.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }); },
    formatDateTime(s) { if (!s) return '-'; const d = new Date(s.replace(' ', 'T')); if (isNaN(d)) return s; return d.toLocaleString('id-ID', { day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' }); },

    statusBadge(s) {
        const map = { pending:['bg-amber-100 text-amber-700','fa-clock','Pending'], review:['bg-blue-100 text-blue-700','fa-eye','Review'], tes_administrasi:['bg-cyan-100 text-cyan-700','fa-clipboard-check','Tes Admin'], tes_tertulis:['bg-indigo-100 text-indigo-700','fa-pen-to-square','Tes Tulis'], interview:['bg-purple-100 text-purple-700','fa-handshake','Interview'], diterima:['bg-emerald-100 text-emerald-700','fa-circle-check','Diterima'], ditolak:['bg-rose-100 text-rose-700','fa-circle-xmark','Ditolak'] };
        const [cls, icon, label] = map[s] || ['bg-slate-100 text-slate-700','fa-circle', s];
        return `<span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[11px] font-bold ${cls}"><i class="fa-solid ${icon}"></i>${label}</span>`;
    },
    lowonganStatusBadge(s) { const map = { aktif:'bg-emerald-100 text-emerald-700', nonaktif:'bg-slate-100 text-slate-700', closed:'bg-rose-100 text-rose-700' }; return `<span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-bold uppercase ${map[s] || 'bg-slate-100 text-slate-700'}">${s}</span>`; },

    toast(msg, type='info') {
        const colors = { success:'bg-emerald-500', error:'bg-rose-500', info:'bg-slate-800', warning:'bg-amber-500' };
        const el = document.createElement('div');
        el.className = `fixed top-6 right-6 z-[9999] ${colors[type]||colors.info} text-white px-5 py-3 rounded-xl shadow-2xl font-semibold text-sm flex items-center gap-2 animate-fade-in-up`;
        el.innerHTML = `<i class="fa-solid fa-${type==='success'?'circle-check':type==='error'?'circle-exclamation':'circle-info'}"></i><span>${msg}</span>`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity='0'; el.style.transform='translateY(-10px)'; }, 2800);
        setTimeout(() => el.remove(), 3200);
    },

    confirm(msg, opts = {}) {
        return new Promise(resolve => {
            const title = opts.title || 'Konfirmasi', okText = opts.okText || 'Ya, Lanjutkan';
            const okColor = opts.danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-brand-600 hover:bg-brand-700';
            const html = `<div class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in-up"><div class="bg-white rounded-2xl max-w-sm w-full p-6"><div class="w-14 h-14 rounded-full ${opts.danger?'bg-rose-100 text-rose-600':'bg-brand-100 text-brand-600'} flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid ${opts.danger?'fa-triangle-exclamation':'fa-circle-question'}"></i></div><h3 class="text-center font-bold text-slate-900 text-lg">${title}</h3><p class="text-center text-sm text-slate-600 mt-1 mb-5">${msg}</p><div class="flex gap-2"><button data-cancel class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-sm">Batal</button><button data-ok class="flex-1 py-2.5 rounded-xl ${okColor} text-white font-bold text-sm">${okText}</button></div></div></div>`;
            const wrap = document.createElement('div'); wrap.innerHTML = html;
            document.body.appendChild(wrap.firstElementChild);
            const modal = document.body.lastElementChild;
            modal.querySelector('[data-cancel]').onclick = () => { modal.remove(); resolve(false); };
            modal.querySelector('[data-ok]').onclick = () => { modal.remove(); resolve(true); };
        });
    },

    promptCode(msg, opts = {}) {
        return new Promise(resolve => {
            const title = opts.title || 'Konfirmasi', okText = opts.okText || 'Ya, Lanjutkan', placeholder = opts.placeholder || 'Masukkan kode...';
            const okColor = opts.danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-brand-600 hover:bg-brand-700';
            const html = `<div class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in-up"><div class="bg-white rounded-2xl max-w-sm w-full p-6"><div class="w-14 h-14 rounded-full ${opts.danger?'bg-rose-100 text-rose-600':'bg-brand-100 text-brand-600'} flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid ${opts.danger?'fa-triangle-exclamation':'fa-circle-question'}"></i></div><h3 class="text-center font-bold text-slate-900 text-lg">${title}</h3><p class="text-center text-sm text-slate-600 mt-1 mb-3">${msg}</p><input data-code type="text" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 mb-4" placeholder="${placeholder}" autocomplete="off"><div class="flex gap-2"><button data-cancel class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-sm">Batal</button><button data-ok class="flex-1 py-2.5 rounded-xl ${okColor} text-white font-bold text-sm">${okText}</button></div></div></div>`;
            const wrap = document.createElement('div'); wrap.innerHTML = html;
            document.body.appendChild(wrap.firstElementChild);
            const modal = document.body.lastElementChild;
            const input = modal.querySelector('[data-code]'); input.focus();
            modal.querySelector('[data-cancel]').onclick = () => { modal.remove(); resolve(null); };
            modal.querySelector('[data-ok]').onclick = () => { const v = input.value; modal.remove(); resolve(v); };
            input.addEventListener('keydown', e => { if(e.key==='Enter') { const v = input.value; modal.remove(); resolve(v); } });
        });
    }
};

// ===== LOGOUT SSO =====
function logoutSSO(e) {
    if(e) e.preventDefault();
    localStorage.removeItem('rekrutmen_token');
    localStorage.removeItem('rekrutmen_user');
    document.cookie = "sso_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    const isBkkDomain = window.location.hostname.endsWith('.bkkjateng.co.id');
    if(isBkkDomain) { document.cookie = "sso_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.bkkjateng.co.id;"; }
    window.location.href = BASE_APP + '/login';
}

// ===== WHOAMI SYNC (auto-fetch user data) =====
(async () => {
    const token = ADMIN.token;
    if (!token) return;

    const rawUser = localStorage.getItem('rekrutmen_user');
    let user = rawUser ? JSON.parse(rawUser) : null;

    // Paint user info di sidebar
    function paintUser(u) {
        if (!u) return;
        window.__USER = u;
        const nameEl = document.getElementById('sidebarUserName');
        const unitEl = document.getElementById('sidebarUserUnit');
        if (nameEl) nameEl.textContent = u.full_name || u.nama || u.nama_lengkap || '-';
        if (unitEl) unitEl.textContent = u.unit_kerja || u.job_position || u.role || '-';
    }

    if (user) paintUser(user);

    try {
        const res = await fetch(API_WHOAMI, {
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
        });
        if (res.ok) {
            const json = await res.json();
            if (json.status === 200 && json.data) {
                let u = json.data;
                const unitLower = (u.unit_kerja || u.job_position || '').toLowerCase();
                if (unitLower === 'divisi operasional' || unitLower === 'divisi sdm dan umum') {
                    u.role = 'superadmin';
                } else {
                    u.role = 'admin';
                }
                localStorage.setItem('rekrutmen_user', JSON.stringify(u));
                paintUser(u);
            }
        }
    } catch (err) {
        console.error("Gagal sync whoami:", err);
    }
})();

// ===== SIDEBAR TOGGLE =====
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const btnOpen = document.getElementById('toggle-sidebar');
const btnToggle = document.getElementById('sidebar-toggle-btn');
const toggleIcon = document.getElementById('toggle-icon');
const mainContent = document.getElementById('main-content');
const sidebarLabels = document.querySelectorAll('.sidebar-label');

let sidebarExpanded = localStorage.getItem('sidebar_expanded') !== 'false';

function setSidebarState(expanded) {
    sidebarExpanded = expanded;
    localStorage.setItem('sidebar_expanded', expanded);
    if (expanded) { sidebar.style.width='260px'; mainContent.style.marginLeft='260px'; toggleIcon.className='fa-solid fa-chevron-left text-xs'; sidebarLabels.forEach(el=>{el.style.opacity='1';el.style.width='auto';el.style.overflow='visible';}); }
    else { sidebar.style.width='70px'; mainContent.style.marginLeft='70px'; toggleIcon.className='fa-solid fa-chevron-right text-xs'; sidebarLabels.forEach(el=>{el.style.opacity='0';el.style.width='0';el.style.overflow='hidden';}); }
}
if (window.innerWidth >= 1024) setSidebarState(sidebarExpanded);
btnToggle?.addEventListener('click', () => setSidebarState(!sidebarExpanded));
btnOpen?.addEventListener('click', () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); });
overlay?.addEventListener('click', () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); });
        </script>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto overflow-x-hidden">
            <?php
            $view_file = __DIR__ . "/includes/views/{$page}.php";
            if (file_exists($view_file)) {
                include $view_file;
            } else {
                echo '<div class="bg-white rounded-2xl p-10 text-center"><i class="fa-solid fa-face-frown text-5xl text-slate-300 mb-3"></i><p class="font-bold text-slate-800">Halaman tidak ditemukan</p></div>';
            }
            ?>
        </main>
    </div>
</div>
<?php endif; ?>

</body>
</html>
