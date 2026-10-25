<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Lokasi;
use App\Models\ParameterBahan;
use App\Models\Prediksi;
use App\Models\SaranDistribusi;
use App\Models\SaranPembelian;
use App\Services\PrediksiService;
use Illuminate\Http\Request;

class PrediksiController extends Controller
{
    protected PrediksiService $prediksiService;

    public function __construct(PrediksiService $prediksiService)
    {
        $this->prediksiService = $prediksiService;
    }

    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('m'));

        $prediksiList = Prediksi::with(['bahan.kategori', 'lokasi'])
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->orderBy('id_lokasi')
            ->paginate(15);

        $parameterList = ParameterBahan::with(['bahan', 'lokasi'])->orderBy('id_bahan')->take(10)->get();
        $saranPembelian = SaranPembelian::with('bahan')->where('status', 'draft')->get();
        $saranDistribusi = SaranDistribusi::with(['bahan', 'lokasiTujuan'])->where('status', 'draft')->get();

        return view('prediksi.index', compact('prediksiList', 'parameterList', 'saranPembelian', 'saranDistribusi', 'tahun', 'bulan'));
    }

    /**
     * Hitung ulang prediksi & ROP on-demand
     */
    public function generate(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('m'));
        $alpha = (float) $request->input('alpha', 0.20);

        $hasil = $this->prediksiService->hitungSemua($tahun, $bulan, $alpha);

        return redirect()->route('prediksi.index', ['tahun' => $tahun, 'bulan' => $bulan])
            ->with('success', "Perhitungan prediksi Single Exponential Smoothing & ROP berhasil dijalankan. {$hasil['prediksi']} kalkulasi diproses.");
    }
}
