@extends('layouts.app', ['pageTitle' => 'Dashboard Admin'])

@section('content')
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

        <!-- Card Siswa Remidi -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $remidiPending }}</h3>
                    <p>Siswa Perlu Remidi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#siswaRemidi" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

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
                                {{ $tahunAkademikAktif->tanggal_mulai->format('d M Y') }} -
                                {{ $tahunAkademikAktif->tanggal_selesai->format('d M Y') }}
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
    </script>
@endpush
