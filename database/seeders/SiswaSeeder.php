<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = Kelas::where('tingkat', 10)->first();

        $siswa = [
            [
                'nisn' => '0051234567',
                'nama' => 'Rizki Ramadhan',
                'status' => 'aktif',
                'current_class_id' => $kelas->id,
            ],
            [
                'nisn' => '0051234568',
                'nama' => 'Putri Wulandari',
                'status' => 'aktif',
                'current_class_id' => $kelas->id,
            ],
            [
                'nisn' => '0051234569',
                'nama' => 'Andi Saputra',
                'status' => 'aktif',
                'current_class_id' => $kelas->id,
            ],
            [
                'nisn' => '0051234570',
                'nama' => 'Sari Rahayu',
                'status' => 'aktif',
                'current_class_id' => $kelas->id,
            ],
            [
                'nisn' => '0051234571',
                'nama' => 'Dimas Wijaya',
                'status' => 'aktif',
                'current_class_id' => $kelas->id,
            ],
        ];

        foreach ($siswa as $data) {
            Siswa::create($data);
        }
    }
}
