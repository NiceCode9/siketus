@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Absensi Harian</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Tanggal:</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}"
                                    onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Waktu</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Pertemuan Ke</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pertemuanList as $key => $pertemuan)
                                    @php
                                        $jadwal = $pertemuan->jadwalPelajaran;
                                        $guruKelas = $jadwal->guruKelas;
                                        $mapel = $guruKelas->guruMapel->mapel;
                                        $kelas = $guruKelas->kelas;
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $jadwal->jam_mulai->format('H:i') }} -
                                            {{ $jadwal->jam_selesai->format('H:i') }}</td>
                                        <td>{{ $mapel->nama_mapel }}</td>
                                        <td>{{ $kelas->nama_lengkap }}</td>
                                        <td>{{ $pertemuan->pertemuan_ke }}</td>
                                        <td>
                                            @if ($pertemuan->status == 'scheduled')
                                                <span class="badge badge-secondary">Terjadwal</span>
                                            @elseif($pertemuan->status == 'ongoing')
                                                <span class="badge badge-info">Sedang Berlangsung</span>
                                            @elseif($pertemuan->status == 'completed')
                                                <span class="badge badge-success">Selesai</span>
                                            @else
                                                <span class="badge badge-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($pertemuan->status == 'completed')
                                                <a href="{{ route('admin.absensi.edit', $pertemuan->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit Absensi
                                                </a>
                                            @else
                                                <a href="{{ route('admin.absensi.create', $pertemuan->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-check"></i> Mulai Absensi
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada jadwal mengajar hari ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
