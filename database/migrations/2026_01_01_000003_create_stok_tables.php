<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->decimal('stok_minimum', 12, 2)->default(5);
            $table->timestamps();

            $table->unique(['id_bahan', 'id_lokasi']);
        });

        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('jenis', ['masuk', 'keluar', 'kirim', 'terima', 'penyesuaian']);
            $table->decimal('jumlah', 12, 2);
            $table->decimal('stok_sebelum', 12, 2);
            $table->decimal('stok_sesudah', 12, 2);
            $table->string('referensi_tipe', 50)->nullable(); // contoh: 'barang_masuk', 'pengiriman', 'pemakaian', 'kerusakan', 'manual'
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('keterangan', 255);
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
        Schema::dropIfExists('stok_lokasi');
    }
};
