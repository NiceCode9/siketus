@extends('layouts.app', ['pageTitle' => 'Input Nilai Saya'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Input Nilai</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Perhatian:</strong> Nilai yang Anda input tidak boleh melebihi nilai yang telah diinput oleh guru.
            </div>

            <form id="formNilai" action="{{ route('siswa.penilaian.store') }}" method="POST">
                @csrf
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
                                <input type="hidden" name="guru_kelas_id[]" value="{{ $gk->id }}">
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
                                            placeholder="0-100" data-row="{{ $gk->id }}"
                                            data-guru-kelas="{{ $gk->id }}" data-jenis-ujian="{{ $jenis->id }}">
                                        <small class="error-message text-danger d-none"
                                            data-error="{{ $gk->id }}-{{ $jenis->id }}"></small>
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
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-control.is-invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .error-message {
            display: block;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Validasi real-time saat input
            $('.nilai-input').on('blur', function() {
                var input = $(this);
                var guruKelasId = input.data('guru-kelas');
                var jenisUjianId = input.data('jenis-ujian');
                var nilaiSiswa = parseFloat(input.val());
                var errorMsg = $(`small[data-error="${guruKelasId}-${jenisUjianId}"]`);

                if (!nilaiSiswa || nilaiSiswa === '') {
                    input.removeClass('is-invalid');
                    errorMsg.addClass('d-none').text('');
                    return;
                }

                // Ajax request untuk validasi
                $.ajax({
                    url: '{{ route('siswa.penilaian.get-nilai-guru') }}',
                    method: 'GET',
                    data: {
                        guru_kelas_id: guruKelasId,
                        jenis_ujian_id: jenisUjianId
                    },
                    success: function(response) {
                        if (response.success && response.nilai !== null) {
                            var nilaiGuru = parseFloat(response.nilai);

                            if (nilaiSiswa > nilaiGuru) {
                                input.addClass('is-invalid');
                                errorMsg.removeClass('d-none')
                                    .text(`Nilai tidak boleh melebihi ${nilaiGuru}`);
                            } else {
                                input.removeClass('is-invalid');
                                errorMsg.addClass('d-none').text('');
                            }
                        } else {
                            input.removeClass('is-invalid');
                            errorMsg.addClass('d-none').text('');
                        }
                    },
                    error: function() {
                        console.error('Gagal memvalidasi nilai');
                    }
                });
            });

            // Hitung rata-rata
            $('.nilai-input').on('input', function() {
                var row = $(this).data('row');
                var total = 0;
                var count = 0;

                $(`input[data-row="${row}"]`).each(function() {
                    var val = parseFloat($(this).val());
                    if (!isNaN(val) && val !== '') {
                        total += val;
                        count++;
                    }
                });

                var rataRata = count > 0 ? (total / count).toFixed(2) : '-';
                $(`.rata-rata-${row}`).text(rataRata);
            });

            // Submit form dengan AJAX
            $('#formNilai').on('submit', function(e) {
                e.preventDefault();

                // Cek apakah ada nilai yang invalid
                if ($('.form-control.is-invalid').length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Terdapat nilai yang melebihi batas yang ditentukan guru!',
                    });
                    return false;
                }

                // Cek apakah ada nilai yang diisi
                var hasValue = false;
                $('input[type="number"]').each(function() {
                    if ($(this).val() !== '') {
                        hasValue = true;
                        return false;
                    }
                });

                if (!hasValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Harap isi minimal satu nilai sebelum menyimpan!',
                    });
                    return false;
                }

                // Konfirmasi
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menyimpan nilai ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });

            function submitForm() {
                var formData = $('#formNilai').serialize();
                var btnSimpan = $('#btnSimpan');

                btnSimpan.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: '{{ route('siswa.penilaian.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        btnSimpan.prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Simpan Nilai');

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessages = [];

                            // Tampilkan error pada field yang bermasalah
                            $.each(errors, function(guruKelasId, jenisUjianErrors) {
                                $.each(jenisUjianErrors, function(jenisUjianId, error) {
                                    var input = $(
                                        `input[data-guru-kelas="${guruKelasId}"][data-jenis-ujian="${jenisUjianId}"]`
                                    );
                                    var errorMsg = $(
                                        `small[data-error="${guruKelasId}-${jenisUjianId}"]`
                                    );

                                    input.addClass('is-invalid');
                                    errorMsg.removeClass('d-none').text(error.message);
                                    errorMessages.push(error.message);
                                });
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: xhr.responseJSON.message + '<br><small>' + errorMessages
                                    .join('<br>') + '</small>',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat menyimpan nilai',
                            });
                        }
                    }
                });
            }
        });
    </script>
@endpush
