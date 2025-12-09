<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Lengkap - {{ $siswa->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .header-section h3 {
            margin: 0;
            font-weight: bold;
            font-size: 16px;
        }

        .header-section p {
            margin: 2px 0;
            font-size: 10px;
        }

        .title-section {
            text-align: center;
            margin-bottom: 15px;
        }

        .title-section h5 {
            margin: 0;
            font-weight: bold;
            text-decoration: underline;
            font-size: 13px;
        }

        .info-section {
            margin-bottom: 15px;
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 2px 0;
            font-size: 10px;
        }

        .info-section td:first-child {
            width: 130px;
            font-weight: bold;
        }

        .info-section td:nth-child(3) {
            width: 130px;
            font-weight: bold;
        }

        .table-report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table-report th,
        .table-report td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 10px;
        }

        .table-report th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .table-report td.text-left {
            text-align: left;
        }

        .signature-section {
            margin-top: 30px;
        }

        .signature-section table {
            width: 100%;
        }

        .signature-section td {
            font-size: 10px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Page break untuk memisahkan laporan */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- ========== HALAMAN 1: LAPORAN MATA PELAJARAN ========== -->
    <div class="page-break">
        <!-- Header / Kop Surat -->
        <div class="header-section">
            <h3>SMK UNGGULAN NU MOJOAGUNG</h3>
            <p>Jl. Pendidikan No. 123, Kota, Provinsi 12345</p>
            <p>Telp: (021) 12345678 | Email: smknumojoagung@smpn1.sch.id</p>
        </div>

        <!-- Title -->
        <div class="title-section">
            <h5>LAPORAN NILAI MATA PELAJARAN</h5>
        </div>

        <!-- Info Siswa -->
        <div class="info-section">
            <table>
                <tr>
                    <td>Nama Siswa</td>
                    <td>: {{ $siswa->nama }}</td>
                    <td>NIS</td>
                    <td>: {{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: {{ $kelas ? $kelas->nama_lengkap : '-' }}</td>
                    <td>Tahun Akademik</td>
                    <td>: {{ $tahunAkademik->nama_tahun_akademik }}</td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td colspan="3">: Semester {{ $semester }} ({{ $semester == 1 ? 'Ganjil' : 'Genap' }})</td>
                </tr>
            </table>
        </div>

        <!-- Tabel Nilai Mapel -->
        <table class="table-report">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 150px;">Nama Mata Pelajaran</th>
                    <th style="width: 120px;">Guru Pengajar</th>
                    @foreach ($jenisUjian as $ju)
                        <th style="width: {{ 400 / count($jenisUjian) }}px;">{{ $ju->nama_jenis_ujian }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($reportDataMapel as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $data['mapel']->nama_mapel }}</td>
                        <td class="text-left">{{ $data['guru'] }}</td>
                        @foreach ($jenisUjian as $ju)
                            <td>{{ $data['nilai'][$ju->id] ?? '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 3 + count($jenisUjian) }}">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <table>
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%;">
                        <div class="signature-box">
                            <p style="margin-bottom: 5px;">
                                Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                            </p>
                            <p style="margin: 0;">Kepala Sekolah,</p>
                            <div class="signature-name">
                                ______________________
                            </div>
                            <p style="margin-top: 5px;">NIP. </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ========== HALAMAN 2: LAPORAN KEDISIPLINAN ========== -->
    <div>
        <!-- Header / Kop Surat -->
        <div class="header-section">
            <h3>SMK UNGGULAN NU MOJOAGUNG</h3>
            <p>Jl. Pendidikan No. 123, Kota, Provinsi 12345</p>
            <p>Telp: (021) 12345678 | Email: smknumojoagung@smpn1.sch.id</p>
        </div>

        <!-- Title -->
        <div class="title-section">
            <h5>LAPORAN KEDISIPLINAN</h5>
        </div>

        <!-- Info Siswa -->
        <div class="info-section">
            <table>
                <tr>
                    <td>Nama Siswa</td>
                    <td>: {{ $siswa->nama }}</td>
                    <td>NIS</td>
                    <td>: {{ $siswa->nisn }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: {{ $kelas ? $kelas->nama_lengkap : '-' }}</td>
                    <td>Tahun Akademik</td>
                    <td>: {{ $tahunAkademik->nama_tahun_akademik }}</td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td colspan="3">: Semester {{ $semester }} ({{ $semester == 1 ? 'Ganjil' : 'Genap' }})</td>
                </tr>
            </table>
        </div>

        <!-- Tabel Kedisiplinan -->
        <table class="table-report">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 150px;">Jenis</th>
                    <th style="width: 120px;">Petugas</th>
                    <th style="width: 120px;">Validasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kedisiplinan as $index => $val)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $val->jenis }}</td>
                        <td>{{ $guru[$val->id] }}</td>
                        <td>{{ isset($nilai[$val->id]) && $nilai[$val->id] ? 'valid' : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <table>
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%;">
                        <div class="signature-box">
                            <p style="margin-bottom: 5px;">
                                Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                            </p>
                            <p style="margin: 0;">Kepala Sekolah,</p>
                            <div class="signature-name">
                                ______________________
                            </div>
                            <p style="margin-top: 5px;">NIP. </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
