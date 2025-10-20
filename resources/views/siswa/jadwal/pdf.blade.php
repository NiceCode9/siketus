<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Jadwal Pelajaran</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0;
            font-size: 11px;
        }

        .info-section {
            margin-bottom: 15px;
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 3px 0;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .schedule-table th {
            background-color: #4a5568;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #333;
        }

        .schedule-table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        .schedule-table tr:nth-child(even) {
            background-color: #f7fafc;
        }

        .day-header {
            background-color: #e2e8f0;
            font-weight: bold;
            padding: 8px 5px !important;
            color: #2d3748;
            text-align: center;
            font-size: 12px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }

        .footer .signature {
            margin-top: 50px;
            text-align: right;
        }

        .no-jadwal {
            text-align: center;
            padding: 15px;
            color: #718096;
            font-style: italic;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-primary {
            background-color: #4299e1;
            color: white;
        }

        .badge-success {
            background-color: #48bb78;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title ?? 'Jadwal Pelajaran' }}</h1>
        <p><strong>{{ $schoolName ?? 'Sekolah ABC' }}</strong></p>
        <p>{{ $schoolAddress ?? 'Jl. Pendidikan No. 123, Surabaya' }}</p>
        @if (isset($kelas))
            <p>Kelas: <strong>{{ $kelas->nama_kelas }}</strong></p>
        @endif
        @if (isset($guru))
            <p>Guru: <strong>{{ $guru->nama }}</strong></p>
        @endif
        @if (isset($tahunAkademik))
            <p>Tahun Akademik: <strong>{{ $tahunAkademik->nama_tahun_akademik }}</strong> -
                {{ $tahunAkademik->semester }}</p>
        @endif
    </div>

    <!-- Schedule Content -->
    @if (isset($type) && $type == 'daily')
        <!-- Daily Schedule -->
        @if (isset($jadwalPerHari) && count($jadwalPerHari) > 0)
            @foreach ($jadwalPerHari as $hari => $jadwalList)
                @if ($jadwalList->count() > 0)
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th colspan="5" class="day-header">{{ strtoupper($hari) }}</th>
                            </tr>
                            <tr>
                                <th width="12%">Waktu</th>
                                <th width="25%">Mata Pelajaran</th>
                                <th width="23%">Guru</th>
                                <th width="20%">Kelas</th>
                                <th width="20%">Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwalList as $jadwal)
                                <tr>
                                    <td> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                    <td><strong>{{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel }}</strong></td>
                                    <td>{{ $jadwal->guruKelas->guruMapel->guru->nama }}</td>
                                    <td>{{ $jadwal->guruKelas->kelas->nama_lengkap }}</td>
                                    <td>{{ $jadwal->ruangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endforeach
        @endif
    @else
        <!-- Weekly/Semester Schedule -->
        @if (isset($jadwalPerHari))
            @foreach ($hariList as $hari)
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th colspan="5" class="day-header">{{ strtoupper($hari) }}</th>
                        </tr>
                        <tr>
                            <th width="12%">Waktu</th>
                            <th width="25%">Mata Pelajaran</th>
                            @if (!isset($guru))
                                <th width="23%">Guru</th>
                            @endif
                            @if (!isset($kelas))
                                <th width="20%">Kelas</th>
                            @endif
                            <th width="20%">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($jadwalPerHari[$hari]->count() > 0)
                            @foreach ($jadwalPerHari[$hari] as $jadwal)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                    <td><strong>{{ $jadwal->guruKelas->guruMapel->mapel->nama_mapel }}</strong></td>
                                    @if (!isset($guru))
                                        <td>{{ $jadwal->guruKelas->guruMapel->guru->nama }}</td>
                                    @endif
                                    @if (!isset($kelas))
                                        <td>{{ $jadwal->guruKelas->kelas->nama_lengkap }}</td>
                                    @endif
                                    <td>{{ $jadwal->ruangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="no-jadwal">Tidak ada jadwal pada hari ini</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endforeach
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        @if (isset($showSignature) && $showSignature)
            <div class="signature">
                <p>Mengetahui,</p>
                <br><br><br>
                <p>____________________</p>
                <p><strong>Kepala Sekolah</strong></p>
            </div>
        @endif
    </div>

    <!-- Notes -->
    @if (isset($showNotes) && $showNotes)
        <div style="margin-top: 20px; padding: 10px; background-color: #f7fafc; border-left: 4px solid #4299e1;">
            <p style="margin: 0; font-size: 10px;"><strong>Catatan:</strong></p>
            <ul style="margin: 5px 0; padding-left: 20px; font-size: 10px;">
                <li>Harap datang 10 menit sebelum pelajaran dimulai</li>
                <li>Jika ada perubahan jadwal akan diinformasikan lebih lanjut</li>
                <li>Untuk kelas praktikum, mohon konfirmasi ruangan kepada guru pengampu</li>
            </ul>
        </div>
    @endif
</body>

</html>
