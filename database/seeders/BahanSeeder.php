<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\User;
use App\Services\StokService;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    public function run(): void
    {
        $katOut = Kategori::where('kode_kategori', 'KAT-OUT')->first();
        $katInd = Kategori::where('kode_kategori', 'KAT-IND')->first();
        $katKrt = Kategori::where('kode_kategori', 'KAT-KRT')->first();
        $katTnt = Kategori::where('kode_kategori', 'KAT-TNT')->first();
        $katSkc = Kategori::where('kode_kategori', 'KAT-SKC')->first();

        $pusat = Lokasi::where('kode_lokasi', 'PST-01')->first();
        $cabangs = Lokasi::where('tipe', 'cabang')->get();
        $adminPusat = User::where('role', 'admin_pusat')->first();

        $stokService = app(StokService::class);

        $daftarBahan = [
            // Outdoor
            [
                'kode_bahan' => 'BHN-FLX-280',
                'nama_bahan' => 'Spanduk Flexi Frontlite 280gr (3.2m x 70m)',
                'id_kategori' => $katOut->id,
                'satuan' => 'roll',
                'spesifikasi' => 'Ketebalan 280 gsm, lebar 3.2m panjang 70m, permukaan semi-gloss, serat polyester kuat',
                'harga_estimasi' => 580000,
                'stok_pusat' => 45,
                'min_pusat' => 10,
                'stok_cabang' => 12,
                'min_cabang' => 5,
            ],
            [
                'kode_bahan' => 'BHN-FLX-440',
                'nama_bahan' => 'Spanduk Flexi Korea 440gr (3.2m x 50m)',
                'id_kategori' => $katOut->id,
                'satuan' => 'roll',
                'spesifikasi' => 'Heavy duty 440 gsm, high tenacity, tahan UV luar ruangan hingga 2 tahun',
                'harga_estimasi' => 950000,
                'stok_pusat' => 28,
                'min_pusat' => 8,
                'stok_cabang' => 4,
                'min_cabang' => 5, // Status: Menipis di cabang
            ],
            [
                'kode_bahan' => 'BHN-FLX-BCL',
                'nama_bahan' => 'Flexi Backlite 510gr (2.5m x 50m)',
                'id_kategori' => $katOut->id,
                'satuan' => 'roll',
                'spesifikasi' => 'Translucent untuk neonbox outdoor, daya tembus cahaya merata',
                'harga_estimasi' => 1250000,
                'stok_pusat' => 15,
                'min_pusat' => 5,
                'stok_cabang' => 2,
                'min_cabang' => 3,
            ],

            // Indoor
            [
                'kode_bahan' => 'BHN-VIN-GLS',
                'nama_bahan' => 'Stiker Vinyl Glossy Ritrama (1.27m x 50m)',
                'id_kategori' => $katInd->id,
                'satuan' => 'roll',
                'spesifikasi' => 'Permukaan glossy putih bersih, lem adhesive permanen, anti kerut',
                'harga_estimasi' => 720000,
                'stok_pusat' => 60,
                'min_pusat' => 15,
                'stok_cabang' => 18,
                'min_cabang' => 6,
            ],
            [
                'kode_bahan' => 'BHN-VIN-MAT',
                'nama_bahan' => 'Stiker Vinyl Matte Camel (1.07m x 50m)',
                'id_kategori' => $katInd->id,
                'satuan' => 'roll',
                'spesifikasi' => 'Permukaan doff lembut tanpa pantulan cahaya, cocok untuk signage indoor',
                'harga_estimasi' => 680000,
                'stok_pusat' => 35,
                'min_pusat' => 10,
                'stok_cabang' => 5,
                'min_cabang' => 6, // Menipis
            ],
            [
                'kode_bahan' => 'BHN-ALB-STD',
                'nama_bahan' => 'Albatros Matte Synthetic Paper (0.914m x 30m)',
                'id_kategori' => $katInd->id,
                'satuan' => 'roll',
                'spesifikasi' => 'Kertas sintetis untuk X-Banner dan Roll Up banner, tidak mudah sobek',
                'harga_estimasi' => 380000,
                'stok_pusat' => 50,
                'min_pusat' => 12,
                'stok_cabang' => 8,
                'min_cabang' => 4,
            ],

            // Kertas & Karton
            [
                'kode_bahan' => 'BHN-KRT-AP260',
                'nama_bahan' => 'Art Paper 260gr Plano (65cm x 100cm)',
                'id_kategori' => $katKrt->id,
                'satuan' => 'rim',
                'spesifikasi' => 'Kertas licin dua sisi 260 gsm, standar brosur eksklusif, flyer, kalender',
                'harga_estimasi' => 310000,
                'stok_pusat' => 120,
                'min_pusat' => 30,
                'stok_cabang' => 25,
                'min_cabang' => 10,
            ],
            [
                'kode_bahan' => 'BHN-KRT-AC310',
                'nama_bahan' => 'Art Carton 310gr Plano (79cm x 109cm)',
                'id_kategori' => $katKrt->id,
                'satuan' => 'rim',
                'spesifikasi' => 'Karton tebal kaku dua sisi licin 310 gsm, untuk cover buku, packaging, hangtag',
                'harga_estimasi' => 460000,
                'stok_pusat' => 85,
                'min_pusat' => 20,
                'stok_cabang' => 15,
                'min_cabang' => 8,
            ],
            [
                'kode_bahan' => 'BHN-KRT-HVS80',
                'nama_bahan' => 'Kertas HVS 80gr A3+',
                'id_kategori' => $katKrt->id,
                'satuan' => 'rim',
                'spesifikasi' => 'Ukuran 32cm x 48cm 80 gsm putih bersih untuk cetak dokumen dan kop surat digital',
                'harga_estimasi' => 95000,
                'stok_pusat' => 200,
                'min_pusat' => 40,
                'stok_cabang' => 40,
                'min_cabang' => 15,
            ],

            // Tinta & Kimia
            [
                'kode_bahan' => 'BHN-TNT-CYAN',
                'nama_bahan' => 'Tinta Eco Solvent Cyan (C)',
                'id_kategori' => $katTnt->id,
                'satuan' => 'liter',
                'spesifikasi' => 'Tinta ramah lingkungan pigment tinggi untuk print head DX5 / XP600 / i3200',
                'harga_estimasi' => 275000,
                'stok_pusat' => 50,
                'min_pusat' => 15,
                'stok_cabang' => 6,
                'min_cabang' => 4,
            ],
            [
                'kode_bahan' => 'BHN-TNT-MAGENTA',
                'nama_bahan' => 'Tinta Eco Solvent Magenta (M)',
                'id_kategori' => $katTnt->id,
                'satuan' => 'liter',
                'spesifikasi' => 'Tinta eco solvent kemasan botol 1 liter, warna magenta tajam tahan pudar',
                'harga_estimasi' => 275000,
                'stok_pusat' => 48,
                'min_pusat' => 15,
                'stok_cabang' => 7,
                'min_cabang' => 4,
            ],
            [
                'kode_bahan' => 'BHN-TNT-YELLOW',
                'nama_bahan' => 'Tinta Eco Solvent Yellow (Y)',
                'id_kategori' => $katTnt->id,
                'satuan' => 'liter',
                'spesifikasi' => 'Tinta eco solvent kemasan botol 1 liter warna kuning pekat',
                'harga_estimasi' => 275000,
                'stok_pusat' => 45,
                'min_pusat' => 15,
                'stok_cabang' => 5,
                'min_cabang' => 4,
            ],
            [
                'kode_bahan' => 'BHN-TNT-BLACK',
                'nama_bahan' => 'Tinta Eco Solvent Black (K)',
                'id_kategori' => $katTnt->id,
                'satuan' => 'liter',
                'spesifikasi' => 'Tinta eco solvent kemasan botol 1 liter warna hitam pekat',
                'harga_estimasi' => 275000,
                'stok_pusat' => 60,
                'min_pusat' => 15,
                'stok_cabang' => 9,
                'min_cabang' => 4,
            ],

            // Suku Cadang (Kategori: Suku Cadang)
            [
                'kode_bahan' => 'BHN-SKC-HDXP600',
                'nama_bahan' => 'Print Head Epson XP600 Unlocked',
                'id_kategori' => $katSkc->id,
                'satuan' => 'pcs',
                'spesifikasi' => 'Original print head XP600 6-channel nozzle untuk mesin outdoor / eco solvent',
                'harga_estimasi' => 3800000,
                'stok_pusat' => 8,
                'min_pusat' => 3,
                'stok_cabang' => 1,
                'min_cabang' => 1,
            ],
            [
                'kode_bahan' => 'BHN-SKC-DMPUV',
                'nama_bahan' => 'Damper Tinta Eco Solvent / UV Besar',
                'id_kategori' => $katSkc->id,
                'satuan' => 'pcs',
                'spesifikasi' => 'Damper penyaring aliran tinta anti mampet untuk head DX5 / XP600',
                'harga_estimasi' => 45000,
                'stok_pusat' => 40,
                'min_pusat' => 10,
                'stok_cabang' => 0, // Kritis di cabang
                'min_cabang' => 2,
            ],
            [
                'kode_bahan' => 'BHN-SKC-WIPER',
                'nama_bahan' => 'Wiper Blade Karet Pembersih Head',
                'id_kategori' => $katSkc->id,
                'satuan' => 'pcs',
                'spesifikasi' => 'Karet wiper tahan solvent untuk membersihkan nozzle plate',
                'harga_estimasi' => 35000,
                'stok_pusat' => 30,
                'min_pusat' => 8,
                'stok_cabang' => 2,
                'min_cabang' => 2,
            ],
            [
                'kode_bahan' => 'BHN-SKC-BLDROL',
                'nama_bahan' => 'Mata Pisau Mesin Cutting Roland 45 Derajat',
                'id_kategori' => $katSkc->id,
                'satuan' => 'pcs',
                'spesifikasi' => 'Tungsten carbide blade 45° untuk potong stiker vinyl dan polyflex',
                'harga_estimasi' => 65000,
                'stok_pusat' => 50,
                'min_pusat' => 15,
                'stok_cabang' => 4,
                'min_cabang' => 3,
            ],
        ];

        foreach ($daftarBahan as $item) {
            $bahan = Bahan::updateOrCreate(
                ['kode_bahan' => $item['kode_bahan']],
                [
                    'nama_bahan' => $item['nama_bahan'],
                    'id_kategori' => $item['id_kategori'],
                    'satuan' => $item['satuan'],
                    'spesifikasi' => $item['spesifikasi'],
                    'harga_estimasi' => $item['harga_estimasi'],
                    'is_aktif' => true,
                ]
            );

            // Set stok gudang pusat lewat StokService
            $stokService->setStokMinimum($bahan->id, $pusat->id, $item['min_pusat']);
            if ($item['stok_pusat'] > 0) {
                $stokService->tambahStok(
                    $bahan->id,
                    $pusat->id,
                    $item['stok_pusat'],
                    'masuk',
                    'Saldo Awal Gudang Pusat (Inisialisasi Sistem)',
                    $adminPusat?->id,
                    'saldo_awal',
                    $bahan->id
                );
            }

            // Set stok cabang-cabang
            foreach ($cabangs as $index => $cabang) {
                // Beri sedikit variasi stok antar cabang agar tabel dan dashboard terlihat realistis
                $variasi = ($index % 3 == 0) ? -1 : (($index % 2 == 0) ? 1 : 0);
                $stokReal = max(0, $item['stok_cabang'] + $variasi);

                // Khusus damper: bikin cabang 1 punya 0 (Kritis)
                if ($item['kode_bahan'] === 'BHN-SKC-DMPUV' && $index === 0) {
                    $stokReal = 0;
                }

                $stokService->setStokMinimum($bahan->id, $cabang->id, $item['min_cabang']);
                if ($stokReal > 0) {
                    $stokService->tambahStok(
                        $bahan->id,
                        $cabang->id,
                        $stokReal,
                        'terima',
                        'Saldo Awal Cabang ' . $cabang->nama_lokasi,
                        $adminPusat?->id,
                        'saldo_awal',
                        $bahan->id
                    );
                }
            }
        }
    }
}
