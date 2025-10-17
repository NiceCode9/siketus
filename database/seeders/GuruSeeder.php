<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guru = [
            [
                'nip' => '198501012010011001',
                'nama' => 'Ahmad Fauzi, S.Pd',
                'biografi' => 'Guru Matematika dengan pengalaman 15 tahun',
                'bidang_keahlian' => 'Matematika',
            ],
            [
                'nip' => '198702152011012002',
                'nama' => 'Siti Nurhaliza, S.Pd',
                'biografi' => 'Guru Bahasa Indonesia berpengalaman',
                'bidang_keahlian' => 'Bahasa Indonesia',
            ],
            [
                'nip' => '199001202012011003',
                'nama' => 'Budi Santoso, S.Kom',
                'biografi' => 'Ahli di bidang Pemrograman Web dan Database',
                'bidang_keahlian' => 'Pemrograman',
            ],
            [
                'nip' => '198905102013012004',
                'nama' => 'Dewi Lestari, S.Pd',
                'biografi' => 'Guru Bahasa Inggris dengan sertifikasi internasional',
                'bidang_keahlian' => 'Bahasa Inggris',
            ],
            [
                'nip' => '199203152014011005',
                'nama' => 'Eko Prasetyo, S.Kom',
                'biografi' => 'Spesialis Jaringan Komputer dan Sistem Operasi',
                'bidang_keahlian' => 'Jaringan Komputer',
            ],
        ];

        foreach ($guru as $data) {
            Guru::create($data);
        }
    }
}
