@extends('layouts.app', ['pageTitle' => 'Input Nilai Saya'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Input Nilai</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('siswa.penilaian.index') }}" method="GET">
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center align-middle" style="width: 250px;">Mata Pelajaran</th>
                            @foreach ($jenisUjians as $ju)
                                <th class="text-center align-middle" style="width: 100px;">{{ $ju->nama_jenis_ujian }}</th>
                            @endforeach
                            <th class="text-center align-middle" style="width: 100px;">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guruKelas as $gk)
                            <tr>
                                <td><strong>{{ $gk->guruMapel->mapel->nama_mapel }}</strong></td>
                                @php
                                    $totalNilai = 0;
                                    $countNilai = 0;
                                @endphp
                                @foreach ($jenisUjians as $jenis)
                                    @php
                                        $nilai = $nilaiData[$gk->id][$jenis->id] ?? '';
                                        if ($nilai !== '' && $nilai !== null) {
                                            $totalNilai += $nilai;
                                            $countNilai++;
                                        }
                                    @endphp
                                    <td>
                                        <input type="number" name="nilai[{{ $gk->id }}][{{ $jenis->id }}]"
                                            class="form-control form-control-sm text-center nilai-input"
                                            value="{{ $nilai }}" min="0" max="100" step="0.01"
                                            placeholder="0-100" data-row="{{ $gk->id }}">
                                    </td>
                                @endforeach
                                <td class="text-center align-middle">
                                    <strong class="rata-rata-{{ $gk->id }}">
                                        {{ $countNilai > 0 ? number_format($totalNilai / $countNilai, 2) : '-' }}
                                    </strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
@endsection
