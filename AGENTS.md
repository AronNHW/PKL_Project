# CetakStok: Gambaran Proyek dan Panduan Desain

> Dokumen gambaran untuk proyek PKL. Dipakai sebagai acuan tim dan sebagai konteks untuk AI agent di IDE (simpan di root proyek, misalnya sebagai `AGENTS.md` atau `docs/gambaran.md`).

## 1. Ringkasan

Sistem manajemen inventaris bahan baku percetakan untuk **gudang pusat dan 5 cabang**, dengan distribusi pusat ke cabang (rekonsiliasi kirim vs terima), pelaporan kerusakan mesin, riwayat perbaikan, dan prediksi pemakaian. Semua modul berada dalam **satu proyek dan satu database** agar hosting mudah.

## 2. Spesifikasi Teknis

| Bagian | Pilihan |
|---|---|
| Framework | **Laravel 12** (sesuai arahan penanggung jawab) |
| Bahasa | PHP 8.2+ |
| Database | MySQL |
| Tampilan | Blade + Tailwind CSS (Vite) + Alpine.js |
| Login | Laravel Breeze (Blade), ditambah peran |
| Grafik | Chart.js |
| Impor/ekspor | Laravel Excel (maatwebsite/excel), DomPDF untuk PDF |
| Foto | Laravel Storage (disk `public`), database hanya menyimpan path |

**Perintah awal (pastikan versi 12):**

```bash
composer create-project laravel/laravel:^12.0 percetakan-app
cd percetakan-app
php artisan --version     # harus tampil Laravel Framework 12.x
```

## 3. Peran Pengguna

| Peran | Akses |
|---|---|
| `owner` | Dashboard, semua laporan (baca saja) |
| `admin_pusat` | Master data, barang masuk, persetujuan dan pengiriman, mesin, prediksi |
| `admin_cabang` | Permintaan, penerimaan, pemakaian, laporan kerusakan (hanya data cabangnya) |

Data admin cabang dibatasi dengan `id_lokasi` milik penggunanya (global scope atau policy).

## 4. Fase Pengembangan

| Fase | Isi |
|---|---|
| 1 | Login, peran, master bahan (impor Excel), stok per lokasi, dashboard |
| 2 | Barang masuk, permintaan, pengiriman, penerimaan, laporan selisih |
| 3 | Pemakaian bahan, laporan kerusakan mesin (foto + kronologi), riwayat perbaikan |
| 4 | Laporan dan cetak (PDF/Excel) |
| 5 | Kelola pengguna dan cabang |
| 6 | Prediksi (Exponential Smoothing) dan Reorder Point |

## 5. Struktur Menu

```
Dashboard
Master Bahan  (bahan, kategori, supplier, lokasi)
Stok          (stok per lokasi, riwayat mutasi)
Barang Masuk
Distribusi    (permintaan, pengiriman, penerimaan, selisih)
Pemakaian
Mesin         (data mesin, laporan kerusakan, riwayat)
Prediksi      (prediksi, rekomendasi)
Laporan
Pengguna
```

## 6. Struktur Folder (Laravel)

```
app/
  Models/            Bahan, Lokasi, StokLokasi, MutasiStok, Pengiriman, ...
  Http/Controllers/  BahanController, PengirimanController, KerusakanController, ...
  Services/          StokService, PrediksiService
  Policies/          LokasiPolicy
database/
  migrations/
  seeders/           LokasiSeeder, BahanSeeder, UserSeeder
resources/
  views/
    layouts/         app.blade.php (sidebar + header)
    components/      tabel, status-chip, form-field, empty-state
    bahan/ stok/ distribusi/ mesin/ prediksi/ laporan/
routes/web.php
```

**Catatan penting:**
- **Semua perubahan stok lewat `StokService`**, jangan mengubah `stok_lokasi` langsung dari controller. Setiap perubahan wajib mencatat `mutasi_stok`, dibungkus `DB::transaction`.
- Jika memakai nama tabel bahasa Indonesia (`bahan`, `lokasi`), tulis `protected $table = 'bahan';` di model, karena Laravel menebak nama jamak bahasa Inggris (`bahans`).
- Prediksi dijalankan lewat Artisan command + scheduler (rekap pemakaian bulanan, hitung ROP).

## 7. Tabel Database

**Master:** `users` (tambah `role`, `id_lokasi`), `lokasi`, `kategori`, `supplier`, `bahan`
**Stok:** `stok_lokasi` (id_bahan, id_lokasi, jumlah, stok_minimum), `mutasi_stok`
**Barang masuk:** `barang_masuk`, `detail_masuk`
**Distribusi:** `permintaan`, `detail_permintaan`, `pengiriman`, `detail_pengiriman` (jumlah_kirim, jumlah_terima, selisih)
**Pemakaian:** `pemakaian`, `pemakaian_bulanan`
**Mesin:** `mesin`, `laporan_kerusakan`, `detail_kerusakan` (id_bahan suku cadang, jumlah, foto), `riwayat_perbaikan`
**Prediksi:** `parameter_bahan`, `prediksi`, `saran_distribusi`, `saran_pembelian`

Suku cadang adalah baris di `bahan` dengan kategori **Suku cadang**, sehingga pemakaiannya otomatis memotong stok.

## 8. Alur Utama

**Distribusi:** cabang mengajukan permintaan → pusat menyetujui dan membuat surat jalan (stok pusat berkurang) → cabang mengisi jumlah diterima (stok cabang bertambah) → selisih ditandai dan ditindaklanjuti.

**Kerusakan mesin:** cabang memilih mesin, suku cadang, foto, kronologi → pusat memeriksa (status: dilaporkan, diperiksa, diperbaiki, selesai) → saat selesai stok suku cadang dipotong dan riwayat mesin tercatat.

## 9. Panduan Desain (Anti-Slop)

Tujuan: tampilan seperti **alat kerja gudang**, bukan template AI. Padat informasi, tenang, mudah dipindai. Prinsipnya: setiap elemen harus punya fungsi.

### 9.1 Ciri "slop" yang dilarang

- Gradien ungu-biru, efek kaca (glassmorphism), bayangan besar dan blur
- Kartu raksasa bersudut sangat bulat dengan angka besar tapi tidak berguna
- Emoji sebagai ikon, ilustrasi dekoratif, ikon di setiap judul
- Semua rata tengah, hero besar di halaman admin
- Data palsu generik (John Doe, Lorem ipsum, Item 1, 2, 3)
- Kata kosong: "Selamat datang di dashboard canggih", "Kelola dengan mudah"
- Animasi berlebihan (memantul, muncul bertahap di setiap elemen)
- Warna sebagai satu-satunya penanda status

### 9.2 Gaya yang dipakai

**Warna (token CSS):**

| Token | Nilai | Pemakaian |
|---|---|---|
| `--bg` | `#F6F7F9` | Latar halaman |
| `--surface` | `#FFFFFF` | Tabel, panel |
| `--border` | `#DDE1E7` | Garis pemisah (tanpa bayangan) |
| `--text` | `#1B2430` | Teks utama |
| `--muted` | `#5F6B7A` | Teks pendukung |
| `--accent` | `#1F4FD8` | Tombol utama, tautan aktif (satu warna saja) |
| `--ok` | `#1E7B4F` | Stok aman |
| `--warn` | `#A86A00` | Menipis |
| `--danger` | `#B42318` | Kritis, selisih, rusak |

Sidebar memakai satu warna gelap polos (`#14213D`), tanpa gradien.

**Tipografi:**
- Teks: **IBM Plex Sans** (atau Public Sans)
- Kode barang, nomor surat jalan, angka tabel: **IBM Plex Mono** atau `font-variant-numeric: tabular-nums` agar angka sejajar
- Ukuran: 14px untuk isi dan tabel, 12px untuk label, 20px untuk judul halaman. Maksimal 3 ukuran.

**Bentuk dan jarak:**
- Grid 4px. Sudut membulat **6px** (bukan 16-24px)
- Pemisah dengan garis 1px, bukan bayangan
- Tinggi baris tabel 40px, rapat tapi terbaca

### 9.3 Aturan komponen

- **Status = teks + warna.** Chip kecil bertuliskan "Kritis", "Menipis", "Aman", bukan hanya titik warna.
- **Tabel adalah pusat.** Tersedia pencarian, filter, urutan kolom, paginasi. Angka rata kanan, teks rata kiri, satuan selalu tampil (`18 roll`).
- **Dashboard:** kartu ringkasan kecil (judul 12px, angka 24px) yang bisa diklik menuju daftar terkait. Daftar kerja di bawahnya: bahan perlu dipesan, pengiriman belum dikonfirmasi, laporan kerusakan aktif.
- **Grafik:** satu pesan per grafik, sumbu berlabel dengan satuan benar (roll, rim, rupiah), tanpa dekorasi 3D.
- **Form:** label di atas kolom, satu kolom untuk HP, pesan error spesifik ("Jumlah diterima tidak boleh lebih dari 18 roll").
- **Ikon:** satu set garis (Lucide atau Heroicons outline), ukuran seragam, hanya di menu dan tombol aksi.
- **Keadaan kosong:** kalimat konkret dan satu aksi ("Belum ada laporan kerusakan. Buat laporan baru").
- **Notifikasi:** pesan singkat dan jelas ("Pengiriman SJ-2026-0045 diterima, selisih 1 roll").
- **Animasi:** hanya transisi 150ms pada hover dan buka-tutup panel.

### 9.4 Format data (Indonesia)

- Angka: `1.250`, rupiah `Rp 4.820.000`
- Tanggal: `20 Okt 2026`, jam 24 jam (`21.27 WIB`)
- Satuan mengikuti bahan (roll, rim, liter, pcs). **Jangan menyamakan semua ke "ton".**
- Nomor dokumen berpola: `SJ-2026-0045` (surat jalan), `PR-2026-0012` (permintaan), `LK-2026-0007` (laporan kerusakan)

### 9.5 Data contoh yang realistis

Pakai nama nyata sektor percetakan: Stiker vinyl, Spanduk flexi 280gr, Art paper 260gr, Tinta cyan, Head printer, Roller. Nama cabang dan mesin diganti sesuai data perusahaan.

### 9.6 Halaman cabang (HP)

Halaman penerimaan, pemakaian, dan laporan kerusakan dipakai di lapangan: tombol minimal 44px, satu kolom, input angka memakai keypad numerik, unggah foto langsung dari kamera.

### 9.7 Aksesibilitas

Kontras teks minimal 4,5:1, fokus keyboard terlihat, status tidak hanya bergantung pada warna.

## 10. Instruksi untuk AI Agent

Tempel ini saat meminta agent membuat halaman:

```
Proyek Laravel 12 + Blade + Tailwind + Alpine. Ikuti docs/gambaran.md.
Desain anti-slop: tanpa gradien, tanpa bayangan besar, tanpa emoji, sudut 6px,
satu warna aksen biru #1F4FD8, status berupa chip teks + warna, tabel padat
dengan angka rata kanan dan satuan tampil, font IBM Plex Sans.
Semua perubahan stok lewat StokService dan wajib mencatat mutasi_stok dalam
DB::transaction. Admin cabang hanya melihat data lokasinya. Teks bahasa
Indonesia, data contoh realistis (stiker vinyl, spanduk flexi, art paper).
Kerjakan satu fase per permintaan, mulai dari fase 1.
```

## 11. Hal yang Masih Perlu Dikonfirmasi

- Nama 5 cabang dan nama gudang pusat sebenarnya
- Satuan bahan dan konversinya
- Siapa yang menyetujui laporan kerusakan, dan suku cadang diambil dari pusat atau cabang
- Satu login untuk semua modul, dan apakah operator punya akun sendiri
- Apakah stok keluar nanti terhubung ke SPK yang sudah ada
