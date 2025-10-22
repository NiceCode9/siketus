@extends('layouts.app')


@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Siswa</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">NISN</th>
                            <td>: {{ $siswa->nisn }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>: {{ $siswa->nama }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>: {{ $kelas->nama_lengkap }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Tahun Akademik</th>
                            <td>: {{ $tahunAkademik->nama_tahun_akademik }}</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>: {{ ucfirst($semester) }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>:
                                @if ($kategori == 'mapel')
                                    <span class="badge badge-primary">Mata Pelajaran -
                                        {{ $guruKelas->guruMapel->mapel->nama_mapel }}</span>
                                @elseif($kategori == 'kedisiplinan')
                                    <span class="badge badge-warning">Kedisiplinan</span>
                                @else
                                    <span class="badge badge-success">Kegiatan Keagamaan</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('guru.penilaian.store') }}" method="POST">
        @csrf
        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
        <input type="hidden" name="tahun_akademik_id" value="{{ $tahunAkademik->id }}">
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="hidden" name="kategori" value="{{ $kategori }}">
        @if ($kategori == 'mapel')
            <input type="hidden" name="mapel_id" value="{{ $mapelId }}">
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Penilaian</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($kategori == 'mapel')
                    {{-- Penilaian Mata Pelajaran --}}
                    <input type="hidden" name="guru_kelas_id" value="{{ $guruKelas->id }}">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Jenis Ujian</th>
                                    <th width="150">Nilai by siswa (0-100)</th>
                                    <th width="150">Nilai (0-100)</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jenisUjianList as $index => $jenisUjian)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $jenisUjian->nama_jenis_ujian }}</strong>
                                            @if ($jenisUjian->deskripsi)
                                                <br><small class="text-muted">{{ $jenisUjian->deskripsi }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" max="100"
                                                step="0.01"
                                                value="{{ $existingNilai[$jenisUjian->id]->nilai_by_siswa ?? '' }}"
                                                placeholder="0-100" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="nilai[{{ $jenisUjian->id }}]" class="form-control"
                                                min="0" max="100" step="0.01"
                                                value="{{ $existingNilai[$jenisUjian->id]->nilai ?? '' }}"
                                                placeholder="0-100">
                                        </td>
                                        <td>
                                            <textarea name="catatan[{{ $jenisUjian->id }}]" class="form-control" rows="2" placeholder="Catatan (opsional)">{{ $existingNilai[$jenisUjian->id]->catatan ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Belum ada jenis ujian yang terdaftar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif($kategori == 'kedisiplinan')
                    {{-- Penilaian Kedisiplinan --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Jenis Kedisiplinan</th>
                                    <th width="150">Nilai (0-100)</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kedisiplinanList as $index => $kedisiplinan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $kedisiplinan->jenis }}</strong></td>
                                        <td>
                                            <input type="number" name="nilai[{{ $kedisiplinan->id }}]"
                                                class="form-control" min="0" max="100" step="0.01"
                                                value="{{ $existingNilai[$kedisiplinan->id]->nilai ?? '' }}"
                                                placeholder="0-100">
                                        </td>
                                        <td>
                                            <textarea name="catatan[{{ $kedisiplinan->id }}]" class="form-control" rows="2" placeholder="Catatan (opsional)">{{ $existingNilai[$kedisiplinan->id]->catatan ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Belum ada jenis kedisiplinan yang
                                            terdaftar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif($kategori == 'keagamaan')
                    {{-- Penilaian Kegiatan Keagamaan --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kegiatan Keagamaan</th>
                                    <th width="150">Nilai (0-100)</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kegiatanKeagamaanList as $index => $kegiatan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
                                        <td>
                                            <input type="number" name="nilai[{{ $kegiatan->id }}]" class="form-control"
                                                min="0" max="100" step="0.01"
                                                value="{{ $existingNilai[$kegiatan->id]->nilai ?? '' }}"
                                                placeholder="0-100">
                                        </td>
                                        <td>
                                            <textarea name="catatan[{{ $kegiatan->id }}]" class="form-control" rows="2" placeholder="Catatan (opsional)">{{ $existingNilai[$kegiatan->id]->catatan ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Belum ada kegiatan keagamaan yang
                                            terdaftar untuk semester ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Catatan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Nilai berkisar antara 0 sampai 100</li>
                        <li>Kosongkan nilai jika belum ingin mengisi</li>
                        <li>Data yang sudah ada akan diperbarui jika Anda mengisi ulang</li>
                    </ul>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Nilai
                </button>
                @php
                    $data = [
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'kelas_id' => $kelas->id,
                        'semester' => $semester,
                        'kategori' => $kategori,
                    ];

                    if ($kategori == 'mapel') {
                        $data['mapel_id'] = $mapelId ?? '';
                    }
                @endphp
                <a href="{{ route('guru.penilaian.index', $data) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Validasi nilai tidak boleh lebih dari 100
            $('input[type="number"]').on('input', function() {
                var value = parseFloat($(this).val());
                if (value > 100) {
                    $(this).val(100);
                    alert('Nilai maksimal adalah 100');
                }
                if (value < 0) {
                    $(this).val(0);
                    alert('Nilai minimal adalah 0');
                }
            });

            // Konfirmasi sebelum submit
            $('form').on('submit', function(e) {
                var hasValue = false;
                $('input[type="number"]').each(function() {
                    if ($(this).val() !== '') {
                        hasValue = true;
                        return false;
                    }
                });

                if (!hasValue) {
                    e.preventDefault();
                    alert('Harap isi minimal satu nilai sebelum menyimpan!');
                    return false;
                }

                return confirm('Apakah Anda yakin ingin menyimpan nilai ini?');
            });
        });
    </script>
@endpush
