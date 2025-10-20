<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            JurusanSeeder::class,
            KelasSeeder::class,
            TahunAkademikSeeder::class,
            MapelSeeder::class,
            GuruSeeder::class,
            SiswaSeeder::class,
            UserSeeder::class,
            GuruMapelSeeder::class,
            GuruKelasSeeder::class,
            RiwayatKelasSeeder::class,
            KedisiplinanSeeder::class,
            KegiatanKeagamaanSeeder::class,
            JadwalPelajaranSeeder::class,
        ]);
    }
}
