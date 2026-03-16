# 🌊 3flo - "Non-stop Creative" Digital Experience

[![Run Tests](https://github.com/3flonet/platform_creative_agency/actions/workflows/test.yml/badge.svg)](https://github.com/3flonet/platform_creative_agency/actions)

3flo adalah platform digital agensi kreatif imersif yang dirancang untuk merepresentasikan filosofi **"Non-stop Creative"**. Website ini bukan sekadar galeri statis, melainkan sebuah **Immersive Journey** yang membawa audiens menelusuri alur kerja dari ide hingga produksi.

## ✨ Fitur Utama

- **🚀 Intro Preloader Sequence**: Pengalaman pembuka brand reveal yang mulus dengan efek sliding transisi.
- **🌀 3D Scroll Universe**: Integrasi Three.js (React Three Fiber) di mana objek 3D berinteraksi dan berubah bentuk/posisi sesuai narasi scroll user.
- **🛠️ Filament-Powered Admin**: Panel administrasi lengkap untuk mengelola:
  - **Services**: Manajemen layanan kreatif (Experiential, Visual Soul, Digital Engine).
  - **Projects**: Portfolio mendalam dengan detail relasi ke layanan terkait.
  - **3D Tuning**: Pengaturan posisi, rotasi, dan skala objek 3D langsung dari dashboard tanpa menyentuh kode.
- **✍️ Creative Journal**: Sistem manajemen artikel untuk membagikan pemikiran dan update terbaru.
- **📱 Ultra-Responsive & Smooth Scroll**: Menggunakan Lenis Scroll untuk pergerakan yang mewah dan adaptif di semua perangkat.

## 🚀 Teknologi

- **Backend**: Laravel 12
- **Frontend**: React, Inertia.js, Tailwind CSS
- **Animations**: GSAP (GreenSock), Framer Motion
- **3D Engine**: Three.js, React Three Fiber (R3F)
- **Tooling**: Vite, Pest (Testing), Filament (CMS)

## 🛠️ Deployment & Installation

3flo Engine dilengkapi dengan **Interactive Web Installer** untuk memudahkan proses deployment di server produksi maupun lokal.

### 📋 Prasyarat Sistem
- **PHP**: ^8.2 (Rekomendasi 8.4)
- **Database**: MySQL 8.0+ / MariaDB
- **Ekstensi PHP**: `bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml`
- **Node.js**: v18+ & NPM (untuk build asset)

### 🚀 Panduan Langkah-demi-Langkah

#### 1. Persiapan Server
Clone repository dan pindah ke direktori project:
```bash
git clone https://github.com/3flonet/platform_creative_agency.git
cd platform_creative_agency
```

#### 2. Instalasi Dependensi
Tergantung pada tipe server Anda:

*   **Opsi A: VPS / Dedicated Server (Akses SSH)**
    Jalankan perintah langsung di terminal server:
    ```bash
    # Backend
    composer install --optimize-autoloader --no-dev
    # Frontend build
    npm install && npm run build
    ```

*   **Opsi B: Shared Hosting / cPanel (Tanpa Akses SSH/Node.js)**
    Lakukan build di laptop/lokal terlebih dahulu, lalu upload hasilnya:
    1. Jalankan `npm install && npm run build` di laptop Anda.
    2. Kompres seluruh project (termasuk folder `vendor` dan `public/build`).
    3. Upload dan ekstrak file `.zip` tersebut di File Manager cPanel.

#### 3. Konfigurasi Environment & Wizard
1. Salin file `.env.example` menjadi `.env`.
2. **Penting**: Jalankan perintah berikut untuk menghasilkan kunci enkripsi aplikasi:
   ```bash
   php artisan key:generate
   ```
   *(Tanpa APP_KEY, server akan menampilkan error `MissingAppKeyException`).*
3. **Penting**: Jalankan perintah symlink untuk menghubungkan folder upload ke folder publik:
   ```bash
   php artisan storage:link
   ```
3. Pastikan izin akses (folder permissions) untuk folder `storage` dan `bootstrap/cache` diatur ke **775** atau **777** agar server bisa menulis file.
4. Akses domain Anda di browser; **Automatic Web Installer** akan muncul untuk memandu Anda melakukan konfigurasi Database dan Lisensi tanpa perlu mengedit file secara manual.

> [!TIP]
> Jika Anda menjumpai error "No application encryption key has been specified" saat pertama kali membuka browser, pastikan Anda sudah menjalankan `php artisan key:generate` di terminal server.

#### 4. Finalisasi
Setelah instalasi Wizard selesai dan symlink terpasang, sistem akan membuat file `storage/installed.lock`. Project Anda kini siap digunakan secara publik.

---

### 🔑 Default Admin Access
- **Identity**: `admin@3flo.net`
- **Secret**: `password` (Ganti segera setelah login di menu Profile).

---

## 📄 Lisensi
Project ini dikembangkan secara eksklusif untuk 3flo. Hak Cipta © 2026.

---

**Selamat!** Project 3FLO Engine Anda kini telah berhasil terinstal Selamat berkarya dan terus berkreasi secara non-stop! 🚀🌊
