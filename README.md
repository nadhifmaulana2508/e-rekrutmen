# E-Form Rekrutmen Pegawai

Sistem rekrutmen pegawai berbasis PHP native dengan pemisahan **REST API** dan **Client (Public Pages + Admin Panel)**. Frontend dibangun dengan **Tailwind CSS** via CDN, fully responsive untuk semua device.

## Stack
- PHP Native 8.x (tanpa framework)
- MySQL / MariaDB (PDO)
- Tailwind CSS (CDN)
- Font Awesome 6 (icons)
- Plus Jakarta Sans (Google Fonts)
- JWT Auth (custom helper)

## Struktur Project
```
rekrutmen/
├── index.php              # Front controller (router root)
├── .htaccess              # URL rewrite -> index.php
├── .env                   # Kredensial DB & JWT secret (copy dari .env.example)
├── sql/
│   └── schema.sql         # Skema tabel + seed admin default
├── api/                   # REST API Layer
│   ├── index.php
│   ├── config/
│   ├── controllers/       # AuthController, LowonganController, PelamarController, DashboardController
│   ├── helpers/           # response, JWT, upload
│   ├── middlewares/       # auth JWT
│   └── routes/            # auth, lowongan, pelamar, dashboard
├── pages/                 # Halaman publik pelamar
│   ├── home.php           # Landing page
│   ├── lowongan.php       # List lowongan
│   ├── detail.php         # Detail lowongan
│   ├── form.php           # Form pelamaran
│   ├── status.php         # Cek status lamaran
│   └── faq.php
├── views/                 # Layout shared publik
│   ├── header.php
│   ├── navbar.php
│   ├── script.php
│   └── footer.php
├── client/                # Panel Admin (private)
│   ├── index.php
│   └── includes/
│       ├── auth_logic.php
│       ├── components.php
│       └── views/
└── uploads/
    ├── cv/                # PDF CV pelamar
    └── foto/              # Foto pelamar
```

## Setup Local (XAMPP / Laragon)
1. Clone ke folder server: `htdocs/rekrutmen/`
2. Copy `.env.example` -> `.env`, isi kredensial DB
3. Buat database `rekrutmen_db` & import `sql/schema.sql`
4. Akses: `http://localhost/rekrutmen/`
5. Admin panel: `http://localhost/rekrutmen/client/`
   - Default login: `admin` / `admin123` (ubah setelah login pertama!)

## Routing
- Public: `/`, `/home`, `/lowongan`, `/lowongan/{id}`, `/form/{id}`, `/status`, `/faq`
- API: `/api/auth/login`, `/api/lowongan`, `/api/pelamar`, `/api/dashboard/stats`, dsb.
- Admin: `/client/` (require login JWT disimpan di session)

## Fitur
**Publik (Pelamar):**
- Landing page modern dengan hero + featured lowongan
- List lowongan + filter divisi / tipe kerja
- Detail lowongan dengan requirements & benefits
- Form pelamaran (upload CV PDF + foto)
- Kode tracking otomatis untuk cek status lamaran

**Admin:**
- Dashboard analitik (total lowongan aktif, total pelamar, pipeline status)
- CRUD Lowongan
- Kelola Pelamar (filter, ubah status: pending/review/interview/diterima/ditolak)
- Detail pelamar + download CV
