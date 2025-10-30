@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Proses Kenaikan Kelas</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.kenaikan-kelas.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-chalkboard"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Kelas Asal</span>
                                        <span class="info-box-number">{{ $kelasAsal->nama_lengkap }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Siswa</span>
                                        <span class="info-box-number">{{ $siswaList->count() }} Siswa</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($siswaList->count() == 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Tidak ada siswa di kelas ini.
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.kenaikan-kelas.store') }}" id="formKenaikanKelas">
                                @csrf
                                <input type="hidden" name="kelas_asal_id" value="{{ $kelasAsal->id }}">

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Tahun Akademik Tujuan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tahun_akademik_baru_id">Tahun Akademik Baru <span
                                                            class="text-danger">*</span></label>
                                                    <select name="tahun_akademik_baru_id" id="tahun_akademik_baru_id"
                                                        class="form-control" required>
                                                        <option value="">-- Pilih Tahun Akademik --</option>
                                                        @if ($tahunAkademikAktif)
                                                            <option value="{{ $tahunAkademikAktif->id }}" selected>
                                                                {{ $tahunAkademikAktif->tahun_mulai }}/{{ $tahunAkademikAktif->tahun_selesai }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                    <small class="form-text text-muted">Tahun akademik untuk tahun ajaran
                                                        baru</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Pengaturan Kenaikan Kelas per Siswa</h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-sm btn-info" onclick="setSemuaNaik()">
                                                <i class="fas fa-arrow-up"></i> Set Semua Naik Kelas
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th>NISN</th>
                                                        <th>Nama Siswa</th>
                                                        <th>Kelas Sekarang</th>
                                                        <th width="20%">Kelas Tujuan</th>
                                                        <th width="15%">Status</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($siswaList as $index => $siswa)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                {{ $siswa->nisn }}
                                                                <input type="hidden"
                                                                    name="siswa[{{ $index }}][siswa_id]"
                                                                    value="{{ $siswa->id }}">
                                                            </td>
                                                            <td>{{ $siswa->nama }}</td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    {{ $siswa->currentClass->nama_lengkap }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <select name="siswa[{{ $index }}][kelas_tujuan_id]"
                                                                    class="form-control form-control-sm kelas-tujuan"
                                                                    required>
                                                                    <option value="">-- Pilih --</option>
                                                                    @foreach ($kelasTujuanList as $kelas)
                                                                        <option value="{{ $kelas->id }}"
                                                                            data-tingkat="{{ $kelas->tingkat }}">
                                                                            {{ $kelas->nama_lengkap }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select name="siswa[{{ $index }}][status]"
                                                                    class="form-control form-control-sm status-siswa"
                                                                    required>
                                                                    <option value="naik">Naik Kelas</option>
                                                                    <option value="tinggal">Tinggal Kelas</option>
                                                                    <option value="lulus">Lulus</option>
                                                                    <option value="pindah">Pindah Sekolah</option>
                                                                    <option value="dropout">Dropout</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="siswa[{{ $index }}][keterangan]"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Keterangan (opsional)">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Apakah Anda yakin ingin memproses kenaikan kelas untuk {{ $siswaList->count() }} siswa?')">
                                        <i class="fas fa-save"></i> Proses Kenaikan Kelas
                                    </button>
                                    <a href="{{ route('admin.kenaikan-kelas.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setSemuaNaik() {
            // Set kelas tujuan pertama (tingkat tertinggi) untuk semua siswa
            const kelasTujuanFirst = document.querySelector('.kelas-tujuan option[data-tingkat]');

            if (!kelasTujuanFirst) {
                alert('Tidak ada kelas tujuan tersedia');
                return;
            }

            const tingkatTertinggi = Math.max(...Array.from(document.querySelectorAll('.kelas-tujuan option[data-tingkat]'))
                .map(opt => parseInt(opt.dataset.tingkat)));

            // Cari kelas dengan tingkat tertinggi pertama
            const kelasNaik = Array.from(document.querySelectorAll('.kelas-tujuan option[data-tingkat]'))
                .find(opt => parseInt(opt.dataset.tingkat) === tingkatTertinggi);

            if (kelasNaik) {
                document.querySelectorAll('.kelas-tujuan').forEach(select => {
                    select.value = kelasNaik.value;
                });

                // Set status semua ke "Naik Kelas"
                document.querySelectorAll('.status-siswa').forEach(select => {
                    select.value = 'naik';
                });

                alert('Semua siswa telah diset untuk naik kelas');
            }
        }

        // Auto select status based on kelas tujuan
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.kelas-tujuan').forEach(select => {
                select.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const statusSelect = row.querySelector('.status-siswa');
                    const selectedOption = this.options[this.selectedIndex];

                    if (selectedOption && selectedOption.dataset.tingkat) {
                        const tingkatAsal = {{ $kelasAsal->tingkat }};
                        const tingkatTujuan = parseInt(selectedOption.dataset.tingkat);

                        if (tingkatTujuan > tingkatAsal) {
                            statusSelect.value = 'naik';
                        } else if (tingkatTujuan === tingkatAsal) {
                            statusSelect.value = 'tinggal';
                        }
                    }
                });
            });
        });
    </script>
@endpush
