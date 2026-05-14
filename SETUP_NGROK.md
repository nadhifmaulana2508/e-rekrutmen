# Setup Lokal + Ngrok

## 1. Persiapan Database

Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), lalu:

1. **Buat database** bernama `rekrutmen_db` (collation: `utf8mb4_general_ci`)
2. **Import schema**: klik database tersebut → tab Import → pilih file `sql/schema.sql` → klik Go

## 2. Konfigurasi .env

Copy `.env.example` menjadi `.env` di root project:

```
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASS=
DB_NAME=rekrutmen_db
JWT_SECRET=ganti-dengan-string-random-panjang
```

> Jika MySQL Anda pakai password, isi `DB_PASS` sesuai.

## 3. Pastikan Apache Running

- Buka XAMPP → Start Apache & MySQL
- Pastikan project ada di: `C:\xampp\htdocs\rekrutmen\` (atau sesuai subfolder)
- Test: buka `http://localhost/rekrutmen/` di browser

## 4. Setup Ngrok

```bash
# Download & install ngrok dari https://ngrok.com/download

# Login (pakai token dari dashboard ngrok)
ngrok config add-authtoken YOUR_TOKEN

# Jalankan (arahkan ke port Apache = 80)
ngrok http 80
```

Jika Anda punya domain statis ngrok (seperti `riveter-runt-hatred.ngrok-free.app`):
```bash
ngrok http --domain=riveter-runt-hatred.ngrok-free.app 80
```

## 5. Akses

Buka di browser / share ke orang lain:
```
https://riveter-runt-hatred.ngrok-free.app/rekrutmen/
```

Admin panel:
```
https://riveter-runt-hatred.ngrok-free.app/rekrutmen/client/
```

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| "Koneksi database gagal" | Pastikan MySQL running dan database `rekrutmen_db` sudah dibuat + import schema.sql |
| "Unknown database" | Buat database `rekrutmen_db` di phpMyAdmin lalu import `sql/schema.sql` |
| Halaman blank / 500 error | Cek Apache error log di `C:\xampp\apache\logs\error.log` |
| Icon kotak-kotak | Clear cache browser (Ctrl+Shift+R) |
| Ngrok shows "Visit site" button | Klik tombol "Visit Site" — ini normal untuk free plan ngrok |

## Login Admin Default

- **Username**: `admin`
- **Password**: `admin123`
