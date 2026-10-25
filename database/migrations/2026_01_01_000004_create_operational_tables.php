<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Barang Masuk
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_masuk', 40)->unique();
            $table->foreignId('id_supplier')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_masuk');
            $table->string('nomor_po', 50)->nullable();
            $table->string('nomor_surat_jalan_supplier', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('detail_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang_masuk')->constrained('barang_masuk')->cascadeOnDelete();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->decimal('harga_satuan', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Distribusi: Permintaan
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permintaan', 40)->unique();
            $table->foreignId('id_lokasi_pemohon')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_permintaan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'diproses', 'selesai'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('id_user_pemohon')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_user_penyetuju')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_permintaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_permintaan')->constrained('permintaan')->cascadeOnDelete();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('jumlah_minta', 12, 2);
            $table->decimal('jumlah_disetujui', 12, 2)->default(0);
            $table->timestamps();
        });

        // Distribusi: Pengiriman
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat_jalan', 40)->unique();
            $table->foreignId('id_permintaan')->nullable()->constrained('permintaan')->nullOnDelete();
            $table->foreignId('id_lokasi_asal')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_lokasi_tujuan')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_kirim');
            $table->date('tanggal_terima')->nullable();
            $table->enum('status', ['dikirim', 'diterima', 'selisih'])->default('dikirim');
            $table->string('ekspedisi_sopir', 100)->nullable();
            $table->text('catatan_kirim')->nullable();
            $table->text('catatan_terima')->nullable();
            $table->foreignId('id_user_pengirim')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_user_penerima')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('detail_pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengiriman')->constrained('pengiriman')->cascadeOnDelete();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('jumlah_kirim', 12, 2);
            $table->decimal('jumlah_terima', 12, 2)->default(0);
            $table->decimal('selisih', 12, 2)->default(0);
            $table->text('catatan_selisih')->nullable();
            $table->timestamps();
        });

        // Pemakaian Bahan
        Schema::create('pemakaian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pemakaian', 40)->unique();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_pemakaian');
            $table->string('nomor_spk', 50)->nullable();
            $table->text('keperluan')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('detail_pemakaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemakaian')->constrained('pemakaian')->cascadeOnDelete();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('pemakaian_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('tahun');
            $table->tinyInteger('bulan');
            $table->decimal('total_pemakaian', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['id_bahan', 'id_lokasi', 'tahun', 'bulan']);
        });

        // Mesin & Perbaikan
        Schema::create('mesin', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin', 30)->unique();
            $table->string('nama_mesin', 100);
            $table->string('tipe_mesin', 50)->nullable(); // Outdoor, Indoor, Cutting, Press, Offset
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('status', ['aktif', 'rusak', 'perbaikan', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan', 40)->unique();
            $table->foreignId('id_mesin')->constrained('mesin')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_lapor');
            $table->text('gejala');
            $table->text('kronologi');
            $table->enum('status', ['dilaporkan', 'diperiksa', 'diperbaiki', 'selesai'])->default('dilaporkan');
            $table->string('foto_kerusakan')->nullable();
            $table->foreignId('id_user_pelapor')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('detail_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_laporan_kerusakan')->constrained('laporan_kerusakan')->cascadeOnDelete();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->restrictOnDelete(); // suku cadang
            $table->decimal('jumlah', 12, 2);
            $table->string('foto_suku_cadang')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_perbaikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_laporan_kerusakan')->constrained('laporan_kerusakan')->cascadeOnDelete();
            $table->foreignId('id_mesin')->constrained('mesin')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_selesai');
            $table->text('tindakan');
            $table->string('teknisi', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Prediksi & ROP
        Schema::create('parameter_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('lead_time_hari')->default(3);
            $table->decimal('safety_factor', 4, 2)->default(1.65); // 95% service level
            $table->decimal('alpha', 4, 2)->default(0.20); // SES alpha factor
            $table->decimal('rop', 12, 2)->default(0);
            $table->decimal('safety_stock', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['id_bahan', 'id_lokasi']);
        });

        Schema::create('prediksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger('periode_tahun');
            $table->tinyInteger('periode_bulan');
            $table->decimal('hasil_prediksi', 12, 2);
            $table->string('metode', 50)->default('Single Exponential Smoothing');
            $table->timestamps();
        });

        Schema::create('saran_distribusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_lokasi_tujuan')->constrained('lokasi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('jumlah_saran', 12, 2);
            $table->enum('status', ['draft', 'diajukan', 'diabaikan'])->default('draft');
            $table->timestamps();
        });

        Schema::create('saran_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('jumlah_saran', 12, 2);
            $table->decimal('estimasi_biaya', 14, 2)->default(0);
            $table->enum('status', ['draft', 'diproses', 'diabaikan'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saran_pembelian');
        Schema::dropIfExists('saran_distribusi');
        Schema::dropIfExists('prediksi');
        Schema::dropIfExists('parameter_bahan');
        Schema::dropIfExists('riwayat_perbaikan');
        Schema::dropIfExists('detail_kerusakan');
        Schema::dropIfExists('laporan_kerusakan');
        Schema::dropIfExists('mesin');
        Schema::dropIfExists('pemakaian_bulanan');
        Schema::dropIfExists('detail_pemakaian');
        Schema::dropIfExists('pemakaian');
        Schema::dropIfExists('detail_pengiriman');
        Schema::dropIfExists('pengiriman');
        Schema::dropIfExists('detail_permintaan');
        Schema::dropIfExists('permintaan');
        Schema::dropIfExists('detail_masuk');
        Schema::dropIfExists('barang_masuk');
    }
};
