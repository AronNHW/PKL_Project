<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\Lokasi;
use App\Models\ParameterBahan;
use App\Models\PemakaianBulanan;
use App\Models\Prediksi;
use App\Models\SaranDistribusi;
use App\Models\SaranPembelian;
use App\Models\StokLokasi;
use Illuminate\Support\Facades\DB;

class PrediksiService
{
    /**
     * Menjalankan kalkulasi Single Exponential Smoothing (SES) dan Reorder Point (ROP)
     * untuk seluruh bahan dan lokasi.
     */
    public function hitungSemua(int $tahunTarget, int $bulanTarget, float $defaultAlpha = 0.20): array
    {
        $bahanList = Bahan::where('is_aktif', true)->get();
        $lokasiList = Lokasi::where('is_aktif', true)->get();

        $prediksiDihasilkan = 0;
        $saranPembelianDihasilkan = 0;
        $saranDistribusiDihasilkan = 0;

        foreach ($bahanList as $bahan) {
            foreach ($lokasiList as $lokasi) {
                // Ambil atau buat parameter
                $param = ParameterBahan::firstOrCreate(
                    ['id_bahan' => $bahan->id, 'id_lokasi' => $lokasi->id],
                    [
                        'lead_time_hari' => $lokasi->isPusat() ? 7 : 3,
                        'safety_factor' => 1.65,
                        'alpha' => $defaultAlpha,
                        'rop' => 0,
                        'safety_stock' => 0,
                    ]
                );

                $alpha = (float) $param->alpha;

                // Ambil histori pemakaian 6 bulan terakhir
                $histori = PemakaianBulanan::where('id_bahan', $bahan->id)
                    ->where('id_lokasi', $lokasi->id)
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->take(12)
                    ->get();

                // Jika belum ada pemakaian, gunakan estimasi berbasis stok minimum
                $rataRataBulanan = $histori->count() > 0 
                    ? (float) $histori->avg('total_pemakaian') 
                    : max(5, (float) $bahan->getStokDiLokasi($lokasi->id) * 0.4);

                // SES Calculation
                $forecast = $rataRataBulanan;
                foreach ($histori as $h) {
                    $aktual = (float) $h->total_pemakaian;
                    $forecast = ($alpha * $aktual) + ((1 - $alpha) * $forecast);
                }

                // Simpan hasil prediksi
                Prediksi::updateOrCreate(
                    [
                        'id_bahan' => $bahan->id,
                        'id_lokasi' => $lokasi->id,
                        'periode_tahun' => $tahunTarget,
                        'periode_bulan' => $bulanTarget,
                    ],
                    [
                        'hasil_prediksi' => round($forecast, 2),
                        'metode' => 'Single Exponential Smoothing (α=' . $alpha . ')',
                    ]
                );
                $prediksiDihasilkan++;

                // Hitung Safety Stock & ROP
                // d = pemakaian harian (asumsi 26 hari kerja sebulan)
                $permintaanHarian = max(0.1, $forecast / 26);
                $leadTime = max(1, (int) $param->lead_time_hari);
                $safetyStock = round($param->safety_factor * sqrt($leadTime) * ($permintaanHarian * 0.3), 2);
                $rop = round(($permintaanHarian * $leadTime) + $safetyStock, 2);

                $param->update([
                    'safety_stock' => $safetyStock,
                    'rop' => $rop,
                ]);

                // Evaluasi apakah stok fisik saat ini <= ROP
                $stokFisik = $bahan->getStokDiLokasi($lokasi->id);
                if ($stokFisik <= $rop) {
                    $kebutuhan = max(5, round($forecast - $stokFisik + $safetyStock, 0));

                    if ($lokasi->isPusat()) {
                        // Pusat perlu order ke supplier (Saran Pembelian)
                        SaranPembelian::updateOrCreate(
                            ['id_bahan' => $bahan->id, 'status' => 'draft'],
                            [
                                'jumlah_saran' => $kebutuhan,
                                'estimasi_biaya' => $kebutuhan * (float) $bahan->harga_estimasi,
                            ]
                        );
                        $saranPembelianDihasilkan++;
                    } else {
                        // Cabang perlu pasokan dari pusat (Saran Distribusi)
                        SaranDistribusi::updateOrCreate(
                            ['id_bahan' => $bahan->id, 'id_lokasi_tujuan' => $lokasi->id, 'status' => 'draft'],
                            [
                                'jumlah_saran' => $kebutuhan,
                            ]
                        );
                        $saranDistribusiDihasilkan++;
                    }
                }
            }
        }

        return [
            'prediksi' => $prediksiDihasilkan,
            'saran_pembelian' => $saranPembelianDihasilkan,
            'saran_distribusi' => $saranDistribusiDihasilkan,
        ];
    }
}
