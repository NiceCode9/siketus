<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisUjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAkademik = \App\Models\TahunAkademik::aktif()->first();

        $jenisUjian = [
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 1',
                'deskripsi' => 'Ulangan Harian ke 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 2',
                'deskripsi' => 'Ulangan Harian ke 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UTS',
                'deskripsi' => 'Ulangan Tengah Semester',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 3',
                'deskripsi' => 'Ulangan Harian ke 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 4',
                'deskripsi' => 'Ulangan Harian ke 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UAS',
                'deskripsi' => 'Ulangan Akhir Semester',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \App\Models\JenisUjian::insert($jenisUjian);
    }
}
