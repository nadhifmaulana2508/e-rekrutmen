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

<div id="loader" class="bg-white rounded-2xl p-10 animate-pulse"><div class="h-6 bg-slate-200 rounded w-1/2 mb-3"></div><div class="h-40 bg-slate-100 rounded"></div></div>
<div id="content" class="hidden"></div>

<script>
(async function () {
    const id  = <?= $id ?>;
    const res = await ADMIN.api('/pelamar/' + id);
    document.getElementById('loader').classList.add('hidden');
    const box = document.getElementById('content');
    box.classList.remove('hidden');

    if (res.status !== 200) {
        box.innerHTML = `<div class="bg-white rounded-2xl p-10 text-center"><i class="fa-solid fa-user-slash text-4xl text-slate-400 mb-2"></i><p class="font-bold">${res.message}</p></div>`;
        return;
    }

    const p = res.data;
    const initial = p.nama_lengkap.charAt(0).toUpperCase();
    const foto3x4Url = p.foto_3x4 ? `${ADMIN.baseUrl}/uploads/foto/${p.foto_3x4}` : '';

    function kv(label, val, wide) {
        return `<div class="${wide?'sm:col-span-2':''}"><p class="text-slate-500 font-medium text-xs uppercase">${label}</p><p class="font-semibold text-slate-800 mt-0.5">${val||'<span class="text-slate-400 italic">-</span>'}</p></div>`;
    }

    // Pengalaman
    const expHtml = (p.pengalaman||[]).length ? p.pengalaman.map((e,i)=>`
        <div class="p-3 bg-slate-50 rounded-xl mb-2">
            <p class="font-bold text-sm text-slate-900">${e.nama_perusahaan} <span class="font-normal text-slate-500">— ${e.jabatan||''}</span></p>
            <p class="text-xs text-slate-500">${e.periode_kerja||''} ${e.gaji_terakhir?'| Gaji: '+ADMIN.formatRupiah(e.gaji_terakhir):''}</p>
            ${e.deskripsi_pekerjaan?`<p class="text-xs text-slate-600 mt-1">${e.deskripsi_pekerjaan}</p>`:''}
            ${e.alasan_berhenti?`<p class="text-xs text-slate-400 mt-1">Alasan: ${e.alasan_berhenti}</p>`:''}
        </div>`).join('') : '<p class="text-sm text-slate-400 italic">Tidak ada pengalaman kerja</p>';

    // Dokumen
    const docHtml = (p.dokumen||[]).length ? p.dokumen.map(d=>`
        <a href="${ADMIN.baseUrl}/uploads/cv/${d.nama_file}" target="_blank" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition">
            <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-sm"><i class="fa-solid fa-file"></i></div>
            <div class="flex-1 min-w-0"><p class="text-sm font-bold text-slate-900 truncate">${d.jenis_dokumen.replace(/_/g,' ')}</p><p class="text-xs text-slate-500 truncate">${d.nama_asli||d.nama_file}</p></div>
            <i class="fa-solid fa-download text-slate-400"></i>
        </a>`).join('') : '<p class="text-sm text-slate-400 italic">Tidak ada dokumen</p>';

    box.innerHTML = `
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                <div class="h-20 gradient-brand"></div>
                <div class="p-6 -mt-10">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        ${foto3x4Url?`<img src="${foto3x4Url}" class="w-20 h-20 rounded-2xl border-4 border-white object-cover shadow-lg">`:`<div class="w-20 h-20 rounded-2xl border-4 border-white gradient-brand text-white flex items-center justify-center text-2xl font-black shadow-lg">${initial}</div>`}
                        <div class="flex-1">
                            <h2 class="text-xl font-extrabold text-slate-900">${p.nama_lengkap}</h2>
                            <p class="text-sm text-slate-500">${p.email} &bull; ${p.no_hp}</p>
                            <div class="mt-2 flex flex-wrap gap-2">${ADMIN.statusBadge(p.status_lamaran)}<code class="text-xs bg-slate-100 rounded px-2 py-1">${p.kode_tracking}</code></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Posisi -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3"><i class="fa-solid fa-briefcase text-brand-500 mr-2"></i>Posisi & Penempatan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    ${kv('Posisi Dilamar', p.posisi_dilamar)}
                    ${kv('Penempatan', p.penempatan)}
                    ${kv('Bersedia Seluruh Wilayah', p.bersedia_seluruh_wilayah)}
                    ${kv('Sumber Info', p.sumber_informasi)}
                    ${kv('Ekspektasi Gaji', p.ekspektasi_gaji ? ADMIN.formatRupiah(p.ekspektasi_gaji) : '-')}
                    ${kv('Mulai Bekerja', p.ketersediaan_mulai)}
                </dl>
            </div>

            <!-- Data Pribadi -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3"><i class="fa-solid fa-id-card text-brand-500 mr-2"></i>Data Pribadi</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    ${kv('Nama Lengkap', p.nama_lengkap)}
                    ${kv('Nama Panggilan', p.nama_panggilan)}
                    ${kv('Jenis Kelamin', p.jenis_kelamin)}
                    ${kv('Tempat/Tgl Lahir', (p.tempat_lahir||'')+', '+ADMIN.formatDate(p.tanggal_lahir))}
                    ${kv('Status Pernikahan', p.status_pernikahan)}
                    ${kv('Agama', p.agama)}
                    ${kv('No KTP', p.nomor_ktp)}
                    ${kv('NPWP', p.npwp)}
                    ${kv('Alamat KTP', p.alamat_ktp, true)}
                    ${kv('Alamat Domisili', p.alamat_domisili, true)}
                    ${kv('LinkedIn', p.akun_linkedin)}
                </dl>
            </div>

            <!-- Pendidikan -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3"><i class="fa-solid fa-graduation-cap text-brand-500 mr-2"></i>Pendidikan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    ${kv('Pendidikan', p.pendidikan_terakhir)}
                    ${kv('Institusi', p.nama_institusi)}
                    ${kv('Jurusan', p.jurusan)}
                    ${kv('Tahun Lulus', p.tahun_lulus)}
                    ${kv('IPK', p.ipk)}
                    ${kv('Prestasi', p.prestasi)}
                </dl>
            </div>

            <!-- Pengalaman -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3"><i class="fa-solid fa-building text-brand-500 mr-2"></i>Pengalaman Kerja</h3>
                ${expHtml}
            </div>

            <!-- Kemampuan -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3"><i class="fa-solid fa-laptop-code text-brand-500 mr-2"></i>Kemampuan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    ${kv('Komputer', (p.kemampuan_komputer||[]).join(', '))}
                    ${kv('Bahasa Asing', p.bahasa_asing)}
                    ${kv('Sertifikasi', p.sertifikasi)}
                    ${kv('Keahlian Khusus', p.keahlian_khusus)}
                    ${kv('SIM', (p.sim||[]).join(', '))}
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-5 lg:sticky lg:top-24">
            <!-- Update Status -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-1">Update Status</h3>
                <p class="text-xs text-slate-500 mb-4">Email notifikasi otomatis dikirim ke pelamar.</p>
                <form id="status-form" class="space-y-3">
                    <select name="status_lamaran" class="input" required>
                        <option value="pending" ${p.status_lamaran==='pending'?'selected':''}>Pending</option>
                        <option value="review" ${p.status_lamaran==='review'?'selected':''}>Review</option>
                        <option value="tes_administrasi" ${p.status_lamaran==='tes_administrasi'?'selected':''}>Tes Administrasi</option>
                        <option value="tes_tertulis" ${p.status_lamaran==='tes_tertulis'?'selected':''}>Tes Tertulis</option>
                        <option value="interview" ${p.status_lamaran==='interview'?'selected':''}>Interview</option>
                        <option value="diterima" ${p.status_lamaran==='diterima'?'selected':''}>Diterima</option>
                        <option value="ditolak" ${p.status_lamaran==='ditolak'?'selected':''}>Ditolak</option>
                    </select>
                    <textarea name="catatan_admin" rows="3" class="input" placeholder="Catatan (opsional)...">${p.catatan_admin||''}</textarea>
                    <button type="submit" class="w-full py-2.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-md"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan & Kirim Email</button>
                </form>
            </div>

            <!-- Dokumen -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-900 mb-3">Dokumen (${(p.dokumen||[]).length})</h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">${docHtml}</div>
            </div>

            <!-- Delete -->
            <button id="btn-delete" class="w-full py-2.5 rounded-xl bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-sm"><i class="fa-solid fa-trash mr-1"></i> Hapus Pelamar</button>
        </aside>
    </div>`;

    // Status form submit
    document.getElementById('status-form').addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const payload = Object.fromEntries(fd.entries());
        const r = await ADMIN.api('/pelamar/'+id+'/status', { method:'PUT', body:JSON.stringify(payload) });
        if (r.status === 200) ADMIN.toast(r.message || 'Status diperbarui', 'success');
        else ADMIN.toast(r.message || 'Gagal', 'error');
    });

    // Delete
    document.getElementById('btn-delete').addEventListener('click', async () => {
        const ok = await ADMIN.confirm('Hapus data pelamar <b>'+p.nama_lengkap+'</b>?', {danger:true, okText:'Hapus'});
        if (!ok) return;
        const r = await ADMIN.api('/pelamar/'+id, {method:'DELETE'});
        if (r.status===200) { ADMIN.toast('Dihapus','success'); setTimeout(()=>location.href=ADMIN.baseUrl+'/client/pelamar',700); }
        else ADMIN.toast(r.message||'Gagal','error');
    });
})();
</script>
