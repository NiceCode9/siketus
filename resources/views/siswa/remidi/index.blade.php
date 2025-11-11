@extends('layouts.app', ['pageTitle' => 'Riwayat Remidi'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Remidi Saya</h3>
        </div>
        <div class="card-body">
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
                    <div class="info-box bg-secondary">
                        <span class="info-box-icon"><i class="fas fa-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Batal</span>
                            <span class="info-box-number">{{ $statistik['batal'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <form action="{{ route('siswa.remidi.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="selesai" {{ $statusFilter == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ $statusFilter == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Remidi Pending -->
            @if ($remidiPending->count() > 0)
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Remidi Pending ({{ $remidiPending->count() }})</h5>
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="bg-warning">
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Jenis Ujian</th>
                                <th>Nilai Asli</th>
                                <th>KKM</th>
                                <th>Nilai Remidi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($remidiPending as $index => $remidi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $remidi->guruKelas->guruMapel->mapel->nama_mapel }}</strong></td>
                                    <td>{{ $remidi->jenisUjian->nama_jenis_ujian }}</td>
                                    <td><span class="badge badge-danger">{{ $remidi->nilai_asli }}</span></td>
                                    <td>{{ $remidi->kkm }}</td>
                                    <td>
                                        @if ($remidi->nilai_remidi)
                                            <span
                                                class="badge badge-{{ $remidi->nilai_remidi >= $remidi->kkm ? 'success' : 'warning' }}">
                                                {{ $remidi->nilai_remidi }}
                                            </span>
                                            @if ($remidi->nilai_remidi < $remidi->kkm)
                                                <br><small class="text-danger">Perlu remidi ulang</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum remidi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Menunggu
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('siswa.remidi.show', $remidi->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Remidi Selesai -->
            @if ($remidiSelesai->count() > 0)
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Remidi Selesai ({{ $remidiSelesai->count() }})</h5>
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="bg-success">
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Jenis Ujian</th>
                                <th>Nilai Asli</th>
                                <th>KKM</th>
                                <th>Nilai Remidi</th>
                                <th>Nilai Akhir</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($remidiSelesai as $index => $remidi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $remidi->guruKelas->guruMapel->mapel->nama_mapel }}</strong></td>
                                    <td>{{ $remidi->jenisUjian->nama_jenis_ujian }}</td>
                                    <td><span class="badge badge-danger">{{ $remidi->nilai_asli }}</span></td>
                                    <td>{{ $remidi->kkm }}</td>
                                    <td><span class="badge badge-info">{{ $remidi->nilai_remidi }}</span></td>
                                    <td><span class="badge badge-success">{{ $remidi->nilai_akhir }}</span></td>
                                    <td>{{ $remidi->tanggal_remidi ? $remidi->tanggal_remidi->format('d/m/Y') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($remidiList->count() == 0)
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>Tidak ada data remidi</h5>
                    <p>Anda tidak memiliki riwayat remidi</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .info-box {
            min-height: 80px;
        }
    </style>
@endpush
