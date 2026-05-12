<?php
// Support edit mode via ?edit=ID atau segmen /form_lowongan/ID
$editId = 0;
if (!empty($_GET['id']) && ctype_digit((string)$_GET['id'])) $editId = (int)$_GET['id'];
?>
<div class="max-w-4xl">
    <form id="form-lowongan" class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-pink-50">
            <h2 class="text-xl font-extrabold text-slate-900" id="form-title"><?= $editId ? 'Edit Lowongan' : 'Buat Lowongan Baru' ?></h2>
            <p class="text-sm text-slate-500">Isi detail lowongan dengan akurat agar kandidat tertarik.</p>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="label">Judul Posisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required class="input" placeholder="Contoh: Frontend Developer">
                </div>
                <div>
                    <label class="label">Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="divisi" required class="input" placeholder="Teknologi / HR / Marketing">
                </div>
                <div>
                    <label class="label">Lokasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi" required class="input" placeholder="Jakarta / Remote">
                </div>
                <div>
                    <label class="label">Tipe Kerja</label>
                    <select name="tipe_kerja" class="input">
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="kontrak">Kontrak</option>
                        <option value="magang">Magang</option>
                        <option value="freelance">Freelance</option>
                    </select>
                </div>
                <div>
                    <label class="label">Level</label>
                    <select name="level" class="input">
                        <option value="fresh_graduate">Fresh Graduate</option>
                        <option value="junior" selected>Junior</option>
                        <option value="middle">Middle</option>
                        <option value="senior">Senior</option>
                        <option value="lead">Lead</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div>
                    <label class="label">Gaji Min (IDR)</label>
                    <input type="number" name="gaji_min" class="input" placeholder="8000000">
                </div>
                <div>
                    <label class="label">Gaji Max (IDR)</label>
                    <input type="number" name="gaji_max" class="input" placeholder="15000000">
                </div>
                <div>
                    <label class="label">Deadline</label>
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
                <div class="md:col-span-2">
                    <label class="label">Deskripsi Pekerjaan <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi" rows="4" required class="input" placeholder="Gambaran umum pekerjaan, tanggung jawab, dll."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Kualifikasi <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(satu baris per item)</span></label>
                    <textarea name="requirements" rows="5" required class="input" placeholder="Minimal S1 Teknik Informatika&#10;Menguasai PHP &amp; MySQL&#10;Pengalaman 2 tahun"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Benefit <span class="text-xs font-normal text-slate-400">(satu baris per item)</span></label>
                    <textarea name="benefits" rows="4" class="input" placeholder="BPJS Kesehatan&#10;Gaji kompetitif&#10;Remote friendly"></textarea>
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

    if (editId) {
        title.textContent = 'Edit Lowongan #' + editId;
        const res = await ADMIN.api('/lowongan/' + editId);
        if (res.status === 200) {
            const d = res.data;
            for (const [k, v] of Object.entries(d)) {
                const el = form.elements[k];
                if (!el) continue;
                el.value = v ?? '';
            }
        } else {
            ADMIN.toast(res.message || 'Lowongan tidak ditemukan', 'error');
            setTimeout(() => location.href = ADMIN.baseUrl + '/client/lowongan', 1200);
            return;
        }
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Menyimpan...';

        const fd = new FormData(form);
        const payload = Object.fromEntries(fd.entries());

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
