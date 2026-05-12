<section class="py-16 bg-slate-50 min-h-[70vh] mesh-bg">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-3xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl gradient-brand flex items-center justify-center text-white text-2xl mx-auto mb-4 shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Cek <span class="gradient-text">Status Lamaran</span></h1>
            <p class="text-slate-600 mt-3">Masukkan kode tracking yang kamu terima saat submit lamaran.</p>
        </div>

        <form id="form-track" class="bg-white rounded-2xl shadow-lg shadow-slate-900/5 border border-slate-100 p-3 flex gap-2">
            <div class="relative flex-1">
                <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="kode" type="text" placeholder="Contoh: REK-20260112-ABC123" required
                       class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm font-semibold uppercase">
            </div>
            <button class="px-6 py-3 rounded-xl gradient-brand text-white font-bold whitespace-nowrap">
                <i class="fa-solid fa-search mr-1"></i> Cek
            </button>
        </form>

        <div id="result" class="mt-6"></div>
    </div>
</section>

<script>
(function () {
    const form   = document.getElementById('form-track');
    const result = document.getElementById('result');

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const kode = document.getElementById('kode').value.trim().toUpperCase();
        if (!kode) return;

        result.innerHTML = `<div class="bg-white rounded-2xl p-10 text-center animate-pulse"><div class="h-6 bg-slate-200 rounded w-1/2 mx-auto"></div></div>`;

        const res = await APP.api('/pelamar/track/' + encodeURIComponent(kode));
        if (res.status !== 200) {
            result.innerHTML = `<div class="bg-white rounded-2xl p-8 text-center border border-rose-100">
                <i class="fa-solid fa-circle-exclamation text-4xl text-rose-500 mb-2"></i>
                <p class="font-bold text-slate-800">${res.message || 'Kode tidak ditemukan'}</p>
                <p class="text-sm text-slate-500 mt-1">Periksa kembali kode tracking kamu.</p>
            </div>`;
            return;
        }

        const d = res.data;
        const stages = [
            { key: 'pending',   label: 'Lamaran Diterima' },
            { key: 'review',    label: 'Sedang Direview' },
            { key: 'interview', label: 'Tahap Interview' },
            { key: 'diterima',  label: 'Diterima' },
        ];
        const rejected = d.status_lamaran === 'ditolak';
        const currentIdx = rejected ? -1 : stages.findIndex(s => s.key === d.status_lamaran);
        const activeIdx  = currentIdx === -1 ? 0 : currentIdx;

        result.innerHTML = `
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up">
            <div class="p-6 sm:p-8 gradient-brand text-white">
                <p class="text-xs font-bold text-white/70 uppercase tracking-wider">Kode Tracking</p>
                <p class="text-xl sm:text-2xl font-extrabold tracking-wider">${d.kode_tracking}</p>
                <div class="mt-3">${APP.statusBadge(d.status_lamaran)}</div>
            </div>

            <div class="p-6 sm:p-8">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-8">
                    <div><dt class="text-slate-500 font-medium">Nama</dt><dd class="font-bold text-slate-900 mt-0.5">${d.nama_lengkap}</dd></div>
                    <div><dt class="text-slate-500 font-medium">Email</dt><dd class="font-bold text-slate-900 mt-0.5 truncate">${d.email}</dd></div>
                    <div><dt class="text-slate-500 font-medium">Posisi</dt><dd class="font-bold text-slate-900 mt-0.5">${d.judul_lowongan || '-'}</dd></div>
                    <div><dt class="text-slate-500 font-medium">Divisi</dt><dd class="font-bold text-slate-900 mt-0.5">${d.divisi || '-'}</dd></div>
                    <div><dt class="text-slate-500 font-medium">Tanggal Melamar</dt><dd class="font-bold text-slate-900 mt-0.5">${APP.formatDate(d.created_at)}</dd></div>
                    <div><dt class="text-slate-500 font-medium">Update Terakhir</dt><dd class="font-bold text-slate-900 mt-0.5">${APP.formatDate(d.updated_at)}</dd></div>
                </dl>

                <!-- Timeline -->
                <div class="relative">
                    ${rejected
                        ? `<div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-center">
                                <i class="fa-solid fa-circle-xmark text-3xl text-rose-500 mb-2"></i>
                                <p class="font-bold text-rose-700">Mohon maaf, lamaran kamu belum berhasil kali ini.</p>
                                <p class="text-sm text-rose-600 mt-1">Tetap semangat mencoba di lowongan lain ya!</p>
                           </div>`
                        : `<div class="hidden sm:block absolute top-6 left-[10%] right-[10%] h-1 bg-slate-100 rounded-full z-0">
                                <div class="h-full gradient-brand rounded-full transition-all duration-500" style="width:${(activeIdx / (stages.length-1)) * 100}%"></div>
                           </div>
                           <div class="relative z-10 grid grid-cols-4 gap-3">
                                ${stages.map((s, i) => {
                                    const done   = i <= activeIdx;
                                    const active = i === activeIdx;
                                    return `<div class="text-center">
                                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-lg mb-2 transition
                                                    ${done ? 'gradient-brand text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-100 text-slate-400 border-2 border-white'}
                                                    ${active ? 'ring-4 ring-brand-200' : ''}">
                                            <i class="fa-solid ${done ? 'fa-check' : 'fa-circle'}"></i>
                                        </div>
                                        <p class="text-[11px] font-bold ${done ? 'text-slate-900' : 'text-slate-400'}">${s.label}</p>
                                    </div>`;
                                }).join('')}
                           </div>`
                    }
                </div>

                ${d.catatan_admin ? `
                <div class="mt-6 bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                    <p class="text-xs font-bold text-brand-700 uppercase mb-1"><i class="fa-solid fa-note-sticky mr-1"></i> Catatan dari HR</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">${d.catatan_admin}</p>
                </div>` : ''}
            </div>
        </div>`;
    });
})();
</script>
