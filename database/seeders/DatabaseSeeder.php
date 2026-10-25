<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LokasiSeeder::class,
            KategoriSeeder::class,
            SupplierSeeder::class,
            UserSeeder::class,
            MesinSeeder::class,
            // BahanSeeder::class, // Dinonaktifkan agar data bahan/stok bersih untuk pengujian mandiri
        ]);
    }
}
