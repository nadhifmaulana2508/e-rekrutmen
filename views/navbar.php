<?php
$current = basename($_GET['url'] ?? 'home');
$navItems = [
    'home'     => ['Home',      'fa-house'],
    'lowongan' => ['Lowongan',   'fa-briefcase'],
    'status'   => ['Cek Status', 'fa-magnifying-glass'],
    'faq'      => ['FAQ',        'fa-circle-question'],
];
?>
<nav id="navbar" class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200/60 transition-all duration-300">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">

            <!-- Logo -->
            <a href="<?= BASE_URL ?>/home" class="flex items-center gap-3 group">
                <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl gradient-brand flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-user-tie text-lg"></i>
                </div>
                <div class="hidden sm:block">
                    <p class="font-extrabold text-slate-900 leading-none text-lg">Rekrutmen</p>
                    <p class="text-[11px] text-slate-500 font-medium">Karier Impianmu Dimulai Di Sini</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-1">
                <?php foreach ($navItems as $key => [$label, $icon]): 
                    $active = $current === $key || ($key === 'home' && in_array($current, ['', 'home']));
                ?>
                    <a href="<?= BASE_URL ?>/<?= $key ?>"
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2
                              <?= $active ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                        <i class="fa-solid <?= $icon ?> text-xs"></i>
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Right CTA -->
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>/lowongan"
                   class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fa-solid fa-rocket"></i>
                    Lamar Sekarang
                </a>

                <!-- Mobile toggle -->
                <button id="mobile-toggle" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-700 transition" aria-label="Menu">
                    <i id="icon-bar" class="fa-solid fa-bars text-xl"></i>
                    <i id="icon-x" class="fa-solid fa-xmark text-xl hidden"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="lg:hidden hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-4 space-y-1">
            <?php foreach ($navItems as $key => [$label, $icon]): 
                $active = $current === $key || ($key === 'home' && in_array($current, ['', 'home']));
            ?>
                <a href="<?= BASE_URL ?>/<?= $key ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                          <?= $active ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-100' ?>">
                    <i class="fa-solid <?= $icon ?> w-5 text-center"></i>
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
            <a href="<?= BASE_URL ?>/lowongan"
               class="flex items-center gap-3 px-4 py-3 rounded-xl gradient-brand text-white font-bold text-sm mt-3 shadow-md shadow-indigo-500/30">
                <i class="fa-solid fa-rocket w-5 text-center"></i>
                Lamar Sekarang
            </a>
        </div>
    </div>
</nav>

<script>
(function () {
    const btn   = document.getElementById('mobile-toggle');
    const menu  = document.getElementById('mobile-menu');
    const iBar  = document.getElementById('icon-bar');
    const iX    = document.getElementById('icon-x');
    const nav   = document.getElementById('navbar');

    btn?.addEventListener('click', () => {
        menu.classList.toggle('hidden');
        iBar.classList.toggle('hidden');
        iX.classList.toggle('hidden');
    });

    // Shadow on scroll
    const onScroll = () => {
        if (window.scrollY > 8) nav.classList.add('shadow-md');
        else nav.classList.remove('shadow-md');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();
</script>
