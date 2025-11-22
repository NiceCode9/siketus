@extends('layouts.app', ['pageTitle' => 'Dashboard Guru'])

@section('content')
    <div class="row">
        <!-- Info Tahun Akademik -->
        <div class="col-lg-12">
            <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="icon fas fa-calendar-alt"></i>
                            Tahun Akademik: <strong>{{ $tahunAkademikAktif->nama_tahun_akademik ?? '-' }}</strong>
                            &nbsp;|&nbsp;
                            Semester: <span
                                class="badge badge-primary">{{ ucfirst($tahunAkademikAktif->semester ?? '-') }}</span>
                        </h5>
                    </div>
                    <div>
                        <small class="text-muted">
                            @if ($tahunAkademikAktif)
                                {{ $tahunAkademikAktif->getTanggalMulai()?->format('d M Y') }} -
                                {{ $tahunAkademikAktif->getTanggalSelesai()?->format('d M Y') }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kelas yang Diajar -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $kelasYangDiajar->count() }}</h3>
                    <p>Kelas yang Diajar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <a href="#kelasDetail" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $jadwalHariIni->count() }}</h3>
                    <p>Jadwal Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#jadwalHariIni" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Siswa Perlu Remidi -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $siswaRemidi->count() }}</h3>
                    <p>Siswa Perlu Remidi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('guru.remidi.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Siswa Tidak Layak Ujian -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $eligibilityData['tidak_layak'] }}</h3>
                    <p>Tidak Layak Ujian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ban"></i>
                </div>
                <a href="#siswaTidakLayak" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERT: SISWA TIDAK LAYAK UJIAN DI KELAS ANDA --}}
    {{-- ============================================ --}}
    @if ($eligibilityData['tidak_layak'] > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                    <p class="mb-0">
                        Terdapat <strong>{{ $eligibilityData['tidak_layak'] }} siswa</strong> di kelas yang Anda ajar
                        yang <strong>belum memenuhi syarat</strong> untuk mengikuti Ujian Semester
                        {{ ucfirst($tahunAkademikAktif->semester ?? '-') }}.
                        @if ($eligibilityData['masalah_mapel_guru'] > 0)
                            <br>
                            <span class="text-warning">
                                <i class="fas fa-book"></i> {{ $eligibilityData['masalah_mapel_guru'] }} siswa memiliki
                                masalah di mata pelajaran yang Anda ajar.
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Jadwal Mengajar Hari Ini -->
        <div class="col-lg-6">
            <div class="card" id="jadwalHariIni">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day mr-1"></i>
                        Jadwal Mengajar Hari Ini
                    </h3>
                </div>
                <div class="card-body">
                    @if ($jadwalHariIni->count() > 0)
                        <div class="timeline">
                            @foreach ($jadwalHariIni as $jadwal)
                                <div>
                                    <i class="fas fa-clock bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </span>
                                        <h3 class="timeline-header">
                                            {{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}
                                        </h3>
                                        <div class="timeline-body">
                                            <strong>Kelas:</strong> {{ $jadwal->guruKelas->kelas->nama_lengkap ?? '-' }}
                                            <br>
                                            <strong>Ruangan:</strong> {{ $jadwal->ruangan ?? '-' }}
                                        </div>
                                        <div class="timeline-footer">
                                            <a href="{{ route('guru.jadwal-guru.show', $jadwal->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada jadwal mengajar hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pertemuan Terbaru -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        Pertemuan Terbaru
                    </h3>
                </div>
                <div class="card-body">
                    @if ($pertemuanTerbaru->count() > 0)
                        <ul class="list-group">
                            @foreach ($pertemuanTerbaru as $pertemuan)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $pertemuan->jadwalPelajaran->guruKelas->kelas->nama_lengkap ?? '-' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $pertemuan->tanggal->format('d M Y') }} - Pertemuan
                                                ke-{{ $pertemuan->pertemuan_ke }}
                                            </small>
                                        </div>
                                        <div>
                                            @php
                                                $totalAbsensi = $pertemuan->absensi->count();
                                                $hadir = $pertemuan->absensi
                                                    ->where('status_kehadiran', 'hadir')
                                                    ->count();
                                                $persentase =
                                                    $totalAbsensi > 0 ? round(($hadir / $totalAbsensi) * 100) : 0;
                                            @endphp
                                            <span
                                                class="badge badge-{{ $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger') }}">
                                                Kehadiran: {{ $persentase }}%
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada pertemuan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Kelas yang Diajar -->
        <div class="col-lg-12">
            <div class="card" id="kelasDetail">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chalkboard mr-1"></i>
                        Daftar Kelas yang Diajar
                    </h3>
                </div>
                <div class="card-body">
                    @if ($kelasYangDiajar->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kelasYangDiajar as $index => $guruKelas)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                            <td>{{ $guruKelas->kelas->nama_lengkap ?? '-' }}</td>
                                            <td>
                                                @if ($guruKelas->aktif)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('guru.penilaian.index') }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Input Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada kelas yang diajar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Nilai per Kelas -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Statistik Rata-rata Nilai per Kelas
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statistikNilaiChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Siswa yang Perlu Remidi -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Siswa yang Perlu Remidi (10 Terbaru)
                    </h3>
                </div>
                <div class="card-body">
                    @if ($siswaRemidi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Ujian</th>
                                        <th>Nilai Asli</th>
                                        <th>KKM</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaRemidi as $index => $remidi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $remidi->siswa->nama ?? '-' }}</td>
                                            <td>{{ $remidi->guruKelas->kelas->nama_lengkap ?? '-' }}</td>
                                            <td>{{ $remidi->jenisUjian->nama_jenis_ujian ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-danger">{{ $remidi->nilai_asli }}</span>
                                            </td>
                                            <td>{{ $remidi->kkm }}</td>
                                            <td>
                                                <a href="{{ route('guru.remidi.show', $remidi->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Tidak ada siswa yang perlu remidi saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DAFTAR SISWA TIDAK LAYAK UJIAN DI KELAS GURU --}}
    {{-- ============================================ --}}
    <div class="row" id="siswaTidakLayak">
        <div class="col-lg-12">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ban mr-1"></i>
                        Siswa Tidak Layak Ujian di Kelas Anda
                        <span class="badge badge-danger ml-2">{{ $eligibilityData['tidak_layak'] }} siswa</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if ($eligibilityData['siswa_tidak_layak']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th class="text-center">Mapel</th>
                                        <th class="text-center">Kedisiplinan</th>
                                        <th class="text-center">Keagamaan</th>
                                        <th>Ringkasan Masalah</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($eligibilityData['siswa_tidak_layak'] as $index => $data)
                                        <tr class="{{ $data['has_mapel_issues_guru'] ? 'table-warning' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $data['siswa']->nisn ?? '-' }}</td>
                                            <td>
                                                <strong>{{ $data['siswa']->nama ?? '-' }}</strong>
                                                @if ($data['has_mapel_issues_guru'])
                                                    <br>
                                                    <small class="text-danger">
                                                        <i class="fas fa-exclamation-circle"></i> Perlu remidi di mapel
                                                        Anda
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $data['kelas']->nama_lengkap ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($data['has_mapel_issues'])
                                                    <span class="badge badge-danger" title="Ada masalah nilai mapel">
                                                        <i class="fas fa-times"></i>
                                                    </span>
                                                @else
                                                    <span class="badge badge-success" title="OK">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($data['has_kedisiplinan_issues'])
                                                    <span class="badge badge-danger" title="Ada masalah kedisiplinan">
                                                        <i class="fas fa-times"></i>
                                                    </span>
                                                @else
                                                    <span class="badge badge-success" title="OK">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($data['has_keagamaan_issues'])
                                                    <span class="badge badge-danger" title="Ada masalah keagamaan">
                                                        <i class="fas fa-times"></i>
                                                    </span>
                                                @else
                                                    <span class="badge badge-success" title="OK">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    @foreach ($data['summary'] as $summary)
                                                        <span class="d-block text-danger">• {{ $summary }}</span>
                                                    @endforeach
                                                </small>
                                            </td>
                                            <td>
                                                @if ($data['has_mapel_issues_guru'])
                                                    <a href="{{ route('guru.remidi.index') }}"
                                                        class="btn btn-sm btn-warning" title="Input Nilai Remidi">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Legend --}}
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-square text-warning"></i> = Siswa yang perlu remidi di mata pelajaran yang
                                Anda ajar
                            </small>
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i>
                            Semua siswa di kelas yang Anda ajar memenuhi syarat untuk mengikuti Ujian Semester
                            {{ ucfirst($tahunAkademikAktif->semester ?? '-') }}!
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
                    <a href="{{ route('guru.absensi.index') }}" class="btn btn-primary">
                        <i class="fas fa-check-square"></i> Input Absensi
                    </a>
                    <a href="{{ route('guru.penilaian.index') }}" class="btn btn-success">
                        <i class="fas fa-edit"></i> Input Nilai
                    </a>
                    <a href="{{ route('guru.jadwal-guru.index') }}" class="btn btn-info">
                        <i class="fas fa-calendar"></i> Lihat Jadwal Lengkap
                    </a>
                    <a href="{{ route('guru.remidi.index') }}" class="btn btn-warning">
                        <i class="fas fa-list"></i> Kelola Remidi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Chart Statistik Nilai per Kelas
        const statistikCtx = document.getElementById('statistikNilaiChart').getContext('2d');

        const statistikData = @json($statistikNilai);
        const labels = statistikData.map(item => item.kelas + ' - ' + item.mapel);
        const data = statistikData.map(item => item.rata_rata);

        const statistikChart = new Chart(statistikCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
@endpush
