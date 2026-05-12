<?php
$id = (int)($_GET['id'] ?? 0);
?>
<section class="py-10 bg-slate-50 min-h-[60vh]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-600 font-semibold mb-6 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar lowongan
        </a>

        <div id="loader" class="bg-white rounded-3xl p-8 animate-pulse">
            <div class="h-8 bg-slate-200 rounded w-2/3 mb-3"></div>
            <div class="h-5 bg-slate-200 rounded w-1/3 mb-8"></div>
            <div class="h-40 bg-slate-100 rounded"></div>
        </div>

        <div id="detail" class="hidden"></div>
    </div>
</section>

<script>
(async function () {
    const id = <?= $id ?>;
    if (!id) { location.href = APP.baseUrl + '/lowongan'; return; }

    const res = await APP.api('/lowongan/' + id);
    document.getElementById('loader').classList.add('hidden');
    const el = document.getElementById('detail');
    el.classList.remove('hidden');

    if (res.status !== 200) {
        el.innerHTML = `<div class="bg-white rounded-3xl p-10 text-center">
            <i class="fa-solid fa-circle-exclamation text-5xl text-rose-500 mb-3"></i>
            <p class="font-bold text-slate-800">${res.message || 'Lowongan tidak ditemukan'}</p>
            <a href="${APP.baseUrl}/lowongan" class="inline-block mt-4 text-brand-600 font-semibold">Kembali ke daftar</a>
        </div>`;
        return;
    }

    const j = res.data;
    const lines = s => (s || '').split('\n').map(x => x.trim()).filter(Boolean);
    const deadline = j.deadline ? APP.formatDate(j.deadline) : 'Fleksibel';
    const gradients = ['from-indigo-500 to-purple-500','from-pink-500 to-rose-500','from-emerald-500 to-teal-500','from-amber-500 to-orange-500','from-sky-500 to-blue-500'];
    const g = gradients[j.id % gradients.length];
    const canApply = j.status === 'aktif' && (!j.deadline || new Date(j.deadline) >= new Date(new Date().toDateString()));

    el.innerHTML = `
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">
        <!-- Main -->
        <div class="space-y-6">
            <!-- Header card -->
            <div class="relative overflow-hidden bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <div class="absolute inset-0 opacity-5 gradient-brand"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br ${g} flex items-center justify-center text-white text-3xl shadow-xl shrink-0">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-brand-600 uppercase tracking-wide">${j.divisi}</p>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">${j.judul}</h1>
                        <div class="flex flex-wrap gap-3 mt-3 text-sm text-slate-600">
                            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-rose-500"></i> ${j.lokasi}</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-clock text-indigo-500"></i> ${APP.formatTipeKerja(j.tipe_kerja)}</span>
                            <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-user-graduate text-emerald-500"></i> ${APP.formatLevel(j.level)}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2"><i class="fa-solid fa-file-lines text-brand-500"></i> Deskripsi Pekerjaan</h2>
                <p class="text-slate-600 leading-relaxed whitespace-pre-line">${j.deskripsi}</p>
            </div>

            <!-- Requirements -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-list-check text-brand-500"></i> Kualifikasi</h2>
                <ul class="space-y-3">
                    ${lines(j.requirements).map(r => `
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 mt-1 shrink-0"></i>
                            <span class="text-slate-700">${r}</span>
                        </li>`).join('')}
                </ul>
            </div>

            <!-- Benefits -->
            ${lines(j.benefits).length ? `
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-gift text-pink-500"></i> Benefit</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    ${lines(j.benefits).map(b => `
                        <div class="flex items-start gap-3 bg-slate-50 rounded-xl p-3">
                            <i class="fa-solid fa-star text-amber-500 mt-1"></i>
                            <span class="text-sm text-slate-700">${b}</span>
                        </div>`).join('')}
                </div>
            </div>` : ''}
        </div>

        <!-- Sidebar / Apply card -->
        <aside class="space-y-5">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sticky top-24">
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wide">Range Gaji</p>
                <p class="text-2xl font-extrabold gradient-text mt-1">${APP.formatGajiRange(j.gaji_min, j.gaji_max)}</p>
                <p class="text-xs text-slate-400">per bulan</p>

                <div class="my-5 border-t border-slate-100"></div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Divisi</dt><dd class="font-semibold text-slate-800">${j.divisi}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Lokasi</dt><dd class="font-semibold text-slate-800">${j.lokasi}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Tipe</dt><dd class="font-semibold text-slate-800">${APP.formatTipeKerja(j.tipe_kerja)}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Level</dt><dd class="font-semibold text-slate-800">${APP.formatLevel(j.level)}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Deadline</dt><dd class="font-semibold text-slate-800">${deadline}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Pelamar</dt><dd class="font-semibold text-slate-800">${j.jumlah_pelamar || 0} orang</dd></div>
                </dl>

                <div class="mt-6">
                    ${canApply
                        ? `<a href="${APP.baseUrl}/form/${j.id}" class="block w-full text-center py-3.5 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 hover:-translate-y-0.5 transition">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Lamar Sekarang
                           </a>`
                        : `<button disabled class="block w-full text-center py-3.5 rounded-xl bg-slate-200 text-slate-500 font-bold cursor-not-allowed">
                                <i class="fa-solid fa-lock mr-1"></i> Lowongan Ditutup
                           </button>`}
                </div>
                <p class="text-[11px] text-slate-400 text-center mt-3">Dengan melamar, kamu menyetujui syarat & ketentuan platform.</p>
            </div>
        </aside>
    </div>`;
})();
</script>
