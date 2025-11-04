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

            @if (!empty($nilaiBerlebih))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Peringatan:</strong> Terdapat
                    {{ count($nilaiBerlebih, COUNT_RECURSIVE) - count($nilaiBerlebih) }} nilai yang melebihi nilai guru.
                    Silakan koreksi nilai tersebut.
                </div>
            @endif

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
                                        $nilaiInfo = $nilaiData[$gk->id][$jenis->id] ?? [
                                            'nilai_siswa' => '',
                                            'nilai_guru' => 0,
                                            'is_overflow' => false,
                                        ];
                                        $nilai = $nilaiInfo['nilai_siswa'];
                                        $nilaiGuru = $nilaiInfo['nilai_guru'];
                                        $isOverflow = $nilaiInfo['is_overflow'];

                                        if ($nilai !== '' && $nilai !== null) {
                                            $totalNilai += $nilai;
                                            $countNilai++;
                                        }
                                    @endphp
                                    <td>
                                        <input type="hidden" name="semester[{{ $gk->id }}][{{ $jenis->id }}]"
                                            value="{{ $jenis->semester }}">
                                        <input type="number" name="nilai[{{ $gk->id }}][{{ $jenis->id }}]"
                                            class="form-control form-control-sm text-center nilai-input {{ $isOverflow ? 'is-invalid' : '' }}"
                                            value="{{ $nilai }}" min="0" max="100" step="0.01"
                                            placeholder="0-100" data-row="{{ $gk->id }}"
                                            data-guru-kelas="{{ $gk->id }}" data-jenis-ujian="{{ $jenis->id }}"
                                            data-nilai-guru="{{ $nilaiGuru }}">

                                        @if ($isOverflow)
                                            <small class="error-message text-danger overflow-warning"
                                                data-error="{{ $gk->id }}-{{ $jenis->id }}">
                                                <i class="fas fa-exclamation-circle"></i> Nilai melebihi nilai guru
                                                ({{ $nilaiGuru }})
                                            </small>
                                        @else
                                            <small class="error-message text-danger d-none"
                                                data-error="{{ $gk->id }}-{{ $jenis->id }}"></small>
                                        @endif
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

        .overflow-warning {
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Simpan nilai awal untuk tracking perubahan
            var originalValues = {};
            $('.nilai-input').each(function() {
                var guruKelasId = $(this).data('guru-kelas');
                var jenisUjianId = $(this).data('jenis-ujian');
                var key = guruKelasId + '-' + jenisUjianId;
                originalValues[key] = $(this).val();
            });

            // Fungsi debounce untuk menunda eksekusi sampai user berhenti mengetik
            function debounce(func, delay) {
                let timer;
                return function(...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(this, args), delay);
                };
            }

            // Validasi real-time dengan debounce
            $('.nilai-input').on('input', debounce(function() {
                var input = $(this);
                var guruKelasId = input.data('guru-kelas');
                var jenisUjianId = input.data('jenis-ujian');
                var nilaiSiswa = parseFloat(input.val());
                var errorMsg = $(`small[data-error="${guruKelasId}-${jenisUjianId}"]`);

                // Jika input kosong atau bukan angka valid, hapus error
                if (input.val() === '' || isNaN(nilaiSiswa)) {
                    input.removeClass('is-invalid');
                    errorMsg.addClass('d-none').removeClass('overflow-warning').text('');
                    return;
                }

                // Simpan timestamp request agar hasil lama tidak menimpa hasil baru
                var requestTime = Date.now();
                input.data('last-request', requestTime);

                $.ajax({
                    url: '{{ route('siswa.penilaian.get-nilai-guru') }}',
                    method: 'GET',
                    data: {
                        guru_kelas_id: guruKelasId,
                        jenis_ujian_id: jenisUjianId
                    },
                    success: function(response) {
                        // Cek apakah ini masih request terbaru
                        if (input.data('last-request') !== requestTime) return;

                        if (response.success && response.nilai !== null) {
                            var nilaiGuru = parseFloat(response.nilai);

                            // Update data-nilai-guru
                            input.attr('data-nilai-guru', nilaiGuru);

                            if (nilaiSiswa > nilaiGuru) {
                                input.addClass('is-invalid');
                                errorMsg.removeClass('d-none')
                                    .addClass('overflow-warning')
                                    .html(
                                        `<i class="fas fa-exclamation-circle"></i> Nilai melebihi nilai guru (${nilaiGuru})`
                                        );
                            } else {
                                input.removeClass('is-invalid');
                                errorMsg.addClass('d-none').removeClass('overflow-warning')
                                    .text('');
                            }
                        } else {
                            // Jika guru belum input nilai, boleh input bebas
                            input.removeClass('is-invalid');
                            errorMsg.addClass('d-none').removeClass('overflow-warning')
                                .text('');
                        }
                    },
                    error: function() {
                        console.error('Gagal memvalidasi nilai');
                    }
                });
            }, 400)); // 400ms delay debounce

            // Hitung rata-rata
            $('.nilai-input').on('input', function() {
                var row = $(this).data('row');
                var total = 0;
                var count = 0;

                $(`input[data-row="${row}"]`).each(function() {
                    var val = parseFloat($(this).val());
                    if (!isNaN(val) && $(this).val() !== '') {
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

                // Cek apakah ada nilai yang BERUBAH atau BARU diisi
                var hasChanges = false;
                $('.nilai-input').each(function() {
                    var guruKelasId = $(this).data('guru-kelas');
                    var jenisUjianId = $(this).data('jenis-ujian');
                    var key = guruKelasId + '-' + jenisUjianId;
                    var currentVal = $(this).val();
                    var originalVal = originalValues[key] || '';

                    // Cek jika ada perubahan atau nilai baru
                    if (currentVal !== '' && currentVal !== originalVal) {
                        hasChanges = true;
                        return false; // break loop
                    }
                });

                if (!hasChanges) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Tidak ada perubahan nilai yang perlu disimpan!',
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
                // Buat FormData baru hanya dengan nilai yang berubah atau baru diisi
                var formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());

                var guruKelasIds = [];
                var hasData = false;

                $('.nilai-input').each(function() {
                    var guruKelasId = $(this).data('guru-kelas');
                    var jenisUjianId = $(this).data('jenis-ujian');
                    var key = guruKelasId + '-' + jenisUjianId;
                    var currentVal = $(this).val();
                    var originalVal = originalValues[key] || '';

                    // Hanya kirim yang berubah atau baru diisi
                    if (currentVal !== '' && currentVal !== originalVal) {
                        if (!guruKelasIds.includes(guruKelasId)) {
                            guruKelasIds.push(guruKelasId);
                        }

                        formData.append(`nilai[${guruKelasId}][${jenisUjianId}]`, currentVal);

                        // Ambil semester dari hidden input
                        var semester = $(`input[name="semester[${guruKelasId}][${jenisUjianId}]"]`).val();
                        formData.append(`semester[${guruKelasId}][${jenisUjianId}]`, semester);

                        hasData = true;
                    }
                });

                // Tambahkan guru_kelas_id
                guruKelasIds.forEach(function(id) {
                    formData.append('guru_kelas_id[]', id);
                });

                if (!hasData) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Tidak ada data yang perlu disimpan!',
                    });
                    return;
                }

                var btnSimpan = $('#btnSimpan');
                btnSimpan.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: '{{ route('siswa.penilaian.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
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
                            var response = xhr.responseJSON;

                            if (response.errors) {
                                var errorMessages = [];

                                // Tampilkan error pada field yang bermasalah
                                $.each(response.errors, function(guruKelasId, jenisUjianErrors) {
                                    $.each(jenisUjianErrors, function(jenisUjianId, error) {
                                        var input = $(
                                            `input[data-guru-kelas="${guruKelasId}"][data-jenis-ujian="${jenisUjianId}"]`
                                        );
                                        var errorMsg = $(
                                            `small[data-error="${guruKelasId}-${jenisUjianId}"]`
                                        );

                                        input.addClass('is-invalid');
                                        errorMsg.removeClass('d-none').addClass(
                                            'overflow-warning').text(error.message);
                                        errorMessages.push(error.message);
                                    });
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal',
                                    html: response.message + '<br><small>' + errorMessages
                                        .join('<br>') + '</small>',
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal',
                                    text: response.message || 'Harap isi minimal satu nilai!',
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan nilai',
                            });
                        }
                    }
                });
            }
        });
    </script>
@endpush
