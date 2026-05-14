<!-- Toolbar -->
<div class="bg-white rounded-2xl border border-slate-100 p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div class="relative md:col-span-1">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input id="f-q" type="text" placeholder="Cari nama / email / kode..." class="input pl-10">
    </div>
    <select id="f-status" class="input">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="review">Review</option>
        <option value="tes_administrasi">Tes Administrasi</option>
        <option value="tes_tertulis">Tes Tertulis</option>
        <option value="interview">Interview</option>
        <option value="diterima">Diterima</option>
        <option value="ditolak">Ditolak</option>
    </select>
    <select id="f-posisi" class="input">
        <option value="">Semua Posisi</option>
    </select>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 mt-5 overflow-hidden">
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead><tr class="text-left text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-100">
    <th class="px-4 py-3">Pelamar</th><th class="px-4 py-3">Posisi</th><th class="px-4 py-3">Penempatan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3 text-right">Aksi</th>
</tr></thead>
<tbody id="rows"><tr><td colspan="6" class="py-12 text-center text-slate-400">Memuat data...</td></tr></tbody>
</table>
</div>
</div>

<script>
(async function(){
const rows=document.getElementById('rows');
const q=document.getElementById('f-q');
const st=document.getElementById('f-status');
const pos=document.getElementById('f-posisi');
let all=[];

async function load(){
    rows.innerHTML='<tr><td colspan="6" class="py-12 text-center text-slate-400">Memuat...</td></tr>';
    const res=await ADMIN.api('/pelamar');
    if(res.status!==200){rows.innerHTML=`<tr><td colspan="6" class="py-12 text-center text-rose-500">${res.message}</td></tr>`;return;}
    all=res.data||[];
    // Populate posisi filter
    const unique=[...new Set(all.map(x=>x.posisi_dilamar))].sort();
    pos.innerHTML='<option value="">Semua Posisi</option>'+unique.map(p=>`<option value="${p}">${p}</option>`).join('');
    render();
}

function render(){
    const qv=q.value.toLowerCase().trim();
    const stv=st.value;
    const pv=pos.value;
    const list=all.filter(p=>{
        if(stv&&p.status_lamaran!==stv)return false;
        if(pv&&p.posisi_dilamar!==pv)return false;
        if(qv){const t=(p.nama_lengkap+' '+p.email+' '+p.kode_tracking).toLowerCase();if(!t.includes(qv))return false;}
        return true;
    });
    if(list.length===0){rows.innerHTML='<tr><td colspan="6" class="py-12 text-center text-slate-400">Tidak ada pelamar.</td></tr>';return;}
    rows.innerHTML=list.map(p=>`
    <tr class="border-b border-slate-50 hover:bg-slate-50/60">
        <td class="px-4 py-3"><a href="${ADMIN.baseUrl}/client/pelamar_detail/${p.id}" class="font-bold text-slate-900 hover:text-brand-600">${p.nama_lengkap}</a><p class="text-xs text-slate-500">${p.email}</p></td>
        <td class="px-4 py-3 text-slate-700">${p.posisi_dilamar}</td>
        <td class="px-4 py-3 text-slate-600">${p.penempatan}</td>
        <td class="px-4 py-3">${ADMIN.statusBadge(p.status_lamaran)}</td>
        <td class="px-4 py-3 text-slate-600 whitespace-nowrap">${ADMIN.formatDateTime(p.created_at)}</td>
        <td class="px-4 py-3 text-right"><a href="${ADMIN.baseUrl}/client/pelamar_detail/${p.id}" class="px-3 py-2 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold"><i class="fa-solid fa-eye mr-1"></i>Detail</a></td>
    </tr>`).join('');
}

[q,st,pos].forEach(el=>el.addEventListener('input',render));
load();
})();
</script>
