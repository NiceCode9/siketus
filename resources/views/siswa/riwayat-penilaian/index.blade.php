@extends('layouts.app', ['pageTitle' => 'Riwayat Nilai Saya'])

@section('content')
    <div class="row">
        <div class="col-md-3">
            {{-- Info Siswa --}}
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h3 class="profile-username text-center">{{ $siswa->nama }}</h3>
                    <p class="text-muted text-center">{{ $siswa->nisn }}</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Kelas</b>
                            <a class="float-right">{{ $siswa->currentClass->nama_lengkap ?? '-' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <a class="float-right">
                                <span class="badge badge-{{ $siswa->status == 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($siswa->status) }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            {{-- Filter --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Periode</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('siswa.riwayat-penilaian.siswa.index') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tahun_akademik_id">Tahun Akademik</label>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester">Semester</label>
                                    <select name="semester" id="semester" class="form-control" required>
                                        <option value="">-- Pilih Semester --</option>
                                        <option value="ganjil" {{ $selectedSemester == 'ganjil' ? 'selected' : '' }}>Ganjil
                                        </option>
                                        <option value="genap" {{ $selectedSemester == 'genap' ? 'selected' : '' }}>Genap
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="{{ route('siswa.riwayat-penilaian.siswa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </form>
                </div>
            </div>

            @if ($selectedTahunAkademik && $selectedSemester)
                {{-- Statistik --}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $statistik['total_nilai_mapel'] }}</h3>
                                <p>Nilai Mapel</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($statistik['rata_rata_mapel'], 1) }}</h3>
                                <p>Rata-rata Mapel</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($statistik['rata_rata_kedisiplinan'], 1) }}</h3>
                                <p>Kedisiplinan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ number_format($statistik['rata_rata_keagamaan'], 1) }}</h3>
                                <p>Keagamaan</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-pray"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nilai Mata Pelajaran --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book"></i> Nilai Mata Pelajaran
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($nilaiMapel->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Mata Pelajaran</th>
                                            <th>Jenis Ujian</th>
                                            <th width="100" class="text-center">Nilai</th>
                                            <th width="100" class="text-center">Grade</th>
                                            <th>Guru</th>
                                            <th width="150">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($nilaiMapel as $nilai)
                                            <tr>
                                                <td>{{ $nilai->guruKelas->guruMapel->mapel->nama_mapel }}</td>
                                                <td>{{ $nilai->jenisUjian->nama_jenis_ujian }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilai->nilai) }}">
                                                        {{ number_format($nilai->nilai, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ App\Helpers\NilaiHelper::nilaiToHuruf($nilai->nilai) }}</strong>
                                                </td>
                                                <td>{{ $nilai->guruKelas->guruMapel->guru->nama }}</td>
                                                <td>{{ $nilai->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada nilai mata pelajaran untuk periode ini.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Nilai Kedisiplinan --}}
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-check"></i> Nilai Kedisiplinan
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($nilaiKedisiplinan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Jenis Kedisiplinan</th>
                                            <th width="100" class="text-center">Nilai</th>
                                            <th width="150" class="text-center">Predikat</th>
                                            <th>Catatan</th>
                                            <th>Penilai</th>
                                            <th width="150">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($nilaiKedisiplinan as $nilai)
                                            <tr>
                                                <td>{{ $nilai->kedisiplinan->jenis }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilai->nilai) }}">
                                                        {{ number_format($nilai->nilai, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    {{ App\Helpers\NilaiHelper::nilaiToPredikat($nilai->nilai) }}</td>
                                                <td>{{ $nilai->catatan ?? '-' }}</td>
                                                <td>{{ $nilai->guru->nama }}</td>
                                                <td>{{ $nilai->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th class="text-right">Rata-rata:</th>
                                            <th class="text-center">
                                                <span
                                                    class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilaiKedisiplinan->avg('nilai')) }} badge-lg">
                                                    {{ number_format($nilaiKedisiplinan->avg('nilai'), 2) }}
                                                </span>
                                            </th>
                                            <th class="text-center">
                                                {{ App\Helpers\NilaiHelper::nilaiToPredikat($nilaiKedisiplinan->avg('nilai')) }}
                                            </th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada nilai kedisiplinan untuk periode ini.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Nilai Kegiatan Keagamaan --}}
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pray"></i> Nilai Kegiatan Keagamaan
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($nilaiKeagamaan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kegiatan</th>
                                            <th width="100" class="text-center">Nilai</th>
                                            <th width="150" class="text-center">Predikat</th>
                                            <th>Catatan</th>
                                            <th>Penilai</th>
                                            <th width="150">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($nilaiKeagamaan as $nilai)
                                            <tr>
                                                <td>{{ $nilai->kegiatanKeagamaan->nama_kegiatan }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilai->nilai) }}">
                                                        {{ number_format($nilai->nilai, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    {{ App\Helpers\NilaiHelper::nilaiToPredikat($nilai->nilai) }}</td>
                                                <td>{{ $nilai->catatan ?? '-' }}</td>
                                                <td>{{ $nilai->guru->nama }}</td>
                                                <td>{{ $nilai->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th class="text-right">Rata-rata:</th>
                                            <th class="text-center">
                                                <span
                                                    class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilaiKeagamaan->avg('nilai')) }} badge-lg">
                                                    {{ number_format($nilaiKeagamaan->avg('nilai'), 2) }}
                                                </span>
                                            </th>
                                            <th class="text-center">
                                                {{ App\Helpers\NilaiHelper::nilaiToPredikat($nilaiKeagamaan->avg('nilai')) }}
                                            </th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada nilai kegiatan keagamaan untuk periode ini.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .badge-lg {
            font-size: 1.1em;
            padding: 0.5em 0.8em;
        }
    </style>
@endpush
