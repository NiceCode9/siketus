@extends('layouts.app', ['pageTitle' => 'Daftar Remidi'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Siswa Remidi</h3>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form action="{{ route('guru.remidi.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <label>Tahun Akademik</label>
                        <select name="tahun_akademik_id" class="form-control" onchange="this.form.submit()">
                            @foreach ($tahunAkademiks as $ta)
                                <option value="{{ $ta->id }}"
                                    {{ $selectedTahunAkademik == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_akademik }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $selectedKelas == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Mata Pelajaran</label>
                        <select name="mapel_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($mapelList as $mapel)
                                <option value="{{ $mapel->id }}" {{ $selectedMapel == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="selesai" {{ $selectedStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ $selectedStatus == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                </div>
            </form>

            @if ($statistik)
                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Remidi</span>
                                <span class="info-box-number">{{ $statistik['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pending</span>
                                <span class="info-box-number">{{ $statistik['pending'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Selesai</span>
                                <span class="info-box-number">{{ $statistik['selesai'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Siswa Remidi</span>
                                <span class="info-box-number">{{ $statistik['siswa_remidi'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Ujian</th>
                            <th>Nilai Asli</th>
                            <th>KKM</th>
                            <th>Nilai Remidi</th>
                            <th>Nilai Akhir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($remidiList as $index => $remidi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $remidi->siswa->nama }}</strong></td>
                                <td>{{ $remidi->jenisUjian->nama_jenis_ujian }}</td>
                                <td>
                                    <span class="badge badge-danger">{{ $remidi->nilai_asli }}</span>
                                </td>
                                <td>{{ $remidi->kkm }}</td>
                                <td>
                                    @if ($remidi->nilai_remidi)
                                        <span
                                            class="badge badge-{{ $remidi->nilai_remidi >= $remidi->kkm ? 'success' : 'warning' }}">
                                            {{ $remidi->nilai_remidi }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-{{ $remidi->nilai_akhir >= $remidi->kkm ? 'success' : 'danger' }}">
                                        {{ $remidi->nilai_akhir }}
                                    </strong>
                                </td>
                                <td>
                                    @if ($remidi->status_remidi == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($remidi->status_remidi == 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-secondary">Batal</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($remidi->status_remidi == 'pending')
                                        <button type="button" class="btn btn-sm btn-primary btn-input-nilai"
                                            data-id="{{ $remidi->id }}" data-siswa="{{ $remidi->siswa->nama }}"
                                            data-ujian="{{ $remidi->jenisUjian->nama_jenis_ujian }}"
                                            data-kkm="{{ $remidi->kkm }}">
                                            <i class="fas fa-edit"></i> Input Nilai
                                        </button>
                                    @endif
                                    <a href="{{ route('guru.remidi.show', $remidi->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    @if ($selectedKelas && $selectedMapel)
                                        Tidak ada data remidi
                                    @else
                                        Silakan pilih kelas dan mata pelajaran
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Input Nilai Remidi -->
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
                        <input type="hidden" id="remidi_id">
                        <div class="form-group">
                            <label>Nama Siswa</label>
                            <input type="text" class="form-control" id="nama_siswa" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jenis Ujian</label>
                            <input type="text" class="form-control" id="jenis_ujian" readonly>
                        </div>
                        <div class="form-group">
                            <label>KKM</label>
                            <input type="text" class="form-control" id="kkm" readonly>
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
            // Open modal
            $('.btn-input-nilai').on('click', function() {
                var id = $(this).data('id');
                var siswa = $(this).data('siswa');
                var ujian = $(this).data('ujian');
                var kkm = $(this).data('kkm');

                $('#remidi_id').val(id);
                $('#nama_siswa').val(siswa);
                $('#jenis_ujian').val(ujian);
                $('#kkm').val(kkm);
                $('#nilai_remidi').val('');
                $('#keterangan').val('');

                $('#modalInputNilai').modal('show');
            });

            // Submit form
            $('#formInputNilai').on('submit', function(e) {
                e.preventDefault();

                var remidiId = $('#remidi_id').val();
                var nilaiRemidi = $('#nilai_remidi').val();
                var keterangan = $('#keterangan').val();

                $.ajax({
                    url: '{{ route('guru.remidi.input-nilai', ':id') }}'.replace(':id', remidiId),
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
