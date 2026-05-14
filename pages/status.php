<section class="py-16 bg-gray-50 min-h-[70vh] mesh-bg">
<div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-3xl">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl gradient-blue flex items-center justify-center text-white text-2xl mx-auto mb-4 shadow-lg"><i class="fa-solid fa-magnifying-glass"></i></div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Cek <span class="gradient-text">Status Lamaran</span></h1>
        <p class="text-gray-600 mt-3">Masukkan kode tracking yang Anda terima setelah submit lamaran.</p>
    </div>
    <form id="form-track" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-3 flex gap-2">
        <div class="relative flex-1">
            <i class="fa-solid fa-hashtag absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input id="kode" type="text" placeholder="Contoh: BPR-20260514-ABC123" required class="w-full pl-11 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm font-semibold uppercase">
        </div>
        <button class="px-6 py-3 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold whitespace-nowrap transition"><i class="fa-solid fa-search mr-1"></i> Cek</button>
    </form>
    <div id="result" class="mt-6"></div>
</div>
</section>
<script>
(function(){
const form=document.getElementById('form-track');
const result=document.getElementById('result');
form.addEventListener('submit',async e=>{
    e.preventDefault();
    const kode=document.getElementById('kode').value.trim().toUpperCase();
    if(!kode)return;
    result.innerHTML=`<div class="bg-white rounded-2xl p-10 text-center animate-pulse"><div class="h-6 bg-gray-200 rounded w-1/2 mx-auto"></div></div>`;
    const res=await APP.api('/pelamar/track/'+encodeURIComponent(kode));
    if(res.status!==200){
        result.innerHTML=`<div class="bg-white rounded-2xl p-8 text-center border border-rose-100"><i class="fa-solid fa-circle-exclamation text-4xl text-rose-500 mb-2"></i><p class="font-bold text-slate-800">${res.message||'Kode tidak ditemukan'}</p></div>`;
        return;
    }
    const d=res.data;
    const stages=[{key:'pending',label:'Lamaran Diterima'},{key:'review',label:'Seleksi Administrasi'},{key:'tes_administrasi',label:'Tes Administrasi'},{key:'tes_tertulis',label:'Tes Tertulis'},{key:'interview',label:'Interview'},{key:'diterima',label:'Diterima'}];
    const rejected=d.status_lamaran==='ditolak';
    const currentIdx=rejected?-1:stages.findIndex(s=>s.key===d.status_lamaran);
    const activeIdx=currentIdx===-1?0:currentIdx;

    result.innerHTML=`
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
        <div class="p-6 gradient-blue text-white">
            <p class="text-xs font-bold text-white/70 uppercase">Kode Tracking</p>
            <p class="text-xl font-extrabold tracking-wider">${d.kode_tracking}</p>
            <div class="mt-2">${APP.statusBadge(d.status_lamaran)}</div>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-6">
                <div><dt class="text-gray-500">Nama</dt><dd class="font-bold text-slate-900 mt-0.5">${d.nama_lengkap}</dd></div>
                <div><dt class="text-gray-500">Email</dt><dd class="font-bold text-slate-900 mt-0.5 truncate">${d.email}</dd></div>
                <div><dt class="text-gray-500">Posisi</dt><dd class="font-bold text-slate-900 mt-0.5">${d.posisi_dilamar}</dd></div>
                <div><dt class="text-gray-500">Penempatan</dt><dd class="font-bold text-slate-900 mt-0.5">${d.penempatan}</dd></div>
                <div><dt class="text-gray-500">Tanggal Melamar</dt><dd class="font-bold text-slate-900 mt-0.5">${APP.formatDate(d.created_at)}</dd></div>
                <div><dt class="text-gray-500">Update Terakhir</dt><dd class="font-bold text-slate-900 mt-0.5">${APP.formatDate(d.updated_at)}</dd></div>
            </dl>
            ${rejected?`<div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-center"><i class="fa-solid fa-circle-xmark text-3xl text-rose-500 mb-2"></i><p class="font-bold text-rose-700">Mohon maaf, Anda belum lolos pada tahap seleksi kali ini.</p><p class="text-sm text-rose-600 mt-1">Tetap semangat, Anda bisa mencoba di kesempatan berikutnya!</p></div>`
            :`<div class="relative">
                <div class="hidden sm:block absolute top-6 left-[8%] right-[8%] h-1 bg-gray-100 rounded-full z-0"><div class="h-full bg-brand-600 rounded-full transition-all" style="width:${(activeIdx/(stages.length-1))*100}%"></div></div>
                <div class="relative z-10 grid grid-cols-3 sm:grid-cols-6 gap-2">
                    ${stages.map((s,i)=>{
                        const done=i<=activeIdx;const active=i===activeIdx;
                        return`<div class="text-center"><div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto rounded-full flex items-center justify-center text-sm mb-1 ${done?'bg-brand-600 text-white shadow-lg':'bg-gray-100 text-gray-400'} ${active?'ring-4 ring-brand-200':''}"><i class="fa-solid ${done?'fa-check':'fa-circle text-[6px]'}"></i></div><p class="text-[10px] font-bold ${done?'text-slate-900':'text-gray-400'}">${s.label}</p></div>`;
                    }).join('')}
                </div>
            </div>`}
            ${d.catatan_admin?`<div class="mt-6 bg-brand-50 border border-brand-100 rounded-xl p-4"><p class="text-xs font-bold text-brand-700 uppercase mb-1"><i class="fa-solid fa-note-sticky mr-1"></i>Catatan HR</p><p class="text-sm text-slate-700 whitespace-pre-line">${d.catatan_admin}</p></div>`:''}
        </div>
    </div>`;
});
})();
</script>
