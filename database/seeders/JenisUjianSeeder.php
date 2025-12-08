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
                'is_syarat_ujian' => true,
                'semester' => 'ganjil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 2',
                'deskripsi' => 'Ulangan Harian ke 2',
                'is_syarat_ujian' => true,
                'semester' => 'ganjil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UTS',
                'deskripsi' => 'Ulangan Tengah Semester',
                'is_syarat_ujian' => false,
                'semester' => 'ganjil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 3',
                'deskripsi' => 'Ulangan Harian ke 3',
                'is_syarat_ujian' => true,
                'semester' => 'genap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UH 4',
                'deskripsi' => 'Ulangan Harian ke 4',
                'is_syarat_ujian' => true,
                'semester' => 'genap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_jenis_ujian' => 'UAS',
                'deskripsi' => 'Ulangan Akhir Semester',
                'is_syarat_ujian' => false,
                'semester' => 'genap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \App\Models\JenisUjian::insert($jenisUjian);
    }
}
