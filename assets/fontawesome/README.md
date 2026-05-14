# Font Awesome 6.5.1 (Lokal)

Font Awesome di-host secara lokal untuk mengatasi masalah icon "kotak-kotak" pada iPhone/iOS Safari yang disebabkan oleh CDN `crossorigin` issue.

## Setup

Jalankan script download untuk mengunduh file Font Awesome:

```bash
php assets/fontawesome/download.php
```

Script ini akan:
1. Download Font Awesome 6.5.1 dari official CDN
2. Extract CSS ke `css/`
3. Extract webfonts (woff2/ttf) ke `webfonts/`

## Struktur

```
assets/fontawesome/
├── css/
│   └── all.min.css      ← stylesheet utama
├── webfonts/
│   ├── fa-solid-900.woff2
│   ├── fa-regular-400.woff2
│   ├── fa-brands-400.woff2
│   └── ... (file font lainnya)
├── download.php         ← script download
└── README.md
```

## Kenapa Lokal?

iOS Safari memiliki bug/limitasi dengan `crossorigin="anonymous"` pada CDN font.
Dengan hosting lokal, font dimuat dari same-origin sehingga tidak ada CORS issue.
