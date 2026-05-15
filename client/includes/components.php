<?php
/**
 * Render sidebar admin panel
 * - Push layout: toggle buka/tutup dengan tombol
 * - Content area ikut mengecil/melebar saat sidebar dibuka/ditutup
 */
function renderSidebar(string $page, array $user): void {
    $base = BASE_URL . '/client';
    $menu = [
        ['dashboard',     'Dashboard',       'fa-chart-line'],
        ['lowongan',      'Lowongan',        'fa-briefcase'],
        ['form_lowongan', 'Tambah Lowongan', 'fa-plus'],
        ['pelamar',       'Pelamar',         'fa-user-group'],
    ];
    $initial = strtoupper(substr($user['nama'] ?? $user['username'] ?? 'A', 0, 1));
    ?>
    <!-- Mobile overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden lg:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 bg-white border-r border-slate-200 flex flex-col transform -translate-x-full lg:translate-x-0 transition-all duration-300 overflow-hidden w-72 lg:w-[260px]" data-expanded="true">
        <!-- Brand -->
        <div class="h-16 px-4 flex items-center gap-3 border-b border-slate-100 shrink-0">
            <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center text-white shadow-md shrink-0">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div class="sidebar-label whitespace-nowrap transition-opacity duration-200">
                <p class="font-extrabold text-slate-900 leading-tight">BPR BKK Jateng</p>
                <p class="text-[11px] text-slate-500 font-medium">Admin Panel</p>
            </div>
        </div>

        <!-- Toggle button (desktop) -->
        <button id="sidebar-toggle-btn" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 absolute top-4 -right-4 z-50 shadow-md border border-slate-200 transition-all" title="Toggle Sidebar">
            <i class="fa-solid fa-chevron-left text-xs" id="toggle-icon"></i>
        </button>

        <!-- Menu -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            <p class="sidebar-label text-[10px] uppercase tracking-wider text-slate-400 font-bold px-3 mb-2">Menu Utama</p>
            <?php foreach ($menu as [$key, $label, $icon]):
                $active = $page === $key || ($key === 'pelamar' && $page === 'pelamar_detail');
            ?>
                <a href="<?= $base ?>/<?= $key ?>" title="<?= $label ?>"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                          <?= $active ? 'bg-brand-600 text-white shadow-md shadow-indigo-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <i class="fa-solid <?= $icon ?> w-5 text-center shrink-0 <?= $active ? '' : 'text-slate-400' ?>"></i>
                    <span class="sidebar-label whitespace-nowrap"><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- User card -->
        <div class="p-3 border-t border-slate-100 shrink-0">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-50">
                <div class="w-10 h-10 rounded-full gradient-brand flex items-center justify-center text-white font-bold shrink-0"><?= $initial ?></div>
                <div class="sidebar-label flex-1 min-w-0">
                    <p id="sidebarUserName" class="font-bold text-sm text-slate-900 truncate"><?= htmlspecialchars($user['nama'] ?? $user['username'] ?? 'User') ?></p>
                    <p id="sidebarUserUnit" class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($user['unit_kerja'] ?? $user['email'] ?? $user['role'] ?? '') ?></p>
                </div>
                <a href="#" onclick="logoutSSO(event)" class="sidebar-label w-9 h-9 rounded-lg bg-white hover:bg-rose-50 hover:text-rose-600 text-slate-500 flex items-center justify-center transition shrink-0" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>
    <?php
}

/**
 * Render top header (mobile friendly)
 */
function renderTopbar(string $title, string $subtitle = ''): void {
    ?>
    <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center gap-3 sticky top-0 z-20">
        <button id="toggle-sidebar" class="lg:hidden w-10 h-10 rounded-lg hover:bg-slate-100 flex items-center justify-center">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="flex-1 min-w-0">
            <h1 class="font-extrabold text-slate-900 text-lg leading-tight truncate"><?= htmlspecialchars($title) ?></h1>
            <?php if ($subtitle): ?><p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($subtitle) ?></p><?php endif; ?>
        </div>
        <a href="<?= BASE_URL ?>" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm font-semibold text-slate-700">
            <i class="fa-solid fa-up-right-from-square"></i> Lihat Situs
        </a>
    </header>
    <?php
}
