@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-1">
                                    <i class="fas fa-calendar-alt"></i> Jadwal Kelas {{ $kelas->nama_kelas }}
                                </h3>
                                <p class="mb-0 text-muted">
                                    {{ $tahunAkademik->nama_tahun_akademik }} - {{ $tahunAkademik->semester }}
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <button class="btn btn-danger" id="btn-download-pdf">
                                    <i class="fas fa-download"></i> Download PDF
                                </button>
                                {{-- <button class="btn btn-success" id="btn-add-calendar">
                                    <i class="fas fa-calendar-plus"></i> Add to Calendar
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-day"></i>
                            Hari Ini - {{ $hariIni }}, {{ now()->format('d F Y') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($jadwalHariIni->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-smile-beam fa-3x mb-3"></i>
                                <h5>Tidak ada jadwal pelajaran hari ini</h5>
                                <p class="mb-0">Selamat menikmati hari libur! 🎉</p>
                            </div>
                        @else
                            <div class="jadwal-list">
                                @foreach ($jadwalHariIni as $jadwal)
                                    @php
                                        $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai);
                                        $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                                        $sekarang = now();

                                        $isCurrent = $sekarang->between($jamMulai, $jamSelesai);
                                        $isPast = $sekarang->greaterThan($jamSelesai);
                                        $isUpcoming = $sekarang->lessThan($jamMulai);

                                        $cardClass = $isCurrent
                                            ? 'border-success'
                                            : ($isPast
                                                ? 'border-secondary'
                                                : 'border-primary');
                                        $badgeClass = $isCurrent ? 'success' : ($isPast ? 'secondary' : 'primary');
                                        $statusText = $isCurrent
                                            ? 'SEDANG BERLANGSUNG'
                                            : ($isPast
                                                ? 'SUDAH SELESAI'
                                                : 'AKAN DATANG');
                                        $iconClass = $isCurrent
                                            ? 'fa-play-circle'
                                            : ($isPast
                                                ? 'fa-check-circle'
                                                : 'fa-clock');

                                        $mapelColors = [
                                            'Matematika' => 'primary',
                                            'Fisika' => 'success',
                                            'Kimia' => 'info',
                                            'Biologi' => 'teal',
                                            'Bahasa Indonesia' => 'warning',
                                            'Bahasa Inggris' => 'orange',
                                            'Sejarah' => 'purple',
                                            'Geografi' => 'pink',
                                        ];

                                        $namaMapel = $jadwal->guruKelas->guruMapel->mapel->nama_mapel;
                                        $colorClass = $mapelColors[$namaMapel] ?? 'secondary';
                                    @endphp

                                    <div
                                        class="card mb-3 {{ $cardClass }} shadow-sm jadwal-card {{ $isCurrent ? 'current-class' : '' }}">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center border-right">
                                                    <div class="jam-display">
                                                        <h2 class="mb-0 text-{{ $colorClass }}">
                                                            {{ $jamMulai->format('H:i') }}
                                                        </h2>
                                                        <small class="text-muted">sampai</small>
                                                        <h4 class="mb-0 text-{{ $colorClass }}">
                                                            {{ $jamSelesai->format('H:i') }}
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="d-flex align-items-start mb-2">
                                                        <i
                                                            class="fas {{ $iconClass }} fa-2x text-{{ $badgeClass }} mr-3 mt-1"></i>
                                                        <div>
                                                            <span
                                                                class="badge badge-{{ $badgeClass }} mb-2">{{ $statusText }}</span>
                                                            <h4 class="mb-1">
                                                                <span class="badge badge-{{ $colorClass }} mr-2">
                                                                    <i class="fas fa-book"></i>
                                                                </span>
                                                                {{ $namaMapel }}
                                                            </h4>
                                                            <p class="mb-2">
                                                                <i class="fas fa-chalkboard-teacher text-muted"></i>
                                                                <strong>Guru:</strong>
                                                                {{ $jadwal->guruKelas->guruMapel->guru->nama }}
                                                            </p>
                                                            <p class="mb-0">
                                                                <i class="fas fa-door-open text-muted"></i>
                                                                <strong>Ruangan:</strong>
                                                                {{ $jadwal->ruangan ?? 'Belum ditentukan' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-right">
                                                    @if ($isCurrent)
                                                        <div class="alert alert-success mb-2 py-2">
                                                            <i class="fas fa-info-circle"></i>
                                                            <small>Sedang berlangsung</small>
                                                        </div>
                                                    @elseif($isUpcoming)
                                                        @php
                                                            $diff = $sekarang->diffForHumans($jamMulai);
                                                        @endphp
                                                        <div class="alert alert-info mb-2 py-2">
                                                            <i class="fas fa-hourglass-half"></i>
                                                            <small>{{ $diff }}</small>
                                                        </div>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-info btn-block"
                                                        onclick="viewDetail({{ $jadwal->id }})">
                                                        <i class="fas fa-info-circle"></i> Detail
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                    </div>
                    <div class="card-body">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-pills nav-fill mb-3" id="hari-tabs" role="tablist">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $index => $hari)
                                <li class="nav-item">
                                    <a class="nav-link {{ $hari == $hariIni ? 'active' : '' }}"
                                        id="tab-{{ strtolower($hari) }}" data-toggle="pill"
                                        href="#{{ strtolower($hari) }}" role="tab">
                                        <i class="fas fa-calendar-day"></i>
                                        <strong>{{ $hari }}</strong>
                                        <br>
                                        <small class="badge badge-{{ $hari == $hariIni ? 'light' : 'primary' }}">
                                            {{ $jadwalPerHari[$hari]->count() }} jadwal
                                        </small>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="hari-tabContent">
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <div class="tab-pane fade {{ $hari == $hariIni ? 'show active' : '' }}"
                                    id="{{ strtolower($hari) }}" role="tabpanel">
                                    @if ($jadwalPerHari[$hari]->isEmpty())
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                                            <p class="mb-0">Tidak ada jadwal pelajaran pada hari
                                                <strong>{{ $hari }}</strong>
                                            </p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="15%"><i class="fas fa-clock"></i> Waktu</th>
                                                        <th width="30%"><i class="fas fa-book"></i> Mata Pelajaran</th>
                                                        <th width="25%"><i class="fas fa-chalkboard-teacher"></i> Guru
                                                        </th>
                                                        <th width="15%"><i class="fas fa-door-open"></i> Ruangan</th>
                                                        <th width="15%" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($jadwalPerHari[$hari] as $jadwal)
                                                        @php
                                                            $namaMapel =
                                                                $jadwal->guruKelas->guruMapel->mapel->nama_mapel;
                                                            $mapelColors = [
                                                                'Matematika' => 'primary',
                                                                'Fisika' => 'success',
                                                                'Kimia' => 'info',
                                                                'Biologi' => 'teal',
                                                                'Bahasa Indonesia' => 'warning',
                                                                'Bahasa Inggris' => 'orange',
                                                            ];
                                                            $colorClass = $mapelColors[$namaMapel] ?? 'secondary';
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <strong class="text-{{ $colorClass }}">
                                                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                                </strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    s/d
                                                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $colorClass }} mr-1">
                                                                    <i class="fas fa-book"></i>
                                                                </span>
                                                                <strong>{{ $namaMapel }}</strong>
                                                            </td>
                                                            <td>
                                                                <i class="fas fa-user-tie text-muted"></i>
                                                                {{ $jadwal->guruKelas->guruMapel->guru->nama }}
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-light">
                                                                    {{ $jadwal->ruangan ?? '-' }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <button class="btn btn-sm btn-info"
                                                                    onclick="viewDetail({{ $jadwal->id }})">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
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

        <!-- Info Card -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Informasi</h5>
                    <ul class="mb-0">
                        <li>Harap datang 10 menit sebelum pelajaran dimulai</li>
                        <li>Jika ada perubahan jadwal, akan ada notifikasi dari sekolah</li>
                        <li>Pastikan membawa perlengkapan sesuai mata pelajaran</li>
                        <li>Untuk jadwal praktikum, konfirmasi ruangan kepada guru pengampu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detail-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i> Detail Jadwal Pelajaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-book"></i> Informasi Pelajaran
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="45%">Mata Pelajaran</th>
                                    <td id="detail-mapel">-</td>
                                </tr>
                                <tr>
                                    <th>Guru Pengampu</th>
                                    <td id="detail-guru">-</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td id="detail-kelas">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-calendar-alt"></i> Jadwal
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="45%">Hari</th>
                                    <td id="detail-hari">-</td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td id="detail-waktu">-</td>
                                </tr>
                                <tr>
                                    <th>Ruangan</th>
                                    <td id="detail-ruangan">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-line"></i> Informasi Tambahan
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="25%">Tahun Akademik</th>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-info" id="btn-add-reminder">
                        <i class="fas fa-bell"></i> Set Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .jadwal-card {
            border-width: 2px;
            transition: all 0.3s ease;
        }

        .jadwal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
        }

        .current-class {
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {

            0%,
            100% {
                border-color: #28a745;
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }

            50% {
                border-color: #20c997;
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
        }

        .jam-display h2,
        .jam-display h4 {
            font-weight: bold;
        }

        .nav-pills .nav-link {
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s;
        }

        .nav-pills .nav-link:hover {
            background-color: #f8f9fa;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .border-right {
            border-right: 2px solid #dee2e6 !important;
        }

        .badge {
            padding: 0.4em 0.6em;
        }

        .text-teal {
            color: #20c997 !important;
        }

        .text-orange {
            color: #fd7e14 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .text-pink {
            color: #e83e8c !important;
        }

        .badge-teal {
            background-color: #20c997 !important;
            color: white;
        }

        .badge-orange {
            background-color: #fd7e14 !important;
            color: white;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function viewDetail(id) {
            $.ajax({
                url: "/siswa/jadwal/show/" + id,
                success: function(data) {
                    $('#detail-mapel').html('<strong>' + data.mapel + '</strong>');
                    $('#detail-guru').text(data.guru);
                    $('#detail-kelas').text(data.kelas);
                    $('#detail-hari').html('<span class="badge badge-primary">' + data.hari + '</span>');
                    $('#detail-waktu').html('<strong>' + data.waktu + '</strong>');
                    $('#detail-ruangan').html(data.ruangan ?
                        '<span class="badge badge-info">' + data.ruangan + '</span>' :
                        '<span class="badge badge-secondary">Belum ditentukan</span>');
                    $('#detail-tahun').text(data.tahun_akademik);
                    $('#detail-pertemuan').text(data.total_pertemuan + ' kali pertemuan');

                    let statusBadge = data.is_active ?
                        '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>' :
                        '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Tidak Aktif</span>';
                    $('#detail-status').html(statusBadge);

                    $('#btn-add-reminder').data('id', id);
                    $('#detail-modal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengambil detail jadwal!',
                    });
                }
            });
        }

        $(document).ready(function() {
            // Download PDF
            $('#btn-download-pdf').click(function() {
                Swal.fire({
                    title: 'Download Jadwal',
                    text: 'Pilih format jadwal yang ingin didownload',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-calendar-week"></i> Jadwal Minggu Ini',
                    cancelButtonText: '<i class="fas fa-calendar"></i> Jadwal Semester',
                    showDenyButton: true,
                    denyButtonText: '<i class="fas fa-calendar-day"></i> Jadwal Hari Ini'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open('/siswa/jadwal/download-pdf?type=weekly', '_blank');
                    } else if (result.isDenied) {
                        window.open('/siswa/jadwal/download-pdf?type=daily', '_blank');
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.open('/siswa/jadwal/download-pdf?type=semester', '_blank');
                    }
                });
            });

            // Add to Calendar
            $('#btn-add-calendar').click(function() {
                Swal.fire({
                    title: 'Export ke Calendar',
                    html: '<p>Pilih format calendar yang Anda gunakan:</p>' +
                        '<div class="btn-group-vertical w-100">' +
                        '<button class="btn btn-outline-primary mb-2" onclick="exportCalendar(\'google\')"><i class="fab fa-google"></i> Google Calendar</button>' +
                        '<button class="btn btn-outline-info mb-2" onclick="exportCalendar(\'outlook\')"><i class="fab fa-microsoft"></i> Outlook</button>' +
                        '<button class="btn btn-outline-secondary" onclick="exportCalendar(\'ical\')"><i class="fas fa-calendar"></i> iCal (Apple)</button>' +
                        '</div>',
                    showConfirmButton: false,
                    showCloseButton: true
                });
            });

            // Set Reminder
            $(document).on('click', '#btn-add-reminder', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Set Reminder',
                    html: '<p>Kapan Anda ingin diingatkan sebelum pelajaran dimulai?</p>' +
                        '<select id="reminder-time" class="form-control">' +
                        '<option value="5">5 menit sebelumnya</option>' +
                        '<option value="10" selected>10 menit sebelumnya</option>' +
                        '<option value="15">15 menit sebelumnya</option>' +
                        '<option value="30">30 menit sebelumnya</option>' +
                        '</select>',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Set Reminder',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        return $('#reminder-time').val();
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/siswa/jadwal/set-reminder',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                jadwal_id: id,
                                reminder_time: result.value
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Reminder Diaktifkan!',
                                    text: 'Anda akan mendapat notifikasi ' +
                                        result.value +
                                        ' menit sebelum pelajaran dimulai.',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal mengaktifkan reminder. Silakan coba lagi.',
                                });
                            }
                        });
                    }
                });
            });

            // Auto refresh untuk update status jadwal hari ini
            setInterval(function() {
                // Hanya refresh jika ada jadwal hari ini
                if ($('.jadwal-list .jadwal-card').length > 0) {
                    location.reload();
                }
            }, 300000); // Refresh setiap 5 menit
        });

        function exportCalendar(type) {
            window.location.href = '/siswa/jadwal/export-calendar?type=' + type;
            Swal.fire({
                icon: 'success',
                title: 'Export Berhasil!',
                text: 'File calendar sedang didownload...',
                timer: 2000,
                showConfirmButton: false
            });
        }
    </script>
@endpush
