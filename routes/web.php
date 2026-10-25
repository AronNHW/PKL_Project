<?php

use App\Http\Controllers\BahanController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\PemakaianController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // -------------------------------------------------------------
    // FITUR UMUM (DASHBOARD, STOK BACA, KATALOG BAHAN, PROFIL)
    // -------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Katalog Bahan (Read-only untuk cabang & owner)
    Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');
    Route::get('/bahan/{bahan}', [BahanController::class, 'show'])->whereNumber('bahan')->name('bahan.show');

    // Monitoring Stok & Mutasi
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/stok/mutasi', [StokController::class, 'mutasi'])->name('stok.mutasi');

    // Distribusi (Tampilan Daftar)
    Route::get('/distribusi/permintaan', [DistribusiController::class, 'permintaan'])->name('distribusi.permintaan');
    Route::get('/distribusi/pengiriman', [DistribusiController::class, 'pengiriman'])->name('distribusi.pengiriman');

    // Pemakaian & Mesin (Tampilan Daftar)
    Route::get('/pemakaian', [PemakaianController::class, 'index'])->name('pemakaian.index');
    Route::get('/mesin', [MesinController::class, 'index'])->name('mesin.index');

    // Laporan & Ekspor (Cabang terisolasi lokasinya di Controller)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');

    // Profil Akun
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -------------------------------------------------------------
    // KHUSUS ADMIN CABANG (OPERASIONAL UNIT LOKAL)
    // -------------------------------------------------------------
    Route::middleware(['role:admin_cabang'])->group(function () {
        // Pengajuan Permintaan Bahan ke Pusat
        Route::post('/distribusi/permintaan', [DistribusiController::class, 'storePermintaan'])->name('distribusi.permintaan.store');
        
        // Konfirmasi Penerimaan Surat Jalan dari Pusat
        Route::put('/distribusi/pengiriman/{pengiriman}/terima', [DistribusiController::class, 'konfirmasiPenerimaan'])->name('distribusi.pengiriman.terima');
        
        // Pencatatan Pemakaian Bahan Produksi Harian
        Route::post('/pemakaian', [PemakaianController::class, 'store'])->name('pemakaian.store');
        
        // Pelaporan Kerusakan Mesin Cabang
        Route::post('/mesin/lapor', [MesinController::class, 'laporKerusakan'])->name('mesin.lapor');
    });

    // -------------------------------------------------------------
    // KHUSUS ADMIN PUSAT (KONTROL MASTER, LOGISTIK, USER & PERSETUJUAN)
    // -------------------------------------------------------------
    Route::middleware(['role:admin_pusat'])->group(function () {
        // Kelola Pengguna (User Management)
        Route::resource('users', UserController::class)->only(['index', 'store', 'update']);

        // Master Bahan & Impor CSV
        Route::get('/bahan/template-csv', [BahanController::class, 'templateCsv'])->name('bahan.template-csv');
        Route::post('/bahan/import-csv', [BahanController::class, 'importCsv'])->name('bahan.import-csv');
        Route::get('/bahan/create', [BahanController::class, 'create'])->name('bahan.create');
        Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
        Route::get('/bahan/{bahan}/edit', [BahanController::class, 'edit'])->name('bahan.edit');
        Route::put('/bahan/{bahan}', [BahanController::class, 'update'])->name('bahan.update');
        Route::delete('/bahan/{bahan}', [BahanController::class, 'destroy'])->name('bahan.destroy');

        // Master Kategori, Supplier, Lokasi (Mutasi Data)
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

        Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
        Route::put('/lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');

        // Penyesuaian Stok (Stock Opname & Batas Minimum)
        Route::post('/stok/sesuaikan', [StokController::class, 'sesuaikan'])->name('stok.sesuaikan');
        Route::post('/stok/update-minimum', [StokController::class, 'updateMinimum'])->name('stok.update-minimum');

        // Barang Masuk dari Supplier
        Route::resource('barang-masuk', BarangMasukController::class)->only(['index', 'store']);

        // Logistik Distribusi Pusat
        Route::patch('/distribusi/permintaan/{permintaan}/status', [DistribusiController::class, 'updateStatusPermintaan'])->name('distribusi.permintaan.status');
        Route::post('/distribusi/pengiriman', [DistribusiController::class, 'storePengiriman'])->name('distribusi.pengiriman.store');

        // Update Status Perbaikan Mesin & Potong Suku Cadang
        Route::patch('/mesin/kerusakan/{laporan}/status', [MesinController::class, 'updateStatusKerusakan'])->name('mesin.kerusakan.status');
    });

    // -------------------------------------------------------------
    // ADMIN PUSAT & OWNER (ANALISIS, PREDIKSI & VIEW MASTER REFERENCE)
    // -------------------------------------------------------------
    Route::middleware(['role:admin_pusat,owner'])->group(function () {
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');

        Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
        Route::post('/prediksi/generate', [PrediksiController::class, 'generate'])->name('prediksi.generate');
    });
});

require __DIR__.'/auth.php';
