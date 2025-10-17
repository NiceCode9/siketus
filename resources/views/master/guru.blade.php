@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Guru</h3>
                        <button type="button" class="btn btn-primary float-right" id="add-guru-btn">
                            <i class="fas fa-plus"></i> Tambah Guru
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="guru-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIP</th>
                                        <th>Nama</th>
                                        <th>Bidang Keahlian</th>
                                        <th>Biografi</th>
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
    <div class="modal fade" id="guru-modal" tabindex="-1" role="dialog" aria-labelledby="guru-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guru-modal-label">Tambah Guru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="guru-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="guru_id">
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" required>
                            <div class="invalid-feedback" id="nip-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Guru</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                            <div class="invalid-feedback" id="nama-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="bidang_keahlian">Bidang Keahlian</label>
                            <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian" required>
                            <div class="invalid-feedback" id="bidang_keahlian-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="biografi">Biografi</label>
                            <textarea class="form-control" id="biografi" name="biografi" rows="3"></textarea>
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
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
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
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#guru-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.guru.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'bidang_keahlian',
                        name: 'bidang_keahlian'
                    },
                    {
                        data: 'biografi',
                        name: 'biografi'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            // Reset form and show modal for add
            $('#add-guru-btn').click(function() {
                $('#guru-form')[0].reset();
                $('#guru_id').val('');
                $('#guru-modal-label').text('Tambah Guru');
                $('#guru-modal').modal('show');
                clearValidationErrors();
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.guru.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    success: function(response) {
                        $('#guru_id').val(response.id);
                        $('#nip').val(response.nip);
                        $('#nama').val(response.nama);
                        $('#bidang_keahlian').val(response.bidang_keahlian);
                        $('#biografi').val(response.biografi);

                        $('#guru-modal-label').text('Edit Guru');
                        $('#guru-modal').modal('show');
                        clearValidationErrors();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data guru.'
                        });
                    }
                });
            });

            // Save form (create/update)
            $('#guru-form').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var id = $('#guru_id').val();
                var url = id ? "{{ route('admin.guru.edit', ':id') }}".replace(':id', id) :
                    "{{ route('admin.guru.store') }}";
                var method = id ? 'PUT' : 'POST';

                if (id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            $('#guru-modal').modal('hide');
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
                    text: "Data guru " + nama + " akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.guru.destroy', ':id') }}".replace(':id',
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
            $('#guru-modal').on('hidden.bs.modal', function() {
                $('#guru-form')[0].reset();
                clearValidationErrors();
            });
        });
    </script>
@endpush
