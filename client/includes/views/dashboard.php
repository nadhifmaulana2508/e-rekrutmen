<!-- STATS CARDS -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
<?php
$cards = [
    ['Total Lowongan','from-indigo-500 to-purple-500','fa-briefcase','total_lowongan'],
    ['Lowongan Aktif','from-emerald-500 to-teal-500','fa-circle-check','lowongan_aktif'],
    ['Total Pelamar','from-pink-500 to-rose-500','fa-user-group','total_pelamar'],
    ['Diterima','from-amber-500 to-orange-500','fa-award','diterima'],
];
foreach($cards as [$label,$grad,$icon,$key]):?>
<div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <div class="flex items-start justify-between">
        <div><p class="text-xs font-semibold text-slate-500 uppercase"><?=$label?></p><p class="text-3xl font-extrabold text-slate-900 mt-2 stat-<?=$key?>">–</p></div>
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?=$grad?> flex items-center justify-center text-white shadow-md"><i class="fa-solid <?=$icon?>"></i></div>
    </div>
</div>
<?php endforeach;?>
</section>

<section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
<!-- Pipeline -->
<div class="bg-white rounded-2xl border border-slate-100 p-6 xl:col-span-1">
    <h2 class="font-bold text-slate-900">Pipeline Pelamar</h2>
    <div id="pipeline-list" class="mt-4 space-y-3"><div class="h-10 bg-slate-100 rounded-xl animate-pulse"></div></div>
</div>
<!-- Top Posisi -->
<div class="bg-white rounded-2xl border border-slate-100 p-6 xl:col-span-2">
    <h2 class="font-bold text-slate-900 mb-4">Posisi Terpopuler</h2>
    <div id="top-list" class="space-y-3"><div class="h-14 bg-slate-100 rounded-xl animate-pulse"></div></div>
</div>
</section>

<!-- Recent -->
<section class="bg-white rounded-2xl border border-slate-100 p-6 mt-6">
<div class="flex items-center justify-between mb-5">
    <h2 class="font-bold text-slate-900">Pelamar Terbaru</h2>
    <a href="<?= BASE_URL ?>/client/pelamar" class="text-xs text-brand-600 font-semibold hover:underline">Lihat semua →</a>
</div>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead><tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
    <th class="px-3 py-3">Nama</th><th class="px-3 py-3">Posisi</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Tanggal</th>
</tr></thead>
<tbody id="recent-rows"><tr><td colspan="4" class="py-8 text-center text-slate-400">Memuat...</td></tr></tbody>
</table>
</div>
</section>

<script>
(async function(){
const res=await ADMIN.api('/dashboard/stats');
if(res.status!==200){ADMIN.toast(res.message||'Gagal','error');return;}
const d=res.data;
const diterima=(d.pipeline.find(p=>p.status==='diterima')||{}).total||0;
document.querySelector('.stat-total_lowongan').textContent=d.total_lowongan;
document.querySelector('.stat-lowongan_aktif').textContent=d.lowongan_aktif;
document.querySelector('.stat-total_pelamar').textContent=d.total_pelamar;
document.querySelector('.stat-diterima').textContent=diterima;

const statuses=['pending','review','tes_administrasi','tes_tertulis','interview','diterima','ditolak'];
const labels={pending:'Pending',review:'Review',tes_administrasi:'Tes Admin',tes_tertulis:'Tes Tulis',interview:'Interview',diterima:'Diterima',ditolak:'Ditolak'};
const colors={pending:'bg-amber-500',review:'bg-blue-500',tes_administrasi:'bg-cyan-500',tes_tertulis:'bg-indigo-500',interview:'bg-purple-500',diterima:'bg-emerald-500',ditolak:'bg-rose-500'};
const byStatus=Object.fromEntries((d.pipeline||[]).map(p=>[p.status,parseInt(p.total)]));
const total=Math.max(1,d.total_pelamar);
document.getElementById('pipeline-list').innerHTML=statuses.map(s=>{
    const val=byStatus[s]||0;const pct=Math.round((val/total)*100);
    return`<div><div class="flex justify-between mb-1 text-xs"><span class="font-semibold text-slate-700">${labels[s]}</span><span class="font-bold">${val}</span></div><div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full ${colors[s]} rounded-full" style="width:${pct}%"></div></div></div>`;
}).join('');

const topEl=document.getElementById('top-list');
if(!d.top_posisi||d.top_posisi.length===0){topEl.innerHTML='<p class="text-slate-400 text-sm">Belum ada data</p>';}
else{
    const mx=Math.max(1,...d.top_posisi.map(x=>parseInt(x.total)));
    topEl.innerHTML=d.top_posisi.map((l,i)=>{
        const pct=Math.round((parseInt(l.total)/mx)*100);
        return`<div class="p-3 rounded-xl hover:bg-slate-50"><div class="flex items-center gap-3"><span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold">${i+1}</span><div class="flex-1 min-w-0"><p class="font-bold text-sm truncate">${l.posisi}</p></div><p class="text-lg font-extrabold">${l.total}</p></div><div class="h-1.5 bg-slate-100 rounded-full overflow-hidden mt-2 ml-10"><div class="h-full gradient-brand rounded-full" style="width:${pct}%"></div></div></div>`;
    }).join('');
}

const recentEl=document.getElementById('recent-rows');
if(!d.recent||d.recent.length===0){recentEl.innerHTML='<tr><td colspan="4" class="py-8 text-center text-slate-400">Belum ada pelamar</td></tr>';}
else{recentEl.innerHTML=d.recent.map(r=>`<tr class="border-b border-slate-50 hover:bg-slate-50/60"><td class="px-3 py-3"><a href="${ADMIN.baseUrl}/client/pelamar_detail/${r.id}" class="font-semibold hover:text-brand-600">${r.nama_lengkap}</a><p class="text-xs text-slate-500">${r.email}</p></td><td class="px-3 py-3 text-slate-700">${r.posisi_dilamar}</td><td class="px-3 py-3">${ADMIN.statusBadge(r.status_lamaran)}</td><td class="px-3 py-3 text-slate-600 whitespace-nowrap">${ADMIN.formatDateTime(r.created_at)}</td></tr>`).join('');}
})();
</script>
