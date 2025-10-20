@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Management Pertemuan</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Pilih Tahun Akademik:</label>
                                    <select name="tahun_akademik_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Pilih Tahun Akademik</option>
                                        @foreach ($tahunAkademikList as $ta)
                                            <option value="{{ $ta->id }}"
                                                {{ $tahunAkademikId == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun_akademik }}
                                                @if ($ta->status_aktif)
                                                    <span class="badge badge-success">Aktif</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>

                        @if ($tahunAkademikId)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">Statistik Pertemuan</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body">
                                                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                                                    <p class="mb-0">Total Pertemuan</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-secondary text-white">
                                                <div class="card-body">
                                                    <h3>{{ $stats['scheduled'] ?? 0 }}</h3>
                                                    <p class="mb-0">Terjadwal</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h3>{{ $stats['completed'] ?? 0 }}</h3>
                                                    <p class="mb-0">Selesai</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body">
                                                    <h3>{{ $stats['cancelled'] ?? 0 }}</h3>
                                                    <p class="mb-0">Dibatalkan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Generate Pertemuan
                                                Otomatis</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-3">
                                                <i class="fas fa-info-circle text-info"></i>
                                                Sistem akan membuat jadwal pertemuan untuk seluruh semester berdasarkan:
                                            </p>
                                            <ul>
                                                <li>Jadwal pelajaran yang sudah dibuat</li>
                                                <li>Kalender akademik (hari libur akan di-skip otomatis)</li>
                                                <li>Periode tahun akademik yang dipilih</li>
                                            </ul>

                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Perhatian:</strong> Pastikan jadwal pelajaran dan kalender libur
                                                sudah lengkap sebelum generate!
                                            </div>

                                            <form action="{{ route('admin.pertemuan.generate') }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin generate pertemuan? Proses ini mungkin memakan waktu beberapa menit.')">
                                                @csrf
                                                <input type="hidden" name="tahun_akademik_id"
                                                    value="{{ $tahunAkademikId }}">

                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-cogs"></i> Generate Pertemuan
                                                </button>

                                                <a href="{{ route('admin.pertemuan.list', ['tahun_akademik_id' => $tahunAkademikId]) }}"
                                                    class="btn btn-info btn-lg">
                                                    <i class="fas fa-list"></i> Lihat Daftar Pertemuan
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h5 class="mb-0"><i class="fas fa-trash-restore"></i> Reset Pertemuan</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-3">
                                                <i class="fas fa-info-circle text-warning"></i>
                                                Menghapus semua pertemuan yang <strong>belum diabsen</strong> (status:
                                                Terjadwal) untuk tahun akademik ini.
                                            </p>

                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Peringatan:</strong> Pertemuan yang sudah diabsen TIDAK akan
                                                dihapus. Gunakan fitur ini jika ada kesalahan saat generate.
                                            </div>

                                            <form action="{{ route('admin.pertemuan.reset') }}" method="POST"
                                                onsubmit="return confirm('YAKIN ingin menghapus semua pertemuan yang belum diabsen? Tindakan ini tidak dapat dibatalkan!')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tahun_akademik_id"
                                                    value="{{ $tahunAkademikId }}">

                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i> Reset Pertemuan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Silakan pilih tahun akademik terlebih dahulu.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
