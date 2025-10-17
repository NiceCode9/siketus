<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapel = [
            ['nama_mapel' => 'Matematika', 'kode_pelajaran' => 'MTK', 'deskripsi' => 'Mata pelajaran Matematika'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_pelajaran' => 'BIND', 'deskripsi' => 'Mata pelajaran Bahasa Indonesia'],
            ['nama_mapel' => 'Bahasa Inggris', 'kode_pelajaran' => 'BING', 'deskripsi' => 'Mata pelajaran Bahasa Inggris'],
            ['nama_mapel' => 'Pemrograman Web', 'kode_pelajaran' => 'PWEB', 'deskripsi' => 'Mata pelajaran Pemrograman Web'],
            ['nama_mapel' => 'Basis Data', 'kode_pelajaran' => 'BD', 'deskripsi' => 'Mata pelajaran Basis Data'],
            ['nama_mapel' => 'Pemrograman Berorientasi Objek', 'kode_pelajaran' => 'PBO', 'deskripsi' => 'Mata pelajaran PBO'],
            ['nama_mapel' => 'Jaringan Komputer', 'kode_pelajaran' => 'JARKOM', 'deskripsi' => 'Mata pelajaran Jaringan Komputer'],
            ['nama_mapel' => 'Sistem Operasi', 'kode_pelajaran' => 'SO', 'deskripsi' => 'Mata pelajaran Sistem Operasi'],
        ];

        foreach ($mapel as $data) {
            Mapel::create($data);
        }
    }
}
