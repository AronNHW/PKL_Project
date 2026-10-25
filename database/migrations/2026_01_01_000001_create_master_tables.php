<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lokasi', 20)->unique();
            $table->string('nama_lokasi', 100);
            $table->enum('tipe', ['pusat', 'cabang'])->default('cabang');
            $table->string('alamat')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('penanggung_jawab', 100)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori', 20)->unique();
            $table->string('nama_kategori', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('kode_supplier', 20)->unique();
            $table->string('nama_supplier', 150);
            $table->string('kontak_person', 100)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('bahan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan', 30)->unique();
            $table->string('nama_bahan', 150);
            $table->foreignId('id_kategori')->constrained('kategori')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('satuan', 30); // roll, rim, liter, pcs, box, meter
            $table->text('spesifikasi')->nullable();
            $table->decimal('harga_estimasi', 14, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan');
        Schema::dropIfExists('supplier');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('lokasi');
    }
};
