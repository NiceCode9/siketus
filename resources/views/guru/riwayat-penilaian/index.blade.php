@extends('layouts.app', ['pageTitle' => 'Riwayat Penilaian'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Riwayat Penilaian</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('guru.riwayat-penilaian.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tahun_akademik_id">Tahun Akademik <span class="text-danger">*</span></label>
                            <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-control" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach ($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ $selectedTahunAkademik == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama_tahun_akademik }} {{ $ta->status_aktif ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="kelas_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}"
                                        {{ $selectedKelas == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="semester">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="semester" class="form-control" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="ganjil" {{ $selectedSemester == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="genap" {{ $selectedSemester == 'genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kategori">Kategori Penilaian <span class="text-danger">*</span></label>
                            <select name="kategori" id="kategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="mapel" {{ $selectedKategori == 'mapel' ? 'selected' : '' }}>Mata Pelajaran
                                </option>
                                <option value="kedisiplinan" {{ $selectedKategori == 'kedisiplinan' ? 'selected' : '' }}>
                                    Kedisiplinan</option>
                                <option value="keagamaan" {{ $selectedKategori == 'keagamaan' ? 'selected' : '' }}>Kegiatan
                                    Keagamaan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="{{ route('guru.riwayat-penilaian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!empty($riwayatData) && count($riwayatData) > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Riwayat Penilaian -
                    @if ($selectedKategori == 'mapel')
                        <span class="badge badge-primary">Mata Pelajaran</span>
                    @elseif($selectedKategori == 'kedisiplinan')
                        <span class="badge badge-warning">Kedisiplinan</span>
                    @elseif($selectedKategori == 'keagamaan')
                        <span class="badge badge-success">Kegiatan Keagamaan</span>
                    @endif
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th width="120" class="text-center">Jumlah Nilai</th>
                            <th width="120" class="text-center">Rata-rata</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($riwayatData as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item['siswa']->nisn }}</td>
                                <td>{{ $item['siswa']->nama }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $item['jumlah_nilai'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($item['rata_rata']) }}">
                                        {{ number_format($item['rata_rata'], 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('guru.riwayat-penilaian.detail', [
                                        'siswa' => $item['siswa']->id,
                                        'tahun_akademik_id' => $selectedTahunAkademik,
                                        'semester' => $selectedSemester,
                                        'kategori' => $selectedKategori,
                                    ]) }}"
                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Total Siswa:</strong> {{ count($riwayatData) }}
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Rata-rata Kelas:</strong>
                        <span
                            class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($riwayatData->avg('rata_rata')) }} badge-lg">
                            {{ number_format($riwayatData->avg('rata_rata'), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @elseif(request()->has('tahun_akademik_id') &&
            request()->has('kelas_id') &&
            request()->has('semester') &&
            request()->has('kategori'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada data riwayat penilaian untuk filter yang dipilih.
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto submit saat tahun akademik berubah
            $('#tahun_akademik_id').change(function() {
                if ($(this).val()) {
                    $('#kelas_id').val('');
                    $('#semester').val('');
                    $('#kategori').val('');
                    $('#filterForm').submit();
                }
            });
        });
    </script>
@endpush
