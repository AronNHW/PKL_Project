<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\DetailPemakaian;
use App\Models\Lokasi;
use App\Models\Pemakaian;
use App\Models\PemakaianBulanan;
use App\Services\StokService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemakaianController extends Controller
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

        $query = Pemakaian::with(['lokasi', 'user', 'details.bahan']);

        if ($isCabang && $user->id_lokasi) {
            $query->where('id_lokasi', $user->id_lokasi);
        } elseif ($request->filled('lokasi')) {
            $query->where('id_lokasi', $request->lokasi);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_pemakaian', 'like', "%{$keyword}%")
                  ->orWhere('nomor_spk', 'like', "%{$keyword}%")
                  ->orWhere('keperluan', 'like', "%{$keyword}%");
            });
        }

        $pemakaianList = $query->latest('id')->paginate(15)->withQueryString();
        $lokasiList = Lokasi::where('is_aktif', true)->get();
        $bahanList = Bahan::where('is_aktif', true)->orderBy('nama_bahan')->get();

        return view('pemakaian.index', compact('pemakaianList', 'lokasiList', 'bahanList', 'isCabang'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $lokasiId = $user->isAdminCabang() ? $user->id_lokasi : $request->input('id_lokasi');

        if (!$lokasiId) {
            return back()->with('error', 'Lokasi penggunaan bahan wajib ditentukan.');
        }

        $validated = $request->validate([
            'tanggal_pemakaian' => 'required|date',
            'nomor_spk' => 'nullable|string|max:50',
            'keperluan' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:bahan,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($validated, $lokasiId, $user) {
                $year = date('Y', strtotime($validated['tanggal_pemakaian']));
                $month = (int) date('m', strtotime($validated['tanggal_pemakaian']));
                $count = Pemakaian::whereYear('tanggal_pemakaian', $year)->count() + 1;
                $nomor = 'PK-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                $pemakaian = Pemakaian::create([
                    'nomor_pemakaian' => $nomor,
                    'id_lokasi' => $lokasiId,
                    'tanggal_pemakaian' => $validated['tanggal_pemakaian'],
                    'nomor_spk' => $validated['nomor_spk'] ?? null,
                    'keperluan' => $validated['keperluan'],
                    'catatan' => $validated['catatan'] ?? null,
                    'id_user' => $user->id,
                ]);

                foreach ($validated['items'] as $item) {
                    DetailPemakaian::create([
                        'id_pemakaian' => $pemakaian->id,
                        'id_bahan' => $item['id_bahan'],
                        'jumlah' => $item['jumlah'],
                    ]);

                    // Potong stok di lokasi terkait melalui StokService
                    $this->stokService->kurangStok(
                        (int) $item['id_bahan'],
                        (int) $lokasiId,
                        (float) $item['jumlah'],
                        'keluar',
                        "Pemakaian Produksi ({$nomor}) " . ($validated['nomor_spk'] ? "[SPK: {$validated['nomor_spk']}]" : ''),
                        $user->id,
                        'pemakaian',
                        $pemakaian->id
                    );

                    // Akumulasi ke pemakaian_bulanan untuk data Exponential Smoothing
                    $rekapBulanan = PemakaianBulanan::firstOrCreate(
                        [
                            'id_bahan' => $item['id_bahan'],
                            'id_lokasi' => $lokasiId,
                            'tahun' => $year,
                            'bulan' => $month,
                        ],
                        ['total_pemakaian' => 0]
                    );

                    $rekapBulanan->increment('total_pemakaian', $item['jumlah']);
                }
            });

            return redirect()->route('pemakaian.index')->with('success', 'Pemakaian bahan berhasil dicatat dan stok fisik lokasi telah dipotong.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses pemakaian: ' . $e->getMessage());
        }
    }
}
