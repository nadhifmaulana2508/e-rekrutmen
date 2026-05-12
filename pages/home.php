<!-- HERO -->
<section class="relative overflow-hidden mesh-bg">
    <div class="absolute top-10 right-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float"></div>
    <div class="absolute bottom-10 left-10 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay:2s"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-28 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in-up">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/80 backdrop-blur border border-brand-200 text-brand-700 rounded-full text-xs font-bold mb-5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Lowongan baru setiap minggu
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6">
                    Temukan <span class="gradient-text">Karier Impian</span> Yang <br class="hidden sm:block">Sesuai Denganmu.
                </h1>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl mb-8">
                    Platform rekrutmen digital yang menghubungkan talenta terbaik dengan perusahaan unggulan. Apply cukup beberapa klik, tracking status lamaranmu kapan saja.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?= BASE_URL ?>/lowongan"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-briefcase"></i> Lihat Lowongan
                    </a>
                    <a href="<?= BASE_URL ?>/status"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white border-2 border-slate-200 text-slate-700 font-bold hover:border-brand-500 hover:text-brand-600 transition-all">
                        <i class="fa-solid fa-magnifying-glass"></i> Cek Status Lamaran
                    </a>
                </div>

                <!-- Stat mini -->
                <div class="grid grid-cols-3 gap-4 mt-10 max-w-md">
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900" id="stat-lowongan">–</p>
                        <p class="text-xs text-slate-500 font-medium">Lowongan Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">24 Jam</p>
                        <p class="text-xs text-slate-500 font-medium">Review Pertama</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">100%</p>
                        <p class="text-xs text-slate-500 font-medium">Proses Digital</p>
                    </div>
                </div>
            </div>

            <!-- Ilustrasi / card preview -->
            <div class="relative">
                <div class="relative animate-fade-in-up" style="animation-delay:0.15s">
                    <div class="bg-white rounded-3xl shadow-2xl shadow-indigo-500/20 p-6 border border-slate-100 transform rotate-1 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl gradient-brand flex items-center justify-center text-white text-lg"><i class="fa-solid fa-laptop-code"></i></div>
                            <div>
                                <p class="font-bold text-slate-900">Frontend Developer</p>
                                <p class="text-xs text-slate-500">Teknologi &bull; Jakarta Selatan</p>
                            </div>
                            <span class="ml-auto text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full">AKTIF</span>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-money-bill-wave text-emerald-500 w-4"></i> Rp 8jt - 15jt /bln</div>
                            <div class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-briefcase text-indigo-500 w-4"></i> Full Time &bull; Middle</div>
                            <div class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-user-group text-pink-500 w-4"></i> 42+ Pelamar</div>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full w-4/5 gradient-brand rounded-full"></div></div>
                        <p class="text-[11px] text-slate-400 mt-1">80% slot terisi</p>
                    </div>

                    <div class="absolute -top-4 -left-6 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 flex items-center gap-3 animate-float" style="animation-delay:1s">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fa-solid fa-check"></i></div>
                        <div>
                            <p class="text-xs text-slate-500">Lamaran Dikirim</p>
                            <p class="text-sm font-bold text-slate-900">+124 hari ini</p>
                        </div>
                    </div>

                    <div class="absolute -bottom-4 -right-4 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 flex items-center gap-3 animate-float" style="animation-delay:2.5s">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600"><i class="fa-solid fa-bolt"></i></div>
                        <div>
                            <p class="text-xs text-slate-500">Hiring Speed</p>
                            <p class="text-sm font-bold text-slate-900">7 hari rata-rata</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED LOWONGAN -->
<section class="py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-brand-600 font-bold tracking-wider text-xs uppercase">Hot Jobs</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Lowongan Terbaru <span class="gradient-text">Minggu Ini</span></h2>
            </div>
            <a href="<?= BASE_URL ?>/lowongan" class="hidden sm:inline-flex items-center gap-2 text-brand-600 font-semibold hover:gap-3 transition-all">
                Lihat semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div id="featured-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Skeleton -->
            <div class="animate-pulse bg-slate-100 h-60 rounded-2xl"></div>
            <div class="animate-pulse bg-slate-100 h-60 rounded-2xl hidden sm:block"></div>
            <div class="animate-pulse bg-slate-100 h-60 rounded-2xl hidden lg:block"></div>
        </div>

        <div class="mt-8 sm:hidden">
            <a href="<?= BASE_URL ?>/lowongan" class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl border-2 border-slate-200 font-bold text-slate-700 hover:border-brand-500 hover:text-brand-600">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-16 lg:py-20 bg-slate-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-brand-600 font-bold tracking-wider text-xs uppercase">Alur Pelamaran</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Cara Melamar Hanya <span class="gradient-text">4 Langkah</span></h2>
            <p class="text-slate-600 mt-4">Proses rekrutmen yang modern, transparan, dan fully digital dari rumah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $steps = [
                ['fa-magnifying-glass', 'from-indigo-500 to-purple-500',  '1', 'Pilih Lowongan',    'Jelajahi lowongan aktif yang sesuai dengan skill & minat kamu.'],
                ['fa-file-lines',       'from-purple-500 to-pink-500',    '2', 'Isi Formulir',      'Lengkapi data diri, upload CV (PDF), & foto dalam 1 halaman.'],
                ['fa-handshake',        'from-pink-500 to-orange-500',    '3', 'Proses Seleksi',     'Tim HR akan review & menghubungi jika kamu lolos tahap awal.'],
                ['fa-party-horn',       'from-emerald-500 to-teal-500',   '4', 'Offering',          'Selamat bergabung! Negosiasi offering & mulai karier barumu.'],
            ];
            foreach ($steps as [$icon, $grad, $num, $title, $desc]): ?>
                <div class="bg-white rounded-2xl p-6 border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?= $grad ?> flex items-center justify-center text-white text-xl shadow-lg shadow-indigo-500/20 mb-4">
                        <i class="fa-solid <?= $icon ?>"></i>
                    </div>
                    <div class="absolute top-6 right-6 text-5xl font-black text-slate-100 group-hover:text-brand-100 transition"><?= $num ?></div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2"><?= $title ?></h3>
                    <p class="text-sm text-slate-600 leading-relaxed"><?= $desc ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WHY US -->
<section class="py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <div class="grid grid-cols-2 gap-4">
                    <?php
                    $feats = [
                        ['fa-shield-heart', 'bg-emerald-100 text-emerald-600', 'Proses Aman',       'Data kamu terenkripsi & tidak dibagikan ke pihak ketiga.'],
                        ['fa-bolt',         'bg-indigo-100 text-indigo-600',   'Super Cepat',       'Review awal maksimal 2x24 jam sejak lamaran masuk.'],
                        ['fa-mobile-screen','bg-pink-100 text-pink-600',       'Mobile Friendly',   'Lamar dari HP, tablet, atau desktop sama mulusnya.'],
                        ['fa-chart-line',   'bg-amber-100 text-amber-600',     'Tracking Real-time','Cek status lamaran kapan saja dengan kode tracking.'],
                    ];
                    foreach ($feats as [$ic, $cls, $t, $d]): ?>
                        <div class="bg-slate-50 rounded-2xl p-5 hover:shadow-md transition">
                            <div class="w-11 h-11 rounded-xl <?= $cls ?> flex items-center justify-center text-lg mb-3"><i class="fa-solid <?= $ic ?>"></i></div>
                            <p class="font-bold text-slate-900"><?= $t ?></p>
                            <p class="text-xs text-slate-500 leading-relaxed mt-1"><?= $d ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <span class="text-brand-600 font-bold tracking-wider text-xs uppercase">Keunggulan Platform</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 mb-5">
                    Kenapa Memilih <br><span class="gradient-text">Platform Kami?</span>
                </h2>
                <p class="text-slate-600 leading-relaxed mb-6">
                    Kami berkomitmen membangun pengalaman melamar kerja yang bebas ribet. Mulai dari antarmuka yang intuitif, proses pendaftaran sekali klik, hingga tracking status yang transparan.
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span class="text-slate-700">Verifikasi lamaran otomatis via email</span></li>
                    <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span class="text-slate-700">Notifikasi perubahan status lamaran</span></li>
                    <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check text-emerald-500 mt-1"></i><span class="text-slate-700">Dashboard admin HR untuk manajemen kandidat</span></li>
                </ul>
                <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl gradient-brand text-white font-bold shadow-lg shadow-indigo-500/30 hover:-translate-y-0.5 transition">
                    Mulai Lamar <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 lg:py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl gradient-brand p-10 lg:p-16 text-center text-white shadow-2xl shadow-indigo-500/30">
            <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.3) 0, transparent 50%), radial-gradient(circle at 80% 80%, rgba(255,255,255,0.3) 0, transparent 50%);"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Siap Memulai Karier Baru?</h2>
                <p class="text-white/90 text-base sm:text-lg mb-8">Daftar sekarang dan jadi bagian dari tim-tim hebat di perusahaan pilihan.</p>
                <a href="<?= BASE_URL ?>/lowongan" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white text-brand-700 font-bold shadow-lg hover:scale-105 transition">
                    Jelajahi Lowongan <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
(async function () {
    const container = document.getElementById('featured-list');
    const statEl    = document.getElementById('stat-lowongan');

    const res = await APP.api('/lowongan');
    if (res.status === 200 && Array.isArray(res.data)) {
        statEl.textContent = res.data.length;
        const top = res.data.slice(0, 6);
        if (top.length === 0) {
            container.innerHTML = `<div class="col-span-full text-center py-12 bg-slate-50 rounded-2xl">
                <i class="fa-solid fa-briefcase text-4xl text-slate-300 mb-3"></i>
                <p class="text-slate-500 font-medium">Belum ada lowongan aktif. Silakan kembali nanti ya!</p>
            </div>`;
            return;
        }
        container.innerHTML = top.map(renderCard).join('');
    } else {
        statEl.textContent = '0';
        container.innerHTML = `<div class="col-span-full text-center py-12 bg-rose-50 rounded-2xl text-rose-600">
            <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
            <p class="font-medium">${res.message || 'Gagal memuat lowongan'}</p>
        </div>`;
    }

    function renderCard(j) {
        const gradients = ['from-indigo-500 to-purple-500','from-pink-500 to-rose-500','from-emerald-500 to-teal-500','from-amber-500 to-orange-500','from-sky-500 to-blue-500','from-fuchsia-500 to-pink-500'];
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
                <span class="text-xs text-slate-500">Deadline <span class="font-semibold text-slate-700">${deadline}</span></span>
            </div>
        </a>`;
    }
})();
</script>
