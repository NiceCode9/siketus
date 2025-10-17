@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Siswa</h3>
                        <button type="button" class="btn btn-primary float-right" id="add-siswa-btn">
                            <i class="fas fa-plus"></i> Tambah Siswa
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="siswa-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
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
    <div class="modal fade" id="siswa-modal" tabindex="-1" role="dialog" aria-labelledby="siswa-modal-label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="siswa-modal-label">Tambah Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="siswa-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="siswa_id">

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Data Siswa</h6>
                                <div class="form-group">
                                    <label for="nisn">NISN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nisn" name="nisn" required>
                                    <div class="invalid-feedback" id="nisn-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama Siswa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                    <div class="invalid-feedback" id="nama-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="current_class_id">Kelas</label>
                                    <select class="form-control" id="current_class_id" name="current_class_id">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="current_class_id-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    <div class="invalid-feedback" id="status-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Data Akun</h6>
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required
                                        readonly>
                                    <small class="form-text text-muted">Otomatis terisi dari NISN</small>
                                    <div class="invalid-feedback" id="username-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="default@example.com" required>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="password" name="password" required
                                        readonly>
                                    <small class="form-text text-muted">Otomatis terisi dari NISN (minimal 6
                                        karakter)</small>
                                    <div class="invalid-feedback" id="password-error"></div>
                                </div>
                                <div class="form-group" style="display: none;">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="text" class="form-control" id="password_confirmation"
                                        name="password_confirmation" readonly>
                                </div>
                            </div>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-modal-label">Detail Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Data Siswa</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">NISN</th>
                                    <td id="view-nisn">-</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td id="view-nama">-</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td id="view-kelas">-</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="view-status">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Data Akun</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Username</th>
                                    <td id="view-username">-</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td id="view-email">-</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>Siswa</td>
                                </tr>
                            </table>
                        </div>
                    </div>
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

        .badge {
            font-size: 0.75rem;
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
            var table = $('#siswa-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('siswa.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nisn',
                        name: 'nisn'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
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
            $('#add-siswa-btn').click(function() {
                $('#siswa-form')[0].reset();
                $('#siswa_id').val('');
                $('#siswa-modal-label').text('Tambah Siswa');
                $('#password').prop('required', true);
                $('#siswa-modal').modal('show');
                clearValidationErrors();
            });

            // View button click
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('siswa') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        let namaKelas = response.siswa.current_class ? response.siswa
                            .current_class.tingkat + '-' + response.siswa.current_class.jurusan
                            .kode_jurusan + '-' + response.siswa.current_class.nama_kelas : '-';
                        $('#view-nisn').text(response.siswa.nisn);
                        $('#view-nama').text(response.siswa.nama);
                        $('#view-kelas').text(namaKelas);
                        $('#view-status').text(response.siswa.status);

                        if (response.akun) {
                            $('#view-username').text(response.akun.username);
                            $('#view-email').text(response.akun.email);
                        } else {
                            $('#view-username').text('-');
                            $('#view-email').text('-');
                        }

                        $('#view-modal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data siswa.'
                        });
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('siswa') }}/" + id + "/edit",
                    type: "GET",
                    success: function(response) {
                        $('#siswa_id').val(response.siswa.id);
                        $('#nisn').val(response.siswa.nisn);
                        $('#nama').val(response.siswa.nama);
                        $('#current_class_id').val(response.siswa.current_class_id);
                        $('#status').val(response.siswa.status);

                        if (response.akun) {
                            $('#username').val(response.akun.username);
                            $('#email').val(response.akun.email);
                        }

                        $('#password').prop('required', false);
                        $('#siswa-modal-label').text('Edit Siswa');
                        $('#siswa-modal').modal('show');
                        clearValidationErrors();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data siswa.'
                        });
                    }
                });
            });

            // Save form (create/update)
            $('#siswa-form').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var id = $('#siswa_id').val();
                var url = id ? "{{ url('siswa') }}/" + id : "{{ route('siswa.store') }}";
                var method = id ? 'PUT' : 'POST';

                // Remove password confirmation from form data
                formData.delete('password_confirmation');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            $('#siswa-modal').modal('hide');
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
                    text: "Data siswa " + nama + " dan akunnya akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('siswa') }}/" + id,
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

            // NISN keyup event untuk auto-fill username dan password
            $('#nisn').on('keyup', function() {
                var nisn = $(this).val();
                if (nisn) {
                    // Set username sama dengan NISN
                    $('#username').val(nisn);

                    // Set password sama dengan NISN (minimal 6 karakter)
                    // Jika NISN kurang dari 6 karakter, tambahkan '123456' di belakangnya
                    var password = nisn;
                    // if (password.length < 6) {
                    //     password = password + '123456'.substring(0, 6 - password.length);
                    // }
                    $('#password').val(password);
                    $('#password_confirmation').val(password);
                } else {
                    // Jika NISN kosong, kosongkan juga username dan password
                    $('#username').val('');
                    $('#password').val('');
                    $('#password_confirmation').val('');
                }
            });

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
            $('#siswa-modal').on('hidden.bs.modal', function() {
                $('#siswa-form')[0].reset();
                $('#password').prop('required', true);
                clearValidationErrors();
            });
        });
    </script>
@endpush
