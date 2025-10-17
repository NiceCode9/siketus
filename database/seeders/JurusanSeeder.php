<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusan = [
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak', 'kode_jurusan' => 'RPL'],
            ['nama_jurusan' => 'Teknik Komputer dan Jaringan', 'kode_jurusan' => 'TKJ'],
            ['nama_jurusan' => 'Multimedia', 'kode_jurusan' => 'MM'],
            ['nama_jurusan' => 'Akuntansi', 'kode_jurusan' => 'AKL'],
            ['nama_jurusan' => 'Otomatisasi dan Tata Kelola Perkantoran', 'kode_jurusan' => 'OTKP'],
        ];

        foreach ($jurusan as $data) {
            Jurusan::create($data);
        }
    }
}
