@extends('layouts.app', ['pageTitle' => 'Kelola KKM'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kelola Kriteria Ketuntasan Minimal (KKM)</h3>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form action="{{ route('guru.kkm.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <label for="tahun_akademik_id">Tahun Akademik</label>
                        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-control"
                            onchange="this.form.submit()">
                            @foreach ($tahunAkademiks as $ta)
                                <option value="{{ $ta->id }}"
                                    {{ $selectedTahunAkademik == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_akademik }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-chalkboard"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Kelas Diampu</span>
                            <span class="info-box-number">{{ $statistik['total_kelas'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">KKM Sudah Diset</span>
                            <span class="info-box-number">{{ $statistik['total_kkm_set'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Kelas -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>KKM Diset</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelasList as $index => $kelas)
                            @php
                                $guruKelas = $statistik['guru_kelas_list']->where('kelas_id', $kelas->id)->first();
                                $kkmCount = $guruKelas ? $guruKelas->kkmMapel->count() : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $kelas->nama_lengkap }}</strong></td>
                                <td>{{ $guruKelas->guruMapel->mapel->nama_mapel ?? '-' }}</td>
                                <td>
                                    @if ($kkmCount > 0)
                                        <span class="badge badge-success">{{ $kkmCount }} KKM</span>
                                    @else
                                        <span class="badge badge-warning">Belum diset</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($guruKelas)
                                        <a href="{{ route('guru.kkm.create', ['guru_kelas_id' => $guruKelas->id]) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> {{ $kkmCount > 0 ? 'Edit' : 'Set' }} KKM
                                        </a>
                                        @if ($kkmCount > 0)
                                            <a href="{{ route('guru.kkm.show', $guruKelas->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data kelas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .info-box {
            min-height: 80px;
            border-radius: 5px;
        }
    </style>
@endpush
