# KosCare — Sistem Pelaporan & Pemantauan Kerusakan Fasilitas Kos

> **"Hunian Nyaman, Perbaikan Terpantau"**  
> Dibuat untuk keperluan tugas kuliah menggunakan arsitektur **PHP Native (PHP 8.3+)** dan **MySQL/MariaDB**, siap dijalankan di lingkungan lokal **XAMPP Windows**.

---

## 📋 Daftar Isi
1. [Tentang Aplikasi](#-tentang-aplikasi)
2. [Fitur & Hak Akses Role](#-fitur--hak-akses-role)
3. [Arsitektur & Keamanan](#-arsitektur--keamanan)
4. [Persyaratan Sistem](#-persyaratan-sistem)
5. [Panduan Instalasi XAMPP Lengkap (Langkah demi Langkah)](#-panduan-instalasi-xampp-lengkap)
6. [Kredensial Akun Awal](#-kredensial-akun-awal)
7. [Struktur Folder Proyek](#-struktur-folder-proyek)
8. [Solusi Kendala & Error Umum (Troubleshooting)](#-solusi-kendala--error-umum)

---

## 🏠 Tentang Aplikasi

**KosCare** adalah sistem informasi berbasis web untuk mendokumentasikan, mengelola, dan memantau perbaikan kerusakan fasilitas rumah kos secara transparan dan terstruktur.

Masalah utama yang diselesaikan adalah pelaporan manual atau via WhatsApp yang sering kali tertimbun chat lain, lambat ditanggapi, dan tidak memiliki dokumentasi riwayat biaya maupun bukti fisik perbaikan.

---

## 👥 Fitur & Hak Akses Role

### 1. Penghuni (Anak Kos)
- **Registrasi Akun:** Formulir publik khusus penghuni dengan validasi keunikan username & email, nomor kamar, dan password minimal 8 karakter.
- **Dashboard Pribadi:** Ringkasan jumlah laporan (Menunggu, Diproses, Selesai) milik diri sendiri.
- **Pelaporan Kerusakan:** Form dengan nomor tiket otomatis (`KC-YYYYMM-XXXX`), kategori kerusakan, lokasi kamar, deskripsi, tingkat urgensi (Rendah, Sedang, Tinggi, Darurat), serta unggah foto bukti (JPG/PNG/WebP maks 2MB).
- **Pemantauan Riwayat & Timeline:** Melihat perkembangan status step-by-step beserta catatan dari pengelola secara real-time.
- **Diskusi/Tanggapan:** Mengirim balasan atas pertanyaan klarifikasi pengelola.
- **Profil Mandiri:** Memperbarui data diri dan mengubah kata sandi dengan verifikasi kata sandi lama.

### 2. Pengelola Kos (Administrator — Vania Raissa)
- **Dashboard Manajemen:** Metrik total laporan, antrean verifikasi baru, laporan sedang berjalan, laporan selesai, dan peringatan bahaya untuk laporan berurgensi **Tinggi / Darurat**.
- **Pemeriksaan & Verifikasi:** Melihat foto kondisi fisik, mengunduh, dan meninjau lokasi kerusakan.
- **Kontrol Status:** Mengubah status laporan ke:
  1. *Menunggu Pemeriksaan*
  2. *Sedang Diproses*
  3. *Menunggu Informasi* (wajib mengisi keterangan info yang dibutuhkan)
  4. *Selesai* (wajib mengisi tanggal selesai, nama teknisi, dan catatan hasil perbaikan)
  5. *Ditolak* (wajib mengisi alasan penolakan)
- **Riwayat Perbaikan:** Rekapitulasi pekerjaan yang telah tuntas beserta rincian estimasi biaya.
- **Hapus / Arsip:** Tombol hapus laporan yang aman dengan validasi CSRF dan konfirmasi ganda.
- **Log Aktivitas:** Pencatatan otomatis ke tabel `activity_logs`.

### 3. Teknisi Lapangan (Support Role)
- **Daftar Tugas Lapangan:** Memantau antrean fasilitas rusak yang membutuhkan tindakan fisik di kamar kos.
- **Detail Pekerjaan:** Membaca catatan deskripsi, lokasi, dan foto sebelum datang ke lokasi.

---

## 🔒 Arsitektur & Keamanan Sistem

1. **PDO Prepared Statements:** Seluruh interaksi database menggunakan prepared statements (`PDO::ATTR_EMULATE_PREPARES => false`) untuk mencegah serangan **SQL Injection**.
2. **Output Escaping XSS:** Seluruh data yang dicetak ke layar dilindungi fungsi helper `e()` / `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
3. **Proteksi CSRF:** Setiap formulir POST dilindungi dengan token CSRF unik yang tersimpan dalam sesi dan divalidasi dengan `hash_equals()`.
4. **Session Fixation Prevention:** Session ID diregenerasi secara otomatis menggunakan `session_regenerate_id(true)` saat pengguna berhasil login.
5. **Enkripsi Sandi Modern:** Password disimpan menggunakan algoritma standar industri `password_hash($pass, PASSWORD_BCRYPT)` dan diverifikasi dengan `password_verify()`.
6. **Keamanan Folder Uploads:** Folder `uploads/reports/` dilengkapi file `.htaccess` yang mematikan eksekusi script PHP dan CGI untuk mencegah upload *web shell*.
7. **Pembersihan File Gagal:** Jika transaksi database gagal, file gambar sementara langsung dihapus dari disk agar tidak menjadi file yatim.

---

## 💻 Persyaratan Sistem

- **OS:** Windows 10/11 (atau macOS / Linux)
- **Aplikasi:** XAMPP for Windows (versi dengan PHP 8.1, 8.2, atau 8.3)
- **Browser:** Google Chrome, Microsoft Edge, Firefox, atau browser modern lainnya

---

## 🚀 Panduan Instalasi XAMPP Lengkap

Ikuti langkah-langkah mudah di bawah ini untuk menjalankan KosCare di komputer Anda:

### Langkah 1: Siapkan Folder Proyek di XAMPP
1. Pastikan XAMPP telah terinstal di komputer Anda (biasanya di `C:\xampp`).
2. Buat folder baru bernama **`koscare`** di dalam folder `htdocs`, sehingga jalurnya menjadi:  
   `C:\xampp\htdocs\koscare\`
3. Salin / ekstrak seluruh file proyek KosCare ke dalam folder tersebut.

### Langkah 2: Nyalakan Service Apache & MySQL
1. Buka aplikasi **XAMPP Control Panel** (klik kanan lalu pilih *Run as Administrator* jika diperlukan).
2. Pada modul **Apache**, klik tombol **Start** hingga indikator berubah menjadi warna hijau.
3. Pada modul **MySQL**, klik tombol **Start** hingga indikator berubah menjadi warna hijau.

### Langkah 3: Impor Database MySQL melalui phpMyAdmin
1. Buka browser web (Chrome/Edge) lalu kunjungi alamat:  
   `http://localhost/phpmyadmin/`
2. Klik tab **Databases** (Basis Data) pada menu atas.
3. Ketikkan nama database: `koscare_db` dengan collation `utf8mb4_unicode_ci`, lalu klik **Create**.
4. Klik database `koscare_db` yang baru saja dibuat di sidebar kiri.
5. Klik tab **Import** pada menu atas.
6. Klik tombol **Choose File** (Pilih File) dan arahkan ke file:  
   `C:\xampp\htdocs\koscare\database\koscare.sql`
7. Gulir ke bawah lalu klik tombol **Import** (Kirim). Tunggu hingga muncul pesan centang hijau *"Import has been successfully finished"*.

### Langkah 4: Sesuaikan Konfigurasi Database (Jika Diperlukan)
File konfigurasi database berada di `config/database.php`. Secara default, konfigurasi telah disesuaikan dengan standar XAMPP:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'koscare_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan jika MySQL XAMPP Anda belum diberi password
define('DB_PORT', '3306');
```
*Catatan: Jika instalasi MySQL XAMPP Anda menggunakan password root atau port non-standar (misal 3307), sesuaikan nilai di file tersebut.*

### Langkah 5: Jalankan Skrip Setup Pengelola Sekali Pakai
1. Buka tautan berikut di browser Anda:  
   `http://localhost/koscare/setup/create_admin.php`
2. Skrip akan memverifikasi dan membuat akun administrator awal **Vania Raissa**.
3. Sistem akan membuat file `setup/setup.lock` otomatis untuk mengunci eksekusi ulang.
4. *(Opsional untuk keamanan maksimal)*: Anda dapat menghapus file `setup/create_admin.php` setelah langkah ini.

### Langkah 6: Buka Aplikasi KosCare
1. Kunjungi halaman utama aplikasi di:  
   `http://localhost/koscare/`
2. Anda akan disambut oleh landing page resmi KosCare: **"Hunian Nyaman, Perbaikan Terpantau"**.
3. Klik tombol **Masuk Pengelola** untuk login dengan akun administrator, atau klik **Registrasi Penghuni** untuk membuat akun kamar baru.

---

## 🔑 Kredensial Akun Awal

| Peran (Role) | Nama Pengguna (Username) | Kata Sandi (Password) | Keterangan |
| :--- | :--- | :--- | :--- |
| **Pengelola** | `vania.raissa` | `VaniaR@1` | Administrator utama pengelola kos |
| **Penghuni (Demo)** | `dimas.anggoro` | `Penghuni123` | Penghuni Kamar 102 |
| **Teknisi (Demo)** | `joko.teknisi` | `Teknisi123` | Staf perbaikan lapangan |

*Tips: Disarankan mengganti kata sandi setelah Anda login pertama kali melalui menu Profil.*

---

## 📁 Struktur Folder Proyek

```text
koscare/
├── index.php                 # Halaman Beranda / Landing Page publik wajib
├── login.php                 # Autentikasi Login (Penghuni / Pengelola / Teknisi)
├── register.php              # Pendaftaran akun Penghuni publik
├── logout.php                # Pembersihan sesi & keluar aman
├── dashboard/
│   ├── penghuni.php          # Dashboard statistik & laporan pribadi penghuni
│   └── pengelola.php         # Panel kendali metrik, urgensi darurat, & aksi pengelola
├── reports/
│   ├── create.php            # Formulir lapor kerusakan (tiket unik, upload foto)
│   ├── index.php             # Daftar tabel laporan (terfilter sesuai role)
│   ├── detail.php            # Rincian tiket, timeline riwayat, & diskusi
│   ├── update_status.php     # Endpoint transaksi PDO pembaruan status laporan
│   └── delete.php            # Endpoint penghapusan aman laporan oleh pengelola
├── repairs/
│   └── history.php           # Arsip dokumentasi perbaikan fasilitas selesai
├── profile/
│   └── index.php             # Edit profil data diri & ganti kata sandi
├── technician/
│   └── dashboard.php         # Dashboard antrean penugasan teknisi
├── config/
│   ├── database.php          # Koneksi MySQL via PDO dengan error catching ramah
│   └── app.php               # Konstanta sistem, daftar status, & sesi aman
├── includes/
│   ├── auth.php              # Helper login, otorisasi role-based, & session
│   ├── functions.php         # Helper sanitasi XSS, badge pastel, & format tanggal
│   ├── csrf.php              # Generator & validator token proteksi CSRF
│   ├── header.php            # Header layout HTML, fonts, & stylesheet
│   ├── navbar.php            # Navigasi atas responsif dengan role detection
│   ├── sidebar.php           # Sidebar navigasi panel dashboard desktop
│   └── footer.php            # Footer penutup & bundle script JavaScript
├── assets/
│   ├── css/
│   │   └── style.css         # Styling tema pastel elegan (Ivory, Sage, Pink, Lavender)
│   └── js/
│       └── app.js            # Validasi form, preview foto, & dialog konfirmasi
├── uploads/
│   └── reports/              # Folder penyimpanan foto unggahan kerusakan
│       └── .htaccess         # Keamanan: blokir eksekusi script PHP dalam uploads
├── database/
│   └── koscare.sql           # Skrip DDL & DML skema database koscare_db
├── setup/
│   └── create_admin.php      # Skrip setup one-time akun Vania Raissa
└── README.md                 # Dokumentasi panduan instalasi lengkap
```

---

## 🛠 Solusi Kendala & Error Umum (Troubleshooting)

### 1. Modul Apache di XAMPP Gagal Start (Port 80/443 Conflict)
- **Penyebab:** Port 80 atau 443 sedang dipakai aplikasi lain seperti Skype, VMware, IIS, atau Docker.
- **Solusi:**
  1. Pada baris Apache di XAMPP Control Panel, klik **Config** > **Apache (httpd.conf)**.
  2. Cari baris `Listen 80` dan ubah menjadi `Listen 8080`.
  3. Cari baris `ServerName localhost:80` dan ubah menjadi `ServerName localhost:8080`.
  4. Simpan file lalu nyalakan kembali Apache. Buka aplikasi via `http://localhost:8080/koscare/`.

### 2. Modul MySQL Tidak Bisa Dinyalakan (Error: MySQL shutdown unexpectedly)
- **Penyebab:** File log transaksi crash atau bentrok port 3306.
- **Solusi:**
  1. Buka folder `C:\xampp\mysql\data\`.
  2. Cadangkan folder `data` tersebut.
  3. Hapus file `ibdata1`, `ib_logfile0`, dan `ib_logfile1` (jangan hapus folder database!).
  4. Buka kembali XAMPP Control Panel dan nyalakan MySQL.

### 3. Muncul Pesan "⚠️ Gagal Terhubung ke Database MySQL"
- **Penyebab:** Database `koscare_db` belum dibuat di phpMyAdmin atau MySQL belum berjalan.
- **Solusi:**
  1. Pastikan indikator MySQL di XAMPP Control Panel berwarna hijau.
  2. Buka `http://localhost/phpmyadmin/` dan pastikan database `koscare_db` sudah ada beserta tabel-tabelnya (`users`, `reports`, dll).

### 4. Foto Tidak Muncul atau Gagal Diunggah
- **Penyebab:** Izin folder `uploads/reports/` di sistem operasi terbatas atau ukuran file > 2MB.
- **Solusi:**
  1. Pastikan file gambar berformat JPG, PNG, atau WebP dengan ukuran di bawah 2 MB.
  2. Pastikan folder `uploads/reports/` memiliki izin baca dan tulis (Read & Write).

---
*Dikembangkan dengan penuh dedikasi untuk keandalan hunian kos yang tertib, nyaman, dan transparan.*
