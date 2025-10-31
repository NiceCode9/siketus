@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kenaikan Kelas</h3>
                        <div class="card-tools">
                            @if ($tahunAkademikAktif)
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#modalKenaikanMassal">
                                    <i class="fas fa-users"></i> Kenaikan Massal
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (!$tahunAkademikAktif)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Tidak ada tahun akademik aktif. Silakan aktifkan
                                tahun akademik terlebih dahulu.
                            </div>
                        @else
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Tahun Akademik Aktif</span>
                                            <span
                                                class="info-box-number">{{ $tahunAkademikAktif->nama_tahun_akademik }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Kelas -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Pilih Kelas</h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('admin.kenaikan-kelas.index') }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kelas_id">Kelas</label>
                                                    <select name="kelas_id" id="kelas_id" class="form-control" required>
                                                        <option value="">-- Pilih Kelas --</option>
                                                        @foreach ($kelasList as $kelas)
                                                            <option value="{{ $kelas->id }}"
                                                                {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                                                {{ $kelas->nama_lengkap }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fas fa-search"></i> Tampilkan Siswa
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Daftar Siswa -->
                            @if ($kelasSelected)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            Siswa Kelas {{ $kelasSelected->nama_lengkap }}
                                            <span class="badge badge-primary ml-2">{{ $siswaList->count() }} Siswa</span>
                                        </h5>
                                        <div class="card-tools">
                                            @if ($siswaList->count() > 0)
                                                <a href="{{ route('admin.kenaikan-kelas.create', ['kelas_asal_id' => $kelasSelected->id]) }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-arrow-up"></i> Proses Kenaikan Kelas
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($siswaList->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No</th>
                                                            <th>NISN</th>
                                                            <th>Nama Siswa</th>
                                                            <th>Kelas Sekarang</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($siswaList as $index => $siswa)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $siswa->nisn }}</td>
                                                                <td>{{ $siswa->nama }}</td>
                                                                <td>
                                                                    @if ($siswa->currentClass)
                                                                        <span class="badge badge-info">
                                                                            {{ $siswa->currentClass->nama_lengkap }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge badge-secondary">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-success">
                                                                        {{ ucfirst($siswa->status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> Tidak ada siswa di kelas ini.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kenaikan Massal -->
    <div class="modal fade" id="modalKenaikanMassal" tabindex="-1" role="dialog"
        aria-labelledby="modalKenaikanMassalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.kenaikan-kelas.naikkan-massal') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalKenaikanMassalLabel">Kenaikan Kelas Massal</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian!</strong> Fitur ini akan menaikkan <strong>SEMUA siswa aktif</strong> ke
                            tingkat berikutnya secara otomatis.
                        </div>

                        <div class="form-group">
                            <label for="tahun_akademik_baru_id">Tahun Akademik Tujuan <span
                                    class="text-danger">*</span></label>
                            <select name="tahun_akademik_baru_id" id="tahun_akademik_baru_id" class="form-control"
                                required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach ($tahunAkademikList as $ta)
                                    @if ($ta->id != $tahunAkademikAktif->id)
                                        <option value="{{ $ta->id }}">
                                            {{ $ta->nama_tahun_akademik }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih tahun akademik untuk tahun ajaran baru</small>
                        </div>

                        <div class="alert alert-info">
                            <strong>Catatan:</strong>
                            <ul class="mb-0">
                                <li>Siswa akan naik ke tingkat berikutnya di jurusan yang sama</li>
                                <li>Siswa tingkat tertinggi akan otomatis berstatus lulus</li>
                                <li>Proses ini tidak dapat dibatalkan</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Apakah Anda yakin ingin melakukan kenaikan kelas massal? Proses ini tidak dapat dibatalkan.')">
                            <i class="fas fa-check"></i> Proses Kenaikan Massal
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
            // Auto hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
