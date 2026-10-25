<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $lokasiList = [
            [
                'kode_lokasi' => 'PST-01',
                'nama_lokasi' => 'Gudang Pusat Surabaya',
                'tipe' => 'pusat',
                'alamat' => 'Jl. Industri Rungkut No. 88, Surabaya',
                'telepon' => '031-8432001',
                'penanggung_jawab' => 'Bambang Sudiro',
                'is_aktif' => true,
            ],
            [
                'kode_lokasi' => 'CB-01',
                'nama_lokasi' => 'Cabang Rungkut',
                'tipe' => 'cabang',
                'alamat' => 'Jl. Rungkut Madya No. 45, Surabaya',
                'telepon' => '031-8711002',
                'penanggung_jawab' => 'Ahmad Fauzi',
                'is_aktif' => true,
            ],
            [
                'kode_lokasi' => 'CB-02',
                'nama_lokasi' => 'Cabang Gubeng',
                'tipe' => 'cabang',
                'alamat' => 'Jl. Sumatera No. 12, Surabaya',
                'telepon' => '031-5032003',
                'penanggung_jawab' => 'Rina Wijaya',
                'is_aktif' => true,
            ],
            [
                'kode_lokasi' => 'CB-03',
                'nama_lokasi' => 'Cabang Darmo',
                'tipe' => 'cabang',
                'alamat' => 'Jl. Raya Darmo No. 110, Surabaya',
                'telepon' => '031-5674004',
                'penanggung_jawab' => 'Hendra Setiawan',
                'is_aktif' => true,
            ],
            [
                'kode_lokasi' => 'CB-04',
                'nama_lokasi' => 'Cabang Sidoarjo',
                'tipe' => 'cabang',
                'alamat' => 'Jl. Pahlawan No. 78, Sidoarjo',
                'telepon' => '031-8945005',
                'penanggung_jawab' => 'Dewi Lestari',
                'is_aktif' => true,
            ],
            [
                'kode_lokasi' => 'CB-05',
                'nama_lokasi' => 'Cabang Gresik',
                'tipe' => 'cabang',
                'alamat' => 'Jl. Veteran No. 29, Gresik',
                'telepon' => '031-3982006',
                'penanggung_jawab' => 'Budi Santoso',
                'is_aktif' => true,
            ],
        ];

        foreach ($lokasiList as $item) {
            Lokasi::updateOrCreate(['kode_lokasi' => $item['kode_lokasi']], $item);
        }
    }
}
