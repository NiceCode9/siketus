@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    @if (session('error'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="card-header">
                        <h4 class="mb-0">Form Absensi</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Info Pertemuan:</strong><br>
                            Mata Pelajaran: {{ $pertemuan->jadwalPelajaran->guruKelas->guruMapel->mapel->nama_mapel }}<br>
                            Kelas: {{ $pertemuan->jadwalPelajaran->guruKelas->kelas->nama_kelas }}<br>
                            Tanggal: {{ $pertemuan->tanggal->format('d/m/Y') }}<br>
                            Pertemuan Ke: {{ $pertemuan->pertemuan_ke }}
                        </div>

                        <form action="{{ route('guru.absensi.store', $pertemuan->id) }}" method="POST" id="formAbsensi">
                            @csrf

                            <div class="form-group">
                                <label>Materi Pembelajaran</label>
                                <textarea name="materi" class="form-control" rows="3" placeholder="Tulis materi yang diajarkan...">{{ $pertemuan->materi }}</textarea>
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-success" onclick="setSemuaHadir()">
                                    <i class="fas fa-check-double"></i> Semua Hadir
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="setSemuaAlpha()">
                                    <i class="fas fa-times"></i> Semua Alpha
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">NIS</th>
                                            <th>Nama Siswa</th>
                                            <th width="15%">Status</th>
                                            <th width="30%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($siswaList as $key => $siswa)
                                            @php
                                                $absensi = $absensiData->get($siswa->id);
                                                $status = $absensi ? $absensi->status_kehadiran : 'hadir';
                                                $keterangan = $absensi ? $absensi->keterangan : '';
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $siswa->nisn }}</td>
                                                <td>{{ $siswa->nama }}</td>
                                                <td>
                                                    <input type="hidden" name="absensi[{{ $key }}][siswa_id]"
                                                        value="{{ $siswa->id }}">
                                                    <select name="absensi[{{ $key }}][status_kehadiran]"
                                                        class="form-control form-control-sm status-select">
                                                        <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>
                                                            Hadir</option>
                                                        <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>
                                                            Izin</option>
                                                        <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>
                                                            Sakit</option>
                                                        <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }}>
                                                            Alpha</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="absensi[{{ $key }}][keterangan]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Keterangan (opsional)" value="{{ $keterangan }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Absensi
                                </button>
                                <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setSemuaHadir() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.value = 'hadir';
            });
        }

        function setSemuaAlpha() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.value = 'alpha';
            });
        }
    </script>
@endsection
