@extends('layouts.app', ['pageTitle' => 'Detail Remidi'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Remidi</h3>
            <div class="card-tools">
                <a href="{{ route('siswa.remidi.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Status Badge -->
            <div class="text-center mb-4">
                @if ($remidi->status_remidi == 'pending')
                    <span class="badge badge-warning p-3" style="font-size: 1.2rem;">
                        <i class="fas fa-clock"></i> STATUS: PENDING
                    </span>
                @elseif($remidi->status_remidi == 'selesai')
                    <span class="badge badge-success p-3" style="font-size: 1.2rem;">
                        <i class="fas fa-check-circle"></i> STATUS: SELESAI
                    </span>
                @else
                    <span class="badge badge-secondary p-3" style="font-size: 1.2rem;">
                        <i class="fas fa-times-circle"></i> STATUS: BATAL
                    </span>
                @endif
            </div>

            <div class="row">
                <!-- Informasi Mata Pelajaran -->
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-book"></i> Informasi Mata Pelajaran</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Mata Pelajaran</strong></td>
                                    <td>: {{ $remidi->guruKelas->guruMapel->mapel->nama_mapel }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guru Pengampu</strong></td>
                                    <td>: {{ $remidi->guruKelas->guruMapel->guru->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Ujian</strong></td>
                                    <td>: {{ $remidi->jenisUjian->nama_jenis_ujian }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Semester</strong></td>
                                    <td>:
                                        <span class="badge badge-{{ $remidi->semester == 'ganjil' ? 'info' : 'success' }}">
                                            {{ ucfirst($remidi->semester) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas</strong></td>
                                    <td>: {{ $remidi->kelas->nama_kelas }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Informasi Nilai -->
                <div class="col-md-6">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-chart-line"></i> Informasi Nilai</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nilai Asli</strong></td>
                                    <td>:
                                        <span class="badge badge-danger" style="font-size: 1.1rem;">
                                            {{ $remidi->nilai_asli }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>KKM</strong></td>
                                    <td>:
                                        <span class="badge badge-info" style="font-size: 1.1rem;">
                                            {{ $remidi->kkm }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Nilai Remidi</strong></td>
                                    <td>:
                                        @if ($remidi->nilai_remidi)
                                            <span
                                                class="badge badge-{{ $remidi->nilai_remidi >= $remidi->kkm ? 'success' : 'warning' }}"
                                                style="font-size: 1.1rem;">
                                                {{ $remidi->nilai_remidi }}
                                            </span>
                                        @else
                                            <span class="text-muted">Belum dilakukan remidi</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Nilai Akhir</strong></td>
                                    <td>:
                                        <span
                                            class="badge badge-{{ $remidi->nilai_akhir >= $remidi->kkm ? 'success' : 'danger' }}"
                                            style="font-size: 1.2rem;">
                                            {{ $remidi->nilai_akhir }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Selisih dari KKM</strong></td>
                                    <td>:
                                        @php
                                            $selisih = $remidi->nilai_akhir - $remidi->kkm;
                                        @endphp
                                        <span class="badge badge-{{ $selisih >= 0 ? 'success' : 'danger' }}">
                                            {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Tanggal -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-info-circle"></i> Detail Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="180"><strong>Tanggal Remidi</strong></td>
                                            <td>:
                                                @if ($remidi->tanggal_remidi)
                                                    {{ $remidi->tanggal_remidi->format('d F Y, H:i') }} WIB
                                                @else
                                                    <span class="text-muted">Belum ada jadwal</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Remidi</strong></td>
                                            <td>:
                                                @if ($remidi->status_remidi == 'pending')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> Menunggu Remidi
                                                    </span>
                                                @elseif($remidi->status_remidi == 'selesai')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Selesai - Tuntas
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-times"></i> Dibatalkan
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="180"><strong>Dibuat Tanggal</strong></td>
                                            <td>: {{ $remidi->created_at->format('d F Y, H:i') }} WIB</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Terakhir Update</strong></td>
                                            <td>: {{ $remidi->updated_at->format('d F Y, H:i') }} WIB</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            @if ($remidi->keterangan)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-comment"></i> Keterangan dari Guru</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $remidi->keterangan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Alert Messages -->
            @if ($remidi->status_remidi == 'pending')
                @if (!$remidi->nilai_remidi)
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Perhatian</h5>
                        <p>Anda perlu melakukan remidi untuk ujian ini. Silakan hubungi guru pengampu untuk mengetahui
                            jadwal remidi.</p>
                        <ul class="mb-0">
                            <li>Mata Pelajaran: <strong>{{ $remidi->guruKelas->guruMapel->mapel->nama_mapel }}</strong>
                            </li>
                            <li>Guru: <strong>{{ $remidi->guruKelas->guruMapel->guru->nama ?? '-' }}</strong></li>
                            <li>Nilai Anda: <strong>{{ $remidi->nilai_asli }}</strong> (KKM: {{ $remidi->kkm }})</li>
                        </ul>
                    </div>
                @else
                    @if ($remidi->nilai_remidi < $remidi->kkm)
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle"></i> Remidi Ulang Diperlukan</h5>
                            <p>Nilai remidi Anda ({{ $remidi->nilai_remidi }}) masih di bawah KKM ({{ $remidi->kkm }}).
                                Anda perlu melakukan remidi ulang.</p>
                        </div>
                    @endif
                @endif
            @elseif($remidi->status_remidi == 'selesai')
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Selamat!</h5>
                    <p class="mb-0">Anda telah berhasil menyelesaikan remidi dan mencapai nilai di atas KKM.</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                @if ($remidi->status_remidi == 'pending' && $remidi->is_notified)
                    <button type="button" class="btn btn-info" id="btnMarkAsRead">
                        <i class="fas fa-check"></i> Tandai Sudah Dibaca
                    </button>
                @endif
                <a href="{{ route('siswa.remidi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Lihat Semua Remidi
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-outline {
            border-top: 3px solid;
        }

        .table td {
            padding: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#btnMarkAsRead').on('click', function() {
                $.ajax({
                    url: '{{ route('siswa.remidi.mark-as-read', $remidi->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            }).then(() => {
                                $('#btnMarkAsRead').remove();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan',
                        });
                    }
                });
            });
        });
    </script>
@endpush
