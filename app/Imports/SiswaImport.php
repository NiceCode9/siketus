<?php

namespace App\Imports;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{

    private $errors = [];
    private $successCount = 0;
    private $skipCount = 0;

    public function collection(Collection $rows)
    {
        $tahunAkademikAktid = TahunAkademik::where('status_aktif', true)->first();

        if (!$tahunAkademikAktid) {
            throw new \Exception('Tidak ada tahun ajaran aktif yang ditemukan');
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena index dimulai dari 0 dan ada header

            try {
                // Validasi NISN unik
                if (Siswa::where('nisn', $row['nisn'])->exists()) {
                    $this->errors[] = "Baris {$rowNumber}: NISN {$row['nisn']} sudah terdaftar";
                    $this->skipCount++;
                    continue;
                }

                $jurusan = Jurusan::where('kode_jurusan', $row['kode_jurusan'])->first();
                // dd($jurusan);
                if (!$jurusan) {
                    $this->errors[] = "Baris {$rowNumber}: Jurusan dengan kode {$row['kode_jurusan']} tidak ditemukan";
                    $this->skipCount++;
                    continue;
                }

                // Cari kelas berdasarkan tingkat dan nama
                $kelas = Kelas::where('tingkat', $row['tingkat'])
                    ->where('jurusan_id', $jurusan->id)
                    ->where('nama_kelas', $row['nama_kelas'])
                    ->first();

                if (!$kelas) {
                    $this->errors[] = "Baris {$rowNumber}: Kelas {$row['tingkat_kelas']}-{$row['nama_kelas']} tidak ditemukan";
                    $this->skipCount++;
                    DB::rollBack();
                    continue;
                }

                $siswaData = [
                    'nisn' => $row['nisn'],
                    'nama' => $row['nama'],
                    'status' => $row['status'],
                    'current_class_id' => $kelas->id,
                ];

                $siswa = Siswa::create($siswaData);

                $siswa->riwayatKelas()->create([
                    'kelas_id' => $kelas->id,
                    'tahun_akademik_id' => $tahunAkademikAktid->id,
                    'status' => $row['status'],
                    'ketrangan' => 'Import dari file Excel',
                ]);

                $siswa->akun()->create([
                    'name' => $row['nama'],
                    'username' => $row['nisn'],
                    'email' => $row['nisn'] . '@sekolah.com',
                    'password' => $row['nisn'],
                ]);

                $this->successCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->skipCount++;
            }
        }
    }

    public function rules(): array
    {
        return [
            'nisn' => 'required|unique:siswa,nisn',
            'tingkat' => 'required',
            'nama_kelas' => 'required',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nisn.unique' => 'NISN :input sudah terdaftar di sistem.',
            'tingkat.required' => 'Tingkat kelas wajib diisi.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getSkipCount(): int
    {
        return $this->skipCount;
    }
}
