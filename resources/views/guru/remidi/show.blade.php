@extends('layouts.app', ['pageTitle' => 'Detail Remidi'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Remidi Siswa</h3>
            <div class="card-tools">
                <a href="{{ route('guru.remidi.index') }}" class="btn btn-sm btn-secondary">
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
                <!-- Informasi Siswa -->
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-user"></i> Informasi Siswa</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Siswa</strong></td>
                                    <td>: {{ $remidi->siswa->nama }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NISN</strong></td>
                                    <td>: {{ $remidi->siswa->nisn }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas</strong></td>
                                    <td>: {{ $remidi->kelas->nama_kelas }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Informasi Ujian -->
                <div class="col-md-6">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-book"></i> Informasi Ujian</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Mata Pelajaran</strong></td>
                                    <td>: {{ $remidi->guruKelas->guruMapel->mapel->nama_mapel }}</td>
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
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Nilai -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-chart-line"></i> Detail Nilai</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="info-box bg-danger">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Nilai Asli</span>
                                            <span class="info-box-number" style="font-size: 2rem;">
                                                {{ $remidi->nilai_asli }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <div class="info-box-content">
                                            <span class="info-box-text">KKM</span>
                                            <span class="info-box-number" style="font-size: 2rem;">
                                                {{ $remidi->kkm }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div
                                        class="info-box bg-{{ $remidi->nilai_remidi ? ($remidi->nilai_remidi >= $remidi->kkm ? 'success' : 'warning') : 'secondary' }}">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Nilai Remidi</span>
                                            <span class="info-box-number" style="font-size: 2rem;">
                                                {{ $remidi->nilai_remidi ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div
                                        class="info-box bg-{{ $remidi->nilai_akhir >= $remidi->kkm ? 'success' : 'danger' }}">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Nilai Akhir</span>
                                            <span class="info-box-number" style="font-size: 2rem;">
                                                {{ $remidi->nilai_akhir }}
                                            </span>
                                        </div>
                                    </div>
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
                                <h5 class="card-title"><i class="fas fa-comment"></i> Keterangan</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $remidi->keterangan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Riwayat -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default card-outline">
                        <div class="card-header">
                            <h5 class="card-title"><i class="fas fa-history"></i> Riwayat</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Dibuat Tanggal</strong></td>
                                    <td>: {{ $remidi->created_at->format('d F Y, H:i') }} WIB</td>
                                </tr>
                                @if ($remidi->tanggal_remidi)
                                    <tr>
                                        <td><strong>Tanggal Input Nilai Remidi</strong></td>
                                        <td>: {{ $remidi->tanggal_remidi->format('d F Y, H:i') }} WIB</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Terakhir Diupdate</strong></td>
                                    <td>: {{ $remidi->updated_at->format('d F Y, H:i') }} WIB</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                @if ($remidi->status_remidi == 'pending')
                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal"
                        data-target="#modalInputNilai">
                        <i class="fas fa-edit"></i> Input Nilai Remidi
                    </button>
                @endif
                <a href="{{ route('guru.remidi.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-list"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Input Nilai -->
    <div class="modal fade" id="modalInputNilai" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Input Nilai Remidi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formInputNilai">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Siswa:</strong> {{ $remidi->siswa->nama }}<br>
                            <strong>Nilai Asli:</strong> {{ $remidi->nilai_asli }}<br>
                            <strong>KKM:</strong> {{ $remidi->kkm }}
                        </div>
                        <div class="form-group">
                            <label>Nilai Remidi <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="nilai_remidi" min="0" max="100"
                                step="0.01" required>
                            <small class="form-text text-muted">Nilai antara 0-100</small>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control" id="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#formInputNilai').on('submit', function(e) {
                e.preventDefault();

                var nilaiRemidi = $('#nilai_remidi').val();
                var keterangan = $('#keterangan').val();

                $.ajax({
                    url: '{{ route('guru.remidi.input-nilai', $remidi->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nilai_remidi: nilaiRemidi,
                        keterangan: keterangan
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        });
                    }
                });
            });
        });
    </script>
@endpush
