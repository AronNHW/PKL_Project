<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\StokLokasi;
use App\Services\StokService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BahanController extends Controller
{
    protected StokService $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    public function index(Request $request)
    {
        $query = Bahan::with(['kategori', 'stokLokasi.lokasi']);

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_bahan', 'like', "%{$keyword}%")
                  ->orWhere('kode_bahan', 'like', "%{$keyword}%")
                  ->orWhere('spesifikasi', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_aktif', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('is_aktif', false);
            }
        }

        $bahanList = $query->orderBy('nama_bahan')->paginate(15)->withQueryString();
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $lokasiList = Lokasi::where('is_aktif', true)->orderBy('tipe')->orderBy('nama_lokasi')->get();

        return view('bahan.index', compact('bahanList', 'kategoriList', 'lokasiList'));
    }

    public function create()
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        $lokasiList = Lokasi::where('is_aktif', true)->get();
        return view('bahan.create', compact('kategoriList', 'lokasiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_bahan' => 'required|string|max:30|unique:bahan,kode_bahan',
            'nama_bahan' => 'required|string|max:150',
            'id_kategori' => 'required|exists:kategori,id',
            'satuan' => 'required|string|max:30',
            'spesifikasi' => 'nullable|string',
            'harga_estimasi' => 'required|numeric|min:0',
            'stok_awal_pusat' => 'nullable|numeric|min:0',
            'stok_minimum' => 'nullable|numeric|min:0',
        ]);

        $bahan = Bahan::create([
            'kode_bahan' => strtoupper(trim($validated['kode_bahan'])),
            'nama_bahan' => trim($validated['nama_bahan']),
            'id_kategori' => $validated['id_kategori'],
            'satuan' => strtolower(trim($validated['satuan'])),
            'spesifikasi' => $validated['spesifikasi'] ?? null,
            'harga_estimasi' => $validated['harga_estimasi'],
            'is_aktif' => true,
        ]);

        // Inisialisasi stok minimum dan stok awal jika ada
        $allLokasi = Lokasi::all();
        $minStok = $validated['stok_minimum'] ?? 5;

        foreach ($allLokasi as $lokasi) {
            $this->stokService->setStokMinimum($bahan->id, $lokasi->id, $minStok);
        }

        if (!empty($validated['stok_awal_pusat']) && $validated['stok_awal_pusat'] > 0) {
            $pusat = Lokasi::where('tipe', 'pusat')->first();
            if ($pusat) {
                $this->stokService->tambahStok(
                    $bahan->id,
                    $pusat->id,
                    (float) $validated['stok_awal_pusat'],
                    'masuk',
                    'Saldo awal bahan baru',
                    auth()->id(),
                    'saldo_awal',
                    $bahan->id
                );
            }
        }

        return redirect()->route('bahan.index')->with('success', "Bahan {$bahan->nama_bahan} berhasil didaftarkan.");
    }

    public function show(Bahan $bahan)
    {
        $bahan->load(['kategori', 'stokLokasi.lokasi', 'mutasiStok' => function ($q) {
            $q->with('lokasi', 'user')->latest('id')->take(20);
        }]);

        $lokasiList = Lokasi::all();
        return view('bahan.show', compact('bahan', 'lokasiList'));
    }

    public function edit(Bahan $bahan)
    {
        $kategoriList = Kategori::orderBy('nama_kategori')->get();
        return view('bahan.edit', compact('bahan', 'kategoriList'));
    }

    public function update(Request $request, Bahan $bahan)
    {
        $validated = $request->validate([
            'kode_bahan' => 'required|string|max:30|unique:bahan,kode_bahan,' . $bahan->id,
            'nama_bahan' => 'required|string|max:150',
            'id_kategori' => 'required|exists:kategori,id',
            'satuan' => 'required|string|max:30',
            'spesifikasi' => 'nullable|string',
            'harga_estimasi' => 'required|numeric|min:0',
            'is_aktif' => 'required|boolean',
        ]);

        $bahan->update([
            'kode_bahan' => strtoupper(trim($validated['kode_bahan'])),
            'nama_bahan' => trim($validated['nama_bahan']),
            'id_kategori' => $validated['id_kategori'],
            'satuan' => strtolower(trim($validated['satuan'])),
            'spesifikasi' => $validated['spesifikasi'] ?? null,
            'harga_estimasi' => $validated['harga_estimasi'],
            'is_aktif' => $validated['is_aktif'],
        ]);

        return redirect()->route('bahan.index')->with('success', "Bahan {$bahan->nama_bahan} berhasil diperbarui.");
    }

    public function destroy(Bahan $bahan)
    {
        // Cek apakah bahan pernah memiliki mutasi atau transaksi
        $hasMutasi = $bahan->mutasiStok()->exists();
        if ($hasMutasi) {
            $bahan->update(['is_aktif' => false]);
            return redirect()->route('bahan.index')->with('success', "Bahan dinonaktifkan karena telah memiliki riwayat mutasi stok.");
        }

        $bahan->stokLokasi()->delete();
        $bahan->delete();

        return redirect()->route('bahan.index')->with('success', "Bahan berhasil dihapus.");
    }

    /**
     * Download format template CSV untuk impor master bahan
     */
    public function templateCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_master_bahan.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['kode_bahan', 'nama_bahan', 'kode_kategori', 'satuan', 'harga_estimasi', 'spesifikasi', 'stok_minimum']);
            fputcsv($handle, ['BHN-SAMPLE-01', 'Contoh Stiker Matte A3+', 'KAT-IND', 'roll', '350000', 'Spesifikasi detail bahan', '5']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Impor data master bahan dari CSV (Fase 1)
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $countSuccess = 0;
        $countError = 0;

        $kategoriMap = Kategori::pluck('id', 'kode_kategori')->toArray();
        $lokasiList = Lokasi::all();

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 4 || empty($row[0])) {
                continue;
            }

            $kodeBahan = strtoupper(trim($row[0]));
            $namaBahan = trim($row[1]);
            $kodeKategori = strtoupper(trim($row[2]));
            $satuan = strtolower(trim($row[3]));
            $hargaEstimasi = isset($row[4]) ? (float) str_replace(['.', ','], ['', '.'], $row[4]) : 0;
            $spesifikasi = $row[5] ?? null;
            $minStok = isset($row[6]) ? (float) $row[6] : 5;

            $kategoriId = $kategoriMap[$kodeKategori] ?? Kategori::first()?->id;

            if (!$kategoriId) {
                $countError++;
                continue;
            }

            $bahan = Bahan::updateOrCreate(
                ['kode_bahan' => $kodeBahan],
                [
                    'nama_bahan' => $namaBahan,
                    'id_kategori' => $kategoriId,
                    'satuan' => $satuan,
                    'harga_estimasi' => $hargaEstimasi,
                    'spesifikasi' => $spesifikasi,
                    'is_aktif' => true,
                ]
            );

            foreach ($lokasiList as $lokasi) {
                $this->stokService->setStokMinimum($bahan->id, $lokasi->id, $minStok);
            }

            $countSuccess++;
        }

        fclose($handle);

        return redirect()->route('bahan.index')->with('success', "Proses impor selesai. {$countSuccess} bahan berhasil diproses.");
    }
}
