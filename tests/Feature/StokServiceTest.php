<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\MutasiStok;
use App\Models\StokLokasi;
use App\Models\User;
use App\Services\StokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StokServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_stok_service_tambah_dan_kurang_stok_mencatat_mutasi(): void
    {
        $lokasi = Lokasi::create([
            'kode_lokasi' => 'PST-01',
            'nama_lokasi' => 'Gudang Pusat',
            'tipe' => 'pusat',
        ]);

        $kategori = Kategori::create([
            'kode_kategori' => 'KAT-OUT',
            'nama_kategori' => 'Outdoor',
        ]);

        $bahan = Bahan::create([
            'kode_bahan' => 'TEST-01',
            'nama_bahan' => 'Spanduk Flexi Test',
            'id_kategori' => $kategori->id,
            'satuan' => 'roll',
            'harga_estimasi' => 500000,
        ]);

        $user = User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_pusat',
            'id_lokasi' => $lokasi->id,
        ]);

        $stokService = app(StokService::class);

        // Tambah 20 roll
        $stok = $stokService->tambahStok($bahan->id, $lokasi->id, 20, 'masuk', 'Saldo awal', $user->id);
        $this->assertEquals(20, $stok->jumlah);
        $this->assertDatabaseHas('mutasi_stok', [
            'id_bahan' => $bahan->id,
            'id_lokasi' => $lokasi->id,
            'jenis' => 'masuk',
            'jumlah' => 20,
            'stok_sebelum' => 0,
            'stok_sesudah' => 20,
        ]);

        // Kurang 5 roll
        $stok = $stokService->kurangStok($bahan->id, $lokasi->id, 5, 'keluar', 'Dipakai', $user->id);
        $this->assertEquals(15, $stok->jumlah);
        $this->assertDatabaseHas('mutasi_stok', [
            'id_bahan' => $bahan->id,
            'id_lokasi' => $lokasi->id,
            'jenis' => 'keluar',
            'jumlah' => 5,
            'stok_sebelum' => 20,
            'stok_sesudah' => 15,
        ]);
    }
}
