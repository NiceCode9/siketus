@extends('layouts.app')

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
                        <table class="table table-bordered table-striped table-hover text-nowrap">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" width="5%">No</th>
                                    <th scope="col">Tahun Akademik</th>
                                    <th scope="col">Tanggal Mulai</th>
                                    <th scope="col">Tanggal Selesai</th>
                                    <th scope="col">Status Aktif</th>
                                    <th scope="col" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tahunAkademik as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->nama_tahun_akademik }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->locale('id')->isoFormat('D MMMM YYYY') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY') }}
                                        </td>
                                        <td>
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
                                        <td colspan="6" class="text-center">Tidak ada data tahun akademik</td>
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
        <div class="modal-dialog" role="document">
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
                            <label for="nama_tahun_akademik" class="form-label">Nama Tahun Akademik <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_tahun_akademik" name="nama_tahun_akademik"
                                required>
                            <div class="invalid-feedback" id="nama_tahun_akademik_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                            <div class="invalid-feedback" id="tanggal_mulai_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                            <div class="invalid-feedback" id="tanggal_selesai_error"></div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status_aktif"
                                    name="status_aktif" value="1">
                                <label class="custom-control-label" for="status_aktif">Status Aktif</label>
                                <small class="form-text text-muted">Jika dicentang, tahun akademik lain akan otomatis
                                    dinonaktifkan</small>
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
            // CSRF Token untuk Ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Reset form dan validasi saat modal ditutup
            $('#tahunAkademikModal').on('hidden.bs.modal', function() {
                $('#tahunAkademikForm')[0].reset();
                $('#tahun_akademik_id').val('');
                $('#tahunAkademikModalLabel').text('Tambah Tahun Akademik');
                $('#status_aktif').prop('checked', false);
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#saveBtn').prop('disabled', false).find('.spinner-border').addClass('d-none');
            });

            // Simpan data (Create/Update)
            $('#tahunAkademikForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const tahunAkademikId = $('#tahun_akademik_id').val();
                const url = tahunAkademikId ? `/admin/tahun-akademik/${tahunAkademikId}` :
                    '/admin/tahun-akademik';
                const method = tahunAkademikId ? 'PUT' : 'POST';

                // Reset validasi
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
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#saveBtn').prop('disabled', false).find('.spinner-border').addClass(
                            'd-none');

                        if (xhr.status === 422) {
                            // Validasi error
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                const input = $('#' + key);
                                const errorField = $('#' + key + '_error');
                                input.addClass('is-invalid');
                                errorField.text(value[0]);
                            });
                        } else {
                            let errorMessage = 'Terjadi kesalahan!';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage
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
                    $('#tanggal_mulai').val(data.tanggal_mulai);
                    $('#tanggal_selesai').val(data.tanggal_selesai);
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

            // Delete data dengan konfirmasi SweetAlert
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
                            beforeSend: function() {
                                // Tampilkan loading
                                $('.delete-btn[data-id="' + id + '"]').prop('disabled',
                                        true)
                                    .html('<i class="fas fa-spinner fa-spin"></i>');
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan!';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errorMessage
                                });
                            },
                            complete: function() {
                                $('.delete-btn[data-id="' + id + '"]').prop('disabled',
                                        false)
                                    .html('<i class="fas fa-trash"></i>');
                            }
                        });
                    }
                });
            });

            // Validasi tanggal
            $('#tanggal_mulai, #tanggal_selesai').on('change', function() {
                const mulai = new Date($('#tanggal_mulai').val());
                const selesai = new Date($('#tanggal_selesai').val());

                if (mulai && selesai && mulai >= selesai) {
                    $('#tanggal_selesai').addClass('is-invalid');
                    $('#tanggal_selesai_error').text('Tanggal selesai harus setelah tanggal mulai');
                } else {
                    $('#tanggal_selesai').removeClass('is-invalid');
                    $('#tanggal_selesai_error').text('');
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>
@endpush
