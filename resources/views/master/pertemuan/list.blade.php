@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Daftar Pertemuan</h4>
                        <a href="{{ route('admin.pertemuan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Tahun Akademik:</label>
                                    <select name="tahun_akademik_id" class="form-control form-control-sm">
                                        @foreach ($tahunAkademikList as $ta)
                                            <option value="{{ $ta->id }}"
                                                {{ $tahunAkademikId == $ta->id ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun_akademik }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Status:</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">Semua Status</option>
                                        <option value="scheduled" {{ $status == 'scheduled' ? 'selected' : '' }}>Terjadwal
                                        </option>
                                        <option value="ongoing" {{ $status == 'ongoing' ? 'selected' : '' }}>Sedang
                                            Berlangsung</option>
                                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai
                                        </option>
                                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Dari Tanggal:</label>
                                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                        value="{{ $tanggalMulai }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Sampai Tanggal:</label>
                                    <input type="date" name="tanggal_selesai" class="form-control form-control-sm"
                                        value="{{ $tanggalSelesai }}">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block btn-sm">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('admin.pertemuan.list', ['tahun_akademik_id' => $tahunAkademikId]) }}"
                                        class="btn btn-secondary btn-block btn-sm">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="3%">No</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="8%">Hari</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th>Kelas</th>
                                        <th width="8%">Pertemuan Ke</th>
                                        <th width="12%">Status</th>
                                        <th width="8%">Auto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pertemuanList as $key => $pertemuan)
                                        @php
                                            $jadwal = $pertemuan->jadwalPelajaran;
                                            $guruKelas = $jadwal->guruKelas;
                                        @endphp
                                        <tr>
                                            <td>{{ $pertemuanList->firstItem() + $key }}</td>
                                            <td>{{ $pertemuan->tanggal->format('d/m/Y') }}</td>
                                            <td>{{ $pertemuan->tanggal->locale('id')->isoFormat('dddd') }}</td>
                                            <td>{{ $guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                            <td>{{ $guruKelas->guruMapel->guru->nama ?? '-' }}</td>
                                            <td>{{ $guruKelas->kelas->nama_lengkap ?? '-' }}</td>
                                            <td class="text-center">{{ $pertemuan->pertemuan_ke }}</td>
                                            <td>
                                                @if ($pertemuan->status == 'scheduled')
                                                    <span class="badge badge-secondary">Terjadwal</span>
                                                @elseif($pertemuan->status == 'ongoing')
                                                    <span class="badge badge-info">Berlangsung</span>
                                                @elseif($pertemuan->status == 'completed')
                                                    <span class="badge badge-success">Selesai</span>
                                                @else
                                                    <span class="badge badge-danger">Dibatalkan</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($pertemuan->generated_auto)
                                                    <i class="fas fa-robot text-success" title="Auto Generated"></i>
                                                @else
                                                    <i class="fas fa-user text-primary" title="Manual"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data pertemuan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pertemuanList->appends(request()->query())->links() }}
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>Keterangan:</strong><br>
                            <i class="fas fa-robot text-success"></i> = Pertemuan di-generate otomatis oleh sistem<br>
                            <i class="fas fa-user text-primary"></i> = Pertemuan dibuat manual
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
