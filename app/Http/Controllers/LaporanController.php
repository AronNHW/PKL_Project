<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BarangMasuk;
use App\Models\LaporanKerusakan;
use App\Models\Lokasi;
use App\Models\MutasiStok;
use App\Models\Pemakaian;
use App\Models\Pengiriman;
use App\Models\StokLokasi;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isCabang = $user->isAdminCabang();

        $tipeLaporan = $request->input('tipe', 'stok');
        $lokasiId = $isCabang ? $user->id_lokasi : $request->input('lokasi_id');
        $tglMulai = $request->input('tgl_mulai', date('Y-m-01'));
        $tglSelesai = $request->input('tgl_selesai', date('Y-m-d'));

        $dataLaporan = collect();

        if ($tipeLaporan === 'stok') {
            $query = StokLokasi::with(['bahan.kategori', 'lokasi']);
            if ($lokasiId) {
                $query->where('id_lokasi', $lokasiId);
            }
            $dataLaporan = $query->join('bahan', 'stok_lokasi.id_bahan', '=', 'bahan.id')
                                 ->select('stok_lokasi.*')
                                 ->orderBy('bahan.nama_bahan')
                                 ->get();
        } elseif ($tipeLaporan === 'mutasi') {
            $query = MutasiStok::with(['bahan', 'lokasi', 'user'])
                ->whereDate('created_at', '>=', $tglMulai)
                ->whereDate('created_at', '<=', $tglSelesai);
            if ($lokasiId) {
                $query->where('id_lokasi', $lokasiId);
            }
            $dataLaporan = $query->latest('id')->get();
        } elseif ($tipeLaporan === 'distribusi') {
            $query = Pengiriman::with(['lokasiAsal', 'lokasiTujuan', 'details.bahan'])
                ->whereDate('tanggal_kirim', '>=', $tglMulai)
                ->whereDate('tanggal_kirim', '<=', $tglSelesai);
            if ($lokasiId) {
                $query->where(function ($q) use ($lokasiId) {
                    $q->where('id_lokasi_asal', $lokasiId)->orWhere('id_lokasi_tujuan', $lokasiId);
                });
            }
            $dataLaporan = $query->latest('id')->get();
        } elseif ($tipeLaporan === 'pemakaian') {
            $query = Pemakaian::with(['lokasi', 'user', 'details.bahan'])
                ->whereDate('tanggal_pemakaian', '>=', $tglMulai)
                ->whereDate('tanggal_pemakaian', '<=', $tglSelesai);
            if ($lokasiId) {
                $query->where('id_lokasi', $lokasiId);
            }
            $dataLaporan = $query->latest('id')->get();
        } elseif ($tipeLaporan === 'mesin') {
            $query = LaporanKerusakan::with(['mesin', 'lokasi', 'userPelapor', 'riwayatPerbaikan'])
                ->whereDate('tanggal_lapor', '>=', $tglMulai)
                ->whereDate('tanggal_lapor', '<=', $tglSelesai);
            if ($lokasiId) {
                $query->where('id_lokasi', $lokasiId);
            }
            $dataLaporan = $query->latest('id')->get();
        }

        $lokasiList = Lokasi::where('is_aktif', true)->get();

        return view('laporan.index', compact(
            'tipeLaporan',
            'lokasiId',
            'tglMulai',
            'tglSelesai',
            'dataLaporan',
            'lokasiList',
            'isCabang'
        ));
    }

    /**
     * Ekspor CSV Data Laporan
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $tipe = $request->input('tipe', 'stok');
        $fileName = 'laporan_' . $tipe . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($tipe, $request) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($tipe === 'stok') {
                fputcsv($handle, ['Kode Bahan', 'Nama Bahan', 'Kategori', 'Lokasi', 'Stok Fisik', 'Batas Min', 'Status']);
                $stok = StokLokasi::with(['bahan.kategori', 'lokasi'])->get();
                foreach ($stok as $s) {
                    fputcsv($handle, [
                        $s->bahan->kode_bahan,
                        $s->bahan->nama_bahan,
                        $s->bahan->kategori->nama_kategori ?? '-',
                        $s->lokasi->nama_lokasi,
                        $s->jumlah . ' ' . $s->bahan->satuan,
                        $s->stok_minimum . ' ' . $s->bahan->satuan,
                        $s->status_stok,
                    ]);
                }
            } elseif ($tipe === 'mutasi') {
                fputcsv($handle, ['Waktu', 'Lokasi', 'Kode Bahan', 'Nama Bahan', 'Jenis Mutasi', 'Sebelum', 'Mutasi', 'Sesudah', 'Keterangan']);
                $mutasi = MutasiStok::with(['bahan', 'lokasi'])->latest('id')->take(500)->get();
                foreach ($mutasi as $m) {
                    fputcsv($handle, [
                        $m->created_at->format('Y-m-d H:i:s'),
                        $m->lokasi->kode_lokasi,
                        $m->bahan->kode_bahan,
                        $m->bahan->nama_bahan,
                        $m->jenis_label,
                        $m->stok_sebelum,
                        $m->jumlah,
                        $m->stok_sesudah,
                        $m->keterangan,
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }
}
