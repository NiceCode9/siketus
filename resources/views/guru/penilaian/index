@extends('layouts.app', ['pageTitle' => 'Penilaian'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Penilaian</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <form method="GET" action="{{ route('guru.penilaian.index') }}" id="filterForm">
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

                    <div class="col-md-2">
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

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="semester">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="semester" class="form-control" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="ganjil" {{ $selectedSemester == 'ganjil' ? 'selected' : '' }}>Ganjil
                                </option>
                                <option value="genap" {{ $selectedSemester == 'genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
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

                    <div class="col-md-3 {{ $selectedKategori == 'mapel' ? '' : 'd-none' }}" id="mapel_id_div">
                        <label for="mapel_id">Mata Pelajaran</label>
                        <select name="mapel_id" id="mapel_id" class="form-control">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach (auth()->user()->guru->guruMapel as $mapel)
                                <option value="{{ $mapel->mapel->id }}"
                                    {{ $selectedMapel == $mapel->mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Tampilkan
                        </button>
                        <a href="{{ route('guru.penilaian.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (count($siswaList) > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Daftar Siswa -
                    @if ($selectedKategori == 'mapel')
                        <span class="badge badge-primary">Mata Pelajaran</span>
                        @if ($guruKelas && $guruKelas->guruMapel)
                            <span class="badge badge-info">{{ $guruKelas->guruMapel->mapel->nama_mapel }}</span>
                        @endif
                    @elseif($selectedKategori == 'kedisiplinan')
                        <span class="badge badge-warning">Kedisiplinan</span>
                    @elseif($selectedKategori == 'keagamaan')
                        <span class="badge badge-success">Kegiatan Keagamaan</span>
                    @endif
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $index => $siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $siswa->nisn }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>
                                    <span class="badge badge-{{ $siswa->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($siswa->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('guru.penilaian.create', [
                                        'siswa_id' => $siswa->id,
                                        'tahun_akademik_id' => $selectedTahunAkademik,
                                        'kelas_id' => $selectedKelas,
                                        'semester' => $selectedSemester,
                                        'kategori' => $selectedKategori,
                                        'mapel_id' => $selectedMapel,
                                    ]) }}"
                                        class="btn btn-sm btn-primary" title="Input Nilai">
                                        <i class="fas fa-edit"></i> Input Nilai
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data siswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($selectedKategori == 'mapel' && $jenisUjianList->count() == 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Belum ada jenis ujian yang terdaftar untuk tahun akademik ini.
            </div>
        @endif

        @if ($selectedKategori == 'kedisiplinan' && $kedisiplinanList->count() == 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Belum ada jenis kedisiplinan yang terdaftar.
            </div>
        @endif

        @if ($selectedKategori == 'keagamaan' && $kegiatanKeagamaanList->count() == 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Belum ada kegiatan keagamaan yang terdaftar untuk semester ini.
            </div>
        @endif
    @endif

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto submit saat tahun akademik berubah untuk load kelas
            $('#tahun_akademik_id').change(function() {
                if ($(this).val()) {
                    $('#kelas_id').val('');
                    $('#semester').val('');
                    $('#kategori').val('');
                    $('#filterForm').submit();
                }
            });

            $('#kategori').change(function(e) {
                e.preventDefault();

                let val = $(this).val();
                if (val == 'mapel') {
                    $('#mapel_id_div').removeClass('d-none');
                    $('#mapel_id').prop('required', true);
                } else {
                    $('#mapel_id_div').addClass('d-none');
                    $('#mapel_id').prop('required', false);
                    $('#mapel_id').val('');
                }
            });
        });
    </script>
@endpush
