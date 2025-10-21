@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Jenis Ujian</h3>
                        <button type="button" class="btn btn-primary float-right" id="add-jenis-ujian-btn">
                            <i class="fas fa-plus"></i> Tambah Jenis Ujian
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="jenis-ujian-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Jenis Ujian</th>
                                        <th>Tahun Akademik</th>
                                        <th>Status Tahun</th>
                                        <th>Deskripsi</th>
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
    <div class="modal fade" id="jenis-ujian-modal" tabindex="-1" role="dialog" aria-labelledby="jenis-ujian-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jenis-ujian-modal-label">Tambah Jenis Ujian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="jenis-ujian-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="jenis_ujian_id">

                        <div class="form-group">
                            <label for="tahun_akademik_id">Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-control" id="tahun_akademik_id" name="tahun_akademik_id" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach ($tahunAkademik as $item)
                                    <option value="{{ $item->id }}" data-status="{{ $item->status }}">
                                        {{ $item->nama_tahun_akademik }}
                                        @if ($item->status_aktif)
                                            (Aktif)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="tahun_akademik_id-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="nama_jenis_ujian">Nama Jenis Ujian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_jenis_ujian" name="nama_jenis_ujian"
                                placeholder="Contoh: UTS, UAS, Ujian Praktikum" required>
                            <div class="invalid-feedback" id="nama_jenis_ujian-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                placeholder="Deskripsi singkat tentang jenis ujian"></textarea>
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
                    <h5 class="modal-title" id="view-modal-label">Detail Jenis Ujian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Nama Jenis Ujian</th>
                            <td id="view-nama-jenis-ujian">-</td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td id="view-tahun-akademik">-</td>
                        </tr>
                        <tr>
                            <th>Status Tahun</th>
                            <td id="view-status-tahun">-</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td id="view-deskripsi">-</td>
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

        .badge {
            font-size: 0.75rem;
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
            var table = $('#jenis-ujian-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.jenis-ujian.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'nama_jenis_ujian',
                        name: 'nama_jenis_ujian',
                        width: '20%'
                    },
                    {
                        data: 'tahun_akademik',
                        name: 'tahunAkademik.nama_tahun_akademik',
                        width: '20%'
                    },
                    {
                        data: 'status_tahun',
                        name: 'tahunAkademik.status_aktif',
                        width: '15%'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                        width: '25%',
                        className: 'text-wrap'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    }
                ],
            });

            // Reset form and show modal for add
            $('#add-jenis-ujian-btn').click(function() {
                $('#jenis-ujian-form')[0].reset();
                $('#jenis_ujian_id').val('');
                $('#jenis-ujian-modal-label').text('Tambah Jenis Ujian');
                $('#jenis-ujian-modal').modal('show');
                clearValidationErrors();
            });

            // View button click
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/jenis-ujian') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        $('#view-nama-jenis-ujian').text(response.jenis_ujian.nama_jenis_ujian);
                        $('#view-tahun-akademik').text(response.tahun_akademik
                            .nama_tahun_akademik);

                        // Status badge
                        var status = response.tahun_akademik.status_aktif;
                        var badgeClass = status ? 'success' : 'secondary';
                        var badgeHtml = '<span class="badge badge-' + badgeClass + '">' +
                            (status ? 'Aktif' : 'Tidak Aktif') + '</span>';
                        $('#view-status-tahun').html(badgeHtml);

                        $('#view-deskripsi').text(response.jenis_ujian.deskripsi || '-');

                        $('#view-modal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data jenis ujian.'
                        });
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/jenis-ujian') }}/" + id + "/edit",
                    type: "GET",
                    success: function(response) {
                        $('#jenis_ujian_id').val(response.id);
                        $('#tahun_akademik_id').val(response.tahun_akademik_id);
                        $('#nama_jenis_ujian').val(response.nama_jenis_ujian);
                        $('#deskripsi').val(response.deskripsi);

                        $('#jenis-ujian-modal-label').text('Edit Jenis Ujian');
                        $('#jenis-ujian-modal').modal('show');
                        clearValidationErrors();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data jenis ujian.'
                        });
                    }
                });
            });

            // Save form (create/update)
            $('#jenis-ujian-form').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var id = $('#jenis_ujian_id').val();
                var url = id ? "{{ url('admin/jenis-ujian') }}/" + id :
                    "{{ route('admin.jenis-ujian.store') }}";
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#jenis-ujian-modal').modal('hide');
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
                    text: "Data jenis ujian \"" + nama + "\" akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/jenis-ujian') }}/" + id,
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
            $('#jenis-ujian-modal').on('hidden.bs.modal', function() {
                $('#jenis-ujian-form')[0].reset();
                clearValidationErrors();
            });
        });
    </script>
@endpush
