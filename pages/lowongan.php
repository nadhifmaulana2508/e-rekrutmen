<!-- HEADER -->
<section class="relative mesh-bg border-b border-slate-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 relative z-10">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900">Semua <span class="gradient-text">Lowongan</span></h1>
        <p class="text-slate-600 mt-3 max-w-2xl">Telusuri semua lowongan aktif & temukan posisi yang cocok dengan passion kamu.</p>

        <!-- Search + Filter -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg shadow-slate-900/5 border border-slate-100 p-3 grid grid-cols-1 md:grid-cols-[1fr_auto_auto_auto] gap-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="f-q" type="text" placeholder="Cari posisi, skill, atau lokasi..." class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm font-medium">
            </div>
            <select id="f-divisi" class="py-3 px-4 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium focus:border-brand-500 outline-none">
                <option value="">Semua Divisi</option>
            </select>
            <select id="f-tipe" class="py-3 px-4 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium focus:border-brand-500 outline-none">
                <option value="">Semua Tipe</option>
                <option value="full_time">Full Time</option>
                <option value="part_time">Part Time</option>
                <option value="kontrak">Kontrak</option>
                <option value="magang">Magang</option>
                <option value="freelance">Freelance</option>
            </select>
            <button id="f-reset" class="py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-700 transition">
                <i class="fa-solid fa-rotate-left mr-1"></i> Reset
            </button>
        </div>
    </div>
</section>

<!-- LIST -->
<section class="py-10 lg:py-14 bg-slate-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-slate-600"><span id="count-result" class="font-bold text-slate-900">0</span> lowongan ditemukan</p>
        </div>

        <div id="job-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="animate-pulse bg-white h-64 rounded-2xl"></div>
            <div class="animate-pulse bg-white h-64 rounded-2xl hidden sm:block"></div>
            <div class="animate-pulse bg-white h-64 rounded-2xl hidden lg:block"></div>
        </div>
    </div>
</section>

<script>
(function () {
    const qEl     = document.getElementById('f-q');
    const divEl   = document.getElementById('f-divisi');
    const tipeEl  = document.getElementById('f-tipe');
    const resetEl = document.getElementById('f-reset');
    const listEl  = document.getElementById('job-list');
    const countEl = document.getElementById('count-result');

    let all = [];

    const gradients = ['from-indigo-500 to-purple-500','from-pink-500 to-rose-500','from-emerald-500 to-teal-500','from-amber-500 to-orange-500','from-sky-500 to-blue-500','from-fuchsia-500 to-pink-500'];

    function render(list) {
        countEl.textContent = list.length;
        if (list.length === 0) {
            listEl.innerHTML = `<div class="col-span-full text-center py-16 bg-white rounded-2xl border border-slate-100">
                <i class="fa-solid fa-briefcase text-5xl text-slate-300 mb-4"></i>
                <p class="font-bold text-slate-700">Tidak ada lowongan yang cocok</p>
                <p class="text-sm text-slate-500 mt-1">Coba ubah filter atau kata kunci pencarian.</p>
            </div>`;
            return;
        }
        listEl.innerHTML = list.map(card).join('');
    }

    function card(j) {
        const g = gradients[j.id % gradients.length];
        const deadline = j.deadline ? APP.formatDate(j.deadline) : 'Flexible';
        return `
        <a href="${APP.baseUrl}/detail/${j.id}" class="group bg-white rounded-2xl p-6 border border-slate-100 hover:border-brand-300 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br ${g} flex items-center justify-center text-white text-lg shadow-md">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold text-brand-600 uppercase tracking-wide">${j.divisi}</p>
                    <h3 class="font-bold text-slate-900 text-lg leading-tight line-clamp-2 group-hover:text-brand-700 transition">${j.judul}</h3>
                </div>
            </div>
            <p class="text-sm text-slate-600 line-clamp-2 mb-4">${j.deskripsi}</p>
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700"><i class="fa-solid fa-location-dot mr-1 text-rose-500"></i>${j.lokasi}</span>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700">${APP.formatTipeKerja(j.tipe_kerja)}</span>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">${APP.formatLevel(j.level)}</span>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-slate-500 font-medium uppercase">Gaji</p>
                    <p class="text-sm font-bold text-slate-900">${APP.formatGajiRange(j.gaji_min, j.gaji_max)}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-500 font-medium uppercase">Deadline</p>
                    <p class="text-sm font-semibold text-slate-800">${deadline}</p>
                </div>
            </div>
        </a>`;
    }

    function applyFilter() {
        const q    = qEl.value.toLowerCase().trim();
        const div  = divEl.value;
        const tipe = tipeEl.value;
        const filtered = all.filter(j => {
            if (div  && j.divisi     !== div)  return false;
            if (tipe && j.tipe_kerja !== tipe) return false;
            if (q) {
                const t = (j.judul + ' ' + j.deskripsi + ' ' + j.lokasi + ' ' + j.divisi).toLowerCase();
                if (!t.includes(q)) return false;
            }
            return true;
        });
        render(filtered);
    }

    [qEl, divEl, tipeEl].forEach(el => el.addEventListener('input', applyFilter));
    resetEl.addEventListener('click', () => { qEl.value=''; divEl.value=''; tipeEl.value=''; applyFilter(); });

    (async function init() {
        const res = await APP.api('/lowongan');
        if (res.status === 200) {
            all = res.data || [];
            // populate divisi unique
            const unique = [...new Set(all.map(x => x.divisi))].sort();
            divEl.insertAdjacentHTML('beforeend', unique.map(d => `<option value="${d}">${d}</option>`).join(''));
            render(all);
        } else {
            listEl.innerHTML = `<div class="col-span-full text-center py-16 bg-rose-50 rounded-2xl text-rose-600">
                <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
                <p class="font-medium">${res.message || 'Gagal memuat data'}</p>
            </div>`;
        }
    })();
})();
</script>
