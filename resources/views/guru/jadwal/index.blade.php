@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0">
                    <i class="fas fa-calendar-check"></i> Jadwal Mengajar Saya
                </h3>
                <p class="text-muted">{{ $tahunAkademik->nama_tahun_akademik ?? '-' }}</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total_jam_minggu'] }}</h3>
                        <p>Jam Mengajar / Minggu</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['total_kelas'] }}</h3>
                        <p>Kelas Diampu</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['jadwal_hari_ini'] }}</h3>
                        <p>Jadwal Hari Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['pertemuan_bulan_ini'] }}</h3>
                        <p>Pertemuan Bulan Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-day"></i>
                            Jadwal Hari Ini - {{ $hariIni }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($jadwalHariIni->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada jadwal mengajar hari ini. Selamat beristirahat! 🎉
                            </div>
                        @else
                            <!-- Jadwal Sedang Berlangsung -->
                            @if ($jadwalSedangBerlangsung)
                                <div class="alert alert-success border-left-success mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-play-circle fa-3x"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">
                                                <span class="badge badge-success">SEDANG BERLANGSUNG</span>
                                            </h5>
                                            <h4 class="mb-1">
                                                {{ $jadwalSedangBerlangsung->guruKelas->guruMapel->mapel->nama_mapel }}</h4>
                                            <p class="mb-2">
                                                <i class="fas fa-clock"></i>
                                                {{ \Carbon\Carbon::parse($jadwalSedangBerlangsung->jam_mulai)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($jadwalSedangBerlangsung->jam_selesai)->format('H:i') }}
                                                &nbsp;|&nbsp;
                                                <i class="fas fa-users"></i>
                                                {{ $jadwalSedangBerlangsung->guruKelas->kelas->nama_kelas }}
                                                &nbsp;|&nbsp;
                                                <i class="fas fa-door-open"></i>
                                                {{ $jadwalSedangBerlangsung->ruangan ?? '-' }}
                                            </p>
                                            <div>
                                                <a href="{{ route('guru.absensi.create', $jadwalSedangBerlangsung->id) }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-user-check"></i> Mulai Absensi
                                                </a>
                                                {{-- <a href="{{ route('guru.materi.index', $jadwalSedangBerlangsung->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-book"></i> Lihat Materi
                                                </a> --}}
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="viewDetail({{ $jadwalSedangBerlangsung->id }})">
                                                    <i class="fas fa-info-circle"></i> Detail
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Jadwal Selanjutnya -->
                            <h6 class="mb-3">
                                <i class="fas fa-list"></i>
                                @if ($jadwalSedangBerlangsung)
                                    Jadwal Selanjutnya
                                @else
                                    Semua Jadwal Hari Ini
                                @endif
                            </h6>
                            <div class="timeline">
                                @foreach ($jadwalHariIni as $jadwal)
                                    @if (!$jadwalSedangBerlangsung || $jadwal->id != $jadwalSedangBerlangsung->id)
                                        <div class="time-label">
                                            <span class="bg-primary">
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                            </span>
                                        </div>
                                        <div>
                                            <i class="fas fa-book bg-{{ $jadwal->isUpcoming ? 'info' : 'secondary' }}"></i>
                                            <div class="timeline-item">
                                                <span class="time">
                                                    <i class="fas fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                </span>
                                                <h3 class="timeline-header">
                                                    {{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel }}
                                                    @if ($jadwal->isUpcoming)
                                                        <span class="badge badge-info ml-2">Akan Datang</span>
                                                    @else
                                                        <span class="badge badge-secondary ml-2">Sudah Lewat</span>
                                                    @endif
                                                </h3>
                                                <div class="timeline-body">
                                                    <p class="mb-2">
                                                        <i class="fas fa-users"></i> <strong>Kelas:</strong>
                                                        {{ $jadwal->guruKelas->kelas->nama_kelas }}<br>
                                                        <i class="fas fa-door-open"></i> <strong>Ruangan:</strong>
                                                        {{ $jadwal->ruangan ?? '-' }}
                                                    </p>
                                                    @if ($jadwal->isUpcoming)
                                                        <button class="btn btn-sm btn-outline-primary reminder-btn"
                                                            data-id="{{ $jadwal->id }}">
                                                            <i class="fas fa-bell"></i> Set Reminder
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-info"
                                                        onclick="viewDetail({{ $jadwal->id }})">
                                                        <i class="fas fa-info-circle"></i> Detail
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div>
                                    <i class="fas fa-check bg-success"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Minggu Ini -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-week"></i> Jadwal Minggu Ini
                        </h5>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-danger" id="btn-export">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="hari-tabs" role="tablist">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $index => $hari)
                                <li class="nav-item">
                                    <a class="nav-link {{ $hari == $hariIni ? 'active' : '' }}"
                                        id="tab-{{ strtolower($hari) }}" data-toggle="tab" href="#{{ strtolower($hari) }}"
                                        role="tab">
                                        {{ $hari }}
                                        <span class="badge badge-primary ml-1">{{ $jadwalPerHari[$hari]->count() }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="hari-tabContent">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <div class="tab-pane fade {{ $hari == $hariIni ? 'show active' : '' }}"
                                    id="{{ strtolower($hari) }}" role="tabpanel">
                                    @if ($jadwalPerHari[$hari]->isEmpty())
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Tidak ada jadwal mengajar pada hari
                                            {{ $hari }}.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="15%">Waktu</th>
                                                        <th width="25%">Mata Pelajaran</th>
                                                        <th width="20%">Kelas</th>
                                                        <th width="15%">Ruangan</th>
                                                        <th width="25%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($jadwalPerHari[$hari] as $jadwal)
                                                        <tr>
                                                            <td>
                                                                <i class="fas fa-clock"></i>
                                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                            </td>
                                                            <td>
                                                                <strong>{{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel }}</strong>
                                                            </td>
                                                            <td>
                                                                <i class="fas fa-users"></i>
                                                                {{ $jadwal->guruKelas->kelas->nama_lengkap }}
                                                            </td>
                                                            <td>
                                                                <i class="fas fa-door-open"></i>
                                                                {{ $jadwal->ruangan ?? '-' }}
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-info"
                                                                        onclick="viewDetail({{ $jadwal->id }})">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <a href="{{ route('guru.absensi.history', $jadwal->id) }}"
                                                                        class="btn btn-primary">
                                                                        <i class="fas fa-history"></i>
                                                                    </a>
                                                                    {{-- <a href="{{ route('guru.materi.index', $jadwal->id) }}"
                                                                        class="btn btn-success">
                                                                        <i class="fas fa-book"></i>
                                                                    </a> --}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detail-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detail Jadwal Mengajar</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Mata Pelajaran</th>
                                    <td id="detail-mapel">-</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td id="detail-kelas">-</td>
                                </tr>
                                <tr>
                                    <th>Hari</th>
                                    <td id="detail-hari">-</td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td id="detail-waktu">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Ruangan</th>
                                    <td id="detail-ruangan">-</td>
                                </tr>
                                <tr>
                                    <th>Tahun Akademik</th>
                                    <td id="detail-tahun">-</td>
                                </tr>
                                <tr>
                                    <th>Total Pertemuan</th>
                                    <td id="detail-pertemuan">-</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="detail-status">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <a href="#" id="btn-detail-absensi" class="btn btn-primary">
                        <i class="fas fa-user-check"></i> Lihat Riwayat Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .small-box {
            border-radius: 0.5rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        .small-box .icon {
            opacity: 0.3;
        }

        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            left: 31px;
            margin: 0;
            border-radius: 2px;
        }

        .timeline>div>.timeline-item {
            box-shadow: 0 1px 1px rgba(0, 0, 0, .1);
            border-radius: 3px;
            margin-top: 0;
            background: #fff;
            color: #444;
            margin-left: 60px;
            margin-right: 15px;
            margin-bottom: 15px;
            padding: 0;
            position: relative;
        }

        .timeline>div>.fas,
        .timeline>div>.far,
        .timeline>div>.fab {
            width: 40px;
            height: 40px;
            font-size: 16px;
            line-height: 40px;
            position: absolute;
            color: #fff;
            background: #999;
            border-radius: 50%;
            text-align: center;
            left: 12px;
            top: 0;
        }

        .timeline>.time-label>span {
            font-weight: 600;
            padding: 5px;
            display: inline-block;
            background-color: #fff;
            border-radius: 4px;
        }

        .timeline-item>.time {
            color: #999;
            float: right;
            padding: 10px;
            font-size: 12px;
        }

        .timeline-item>.timeline-header {
            margin: 0;
            color: #555;
            border-bottom: 1px solid #f4f4f4;
            padding: 10px;
            font-size: 16px;
            line-height: 1.1;
        }

        .timeline-item>.timeline-body {
            padding: 10px;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function viewDetail(id) {
            $.ajax({
                url: "/guru/jadwal/" + id,
                success: function(data) {
                    $('#detail-mapel').text(data.mapel);
                    $('#detail-kelas').text(data.kelas);
                    $('#detail-hari').text(data.hari);
                    $('#detail-waktu').text(data.waktu);
                    $('#detail-ruangan').text(data.ruangan || '-');
                    $('#detail-tahun').text(data.tahun_akademik);
                    $('#detail-pertemuan').text(data.total_pertemuan + ' kali');

                    let statusBadge = data.is_active ?
                        '<span class="badge badge-success">Aktif</span>' :
                        '<span class="badge badge-secondary">Tidak Aktif</span>';
                    $('#detail-status').html(statusBadge);

                    $('#btn-detail-absensi').attr('href', '/guru/absensi/history/' + id);
                    $('#detail-modal').modal('show');
                },
                error: function() {
                    Swal.fire('Error!', 'Gagal mengambil detail jadwal.', 'error');
                }
            });
        }

        $(document).ready(function() {
            // Reminder Button
            $('.reminder-btn').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Set Reminder',
                    text: 'Anda akan mendapat notifikasi 15 menit sebelum kelas dimulai.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, aktifkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX call to set reminder
                        $.ajax({
                            url: '/guru/jadwal/set-reminder/' + id,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Sukses!', 'Reminder telah diaktifkan.',
                                    'success');
                            }
                        });
                    }
                });
            });

            // Export PDF
            $('#btn-export').click(function() {
                // window.open('/guru/jadwal/export-pdf', '_blank');
                window.open('{{ route('guru.jadwal-export-pdf') }}', '_blank');
            });
        });
    </script>
@endpush
