<?php

namespace Database\Seeders;

use App\Models\TahunAkademik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAkademik = [
            [
                'nama_tahun_akademik' => '2023/2024',
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2024-06-30',
                'status_aktif' => false,
            ],
            [
                'nama_tahun_akademik' => '2024/2025',
                'tanggal_mulai' => '2025-10-01',
                'tanggal_selesai' => '2025-11-30',
                'status_aktif' => true,
            ],
        ];

        foreach ($tahunAkademik as $data) {
            TahunAkademik::create($data);
        }
    }
}
