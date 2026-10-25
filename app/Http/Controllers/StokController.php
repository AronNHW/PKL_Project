<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\MutasiStok;
use App\Models\StokLokasi;
use App\Services\StokService;
use Exception;
use Illuminate\Http\Request;

class StokController extends Controller
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
        $lokasiUser = $user->lokasi;

        $query = StokLokasi::with(['bahan.kategori', 'lokasi']);

        // Jika admin cabang, batasi HANYA pada lokasinya
        if ($isCabang && $user->id_lokasi) {
            $query->where('id_lokasi', $user->id_lokasi);
            $selectedLokasiId = $user->id_lokasi;
        } else {
            // Admin Pusat atau Owner bisa filter lokasi
            $selectedLokasiId = $request->input('lokasi');
            if ($selectedLokasiId) {
                $query->where('id_lokasi', $selectedLokasiId);
            }
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->whereHas('bahan', function ($q) use ($request) {
                $q->where('id_kategori', $request->kategori);
            });
        }

        // Pencarian Bahan
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->whereHas('bahan', function ($q) use ($keyword) {
                $q->where('nama_bahan', 'like', "%{$keyword}%")
                  ->orWhere('kode_bahan', 'like', "%{$keyword}%");
            });
        }

        $allStok = $query->join('bahan', 'stok_lokasi.id_bahan', '=', 'bahan.id')
                         ->select('stok_lokasi.*')
                         ->orderBy('bahan.nama_bahan')
                         ->get();

        // Filter Status secara memory collection agar kalkulasi status_stok akurat
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);
            $allStok = $allStok->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status_stok) === $statusFilter;
            });
        }

        // Paginasi manual untuk collection
        $perPage = 15;
        $currentPage = (int) $request->input('page', 1);
        $pagedData = $allStok->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $stokList = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $allStok->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $lokasiList = Lokasi::where('is_aktif', true)->orderBy('tipe')->orderBy('nama_lokasi')->get();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();

        return view('stok.index', compact('stokList', 'lokasiList', 'kategoriList', 'selectedLokasiId', 'isCabang', 'lokasiUser'));
    }

    public function mutasi(Request $request)
    {
        $user = auth()->user();
        $isCabang = $user->isAdminCabang();

        $query = MutasiStok::with(['bahan.kategori', 'lokasi', 'user']);

        if ($isCabang && $user->id_lokasi) {
            $query->where('id_lokasi', $user->id_lokasi);
        } elseif ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('bahan_id')) {
            $query->where('id_bahan', $request->bahan_id);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->whereHas('bahan', function ($q) use ($keyword) {
                $q->where('nama_bahan', 'like', "%{$keyword}%")
                  ->orWhere('kode_bahan', 'like', "%{$keyword}%");
            });
        }

        $mutasiList = $query->latest('id')->paginate(20)->withQueryString();
        $lokasiList = Lokasi::all();
        $bahanList = Bahan::orderBy('nama_bahan')->get();

        return view('stok.mutasi', compact('mutasiList', 'lokasiList', 'bahanList', 'isCabang'));
    }

    /**
     * Penyesuaian stok fisik (Stock Opname)
     */
    public function sesuaikan(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'id_bahan' => 'required|exists:bahan,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'jumlah_fisik' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
        ]);

        if ($user->isAdminCabang() && (int) $user->id_lokasi !== (int) $validated['id_lokasi']) {
            abort(403, 'Anda tidak memiliki hak akses mengubah stok cabang lain.');
        }

        try {
            $this->stokService->penyesuaianStok(
                (int) $validated['id_bahan'],
                (int) $validated['id_lokasi'],
                (float) $validated['jumlah_fisik'],
                $validated['keterangan'],
                $user->id
            );

            return back()->with('success', 'Penyesuaian stok (Stock Opname) berhasil dicatat ke riwayat mutasi.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui batas stok minimum
     */
    public function updateMinimum(Request $request)
    {
        $validated = $request->validate([
            'id_bahan' => 'required|exists:bahan,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'stok_minimum' => 'required|numeric|min:0',
        ]);

        $stok = StokLokasi::where('id_bahan', $validated['id_bahan'])
            ->where('id_lokasi', $validated['id_lokasi'])
            ->firstOrFail();

        $stok->update(['stok_minimum' => $validated['stok_minimum']]);

        return back()->with('success', 'Batas stok minimum berhasil diperbarui.');
    }
}
