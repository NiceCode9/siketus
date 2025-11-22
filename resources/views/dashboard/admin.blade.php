@extends('layouts.app', ['pageTitle' => 'Dashboard Admin'])

@section('content')
    {{-- Info Tahun Akademik & Semester --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt"></i>
                            Tahun Akademik: <strong>{{ $tahunAkademikAktif->nama_tahun_akademik ?? '-' }}</strong>
                            &nbsp;|&nbsp;
                            Semester: <span class="badge badge-primary">{{ ucfirst($semester) }}</span>
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
        <!-- Card Total Siswa -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalSiswa }}</h3>
                    <p>Total Siswa Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <a href="{{ route('admin.siswa.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card Total Guru -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalGuru }}</h3>
                    <p>Total Guru</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="{{ route('admin.guru.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card Total Kelas -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalKelas }}</h3>
                    <p>Total Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
                <a href="{{ route('admin.kelas.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card Siswa Tidak Layak Ujian -->
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
    {{-- SECTION: KELAYAKAN UJIAN SEMESTER --}}
    {{-- ============================================ --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check mr-1"></i>
                        Status Kelayakan Ujian Semester {{ ucfirst($semester) }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Statistik Kelayakan -->
                        <div class="col-md-4">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Siswa Layak Ujian</span>
                                    <span class="info-box-number">{{ $eligibilityData['layak'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar"
                                            style="width: {{ $eligibilityData['persentase_layak'] }}%"></div>
                                    </div>
                                    <span class="progress-description">{{ $eligibilityData['persentase_layak'] }}% dari
                                        total siswa</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-gradient-danger">
                                <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Siswa Tidak Layak</span>
                                    <span class="info-box-number">{{ $eligibilityData['tidak_layak'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-light"
                                            style="width: {{ $eligibilityData['persentase_tidak_layak'] }}%"></div>
                                    </div>
                                    <span class="progress-description">{{ $eligibilityData['persentase_tidak_layak'] }}%
                                        perlu penanganan</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Breakdown per Kategori -->
                            <div class="card card-outline card-secondary h-100">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0">Breakdown Masalah</h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="fas fa-book text-danger"></i> Mapel (Remidi)</span>
                                        <span
                                            class="badge badge-danger">{{ $eligibilityData['per_kategori']['mapel'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><i class="fas fa-user-check text-warning"></i> Kedisiplinan</span>
                                        <span
                                            class="badge badge-warning">{{ $eligibilityData['per_kategori']['kedisiplinan'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-pray text-info"></i> Keagamaan</span>
                                        <span
                                            class="badge badge-info">{{ $eligibilityData['per_kategori']['keagamaan'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik per Kelas --}}
    @if ($eligibilityData['per_kelas']->count() > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Statistik Kelayakan per Kelas
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="kelayakanPerKelasChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Statistik Absensi Hari Ini -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Statistik Absensi Hari Ini
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="absensiChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistik Remidi -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Statistik Remidi
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="remidiChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Rata-rata Nilai per Bulan -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Rata-rata Nilai 6 Bulan Terakhir
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="nilaiChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Siswa yang Perlu Remidi -->
        <div class="col-lg-12">
            <div class="card" id="siswaRemidi">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        Daftar Siswa yang Perlu Remidi (10 Terbaru)
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
                                        <th>NISN</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Jenis Ujian</th>
                                        <th>Nilai Asli</th>
                                        <th>KKM</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaRemidi as $index => $remidi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $remidi->siswa->nama ?? '-' }}</td>
                                            <td>{{ $remidi->siswa->nisn ?? '-' }}</td>
                                            <td>{{ $remidi->guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                            <td>{{ $remidi->jenisUjian->nama_jenis_ujian ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-danger">{{ $remidi->nilai_asli }}</span>
                                            </td>
                                            <td>{{ $remidi->kkm }}</td>
                                            <td>
                                                @if ($remidi->status_remidi == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-success">Selesai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada siswa yang perlu remidi saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DAFTAR SISWA TIDAK LAYAK UJIAN --}}
    {{-- ============================================ --}}
    <div class="row" id="siswaTidakLayak">
        <div class="col-lg-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ban mr-1"></i>
                        Daftar Siswa Tidak Layak Ujian Semester {{ ucfirst($semester) }}
                        <span class="badge badge-light ml-2">{{ $eligibilityData['tidak_layak'] }} siswa</span>
                    </h3>
                    <div class="card-tools">
                        {{-- Bisa ditambahkan tombol export PDF/Excel di sini --}}
                        {{-- <a href="#" class="btn btn-sm btn-light">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a> --}}
                    </div>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($eligibilityData['siswa_tidak_layak'] as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $data['siswa']->nisn ?? '-' }}</td>
                                            <td>
                                                <strong>{{ $data['siswa']->nama ?? '-' }}</strong>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($eligibilityData['tidak_layak'] > 20)
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-info-circle"></i>
                                Menampilkan 20 dari {{ $eligibilityData['tidak_layak'] }} siswa.
                                Untuk melihat semua, silakan export data.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i>
                            Semua siswa memenuhi syarat untuk mengikuti Ujian Semester {{ ucfirst($semester) }}!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Tahun Akademik Aktif -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Tahun Akademik Aktif
                    </h5>
                    <p class="card-text">
                        @if ($tahunAkademikAktif)
                            <strong>{{ $tahunAkademikAktif->nama_tahun_akademik }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $tahunAkademikAktif->getTanggalMulai()?->format('d M Y') }} -
                                {{ $tahunAkademikAktif->getTanggalSelesai()?->format('d M Y') }}
                            </small>
                        @else
                            <span class="text-danger">Tidak ada tahun akademik aktif</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Chart Absensi Hari Ini
        const absensiCtx = document.getElementById('absensiChart').getContext('2d');
        const absensiChart = new Chart(absensiCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    data: [
                        {{ $absensiHariIni['hadir'] ?? 0 }},
                        {{ $absensiHariIni['izin'] ?? 0 }},
                        {{ $absensiHariIni['sakit'] ?? 0 }},
                        {{ $absensiHariIni['alpha'] ?? 0 }}
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Chart Remidi
        const remidiCtx = document.getElementById('remidiChart').getContext('2d');
        const remidiChart = new Chart(remidiCtx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'Selesai'],
                datasets: [{
                    data: [{{ $remidiPending }}, {{ $remidiSelesai }}],
                    backgroundColor: ['#ffc107', '#28a745']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Chart Nilai per Bulan
        const nilaiCtx = document.getElementById('nilaiChart').getContext('2d');
        const namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

        const nilaiData = @json($nilaiPerBulan);
        const labels = nilaiData.map(item => namaBulan[item.bulan - 1]);
        const data = nilaiData.map(item => parseFloat(item.rata_rata).toFixed(2));

        const nilaiChart = new Chart(nilaiCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
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

        // Chart Kelayakan per Kelas
        @if ($eligibilityData['per_kelas']->count() > 0)
            const kelayakanCtx = document.getElementById('kelayakanPerKelasChart').getContext('2d');
            const kelayakanData = @json($eligibilityData['per_kelas']->values());

            const kelayakanChart = new Chart(kelayakanCtx, {
                type: 'bar',
                data: {
                    labels: kelayakanData.map(item => item.nama),
                    datasets: [{
                            label: 'Layak Ujian',
                            data: kelayakanData.map(item => item.total - item.tidak_layak),
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Tidak Layak',
                            data: kelayakanData.map(item => item.tidak_layak),
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                afterBody: function(context) {
                                    const idx = context[0].dataIndex;
                                    const item = kelayakanData[idx];
                                    return `\nPersentase Tidak Layak: ${item.persentase_tidak_layak}%`;
                                }
                            }
                        }
                    }
                }
            });
        @endif
    </script>
@endpush
