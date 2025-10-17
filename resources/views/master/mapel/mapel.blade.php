@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Mata Pelajaran</h3>
                        <button type="button" class="btn btn-primary float-right" id="add-mapel-btn">
                            <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="mapel-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode Pelajaran</th>
                                        <th>Nama Mata Pelajaran</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Guru</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="mapel-modal" tabindex="-1" role="dialog" aria-labelledby="mapel-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapel-modal-label">Tambah Mata Pelajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="mapel-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="mapel_id">

                        <div class="form-group">
                            <label for="kode_pelajaran">Kode Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_pelajaran" name="kode_pelajaran" required>
                            <small class="form-text text-muted">Kode unik untuk mata pelajaran</small>
                            <div class="invalid-feedback" id="kode_pelajaran-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="nama_mapel">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" required>
                            <div class="invalid-feedback" id="nama_mapel-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                placeholder="Deskripsi singkat tentang mata pelajaran"></textarea>
                            <div class="invalid-feedback" id="deskripsi-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="save-btn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="view-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-modal-label">Detail Mata Pelajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Kode Pelajaran</th>
                            <td id="view-kode-pelajaran">-</td>
                        </tr>
                        <tr>
                            <th>Nama Mata Pelajaran</th>
                            <td id="view-nama-mapel">-</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td id="view-deskripsi">-</td>
                        </tr>
                        <tr>
                            <th>Jumlah Guru Pengajar</th>
                            <td id="view-jumlah-guru">-</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .text-wrap {
            white-space: normal !important;
            word-wrap: break-word;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#mapel-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.mapel.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'kode_pelajaran',
                        name: 'kode_pelajaran',
                        width: '15%'
                    },
                    {
                        data: 'nama_mapel',
                        name: 'nama_mapel',
                        width: '20%'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                        width: '30%',
                        className: 'text-wrap'
                    },
                    {
                        data: 'guru_count',
                        name: 'guru_count',
                        width: '15%',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    }
                ],
                order: [
                    [1, 'asc']
                ] // Order by kode_pelajaran by default
            });

            // Reset form and show modal for add
            $('#add-mapel-btn').click(function() {
                $('#mapel-form')[0].reset();
                $('#mapel_id').val('');
                $('#mapel-modal-label').text('Tambah Mata Pelajaran');
                $('#mapel-modal').modal('show');
                clearValidationErrors();
            });

            // View button click
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.mapel.show', ':id') }}".replace(':id', id),
                    type: "GET",
                    success: function(response) {
                        $('#view-kode-pelajaran').text(response.mapel.kode_pelajaran);
                        $('#view-nama-mapel').text(response.mapel.nama_mapel);
                        $('#view-deskripsi').text(response.mapel.deskripsi || '-');
                        $('#view-jumlah-guru').text(response.guru_count + ' guru');

                        $('#view-modal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data mata pelajaran.'
                        });
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.mapel.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    success: function(response) {
                        $('#mapel_id').val(response.id);
                        $('#kode_pelajaran').val(response.kode_pelajaran);
                        $('#nama_mapel').val(response.nama_mapel);
                        $('#deskripsi').val(response.deskripsi);

                        $('#mapel-modal-label').text('Edit Mata Pelajaran');
                        $('#mapel-modal').modal('show');
                        clearValidationErrors();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data mata pelajaran.'
                        });
                    }
                });
            });

            // Save form (create/update)
            $('#mapel-form').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var id = $('#mapel_id').val();
                var url = id ? "{{ route('admin.mapel.update', ':id') }}".replace(':id', id) :
                    "{{ route('admin.mapel.store') }}";
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#mapel-modal').modal('hide');
                            table.draw();

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || xhr.responseJSON.message;
                            showValidationErrors(errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data mata pelajaran \"" + nama + "\" akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.mapel.destroy', ':id') }}".replace(':id',
                                id),
                            type: "DELETE",
                            success: function(response) {
                                if (response.status) {
                                    table.draw();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan saat menghapus data.'
                                });
                            }
                        });
                    }
                });
            });

            // Clear validation errors
            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Show validation errors
            function showValidationErrors(errors) {
                clearValidationErrors();

                if (typeof errors === 'string') {
                    // Single error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Validasi!',
                        text: errors
                    });
                } else {
                    // Multiple field errors
                    $.each(errors, function(field, messages) {
                        var input = $('[name="' + field + '"]');
                        var errorElement = $('#' + field + '-error');

                        input.addClass('is-invalid');
                        errorElement.text(messages[0]);
                    });
                }
            }

            // Close modal and reset form
            $('#mapel-modal').on('hidden.bs.modal', function() {
                $('#mapel-form')[0].reset();
                clearValidationErrors();
            });

            // Auto uppercase for kode_pelajaran
            $('#kode_pelajaran').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>
@endpush
