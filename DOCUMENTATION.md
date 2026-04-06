# 📘 Dokumentasi Operasional: 3FLO Engine
**Versi**: 1.0.0 (Stable Release)
**Status**: Produksi

---

## 1. Arsitektur Sistem
3FLO Engine adalah platform agensi kreatif imersif yang dibangun dengan teknologi modern:
- **Backend**: Laravel 12 & PHP 8.4.
- **Admin Panel**: Filament v3 (TALL Stack) untuk pengelolaan konten yang instan.
- **Frontend**: React 18 melalui Inertia.js untuk transisi halaman tanpa reload.
- **Visual**: Three.js (React Three Fiber) untuk animasi objek 3D di latar belakang.

---

## 2. Sistem Lisensi & Keamanan
Sistem ini menggunakan **LicenseHub Protection**:
- **Validasi**: Setiap domain wajib memiliki Lisensi aktif yang diverifikasi secara digital melalui tanda tangan HMAC SHA-256.
- **Auto-Lock**: Jika kode dimanipulasi secara ilegal, sistem akan masuk ke mode *Tampered* dan memblokir akses Admin Panel demi keamanan data.
- **Pengecekan Rutin**: Aplikasi melakukan verifikasi lisensi setiap hari secara otomatis via Task Scheduler.

---

## 3. Panduan Pengelolaan Konten (User Manual)

### A. Menambahkan Services (Layanan)
Halaman: `/admin/services`
- Layanan ini akan muncul di baris horizontal pada section **Capabilities**.
- Gunakan ikon (SVG/PNG) dengan latar belakang transparan.
- **Sort Order**: Gunakan angka (1, 2, 3) untuk mengatur urutan layanan dari kiri ke kanan.

### B. Menambahkan Projects (Portfolio)
Halaman: `/admin/projects`
- Tambahkan judul, slug, dan gambar utama.
- **Relasi**: Anda dapat menghubungkan satu project dengan satu atau lebih layanan (Services). Ini akan membantu pengunjung memfilter karya berdasarkan keahlian Anda.

### C. Manajemen Teams (Tim)
Halaman: `/admin/teams`
- Gunakan foto profil resmi.
- Sistem secara otomatis akan menampilkan tim Anda di section **Collective** dengan animasi melayang.

---

## 4. Tuning 3D Experience (Matrix transformation)
Halaman: `/admin/general-settings` > Tab **3D Experience**

Ini adalah jantung visual website. Anda bisa mengatur objek 3D per section scroll tanpa menyentuh kode:
1. **Pilih Objek**: Gunakan bentuk standar atau unggah file `.GLB` kustom.
2. **Koordinat Sektor**: Tersedia 6 sektor (Discovery, Matrix, Works, People, Narrative, Connection).
3. **Parameter**:
   - **X (Horizontal)**: Menggeser objek ke kiri/kanan.
   - **Y (Vertical)**: Menggeser objek ke atas/bawah.
   - **Z (Depth)**: Menggeser objek mendekat/menjauh dari layar.
   - **Rotation**: Mengatur sudut kemiringan objek agar dramatis.
   - **Scale**: Mengatur ukuran objek di layar.

---

## 5. Pemeliharaan & Troubleshooting

| Masalah | Solusi |
| :--- | :--- |
| **Konten Tidak Berubah** | Masuk ke *Site Settings* dan klik tombol **Clear System Cache** di pojok kanan atas. |
| **Error 500 (Manifest Not Found)** | Pastikan Anda sudah menjalankan `npm run build` di terminal server. |
| **Lisensi "Invalid Signature"** | Pastikan `APP_KEY` di file `.env` tidak berubah setelah lisensi diaktivasi. |
| **Gambar Tidak Muncul** | Jalankan perintah `php artisan storage:link` untuk menghubungkan folder upload ke folder publik. |

---

## 6. Pengembangan Lanjut
- **Frontend**: Modifikasi file di `resources/js/Pages` dan jalankan `npm run dev` untuk melihat perubahan secara real-time.
- **Admin**: Tambahkan resource baru dengan perintah `php artisan make:filament-resource NamaModel`.

---
*© 2026. 3FLO Engine - Managed by 3FLO Creative Agency.*
