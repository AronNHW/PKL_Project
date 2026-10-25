<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BarangMasuk;
use App\Models\DetailMasuk;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Services\StokService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    protected StokService $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    public function index(Request $request)
    {
        $query = BarangMasuk::with(['supplier', 'lokasi', 'user', 'details.bahan']);

        if ($request->filled('supplier_id')) {
            $query->where('id_supplier', $request->supplier_id);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_masuk', 'like', "%{$keyword}%")
                  ->orWhere('nomor_po', 'like', "%{$keyword}%")
                  ->orWhere('nomor_surat_jalan_supplier', 'like', "%{$keyword}%");
            });
        }

        $barangMasukList = $query->latest('id')->paginate(15)->withQueryString();
        $supplierList = Supplier::orderBy('nama_supplier')->get();
        $lokasiList = Lokasi::where('is_aktif', true)->get();
        $bahanList = Bahan::where('is_aktif', true)->orderBy('nama_bahan')->get();

        return view('barang-masuk.index', compact('barangMasukList', 'supplierList', 'lokasiList', 'bahanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_supplier' => 'required|exists:supplier,id',
            'id_lokasi' => 'required|exists:lokasi,id',
            'tanggal_masuk' => 'required|date',
            'nomor_po' => 'nullable|string|max:50',
            'nomor_surat_jalan_supplier' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:bahan,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Generate nomor_masuk: BM-2026-0001
                $year = date('Y', strtotime($validated['tanggal_masuk']));
                $countThisYear = BarangMasuk::whereYear('tanggal_masuk', $year)->count() + 1;
                $nomorMasuk = 'BM-' . $year . '-' . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

                $barangMasuk = BarangMasuk::create([
                    'nomor_masuk' => $nomorMasuk,
                    'id_supplier' => $validated['id_supplier'],
                    'id_lokasi' => $validated['id_lokasi'],
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'nomor_po' => $validated['nomor_po'] ?? null,
                    'nomor_surat_jalan_supplier' => $validated['nomor_surat_jalan_supplier'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                    'id_user' => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    $hargaSatuan = $item['harga_satuan'] ?? 0;
                    $subtotal = $item['jumlah'] * $hargaSatuan;

                    DetailMasuk::create([
                        'id_barang_masuk' => $barangMasuk->id,
                        'id_bahan' => $item['id_bahan'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotal,
                    ]);

                    // Tambah stok ke gudang tujuan melalui StokService
                    $this->stokService->tambahStok(
                        (int) $item['id_bahan'],
                        (int) $validated['id_lokasi'],
                        (float) $item['jumlah'],
                        'masuk',
                        "Penerimaan Supplier ({$nomorMasuk})",
                        auth()->id(),
                        'barang_masuk',
                        $barangMasuk->id
                    );
                }
            });

            return redirect()->route('barang-masuk.index')->with('success', 'Penerimaan barang masuk dari supplier berhasil dicatat dan stok gudang telah bertambah.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses barang masuk: ' . $e->getMessage());
        }
    }
}
