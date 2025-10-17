<?php

namespace Database\Seeders;

use App\Models\KegiatanKeagamaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KegiatanKeagamaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kegiatan = [
            ['nama_kegiatan' => 'Sholat Dzuhur Berjamaah'],
            ['nama_kegiatan' => 'Tadarus Al-Quran'],
            ['nama_kegiatan' => 'Kajian Keislaman'],
            ['nama_kegiatan' => 'Peringatan Hari Besar Islam'],
            ['nama_kegiatan' => 'Pesantren Kilat'],
        ];

        foreach ($kegiatan as $data) {
            KegiatanKeagamaan::create($data);
        }
    }
}
