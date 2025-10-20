<?php

namespace Database\Seeders;

use App\Models\GuruKelas;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\TahunAkademik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuruKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAktif = TahunAkademik::where('status_aktif', true)->first();
        $guruMapels = GuruMapel::all();
        $kelas10 = Kelas::where('tingkat', 10)->where('jurusan_id', 1)->take(3)->get();
        $kelas11 = Kelas::where('tingkat', 11)->where('jurusan_id', 1)->take(3)->get();
        $kelas12 = Kelas::where('tingkat', 12)->where('jurusan_id', 1)->take(3)->get();

        foreach ($guruMapels as $guruMapel) {
            foreach ($kelas10 as $k) {
                GuruKelas::create([
                    'guru_mapel_id' => $guruMapel->id,
                    'kelas_id' => $k->id,
                    'tahun_akademik_id' => $tahunAktif->id,
                    'aktif' => true,
                    'keterangan' => 'Mengajar tahun ajaran aktif',
                ]);
            }
        }

        foreach ($guruMapels as $guruMapel) {
            foreach ($kelas11 as $k) {
                GuruKelas::create([
                    'guru_mapel_id' => $guruMapel->id,
                    'kelas_id' => $k->id,
                    'tahun_akademik_id' => $tahunAktif->id,
                    'aktif' => true,
                    'keterangan' => 'Mengajar tahun ajaran aktif',
                ]);
            }
        }

        foreach ($guruMapels as $guruMapel) {
            foreach ($kelas12 as $k) {
                GuruKelas::create([
                    'guru_mapel_id' => $guruMapel->id,
                    'kelas_id' => $k->id,
                    'tahun_akademik_id' => $tahunAktif->id,
                    'aktif' => true,
                    'keterangan' => 'Mengajar tahun ajaran aktif',
                ]);
            }
        }
    }
}
