@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Rekap Absensi Per Kelas</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Tahun Akademik <span class="text-danger">*</span></label>
                                    <select name="tahun_akademik_id" class="form-control" required>
                                        <option value="">Pilih Tahun Akademik</option>
                                        @foreach ($tahunAkademikList as $ta)
                                            <option value="{{ $ta->id }}"
                                                {{ $tahunAkademikId == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun_akademik }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Kelas <span class="text-danger">*</span></label>
                                    <select name="kelas_id" class="form-control" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}"
                                                {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Mata Pelajaran (Opsional)</label>
                                    <select name="mapel_id" class="form-control">
                                        <option value="">Semua Mapel</option>
                                        @foreach ($mapelList as $mapel)
                                            <option value="{{ $mapel->id }}"
                                                {{ $mapelId == $mapel->id ? 'selected' : '' }}>
                                                {{ $mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Tampilkan
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($rekap)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="12%">NIS</th>
                                            <th>Nama Siswa</th>
                                            <th width="8%" class="text-center">Hadir</th>
                                            <th width="8%" class="text-center">Izin</th>
                                            <th width="8%" class="text-center">Sakit</th>
                                            <th width="8%" class="text-center">Alpha</th>
                                            <th width="8%" class="text-center">Total</th>
                                            <th width="10%" class="text-center">Persentase</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rekap as $key => $data)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $data->nis }}</td>
                                                <td>{{ $data->nama_siswa }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">{{ $data->hadir }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-warning">{{ $data->izin }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">{{ $data->sakit }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-danger">{{ $data->alpha }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ $data->total_pertemuan }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @if ($data->persentase_hadir >= 75)
                                                        <span
                                                            class="badge badge-success badge-lg">{{ $data->persentase_hadir }}%</span>
                                                    @elseif($data->persentase_hadir >= 50)
                                                        <span
                                                            class="badge badge-warning badge-lg">{{ $data->persentase_hadir }}%</span>
                                                    @else
                                                        <span
                                                            class="badge badge-danger badge-lg">{{ $data->persentase_hadir }}%</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.rekap.per-siswa', ['siswa' => $data->siswa_id, 'tahun_akademik_id' => $tahunAkademikId, 'mapel_id' => $mapelId]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($rekap->count() > 0)
                                <div class="mt-3">
                                    <button onclick="window.print()" class="btn btn-secondary">
                                        <i class="fas fa-print"></i> Cetak
                                    </button>
                                    {{-- <a href="{{ route('rekap.per-kelas.export', request()->query()) }}"
                                        class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </a> --}}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Silakan pilih tahun akademik dan kelas untuk menampilkan
                                rekap absensi
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
