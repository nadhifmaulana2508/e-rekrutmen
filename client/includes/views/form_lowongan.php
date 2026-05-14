<?php
$editId = 0;
if (!empty($_GET['id']) && ctype_digit((string)$_GET['id'])) $editId = (int)$_GET['id'];

// Master data posisi & penempatan (picklist)
$masterPosisi = [
    'AO Dana','AO Kredit','AO Remedial','Analis Kredit & Appraisal',
    'Akuntansi & Pelaporan','Customer Service','Teller',
    'Staf Manajemen Risiko','Staf Kepatuhan','Staf Strategi Anti Fraud',
    'Staf Perlindungan Konsumen','Staf Integritas Pelaporan Keuangan',
    'Staf APU-PPT','Staf Digital Marketing','Staf IT (Development/Security)',
    'Staf Litbang','Staf Penyelesaian Kredit','Staf AMU dan Litigasi','Staf Diklat',
];

$masterPenempatan = [
    'Cabang Utama (Kota Semarang)','Rembang','Pati','Demak','Kendal',
    'Kota Salatiga','Kab. Semarang','Wonogiri','Kota Surakarta','Karanganyar',
    'Sukoharjo','Sragen','Boyolali','Magelang','Wonosobo','Purworejo',
    'Kebumen','Banjarnegara','Purbalingga','Banyumas','Cilacap',
    'Kab. Tegal','Brebes','Kota Tegal','Pemalang','Kota Pekalongan',
    'Kab. Pekalongan','Batang',
];
?>
<div class="max-w-4xl">
    <form id="form-lowongan" class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-pink-50">
            <h2 class="text-xl font-extrabold text-slate-900" id="form-title"><?= $editId ? 'Edit Lowongan' : 'Buat Lowongan Baru' ?></h2>
            <p class="text-sm text-slate-500">Pilih posisi & penempatan yang akan dibuka untuk batch rekrutmen ini.</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Judul -->
            <div>
                <label class="label">Judul / Nama Batch Rekrutmen <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" required class="input" placeholder="Contoh: Rekrutmen Pegawai BPR BKK Jateng Tahun 2026">
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="label">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="input" placeholder="Deskripsi singkat tentang batch rekrutmen ini..."></textarea>
            </div>

            <!-- Persyaratan -->
            <div>
                <label class="label">Persyaratan Umum <span class="text-xs font-normal text-slate-400">(satu baris per item)</span></label>
                <textarea name="persyaratan" rows="5" class="input" placeholder="Warga Negara Indonesia&#10;Usia maksimal 27 tahun&#10;Pendidikan minimal D3/S1&#10;IPK minimal 2.75"></textarea>
            </div>

            <!-- POSISI (checkbox picklist + custom add) -->
            <div>
                <label class="label">Posisi yang Dibuka <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(centang yang ingin dibuka)</span></label>
                <div id="posisi-container" class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <?php foreach ($masterPosisi as $p): ?>
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                            <input type="checkbox" name="posisi_tersedia[]" value="<?= htmlspecialchars($p) ?>" class="accent-brand-600 chk-posisi">
                            <span><?= htmlspecialchars($p) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="flex gap-2 mt-2">
                    <input type="text" id="inp-custom-posisi" class="input flex-1" placeholder="Tambah posisi baru (misal: Pejabat Cabang)...">
                    <button type="button" id="btn-add-posisi" class="px-4 py-2 rounded-xl bg-brand-50 text-brand-700 font-bold text-sm hover:bg-brand-100 whitespace-nowrap">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-1"><span id="count-posisi" class="font-bold text-brand-600">0</span> posisi dipilih</p>
            </div>

            <!-- PENEMPATAN (checkbox picklist) -->
            <div>
                <label class="label">Lokasi Penempatan <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(centang cabang yang tersedia)</span></label>
                <div class="flex gap-2 mb-2">
                    <button type="button" id="btn-sel-all-loc" class="text-xs px-3 py-1 rounded-lg bg-brand-50 text-brand-700 font-bold hover:bg-brand-100">Pilih Semua</button>
                    <button type="button" id="btn-desel-all-loc" class="text-xs px-3 py-1 rounded-lg bg-slate-100 text-slate-600 font-bold hover:bg-slate-200">Hapus Semua</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <?php foreach ($masterPenempatan as $loc): ?>
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                            <input type="checkbox" name="penempatan_tersedia[]" value="<?= htmlspecialchars($loc) ?>" class="accent-brand-600 chk-penempatan">
                            <span><?= htmlspecialchars($loc) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-slate-400 mt-1"><span id="count-penempatan" class="font-bold text-emerald-600">0</span> lokasi dipilih</p>
            </div>

            <!-- Deadline & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Deadline Pelamaran</label>
                    <input type="date" name="deadline" class="input">
                </div>
                <div>
                    <label class="label">Status</label>
                    <select name="status" class="input">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row gap-2 justify-end">
            <a href="<?= BASE_URL ?>/client/lowongan" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 font-bold text-sm text-slate-700 text-center">Batal</a>
            <button id="submit-btn" type="submit" class="px-5 py-2.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-md">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<script>
(async function () {
    const editId = <?= $editId ?>;
    const form   = document.getElementById('form-lowongan');
    const title  = document.getElementById('form-title');

    // Counter update function
    function updatePosisiCount() {
        document.getElementById('count-posisi').textContent = document.querySelectorAll('.chk-posisi:checked').length;
    }
    function updatePenempatanCount() {
        document.getElementById('count-penempatan').textContent = document.querySelectorAll('.chk-penempatan:checked').length;
    }

    const countP = document.getElementById('count-posisi');
    const countL = document.getElementById('count-penempatan');
    document.querySelectorAll('.chk-posisi').forEach(c => c.addEventListener('change', updatePosisiCount));
    document.querySelectorAll('.chk-penempatan').forEach(c => c.addEventListener('change', updatePenempatanCount));

    // Add custom posisi
    document.getElementById('btn-add-posisi').addEventListener('click', () => {
        const inp = document.getElementById('inp-custom-posisi');
        const val = inp.value.trim();
        if (!val) return;
        // Check if already exists
        const existing = [...document.querySelectorAll('.chk-posisi')].map(c => c.value.toLowerCase());
        if (existing.includes(val.toLowerCase())) { ADMIN.toast('Posisi sudah ada', 'warning'); return; }
        // Add new checkbox
        const container = document.getElementById('posisi-container');
        const lbl = document.createElement('label');
        lbl.className = 'flex items-center gap-2 text-sm cursor-pointer hover:bg-white p-1.5 rounded-lg transition';
        lbl.innerHTML = `<input type="checkbox" name="posisi_tersedia[]" value="${val}" class="accent-brand-600 chk-posisi" checked><span>${val}</span>`;
        container.appendChild(lbl);
        lbl.querySelector('input').addEventListener('change', updatePosisiCount);
        updatePosisiCount();
        inp.value = '';
        ADMIN.toast('Posisi "'+val+'" ditambahkan', 'success');
    });
    document.getElementById('inp-custom-posisi').addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('btn-add-posisi').click(); }
    });

    // Select/deselect all penempatan
    document.getElementById('btn-sel-all-loc').addEventListener('click', () => {
        document.querySelectorAll('.chk-penempatan').forEach(c => c.checked = true);
        updatePenempatanCount();
    });
    document.getElementById('btn-desel-all-loc').addEventListener('click', () => {
        document.querySelectorAll('.chk-penempatan').forEach(c => c.checked = false);
        updatePenempatanCount();
    });

    // Edit mode: populate
    if (editId) {
        title.textContent = 'Edit Lowongan #' + editId;
        const res = await ADMIN.api('/lowongan/' + editId);
        if (res.status === 200) {
            const d = res.data;
            form.elements.judul.value       = d.judul || '';
            form.elements.deskripsi.value   = d.deskripsi || '';
            form.elements.persyaratan.value = d.persyaratan || '';
            form.elements.deadline.value    = d.deadline || '';
            form.elements.status.value      = d.status || 'aktif';

            // Check posisi
            (d.posisi_tersedia || []).forEach(p => {
                const chk = form.querySelector(`input[name="posisi_tersedia[]"][value="${p}"]`);
                if (chk) chk.checked = true;
            });
            countP.textContent = document.querySelectorAll('.chk-posisi:checked').length;

            // Check penempatan
            (d.penempatan_tersedia || []).forEach(p => {
                const chk = form.querySelector(`input[name="penempatan_tersedia[]"][value="${p}"]`);
                if (chk) chk.checked = true;
            });
            countL.textContent = document.querySelectorAll('.chk-penempatan:checked').length;
        } else {
            ADMIN.toast(res.message || 'Lowongan tidak ditemukan', 'error');
            setTimeout(() => location.href = ADMIN.baseUrl + '/client/lowongan', 1200);
            return;
        }
    }

    // Submit
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');

        // Collect checked values
        const posisi = [...document.querySelectorAll('.chk-posisi:checked')].map(c => c.value);
        const penempatan = [...document.querySelectorAll('.chk-penempatan:checked')].map(c => c.value);

        if (posisi.length === 0) { ADMIN.toast('Pilih minimal 1 posisi', 'warning'); return; }
        if (penempatan.length === 0) { ADMIN.toast('Pilih minimal 1 lokasi penempatan', 'warning'); return; }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Menyimpan...';

        const payload = {
            judul:                form.elements.judul.value,
            deskripsi:            form.elements.deskripsi.value,
            persyaratan:          form.elements.persyaratan.value,
            posisi_tersedia:      posisi,
            penempatan_tersedia:  penempatan,
            deadline:             form.elements.deadline.value,
            status:               form.elements.status.value,
        };

        const method = editId ? 'PUT' : 'POST';
        const path   = editId ? '/lowongan/' + editId : '/lowongan';
        const res = await ADMIN.api(path, { method, body: JSON.stringify(payload) });

        if (res.status === 200 || res.status === 201) {
            ADMIN.toast(res.message || 'Tersimpan', 'success');
            setTimeout(() => location.href = ADMIN.baseUrl + '/client/lowongan', 700);
        } else {
            ADMIN.toast(res.message || 'Gagal menyimpan', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1"></i> Simpan';
        }
    });
})();
</script>
