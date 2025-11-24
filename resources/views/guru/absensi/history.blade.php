@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Riwayat Absensi</h4>
                    </div>
                    <div class="card-body">
                        {{-- Info Jadwal --}}
                        <div class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mata Pelajaran:</strong>
                                    {{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel }}<br>
                                    <strong>Kelas:</strong> {{ $jadwal->guruKelas->kelas->nama_lengkap }}<br>
                                    <strong>Guru:</strong> {{ $jadwal->guruKelas->guruMapel->guru->nama }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Hari:</strong> {{ ucfirst($jadwal->hari) }}<br>
                                    <strong>Jam:</strong> {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}<br>
                                    @if (isset($tanggalMulai) && isset($tanggalSelesai))
                                        <strong>Periode:</strong> {{ $tanggalMulai->format('d/m/Y') }} -
                                        {{ $tanggalSelesai->format('d/m/Y') }}
                                        <span class="badge badge-primary ml-2">Semester {{ ucfirst($semester) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Filter --}}
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Tahun Akademik</label>
                                    <select name="tahun_akademik_id" class="form-control" onchange="this.form.submit()">
                                        @foreach ($tahunAkademikList as $ta)
                                            <option value="{{ $ta->id }}"
                                                {{ $tahunAkademikId == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun_akademik }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Semester</label>
                                    <select name="semester" class="form-control" onchange="this.form.submit()">
                                        <option value="">Pilih Semester</option>
                                        <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>
                                            Ganjil
                                        </option>
                                        <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>
                                            Genap
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        {{-- Statistik Ringkas --}}
                        @if ($statistik)
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $statistik->hadir }}</h3>
                                            <small>Total Hadir</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $statistik->izin }}</h3>
                                            <small>Total Izin</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $statistik->sakit }}</h3>
                                            <small>Total Sakit</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0">{{ $statistik->alpha }}</h3>
                                            <small>Total Alpha</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Tabel Riwayat Pertemuan --}}
                        <h5 class="mb-3">Daftar Pertemuan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Tanggal</th>
                                        <th width="10%">Pertemuan</th>
                                        <th>Materi</th>
                                        <th width="10%">Jam</th>
                                        <th width="10%" class="text-center">Status</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pertemuanList as $key => $pertemuan)
                                        @php
                                            $totalSiswa = $pertemuan->jadwalPelajaran->guruKelas->kelas
                                                ->siswa()
                                                ->count();
                                            $totalAbsensi = $pertemuan->absensi->count();
                                            $statusComplete = $totalSiswa === $totalAbsensi;
                                        @endphp
                                        <tr>
                                            <td>{{ $pertemuanList->firstItem() + $key }}</td>
                                            <td>{{ $pertemuan->tanggal->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-secondary">Pertemuan
                                                    {{ $pertemuan->pertemuan_ke }}</span>
                                            </td>
                                            <td>
                                                @if ($pertemuan->materi)
                                                    {{ Str::limit($pertemuan->materi, 50) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pertemuan->jam_mulai_aktual)
                                                    {{ $pertemuan->jam_mulai_aktual->format('H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($pertemuan->status == 'completed')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Selesai
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">{{ $totalAbsensi }}/{{ $totalSiswa }}
                                                        siswa</small>
                                                @elseif($pertemuan->status == 'cancelled')
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times"></i> Dibatalkan
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> Belum
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pertemuan->status == 'completed')
                                                    <a href="{{ route('guru.absensi.edit', $pertemuan->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit Absensi">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        onclick="showDetail({{ $pertemuan->id }})" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                @else
                                                    <a href="{{ route('guru.absensi.create', $pertemuan->id) }}"
                                                        class="btn btn-sm btn-primary" title="Input Absensi">
                                                        <i class="fas fa-plus"></i> Absen
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data pertemuan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Menampilkan {{ $pertemuanList->firstItem() ?? 0 }} - {{ $pertemuanList->lastItem() ?? 0 }}
                                dari {{ $pertemuanList->total() }} pertemuan
                            </div>
                            <div>
                                {{ $pertemuanList->appends(request()->query())->links() }}
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-4">
                            <a href="{{ route('guru.jadwal-guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Absensi --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Absensi Pertemuan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalDetailContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(pertemuanId) {
            $('#modalDetail').modal('show');
            $('#modalDetailContent').html(`
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);

            // Ajax request untuk get detail
            $.ajax({
                url: `/guru/absensi/detail/${pertemuanId}`,
                method: 'GET',
                success: function(response) {
                    let html = `
                        <div class="mb-3">
                            <h6><strong>Tanggal:</strong> ${response.pertemuan.tanggal}</h6>
                            <h6><strong>Pertemuan Ke:</strong> ${response.pertemuan.pertemuan_ke}</h6>
                            <h6><strong>Materi:</strong> ${response.pertemuan.materi || '-'}</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">NIS</th>
                                        <th>Nama Siswa</th>
                                        <th width="15%">Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.absensi.forEach((item, index) => {
                        let badgeColor = 'secondary';
                        if (item.status_kehadiran === 'hadir') badgeColor = 'success';
                        else if (item.status_kehadiran === 'izin') badgeColor = 'warning';
                        else if (item.status_kehadiran === 'sakit') badgeColor = 'info';
                        else if (item.status_kehadiran === 'alpha') badgeColor = 'danger';

                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.siswa.nisn}</td>
                                <td>${item.siswa.nama}</td>
                                <td>
                                    <span class="badge badge-${badgeColor}">
                                        ${item.status_kehadiran.toUpperCase()}
                                    </span>
                                </td>
                                <td>${item.keterangan || '-'}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Ringkasan:</strong><br>
                            Hadir: <span class="badge badge-success">${response.summary.hadir}</span> |
                            Izin: <span class="badge badge-warning">${response.summary.izin}</span> |
                            Sakit: <span class="badge badge-info">${response.summary.sakit}</span> |
                            Alpha: <span class="badge badge-danger">${response.summary.alpha}</span>
                        </div>
                    `;

                    $('#modalDetailContent').html(html);
                },
                error: function() {
                    $('#modalDetailContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Gagal memuat data. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        }
    </script>

    <style>
        @media print {

            .btn,
            .pagination,
            .modal,
            .card-header {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
