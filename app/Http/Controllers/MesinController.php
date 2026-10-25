<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\DetailKerusakan;
use App\Models\LaporanKerusakan;
use App\Models\Lokasi;
use App\Models\Mesin;
use App\Models\RiwayatPerbaikan;
use App\Services\StokService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MesinController extends Controller
{
    protected StokService $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isCabang = $user->isAdminCabang();

        // Mesin
        $mesinQuery = Mesin::with('lokasi');
        if ($isCabang && $user->id_lokasi) {
            $mesinQuery->where('id_lokasi', $user->id_lokasi);
        }
        $mesinList = $mesinQuery->orderBy('kode_mesin')->get();

        // Laporan Kerusakan
        $laporanQuery = LaporanKerusakan::with(['mesin', 'lokasi', 'userPelapor', 'detailKerusakan.sukuCadang', 'riwayatPerbaikan']);
        if ($isCabang && $user->id_lokasi) {
            $laporanQuery->where('id_lokasi', $user->id_lokasi);
        }
        $laporanList = $laporanQuery->latest('id')->paginate(15)->withQueryString();

        // Suku cadang list (kategori Suku Cadang)
        $sukuCadangList = Bahan::whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'like', '%suku cadang%');
        })->get();

        $lokasiList = Lokasi::where('is_aktif', true)->get();

        return view('mesin.index', compact('mesinList', 'laporanList', 'sukuCadangList', 'lokasiList', 'isCabang'));
    }

    /**
     * Cabang melaporkan kerusakan mesin (Section 8: foto + kronologi + kebutuhan suku cadang)
     */
    public function laporKerusakan(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'id_mesin' => 'required|exists:mesin,id',
            'tanggal_lapor' => 'required|date',
            'gejala' => 'required|string|max:255',
            'kronologi' => 'required|string',
            'foto_kerusakan' => 'nullable|image|max:5120', // maks 5MB
            'id_bahan' => 'nullable|exists:bahan,id', // Suku cadang yang diduga rusak
            'jumlah_suku_cadang' => 'nullable|numeric|min:1',
        ]);

        $mesin = Mesin::findOrFail($validated['id_mesin']);
        if ($user->isAdminCabang() && (int) $user->id_lokasi !== (int) $mesin->id_lokasi) {
            abort(403, 'Akses Ditolak: Anda hanya dapat melaporkan kerusakan mesin di unit cabang Anda.');
        }
        $lokasiId = $mesin->id_lokasi;

        $fotoPath = null;
        if ($request->hasFile('foto_kerusakan')) {
            $fotoPath = $request->file('foto_kerusakan')->store('laporan-kerusakan', 'public');
        }

        DB::transaction(function () use ($validated, $mesin, $lokasiId, $fotoPath, $user) {
            $year = date('Y');
            $count = LaporanKerusakan::whereYear('tanggal_lapor', $year)->count() + 1;
            $nomor = 'LK-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $laporan = LaporanKerusakan::create([
                'nomor_laporan' => $nomor,
                'id_mesin' => $mesin->id,
                'id_lokasi' => $lokasiId,
                'tanggal_lapor' => $validated['tanggal_lapor'],
                'gejala' => $validated['gejala'],
                'kronologi' => $validated['kronologi'],
                'status' => 'dilaporkan',
                'foto_kerusakan' => $fotoPath,
                'id_user_pelapor' => $user->id,
            ]);

            // Ubah status mesin menjadi rusak
            $mesin->update(['status' => 'rusak']);

            if (!empty($validated['id_bahan'])) {
                DetailKerusakan::create([
                    'id_laporan_kerusakan' => $laporan->id,
                    'id_bahan' => $validated['id_bahan'],
                    'jumlah' => $validated['jumlah_suku_cadang'] ?? 1,
                ]);
            }
        });

        return redirect()->route('mesin.index')->with('success', 'Laporan kerusakan mesin berhasil diterbitkan dan status mesin diset "Rusak".');
    }

    /**
     * Update status pemeriksaan / perbaikan / penyelesaian laporan kerusakan (Section 8)
     */
    public function updateStatusKerusakan(Request $request, LaporanKerusakan $laporan)
    {
        $validated = $request->validate([
            'status' => 'required|in:diperiksa,diperbaiki,selesai',
            'tindakan' => 'nullable|string',
            'teknisi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $laporan) {
                $status = $validated['status'];
                $laporan->update(['status' => $status]);

                $mesin = $laporan->mesin;

                if ($status === 'diperiksa' || $status === 'diperbaiki') {
                    $mesin->update(['status' => 'perbaikan']);
                } elseif ($status === 'selesai') {
                    $mesin->update(['status' => 'aktif']);

                    // Catat Riwayat Perbaikan
                    RiwayatPerbaikan::create([
                        'id_laporan_kerusakan' => $laporan->id,
                        'id_mesin' => $mesin->id,
                        'tanggal_selesai' => now(),
                        'tindakan' => $validated['tindakan'] ?? 'Perbaikan dan penggantian suku cadang selesai.',
                        'teknisi' => $validated['teknisi'] ?? 'Teknisi Pusat',
                        'catatan' => $validated['catatan'] ?? null,
                        'id_user' => auth()->id(),
                    ]);

                    // Potong stok suku cadang jika ada detail kerusakan (Section 8)
                    foreach ($laporan->detailKerusakan as $detail) {
                        $this->stokService->kurangStok(
                            (int) $detail->id_bahan,
                            (int) $laporan->id_lokasi,
                            (float) $detail->jumlah,
                            'keluar',
                            "Suku cadang perbaikan mesin {$mesin->nama_mesin} ({$laporan->nomor_laporan})",
                            auth()->id(),
                            'service_mesin',
                            $laporan->id
                        );
                    }
                }
            });

            return redirect()->route('mesin.index')->with('success', 'Status penanganan kerusakan mesin berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}
