<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\Lokasi;
use App\Models\MutasiStok;
use App\Models\StokLokasi;
use Exception;
use Illuminate\Support\Facades\DB;

class StokService
{
    /**
     * Menambah stok di suatu lokasi dan mencatat mutasi.
     * Dibungkus dalam DB::transaction.
     */
    public function tambahStok(
        int $idBahan,
        int $idLokasi,
        float $jumlah,
        string $jenis,
        string $keterangan,
        ?int $userId = null,
        ?string $referensiTipe = null,
        ?int $referensiId = null
    ): StokLokasi {
        if ($jumlah <= 0) {
            throw new Exception("Jumlah stok yang ditambahkan harus lebih besar dari 0.");
        }

        return DB::transaction(function () use (
            $idBahan,
            $idLokasi,
            $jumlah,
            $jenis,
            $keterangan,
            $userId,
            $referensiTipe,
            $referensiId
        ) {
            $stokLokasi = StokLokasi::lockForUpdate()->firstOrCreate(
                ['id_bahan' => $idBahan, 'id_lokasi' => $idLokasi],
                ['jumlah' => 0, 'stok_minimum' => 5]
            );

            $stokSebelum = (float) $stokLokasi->jumlah;
            $stokSesudah = $stokSebelum + $jumlah;

            $stokLokasi->update(['jumlah' => $stokSesudah]);

            MutasiStok::create([
                'id_bahan' => $idBahan,
                'id_lokasi' => $idLokasi,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'referensi_tipe' => $referensiTipe,
                'referensi_id' => $referensiId,
                'keterangan' => $keterangan,
                'id_user' => $userId,
                'created_at' => now(),
            ]);

            return $stokLokasi->fresh();
        });
    }

    /**
     * Mengurangi stok di suatu lokasi dan mencatat mutasi.
     * Dibungkus dalam DB::transaction.
     */
    public function kurangStok(
        int $idBahan,
        int $idLokasi,
        float $jumlah,
        string $jenis,
        string $keterangan,
        ?int $userId = null,
        ?string $referensiTipe = null,
        ?int $referensiId = null
    ): StokLokasi {
        if ($jumlah <= 0) {
            throw new Exception("Jumlah stok yang dikurangkan harus lebih besar dari 0.");
        }

        return DB::transaction(function () use (
            $idBahan,
            $idLokasi,
            $jumlah,
            $jenis,
            $keterangan,
            $userId,
            $referensiTipe,
            $referensiId
        ) {
            $stokLokasi = StokLokasi::lockForUpdate()->firstOrCreate(
                ['id_bahan' => $idBahan, 'id_lokasi' => $idLokasi],
                ['jumlah' => 0, 'stok_minimum' => 5]
            );

            $stokSebelum = (float) $stokLokasi->jumlah;

            if ($stokSebelum < $jumlah) {
                $bahan = Bahan::find($idBahan);
                $lokasi = Lokasi::find($idLokasi);
                $namaBahan = $bahan ? $bahan->nama_bahan : "ID #$idBahan";
                $namaLokasi = $lokasi ? $lokasi->nama_lokasi : "ID #$idLokasi";
                $satuan = $bahan ? $bahan->satuan : "unit";

                throw new Exception("Stok {$namaBahan} di {$namaLokasi} tidak mencukupi. Tersedia: {$stokSebelum} {$satuan}, diminta: {$jumlah} {$satuan}.");
            }

            $stokSesudah = $stokSebelum - $jumlah;
            $stokLokasi->update(['jumlah' => $stokSesudah]);

            MutasiStok::create([
                'id_bahan' => $idBahan,
                'id_lokasi' => $idLokasi,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'referensi_tipe' => $referensiTipe,
                'referensi_id' => $referensiId,
                'keterangan' => $keterangan,
                'id_user' => $userId,
                'created_at' => now(),
            ]);

            return $stokLokasi->fresh();
        });
    }

    /**
     * Penyesuaian stok fisik (Stock Opname).
     */
    public function penyesuaianStok(
        int $idBahan,
        int $idLokasi,
        float $stokFisikBaru,
        string $keterangan,
        ?int $userId = null
    ): StokLokasi {
        if ($stokFisikBaru < 0) {
            throw new Exception("Jumlah stok fisik tidak boleh negatif.");
        }

        return DB::transaction(function () use (
            $idBahan,
            $idLokasi,
            $stokFisikBaru,
            $keterangan,
            $userId
        ) {
            $stokLokasi = StokLokasi::lockForUpdate()->firstOrCreate(
                ['id_bahan' => $idBahan, 'id_lokasi' => $idLokasi],
                ['jumlah' => 0, 'stok_minimum' => 5]
            );

            $stokSebelum = (float) $stokLokasi->jumlah;
            $selisih = $stokFisikBaru - $stokSebelum;

            $stokLokasi->update(['jumlah' => $stokFisikBaru]);

            MutasiStok::create([
                'id_bahan' => $idBahan,
                'id_lokasi' => $idLokasi,
                'jenis' => 'penyesuaian',
                'jumlah' => abs($selisih),
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokFisikBaru,
                'referensi_tipe' => 'stock_opname',
                'referensi_id' => null,
                'keterangan' => $keterangan . " (Selisih: " . ($selisih >= 0 ? "+{$selisih}" : "{$selisih}") . ")",
                'id_user' => $userId,
                'created_at' => now(),
            ]);

            return $stokLokasi->fresh();
        });
    }

    /**
     * Transfer stok dari lokasi asal ke tujuan (misal pengiriman surat jalan).
     */
    public function transferStok(
        int $idBahan,
        int $idLokasiAsal,
        int $idLokasiTujuan,
        float $jumlah,
        string $keterangan,
        ?int $userId = null,
        ?string $referensiTipe = null,
        ?int $referensiId = null
    ): array {
        return DB::transaction(function () use (
            $idBahan,
            $idLokasiAsal,
            $idLokasiTujuan,
            $jumlah,
            $keterangan,
            $userId,
            $referensiTipe,
            $referensiId
        ) {
            $asal = $this->kurangStok(
                $idBahan,
                $idLokasiAsal,
                $jumlah,
                'kirim',
                "Kirim ke lokasi tujuan - " . $keterangan,
                $userId,
                $referensiTipe,
                $referensiId
            );

            $tujuan = $this->tambahStok(
                $idBahan,
                $idLokasiTujuan,
                $jumlah,
                'terima',
                "Terima dari lokasi asal - " . $keterangan,
                $userId,
                $referensiTipe,
                $referensiId
            );

            return ['asal' => $asal, 'tujuan' => $tujuan];
        });
    }

    /**
     * Cek apakah stok cukup.
     */
    public function cekStokCukup(int $idBahan, int $idLokasi, float $jumlah): bool
    {
        $stok = StokLokasi::where('id_bahan', $idBahan)->where('id_lokasi', $idLokasi)->value('jumlah') ?? 0;
        return (float) $stok >= $jumlah;
    }

    /**
     * Atur stok minimum suatu bahan di lokasi tertentu.
     */
    public function setStokMinimum(int $idBahan, int $idLokasi, float $stokMinimum): StokLokasi
    {
        $stok = StokLokasi::firstOrCreate(
            ['id_bahan' => $idBahan, 'id_lokasi' => $idLokasi],
            ['jumlah' => 0]
        );

        $stok->update(['stok_minimum' => $stokMinimum]);
        return $stok;
    }
}
