@extends('layouts.app', ['pageTitle' => 'Detail Riwayat Penilaian'])

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
                            <td>: {{ $siswa->currentClass->nama_lengkap ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Semester</th>
                            <td>: {{ ucfirst($semester) }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>:
                                @if ($kategori == 'mapel')
                                    <span class="badge badge-primary">Mata Pelajaran</span>
                                @elseif($kategori == 'kedisiplinan')
                                    <span class="badge badge-warning">Kedisiplinan</span>
                                @else
                                    <span class="badge badge-success">Kegiatan Keagamaan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Nilai</th>
                            <td>: <span class="badge badge-info">{{ $detail->count() }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Nilai</h3>
            <div class="card-tools">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if ($detail->count() > 0)
                @if ($kategori == 'mapel')
                    {{-- Detail Nilai Mata Pelajaran --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Jenis Ujian</th>
                                    <th width="100" class="text-center">Nilai</th>
                                    <th width="100" class="text-center">Grade</th>
                                    <th>Catatan</th>
                                    <th width="150">Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail as $index => $nilai)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $nilai->guruKelas->guruMapel->mapel->nama_mapel }}</td>
                                        <td>{{ $nilai->jenisUjian->nama_jenis_ujian }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilai->nilai) }}">
                                                {{ number_format($nilai->nilai, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ App\Helpers\NilaiHelper::nilaiToPredikat($nilai->nilai) }}</td>
                                        <td>{{ $nilai->catatan ?? '-' }}</td>
                                        <td>{{ $nilai->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-right">Rata-rata:</th>
                                    <th class="text-center">
                                        <span
                                            class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($detail->avg('nilai')) }} badge-lg">
                                            {{ number_format($detail->avg('nilai'), 2) }}
                                        </span>
                                    </th>
                                    <th class="text-center">
                                        {{ App\Helpers\NilaiHelper::nilaiToPredikat($detail->avg('nilai')) }}
                                    </th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @elseif($kategori == 'keagamaan')
                    {{-- Detail Nilai Kegiatan Keagamaan --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kegiatan Keagamaan</th>
                                    <th width="100" class="text-center">Nilai</th>
                                    <th width="150" class="text-center">Predikat</th>
                                    <th>Catatan</th>
                                    <th width="150">Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail as $index => $nilai)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
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
                                        <td>{{ $nilai->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-right">Rata-rata:</th>
                                    <th class="text-center">
                                        <span
                                            class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($detail->avg('nilai')) }} badge-lg">
                                            {{ number_format($detail->avg('nilai'), 2) }}
                                        </span>
                                    </th>
                                    <th class="text-center">
                                        {{ App\Helpers\NilaiHelper::nilaiToPredikat($detail->avg('nilai')) }}
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada data nilai untuk siswa ini.
                </div>
            @endif
        </div>
    </div>

@endsection
