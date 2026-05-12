<?php
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo '<div class="bg-white rounded-2xl p-10 text-center"><i class="fa-solid fa-triangle-exclamation text-4xl text-amber-500 mb-2"></i><p class="font-bold">ID pelamar tidak valid.</p></div>';
    return;
}
?>
<a href="<?= BASE_URL ?>/client/pelamar" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-600 font-semibold mb-5">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar pelamar
</a>

<div id="loader" class="bg-white rounded-2xl p-10 animate-pulse">
    <div class="h-6 bg-slate-200 rounded w-1/2 mb-3"></div>
    <div class="h-4 bg-slate-200 rounded w-1/3 mb-6"></div>
    <div class="h-40 bg-slate-100 rounded"></div>
</div>

<div id="content" class="hidden"></div>

<script>
(async function () {
    const id   = <?= $id ?>;
    const res  = await ADMIN.api('/pelamar/' + id);
    document.getElementById('loader').classList.add('hidden');
    const box  = document.getElementById('content');
    box.classList.remove('hidden');

    if (res.status !== 200) {
        box.innerHTML = `<div class="bg-white rounded-2xl p-10 text-center">
            <i class="fa-solid fa-user-slash text-4xl text-slate-400 mb-2"></i>
            <p class="font-bold">${res.message}</p>
        </div>`;
        return;
    }

    const p = res.data;
    const initial = p.nama_lengkap.charAt(0).toUpperCase();
    const fotoUrl = p.foto ? `${ADMIN.baseUrl}/uploads/foto/${p.foto}` : '';
    const cvUrl   = p.cv   ? `${ADMIN.baseUrl}/uploads/cv/${p.cv}`     : '';

    box.innerHTML = `
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">
        <div class="space-y-6">
            <!-- Profile card -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                <div class="h-24 gradient-brand"></div>
                <div class="p-6 -mt-12">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        ${fotoUrl
                            ? `<img src="${fotoUrl}" alt="" class="w-24 h-24 rounded-2xl border-4 border-white object-cover shadow-lg">`
                            : `<div class="w-24 h-24 rounded-2xl border-4 border-white bg-gradient-to-br from-indigo-500 to-pink-500 text-white flex items-center justify-center text-3xl font-black shadow-lg">${initial}</div>`}
                        <div class="flex-1">
                            <h2 class="text-2xl font-extrabold text-slate-900">${p.nama_lengkap}</h2>
                            <p class="text-slate-500 text-sm">${p.email} &bull; ${p.telepon}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                ${ADMIN.statusBadge(p.status_lamaran)}
                                <code class="text-xs bg-slate-100 rounded px-2 py-1 text-slate-700">${p.kode_tracking}</code>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><p class="text-slate-500 font-medium text-xs uppercase">Posisi Dilamar</p><p class="font-bold text-slate-900 mt-0.5">${p.judul_lowongan || '-'}</p></div>
                        <div><p class="text-slate-500 font-medium text-xs uppercase">Divisi</p><p class="font-bold text-slate-900 mt-0.5">${p.divisi || '-'}</p></div>
                        <div><p class="text-slate-500 font-medium text-xs uppercase">Tanggal Melamar</p><p class="font-bold text-slate-900 mt-0.5">${ADMIN.formatDateTime(p.created_at)}</p></div>
                        <div><p class="text-slate-500 font-medium text-xs uppercase">Update Terakhir</p><p class="font-bold text-slate-900 mt-0.5">${ADMIN.formatDateTime(p.updated_at)}</p></div>
                    </div>
                </div>
            </div>

            <!-- Data Pribadi -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-4"><i class="fa-solid fa-id-card text-brand-500 mr-2"></i>Data Pribadi</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    ${kv('Tempat Lahir', p.tempat_lahir)}
                    ${kv('Tanggal Lahir', ADMIN.formatDate(p.tanggal_lahir))}
                    ${kv('Jenis Kelamin', p.jenis_kelamin === 'L' ? 'Laki-laki' : p.jenis_kelamin === 'P' ? 'Perempuan' : '-')}
                    ${kv('Alamat', p.alamat, true)}
                </dl>
            </div>

            <!-- Pendidikan & Pengalaman -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-4"><i class="fa-solid fa-graduation-cap text-brand-500 mr-2"></i>Pendidikan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    ${kv('Pendidikan Terakhir', p.pendidikan_terakhir)}
                    ${kv('Institusi', p.nama_institusi)}
                    ${kv('Jurusan', p.jurusan)}
                    ${kv('Tahun Lulus', p.tahun_lulus)}
                    ${kv('IPK', p.ipk)}
                </dl>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3"><i class="fa-solid fa-briefcase text-brand-500 mr-2"></i>Pengalaman & Portfolio</h3>
                <p class="text-sm text-slate-700 whitespace-pre-line">${p.pengalaman_kerja || '<span class="text-slate-400 italic">Tidak diisi</span>'}</p>
                ${p.link_portfolio ? `<a href="${p.link_portfolio}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-sm text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-up-right-from-square"></i> ${p.link_portfolio}</a>` : ''}
            </div>
        </div>

        <!-- Sidebar actions -->
        <aside class="space-y-5 lg:sticky lg:top-24">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-1">Update Status</h3>
                <p class="text-xs text-slate-500 mb-4">Ubah status lamaran & beri catatan (opsional).</p>

                <form id="status-form" class="space-y-3">
                    <div>
                        <label class="label">Status Lamaran</label>
                        <select name="status_lamaran" class="input" required>
                            <option value="pending"   ${p.status_lamaran === 'pending'   ? 'selected' : ''}>Pending</option>
                            <option value="review"    ${p.status_lamaran === 'review'    ? 'selected' : ''}>Review</option>
                            <option value="interview" ${p.status_lamaran === 'interview' ? 'selected' : ''}>Interview</option>
                            <option value="diterima"  ${p.status_lamaran === 'diterima'  ? 'selected' : ''}>Diterima</option>
                            <option value="ditolak"   ${p.status_lamaran === 'ditolak'   ? 'selected' : ''}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Catatan Admin</label>
                        <textarea name="catatan_admin" rows="4" class="input" placeholder="Contoh: Jadwal interview hari Rabu, 10:00 via Zoom...">${p.catatan_admin || ''}</textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-md">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3">Dokumen</h3>
                <div class="space-y-2">
                    ${cvUrl
                        ? `<a href="${cvUrl}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition">
                                <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center"><i class="fa-solid fa-file-pdf"></i></div>
                                <div class="flex-1 min-w-0"><p class="text-sm font-bold text-slate-900 truncate">CV / Resume</p><p class="text-xs text-slate-500">Klik untuk buka PDF</p></div>
                                <i class="fa-solid fa-download text-slate-400"></i>
                           </a>`
                        : `<p class="text-sm text-slate-400 italic">CV tidak diupload</p>`}
                    ${fotoUrl ? `<a href="${fotoUrl}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-image"></i></div>
                                <div class="flex-1 min-w-0"><p class="text-sm font-bold text-slate-900 truncate">Foto Profil</p><p class="text-xs text-slate-500">Lihat foto</p></div>
                                <i class="fa-solid fa-up-right-from-square text-slate-400"></i>
                           </a>` : ''}
                </div>
            </div>

            <button id="btn-delete" class="w-full py-2.5 rounded-xl bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-sm">
                <i class="fa-solid fa-trash mr-1"></i> Hapus Data Pelamar
            </button>
        </aside>
    </div>`;

    function kv(label, val, wide=false) {
        return `<div class="${wide ? 'sm:col-span-2' : ''}">
                    <p class="text-slate-500 font-medium text-xs uppercase">${label}</p>
                    <p class="font-semibold text-slate-800 mt-0.5">${val || '<span class="text-slate-400 italic">-</span>'}</p>
                </div>`;
    }

    // Status form
    document.getElementById('status-form').addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const payload = Object.fromEntries(fd.entries());
        const r = await ADMIN.api(`/pelamar/${id}/status`, { method: 'PUT', body: JSON.stringify(payload) });
        if (r.status === 200) ADMIN.toast('Status diperbarui', 'success');
        else                  ADMIN.toast(r.message || 'Gagal update', 'error');
    });

    // Delete
    document.getElementById('btn-delete').addEventListener('click', async () => {
        const ok = await ADMIN.confirm(`Yakin ingin menghapus data <b>${p.nama_lengkap}</b>? CV dan foto akan ikut dihapus.`, { danger:true, okText:'Hapus Permanen' });
        if (!ok) return;
        const r = await ADMIN.api('/pelamar/' + id, { method: 'DELETE' });
        if (r.status === 200) {
            ADMIN.toast('Pelamar dihapus', 'success');
            setTimeout(() => location.href = ADMIN.baseUrl + '/client/pelamar', 700);
        } else {
            ADMIN.toast(r.message || 'Gagal menghapus', 'error');
        }
    });
})();
</script>
