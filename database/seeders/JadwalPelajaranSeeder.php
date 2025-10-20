<?php

namespace Database\Seeders;

use App\Models\GuruKelas;
use App\Models\JadwalPelajaran;
use App\Models\TahunAkademik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil tahun akademik aktif
        $tahunAktif = TahunAkademik::where('status_aktif', true)->first();

        if (!$tahunAktif) {
            $this->command->error('Tidak ada tahun akademik aktif!');
            return;
        }

        // Ambil semua guru_kelas untuk tahun akademik aktif
        $guruKelasList = GuruKelas::where('tahun_akademik_id', $tahunAktif->id)
            ->where('aktif', true)
            ->get();

        if ($guruKelasList->isEmpty()) {
            $this->command->error('Tidak ada data GuruKelas untuk tahun akademik aktif!');
            return;
        }

        // Data hari yang tersedia
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Data jam pelajaran (format: jam_mulai, jam_selesai)
        $jamPelajaran = [
            ['07:00', '07:45'],
            ['07:45', '08:30'],
            ['08:30', '09:15'],
            ['09:15', '10:00'],
            ['10:15', '11:00'],
            ['11:00', '11:45'],
            ['12:30', '13:15'],
            ['13:15', '14:00'],
            ['14:00', '14:45'],
            ['14:45', '15:30'],
        ];

        // Data ruangan
        $ruanganList = ['R001', 'R002', 'R003', 'R004', 'R005', 'Lab Komputer 1', 'Lab Komputer 2', 'Lab Bahasa'];

        $jadwalCreated = 0;

        foreach ($guruKelasList as $guruKelas) {
            // Tentukan jumlah jam per minggu berdasarkan mata pelajaran
            $mapelKode = $guruKelas->guruMapel->mapel->kode_pelajaran ?? '';
            $jumlahJam = $this->getJumlahJamByMapel($mapelKode);

            // Generate jadwal untuk jumlah jam yang ditentukan
            for ($i = 0; $i < $jumlahJam; $i++) {
                // Pilih hari secara acak, hindari hari yang sama untuk guru yang sama
                $hari = $hariList[array_rand($hariList)];

                // Pilih jam pelajaran secara acak
                $jam = $jamPelajaran[array_rand($jamPelajaran)];

                // Pilih ruangan secara acak
                $ruangan = $ruanganList[array_rand($ruanganList)];

                // Cek apakah sudah ada jadwal di waktu yang sama untuk kelas yang sama
                $existingJadwal = JadwalPelajaran::where('hari', $hari)
                    ->where('jam_mulai', $jam[0])
                    ->whereHas('guruKelas', function ($query) use ($guruKelas) {
                        $query->where('kelas_id', $guruKelas->kelas_id);
                    })
                    ->exists();

                // Jika tidak ada konflik, buat jadwal
                if (!$existingJadwal) {
                    JadwalPelajaran::create([
                        'guru_kelas_id' => $guruKelas->id,
                        'hari' => $hari,
                        'jam_mulai' => $jam[0],
                        'jam_selesai' => $jam[1],
                        'ruangan' => $ruangan,
                    ]);

                    $jadwalCreated++;
                }
            }
        }

        $this->command->info("Berhasil membuat {$jadwalCreated} jadwal pelajaran untuk tahun akademik {$tahunAktif->tahun_akademik}");
    }

    /**
     * Mendapatkan jumlah jam per minggu berdasarkan kode mata pelajaran
     */
    private function getJumlahJamByMapel(string $kodeMapel): int
    {
        $jamMapel = [
            'MTK' => 4,    // Matematika: 4 jam/minggu
            'BIND' => 4,   // Bahasa Indonesia: 4 jam/minggu
            'BING' => 4,   // Bahasa Inggris: 4 jam/minggu
            'PWEB' => 6,   // Pemrograman Web: 6 jam/minggu
            'BD' => 4,     // Basis Data: 4 jam/minggu
            'PBO' => 5,    // Pemrograman Berorientasi Objek: 5 jam/minggu
            'JARKOM' => 4, // Jaringan Komputer: 4 jam/minggu
            'SO' => 3,     // Sistem Operasi: 3 jam/minggu
        ];

        return $jamMapel[$kodeMapel] ?? 3; // Default 3 jam jika tidak ditemukan
    }
}
