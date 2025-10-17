<?php

namespace Database\Seeders;

use App\Models\Kedisiplinan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KedisiplinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis = [
            ['jenis' => 'Terlambat'],
            ['jenis' => 'Tidak Masuk Tanpa Keterangan'],
            ['jenis' => 'Tidak Mengerjakan Tugas'],
            ['jenis' => 'Melanggar Tata Tertib'],
            ['jenis' => 'Berkelahi'],
        ];

        foreach ($jenis as $data) {
            Kedisiplinan::create($data);
        }
    }
}
