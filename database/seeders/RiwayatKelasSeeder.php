<?php

namespace Database\Seeders;

use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RiwayatKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAktif = TahunAkademik::where('status_aktif', true)->first();
        $siswas = Siswa::all();

        foreach ($siswas as $siswa) {
            RiwayatKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->current_class_id,
                'tahun_akademik_id' => $tahunAktif->id,
                'status' => 'aktif',
                'keterangan' => 'Siswa aktif',
            ]);
        }
    }
}
