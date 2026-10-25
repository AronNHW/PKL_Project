<?php

namespace App\Console\Commands;

use App\Services\PrediksiService;
use Illuminate\Console\Command;

class HitungPrediksiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cetakstok:hitung-prediksi {--tahun=} {--bulan=} {--alpha=0.20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung prediksi pemakaian bahan (Single Exponential Smoothing) dan Reorder Point (ROP) seluruh cabang';

    /**
     * Execute the console command.
     */
    public function handle(PrediksiService $prediksiService): int
    {
        $tahun = (int) ($this->option('tahun') ?: date('Y'));
        $bulan = (int) ($this->option('bulan') ?: date('m'));
        $alpha = (float) $this->option('alpha');

        $this->info("Menjalankan kalkulasi prediksi SES & ROP untuk periode {$bulan}/{$tahun} dengan alpha {$alpha}...");

        $hasil = $prediksiService->hitungSemua($tahun, $bulan, $alpha);

        $this->table(
            ['Metrik', 'Jumlah Dihasilkan'],
            [
                ['Prediksi Bahan & Cabang', $hasil['prediksi']],
                ['Saran Pengadaan (Pusat)', $hasil['saran_pembelian']],
                ['Saran Distribusi (Cabang)', $hasil['saran_distribusi']],
            ]
        );

        $this->info("Perhitungan selesai dan berhasil disimpan.");

        return Command::SUCCESS;
    }
}
