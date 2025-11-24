<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Pertemuan;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AbsensiService
{
    /**
     * Get pertemuan list dengan filter
     */
    public function getPertemuanList(array $filters = []): Collection
    {
        $tanggal = $filters['tanggal'] ?? now()->format('Y-m-d');
        $guruId = $filters['guru_id'] ?? null;
        $kelasId = $filters['kelas_id'] ?? null;
        $tahunAkademikId = $filters['tahun_akademik_id'] ?? null;

        $query = Pertemuan::with([
            'jadwalPelajaran.guruKelas.guruMapel.guru',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'jadwalPelajaran.guruKelas.kelas'
        ]);

        if ($guruId) {
            $query->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            });
        }

        if ($kelasId) {
            $query->whereHas('jadwalPelajaran.guruKelas', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($tahunAkademikId) {
            $query->whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            });
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        return $query->orderBy('jam_mulai_aktual')->get();
    }

    /**
     * Get data untuk form absensi
     */
    public function getAbsensiFormData(Pertemuan $pertemuan): array
    {
        $pertemuan->load([
            'jadwalPelajaran.guruKelas.kelas',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'absensi.siswa'
        ]);

        $kelasId = $pertemuan->jadwalPelajaran->guruKelas->kelas_id;

        // FIX: Gunakan 'current_class_id' bukan 'kelas_id'
        $siswaList = Siswa::where('current_class_id', $kelasId)
            ->orderBy('nama')
            ->get();

        $absensiData = $pertemuan->absensi->keyBy('siswa_id');

        return [
            'pertemuan' => $pertemuan,
            'siswaList' => $siswaList,
            'absensiData' => $absensiData
        ];
    }

    /**
     * Simpan atau update absensi
     */
    public function saveAbsensi(Pertemuan $pertemuan, array $data): bool
    {
        DB::beginTransaction();
        try {
            // Update pertemuan
            $pertemuan->update([
                'materi' => $data['materi'] ?? null,
                'jam_mulai_aktual' => $data['jam_mulai_aktual'] ?? now(),
                'jam_selesai_aktual' => $data['jam_selesai_aktual'] ?? null,
                'status' => 'completed',
            ]);

            // Simpan absensi
            foreach ($data['absensi'] as $absensiData) {
                Absensi::updateOrCreate(
                    [
                        'pertemuan_id' => $pertemuan->id,
                        'siswa_id' => $absensiData['siswa_id'],
                    ],
                    [
                        'status_kehadiran' => $absensiData['status_kehadiran'],
                        'keterangan' => $absensiData['keterangan'] ?? null,
                        'waktu_absen' => now(),
                    ]
                );
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get rekap absensi per kelas
     * FIXED: Menambahkan support untuk semester
     */
    public function getRekapPerKelas(array $filters = []): Collection
    {
        $kelasId = $filters['kelas_id'];
        $tahunAkademikId = $filters['tahun_akademik_id'] ?? $this->getActiveTahunAkademikId();
        $mapelId = $filters['mapel_id'] ?? null;
        $semester = $filters['semester'] ?? null; // NEW: Filter semester

        $tahunAkademik = TahunAkademik::find($tahunAkademikId);

        // FIXED: Tentukan rentang tanggal berdasarkan semester
        if ($semester) {
            if ($semester === 'ganjil') {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_ganjil;
            } else {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_genap;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
            }
        } else {
            // Default: semua semester
            $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
            $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
        }

        // Validasi tanggal
        if (!$tanggalMulai || !$tanggalSelesai) {
            throw new \Exception('Rentang tanggal semester belum diatur untuk tahun akademik ini.');
        }

        $query = Absensi::select(
            'siswa.id as siswa_id',
            'siswa.nama as nama_siswa',
            'siswa.nisn',
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "hadir" THEN 1 END) as hadir'),
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "izin" THEN 1 END) as izin'),
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "sakit" THEN 1 END) as sakit'),
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "alpha" THEN 1 END) as alpha'),
            DB::raw('COUNT(*) as total_pertemuan')
        )
            ->join('siswa', 'absensi.siswa_id', '=', 'siswa.id')
            ->join('riwayat_kelas', function ($join) use ($kelasId, $tahunAkademikId) {
                $join->on('siswa.id', '=', 'riwayat_kelas.siswa_id')
                    ->where('riwayat_kelas.kelas_id', $kelasId)
                    ->where('riwayat_kelas.tahun_akademik_id', $tahunAkademikId);
            })
            ->join('pertemuan', 'absensi.pertemuan_id', '=', 'pertemuan.id')
            ->join('jadwal_pelajaran', 'pertemuan.jadwal_pelajaran_id', '=', 'jadwal_pelajaran.id')
            ->join('guru_kelas', 'jadwal_pelajaran.guru_kelas_id', '=', 'guru_kelas.id')
            ->where('guru_kelas.tahun_akademik_id', $tahunAkademikId)
            ->whereBetween('pertemuan.tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($mapelId) {
            $query->join('guru_mapel', 'guru_kelas.guru_mapel_id', '=', 'guru_mapel.id')
                ->where('guru_mapel.mapel_id', $mapelId);
        }

        return $query->groupBy('siswa.id', 'siswa.nama', 'siswa.nisn')
            ->orderBy('siswa.nama')
            ->get()
            ->map(function ($item) {
                $item->persentase_hadir = $item->total_pertemuan > 0
                    ? round(($item->hadir / $item->total_pertemuan) * 100, 2)
                    : 0;
                return $item;
            });
    }

    /**
     * Get rekap absensi per siswa
     * FIXED: Menambahkan support untuk semester
     */
    public function getRekapPerSiswa(int $siswaId, array $filters = []): array
    {
        $tahunAkademikId = $filters['tahun_akademik_id'] ?? $this->getActiveTahunAkademikId();
        $mapelId = $filters['mapel_id'] ?? null;
        $semester = $filters['semester'] ?? null; // NEW: Filter semester
        $perPage = $filters['per_page'] ?? 20;

        $tahunAkademik = TahunAkademik::find($tahunAkademikId);

        // FIXED: Tentukan rentang tanggal berdasarkan semester
        if ($semester) {
            if ($semester === 'ganjil') {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_ganjil;
            } else {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_genap;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
            }
        } else {
            // Default: semua semester
            $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
            $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
        }

        $query = Absensi::with([
            'pertemuan.jadwalPelajaran.guruKelas.guruMapel.mapel',
            'pertemuan.jadwalPelajaran.guruKelas.guruMapel.guru',
            'pertemuan.jadwalPelajaran.guruKelas.kelas'
        ])
            ->where('siswa_id', $siswaId)
            ->whereHas('pertemuan.jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->whereHas('pertemuan', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            });

        if ($mapelId) {
            $query->whereHas('pertemuan.jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }

        $absensiList = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $ringkasan = $this->getRingkasanAbsensi($siswaId, $tahunAkademikId, $mapelId, $semester);

        return [
            'absensiList' => $absensiList,
            'ringkasan' => $ringkasan
        ];
    }

    /**
     * Get ringkasan absensi siswa
     * FIXED: Menambahkan support untuk semester
     */
    public function getRingkasanAbsensi(
        int $siswaId,
        ?int $tahunAkademikId = null,
        ?int $mapelId = null,
        ?string $semester = null // NEW: Parameter semester
    ): object {
        $tahunAkademikId = $tahunAkademikId ?? $this->getActiveTahunAkademikId();
        $tahunAkademik = TahunAkademik::find($tahunAkademikId);

        // FIXED: Tentukan rentang tanggal berdasarkan semester
        if ($semester) {
            if ($semester === 'ganjil') {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_ganjil;
            } else {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_genap;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
            }
        } else {
            // Default: semua semester
            $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
            $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
        }

        $query = Absensi::select(
            DB::raw('COUNT(CASE WHEN status_kehadiran = "hadir" THEN 1 END) as hadir'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "izin" THEN 1 END) as izin'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "sakit" THEN 1 END) as sakit'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "alpha" THEN 1 END) as alpha'),
            DB::raw('COUNT(*) as total')
        )
            ->where('siswa_id', $siswaId)
            ->whereHas('pertemuan.jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->whereHas('pertemuan', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            });

        if ($mapelId) {
            $query->whereHas('pertemuan.jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }

        $ringkasan = $query->first();

        $ringkasan->persentase_hadir = $ringkasan->total > 0
            ? round(($ringkasan->hadir / $ringkasan->total) * 100, 2)
            : 0;

        return $ringkasan;
    }

    /**
     * Get rekap absensi per guru (untuk melihat pertemuan yang sudah/belum diabsen)
     */
    public function getRekapPerGuru(int $guruId, array $filters = []): Collection
    {
        $tahunAkademikId = $filters['tahun_akademik_id'] ?? $this->getActiveTahunAkademikId();
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $status = $filters['status'] ?? null; // completed, pending, cancelled
        $semester = $filters['semester'] ?? null; // NEW: Filter semester

        $query = Pertemuan::with([
            'jadwalPelajaran.guruKelas.kelas',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'absensi'
        ])
            ->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            });

        // FIXED: Gunakan rentang tanggal yang benar
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($semester) {
            $tahunAkademik = TahunAkademik::find($tahunAkademikId);
            if ($semester === 'ganjil') {
                $query->whereBetween('tanggal', [
                    $tahunAkademik->tanggal_mulai_ganjil,
                    $tahunAkademik->tanggal_selesai_ganjil
                ]);
            } else {
                $query->whereBetween('tanggal', [
                    $tahunAkademik->tanggal_mulai_genap,
                    $tahunAkademik->tanggal_selesai_genap
                ]);
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai_aktual')
            ->get()
            ->map(function ($pertemuan) {
                $pertemuan->total_siswa = $pertemuan->jadwalPelajaran->guruKelas->kelas->siswa()->count();
                $pertemuan->total_absensi = $pertemuan->absensi->count();
                $pertemuan->is_complete = $pertemuan->total_siswa === $pertemuan->total_absensi;
                return $pertemuan;
            });
    }

    /**
     * Get statistik absensi untuk dashboard
     * FIXED: Menambahkan support untuk semester
     */
    public function getStatistikAbsensi(array $filters = []): array
    {
        $tahunAkademikId = $filters['tahun_akademik_id'] ?? $this->getActiveTahunAkademikId();
        $kelasId = $filters['kelas_id'] ?? null;
        $guruId = $filters['guru_id'] ?? null;
        $siswaId = $filters['siswa_id'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $semester = $filters['semester'] ?? null; // NEW: Filter semester

        $query = Absensi::query()
            ->join('pertemuan', 'absensi.pertemuan_id', '=', 'pertemuan.id')
            ->join('jadwal_pelajaran', 'pertemuan.jadwal_pelajaran_id', '=', 'jadwal_pelajaran.id')
            ->join('guru_kelas', 'jadwal_pelajaran.guru_kelas_id', '=', 'guru_kelas.id')
            ->where('guru_kelas.tahun_akademik_id', $tahunAkademikId);

        if ($kelasId) {
            $query->where('guru_kelas.kelas_id', $kelasId);
        }

        if ($guruId) {
            $query->join('guru_mapel', 'guru_kelas.guru_mapel_id', '=', 'guru_mapel.id')
                ->where('guru_mapel.guru_id', $guruId);
        }

        if ($siswaId) {
            $query->where('absensi.siswa_id', $siswaId);
        }

        // FIXED: Support untuk filter semester
        if ($startDate && $endDate) {
            $query->whereBetween('pertemuan.tanggal', [$startDate, $endDate]);
        } elseif ($semester) {
            $tahunAkademik = TahunAkademik::find($tahunAkademikId);
            if ($semester === 'ganjil') {
                $query->whereBetween('pertemuan.tanggal', [
                    $tahunAkademik->tanggal_mulai_ganjil,
                    $tahunAkademik->tanggal_selesai_ganjil
                ]);
            } else {
                $query->whereBetween('pertemuan.tanggal', [
                    $tahunAkademik->tanggal_mulai_genap,
                    $tahunAkademik->tanggal_selesai_genap
                ]);
            }
        }

        $stats = $query->select(
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "hadir" THEN 1 END) as total_hadir'),
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "izin" THEN 1 END) as total_izin'),
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "sakit" THEN 1 END) as total_sakit'),
            DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "alpha" THEN 1 END) as total_alpha'),
            DB::raw('COUNT(*) as total_keseluruhan')
        )->first();

        $stats->persentase_hadir = $stats->total_keseluruhan > 0
            ? round(($stats->total_hadir / $stats->total_keseluruhan) * 100, 2)
            : 0;

        return [
            'total_hadir' => $stats->total_hadir,
            'total_izin' => $stats->total_izin,
            'total_sakit' => $stats->total_sakit,
            'total_alpha' => $stats->total_alpha,
            'total_keseluruhan' => $stats->total_keseluruhan,
            'persentase_hadir' => $stats->persentase_hadir
        ];
    }

    /**
     * Export rekap absensi ke array (bisa digunakan untuk Excel/PDF)
     */
    public function exportRekapAbsensi(array $filters = []): array
    {
        $kelasId = $filters['kelas_id'] ?? null;
        $siswaId = $filters['siswa_id'] ?? null;

        if ($siswaId) {
            $data = $this->getRekapPerSiswa($siswaId, $filters);
            return $data['absensiList']->toArray();
        } elseif ($kelasId) {
            return $this->getRekapPerKelas($filters)->toArray();
        }

        return [];
    }

    /**
     * Get active tahun akademik ID
     */
    private function getActiveTahunAkademikId(): ?int
    {
        $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
        return $tahunAkademik?->id;
    }
}
