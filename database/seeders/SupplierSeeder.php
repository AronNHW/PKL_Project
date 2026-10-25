<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $supplierList = [
            [
                'kode_supplier' => 'SUP-001',
                'nama_supplier' => 'PT Surya Media Grafika',
                'kontak_person' => 'Iwan Susanto',
                'telepon' => '021-5561001',
                'email' => 'sales@suryamedia.co.id',
                'alamat' => 'Kawasan Industri Pulogadung Blok B No. 4, Jakarta Timur',
            ],
            [
                'kode_supplier' => 'SUP-002',
                'nama_supplier' => 'CV Indo Flexi Jaya',
                'kontak_person' => 'Hartono Tan',
                'telepon' => '031-7489002',
                'email' => 'order@indoflexi.com',
                'alamat' => 'Margomulyo Permai Blok C-18, Surabaya',
            ],
            [
                'kode_supplier' => 'SUP-003',
                'nama_supplier' => 'PT Kertas Nusantara Abadi',
                'kontak_person' => 'Lestari Handayani',
                'telepon' => '031-8663003',
                'email' => 'marketing@kertasnusantara.co.id',
                'alamat' => 'Jl. Berbek Industri III No. 12, Waru, Sidoarjo',
            ],
            [
                'kode_supplier' => 'SUP-004',
                'nama_supplier' => 'PT PrintTech Global Solution',
                'kontak_person' => 'Kevin Pratama',
                'telepon' => '021-6548004',
                'email' => 'parts@printtech.co.id',
                'alamat' => 'Mangga Dua Square Blok F-22, Jakarta Utara',
            ],
        ];

        foreach ($supplierList as $item) {
            Supplier::updateOrCreate(['kode_supplier' => $item['kode_supplier']], $item);
        }
    }
}
