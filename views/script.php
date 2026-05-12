<script>
// ==========================================
// Global helper & API client
// ==========================================
window.APP = {
    baseUrl: '<?= BASE_URL ?>',
    apiUrl:  '<?= BASE_URL ?>/api',

    formatRupiah(n) {
        if (n === null || n === undefined || n === '') return '-';
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    },

    formatGajiRange(min, max) {
        if (!min && !max) return 'Negotiable';
        if (min && !max)  return 'Mulai ' + this.formatRupiah(min);
        if (!min && max)  return 'Hingga ' + this.formatRupiah(max);
        return this.formatRupiah(min) + ' – ' + this.formatRupiah(max);
    },

    formatTipeKerja(t) {
        const map = {
            full_time: 'Full Time', part_time: 'Part Time',
            kontrak:   'Kontrak',   magang:    'Magang', freelance: 'Freelance',
        };
        return map[t] || t;
    },

    formatLevel(l) {
        const map = {
            fresh_graduate: 'Fresh Graduate', junior: 'Junior', middle: 'Middle',
            senior: 'Senior', lead: 'Lead', manager: 'Manager',
        };
        return map[l] || l;
    },

    formatDate(s) {
        if (!s) return '-';
        const d = new Date(s);
        if (isNaN(d)) return s;
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    },

    statusBadge(status) {
        const map = {
            pending:   ['bg-amber-100 text-amber-700',   'fa-clock',         'Menunggu Review'],
            review:    ['bg-blue-100 text-blue-700',     'fa-file-magnifying-glass', 'Sedang Direview'],
            interview: ['bg-purple-100 text-purple-700', 'fa-handshake',     'Interview'],
            diterima:  ['bg-emerald-100 text-emerald-700','fa-circle-check','Diterima'],
            ditolak:   ['bg-rose-100 text-rose-700',     'fa-circle-xmark',  'Tidak Diterima'],
        };
        const [cls, icon, label] = map[status] || ['bg-slate-100 text-slate-700', 'fa-circle', status];
        return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${cls}">
                    <i class="fa-solid ${icon}"></i>${label}
                </span>`;
    },

    async api(path, options = {}) {
        const opts = { headers: {}, ...options };
        if (!(opts.body instanceof FormData)) {
            opts.headers['Content-Type'] = opts.headers['Content-Type'] || 'application/json';
        }
        try {
            const res  = await fetch(this.apiUrl + path, opts);
            const data = await res.json().catch(() => ({}));
            return data;
        } catch (e) {
            return { status: 0, message: 'Tidak dapat terhubung ke server', data: null };
        }
    },

    toast(message, type = 'info') {
        const colors = {
            success: 'bg-emerald-500',
            error:   'bg-rose-500',
            info:    'bg-slate-800',
            warning: 'bg-amber-500',
        };
        const el = document.createElement('div');
        el.className = `fixed top-6 right-6 z-[9999] ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-2xl font-semibold text-sm flex items-center gap-2 animate-fade-in-up`;
        el.innerHTML = `<i class="fa-solid fa-${type==='success'?'circle-check':type==='error'?'circle-exclamation':'circle-info'}"></i><span>${message}</span>`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateY(-10px)'; }, 3000);
        setTimeout(() => el.remove(), 3400);
    }
};
</script>
