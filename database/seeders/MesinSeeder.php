<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use App\Models\Mesin;
use Illuminate\Database\Seeder;

class MesinSeeder extends Seeder
{
    public function run(): void
    {
        $lokasiList = Lokasi::all();

        $daftarMesin = [
            ['kode' => 'MSN-OUT-01', 'nama' => 'Flora Polaris 3.2m Outdoor', 'tipe' => 'Outdoor High Speed', 'lokasi_kode' => 'PST-01', 'status' => 'aktif'],
            ['kode' => 'MSN-OUT-02', 'nama' => 'Human Xaar 3.2m Outdoor', 'tipe' => 'Outdoor Standard', 'lokasi_kode' => 'CB-01', 'status' => 'aktif'],
            ['kode' => 'MSN-IND-01', 'nama' => 'Mimaki JV33-160 Eco-Solvent', 'tipe' => 'Indoor High-Res', 'lokasi_kode' => 'CB-01', 'status' => 'rusak'],
            ['kode' => 'MSN-IND-02', 'nama' => 'Roland VersaCAMM VS-640i', 'tipe' => 'Print & Cut Indoor', 'lokasi_kode' => 'CB-02', 'status' => 'aktif'],
            ['kode' => 'MSN-CUT-01', 'nama' => 'Graphtec CE7000-130 Cutting Plotter', 'tipe' => 'Finishing Cutting', 'lokasi_kode' => 'CB-02', 'status' => 'aktif'],
            ['kode' => 'MSN-IND-03', 'nama' => 'Epson SureColor S40670', 'tipe' => 'Indoor Eco-Solvent', 'lokasi_kode' => 'CB-03', 'status' => 'perbaikan'],
            ['kode' => 'MSN-OUT-03', 'nama' => 'Allwin Konica 512i 3.2m', 'tipe' => 'Outdoor High Speed', 'lokasi_kode' => 'CB-04', 'status' => 'aktif'],
            ['kode' => 'MSN-IND-04', 'nama' => 'Xenons Eco Solvent 1.8m', 'tipe' => 'Indoor High-Res', 'lokasi_kode' => 'CB-05', 'status' => 'aktif'],
        ];

        foreach ($daftarMesin as $m) {
            $lokasi = $lokasiList->firstWhere('kode_lokasi', $m['lokasi_kode']);
            if ($lokasi) {
                Mesin::updateOrCreate(
                    ['kode_mesin' => $m['kode']],
                    [
                        'nama_mesin' => $m['nama'],
                        'tipe_mesin' => $m['tipe'],
                        'id_lokasi' => $lokasi->id,
                        'status' => $m['status'],
                        'keterangan' => 'Mesin operasional produksi ' . $lokasi->nama_lokasi,
                    ]
                );
            }
        }
    }
}
