<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriList = [
            [
                'kode_kategori' => 'KAT-OUT',
                'nama_kategori' => 'Bahan Outdoor',
                'deskripsi' => 'Media cetak tahan cuaca untuk spanduk, banner, baliho luar ruangan',
            ],
            [
                'kode_kategori' => 'KAT-IND',
                'nama_kategori' => 'Bahan Indoor',
                'deskripsi' => 'Media cetak stiker, poster, dan display resolusi tinggi dalam ruangan',
            ],
            [
                'kode_kategori' => 'KAT-KRT',
                'nama_kategori' => 'Kertas & Karton',
                'deskripsi' => 'Kertas art paper, art carton, duplex, ivory untuk offset dan digital',
            ],
            [
                'kode_kategori' => 'KAT-TNT',
                'nama_kategori' => 'Tinta & Cairan Kimia',
                'deskripsi' => 'Tinta eco-solvent, UV, solvent, cleaner, dan pernis laminasi',
            ],
            [
                'kode_kategori' => 'KAT-SKC',
                'nama_kategori' => 'Suku Cadang & Mesin',
                'deskripsi' => 'Komponen pengganti dan suku cadang printer serta mesin finishing',
            ],
        ];

        foreach ($kategoriList as $item) {
            Kategori::updateOrCreate(['kode_kategori' => $item['kode_kategori']], $item);
        }
    }
}
