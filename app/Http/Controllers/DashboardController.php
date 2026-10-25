<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\DetailPengiriman;
use App\Models\LaporanKerusakan;
use App\Models\Lokasi;
use App\Models\Mesin;
use App\Models\MutasiStok;
use App\Models\Pemakaian;
use App\Models\Pengiriman;
use App\Models\Permintaan;
use App\Models\StokLokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isCabang = $user->isAdminCabang();
        $isOwner = $user->isOwner();
        $isAdminPusat = $user->isAdminPusat();
        $lokasiId = $user->id_lokasi;

        $pusatLokasi = Lokasi::where('tipe', 'pusat')->first();

        // Data Chart Distribusi Stok Bahan Utama (Gudang Pusat vs Cabang)
        $chartBahanList = Bahan::where('is_aktif', true)->take(6)->get();
        $chartLabels = $chartBahanList->map(fn($b) => substr($b->nama_bahan, 0, 18))->toArray();
        $chartDataPusat = [];
        $chartDataCabangTotal = [];

        foreach ($chartBahanList as $b) {
            $stokPusat = $pusatLokasi ? $b->getStokDiLokasi($pusatLokasi->id) : 0;
            $stokCabangTotal = StokLokasi::where('id_bahan', $b->id)
                ->whereHas('lokasi', fn($q) => $q->where('tipe', 'cabang'))
                ->sum('jumlah');

            $chartDataPusat[] = (float) $stokPusat;
            $chartDataCabangTotal[] = (float) $stokCabangTotal;
        }

        $totalJenisBahan = Bahan::where('is_aktif', true)->count();

        // =========================================================================
        // 1. DATA KHUSUS DASHBOARD OWNER (DIREKSI / EKSEKUTIF - READ ONLY)
        // =========================================================================
        if ($isOwner) {
            // Valuasi total aset stok seluruh gudang dan cabang
            $totalNilaiStok = (float) StokLokasi::join('bahan', 'stok_lokasi.id_bahan', '=', 'bahan.id')
                ->where('bahan.is_aktif', true)
                ->sum(DB::raw('stok_lokasi.jumlah * bahan.harga_estimasi'));

            $allStok = StokLokasi::with('bahan')->get();
            $totalKritis = $allStok->filter(fn($s) => $s->status_stok === 'Kritis')->count();
            $totalMenipis = $allStok->filter(fn($s) => $s->status_stok === 'Menipis')->count();
            $totalAman = $allStok->filter(fn($s) => $s->status_stok === 'Aman')->count();

            $totalMesin = Mesin::count();
            $totalMesinRusak = Mesin::where('status', 'rusak')->count();
            $pengirimanAktifCount = Pengiriman::where('status', 'dikirim')->count();
            $pengirimanSelisihCount = Pengiriman::where('status', 'selisih')->count();

            // Ringkasan kesehatan per cabang dan gudang pusat
            $lokasiKesehatan = Lokasi::with(['stokLokasi.bahan', 'mesin'])->get()->map(function ($lokasi) {
                $stok = $lokasi->stokLokasi;
                $kritis = $stok->filter(fn($s) => $s->status_stok === 'Kritis')->count();
                $menipis = $stok->filter(fn($s) => $s->status_stok === 'Menipis')->count();
                $aman = $stok->filter(fn($s) => $s->status_stok === 'Aman')->count();
                $nilai = $stok->sum(fn($s) => (float) $s->jumlah * (float) ($s->bahan->harga_estimasi ?? 0));
                $mesinRusak = $lokasi->mesin->where('status', 'rusak')->count();

                return (object) [
                    'id' => $lokasi->id,
                    'kode_lokasi' => $lokasi->kode_lokasi,
                    'nama_lokasi' => $lokasi->nama_lokasi,
                    'tipe' => $lokasi->tipe,
                    'penanggung_jawab' => $lokasi->penanggung_jawab,
                    'total_item' => $stok->count(),
                    'kritis' => $kritis,
                    'menipis' => $menipis,
                    'aman' => $aman,
                    'nilai_stok' => $nilai,
                    'mesin_rusak' => $mesinRusak,
                    'total_mesin' => $lokasi->mesin->count(),
                ];
            });

            // Aktivitas mutasi terbaru lintas unit
            $mutasiTerbaru = MutasiStok::with(['bahan', 'lokasi', 'user'])->latest('id')->take(6)->get();

            return view('dashboard', compact(
                'totalJenisBahan',
                'totalNilaiStok',
                'totalKritis',
                'totalMenipis',
                'totalAman',
                'totalMesin',
                'totalMesinRusak',
                'pengirimanAktifCount',
                'pengirimanSelisihCount',
                'lokasiKesehatan',
                'mutasiTerbaru',
                'chartLabels',
                'chartDataPusat',
                'chartDataCabangTotal'
            ));
        }

        // =========================================================================
        // 2. DATA KHUSUS DASHBOARD ADMIN PUSAT (KONTROL GUDANG PUSAT & LOGISTIK)
        // =========================================================================
        if ($isAdminPusat) {
            $pusatId = $pusatLokasi?->id;

            $stokPusat = StokLokasi::where('id_lokasi', $pusatId)->with('bahan.kategori')->get();
            $kritisPusat = 0;
            $menipisPusat = 0;
            $amanPusat = 0;
            $daftarPerluPesanPusat = collect();

            foreach ($stokPusat as $stok) {
                if ($stok->status_stok === 'Kritis') {
                    $kritisPusat++;
                    $daftarPerluPesanPusat->push($stok);
                } elseif ($stok->status_stok === 'Menipis') {
                    $menipisPusat++;
                    $daftarPerluPesanPusat->push($stok);
                } else {
                    $amanPusat++;
                }
            }

            // Permintaan cabang yang menunggu persetujuan pusat
            $permintaanMenungguList = Permintaan::with(['lokasiPemohon', 'userPemohon', 'details.bahan'])
                ->where('status', 'menunggu')
                ->latest('id')
                ->take(6)
                ->get();

            // Surat jalan pengiriman yang sedang transit
            $pengirimanTransitList = Pengiriman::with(['lokasiTujuan', 'userPengirim', 'details.bahan'])
                ->where('status', 'dikirim')
                ->latest('id')
                ->take(5)
                ->get();

            // Pengiriman dengan selisih yang butuh investigasi
            $pengirimanSelisihList = Pengiriman::with(['lokasiTujuan', 'userPengirim', 'details.bahan'])
                ->where('status', 'selisih')
                ->latest('id')
                ->take(5)
                ->get();

            // Kerusakan mesin di cabang yang menunggu disposisi / suku cadang
            $kerusakanAktifList = LaporanKerusakan::with(['mesin', 'lokasi', 'userPelapor', 'details.bahan'])
                ->whereIn('status', ['dilaporkan', 'diperiksa', 'diperbaiki'])
                ->latest('id')
                ->take(6)
                ->get();

            return view('dashboard', compact(
                'totalJenisBahan',
                'kritisPusat',
                'menipisPusat',
                'amanPusat',
                'daftarPerluPesanPusat',
                'permintaanMenungguList',
                'pengirimanTransitList',
                'pengirimanSelisihList',
                'kerusakanAktifList',
                'chartLabels',
                'chartDataPusat',
                'chartDataCabangTotal'
            ));
        }

        // =========================================================================
        // 3. DATA KHUSUS DASHBOARD ADMIN CABANG (OPERASIONAL UNIT CABANG LOKAL)
        // =========================================================================
        $cabangLokasi = $user->lokasi;
        $stokCabang = $lokasiId ? StokLokasi::where('id_lokasi', $lokasiId)->with('bahan.kategori')->get() : collect();

        $kritisCabang = 0;
        $menipisCabang = 0;
        $amanCabang = 0;
        $daftarPerluPesanCabang = collect();

        foreach ($stokCabang as $stok) {
            if ($stok->status_stok === 'Kritis') {
                $kritisCabang++;
                $daftarPerluPesanCabang->push($stok);
            } elseif ($stok->status_stok === 'Menipis') {
                $menipisCabang++;
                $daftarPerluPesanCabang->push($stok);
            } else {
                $amanCabang++;
            }
        }

        // Surat jalan dari pusat yang sedang dalam perjalanan menuju cabang ini
        $pengirimanMenujuCabang = $lokasiId ? Pengiriman::with(['lokasiAsal', 'userPengirim', 'details.bahan'])
            ->where('id_lokasi_tujuan', $lokasiId)
            ->where('status', 'dikirim')
            ->latest('id')
            ->get() : collect();

        // Pemakaian bahan harian terakhir di cabang ini
        $pemakaianTerbaru = $lokasiId ? Pemakaian::with(['details.bahan', 'user'])
            ->where('id_lokasi', $lokasiId)
            ->latest('id')
            ->take(5)
            ->get() : collect();

        // Status mesin cetak di cabang ini
        $mesinCabang = $lokasiId ? Mesin::where('id_lokasi', $lokasiId)
            ->with(['laporanKerusakan' => fn($q) => $q->whereIn('status', ['dilaporkan', 'diperiksa', 'diperbaiki'])])
            ->get() : collect();

        // Permintaan bahan yang diajukan oleh cabang ini
        $permintaanCabangList = $lokasiId ? Permintaan::with('details.bahan')
            ->where('id_lokasi_pemohon', $lokasiId)
            ->latest('id')
            ->take(5)
            ->get() : collect();

        return view('dashboard', compact(
            'cabangLokasi',
            'totalJenisBahan',
            'kritisCabang',
            'menipisCabang',
            'amanCabang',
            'daftarPerluPesanCabang',
            'pengirimanMenujuCabang',
            'pemakaianTerbaru',
            'mesinCabang',
            'permintaanCabangList'
        ));
    }
}
