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
                'nama_tahun_akademik' => '2024/2025',
                'tanggal_mulai_ganjil' => '2025-10-01',
                'tanggal_selesai_ganjil' => '2026-06-30',
                'tanggal_mulai_genap' => '2026-07-01',
                'tanggal_selesai_genap' => '2027-06-30',
                'status_aktif' => true,
            ],
        ];

        foreach ($tahunAkademik as $data) {
            TahunAkademik::create($data);
        }
    }
}
