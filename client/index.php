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

// Auth logic
require_once __DIR__ . '/includes/auth_logic.php';
require_once __DIR__ . '/includes/components.php';

$allowed = ['dashboard', 'lowongan', 'form_lowongan', 'pelamar', 'pelamar_detail', 'login'];
if (!in_array($page, $allowed, true)) $page = 'dashboard';

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rekrutmen</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif'] },
                    colors: {
                        brand: { 50:'#eef2ff',100:'#e0e7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.4s ease-out',
                    },
                    keyframes: {
                        fadeInUp: { '0%': { opacity: 0, transform: 'translateY(12px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6.5.1 - Lokal (fix icon kotak-kotak di iPhone/iOS Safari) -->
    <?php
    $faLocalCss = __DIR__ . '/../assets/fontawesome/css/all.min.css';
    $faLocalExists = file_exists($faLocalCss) && filesize($faLocalCss) > 5000;
    if ($faLocalExists): ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/fontawesome/css/all.min.css">
    <?php else: ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php endif; ?>
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

<?php if (!$is_logged_in): ?>
    <!-- LOGIN PAGE -->
    <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
        <div class="absolute inset-0 gradient-brand"></div>
        <div class="absolute top-10 right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="text-center mb-6">
                <a href="<?= BASE_URL ?>" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm font-semibold mb-6">
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

                <?php if ($error_login): ?>
                    <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($error_login) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="label">ID Pegawai</label>
                        <div class="relative">
                            <i class="fa-solid fa-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="id_peg" required autofocus class="input pl-10" placeholder="102-119">
                        </div>
                    </div>
                    <div>
                        <label class="label">Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-key absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="pass" type="password" name="password" required class="input pl-10 pr-10" placeholder="••••••••">
                            <button type="button" onclick="const i=document.getElementById('pass');i.type=i.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 hover:-translate-y-0.5 transition-all">
                        Masuk <i class="fa-solid fa-arrow-right ml-1"></i>
                    </button>
                </form>

                <p class="text-[11px] text-slate-400 text-center mt-6">
                    Login menggunakan akun SSO BKK Jateng
                </p>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ADMIN SHELL -->
    <div class="flex min-h-screen">
        <?php renderSidebar($page, $admin_user); ?>

        <div id="main-content" class="flex-1 flex flex-col min-w-0 lg:ml-[260px] transition-all duration-300">
            <?php renderTopbar($pageTitle, $pageSubtitle); ?>

            <script>
    // Global admin helper - HARUS sebelum views yang pakai ADMIN.api()
    window.ADMIN = {
        token:  '<?= htmlspecialchars($_SESSION['token'] ?? '') ?>',
        baseUrl:'<?= BASE_URL ?>',
        apiUrl: '<?= BASE_URL ?>/api',

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
                    setTimeout(() => location.href = this.baseUrl + '/client/dashboard?logout=1', 1000);
                }
                return data;
            } catch (e) {
                return { status: 0, message: 'Tidak dapat terhubung ke server' };
            }
        },

        formatRupiah(n) {
            if (n === null || n === undefined || n === '') return '-';
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        },

        formatGaji(min, max) {
            if (!min && !max) return 'Negotiable';
            if (min && !max)  return 'Mulai ' + this.formatRupiah(min);
            if (!min && max)  return 'Hingga ' + this.formatRupiah(max);
            return this.formatRupiah(min) + ' – ' + this.formatRupiah(max);
        },

        formatTipe(t) {
            return { full_time:'Full Time', part_time:'Part Time', kontrak:'Kontrak', magang:'Magang', freelance:'Freelance' }[t] || t;
        },

        formatLevel(l) {
            return { fresh_graduate:'Fresh Graduate', junior:'Junior', middle:'Middle', senior:'Senior', lead:'Lead', manager:'Manager' }[l] || l;
        },

        formatDate(s) {
            if (!s) return '-';
            const d = new Date(s);
            if (isNaN(d)) return s;
            return d.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
        },

        formatDateTime(s) {
            if (!s) return '-';
            const d = new Date(s.replace(' ', 'T'));
            if (isNaN(d)) return s;
            return d.toLocaleString('id-ID', { day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        },

        statusBadge(s) {
            const map = {
                pending:          ['bg-amber-100 text-amber-700',    'fa-clock',         'Pending'],
                review:           ['bg-blue-100 text-blue-700',      'fa-eye',           'Review'],
                tes_administrasi: ['bg-cyan-100 text-cyan-700',      'fa-clipboard-check','Tes Admin'],
                tes_tertulis:     ['bg-indigo-100 text-indigo-700',  'fa-pen-to-square', 'Tes Tulis'],
                interview:        ['bg-purple-100 text-purple-700',  'fa-handshake',     'Interview'],
                diterima:         ['bg-emerald-100 text-emerald-700','fa-circle-check',  'Diterima'],
                ditolak:          ['bg-rose-100 text-rose-700',      'fa-circle-xmark',  'Ditolak'],
            };
            const [cls, icon, label] = map[s] || ['bg-slate-100 text-slate-700','fa-circle', s];
            return `<span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[11px] font-bold ${cls}"><i class="fa-solid ${icon}"></i>${label}</span>`;
        },

        lowonganStatusBadge(s) {
            const map = {
                aktif:    'bg-emerald-100 text-emerald-700',
                nonaktif: 'bg-slate-100 text-slate-700',
                closed:   'bg-rose-100 text-rose-700',
            };
            return `<span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-bold uppercase ${map[s] || 'bg-slate-100 text-slate-700'}">${s}</span>`;
        },

        toast(msg, type='info') {
            const colors = { success:'bg-emerald-500', error:'bg-rose-500', info:'bg-slate-800', warning:'bg-amber-500' };
            const el = document.createElement('div');
            el.className = `fixed top-6 right-6 z-[9999] ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-2xl font-semibold text-sm flex items-center gap-2 animate-fade-in-up`;
            el.innerHTML = `<i class="fa-solid fa-${type==='success'?'circle-check':type==='error'?'circle-exclamation':'circle-info'}"></i><span>${msg}</span>`;
            document.body.appendChild(el);
            setTimeout(() => { el.style.opacity='0'; el.style.transform='translateY(-10px)'; }, 2800);
            setTimeout(() => el.remove(), 3200);
        },

        confirm(msg, opts = {}) {
            return new Promise(resolve => {
                const title   = opts.title   || 'Konfirmasi';
                const okText  = opts.okText  || 'Ya, Lanjutkan';
                const okColor = opts.danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-brand-600 hover:bg-brand-700';
                const html = `
                <div class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in-up">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6">
                        <div class="w-14 h-14 rounded-full ${opts.danger ? 'bg-rose-100 text-rose-600' : 'bg-brand-100 text-brand-600'} flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fa-solid ${opts.danger ? 'fa-triangle-exclamation' : 'fa-circle-question'}"></i>
                        </div>
                        <h3 class="text-center font-bold text-slate-900 text-lg">${title}</h3>
                        <p class="text-center text-sm text-slate-600 mt-1 mb-5">${msg}</p>
                        <div class="flex gap-2">
                            <button data-cancel class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-sm">Batal</button>
                            <button data-ok class="flex-1 py-2.5 rounded-xl ${okColor} text-white font-bold text-sm">${okText}</button>
                        </div>
                    </div>
                </div>`;
                const wrap = document.createElement('div');
                wrap.innerHTML = html;
                document.body.appendChild(wrap.firstElementChild);
                const modal = document.body.lastElementChild;
                modal.querySelector('[data-cancel]').onclick = () => { modal.remove(); resolve(false); };
                modal.querySelector('[data-ok]').onclick     = () => { modal.remove(); resolve(true); };
            });
        },

        // Prompt with input field (returns input value or null if cancelled)
        promptCode(msg, opts = {}) {
            return new Promise(resolve => {
                const title       = opts.title       || 'Konfirmasi';
                const okText      = opts.okText      || 'Ya, Lanjutkan';
                const placeholder = opts.placeholder || 'Masukkan kode...';
                const okColor     = opts.danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-brand-600 hover:bg-brand-700';
                const html = `
                <div class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in-up">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6">
                        <div class="w-14 h-14 rounded-full ${opts.danger ? 'bg-rose-100 text-rose-600' : 'bg-brand-100 text-brand-600'} flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fa-solid ${opts.danger ? 'fa-triangle-exclamation' : 'fa-circle-question'}"></i>
                        </div>
                        <h3 class="text-center font-bold text-slate-900 text-lg">${title}</h3>
                        <p class="text-center text-sm text-slate-600 mt-1 mb-3">${msg}</p>
                        <input data-code type="text" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 mb-4" placeholder="${placeholder}" autocomplete="off">
                        <div class="flex gap-2">
                            <button data-cancel class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-sm">Batal</button>
                            <button data-ok class="flex-1 py-2.5 rounded-xl ${okColor} text-white font-bold text-sm">${okText}</button>
                        </div>
                    </div>
                </div>`;
                const wrap = document.createElement('div');
                wrap.innerHTML = html;
                document.body.appendChild(wrap.firstElementChild);
                const modal = document.body.lastElementChild;
                const input = modal.querySelector('[data-code]');
                input.focus();
                modal.querySelector('[data-cancel]').onclick = () => { modal.remove(); resolve(null); };
                modal.querySelector('[data-ok]').onclick     = () => { const v = input.value; modal.remove(); resolve(v); };
                input.addEventListener('keydown', e => { if(e.key==='Enter') { const v = input.value; modal.remove(); resolve(v); } });
            });
        }
    };

    // Sidebar toggle (push layout)
    const sidebar      = document.getElementById('sidebar');
    const overlay      = document.getElementById('sidebar-overlay');
    const btnOpen      = document.getElementById('toggle-sidebar');
    const btnToggle    = document.getElementById('sidebar-toggle-btn');
    const toggleIcon   = document.getElementById('toggle-icon');
    const mainContent  = document.getElementById('main-content');
    const sidebarLabels = document.querySelectorAll('.sidebar-label');

    let sidebarExpanded = localStorage.getItem('sidebar_expanded') !== 'false';

    function setSidebarState(expanded) {
        sidebarExpanded = expanded;
        localStorage.setItem('sidebar_expanded', expanded);
        if (expanded) {
            sidebar.style.width = '260px';
            mainContent.style.marginLeft = '260px';
            toggleIcon.className = 'fa-solid fa-chevron-left text-xs';
            sidebarLabels.forEach(el => { el.style.opacity = '1'; el.style.width = 'auto'; el.style.overflow = 'visible'; });
        } else {
            sidebar.style.width = '70px';
            mainContent.style.marginLeft = '70px';
            toggleIcon.className = 'fa-solid fa-chevron-right text-xs';
            sidebarLabels.forEach(el => { el.style.opacity = '0'; el.style.width = '0'; el.style.overflow = 'hidden'; });
        }
    }

    // Initialize state on desktop
    if (window.innerWidth >= 1024) {
        setSidebarState(sidebarExpanded);
    }

    // Desktop toggle button
    btnToggle?.addEventListener('click', () => {
        setSidebarState(!sidebarExpanded);
    });

    // Mobile toggle
    btnOpen?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
    </script>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto overflow-x-hidden">
                <?php
                $view_file = __DIR__ . "/includes/views/{$page}.php";
                if (file_exists($view_file)) {
                    include $view_file;
                } else {
                    echo '<div class="bg-white rounded-2xl p-10 text-center">
                            <i class="fa-solid fa-face-frown text-5xl text-slate-300 mb-3"></i>
                            <p class="font-bold text-slate-800">Halaman tidak ditemukan</p>
                          </div>';
                }
                ?>
            </main>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
