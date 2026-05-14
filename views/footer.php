<footer class="bg-slate-900 text-slate-300 pt-16 pb-8 mt-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">

        <!-- Brand -->
        <div class="md:col-span-2">
            <a href="<?= BASE_URL ?>/home" class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-xl gradient-blue flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-building-columns text-lg"></i>
                </div>
                <div>
                    <p class="font-extrabold text-white leading-none text-lg">BPR BKK Jateng</p>
                    <p class="text-xs text-slate-400 font-medium">E-Form Rekrutmen Pegawai</p>
                </div>
            </a>
            <p class="text-sm leading-relaxed text-slate-400 max-w-md">
                Platform rekrutmen digital PT BPR BKK Jateng (Perseroda) untuk mempertemukan talenta terbaik Jawa Tengah dengan peluang karier di dunia perbankan.
            </p>

            <div class="flex items-center gap-3 mt-5">
                <a href="#" aria-label="Instagram" class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"  class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#" aria-label="Twitter"   class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" aria-label="YouTube"   class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

        <!-- Navigasi -->
        <div>
            <h3 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">Jelajahi</h3>
            <ul class="space-y-3 text-sm">
                <li><a href="<?= BASE_URL ?>/home"     class="hover:text-white inline-flex items-center gap-2 transition"><i class="fa-solid fa-chevron-right text-[10px] text-accent-500"></i>Home</a></li>
                <li><a href="<?= BASE_URL ?>/lowongan" class="hover:text-white inline-flex items-center gap-2 transition"><i class="fa-solid fa-chevron-right text-[10px] text-accent-500"></i>Daftar Lowongan</a></li>
                <li><a href="<?= BASE_URL ?>/status"   class="hover:text-white inline-flex items-center gap-2 transition"><i class="fa-solid fa-chevron-right text-[10px] text-accent-500"></i>Cek Status Lamaran</a></li>
                <li><a href="<?= BASE_URL ?>/faq"      class="hover:text-white inline-flex items-center gap-2 transition"><i class="fa-solid fa-chevron-right text-[10px] text-accent-500"></i>FAQ</a></li>
            </ul>
        </div>

        <!-- Kontak -->
        <div>
            <h3 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">Kontak</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-envelope text-accent-500 mt-1"></i>
                    <span>hr@bprbkkjateng.co.id</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-phone text-accent-500 mt-1"></i>
                    <span>(024) 5000-1234</span>
                </li>
                <li class="flex items-start gap-3">
                    <i class="fa-solid fa-location-dot text-accent-500 mt-1"></i>
                    <span>Jl. Pemuda No.142,<br>Kota Semarang, Jawa Tengah</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-slate-500">&copy; <?= date('Y') ?> <span class="font-semibold text-slate-300">PT BPR BKK Jateng (Perseroda)</span>. All Rights Reserved.</p>
        <p class="text-xs text-slate-500">Made with <i class="fa-solid fa-heart text-accent-500"></i> in Jawa Tengah</p>
    </div>
</footer>

</body>
</html>
