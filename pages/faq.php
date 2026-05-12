<section class="py-16 bg-slate-50 min-h-[60vh]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-3xl">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">Frequently Asked <span class="gradient-text">Questions</span></h1>
            <p class="text-slate-600 mt-3">Jawaban cepat untuk pertanyaan yang sering diajukan kandidat.</p>
        </div>

        <div class="space-y-3" id="faq">
            <?php
            $faqs = [
                ['Bagaimana cara melamar?',                         'Pilih lowongan di halaman <strong>Lowongan</strong>, klik <em>Lamar Sekarang</em>, lengkapi formulir, lalu upload CV dalam format PDF. Kamu akan mendapat kode tracking.'],
                ['Apa format CV yang diperbolehkan?',               'Saat ini hanya menerima format <strong>PDF</strong> dengan ukuran maksimal 3MB. Hindari mengirim CV dalam bentuk foto atau screenshot.'],
                ['Berapa lama proses seleksi?',                     'Review awal dilakukan maksimal 2x24 jam. Keseluruhan proses bervariasi 1–3 minggu tergantung posisi & jumlah kandidat.'],
                ['Apakah saya bisa melamar lebih dari satu posisi?', 'Tentu bisa! Kamu dapat melamar ke beberapa posisi sekaligus, tapi pastikan profil kamu cocok dengan masing-masing posisi.'],
                ['Bagaimana cek status lamaran?',                   'Gunakan menu <strong>Cek Status</strong> dan masukkan kode tracking yang kamu terima setelah submit lamaran.'],
                ['Data saya aman?',                                 'Kami menggunakan protokol HTTPS dan tidak membagikan data kandidat ke pihak ketiga. Data hanya digunakan untuk keperluan seleksi internal.'],
                ['Bagaimana jika lupa kode tracking?',              'Silakan hubungi tim HR melalui email <strong>hr@rekrutmen.id</strong> dengan menyertakan email yang kamu gunakan saat melamar.'],
            ];
            foreach ($faqs as $i => [$q, $a]): ?>
                <details class="group bg-white rounded-2xl border border-slate-100 hover:border-brand-200 transition overflow-hidden">
                    <summary class="flex items-center justify-between gap-3 p-5 cursor-pointer font-bold text-slate-900 hover:text-brand-700">
                        <span class="flex items-start gap-3">
                            <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center text-sm shrink-0"><?= $i + 1 ?></span>
                            <span><?= $q ?></span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-sm text-slate-400 transition-transform group-open:rotate-180"></i>
                    </summary>
                    <div class="px-5 pb-5 pl-16 text-slate-600 leading-relaxed"><?= $a ?></div>
                </details>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 bg-white rounded-2xl p-6 border border-slate-100 text-center">
            <p class="text-slate-600">Masih punya pertanyaan lain?</p>
            <a href="mailto:hr@rekrutmen.id" class="mt-3 inline-flex items-center gap-2 px-6 py-3 rounded-xl gradient-brand text-white font-bold shadow-md hover:-translate-y-0.5 transition">
                <i class="fa-solid fa-envelope"></i> Hubungi HR
            </a>
        </div>
    </div>
</section>
