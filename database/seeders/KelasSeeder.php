<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusans = Jurusan::all();
        $tingkat = [10, 11, 12];
        $namaKelas = ['A', 'B', 'C'];

        foreach ($jurusans as $jurusan) {
            foreach ($tingkat as $t) {
                foreach ($namaKelas as $nama) {
                    Kelas::create([
                        'jurusan_id' => $jurusan->id,
                        'tingkat' => $t,
                        'nama_kelas' => $nama,
                    ]);
                }
            }
        }
    }
}
