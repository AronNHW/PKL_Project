<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $pusat = Lokasi::where('kode_lokasi', 'PST-01')->first();
        $cb01 = Lokasi::where('kode_lokasi', 'CB-01')->first();
        $cb02 = Lokasi::where('kode_lokasi', 'CB-02')->first();
        $cb03 = Lokasi::where('kode_lokasi', 'CB-03')->first();
        $cb04 = Lokasi::where('kode_lokasi', 'CB-04')->first();
        $cb05 = Lokasi::where('kode_lokasi', 'CB-05')->first();

        $users = [
            [
                'name' => 'Pak Djoko (Direktur / Owner)',
                'email' => 'owner@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'id_lokasi' => null,
                'telepon' => '081234567890',
                'is_aktif' => true,
            ],
            [
                'name' => 'Bambang Sudiro (Admin Pusat)',
                'email' => 'admin.pusat@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'admin_pusat',
                'id_lokasi' => $pusat?->id,
                'telepon' => '081234567891',
                'is_aktif' => true,
            ],
            [
                'name' => 'Ahmad Fauzi (Admin Cabang Rungkut)',
                'email' => 'admin.rungkut@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'admin_cabang',
                'id_lokasi' => $cb01?->id,
                'telepon' => '081234567892',
                'is_aktif' => true,
            ],
            [
                'name' => 'Rina Wijaya (Admin Cabang Gubeng)',
                'email' => 'admin.gubeng@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'admin_cabang',
                'id_lokasi' => $cb02?->id,
                'telepon' => '081234567893',
                'is_aktif' => true,
            ],
            [
                'name' => 'Hendra Setiawan (Admin Cabang Darmo)',
                'email' => 'admin.darmo@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'admin_cabang',
                'id_lokasi' => $cb03?->id,
                'telepon' => '081234567894',
                'is_aktif' => true,
            ],
            [
                'name' => 'Dewi Lestari (Admin Cabang Sidoarjo)',
                'email' => 'admin.sidoarjo@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'admin_cabang',
                'id_lokasi' => $cb04?->id,
                'telepon' => '081234567895',
                'is_aktif' => true,
            ],
            [
                'name' => 'Budi Santoso (Admin Cabang Gresik)',
                'email' => 'admin.gresik@cetakstok.com',
                'password' => Hash::make('password'),
                'role' => 'admin_cabang',
                'id_lokasi' => $cb05?->id,
                'telepon' => '081234567896',
                'is_aktif' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
