@extends('layouts.app', ['pageTitle' => 'Set KKM'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Set Kriteria Ketuntasan Minimal (KKM)</h3>
            <div class="card-tools">
                <a href="{{ route('guru.kkm.index', ['tahun_akademik_id' => $guruKelas->tahun_akademik_id]) }}"
                    class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Info Kelas -->
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Informasi</h5>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Kelas</strong></td>
                        <td>: {{ $guruKelas->kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <td><strong>Mata Pelajaran</strong></td>
                        <td>: {{ $guruKelas->guruMapel->mapel->nama_mapel }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tahun Akademik</strong></td>
                        <td>: {{ $guruKelas->tahunAkademik->nama_tahun_akademik }}</td>
                    </tr>
                </table>
            </div>

            <form action="{{ route('guru.kkm.store') }}" method="POST" id="formKkm">
                @csrf
                <input type="hidden" name="guru_kelas_id" value="{{ $guruKelas->id }}">
                <input type="hidden" name="tahun_akademik_id" value="{{ $guruKelas->tahun_akademik_id }}">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-primary">
                            <tr>
                                <th width="50">No</th>
                                <th>Jenis Ujian</th>
                                <th>Semester</th>
                                <th width="200">KKM (0-100)</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jenisUjianWithKkm as $index => $item)
                                @php
                                    $jenisUjian = $item['jenis_ujian'];
                                    $kkm = $item['kkm'];
                                    $hasKkm = $item['has_kkm'];
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $jenisUjian->nama_jenis_ujian }}</strong></td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $jenisUjian->semester == 'ganjil' ? 'info' : 'success' }}">
                                            {{ ucfirst($jenisUjian->semester) }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" name="kkm[{{ $jenisUjian->id }}]"
                                            class="form-control kkm-input" value="{{ $kkm }}" min="0"
                                            max="100" step="0.01" placeholder="Contoh: 75">
                                    </td>
                                    <td class="text-center">
                                        @if ($hasKkm)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Sudah diset
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation"></i> Belum diset
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> KKM yang diset akan digunakan untuk menentukan ketuntasan siswa dan status
                    remidi.
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Simpan KKM
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#formKkm').on('submit', function(e) {
                e.preventDefault();

                // Check if at least one KKM is filled
                var hasValue = false;
                $('.kkm-input').each(function() {
                    if ($(this).val() !== '') {
                        hasValue = true;
                        return false;
                    }
                });

                if (!hasValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Harap isi minimal satu KKM!',
                    });
                    return false;
                }

                // Confirm
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menyimpan KKM ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush
