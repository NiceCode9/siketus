@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Detail Absensi Siswa</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">NIS</th>
                                        <td>{{ $siswa->nis }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $siswa->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kelas</th>
                                        <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Ringkasan Kehadiran</h5>
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <h3 class="text-success">{{ $ringkasan->hadir }}</h3>
                                                <small>Hadir</small>
                                            </div>
                                            <div class="col-3">
                                                <h3 class="text-warning">{{ $ringkasan->izin }}</h3>
                                                <small>Izin</small>
                                            </div>
                                            <div class="col-3">
                                                <h3 class="text-info">{{ $ringkasan->sakit }}</h3>
                                                <small>Sakit</small>
                                            </div>
                                            <div class="col-3">
                                                <h3 class="text-danger">{{ $ringkasan->alpha }}</h3>
                                                <small>Alpha</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="text-center">
                                            Persentase Kehadiran:
                                            <span
                                                class="badge badge-{{ $ringkasan->persentase_hadir >= 75 ? 'success' : ($ringkasan->persentase_hadir >= 50 ? 'warning' : 'danger') }} badge-lg">
                                                {{ $ringkasan->persentase_hadir }}%
                                            </span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="GET" class="mb-3">
                            <input type="hidden" name="siswa" value="{{ $siswa->id }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Tahun Akademik</label>
                                    <select name="tahun_akademik_id" class="form-control" onchange="this.form.submit()">
                                        @foreach ($tahunAkademikList as $ta)
                                            <option value="{{ $ta->id }}"
                                                {{ $tahunAkademikId == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun_akademik }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Filter Mata Pelajaran</label>
                                    <select name="mapel_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Mapel</option>
                                        @foreach ($mapelList as $mapel)
                                            <option value="{{ $mapel->id }}"
                                                {{ $mapelId == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>

                        <h5 class="mb-3">Riwayat Absensi</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Tanggal</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th width="8%">Pertemuan</th>
                                        <th width="12%">Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($absensiList as $key => $absensi)
                                        @php
                                            $pertemuan = $absensi->pertemuan;
                                            $guruKelas = $pertemuan->jadwalPelajaran->guruKelas;
                                        @endphp
                                        <tr>
                                            <td>{{ $absensiList->firstItem() + $key }}</td>
                                            <td>{{ $pertemuan->tanggal->format('d/m/Y') }}</td>
                                            <td>{{ $guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                            <td>{{ $guruKelas->guruMapel->guru->nama ?? '-' }}</td>
                                            <td class="text-center">{{ $pertemuan->pertemuan_ke }}</td>
                                            <td>
                                                <span class="badge badge-{{ $absensi->badge_color }}">
                                                    {{ ucfirst($absensi->status_kehadiran) }}
                                                </span>
                                            </td>
                                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data absensi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $absensiList->appends(request()->query())->links() }}

                        <div class="mt-3">
                            <a href="{{ route('admin.rekap.per-kelas', ['kelas_id' => $siswa->kelas_id, 'tahun_akademik_id' => $tahunAkademikId]) }}"
                                class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Rekap Kelas
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
