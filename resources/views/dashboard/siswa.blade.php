@extends('layouts.app', ['pageTitle' => 'Dashboard Siswa'])

@section('content')
    {{-- ============================================ --}}
    {{-- ALERT KELAYAKAN UJIAN SEMESTER --}}
    {{-- ============================================ --}}
    @if (!$eligibility['eligible'])
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fas fa-ban"></i> Peringatan: Tidak Dapat Mengikuti Ujian Semester!</h4>
                    <p>
                        Anda <strong>belum memenuhi syarat</strong> untuk mengikuti
                        <strong>Ujian Semester {{ ucfirst($eligibility['semester'] ?? '-') }}</strong>
                        Tahun Akademik <strong>{{ $eligibility['tahun_akademik'] ?? '-' }}</strong>.
                    </p>

                    {{-- Summary Issues --}}
                    @if (!empty($eligibility['summary']))
                        <hr>
                        <p><strong>Ringkasan Masalah:</strong></p>
                        <ul class="mb-0">
                            @foreach ($eligibility['summary'] as $summary)
                                <li>{{ $summary }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detail Issues per Kategori --}}
        <div class="row">
            {{-- Masalah Penilaian Mapel (Remidi) --}}
            @if ($eligibility['has_mapel_issues'])
                <div class="col-lg-4">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-book mr-1"></i>
                                Penilaian Mapel
                                <span class="badge badge-danger">{{ $eligibility['mapel_issues_count'] }} masalah</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0" style="display: none;">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th>Mata Pelajaran</th>
                                            <th>Jenis Ujian</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($eligibility['issues']['mapel'] as $issue)
                                            <tr>
                                                <td>
                                                    <strong class="d-block">{{ $issue['mapel'] ?? '-' }}</strong>
                                                    <small class="text-danger">{{ $issue['message'] ?? '' }}</small>
                                                </td>

                                                <td>{{ $issue['jenis_ujian'] ?? '-' }}</td>

                                                <td class="text-center">
                                                    @php $type = $issue['type'] ?? null; @endphp

                                                    @if ($type === 'remidi_pending')
                                                        <span class="badge badge-danger"
                                                            title="Nilai: {{ $issue['nilai_asli'] ?? '?' }}, KKM: {{ $issue['kkm'] ?? '?' }}">
                                                            {{ $issue['nilai_asli'] ?? '?' }} / {{ $issue['kkm'] ?? '?' }}
                                                        </span>

                                                    @elseif ($type === 'no_penilaian')
                                                        <span class="badge badge-secondary">Belum Ada</span>

                                                    @elseif ($type === 'nilai_belum_diinput')
                                                        <span class="badge badge-warning">Belum Dinilai</span>

                                                    @else
                                                        <span class="badge badge-info">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    Tidak ada data masalah mapel.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('siswa.remidi.index') }}" class="btn btn-sm btn-danger">
                                <i class="fas fa-list"></i> Lihat Semua Remidi
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Masalah Kedisiplinan --}}
            @if ($eligibility['has_kedisiplinan_issues'])
                <div class="col-lg-4">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-check mr-1"></i>
                                Kedisiplinan
                                <span class="badge badge-warning">{{ $eligibility['kedisiplinan_issues_count'] }}
                                    masalah</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0" style="display: none;">
                            <ul class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                @foreach ($eligibility['issues']['kedisiplinan'] as $issue)
                                    <li class="list-group-item">
                                        @if ($issue['type'] === 'kedisiplinan_kurang')
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><strong>Persentase Kedisiplinan</strong></span>
                                                <div class="text-right">
                                                    <span class="badge badge-warning">{{ $issue['persentase'] }}%</span>
                                                    <br>
                                                    <small>Min: {{ $issue['minimal'] }}%</small>
                                                </div>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: {{ $issue['persentase'] }}%"
                                                    aria-valuenow="{{ $issue['persentase'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Terpenuhi:
                                                {{ $issue['terpenuhi'] }}/{{ $issue['total'] }}</small>
                                        @else
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $issue['jenis'] }}</span>
                                                <span class="badge badge-secondary">Belum Terpenuhi</span>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Masalah Keagamaan --}}
            @if ($eligibility['has_keagamaan_issues'])
                <div class="col-lg-4">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-pray mr-1"></i>
                                Keagamaan
                                <span class="badge badge-info">{{ $eligibility['keagamaan_issues_count'] }} masalah</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0" style="display: none;">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th>Kegiatan</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($eligibility['issues']['keagamaan'] as $issue)
                                            <tr>
                                                <td>
                                                    <strong class="d-block">{{ $issue['kegiatan'] }}</strong>
                                                    @if ($issue['type'] === 'keagamaan_kurang')
                                                        <small class="text-danger">Nilai di bawah standar</small>
                                                    @else
                                                        <small class="text-muted">Belum dinilai</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($issue['type'] === 'keagamaan_kurang')
                                                        <span class="badge badge-danger"
                                                            title="Minimal: {{ $issue['minimal'] }}">
                                                            {{ $issue['nilai'] }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">Min: {{ $issue['minimal'] }}</small>
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        {{-- ELIGIBLE - Tampilkan Pesan Sukses --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check-circle"></i> Selamat!</h5>
                    Anda <strong>memenuhi syarat</strong> untuk mengikuti
                    <strong>Ujian Semester {{ ucfirst($eligibility['semester'] ?? '-') }}</strong>
                    Tahun Akademik <strong>{{ $eligibility['tahun_akademik'] ?? '-' }}</strong>.
                </div>
            </div>
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
                            <span
                                class="float-right badge badge-{{ $siswa->status == 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($siswa->status) }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Tahun Akademik</b>
                            <a class="float-right">{{ $tahunAkademikAktif->nama_tahun_akademik ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Semester</b>
                            <span class="float-right badge badge-primary">
                                {{ ucfirst($tahunAkademikAktif->semester ?? '-') }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Rekap Absensi & Status Ujian -->
        <div class="col-lg-8">
            <!-- Status Kelayakan Ujian -->
            <div class="card {{ $eligibility['eligible'] ? 'card-success' : 'card-danger' }}">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check mr-1"></i>
                        Status Kelayakan Ujian Semester
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6">
                            <div
                                class="info-box {{ !$eligibility['has_mapel_issues'] ? 'bg-success' : 'bg-danger' }} mb-0">
                                <span class="info-box-icon"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Mapel</span>
                                    <span class="info-box-number">
                                        {{ !$eligibility['has_mapel_issues'] ? 'OK' : $eligibility['mapel_issues_count'] . ' Issue' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div
                                class="info-box {{ !$eligibility['has_kedisiplinan_issues'] ? 'bg-success' : 'bg-warning' }} mb-0">
                                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kedisiplinan</span>
                                    <span class="info-box-number">
                                        {{ !$eligibility['has_kedisiplinan_issues'] ? 'OK' : $eligibility['kedisiplinan_issues_count'] . ' Issue' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div
                                class="info-box {{ !$eligibility['has_keagamaan_issues'] ? 'bg-success' : 'bg-info' }} mb-0">
                                <span class="info-box-icon"><i class="fas fa-pray"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Keagamaan</span>
                                    <span class="info-box-number">
                                        {{ !$eligibility['has_keagamaan_issues'] ? 'OK' : $eligibility['keagamaan_issues_count'] . ' Issue' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box {{ $eligibility['eligible'] ? 'bg-success' : 'bg-danger' }} mb-0">
                                <span class="info-box-icon"><i
                                        class="fas {{ $eligibility['eligible'] ? 'fa-check' : 'fa-times' }}"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">
                                        {{ $eligibility['eligible'] ? 'LAYAK' : 'TIDAK LAYAK' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rekap Absensi -->
            {{-- <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check mr-1"></i>
                        Rekap Absensi
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-success mb-0">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hadir</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['hadir'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-warning mb-0">
                                <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Izin</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['izin'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-info mb-0">
                                <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sakit</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['sakit'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-box bg-danger mb-0">
                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Alpha</span>
                                    <span class="info-box-number">{{ $rekapAbsensi['alpha'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    {{-- <div class="row">
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
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Tidak ada jadwal pelajaran minggu ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}

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
                                                <span
                                                    class="badge badge-{{ $nilai->nilai >= 75 ? 'success' : 'danger' }}">
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
                        <div class="alert alert-info mb-0">
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
                                        <span
                                            class="badge badge-{{ $event->jenis_libur == 'nasional' ? 'danger' : 'info' }}">
                                            {{ ucfirst($event->jenis_libur) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Tidak ada event bulan ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
                    {{-- <a href="{{ route('siswa.penilaian.index') }}" class="btn btn-success">
                        <i class="fas fa-edit"></i> Input Nilai Diri
                    </a> --}}
                    <a href="{{ route('siswa.riwayat-penilaian.siswa.index') }}" class="btn btn-info">
                        <i class="fas fa-history"></i> Riwayat Nilai
                    </a>
                    @if ($remidiPending->count() > 0)
                        <a href="{{ route('siswa.remidi.index') }}" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle"></i> Lihat Remidi ({{ $remidiPending->count() }})
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

        .info-box.mb-0 {
            min-height: 80px;
        }

        /* Sticky header untuk tabel dalam scroll */
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa;
        }

        /* Smooth scroll untuk tabel */
        .table-responsive::-webkit-scrollbar {
            width: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Animasi untuk collapse card */
        .card-body {
            transition: all 0.3s ease;
        }

        /* Highlight row on hover */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, .03);
        }
    </style>
@endpush
