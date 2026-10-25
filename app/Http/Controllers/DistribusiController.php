<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\DetailPengiriman;
use App\Models\DetailPermintaan;
use App\Models\Lokasi;
use App\Models\Pengiriman;
use App\Models\Permintaan;
use App\Services\StokService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
    protected StokService $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    /**
     * Halaman Permintaan Cabang
     */
    public function permintaan(Request $request)
    {
        $user = auth()->user();
        $isCabang = $user->isAdminCabang();

        $query = Permintaan::with(['lokasiPemohon', 'userPemohon', 'userPenyetuju', 'details.bahan']);

        if ($isCabang && $user->id_lokasi) {
            $query->where('id_lokasi_pemohon', $user->id_lokasi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permintaanList = $query->latest('id')->paginate(15)->withQueryString();
        $bahanList = Bahan::where('is_aktif', true)->orderBy('nama_bahan')->get();

        return view('distribusi.permintaan', compact('permintaanList', 'bahanList', 'isCabang'));
    }

    /**
     * Cabang membuat permintaan bahan baru
     */
    public function storePermintaan(Request $request)
    {
        $user = auth()->user();
        $lokasiId = $user->isAdminCabang() ? $user->id_lokasi : $request->input('id_lokasi_pemohon');

        if (!$lokasiId) {
            return back()->with('error', 'Lokasi pemohon wajib ditentukan.');
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:bahan,id',
            'items.*.jumlah_minta' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($validated, $lokasiId, $user) {
            $year = date('Y');
            $count = Permintaan::whereYear('tanggal_permintaan', $year)->count() + 1;
            $nomor = 'PR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $permintaan = Permintaan::create([
                'nomor_permintaan' => $nomor,
                'id_lokasi_pemohon' => $lokasiId,
                'tanggal_permintaan' => now(),
                'status' => 'menunggu',
                'catatan' => $validated['catatan'] ?? null,
                'id_user_pemohon' => $user->id,
            ]);

            foreach ($validated['items'] as $item) {
                DetailPermintaan::create([
                    'id_permintaan' => $permintaan->id,
                    'id_bahan' => $item['id_bahan'],
                    'jumlah_minta' => $item['jumlah_minta'],
                    'jumlah_disetujui' => $item['jumlah_minta'],
                ]);
            }
        });

        return redirect()->route('distribusi.permintaan')->with('success', 'Permintaan bahan berhasil diajukan dan sedang menunggu persetujuan gudang pusat.');
    }

    /**
     * Persetujuan / Penolakan Permintaan oleh Admin Pusat
     */
    public function updateStatusPermintaan(Request $request, Permintaan $permintaan)
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan_penolakan' => 'nullable|string',
        ]);

        $permintaan->update([
            'status' => $validated['status'],
            'alasan_penolakan' => $validated['alasan_penolakan'] ?? null,
            'id_user_penyetuju' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        $msg = $validated['status'] === 'disetujui' ? 'Permintaan disetujui.' : 'Permintaan ditolak.';
        return redirect()->route('distribusi.permintaan')->with('success', $msg);
    }

    /**
     * Halaman Surat Jalan & Pengiriman Distribusi
     */
    public function pengiriman(Request $request)
    {
        $user = auth()->user();
        $isCabang = $user->isAdminCabang();

        $query = Pengiriman::with(['lokasiAsal', 'lokasiTujuan', 'userPengirim', 'userPenerima', 'details.bahan', 'permintaan']);

        if ($isCabang && $user->id_lokasi) {
            $query->where(function ($q) use ($user) {
                $q->where('id_lokasi_asal', $user->id_lokasi)
                  ->orWhere('id_lokasi_tujuan', $user->id_lokasi);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengirimanList = $query->latest('id')->paginate(15)->withQueryString();
        $lokasiCabangList = Lokasi::where('tipe', 'cabang')->where('is_aktif', true)->get();
        $bahanList = Bahan::where('is_aktif', true)->orderBy('nama_bahan')->get();

        return view('distribusi.pengiriman', compact('pengirimanList', 'lokasiCabangList', 'bahanList', 'isCabang'));
    }

    /**
     * Gudang Pusat membuat Surat Jalan (mengurangi stok pusat melalui StokService)
     */
    public function storePengiriman(Request $request)
    {
        $user = auth()->user();
        $pusat = Lokasi::where('tipe', 'pusat')->firstOrFail();

        $validated = $request->validate([
            'id_lokasi_tujuan' => 'required|exists:lokasi,id',
            'tanggal_kirim' => 'required|date',
            'ekspedisi_sopir' => 'nullable|string|max:100',
            'catatan_kirim' => 'nullable|string',
            'id_permintaan' => 'nullable|exists:permintaan,id',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:bahan,id',
            'items.*.jumlah_kirim' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($validated, $pusat, $user) {
                $year = date('Y', strtotime($validated['tanggal_kirim']));
                $count = Pengiriman::whereYear('tanggal_kirim', $year)->count() + 1;
                $nomorSJ = 'SJ-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                $pengiriman = Pengiriman::create([
                    'nomor_surat_jalan' => $nomorSJ,
                    'id_permintaan' => $validated['id_permintaan'] ?? null,
                    'id_lokasi_asal' => $pusat->id,
                    'id_lokasi_tujuan' => $validated['id_lokasi_tujuan'],
                    'tanggal_kirim' => $validated['tanggal_kirim'],
                    'status' => 'dikirim',
                    'ekspedisi_sopir' => $validated['ekspedisi_sopir'] ?? null,
                    'catatan_kirim' => $validated['catatan_kirim'] ?? null,
                    'id_user_pengirim' => $user->id,
                ]);

                foreach ($validated['items'] as $item) {
                    DetailPengiriman::create([
                        'id_pengiriman' => $pengiriman->id,
                        'id_bahan' => $item['id_bahan'],
                        'jumlah_kirim' => $item['jumlah_kirim'],
                        'jumlah_terima' => 0,
                        'selisih' => 0,
                    ]);

                    // Kurang stok di pusat via StokService
                    $this->stokService->kurangStok(
                        (int) $item['id_bahan'],
                        $pusat->id,
                        (float) $item['jumlah_kirim'],
                        'kirim',
                        "Pengiriman Distribusi {$nomorSJ}",
                        $user->id,
                        'pengiriman',
                        $pengiriman->id
                    );
                }

                if (!empty($validated['id_permintaan'])) {
                    Permintaan::where('id', $validated['id_permintaan'])->update(['status' => 'diproses']);
                }
            });

            return redirect()->route('distribusi.pengiriman')->with('success', 'Surat Jalan berhasil diterbitkan. Stok gudang pusat telah dipotong.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses pengiriman: ' . $e->getMessage());
        }
    }

    /**
     * Cabang mengonfirmasi penerimaan barang dan input rekonsiliasi
     */
    public function konfirmasiPenerimaan(Request $request, Pengiriman $pengiriman)
    {
        $user = auth()->user();

        if ($user->isAdminCabang() && (int) $user->id_lokasi !== (int) $pengiriman->id_lokasi_tujuan) {
            abort(403, 'Anda hanya dapat mengonfirmasi pengiriman ke cabang Anda.');
        }

        if ($pengiriman->status !== 'dikirim') {
            return back()->with('error', 'Pengiriman ini sudah pernah dikonfirmasi sebelumnya.');
        }

        $validated = $request->validate([
            'tanggal_terima' => 'required|date',
            'catatan_terima' => 'nullable|string',
            'items' => 'required|array',
            'items.*.detail_id' => 'required|exists:detail_pengiriman,id',
            'items.*.jumlah_terima' => 'required|numeric|min:0',
            'items.*.catatan_selisih' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $pengiriman, $user) {
                $adaSelisih = false;

                foreach ($validated['items'] as $itemData) {
                    $detail = DetailPengiriman::find($itemData['detail_id']);
                    if (!$detail || $detail->id_pengiriman !== $pengiriman->id) {
                        continue;
                    }

                    $jmlKirim = (float) $detail->jumlah_kirim;
                    $jmlTerima = (float) $itemData['jumlah_terima'];
                    $selisih = $jmlTerima - $jmlKirim;

                    if ($selisih != 0) {
                        $adaSelisih = true;
                    }

                    $detail->update([
                        'jumlah_terima' => $jmlTerima,
                        'selisih' => $selisih,
                        'catatan_selisih' => $itemData['catatan_selisih'] ?? null,
                    ]);

                    // Tambah stok ke cabang tujuan sesuai jumlah fisik nyata yang diterima
                    if ($jmlTerima > 0) {
                        $this->stokService->tambahStok(
                            $detail->id_bahan,
                            $pengiriman->id_lokasi_tujuan,
                            $jmlTerima,
                            'terima',
                            "Penerimaan Surat Jalan {$pengiriman->nomor_surat_jalan}" . ($selisih != 0 ? " (Ada selisih {$selisih})" : ''),
                            $user->id,
                            'penerimaan',
                            $pengiriman->id
                        );
                    }
                }

                $statusFinal = $adaSelisih ? 'selisih' : 'diterima';

                $pengiriman->update([
                    'tanggal_terima' => $validated['tanggal_terima'],
                    'status' => $statusFinal,
                    'catatan_terima' => $validated['catatan_terima'] ?? null,
                    'id_user_penerima' => $user->id,
                ]);

                if ($pengiriman->id_permintaan) {
                    Permintaan::where('id', $pengiriman->id_permintaan)->update(['status' => 'selesai']);
                }
            });

            return redirect()->route('distribusi.pengiriman')->with('success', 'Konfirmasi penerimaan berhasil disimpan dan stok cabang telah disesuaikan.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengonfirmasi penerimaan: ' . $e->getMessage());
        }
    }
}
