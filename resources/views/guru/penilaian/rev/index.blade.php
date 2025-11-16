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
        {{-- <form action="{{ route('guru.penilaian.store') }}" method="POST">
            @csrf
            <input type="hidden" name="guru_kelas_id" value="{{ $guruKelas->id }}">
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 250px;">Mata Pelajaran</th>
                        @foreach ($jenisUjianList as $ju)
                            <th class="text-center align-middle" style="width: 100px;">{{ $ju->nama_jenis_ujian }}</th>
                        @endforeach
                        <th class="text-center align-middle" style="width: 100px;">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswaList as $siswa)
                        <tr>
                            <td class="align-middle">
                                {{ $siswa->nama }} ({{ $siswa->nisn }})
                                <input type="hidden" name="siswa_id[]" value="{{ $siswa->id }}">
                            </td>
                            @foreach ($jenisUjianList as $ju)
                                <td class="text-center align-middle">
                                    <input type="number" name="nilai[{{ $siswa->id }}][{{ $ju->id }}]"
                                        class="form-control"
                                        value="{{ old('nilai.' . $siswa->id . '.' . $ju->id, isset($nilaiList[$siswa->id][$ju->id]) ? $nilaiList[$siswa->id][$ju->id] : '') }}"
                                        min="0" max="100" step="0.01" required>
                                </td>
                            @endforeach
                            <td class="text-center align-middle">
                                <input type="number" name="rata_rata[{{ $siswa->id }}]" class="form-control"
                                    value="{{ old('rata_rata.' . $siswa->id, isset($rataRataList[$siswa->id]) ? $rataRataList[$siswa->id] : '') }}"
                                    readonly>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" id="btnSimpan">
                    <i class="fas fa-save"></i> Simpan Nilai
                </button>
            </div>
        </form> --}}

        <form action="{{ route('guru.penilaian.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Penilaian</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    @if ($selectedKategori == 'mapel')
                                        @foreach ($jenisUjianList as $jenis)
                                            <th>{{ $jenis->nama_jenis_ujian }}</th>
                                        @endforeach
                                    @elseif ($selectedKategori == 'kedisiplinan')
                                        @foreach ($kedisiplinanList as $kedisiplinan)
                                            <th>{{ $kedisiplinan->jenis }}</th>
                                        @endforeach
                                    @elseif ($selectedKategori == 'keagamaan')
                                        @foreach ($kegiatanKeagamaanList as $kegiatan)
                                            <th>{{ $kegiatan->nama_kegiatan }}</th>
                                        @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswaList as $index => $siswa)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $siswa->nama }}</td>
                                        @if ($selectedKategori == 'mapel')
                                            @foreach ($jenisUjianList as $jenis)
                                                <td>
                                                    <input type="hidden" name="mapel_id" value="{{ $selectedMapel }}">
                                                    <input type="hidden"
                                                        name="tahun_akademik_id"value="{{ $selectedTahunAkademik }}">
                                                    <input type="hidden" name="semester" value="{{ $selectedSemester }}">
                                                    <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                                                    <input type="hidden" name="kategori" value="{{ $selectedKategori }}">
                                                    <input type="hidden" name="guru_kelas_id"
                                                        value="{{ $guruKelas->id }}">

                                                    {{-- <input type="number"
                                                        name="nilai[{{ $siswa->id }}][{{ $jenis->id }}]"
                                                        class="form-control"
                                                        value="{{ old('nilai.' . $siswa->id . '.' . $jenis->id, isset($nilaiList[$siswa->id]['existingNilai'][$jenis->id]) ? $nilaiList[$siswa->id]['existingNilai'][$jenis->id] : '') }}"> --}}
                                                    <input type="number"
                                                        name="nilai[{{ $siswa->id }}][{{ $jenis->id }}]"
                                                        class="form-control"
                                                        value="{{ $nilaiList[$siswa->id]['existingNilai'][$jenis->id] ?? '' }}">
                                                </td>
                                            @endforeach
                                            {{-- Bagian form untuk Kedisiplinan --}}
                                        @elseif ($selectedKategori == 'kedisiplinan')
                                            @foreach ($kedisiplinanList as $kedisiplinan)
                                                <td>
                                                    <input type="hidden" name="tahun_akademik_id"
                                                        value="{{ $selectedTahunAkademik }}">
                                                    <input type="hidden" name="semester"
                                                        value="{{ $selectedSemester }}">
                                                    <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                                                    <input type="hidden" name="kategori"
                                                        value="{{ $selectedKategori }}">

                                                    <div class="form-check text-center">
                                                        <input type="hidden"
                                                            name="nilai[{{ $siswa->id }}][{{ $kedisiplinan->id }}]"
                                                            value="0">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="nilai[{{ $siswa->id }}][{{ $kedisiplinan->id }}]"
                                                            value="1"
                                                            id="kedisiplinan_{{ $siswa->id }}_{{ $kedisiplinan->id }}"
                                                            {{ old("nilai.$siswa->id.$kedisiplinan->id", isset($nilaiList[$siswa->id]['existingNilai'][$kedisiplinan->id]) && $nilaiList[$siswa->id]['existingNilai'][$kedisiplinan->id] == 1 ? 1 : 0) == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            @endforeach

                                            {{-- Bagian form untuk Keagamaan --}}
                                        @elseif ($selectedKategori == 'keagamaan')
                                            @foreach ($kegiatanKeagamaanList as $kegiatan)
                                                <td>
                                                    <input type="hidden" name="tahun_akademik_id"
                                                        value="{{ $selectedTahunAkademik }}">
                                                    <input type="hidden" name="semester"
                                                        value="{{ $selectedSemester }}">
                                                    <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                                                    <input type="hidden" name="kategori"
                                                        value="{{ $selectedKategori }}">

                                                    <input type="number"
                                                        name="nilai[{{ $siswa->id }}][{{ $kegiatan->id }}]"
                                                        class="form-control" min="0" max="100"
                                                        step="0.01"
                                                        value="{{ old("nilai.$siswa->id.$kegiatan->id", isset($nilaiList[$siswa->id]['existingNilai'][$kegiatan->id]) ? $nilaiList[$siswa->id]['existingNilai'][$kegiatan->id] : '') }}">
                                                </td>
                                            @endforeach
                                        @endif

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" id="btnSimpan">
                                <i class="fas fa-save"></i> Simpan Nilai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
