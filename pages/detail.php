<?php $id = (int)($_GET['id'] ?? 0); ?>
<section class="py-10 bg-gray-50 min-h-[60vh]">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-brand-600 font-semibold mb-6"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
<div id="loader" class="bg-white rounded-3xl p-8 animate-pulse"><div class="h-8 bg-gray-200 rounded w-2/3 mb-3"></div><div class="h-40 bg-gray-100 rounded"></div></div>
<div id="detail" class="hidden"></div>
</div>
</section>
<script>
(async function(){
const id=<?=$id?>;
if(!id){location.href=APP.baseUrl+'/lowongan';return;}
const res=await APP.api('/lowongan/'+id);
document.getElementById('loader').classList.add('hidden');
const el=document.getElementById('detail');el.classList.remove('hidden');
if(res.status!==200){el.innerHTML=`<div class="bg-white rounded-3xl p-10 text-center"><i class="fa-solid fa-circle-exclamation text-5xl text-rose-500 mb-3"></i><p class="font-bold">${res.message}</p></div>`;return;}
const j=res.data;
const posisiList=(j.posisi_tersedia||[]).map(p=>`<span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 text-xs font-semibold">${p}</span>`).join('');
const penempatanList=(j.penempatan_tersedia||[]).map(p=>`<span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-accent-50 text-accent-700 text-xs font-semibold">${p}</span>`).join('');
const persyaratan=(j.persyaratan||'').split('\n').filter(Boolean).map(r=>`<li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-brand-500 mt-1 shrink-0"></i><span>${r}</span></li>`).join('');
const deadline=j.deadline?APP.formatDate(j.deadline):'Belum ditentukan';
const canApply=j.status==='aktif'&&(!j.deadline||new Date(j.deadline)>=new Date(new Date().toDateString()));

el.innerHTML=`
<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6">
<div class="space-y-6">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 rounded-2xl gradient-blue flex items-center justify-center text-white text-2xl shadow-lg"><i class="fa-solid fa-building-columns"></i></div>
            <div>
                <p class="text-xs font-bold text-brand-600 uppercase">PT BPR BKK Jateng (Perseroda)</p>
                <h1 class="text-2xl font-extrabold text-slate-900">${j.judul}</h1>
            </div>
        </div>
        <p class="text-gray-600 leading-relaxed whitespace-pre-line">${j.deskripsi||''}</p>
    </div>
    ${persyaratan?`<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-4"><i class="fa-solid fa-list-check text-brand-500 mr-2"></i>Persyaratan Umum</h2>
        <ul class="space-y-2 text-sm text-slate-700">${persyaratan}</ul>
    </div>`:''}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-4"><i class="fa-solid fa-briefcase text-brand-500 mr-2"></i>Posisi yang Dibuka (${(j.posisi_tersedia||[]).length})</h2>
        <div class="flex flex-wrap gap-2">${posisiList}</div>
    </div>
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-4"><i class="fa-solid fa-location-dot text-accent-500 mr-2"></i>Lokasi Penempatan (${(j.penempatan_tersedia||[]).length})</h2>
        <div class="flex flex-wrap gap-2">${penempatanList}</div>
    </div>
</div>
<aside class="space-y-5 lg:sticky lg:top-24">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd class="font-bold ${j.status==='aktif'?'text-emerald-600':'text-rose-600'}">${j.status.toUpperCase()}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Deadline</dt><dd class="font-bold text-slate-800">${deadline}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Total Pelamar</dt><dd class="font-bold text-slate-800">${j.jumlah_pelamar||0}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Posisi Tersedia</dt><dd class="font-bold text-slate-800">${(j.posisi_tersedia||[]).length}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Lokasi</dt><dd class="font-bold text-slate-800">${(j.penempatan_tersedia||[]).length} kota</dd></div>
        </dl>
        <div class="mt-6">
            ${canApply?`<a href="${APP.baseUrl}/form/${j.id}" class="block w-full text-center py-3.5 rounded-xl bg-accent-500 hover:bg-accent-600 text-white font-bold shadow-lg hover:-translate-y-0.5 transition"><i class="fa-solid fa-paper-plane mr-1"></i> Lamar Sekarang</a>`
            :`<button disabled class="block w-full text-center py-3.5 rounded-xl bg-gray-200 text-gray-500 font-bold cursor-not-allowed"><i class="fa-solid fa-lock mr-1"></i> Lowongan Ditutup</button>`}
        </div>
    </div>
</aside>
</div>`;
})();
</script>
