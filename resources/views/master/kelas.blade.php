@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Kelas</h3>
                        <button type="button" class="btn btn-primary float-right" id="add-kelas-btn">
                            <i class="fas fa-plus"></i> Tambah Kelas
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="kelas-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Kelas</th>
                                        <th>Jurusan</th>
                                        <th>Tingkat</th>
                                        <th>Jumlah Siswa</th>
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
    <div class="modal fade" id="kelas-modal" tabindex="-1" role="dialog" aria-labelledby="kelas-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kelas-modal-label">Tambah Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="kelas-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="kelas_id">

                        <div class="form-group">
                            <label for="jurusan_id">Jurusan <span class="text-danger">*</span></label>
                            <select class="form-control" id="jurusan_id" name="jurusan_id" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach ($jurusan as $item)
                                    <option value="{{ $item->id }}" data-kode="{{ $item->kode_jurusan }}">
                                        {{ $item->nama_jurusan }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="jurusan_id-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="tingkat">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-control" id="tingkat" name="tingkat" required>
                                <option value="">-- Pilih Tingkat --</option>
                                @foreach ($tingkat as $item)
                                    <option value="{{ $item }}">Kelas {{ $item }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="tingkat-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas"
                                placeholder="Contoh: A, B, 1, 2" required>
                            <small class="form-text text-muted">Masukkan nama kelas (contoh: A, B, 1, 2)</small>
                            <div class="invalid-feedback" id="nama_kelas-error"></div>
                        </div>

                        <div class="form-group">
                            <label>Nama Lengkap Kelas:</label>
                            <div class="form-control-plaintext" id="nama-lengkap-preview">
                                <span class="text-muted">Pilih jurusan, tingkat, dan nama kelas untuk melihat preview</span>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-modal-label">Detail Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Nama Lengkap Kelas</th>
                            <td id="view-nama-lengkap">-</td>
                        </tr>
                        <tr>
                            <th>Jurusan</th>
                            <td id="view-jurusan">-</td>
                        </tr>
                        <tr>
                            <th>Tingkat</th>
                            <td id="view-tingkat">-</td>
                        </tr>
                        <tr>
                            <th>Nama Kelas</th>
                            <td id="view-nama-kelas">-</td>
                        </tr>
                        <tr>
                            <th>Jumlah Siswa</th>
                            <td id="view-jumlah-siswa">-</td>
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

        .form-control-plaintext {
            min-height: 38px;
            padding: 0.375rem 0.75rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
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
            var table = $('#kelas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.kelas.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'jurusan_nama',
                        name: 'jurusan_nama'
                    },
                    {
                        data: 'tingkat',
                        name: 'tingkat'
                    },
                    {
                        data: 'jumlah_siswa',
                        name: 'jumlah_siswa'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            // Update nama lengkap preview
            function updateNamaLengkapPreview() {
                var jurusan = $('#jurusan_id option:selected').data('kode');
                var tingkat = $('#tingkat').val();
                var namaKelas = $('#nama_kelas').val();

                if (jurusan && jurusan !== '-- Pilih Jurusan --' && tingkat && namaKelas) {
                    var jurusanNama = jurusan.split(' - ')[0]; // Get only jurusan name
                    $('#nama-lengkap-preview').html(tingkat + ' - ' + jurusanNama + ' - ' + namaKelas);
                } else {
                    $('#nama-lengkap-preview').html(
                        '<span class="text-muted">Pilih jurusan, tingkat, dan nama kelas untuk melihat preview</span>'
                    );
                }
            }

            // Event listeners for form changes
            $('#jurusan_id, #tingkat, #nama_kelas').on('change keyup', function() {
                updateNamaLengkapPreview();
            });

            // Reset form and show modal for add
            $('#add-kelas-btn').click(function() {
                $('#kelas-form')[0].reset();
                $('#kelas_id').val('');
                $('#kelas-modal-label').text('Tambah Kelas');
                $('#nama-lengkap-preview').html(
                    '<span class="text-muted">Pilih jurusan, tingkat, dan nama kelas untuk melihat preview</span>'
                );
                $('#kelas-modal').modal('show');
                clearValidationErrors();
            });

            // View button click
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/kelas') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        $('#view-nama-lengkap').text(response.kelas.tingkat + ' - ' + response
                            .jurusan.nama_jurusan + ' - ' + response.kelas.nama_kelas);
                        $('#view-jurusan').text(response.jurusan.nama_jurusan);
                        $('#view-tingkat').text('Kelas ' + response.kelas.tingkat);
                        $('#view-nama-kelas').text(response.kelas.nama_kelas);
                        $('#view-jumlah-siswa').text(response.siswa_count + ' siswa');

                        $('#view-modal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data kelas.'
                        });
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/kelas') }}/" + id + "/edit",
                    type: "GET",
                    success: function(response) {
                        $('#kelas_id').val(response.id);
                        $('#jurusan_id').val(response.jurusan_id);
                        $('#tingkat').val(response.tingkat);
                        $('#nama_kelas').val(response.nama_kelas);

                        // updateNamaLengkapPreview();

                        $('#kelas-modal-label').text('Edit Kelas');
                        $('#kelas-modal').modal('show');
                        clearValidationErrors();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data kelas.'
                        });
                    }
                });
            });

            // Save form (create/update)
            $('#kelas-form').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var id = $('#kelas_id').val();
                var url = id ? "{{ url('admin/kelas') }}/" + id : "{{ route('admin.kelas.store') }}";
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#kelas-modal').modal('hide');
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
                    text: "Data kelas " + nama + " akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/kelas') }}/" + id,
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
            $('#kelas-modal').on('hidden.bs.modal', function() {
                $('#kelas-form')[0].reset();
                clearValidationErrors();
            });
        });
    </script>
@endpush
