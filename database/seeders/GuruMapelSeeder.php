<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\GuruMapel;
use App\Models\Mapel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuruMapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = [
            ['guru_nip' => '198501012010011001', 'mapel_kode' => 'MTK'],
            ['guru_nip' => '198702152011012002', 'mapel_kode' => 'BIND'],
            ['guru_nip' => '199001202012011003', 'mapel_kode' => 'PWEB'],
            ['guru_nip' => '199001202012011003', 'mapel_kode' => 'BD'],
            ['guru_nip' => '199001202012011003', 'mapel_kode' => 'PBO'],
            ['guru_nip' => '198905102013012004', 'mapel_kode' => 'BING'],
            ['guru_nip' => '199203152014011005', 'mapel_kode' => 'JARKOM'],
            ['guru_nip' => '199203152014011005', 'mapel_kode' => 'SO'],
        ];

        foreach ($assignments as $assignment) {
            $guru = Guru::where('nip', $assignment['guru_nip'])->first();
            $mapel = Mapel::where('kode_pelajaran', $assignment['mapel_kode'])->first();

            if ($guru && $mapel) {
                GuruMapel::create([
                    'guru_id' => $guru->id,
                    'mapel_id' => $mapel->id,
                    'keterangan' => 'Pengampu aktif',
                ]);
            }
        }
    }
}
