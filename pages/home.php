<!-- HERO -->
<section class="relative overflow-hidden bg-white">
<div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 -translate-y-1/3 translate-x-1/3"></div>
<div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-orange-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 translate-y-1/3 -translate-x-1/4"></div>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <div class="animate-fade-in-up">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-brand-50 border border-brand-200 text-brand-700 rounded-full text-xs font-bold mb-5 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Lowongan Terbuka
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6">
            Bergabunglah Bersama <span class="gradient-text">BPR BKK Jateng</span>
        </h1>
        <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-xl mb-8">
            PT BPR BKK Jateng (Perseroda) membuka kesempatan bagi putra-putri terbaik Jawa Tengah untuk berkarier di dunia perbankan. Daftar sekarang secara online!
        </p>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold shadow-lg shadow-blue-800/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-briefcase"></i> Lihat Lowongan
            </a>
            <a href="<?= BASE_URL ?>/status" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-bold hover:border-brand-500 hover:text-brand-700 transition-all">
                <i class="fa-solid fa-magnifying-glass"></i> Cek Status Lamaran
            </a>
        </div>
    </div>
    <div class="relative animate-fade-in-up" style="animation-delay:0.15s">
        <!-- Hero Image / Company Card -->
        <div class="relative">
            <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&h=400&fit=crop&crop=faces" alt="Tim Karyawan BPR BKK Jateng" class="rounded-3xl shadow-2xl shadow-blue-900/20 w-full h-[360px] object-cover border-4 border-white">
            <!-- Floating stats card -->
            <div class="absolute -bottom-6 -left-4 bg-white rounded-2xl shadow-xl border border-gray-100 p-4 animate-float">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl gradient-blue flex items-center justify-center text-white text-lg"><i class="fa-solid fa-building-columns"></i></div>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">PT BPR BKK Jateng</p>
                        <p class="text-[11px] text-gray-500">Perseroda &bull; Jawa Tengah</p>
                    </div>
                </div>
                <div class="mt-3 space-y-1.5">
                    <div class="flex items-center gap-2 text-xs text-gray-600"><i class="fa-solid fa-briefcase text-brand-500 w-4"></i> <span><strong id="home-posisi">-</strong> posisi tersedia</span></div>
                    <div class="flex items-center gap-2 text-xs text-gray-600"><i class="fa-solid fa-location-dot text-accent-500 w-4"></i> <span><strong id="home-lokasi">-</strong> lokasi penempatan</span></div>
                    <div class="flex items-center gap-2 text-xs text-gray-600"><i class="fa-solid fa-user-group text-emerald-500 w-4"></i> <span><strong id="home-pelamar">-</strong> pelamar terdaftar</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</section>

<!-- EMPLOYEE / TEAM SECTION -->
<section class="py-16 lg:py-20 bg-gray-50">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-2xl mx-auto mb-12">
    <span class="text-accent-600 font-bold tracking-wider text-xs uppercase">Tim Kami</span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Orang-Orang <span class="gradient-text">Hebat</span> di Balik Layanan</h2>
    <p class="text-gray-600 mt-3">Bergabunglah bersama profesional yang berdedikasi untuk kemajuan perbankan daerah Jawa Tengah.</p>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=500&fit=crop&crop=face" alt="Karyawan 1" class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="font-bold text-white text-sm">Account Officer</p>
            <p class="text-gray-300 text-xs">Divisi Kredit</p>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=500&fit=crop&crop=face" alt="Karyawan 2" class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="font-bold text-white text-sm">Customer Service</p>
            <p class="text-gray-300 text-xs">Divisi Layanan</p>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=500&fit=crop&crop=face" alt="Karyawan 3" class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="font-bold text-white text-sm">Analis Kredit</p>
            <p class="text-gray-300 text-xs">Divisi Risiko</p>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=500&fit=crop&crop=face" alt="Karyawan 4" class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="font-bold text-white text-sm">Teller</p>
            <p class="text-gray-300 text-xs">Divisi Operasional</p>
        </div>
    </div>
</div>
</div>
</section>

<!-- WHY JOIN US -->
<section class="py-16 lg:py-20 bg-white">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-2xl mx-auto mb-12">
    <span class="text-brand-600 font-bold tracking-wider text-xs uppercase">Kenapa Bergabung?</span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Keuntungan Berkarier di <span class="gradient-text">BPR BKK Jateng</span></h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="w-14 h-14 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3 class="text-lg font-bold text-slate-900 mb-2">Pengembangan Karier</h3>
        <p class="text-sm text-gray-600 leading-relaxed">Program pelatihan dan sertifikasi berkala untuk meningkatkan kompetensi karyawan.</p>
    </div>
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="w-14 h-14 rounded-2xl bg-accent-100 text-accent-700 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-hand-holding-heart"></i></div>
        <h3 class="text-lg font-bold text-slate-900 mb-2">Benefit Kompetitif</h3>
        <p class="text-sm text-gray-600 leading-relaxed">Gaji kompetitif, BPJS, tunjangan, bonus tahunan, dan fasilitas kesejahteraan lainnya.</p>
    </div>
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
        <div class="w-14 h-14 rounded-2xl bg-gray-200 text-gray-700 flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-people-group"></i></div>
        <h3 class="text-lg font-bold text-slate-900 mb-2">Lingkungan Positif</h3>
        <p class="text-sm text-gray-600 leading-relaxed">Budaya kerja kolaboratif, supportif, dan berorientasi pada pertumbuhan bersama.</p>
    </div>
</div>
</div>
</section>

<!-- STEPS -->
<section class="py-16 lg:py-20 bg-gray-50">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-2xl mx-auto mb-12">
    <span class="text-accent-600 font-bold tracking-wider text-xs uppercase">Alur Pelamaran</span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Cara Melamar <span class="gradient-text">Mudah &amp; Cepat</span></h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
<?php
$steps = [
    ['fa-magnifying-glass','from-blue-600 to-blue-800','1','Pilih Lowongan','Buka halaman lowongan dan pilih batch rekrutmen yang sedang dibuka.'],
    ['fa-file-lines','from-blue-800 to-brand-900','2','Isi Formulir','Lengkapi formulir A-I secara online (data pribadi, pendidikan, dokumen, dll).'],
    ['fa-envelope','from-accent-500 to-accent-700','3','Dapat Kode Tracking','Setelah submit, Anda menerima kode tracking via email untuk memantau status.'],
    ['fa-handshake','from-emerald-500 to-emerald-700','4','Proses Seleksi','Tim HR akan memproses lamaran Anda melalui tahap administrasi, tes, dan interview.'],
];
foreach($steps as [$icon,$grad,$num,$title,$desc]):?>
<div class="bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group">
    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?=$grad?> flex items-center justify-center text-white text-xl shadow-lg mb-4"><i class="fa-solid <?=$icon?>"></i></div>
    <div class="absolute top-6 right-6 text-5xl font-black text-gray-100 group-hover:text-brand-100 transition"><?=$num?></div>
    <h3 class="text-lg font-bold text-slate-900 mb-2"><?=$title?></h3>
    <p class="text-sm text-gray-600 leading-relaxed"><?=$desc?></p>
</div>
<?php endforeach;?>
</div>
</div>
</section>

<!-- CTA -->
<section class="py-16 lg:py-20">
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
<div class="relative overflow-hidden rounded-3xl gradient-blue p-10 lg:p-16 text-center text-white shadow-2xl">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-accent-500/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="relative z-10 max-w-2xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Siap Bergabung?</h2>
        <p class="text-white/90 text-lg mb-8">Daftarkan diri Anda sekarang dan jadilah bagian dari BPR BKK Jateng.</p>
        <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-accent-500 hover:bg-accent-600 text-white font-bold shadow-lg hover:scale-105 transition">
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
