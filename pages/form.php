<?php $id = (int)($_GET['id'] ?? 0); ?>
<section class="py-8 bg-slate-50 min-h-screen">
<div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
<a href="<?= BASE_URL ?>/detail/<?= $id ?>" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-600 font-semibold mb-6"><i class="fa-solid fa-arrow-left"></i> Kembali</a>

<!-- WIZARD HEADER -->
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6">
<div class="p-6 gradient-brand text-white">
    <div class="flex items-center gap-3 mb-2">
        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/9/9e/Logo_BPR_BKK.png/220px-Logo_BPR_BKK.png" alt="Logo" class="h-12 bg-white rounded-lg p-1" onerror="this.style.display='none'">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold">E-Form Lamaran Kerja</h1>
            <p class="text-white/80 text-sm">PT BPR BKK Jateng (Perseroda)</p>
        </div>
    </div>
</div>
<!-- Step indicator -->
<div class="px-6 py-4 border-b border-slate-100 overflow-x-auto">
    <div class="flex items-center gap-1 min-w-max" id="step-indicator"></div>
</div>
</div>

<form id="apply-form" enctype="multipart/form-data">
<input type="hidden" name="id_lowongan" value="<?= $id ?>">

<!-- SECTION A: POSISI -->
<div class="form-section" data-step="0">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">A</span> Posisi yang Dilamar</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div>
    <label class="label">Posisi yang Dilamar <span class="text-rose-500">*</span></label>
    <select name="posisi_dilamar" required class="input" id="sel-posisi"><option value="">-- Pilih Posisi --</option></select>
</div>
<div>
    <label class="label">Penempatan <span class="text-rose-500">*</span></label>
    <select name="penempatan" required class="input" id="sel-penempatan"><option value="">-- Pilih Penempatan --</option></select>
</div>
<div>
    <label class="label">Bersedia ditempatkan di seluruh wilayah kerja? <span class="text-rose-500">*</span></label>
    <select name="bersedia_seluruh_wilayah" required class="input">
        <option value="Ya">Ya</option><option value="Tidak">Tidak</option>
    </select>
</div>
<div>
    <label class="label">Sumber Informasi Lowongan <span class="text-rose-500">*</span></label>
    <select name="sumber_informasi" class="input" id="sel-sumber">
        <option value="">-- Pilih --</option>
        <option value="Website Perusahaan">Website Perusahaan</option>
        <option value="Instagram">Instagram</option>
        <option value="Facebook">Facebook</option>
        <option value="Job Portal">Job Portal</option>
        <option value="Referensi Pegawai">Referensi Pegawai</option>
        <option value="Lainnya">Lainnya</option>
    </select>
</div>
<div id="wrap-sumber-lain" class="hidden">
    <label class="label">Sebutkan sumber lainnya</label>
    <input type="text" name="sumber_informasi_lainnya" class="input">
</div>
<div>
    <label class="label">Ekspektasi Gaji (Rp)</label>
    <input type="number" name="ekspektasi_gaji" class="input" placeholder="5000000">
</div>
<div>
    <label class="label">Ketersediaan Mulai Bekerja <span class="text-rose-500">*</span></label>
    <select name="ketersediaan_mulai" class="input" id="sel-mulai">
        <option value="">-- Pilih --</option>
        <option value="Segera">Segera</option>
        <option value="1 Minggu">1 Minggu</option>
        <option value="2 Minggu">2 Minggu</option>
        <option value="1 Bulan">1 Bulan</option>
        <option value="Lainnya">Lainnya</option>
    </select>
</div>
<div id="wrap-mulai-lain" class="hidden">
    <label class="label">Sebutkan</label>
    <input type="text" name="ketersediaan_mulai_lainnya" class="input">
</div>
</div>
</div>
</div>

<!-- SECTION B: DATA PRIBADI -->
<div class="form-section hidden" data-step="1">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">B</span> Data Pribadi</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div class="sm:col-span-2"><label class="label">Nama Lengkap sesuai KTP <span class="text-rose-500">*</span></label><input type="text" name="nama_lengkap" required class="input"></div>
<div><label class="label">Nama Panggilan</label><input type="text" name="nama_panggilan" class="input"></div>
<div><label class="label">Jenis Kelamin <span class="text-rose-500">*</span></label>
    <select name="jenis_kelamin" required class="input"><option value="">-- Pilih --</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
<div><label class="label">Tempat Lahir <span class="text-rose-500">*</span></label><input type="text" name="tempat_lahir" required class="input"></div>
<div><label class="label">Tanggal Lahir <span class="text-rose-500">*</span></label><input type="date" name="tanggal_lahir" required class="input"></div>
<div><label class="label">Status Pernikahan</label>
    <select name="status_pernikahan" class="input"><option value="">-- Pilih --</option><option value="Belum Menikah">Belum Menikah</option><option value="Menikah">Menikah</option><option value="Cerai">Cerai</option></select></div>
<div><label class="label">Agama</label><input type="text" name="agama" class="input"></div>
<div><label class="label">Kewarganegaraan</label><input type="text" name="kewarganegaraan" class="input" value="Indonesia"></div>
<div><label class="label">Nomor KTP/NIK <span class="text-rose-500">*</span></label><input type="text" name="nomor_ktp" required class="input" maxlength="16" pattern="[0-9]{16}" title="16 digit angka"></div>
<div><label class="label">NPWP</label><input type="text" name="npwp" class="input"></div>
<div class="sm:col-span-2"><label class="label">Alamat sesuai KTP <span class="text-rose-500">*</span></label><textarea name="alamat_ktp" required rows="2" class="input"></textarea></div>
<div class="sm:col-span-2"><label class="label">Alamat Domisili Saat Ini <span class="text-rose-500">*</span></label><textarea name="alamat_domisili" required rows="2" class="input"></textarea></div>
<div><label class="label">Nomor HP/WA <span class="text-rose-500">*</span></label><input type="tel" name="no_hp" required class="input" placeholder="08xxx"></div>
<div><label class="label">Email Aktif <span class="text-rose-500">*</span></label><input type="email" name="email" required class="input"></div>
<div><label class="label">LinkedIn / Media Sosial</label><input type="text" name="akun_linkedin" class="input" placeholder="https://linkedin.com/in/..."></div>
<div>
    <label class="label">Foto 3x4 <span class="text-rose-500">*</span></label>
    <input type="file" name="foto_3x4" accept="image/*" class="input file-input">
    <p class="text-xs text-slate-400 mt-1">Pas foto berwarna, maks 2MB</p>
</div>
<div>
    <label class="label">Foto Full Body</label>
    <input type="file" name="foto_full_body" accept="image/*" class="input file-input">
    <p class="text-xs text-slate-400 mt-1">Foto seluruh badan, maks 2MB</p>
</div>
</div>
</div>
</div>


<!-- SECTION C: PENDIDIKAN -->
<div class="form-section hidden" data-step="2">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">C</span> Pendidikan</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div><label class="label">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
    <select name="pendidikan_terakhir" required class="input" id="sel-pend">
        <option value="">-- Pilih --</option><option value="SMA/SMK">SMA/SMK</option><option value="D3">D3</option><option value="S1">S1</option><option value="S2">S2</option><option value="Lainnya">Lainnya</option>
    </select></div>
<div id="wrap-pend-lain" class="hidden"><label class="label">Sebutkan</label><input type="text" name="pendidikan_lainnya" class="input"></div>
<div><label class="label">Nama Sekolah/Universitas <span class="text-rose-500">*</span></label><input type="text" name="nama_institusi" required class="input"></div>
<div><label class="label">Jurusan <span class="text-rose-500">*</span></label><input type="text" name="jurusan" required class="input"></div>
<div><label class="label">Tahun Masuk</label><input type="number" name="tahun_masuk" class="input" min="1990" max="2030"></div>
<div><label class="label">Tahun Lulus <span class="text-rose-500">*</span></label><input type="number" name="tahun_lulus" required class="input" min="1990" max="2030"></div>
<div><label class="label">IPK / Nilai Akhir</label><input type="text" name="ipk" class="input" placeholder="3.50"></div>
<div class="sm:col-span-2"><label class="label">Prestasi Akademik / Non Akademik</label><textarea name="prestasi" rows="2" class="input" placeholder="Juara 1 Lomba..., Beasiswa..."></textarea></div>
</div>
</div>
</div>

<!-- SECTION D: PENGALAMAN KERJA -->
<div class="form-section hidden" data-step="3">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">D</span> Pengalaman Kerja</h2>
<p class="text-sm text-slate-500 mb-4">Isi jika memiliki pengalaman kerja (boleh lebih dari 1). Kosongkan jika fresh graduate.</p>
<div id="exp-container"></div>
<button type="button" id="btn-add-exp" class="mt-3 px-4 py-2 rounded-xl bg-brand-50 text-brand-700 font-bold text-sm hover:bg-brand-100 transition">
    <i class="fa-solid fa-plus mr-1"></i> Tambah Pengalaman
</button>
</div>
</div>

<!-- SECTION E: KEMAMPUAN -->
<div class="form-section hidden" data-step="4">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">E</span> Kemampuan &amp; Kompetensi</h2>
<div class="space-y-4">
<div>
    <label class="label">Kemampuan Komputer <span class="text-rose-500">*</span> (pilih semua yang dikuasai)</label>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Microsoft Word" class="accent-brand-600"> Microsoft Word</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Microsoft Excel" class="accent-brand-600"> Microsoft Excel</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Microsoft PowerPoint" class="accent-brand-600"> Microsoft PowerPoint</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Sistem Perbankan" class="accent-brand-600"> Sistem Perbankan</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Core Banking" class="accent-brand-600"> Core Banking</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Desain Grafis" class="accent-brand-600"> Desain Grafis</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Programming / IT" class="accent-brand-600"> Programming / IT</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="kemampuan_komputer[]" value="Lainnya" class="accent-brand-600" id="chk-komputer-lain"> Lainnya</label>
    </div>
    <input type="text" name="kemampuan_komputer_lainnya" class="input mt-2 hidden" id="inp-komputer-lain" placeholder="Sebutkan...">
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div><label class="label">Bahasa Asing</label><input type="text" name="bahasa_asing" class="input" placeholder="Inggris (aktif), Mandarin (pasif)"></div>
<div><label class="label">Sertifikasi / Pelatihan</label><textarea name="sertifikasi" rows="2" class="input" placeholder="Sertifikat Perbankan Lv.1, Training Sales..."></textarea></div>
<div><label class="label">Keahlian Khusus</label><textarea name="keahlian_khusus" rows="2" class="input" placeholder="Analisa kredit, public speaking..."></textarea></div>
<div><label class="label">Memiliki SIM</label>
    <div class="flex gap-4 mt-2">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="sim[]" value="SIM A" class="accent-brand-600"> SIM A</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="sim[]" value="SIM C" class="accent-brand-600"> SIM C</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="sim[]" value="Tidak Ada" class="accent-brand-600"> Tidak Ada</label>
    </div>
</div>
</div>
</div>
</div>
</div>


<!-- SECTION F: KHUSUS POSISI -->
<div class="form-section hidden" data-step="5">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">F</span> Khusus Posisi Tertentu</h2>
<p class="text-sm text-slate-500 mb-4">Isi sesuai posisi yang Anda lamar. Lewati jika tidak relevan.</p>

<div id="khusus-ao" class="mb-6 p-4 bg-amber-50 rounded-xl border border-amber-100 hidden">
<h3 class="font-bold text-sm text-amber-800 mb-3"><i class="fa-solid fa-user-tie mr-1"></i> Untuk AO Dana / AO Kredit / AO Remedial</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div><label class="label text-xs">Pengalaman marketing/lending?</label><select name="khusus_pengalaman_marketing" class="input"><option value="">-</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
<div><label class="label text-xs">Memiliki relasi/networking bisnis?</label><select name="khusus_relasi_bisnis" class="input"><option value="">-</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
<div><label class="label text-xs">Bersedia bekerja dengan target?</label><select name="khusus_bersedia_target" class="input"><option value="">-</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
<div><label class="label text-xs">Memiliki kendaraan pribadi?</label><select name="khusus_kendaraan_pribadi" class="input"><option value="">-</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
</div>
</div>

<div id="khusus-it" class="mb-6 p-4 bg-sky-50 rounded-xl border border-sky-100 hidden">
<h3 class="font-bold text-sm text-sky-800 mb-3"><i class="fa-solid fa-laptop-code mr-1"></i> Untuk Staf IT</h3>
<div class="space-y-3">
<div><label class="label text-xs">Bidang yang dikuasai</label>
    <div class="flex flex-wrap gap-3 mt-1">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="khusus_it_bidang[]" value="Development" class="accent-brand-600"> Development</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="khusus_it_bidang[]" value="Networking" class="accent-brand-600"> Networking</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="khusus_it_bidang[]" value="Cyber Security" class="accent-brand-600"> Cyber Security</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="khusus_it_bidang[]" value="Database" class="accent-brand-600"> Database</label>
    </div>
</div>
<div><label class="label text-xs">Bahasa Pemrograman yang Dikuasai</label><input type="text" name="khusus_it_bahasa_pemrograman" class="input" placeholder="PHP, Python, JavaScript..."></div>
</div>
</div>

<div id="khusus-analis" class="p-4 bg-purple-50 rounded-xl border border-purple-100 hidden">
<h3 class="font-bold text-sm text-purple-800 mb-3"><i class="fa-solid fa-chart-pie mr-1"></i> Untuk Analis Kredit &amp; Appraisal</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div><label class="label text-xs">Memahami analisa laporan keuangan?</label><select name="khusus_analis_lapkeu" class="input"><option value="">-</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
<div><label class="label text-xs">Memahami appraisal/jaminan kredit?</label><select name="khusus_analis_appraisal" class="input"><option value="">-</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option></select></div>
</div>
</div>
</div>
</div>

<!-- SECTION G: INFORMASI TAMBAHAN -->
<div class="form-section hidden" data-step="6">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">G</span> Informasi Tambahan</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div><label class="label">Pernah terlibat kasus hukum? <span class="text-rose-500">*</span></label>
    <select name="pernah_kasus_hukum" required class="input"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></select></div>
<div><label class="label">Hubungan keluarga dengan pegawai? <span class="text-rose-500">*</span></label>
    <select name="hubungan_keluarga_pegawai" required class="input" id="sel-keluarga"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></select></div>
<div id="wrap-keluarga" class="hidden sm:col-span-2"><label class="label">Jika Ya, sebutkan</label><input type="text" name="hubungan_keluarga_detail" class="input" placeholder="Nama, hubungan, jabatan..."></div>
<div><label class="label">Status SLIK/BI Checking</label>
    <select name="status_slik" class="input"><option value="">-- Pilih --</option><option value="Lancar">Lancar</option><option value="Tidak Lancar">Tidak Lancar</option></select></div>
<div><label class="label">Riwayat penyakit tertentu?</label>
    <select name="riwayat_penyakit" class="input" id="sel-penyakit"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></select></div>
<div id="wrap-penyakit" class="hidden sm:col-span-2"><label class="label">Jika Ya, jelaskan</label><textarea name="riwayat_penyakit_detail" rows="2" class="input"></textarea></div>
</div>
</div>
</div>

<!-- SECTION H: DOKUMEN -->
<div class="form-section hidden" data-step="7">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">H</span> Dokumen Unggah</h2>
<p class="text-sm text-slate-500 mb-4">Format: PDF atau gambar (JPG/PNG). Maksimal 5MB per file.</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div><label class="label">Surat Lamaran <span class="text-rose-500">*</span></label><input type="file" name="dokumen_surat_lamaran" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">CV / Daftar Riwayat Hidup <span class="text-rose-500">*</span></label><input type="file" name="dokumen_cv" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">KTP <span class="text-rose-500">*</span></label><input type="file" name="dokumen_ktp" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">Kartu Keluarga (KK) <span class="text-rose-500">*</span></label><input type="file" name="dokumen_kk" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">Ijazah Terakhir <span class="text-rose-500">*</span></label><input type="file" name="dokumen_ijazah" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">Transkrip Nilai <span class="text-rose-500">*</span></label><input type="file" name="dokumen_transkrip" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">Surat Keterangan Sehat <span class="text-rose-500">*</span></label><input type="file" name="dokumen_surat_sehat" accept=".pdf,.jpg,.jpeg,.png" required class="input file-input"></div>
<div><label class="label">Sertifikat Pendukung</label><input type="file" name="dokumen_sertifikat" accept=".pdf,.jpg,.jpeg,.png" class="input file-input"></div>
<div><label class="label">Surat Keterangan Kerja</label><input type="file" name="dokumen_surat_kerja" accept=".pdf,.jpg,.jpeg,.png" class="input file-input"></div>
<div><label class="label">Portfolio</label><input type="file" name="dokumen_portfolio" accept=".pdf,.jpg,.jpeg,.png" class="input file-input"></div>
</div>
</div>
</div>


<!-- SECTION I: PERNYATAAN -->
<div class="form-section hidden" data-step="8">
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-4">
<h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-5"><span class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center text-sm font-bold">I</span> Pernyataan</h2>
<div class="space-y-3 mb-6">
    <label class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition">
        <input type="checkbox" name="pernyataan_data_benar" value="1" required class="mt-1 accent-brand-600">
        <span class="text-sm text-slate-700">Saya menyatakan bahwa seluruh data yang saya isi adalah <strong>benar</strong> dan dapat dipertanggungjawabkan.</span>
    </label>
    <label class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition">
        <input type="checkbox" name="pernyataan_ikut_proses" value="1" required class="mt-1 accent-brand-600">
        <span class="text-sm text-slate-700">Saya bersedia mengikuti <strong>seluruh proses rekrutmen</strong> sesuai ketentuan perusahaan.</span>
    </label>
    <label class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition">
        <input type="checkbox" name="pernyataan_setuju_data" value="1" required class="mt-1 accent-brand-600">
        <span class="text-sm text-slate-700">Saya menyetujui <strong>penggunaan data pribadi</strong> untuk proses rekrutmen.</span>
    </label>
</div>
<div class="bg-brand-50 border border-brand-100 rounded-xl p-4">
    <p class="text-xs text-brand-800 font-semibold mb-2">Tanda Tangan Digital</p>
    <p class="text-sm text-slate-700">Dengan menekan tombol <strong>Kirim Lamaran</strong>, Anda dianggap telah menandatangani formulir ini secara digital.</p>
    <p class="text-sm text-slate-500 mt-2">Tanggal pengisian: <strong id="tgl-isi"></strong></p>
</div>
</div>
</div>

<!-- NAV BUTTONS -->
<div class="flex justify-between items-center mt-4 mb-8">
    <button type="button" id="btn-prev" class="hidden px-6 py-3 rounded-xl bg-white border border-slate-200 font-bold text-sm text-slate-700 hover:bg-slate-100 transition">
        <i class="fa-solid fa-arrow-left mr-1"></i> Sebelumnya
    </button>
    <div class="flex-1"></div>
    <button type="button" id="btn-next" class="px-6 py-3 rounded-xl gradient-brand text-white font-bold text-sm shadow-lg hover:-translate-y-0.5 transition">
        Selanjutnya <i class="fa-solid fa-arrow-right ml-1"></i>
    </button>
    <button type="submit" id="btn-submit" class="hidden px-6 py-3 rounded-xl gradient-brand text-white font-bold text-sm shadow-lg hover:-translate-y-0.5 transition">
        <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Lamaran
    </button>
</div>
</form>
</div>
</section>

<!-- SUCCESS MODAL -->
<div id="success-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur z-[9998] hidden items-center justify-center p-4">
<div class="bg-white rounded-3xl max-w-md w-full p-8 text-center animate-fade-in-up">
    <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-5 text-4xl"><i class="fa-solid fa-circle-check"></i></div>
    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Lamaran Terkirim!</h2>
    <p class="text-slate-600 text-sm mb-2">Terima kasih telah melamar di PT BPR BKK Jateng.</p>
    <p class="text-slate-600 text-sm mb-4">Simpan kode tracking berikut untuk memantau status lamaran Anda:</p>
    <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl p-4 mb-4">
        <p class="text-xs text-slate-500 font-semibold mb-1">KODE TRACKING</p>
        <p id="kode-tracking" class="text-xl font-black gradient-text tracking-wider"></p>
    </div>
    <p class="text-xs text-slate-500 mb-4">Kode ini juga telah dikirim ke email Anda.</p>
    <div class="flex flex-col sm:flex-row gap-2">
        <button id="copy-kode" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition text-sm"><i class="fa-regular fa-copy mr-1"></i> Salin Kode</button>
        <a href="<?= BASE_URL ?>/status" class="flex-1 py-3 rounded-xl gradient-brand text-white font-bold text-sm text-center">Cek Status</a>
    </div>
</div>
</div>

<style>
.label{display:block;font-size:.8125rem;font-weight:600;color:#334155;margin-bottom:.375rem}
.input{width:100%;padding:.625rem .875rem;border-radius:.625rem;background:#f8fafc;border:1px solid #e2e8f0;font-size:.875rem;transition:all .2s}
.input:focus{outline:none;background:#fff;border-color:#6366f1;box-shadow:0 0 0 3px #e0e7ff}
.file-input{padding:.5rem}
</style>


<script>
(async function(){
const id = <?= $id ?>;
if(!id){location.href=APP.baseUrl+'/lowongan';return;}

const steps=['Posisi','Data Pribadi','Pendidikan','Pengalaman','Kemampuan','Khusus Posisi','Info Tambahan','Dokumen','Pernyataan'];
const sections=document.querySelectorAll('.form-section');
const indicator=document.getElementById('step-indicator');
const btnPrev=document.getElementById('btn-prev');
const btnNext=document.getElementById('btn-next');
const btnSubmit=document.getElementById('btn-submit');
let current=0;

// Build step indicator
indicator.innerHTML=steps.map((s,i)=>`
<div class="flex items-center gap-1 step-item" data-i="${i}">
    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition ${i===0?'gradient-brand text-white':'bg-slate-200 text-slate-500'}">${i+1}</div>
    <span class="text-xs font-semibold whitespace-nowrap hidden sm:inline ${i===0?'text-brand-700':'text-slate-400'}">${s}</span>
    ${i<steps.length-1?'<div class="w-4 h-0.5 bg-slate-200 mx-1"></div>':''}
</div>`).join('');

function showStep(n){
    sections.forEach((s,i)=>{s.classList.toggle('hidden',i!==n)});
    document.querySelectorAll('.step-item').forEach((el,i)=>{
        const dot=el.querySelector('div');
        const txt=el.querySelector('span');
        if(i<n){dot.className='w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-emerald-500 text-white';if(txt)txt.className='text-xs font-semibold whitespace-nowrap hidden sm:inline text-emerald-600';}
        else if(i===n){dot.className='w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold gradient-brand text-white';if(txt)txt.className='text-xs font-semibold whitespace-nowrap hidden sm:inline text-brand-700';}
        else{dot.className='w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-slate-200 text-slate-500';if(txt)txt.className='text-xs font-semibold whitespace-nowrap hidden sm:inline text-slate-400';}
    });
    btnPrev.classList.toggle('hidden',n===0);
    btnNext.classList.toggle('hidden',n===steps.length-1);
    btnSubmit.classList.toggle('hidden',n!==steps.length-1);
    window.scrollTo({top:0,behavior:'smooth'});
}

btnNext.addEventListener('click',()=>{
    // Validate current section required fields
    const sec=sections[current];
    const invalids=sec.querySelectorAll(':invalid');
    if(invalids.length>0){invalids[0].reportValidity();return;}
    current++;showStep(current);
});
btnPrev.addEventListener('click',()=>{current--;showStep(current);});

// Load lowongan data (posisi & penempatan)
const res=await APP.api('/lowongan/'+id);
if(res.status===200){
    const d=res.data;
    const selP=document.getElementById('sel-posisi');
    const selL=document.getElementById('sel-penempatan');
    (d.posisi_tersedia||[]).forEach(p=>{selP.insertAdjacentHTML('beforeend',`<option value="${p}">${p}</option>`)});
    (d.penempatan_tersedia||[]).forEach(p=>{selL.insertAdjacentHTML('beforeend',`<option value="${p}">${p}</option>`)});
}else{APP.toast(res.message||'Gagal memuat data lowongan','error');}

// Dynamic show/hide
document.getElementById('sel-sumber').addEventListener('change',e=>{document.getElementById('wrap-sumber-lain').classList.toggle('hidden',e.target.value!=='Lainnya')});
document.getElementById('sel-mulai').addEventListener('change',e=>{document.getElementById('wrap-mulai-lain').classList.toggle('hidden',e.target.value!=='Lainnya')});
document.getElementById('sel-pend').addEventListener('change',e=>{document.getElementById('wrap-pend-lain').classList.toggle('hidden',e.target.value!=='Lainnya')});
document.getElementById('sel-keluarga').addEventListener('change',e=>{document.getElementById('wrap-keluarga').classList.toggle('hidden',e.target.value!=='Ya')});
document.getElementById('sel-penyakit').addEventListener('change',e=>{document.getElementById('wrap-penyakit').classList.toggle('hidden',e.target.value!=='Ya')});
document.getElementById('chk-komputer-lain').addEventListener('change',e=>{document.getElementById('inp-komputer-lain').classList.toggle('hidden',!e.target.checked)});

// Show posisi-specific sections
document.getElementById('sel-posisi').addEventListener('change',e=>{
    const v=e.target.value;
    const isAO=v.includes('AO');
    const isIT=v.includes('IT');
    const isAnalis=v.includes('Analis');
    document.getElementById('khusus-ao').classList.toggle('hidden',!isAO);
    document.getElementById('khusus-it').classList.toggle('hidden',!isIT);
    document.getElementById('khusus-analis').classList.toggle('hidden',!isAnalis);
});

// Pengalaman kerja dynamic
let expCount=0;
function addExp(){
    expCount++;
    const html=`<div class="p-4 bg-slate-50 rounded-xl border border-slate-100 mb-3 exp-item" data-exp="${expCount}">
        <div class="flex justify-between items-center mb-3"><h4 class="font-bold text-sm text-slate-700">Pengalaman #${expCount}</h4>
        <button type="button" class="text-rose-500 hover:text-rose-700 text-xs font-bold btn-rm-exp"><i class="fa-solid fa-trash mr-1"></i>Hapus</button></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div><label class="label text-xs">Nama Perusahaan</label><input type="text" class="input exp-field" data-key="nama_perusahaan"></div>
        <div><label class="label text-xs">Jabatan</label><input type="text" class="input exp-field" data-key="jabatan"></div>
        <div><label class="label text-xs">Periode Kerja</label><input type="text" class="input exp-field" data-key="periode_kerja" placeholder="Jan 2022 - Des 2023"></div>
        <div><label class="label text-xs">Gaji Terakhir (Rp)</label><input type="number" class="input exp-field" data-key="gaji_terakhir"></div>
        <div class="sm:col-span-2"><label class="label text-xs">Deskripsi Pekerjaan</label><textarea rows="2" class="input exp-field" data-key="deskripsi_pekerjaan"></textarea></div>
        <div class="sm:col-span-2"><label class="label text-xs">Alasan Berhenti</label><input type="text" class="input exp-field" data-key="alasan_berhenti"></div>
        </div></div>`;
    document.getElementById('exp-container').insertAdjacentHTML('beforeend',html);
}
document.getElementById('btn-add-exp').addEventListener('click',addExp);
document.getElementById('exp-container').addEventListener('click',e=>{
    if(e.target.closest('.btn-rm-exp')){e.target.closest('.exp-item').remove();}
});

// Date
document.getElementById('tgl-isi').textContent=new Date().toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});

// SUBMIT
document.getElementById('apply-form').addEventListener('submit',async e=>{
    e.preventDefault();
    btnSubmit.disabled=true;
    btnSubmit.innerHTML='<i class="fa-solid fa-spinner animate-spin mr-1"></i> Mengirim...';

    const fd=new FormData(e.target);

    // Collect pengalaman as JSON
    const exps=[];
    document.querySelectorAll('.exp-item').forEach(item=>{
        const obj={};
        item.querySelectorAll('.exp-field').forEach(f=>{obj[f.dataset.key]=f.value});
        if(obj.nama_perusahaan)exps.push(obj);
    });
    fd.append('pengalaman',JSON.stringify(exps));

    const res=await APP.api('/pelamar',{method:'POST',body:fd,headers:{}});
    if(res.status===201){
        document.getElementById('kode-tracking').textContent=res.data.kode_tracking;
        const modal=document.getElementById('success-modal');
        modal.classList.remove('hidden');modal.classList.add('flex');
    }else{
        APP.toast(res.message||'Gagal mengirim lamaran','error');
        btnSubmit.disabled=false;
        btnSubmit.innerHTML='<i class="fa-solid fa-paper-plane mr-1"></i> Kirim Lamaran';
    }
});

document.getElementById('copy-kode').addEventListener('click',()=>{
    navigator.clipboard.writeText(document.getElementById('kode-tracking').textContent).then(()=>APP.toast('Kode disalin!','success'));
});

showStep(0);
})();
</script>
