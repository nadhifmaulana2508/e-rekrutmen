<!-- Toolbar -->
<div class="bg-white rounded-2xl border border-slate-100 p-4 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
    <div class="relative flex-1 max-w-md">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input id="search" type="text" placeholder="Cari judul lowongan..." class="input pl-10">
    </div>
    <div class="flex gap-2">
        <select id="filter-status" class="input w-auto">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
            <option value="closed">Closed</option>
        </select>
        <a href="<?= BASE_URL ?>/client/form_lowongan" class="px-4 py-2.5 rounded-xl gradient-brand text-white font-bold text-sm whitespace-nowrap shadow-md">
            <i class="fa-solid fa-plus mr-1"></i> Buat Baru
        </a>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3">Judul Lowongan</th>
                    <th class="px-4 py-3">Posisi Dibuka</th>
                    <th class="px-4 py-3">Penempatan</th>
                    <th class="px-4 py-3 text-center">Pelamar</th>
                    <th class="px-4 py-3">Deadline</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="rows">
                <tr><td colspan="7" class="py-12 text-center text-slate-400">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
(async function () {
    const rows     = document.getElementById('rows');
    const search   = document.getElementById('search');
    const filterSt = document.getElementById('filter-status');
    let all = [];

    async function load() {
        rows.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-slate-400">Memuat data...</td></tr>`;
        const res = await ADMIN.api('/lowongan?all=1');
        if (res.status !== 200) {
            rows.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-rose-500">${res.message || 'Gagal memuat data'}</td></tr>`;
            return;
        }
        all = res.data || [];
        render();
    }

    function render() {
        const q  = search.value.toLowerCase().trim();
        const st = filterSt.value;
        const list = all.filter(l => {
            if (st && l.status !== st) return false;
            if (q && !l.judul.toLowerCase().includes(q)) return false;
            return true;
        });

        if (list.length === 0) {
            rows.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-slate-400">
                <i class="fa-solid fa-briefcase text-3xl text-slate-300 block mb-2"></i>
                Tidak ada lowongan.
            </td></tr>`;
            return;
        }

        rows.innerHTML = list.map(l => {
            const posisi = (l.posisi_tersedia || []);
            const penempatan = (l.penempatan_tersedia || []);
            const posisiText = posisi.length > 3 ? posisi.slice(0,3).join(', ') + ` +${posisi.length-3}` : posisi.join(', ');
            const penempatanText = penempatan.length > 3 ? penempatan.slice(0,3).join(', ') + ` +${penempatan.length-3}` : penempatan.join(', ');
            return `
            <tr class="border-b border-slate-50 hover:bg-slate-50/60 transition">
                <td class="px-4 py-3">
                    <a href="${ADMIN.baseUrl}/client/form_lowongan/${l.id}" class="font-bold text-slate-900 hover:text-brand-600">${l.judul}</a>
                </td>
                <td class="px-4 py-3">
                    <p class="text-xs text-slate-600 max-w-[200px] truncate" title="${posisi.join(', ')}">${posisiText || '-'}</p>
                    <span class="text-[10px] text-brand-600 font-bold">${posisi.length} posisi</span>
                </td>
                <td class="px-4 py-3">
                    <p class="text-xs text-slate-600 max-w-[200px] truncate" title="${penempatan.join(', ')}">${penempatanText || '-'}</p>
                    <span class="text-[10px] text-emerald-600 font-bold">${penempatan.length} lokasi</span>
                </td>
                <td class="px-4 py-3 text-center font-bold text-slate-900">${l.jumlah_pelamar || 0}</td>
                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">${ADMIN.formatDate(l.deadline)}</td>
                <td class="px-4 py-3">${ADMIN.lowonganStatusBadge(l.status)}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="${ADMIN.baseUrl}/detail/${l.id}" target="_blank" class="inline-flex w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 items-center justify-center" title="Lihat publik"><i class="fa-solid fa-eye"></i></a>
                    <a href="${ADMIN.baseUrl}/client/form_lowongan/${l.id}" class="inline-flex w-9 h-9 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 items-center justify-center" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <button data-id="${l.id}" data-judul="${l.judul.replace(/"/g, '&quot;')}" class="btn-del inline-flex w-9 h-9 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 items-center justify-center" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
        }).join('');

        rows.querySelectorAll('.btn-del').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id    = btn.dataset.id;
                const judul = btn.dataset.judul;
                const ok = await ADMIN.confirm(`Hapus lowongan <b>${judul}</b>? Semua pelamar terkait juga akan dihapus.`, { danger: true, okText: 'Hapus' });
                if (!ok) return;
                const res = await ADMIN.api('/lowongan/' + id, { method: 'DELETE' });
                if (res.status === 200) { ADMIN.toast('Lowongan dihapus', 'success'); load(); }
                else                    { ADMIN.toast(res.message || 'Gagal menghapus', 'error'); }
            });
        });
    }

    [search, filterSt].forEach(el => el.addEventListener('input', render));
    load();
})();
</script>
