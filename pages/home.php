<!-- HERO -->
<section class="relative overflow-hidden mesh-bg">
<div class="absolute top-10 right-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float"></div>
<div class="absolute bottom-10 left-10 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay:2s"></div>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-28 relative z-10">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <div class="animate-fade-in-up">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/80 backdrop-blur border border-brand-200 text-brand-700 rounded-full text-xs font-bold mb-5 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Lowongan Terbuka
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6">
            Bergabunglah Bersama <span class="gradient-text">BPR BKK Jateng</span>
        </h1>
        <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl mb-8">
            PT BPR BKK Jateng (Perseroda) membuka kesempatan bagi putra-putri terbaik Jawa Tengah untuk berkarier di dunia perbankan. Daftar sekarang secara online!
        </p>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-briefcase"></i> Lihat Lowongan
            </a>
            <a href="<?= BASE_URL ?>/status" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white border-2 border-slate-200 text-slate-700 font-bold hover:border-brand-500 hover:text-brand-600 transition-all">
                <i class="fa-solid fa-magnifying-glass"></i> Cek Status Lamaran
            </a>
        </div>
    </div>
    <div class="relative animate-fade-in-up" style="animation-delay:0.15s">
        <div class="bg-white rounded-3xl shadow-2xl shadow-indigo-500/20 p-6 border border-slate-100 transform rotate-1 hover:rotate-0 transition-transform duration-500">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl gradient-brand flex items-center justify-center text-white text-lg"><i class="fa-solid fa-building-columns"></i></div>
                <div><p class="font-bold text-slate-900">PT BPR BKK Jateng</p><p class="text-xs text-slate-500">Perseroda &bull; Jawa Tengah</p></div>
            </div>
            <div class="space-y-2 mb-4">
                <div class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-briefcase text-indigo-500 w-4"></i> <span id="home-posisi">-</span> posisi tersedia</div>
                <div class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-location-dot text-rose-500 w-4"></i> <span id="home-lokasi">-</span> lokasi penempatan</div>
                <div class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-user-group text-emerald-500 w-4"></i> <span id="home-pelamar">-</span> pelamar terdaftar</div>
            </div>
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full w-3/5 gradient-brand rounded-full"></div></div>
        </div>
    </div>
</div>
</div>
</section>

<!-- STEPS -->
<section class="py-16 lg:py-20 bg-white">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-2xl mx-auto mb-12">
    <span class="text-brand-600 font-bold tracking-wider text-xs uppercase">Alur Pelamaran</span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Cara Melamar <span class="gradient-text">Mudah &amp; Cepat</span></h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
<?php
$steps = [
    ['fa-magnifying-glass','from-indigo-500 to-purple-500','1','Pilih Lowongan','Buka halaman lowongan dan pilih batch rekrutmen yang sedang dibuka.'],
    ['fa-file-lines','from-purple-500 to-pink-500','2','Isi Formulir','Lengkapi formulir A-I secara online (data pribadi, pendidikan, dokumen, dll).'],
    ['fa-envelope','from-pink-500 to-orange-500','3','Dapat Kode Tracking','Setelah submit, Anda menerima kode tracking via email untuk memantau status.'],
    ['fa-handshake','from-emerald-500 to-teal-500','4','Proses Seleksi','Tim HR akan memproses lamaran Anda melalui tahap administrasi, tes, dan interview.'],
];
foreach($steps as [$icon,$grad,$num,$title,$desc]):?>
<div class="bg-white rounded-2xl p-6 border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group">
    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?=$grad?> flex items-center justify-center text-white text-xl shadow-lg mb-4"><i class="fa-solid <?=$icon?>"></i></div>
    <div class="absolute top-6 right-6 text-5xl font-black text-slate-100 group-hover:text-brand-100 transition"><?=$num?></div>
    <h3 class="text-lg font-bold text-slate-900 mb-2"><?=$title?></h3>
    <p class="text-sm text-slate-600 leading-relaxed"><?=$desc?></p>
</div>
<?php endforeach;?>
</div>
</div>
</section>

<!-- CTA -->
<section class="py-16 lg:py-20">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<div class="relative overflow-hidden rounded-3xl gradient-brand p-10 lg:p-16 text-center text-white shadow-2xl">
    <div class="relative z-10 max-w-2xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Siap Bergabung?</h2>
        <p class="text-white/90 text-lg mb-8">Daftarkan diri Anda sekarang dan jadilah bagian dari BPR BKK Jateng.</p>
        <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white text-brand-700 font-bold shadow-lg hover:scale-105 transition">
            Daftar Sekarang <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</div>
</div>
</section>

<script>
(async function(){
    const res=await APP.api('/lowongan');
    if(res.status===200&&res.data&&res.data.length>0){
        const j=res.data[0];
        document.getElementById('home-posisi').textContent=(j.posisi_tersedia||[]).length;
        document.getElementById('home-lokasi').textContent=(j.penempatan_tersedia||[]).length;
        document.getElementById('home-pelamar').textContent=j.jumlah_pelamar||0;
    }
})();
</script>
