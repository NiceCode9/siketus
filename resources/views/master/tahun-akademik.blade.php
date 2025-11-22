@extends('layouts.app', ['pageTitle' => 'Tahun Akademik'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Tahun Akademik</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tahunAkademikModal">
                            <i class="fas fa-plus"></i> Tambah Tahun Akademik
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" width="5%">No</th>
                                    <th scope="col">Tahun Akademik</th>
                                    <th scope="col">Semester Ganjil</th>
                                    <th scope="col">Semester Genap</th>
                                    <th scope="col">Semester Saat Ini</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tahunAkademik as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->nama_tahun_akademik }}</strong>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $item->tanggal_mulai_ganjil ? $item->tanggal_mulai_ganjil->format('d M Y') : '-' }}
                                                <br>s/d<br>
                                                {{ $item->tanggal_selesai_ganjil ? $item->tanggal_selesai_ganjil->format('d M Y') : '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $item->tanggal_mulai_genap ? $item->tanggal_mulai_genap->format('d M Y') : '-' }}
                                                <br>s/d<br>
                                                {{ $item->tanggal_selesai_genap ? $item->tanggal_selesai_genap->format('d M Y') : '-' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status_aktif)
                                                <span
                                                    class="badge badge-{{ $item->isGanjil() ? 'info' : 'primary' }} badge-lg">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ $item->semester_label }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status_aktif)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-warning edit-btn" data-id="{{ $item->id }}"
                                                    data-toggle="modal" data-target="#tahunAkademikModal" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger delete-btn" data-id="{{ $item->id }}"
                                                    data-name="{{ $item->nama_tahun_akademik }}" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data tahun akademik</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tahunAkademikModal" tabindex="-1" role="dialog" aria-labelledby="tahunAkademikModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tahunAkademikModalLabel">Tambah Tahun Akademik</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="tahunAkademikForm">
                    <div class="modal-body">
                        <input type="hidden" id="tahun_akademik_id" name="id">

                        <div class="form-group">
                            <label for="nama_tahun_akademik" class="form-label">
                                Nama Tahun Akademik <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama_tahun_akademik" name="nama_tahun_akademik"
                                placeholder="Contoh: 2024/2025" required>
                            <div class="invalid-feedback" id="nama_tahun_akademik_error"></div>
                        </div>

                        <div class="row">
                            <!-- Semester Ganjil -->
                            <div class="col-md-6">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-calendar"></i> Semester Ganjil
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tanggal_mulai_ganjil">Tanggal Mulai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal_mulai_ganjil"
                                                name="tanggal_mulai_ganjil" required>
                                            <div class="invalid-feedback" id="tanggal_mulai_ganjil_error"></div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="tanggal_selesai_ganjil">Tanggal Selesai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal_selesai_ganjil"
                                                name="tanggal_selesai_ganjil" required>
                                            <div class="invalid-feedback" id="tanggal_selesai_ganjil_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Semester Genap -->
                            <div class="col-md-6">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-calendar"></i> Semester Genap
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tanggal_mulai_genap">Tanggal Mulai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal_mulai_genap"
                                                name="tanggal_mulai_genap" required>
                                            <div class="invalid-feedback" id="tanggal_mulai_genap_error"></div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="tanggal_selesai_genap">Tanggal Selesai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal_selesai_genap"
                                                name="tanggal_selesai_genap" required>
                                            <div class="invalid-feedback" id="tanggal_selesai_genap_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status_aktif"
                                    name="status_aktif" value="1">
                                <label class="custom-control-label" for="status_aktif">Status Aktif</label>
                                <small class="form-text text-muted">
                                    Jika dicentang, tahun akademik lain akan otomatis dinonaktifkan.
                                    Semester akan ditentukan otomatis berdasarkan tanggal hari ini.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Reset form saat modal ditutup
            $('#tahunAkademikModal').on('hidden.bs.modal', function() {
                $('#tahunAkademikForm')[0].reset();
                $('#tahun_akademik_id').val('');
                $('#tahunAkademikModalLabel').text('Tambah Tahun Akademik');
                $('#status_aktif').prop('checked', false);
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#saveBtn').prop('disabled', false).find('.spinner-border').addClass('d-none');
            });

            // Simpan data
            $('#tahunAkademikForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const tahunAkademikId = $('#tahun_akademik_id').val();
                const url = tahunAkademikId ? `/admin/tahun-akademik/${tahunAkademikId}` :
                    '/admin/tahun-akademik';

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#saveBtn').prop('disabled', true).find('.spinner-border').removeClass('d-none');

                if (tahunAkademikId) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#tahunAkademikModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    },
                    error: function(xhr) {
                        $('#saveBtn').prop('disabled', false).find('.spinner-border').addClass(
                            'd-none');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '_error').text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                            });
                        }
                    }
                });
            });

            // Edit data
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');

                $.get(`/admin/tahun-akademik/${id}`, function(data) {
                    $('#tahun_akademik_id').val(data.id);
                    $('#nama_tahun_akademik').val(data.nama_tahun_akademik);

                    // Set tanggal semester ganjil
                    if (data.tanggal_mulai_ganjil) {
                        $('#tanggal_mulai_ganjil').val(data.tanggal_mulai_ganjil.split('T')[0]);
                    }
                    if (data.tanggal_selesai_ganjil) {
                        $('#tanggal_selesai_ganjil').val(data.tanggal_selesai_ganjil.split('T')[0]);
                    }

                    // Set tanggal semester genap
                    if (data.tanggal_mulai_genap) {
                        $('#tanggal_mulai_genap').val(data.tanggal_mulai_genap.split('T')[0]);
                    }
                    if (data.tanggal_selesai_genap) {
                        $('#tanggal_selesai_genap').val(data.tanggal_selesai_genap.split('T')[0]);
                    }

                    $('#status_aktif').prop('checked', data.status_aktif);
                    $('#tahunAkademikModalLabel').text('Edit Tahun Akademik');
                }).fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memuat data tahun akademik'
                    });
                });
            });

            // Delete data
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus tahun akademik "${name}"`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/tahun-akademik/${id}`,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan!'
                                });
                            }
                        });
                    }
                });
            });

            // Validasi tanggal otomatis
            $('#tanggal_mulai_ganjil, #tanggal_selesai_ganjil').on('change', function() {
                const mulai = $('#tanggal_mulai_ganjil').val();
                const selesai = $('#tanggal_selesai_ganjil').val();

                if (mulai && selesai && new Date(mulai) >= new Date(selesai)) {
                    $('#tanggal_selesai_ganjil').addClass('is-invalid');
                    $('#tanggal_selesai_ganjil_error').text('Tanggal selesai harus setelah tanggal mulai');
                } else {
                    $('#tanggal_selesai_ganjil').removeClass('is-invalid');
                    $('#tanggal_selesai_ganjil_error').text('');
                }
            });

            $('#tanggal_selesai_ganjil, #tanggal_mulai_genap').on('change', function() {
                const selesaiGanjil = $('#tanggal_selesai_ganjil').val();
                const mulaiGenap = $('#tanggal_mulai_genap').val();

                if (selesaiGanjil && mulaiGenap && new Date(selesaiGanjil) >= new Date(mulaiGenap)) {
                    $('#tanggal_mulai_genap').addClass('is-invalid');
                    $('#tanggal_mulai_genap_error').text(
                        'Tanggal mulai semester genap harus setelah semester ganjil selesai');
                } else {
                    $('#tanggal_mulai_genap').removeClass('is-invalid');
                    $('#tanggal_mulai_genap_error').text('');
                }
            });

            $('#tanggal_mulai_genap, #tanggal_selesai_genap').on('change', function() {
                const mulai = $('#tanggal_mulai_genap').val();
                const selesai = $('#tanggal_selesai_genap').val();

                if (mulai && selesai && new Date(mulai) >= new Date(selesai)) {
                    $('#tanggal_selesai_genap').addClass('is-invalid');
                    $('#tanggal_selesai_genap_error').text('Tanggal selesai harus setelah tanggal mulai');
                } else {
                    $('#tanggal_selesai_genap').removeClass('is-invalid');
                    $('#tanggal_selesai_genap_error').text('');
                }
            });
        });
    </script>
@endpush

@push('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
        }

        .card-outline {
            border-top-width: 3px;
        }
    </style>
@endpush
