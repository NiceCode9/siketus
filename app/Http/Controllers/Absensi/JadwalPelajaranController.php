<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\GuruKelas;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');

        // Default ke tahun akademik aktif
        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        // Jika request untuk calendar view
        if ($request->has('calendar')) {
            return $this->getCalendarEvents($request);
        }

        // Jika request AJAX (DataTables)
        if ($request->ajax()) {
            return $this->getDataTable($request);
        }

        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        return view('master.jadwal.index', compact('tahunAkademikList', 'tahunAkademikId'));
    }

    private function getDataTable(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $kelasId = $request->get('kelas_id');
        $hari = $request->get('hari');

        $query = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'guruKelas.tahunAkademik'
        ])
            ->whereHas('guruKelas', function ($q) use ($tahunAkademikId) {
                if ($tahunAkademikId) {
                    $q->where('tahun_akademik_id', $tahunAkademikId);
                }
            });

        if ($kelasId) {
            $query->whereHas('guruKelas', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($hari) {
            $query->where('hari', $hari);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('hari', function ($row) {
                return $row->hari;
            })
            ->addColumn('waktu', function ($row) {
                return \Carbon\Carbon::parse($row->jam_mulai)->format('H:i') . ' - ' .
                    \Carbon\Carbon::parse($row->jam_selesai)->format('H:i');
            })
            ->addColumn('kelas', function ($row) {
                return $row->guruKelas->kelas->nama_kelas ?? '-';
            })
            ->addColumn('mapel', function ($row) {
                $mapel = $row->guruKelas->guruMapel->mapel->nama_mapel ?? '-';
                $colors = [
                    'Matematika' => 'primary',
                    'Fisika' => 'success',
                    'Kimia' => 'info',
                    'Biologi' => 'success',
                    'Bahasa Indonesia' => 'warning',
                    'Bahasa Inggris' => 'warning',
                ];
                $color = $colors[$mapel] ?? 'secondary';
                return '<span class="badge badge-' . $color . ' badge-mapel">' . $mapel . '</span>';
            })
            ->addColumn('guru', function ($row) {
                return $row->guruKelas->guruMapel->guru->nama ?? '-';
            })
            ->addColumn('ruangan', function ($row) {
                return $row->ruangan ?? '<span class="text-muted">-</span>';
            })
            ->addColumn('status', function ($row) {
                $isActive = $row->guruKelas->aktif;
                $badge = $isActive ?
                    '<span class="badge badge-success">Aktif</span>' :
                    '<span class="badge badge-secondary">Tidak Aktif</span>';

                // Check conflict
                $hasConflict = $this->checkConflict($row);
                if ($hasConflict) {
                    $badge .= ' <span class="badge badge-danger conflict-badge ml-1">Konflik</span>';
                }

                return $badge;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group btn-group-sm">';
                $btn .= '<button class="btn btn-info view-btn" data-id="' . $row->id . '" title="Detail"><i class="fas fa-eye"></i></button>';
                $btn .= '<button class="btn btn-warning edit-btn" data-id="' . $row->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                $btn .= '<button class="btn btn-danger delete-btn" data-id="' . $row->id . '" data-nama="' . ($row->guruKelas->guruMapel->mapel->nama_mapel ?? '') . ' - ' . ($row->guruKelas->kelas->nama_kelas ?? '') . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['mapel', 'ruangan', 'status', 'action'])
            ->make(true);
    }

    private function checkConflict($jadwal)
    {
        // Check guru conflict (same guru, same time, same day)
        $guruConflict = JadwalPelajaran::where('id', '!=', $jadwal->id)
            ->where('hari', $jadwal->hari)
            ->whereHas('guruKelas.guruMapel', function ($q) use ($jadwal) {
                $q->where('guru_id', $jadwal->guruKelas->guruMapel->guru_id);
            })
            ->where(function ($query) use ($jadwal) {
                $query->whereBetween('jam_mulai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                    ->orWhere(function ($q) use ($jadwal) {
                        $q->where('jam_mulai', '<=', $jadwal->jam_mulai)
                            ->where('jam_selesai', '>=', $jadwal->jam_selesai);
                    });
            })
            ->exists();

        // Check ruangan conflict
        $ruanganConflict = false;
        if ($jadwal->ruangan) {
            $ruanganConflict = JadwalPelajaran::where('id', '!=', $jadwal->id)
                ->where('hari', $jadwal->hari)
                ->where('ruangan', $jadwal->ruangan)
                ->where(function ($query) use ($jadwal) {
                    $query->whereBetween('jam_mulai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                        ->orWhereBetween('jam_selesai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                        ->orWhere(function ($q) use ($jadwal) {
                            $q->where('jam_mulai', '<=', $jadwal->jam_mulai)
                                ->where('jam_selesai', '>=', $jadwal->jam_selesai);
                        });
                })
                ->exists();
        }

        return $guruConflict || $ruanganConflict;
    }

    private function getCalendarEvents(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $kelasId = $request->get('kelas_id');

        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas'
        ])
            ->whereHas('guruKelas', function ($q) use ($tahunAkademikId) {
                if ($tahunAkademikId) {
                    $q->where('tahun_akademik_id', $tahunAkademikId);
                }
            });

        if ($kelasId) {
            $jadwal->whereHas('guruKelas', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $jadwal = $jadwal->get();

        $events = [];

        $hariMapping = [
            'Senin' => [
                'english' => 'Monday',
                'number' => 1
            ],
            'Selasa' => [
                'english' => 'Tuesday',
                'number' => 2
            ],
            'Rabu' => [
                'english' => 'Wednesday',
                'number' => 3
            ],
            'Kamis' => [
                'english' => 'Thursday',
                'number' => 4
            ],
            'Jumat' => [
                'english' => 'Friday',
                'number' => 5
            ],
            'Sabtu' => [
                'english' => 'Saturday',
                'number' => 6
            ],
            'Minggu' => [
                'english' => 'Sunday',
                'number' => 0
            ]
        ];

        $mapelColors = [
            'Matematika' => '#007bff',
            'Fisika' => '#28a745',
            'Kimia' => '#17a2b8',
            'Biologi' => '#20c997',
            'Bahasa Indonesia' => '#fd7e14',
            'Bahasa Inggris' => '#ffc107',
            'Sejarah' => '#6f42c1',
            'Geografi' => '#e83e8c',
        ];

        foreach ($jadwal as $j) {
            $hariConfig = $hariMapping[$j->hari] ?? $hariMapping['Senin'];
            $nextDay = \Carbon\Carbon::now()->next($hariConfig['english']);

            $mapel = $j->guruKelas->guruMapel->mapel->nama_mapel ?? '-';
            $kelas = $j->guruKelas->kelas->nama_kelas ?? '-';
            $guru = $j->guruKelas->guruMapel->guru->nama ?? '-';
            $ruangan = $j->ruangan ?? 'TBA';

            $events[] = [
                'id' => $j->id,
                'title' => $mapel . ' - ' . $kelas,
                'start' => $nextDay->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($j->jam_mulai)->format('H:i:s'),
                'end' => $nextDay->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($j->jam_selesai)->format('H:i:s'),
                'backgroundColor' => $mapelColors[$mapel] ?? '#6c757d',
                'borderColor' => $mapelColors[$mapel] ?? '#6c757d',
                'extendedProps' => [
                    'mapel' => $mapel,
                    'kelas' => $kelas,
                    'guru' => $guru,
                    'ruangan' => $ruangan,
                ],
                'daysOfWeek' => [$hariConfig['number']]
            ];
        }

        return response()->json($events);
    }

    public function getKelas(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');

        $kelas = Kelas::with('jurusan') // <-- Load relasi jurusan
            ->whereHas('guruKelas', function ($q) use ($tahunAkademikId) {
                if ($tahunAkademikId) {
                    $q->where('tahun_akademik_id', $tahunAkademikId);
                }
            })
            ->distinct()
            ->orderBy('nama_kelas')
            ->get();

        $data = $kelas->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_kelas' => $item->nama_lengkap,
            ];
        });

        return response()->json($data);
    }


    public function getGuruKelas()
    {
        $guruKelas = GuruKelas::with([
            'guruMapel.guru',
            'guruMapel.mapel',
            'kelas',
            'tahunAkademik'
        ])
            ->where('aktif', true)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->guruMapel->guru->nama . ' - ' .
                        $item->guruMapel->mapel->nama_mapel . ' - ' .
                        $item->kelas->nama_kelas . ' (' .
                        $item->tahunAkademik->nama_tahun_akademik . ')'
                ];
            });

        return response()->json($guruKelas);
    }

    public function create()
    {
        $guruKelasList = GuruKelas::with(['guruMapel.guru', 'guruMapel.mapel', 'kelas', 'tahunAkademik'])
            ->where('aktif', true)
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('master.jadwal.form', compact('guruKelasList', 'hariList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_kelas_id' => 'required|exists:guru_kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
        ]);

        // Check guru conflict
        $guruKelas = GuruKelas::with('guruMapel')->findOrFail($validated['guru_kelas_id']);
        $cekGuru = JadwalPelajaran::where('hari', $validated['hari'])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guruKelas) {
                $q->where('guru_id', $guruKelas->guruMapel->guru_id);
            })
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($cekGuru) {
            return response()->json([
                'status' => false,
                'errors' => ['guru_kelas_id' => ['Guru sudah memiliki jadwal di waktu yang sama!']]
            ], 422);
        }

        // Check kelas conflict
        $cekKelas = JadwalPelajaran::where('guru_kelas_id', $validated['guru_kelas_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($cekKelas) {
            return response()->json([
                'status' => false,
                'errors' => ['hari' => ['Kelas sudah memiliki jadwal di waktu yang sama!']]
            ], 422);
        }

        // Check ruangan conflict if ruangan is provided
        if (!empty($validated['ruangan'])) {
            $cekRuangan = JadwalPelajaran::where('hari', $validated['hari'])
                ->where('ruangan', $validated['ruangan'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                                ->where('jam_selesai', '>=', $validated['jam_selesai']);
                        });
                })
                ->exists();

            if ($cekRuangan) {
                return response()->json([
                    'status' => false,
                    'errors' => ['ruangan' => ['Ruangan sudah digunakan di waktu yang sama!']]
                ], 422);
            }
        }

        JadwalPelajaran::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Jadwal pelajaran berhasil ditambahkan!'
        ]);
    }

    public function show($id)
    {
        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'guruKelas.tahunAkademik'
        ])->findOrFail($id);

        return response()->json([
            'id' => $jadwal->id,
            'mapel' => $jadwal->guruKelas->guruMapel->mapel->nama_mapel,
            'guru' => $jadwal->guruKelas->guruMapel->guru->nama,
            'kelas' => $jadwal->guruKelas->kelas->nama_kelas,
            'hari' => $jadwal->hari,
            'waktu' => \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') . ' - ' .
                \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            'ruangan' => $jadwal->ruangan ?? '-',
            'tahun_akademik' => $jadwal->guruKelas->tahunAkademik->nama_tahun_akademik
        ]);
    }

    public function edit(JadwalPelajaran $jadwal)
    {
        $jadwal->load(['guruKelas']);

        return response()->json([
            'id' => $jadwal->id,
            'guru_kelas_id' => $jadwal->guru_kelas_id,
            'hari' => $jadwal->hari,
            'jam_mulai' => \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i'),
            'jam_selesai' => \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            'ruangan' => $jadwal->ruangan,
        ]);
    }

    public function update(Request $request, JadwalPelajaran $jadwal)
    {
        $validated = $request->validate([
            'guru_kelas_id' => 'required|exists:guru_kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
        ]);

        // Similar conflict checks as store method but exclude current jadwal
        $guruKelas = GuruKelas::with('guruMapel')->findOrFail($validated['guru_kelas_id']);
        $cekGuru = JadwalPelajaran::where('id', '!=', $jadwal->id)
            ->where('hari', $validated['hari'])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guruKelas) {
                $q->where('guru_id', $guruKelas->guruMapel->guru_id);
            })
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($cekGuru) {
            return response()->json([
                'status' => false,
                'errors' => ['guru_kelas_id' => ['Guru sudah memiliki jadwal di waktu yang sama!']]
            ], 422);
        }

        $jadwal->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Jadwal pelajaran berhasil diupdate!'
        ]);
    }

    public function destroy(JadwalPelajaran $jadwal)
    {
        $jadwal->delete();

        return response()->json([
            'status' => true,
            'message' => 'Jadwal pelajaran berhasil dihapus!'
        ]);
    }

    public function exportPdf(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $kelasId = $request->get('kelas_id');

        $tahunAkademik = TahunAkademik::find($tahunAkademikId);
        $kelas = $kelasId ? Kelas::find($kelasId) : null;

        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas'
        ])
            ->whereHas('guruKelas', function ($q) use ($tahunAkademikId, $kelasId) {
                if ($tahunAkademikId) {
                    $q->where('tahun_akademik_id', $tahunAkademikId);
                }
                if ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                }
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = $jadwal->where('hari', $hari);
        }

        $pdf = Pdf::loadView('master.jadwal.pdf', compact(
            'jadwalPerHari',
            'tahunAkademik',
            'kelas',
            'hariList'
        ))->setPaper('a4', 'landscape');

        $filename = 'jadwal-pelajaran';
        if ($kelas) {
            $filename .= '-' . strtolower($kelas->nama_kelas);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        // Implementation for Excel export using Maatwebsite\Excel
        // You can create an export class for this
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia!');
    }
}
