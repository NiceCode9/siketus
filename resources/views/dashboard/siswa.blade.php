@extends('layouts.app', ['pageTitle' => 'Dashboard Siswa'])

@section('content')
    @if (!$cek)
        <div class="alert alert-warning" role="alert">
            Anda Belum Bisa mengikuti ujian karena ada penilaian yang belum Lengkap.
        </div>
    @endif
    <div class="row">
        <!-- Profil Siswa Card -->
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $siswa->nama }}</h3>
                    <p class="text-muted text-center">NISN: {{ $siswa->nisn }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Kelas</b>
                            <a class="float-right">{{ $kelasSekarang->nama_lengkap ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right badge badge-{{ $siswa->status == 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($siswa->status) }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Tahun Akademik</b>
                            <a class="float-right">{{ $tahunAkademikAktif->nama_tahun_akademik ?? '-' }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Rekap Absensi & Notifikasi -->
        <div class="col-lg-8">
            <!-- Rekap Absensi -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check mr-1"></i>
                        Rekap Absensi
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hadir</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['hadir'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Izin</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['izin'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sakit</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['sakit'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Alpha</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['alpha'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifikasi Remidi -->
            @if ($remidiPending->count() > 0)
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                    Anda memiliki <strong>{{ $remidiPending->count() }}</strong> mata pelajaran yang perlu remedial.
                    <a href="{{ route('siswa.remidi.index') }}" class="alert-link">Lihat Detail</a>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Jadwal Pelajaran Minggu Ini -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-week mr-1"></i>
                        Jadwal Pelajaran Minggu Ini
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('siswa.jadwal.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-calendar"></i> Lihat Jadwal Lengkap
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($jadwalMingguIni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Waktu</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th>Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwalMingguIni as $jadwal)
                                        <tr>
                                            <td>{{ $jadwal->hari }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                            </td>
                                            <td>
                                                <strong>{{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</strong>
                                            </td>
                                            <td>{{ $jadwal->guruKelas->guruMapel->guru->nama ?? '-' }}</td>
                                            <td>{{ $jadwal->ruangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada jadwal pelajaran minggu ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Nilai Terbaru -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-1"></i>
                        Nilai Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('siswa.riwayat-penilaian.siswa.index') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-history"></i> Lihat Riwayat Lengkap
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($nilaiTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mata Pelajaran</th>
                                        <th>Jenis Ujian</th>
                                        <th>Nilai</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nilaiTerbaru as $nilai)
                                        <tr>
                                            <td>{{ $nilai->guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                            <td>{{ $nilai->jenisUjian->nama_jenis_ujian ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $nilai->nilai >= 75 ? 'success' : 'danger' }}">
                                                    {{ $nilai->nilai }}
                                                </span>
                                            </td>
                                            <td>{{ ucfirst($nilai->semester) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada nilai yang tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kalender Akademik Bulan Ini -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Kalender Akademik
                    </h3>
                </div>
                <div class="card-body">
                    @if ($kalenderBulanIni->count() > 0)
                        <ul class="list-group">
                            @foreach ($kalenderBulanIni as $event)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $event->tanggal->format('d M') }}</strong>
                                            <br>
                                            <small>{{ $event->keterangan }}</small>
                                        </div>
                                        <span class="badge badge-{{ $event->jenis_libur == 'nasional' ? 'danger' : 'info' }}">
                                            {{ ucfirst($event->jenis_libur) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada event bulan ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Remidi -->
    @if ($remidiPending->count() > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Daftar Mata Pelajaran yang Perlu Remidi
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Jenis Ujian</th>
                                        <th>Nilai Asli</th>
                                        <th>KKM</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($remidiPending as $index => $remidi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $remidi->guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                            <td>{{ $remidi->jenisUjian->nama_jenis_ujian ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-danger">{{ $remidi->nilai_asli }}</span>
                                            </td>
                                            <td>{{ $remidi->kkm }}</td>
                                            <td>
                                                <span class="badge badge-warning">Pending</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('siswa.remidi.show', $remidi->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-1"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('siswa.jadwal.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar"></i> Lihat Jadwal Lengkap
                    </a>
                    <a href="{{ route('siswa.penilaian.index') }}" class="btn btn-success">
                        <i class="fas fa-edit"></i> Input Nilai Diri
                    </a>
                    <a href="{{ route('siswa.riwayat-penilaian.siswa.index') }}" class="btn btn-info">
                        <i class="fas fa-history"></i> Riwayat Nilai
                    </a>
                    @if ($remidiPending->count() > 0)
                        <a href="{{ route('siswa.remidi.index') }}" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle"></i> Lihat Remidi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-box-content {
            padding: 5px 10px;
        }
    </style>
@endpush