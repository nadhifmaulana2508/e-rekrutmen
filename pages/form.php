<?php
$id = (int)($_GET['id'] ?? 0);
?>
<section class="py-10 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <a href="<?= BASE_URL ?>/detail/<?= $id ?>" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-600 font-semibold mb-6 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke detail lowongan
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">

            <!-- FORM -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 gradient-brand text-white">
                    <h1 class="text-2xl sm:text-3xl font-extrabold">Formulir Pelamaran</h1>
                    <p class="text-white/80 mt-1 text-sm">Lengkapi data di bawah dengan akurat. Proses hanya memakan 5 menit.</p>
                </div>

                <form id="apply-form" class="p-6 sm:p-8 space-y-8">
                    <input type="hidden" name="id_lowongan" value="<?= $id ?>">

                    <!-- SECTION 1 -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">1</div>
                            <h2 class="font-bold text-slate-900">Data Pribadi</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="label">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_lengkap" required class="input">
                            </div>
                            <div>
                                <label class="label">Email <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" required class="input">
                            </div>
                            <div>
                                <label class="label">No. Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                                <input type="tel" name="telepon" required class="input" placeholder="08xxxx">
                            </div>
                            <div>
                                <label class="label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="input">
                            </div>
                            <div>
                                <label class="label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="input">
                            </div>
                            <div>
                                <label class="label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="input">
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Link Portfolio / LinkedIn</label>
                                <input type="url" name="link_portfolio" class="input" placeholder="https://...">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="label">Alamat Domisili</label>
                                <textarea name="alamat" rows="2" class="input"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2 -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">2</div>
                            <h2 class="font-bold text-slate-900">Pendidikan</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="input">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach (['SMA','SMK','D3','D4','S1','S2','S3'] as $p): ?>
                                        <option value="<?= $p ?>"><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="label">Nama Institusi</label>
                                <input type="text" name="nama_institusi" class="input">
                            </div>
                            <div>
                                <label class="label">Jurusan</label>
                                <input type="text" name="jurusan" class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Tahun Lulus</label>
                                    <input type="number" name="tahun_lulus" min="1990" max="2030" class="input" placeholder="2024">
                                </div>
                                <div>
                                    <label class="label">IPK</label>
                                    <input type="number" step="0.01" max="4" min="0" name="ipk" class="input" placeholder="3.50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3 -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">3</div>
                            <h2 class="font-bold text-slate-900">Pengalaman & Dokumen</h2>
                        </div>

                        <div class="mb-4">
                            <label class="label">Ringkasan Pengalaman Kerja</label>
                            <textarea name="pengalaman_kerja" rows="4" class="input" placeholder="Contoh: 2022-2024 Frontend Developer di PT ABC, Tech Lead di proyek XYZ..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- FOTO -->
                            <div>
                                <label class="label">Foto Profil (maks 2MB, jpg/png)</label>
                                <label for="foto-input" class="file-dropzone group">
                                    <img id="foto-preview" src="" alt="" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl">
                                    <div id="foto-placeholder" class="flex flex-col items-center gap-1 text-slate-500 relative z-10">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-brand-500"></i>
                                        <span class="text-sm font-semibold">Upload Foto</span>
                                        <span class="text-[11px]">JPG / PNG, max 2MB</span>
                                    </div>
                                </label>
                                <input id="foto-input" type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="hidden">
                            </div>
                            <!-- CV -->
                            <div>
                                <label class="label">CV (PDF, maks 3MB) <span class="text-rose-500">*</span></label>
                                <label for="cv-input" class="file-dropzone group">
                                    <div id="cv-placeholder" class="flex flex-col items-center gap-1 text-slate-500 relative z-10">
                                        <i class="fa-solid fa-file-pdf text-2xl text-rose-500"></i>
                                        <span class="text-sm font-semibold">Upload CV</span>
                                        <span class="text-[11px]">PDF saja, max 3MB</span>
                                    </div>
                                    <div id="cv-filled" class="hidden flex-col items-center gap-1 text-emerald-600 relative z-10">
                                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                                        <span id="cv-filename" class="text-sm font-semibold text-slate-800 line-clamp-1 max-w-full px-2"></span>
                                        <span class="text-[11px] text-emerald-600">Klik untuk ganti file</span>
                                    </div>
                                </label>
                                <input id="cv-input" type="file" name="cv" accept="application/pdf" class="hidden" required>
                            </div>
                        </div>
                    </div>

                    <!-- PRIVACY -->
                    <label class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 cursor-pointer">
                        <input type="checkbox" id="agree" class="mt-1 w-4 h-4 accent-brand-600">
                        <span class="text-sm text-amber-800">
                            Saya menyatakan data yang diisi adalah benar & menyetujui data saya diproses untuk keperluan seleksi rekrutmen.
                        </span>
                    </label>

                    <button id="submit-btn" type="submit" disabled class="w-full py-4 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed hover:-translate-y-0.5 transition">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Lamaran
                    </button>
                </form>
            </div>

            <!-- SIDEBAR LOWONGAN INFO -->
            <aside class="space-y-5 lg:sticky lg:top-24">
                <div id="job-card" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                    <div class="animate-pulse space-y-3">
                        <div class="h-5 bg-slate-200 rounded w-3/4"></div>
                        <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                        <div class="h-20 bg-slate-100 rounded"></div>
                    </div>
                </div>
                <div class="bg-brand-50 border border-brand-100 rounded-3xl p-5">
                    <p class="text-xs font-bold text-brand-800 uppercase tracking-wider mb-2"><i class="fa-solid fa-lightbulb mr-1"></i> Tips</p>
                    <ul class="text-sm text-brand-900 space-y-2 pl-1">
                        <li class="flex gap-2"><i class="fa-solid fa-check mt-1 text-xs"></i>Gunakan email aktif yang sering dicek.</li>
                        <li class="flex gap-2"><i class="fa-solid fa-check mt-1 text-xs"></i>CV dalam format PDF, bukan foto.</li>
                        <li class="flex gap-2"><i class="fa-solid fa-check mt-1 text-xs"></i>Foto profesional (background polos).</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- MODAL SUCCESS -->
<div id="success-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur z-[9998] hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 text-center animate-fade-in-up">
        <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-5 text-4xl">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Lamaran Terkirim!</h2>
        <p class="text-slate-600 text-sm mb-4">Simpan kode tracking ini untuk cek status lamaran kamu kapan saja.</p>
        <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl p-4 mb-6">
            <p class="text-xs text-slate-500 font-semibold mb-1">KODE TRACKING</p>
            <p id="kode-tracking" class="text-xl font-black gradient-text tracking-wider"></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button id="copy-kode" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition"><i class="fa-regular fa-copy mr-1"></i> Salin</button>
            <a id="to-status" href="<?= BASE_URL ?>/status" class="flex-1 py-3 rounded-xl gradient-brand text-white font-bold">Cek Status</a>
        </div>
    </div>
</div>

<style>
    .label { display:block; font-size:0.8125rem; font-weight:600; color:#334155; margin-bottom:0.375rem; }
    .input { width:100%; padding:0.75rem 1rem; border-radius:0.75rem; background:#f8fafc; border:1px solid #e2e8f0; font-size:0.875rem; transition:all .2s; }
    .input:focus { outline:none; background:#fff; border-color:#6366f1; box-shadow:0 0 0 3px #e0e7ff; }
    .file-dropzone { position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center; min-height:140px; border:2px dashed #cbd5e1; border-radius:0.75rem; background:#f8fafc; cursor:pointer; transition:all .2s; }
    .file-dropzone:hover { border-color:#6366f1; background:#eef2ff; }
</style>

<script>
(async function () {
    const id = <?= $id ?>;
    if (!id) { location.href = APP.baseUrl + '/lowongan'; return; }

    // Load job info
    (async () => {
        const res = await APP.api('/lowongan/' + id);
        const box = document.getElementById('job-card');
        if (res.status === 200) {
            const j = res.data;
            box.innerHTML = `
                <p class="text-[11px] font-bold text-brand-600 uppercase tracking-wide">${j.divisi}</p>
                <h3 class="font-extrabold text-slate-900 text-lg leading-tight">${j.judul}</h3>
                <div class="mt-3 flex flex-wrap gap-1.5">
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100">${j.lokasi}</span>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">${APP.formatTipeKerja(j.tipe_kerja)}</span>
                </div>
                <div class="mt-4 p-3 bg-slate-50 rounded-xl">
                    <p class="text-[11px] text-slate-500 font-bold uppercase">Gaji</p>
                    <p class="text-base font-extrabold gradient-text">${APP.formatGajiRange(j.gaji_min, j.gaji_max)}</p>
                </div>`;
        } else {
            box.innerHTML = `<p class="text-rose-600 text-sm">${res.message}</p>`;
        }
    })();

    // File previews
    const fotoInput = document.getElementById('foto-input');
    const fotoPrev  = document.getElementById('foto-preview');
    const fotoPH    = document.getElementById('foto-placeholder');
    fotoInput.addEventListener('change', e => {
        const f = e.target.files[0];
        if (!f) return;
        if (f.size > 2 * 1024 * 1024) { APP.toast('Foto maks 2MB', 'error'); fotoInput.value=''; return; }
        const url = URL.createObjectURL(f);
        fotoPrev.src = url;
        fotoPrev.classList.remove('hidden');
        fotoPH.classList.add('hidden');
    });

    const cvInput  = document.getElementById('cv-input');
    const cvPH     = document.getElementById('cv-placeholder');
    const cvFilled = document.getElementById('cv-filled');
    const cvName   = document.getElementById('cv-filename');
    cvInput.addEventListener('change', e => {
        const f = e.target.files[0];
        if (!f) return;
        if (f.type !== 'application/pdf') { APP.toast('CV harus PDF', 'error'); cvInput.value=''; return; }
        if (f.size > 3 * 1024 * 1024)     { APP.toast('CV maks 3MB', 'error'); cvInput.value=''; return; }
        cvPH.classList.add('hidden');
        cvFilled.classList.remove('hidden'); cvFilled.classList.add('flex');
        cvName.textContent = f.name;
    });

    // Enable submit after checkbox
    const agree  = document.getElementById('agree');
    const btn    = document.getElementById('submit-btn');
    agree.addEventListener('change', () => btn.disabled = !agree.checked);

    // Submit
    document.getElementById('apply-form').addEventListener('submit', async e => {
        e.preventDefault();
        if (!cvInput.files[0]) return APP.toast('CV PDF wajib diupload', 'error');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Mengirim...';

        const fd = new FormData(e.target);
        const res = await APP.api('/pelamar', { method: 'POST', body: fd });

        if (res.status === 201) {
            document.getElementById('kode-tracking').textContent = res.data.kode_tracking;
            const modal = document.getElementById('success-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            APP.toast(res.message || 'Gagal mengirim lamaran', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-1"></i> Kirim Lamaran';
        }
    });

    document.getElementById('copy-kode').addEventListener('click', () => {
        const t = document.getElementById('kode-tracking').textContent;
        navigator.clipboard.writeText(t).then(() => APP.toast('Kode tracking disalin', 'success'));
    });
})();
</script>
