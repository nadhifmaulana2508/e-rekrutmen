<?php
$filter_lowongan = isset($_GET['lowongan']) ? (int)$_GET['lowongan'] : 0;
?>
<!-- Toolbar -->
<div class="bg-white rounded-2xl border border-slate-100 p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div class="relative md:col-span-2">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input id="f-q" type="text" placeholder="Cari nama / email / kode..." class="input pl-10">
    </div>
    <select id="f-status" class="input">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="review">Review</option>
        <option value="interview">Interview</option>
        <option value="diterima">Diterima</option>
        <option value="ditolak">Ditolak</option>
    </select>
    <select id="f-lowongan" class="input">
        <option value="">Semua Lowongan</option>
    </select>
</div>

<!-- Summary -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-5">
    <?php
    $summaries = [
        ['pending',   'Pending',   'bg-amber-100 text-amber-700'],
        ['review',    'Review',    'bg-blue-100 text-blue-700'],
        ['interview', 'Interview', 'bg-purple-100 text-purple-700'],
        ['diterima',  'Diterima',  'bg-emerald-100 text-emerald-700'],
        ['ditolak',   'Ditolak',   'bg-rose-100 text-rose-700'],
    ];
    foreach ($summaries as [$key, $label, $cls]): ?>
        <div class="bg-white rounded-xl border border-slate-100 p-4">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider"><?= $label ?></p>
            <p class="text-2xl font-extrabold mt-1 stat-<?= $key ?>">0</p>
            <span class="mt-2 inline-flex text-[10px] font-bold px-2 py-0.5 rounded-full <?= $cls ?>"><?= $label ?></span>
        </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3">Pelamar</th>
                    <th class="px-4 py-3">Posisi</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="rows">
                <tr><td colspan="6" class="py-12 text-center text-slate-400">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
(async function () {
    const rows  = document.getElementById('rows');
    const q     = document.getElementById('f-q');
    const st    = document.getElementById('f-status');
    const lsel  = document.getElementById('f-lowongan');
    let all = [];

    // Populate lowongan dropdown
    (async () => {
        const res = await ADMIN.api('/lowongan?all=1');
        if (res.status === 200) {
            lsel.insertAdjacentHTML('beforeend',
                res.data.map(l => `<option value="${l.id}">${l.judul}</option>`).join('')
            );
            const pre = <?= $filter_lowongan ?: 0 ?>;
            if (pre) lsel.value = pre;
            render();
        }
    })();

    async function load() {
        rows.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-slate-400">Memuat data...</td></tr>`;
        const res = await ADMIN.api('/pelamar');
        if (res.status !== 200) {
            rows.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-rose-500">${res.message}</td></tr>`;
            return;
        }
        all = res.data || [];
        updateSummary();
        render();
    }

    function updateSummary() {
        const counts = { pending:0, review:0, interview:0, diterima:0, ditolak:0 };
        all.forEach(p => { counts[p.status_lamaran] = (counts[p.status_lamaran] || 0) + 1; });
        for (const k of Object.keys(counts)) {
            const el = document.querySelector('.stat-' + k);
            if (el) el.textContent = counts[k];
        }
    }

    function render() {
        const qv = q.value.toLowerCase().trim();
        const stv = st.value;
        const lid = lsel.value;

        const list = all.filter(p => {
            if (stv && p.status_lamaran !== stv) return false;
            if (lid && String(p.id_lowongan) !== String(lid)) return false;
            if (qv) {
                const t = (p.nama_lengkap + ' ' + p.email + ' ' + p.kode_tracking).toLowerCase();
                if (!t.includes(qv)) return false;
            }
            return true;
        });

        if (list.length === 0) {
            rows.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-slate-400">
                <i class="fa-solid fa-user-slash text-3xl text-slate-300 block mb-2"></i>
                Tidak ada pelamar.
            </td></tr>`;
            return;
        }

        rows.innerHTML = list.map(p => {
            const initial = p.nama_lengkap.charAt(0).toUpperCase();
            return `
            <tr class="border-b border-slate-50 hover:bg-slate-50/60 transition">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full gradient-brand text-white flex items-center justify-center font-bold text-sm">${initial}</div>
                        <div class="min-w-0">
                            <a href="${ADMIN.baseUrl}/client/pelamar_detail/${p.id}" class="font-bold text-slate-900 hover:text-brand-600 truncate block">${p.nama_lengkap}</a>
                            <p class="text-xs text-slate-500 truncate max-w-[240px]">${p.email}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <p class="font-semibold text-slate-800">${p.judul_lowongan || '-'}</p>
                    <p class="text-xs text-slate-500">${p.divisi || ''}</p>
                </td>
                <td class="px-4 py-3"><code class="text-xs bg-slate-100 rounded px-2 py-1 text-slate-700">${p.kode_tracking}</code></td>
                <td class="px-4 py-3">${ADMIN.statusBadge(p.status_lamaran)}</td>
                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">${ADMIN.formatDateTime(p.created_at)}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="${ADMIN.baseUrl}/client/pelamar_detail/${p.id}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold">
                        <i class="fa-solid fa-eye"></i> Detail
                    </a>
                </td>
            </tr>`;
        }).join('');
    }

    [q, st, lsel].forEach(el => el.addEventListener('input', render));
    load();
})();
</script>
