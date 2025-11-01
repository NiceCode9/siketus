<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lembar Ketuntasan Penilaian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
        }

        .header p {
            margin: 3px 0;
            font-size: 9pt;
        }

        .info-section {
            margin: 15px 0;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            display: table-cell;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .page-number {
            text-align: right;
            font-size: 8pt;
            margin-top: 10px;
        }

        .col-no {
            width: 30px;
        }

        .col-mapel {
            width: 200px;
        }

        .col-nilai {
            width: 50px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h2>LEMBAR KETUNTASAN PENILAIAN FORMATIF SUMATIF DAN KEDISIPLINAN</h2>
        <p>Tahun Akademik: {{ $tahunAkademik->nama_tahun_akademik }} - Semester: {{ ucfirst($semester) }}</p>
    </div>

    {{-- Info Siswa --}}
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">NAMA</div>
            <div class="info-value">: {{ strtoupper($siswa->nama) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">KELAS</div>
            <div class="info-value">: {{ $siswa->currentClass->nama_lengkap ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">NISN</div>
            <div class="info-value">: {{ $siswa->nisn }}</div>
        </div>
    </div>

    {{-- Tabel Nilai Mapel --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO</th>
                <th rowspan="2" class="col-mapel text-left">
                    MAPEL<br>
                    {{ strtoupper($siswa->currentClass->nama_lengkap ?? 'KELAS X') }} SEMESTER
                    {{ strtoupper($semester) }}
                </th>
                @php
                    // Ambil semua jenis ujian yang ada dari data nilai
                    $jenisUjianList = [];
                    foreach ($groupedNilaiMapel as $mapel => $ujianGroup) {
                        foreach ($ujianGroup->keys() as $ujian) {
                            if (!in_array($ujian, $jenisUjianList)) {
                                $jenisUjianList[] = $ujian;
                            }
                        }
                    }

                    // Jumlah kolom tetap 10 sesuai gambar
                    $jumlahKolom = 10;
                @endphp
                <th colspan="{{ $jumlahKolom }}">JENIS UJIAN</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= $jumlahKolom; $i++)
                    @if ($i <= count($jenisUjianList))
                        <th class="col-nilai">{{ $jenisUjianList[$i - 1] }}</th>
                    @else
                        <th class="col-nilai">{{ $i }}</th>
                    @endif
                @endfor
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            {{-- Data Nilai Mapel --}}
            @foreach ($groupedNilaiMapel as $mapel => $ujianGroup)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ $mapel }}</td>

                    @for ($i = 1; $i <= $jumlahKolom; $i++)
                        @php
                            $ujianName = $i <= count($jenisUjianList) ? $jenisUjianList[$i - 1] : null;
                        @endphp
                        <td>
                            @if ($ujianName && isset($ujianGroup[$ujianName]))
                                {{ number_format($ujianGroup[$ujianName]->first()->nilai, 0) }}
                            @else
                                -
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach

            {{-- Isi baris kosong jika kurang dari 15 mapel --}}
            @for ($i = count($groupedNilaiMapel); $i < 15; $i++)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left"></td>
                    @for ($j = 0; $j < $jumlahKolom; $j++)
                        <td>-</td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Footer dengan TTD --}}
    <div style="margin-top: 40px;">
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;"></td>
                <td style="border: none; width: 50%; text-align: center;">
                    <p style="margin-bottom: 5px;">Surabaya, _______________</p>
                    <p style="margin-bottom: 5px;">Wali Kelas,</p>
                    <br><br><br>
                    <p style="margin-top: 50px; border-bottom: 1px solid #000; display: inline-block; padding: 0 50px;">
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Nomor Halaman --}}
    <div class="page-number">
        60
    </div>
</body>

</html>
