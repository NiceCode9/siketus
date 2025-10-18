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
            [
                'nama_kegiatan' => 'Sholat Dzuhur Berjamaah',
                'tahun_akademik_id' => 2,
                'tingkat_kelas' => '10',
                'semester' => 'Ganjil',
            ],
            [
                'nama_kegiatan' => 'Tadarus Al-Quran',
                'tahun_akademik_id' => 2,
                'tingkat_kelas' => '10',
                'semester' => 'Ganjil',
            ],
            [
                'nama_kegiatan' => 'Kajian Keislaman',
                'tahun_akademik_id' => 2,
                'tingkat_kelas' => '10',
                'semester' => 'Ganjil',
            ],
            [
                'nama_kegiatan' => 'Peringatan Hari Besar Islam',
                'tahun_akademik_id' => 2,
                'tingkat_kelas' => '10',
                'semester' => 'Ganjil',
            ],
            [
                'nama_kegiatan' => 'Pesantren Kilat',
                'tahun_akademik_id' => 2,
                'tingkat_kelas' => '10',
                'semester' => 'Ganjil',
            ],
        ];

        foreach ($kegiatan as $data) {
            KegiatanKeagamaan::create($data);
        }
    }
}
