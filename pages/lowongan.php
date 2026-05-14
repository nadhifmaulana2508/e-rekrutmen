<section class="relative mesh-bg border-b border-gray-200">
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 relative z-10">
    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900">Lowongan <span class="gradient-text">Terbuka</span></h1>
    <p class="text-gray-600 mt-3 max-w-2xl">Temukan posisi yang sesuai dengan keahlian Anda di PT BPR BKK Jateng (Perseroda).</p>
</div>
</section>

<section class="py-10 lg:py-14 bg-gray-50">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div id="job-list" class="grid grid-cols-1 gap-5">
        <div class="animate-pulse bg-white h-48 rounded-2xl"></div>
    </div>
</div>
</section>

<script>
(async function(){
    const listEl=document.getElementById('job-list');
    const res=await APP.api('/lowongan');
    if(res.status!==200||!res.data||res.data.length===0){
        listEl.innerHTML=`<div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100">
            <i class="fa-solid fa-briefcase text-5xl text-gray-300 mb-4"></i>
            <p class="font-bold text-slate-700">Belum ada lowongan terbuka saat ini</p>
            <p class="text-sm text-gray-500 mt-1">Silakan cek kembali nanti.</p></div>`;
        return;
    }
    listEl.innerHTML=res.data.map(j=>{
        const posisi=(j.posisi_tersedia||[]).slice(0,5).join(', ');
        const more=(j.posisi_tersedia||[]).length>5?` +${j.posisi_tersedia.length-5} lainnya`:'';
        const deadline=j.deadline?APP.formatDate(j.deadline):'Belum ditentukan';
        return`
        <a href="${APP.baseUrl}/detail/${j.id}" class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-brand-300 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl gradient-blue flex items-center justify-center text-white text-2xl shadow-lg shrink-0">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-extrabold text-slate-900 group-hover:text-brand-700 transition">${j.judul}</h3>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">${j.deskripsi||''}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-700"><i class="fa-solid fa-users mr-1"></i>${j.jumlah_pelamar||0} Pelamar</span>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-accent-50 text-accent-700"><i class="fa-solid fa-calendar mr-1"></i>Deadline: ${deadline}</span>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700"><i class="fa-solid fa-briefcase mr-1"></i>${(j.posisi_tersedia||[]).length} Posisi</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-list-check mr-1"></i>${posisi}${more}</p>
                </div>
                <div class="shrink-0">
                    <span class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-brand-50 text-brand-700 font-bold text-sm group-hover:bg-brand-700 group-hover:text-white transition">
                        Lihat Detail <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </a>`;
    }).join('');
})();
</script>
