@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Kedisiplinan</h3>
                        <button class="btn btn-primary btn-sm float-right" data-target="#modal-kedisiplinan"
                            data-toggle="modal" id="btn-tambah">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-kedisiplinan">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Kedisiplinan</th>
                                        <th>Dibuat Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah/Edit -->
    <div class="modal fade" id="modal-kedisiplinan" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Data Kedisiplinan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form-kedisiplinan">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        @csrf
                        <div class="form-group">
                            <label for="jenis">Jenis Kedisiplinan</label>
                            <input type="text" class="form-control" id="jenis" name="jenis" required>
                            <div class="invalid-feedback" id="jenis-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // CSRF Token untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load data saat halaman dimuat
            loadData();

            // Tombol Tambah Data
            $('#btn-tambah').click(function() {
                $('#modal-kedisiplinan').modal('show');
                $('#form-kedisiplinan')[0].reset();
                $('#modal-title').text('Tambah Data Kedisiplinan');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
            });

            // Simpan Data (Create/Update)
            $('#form-kedisiplinan').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const id = $('#id').val();
                const url = id ? `{{ route('admin.kedisiplinan.update', ':id') }}`.replace(':id', id) :
                    '{{ route('admin.kedisiplinan.store') }}';
                const method = id ? 'PUT' : 'POST';

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
                        if (response.success) {
                            $('#modal-kedisiplinan').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadData();
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}-error`).text(value[0]);
                            });
                        }
                    }
                });
            });

            // Edit Data
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.get(`{{ route('admin.kedisiplinan.edit', ':id') }}`.replace(':id', id), function(
                    response) {
                    if (response.success) {
                        $('#modal-title').text('Edit Data Kedisiplinan');
                        $('#id').val(response.data.id);
                        $('#jenis').val(response.data.jenis);
                        $('#modal-kedisiplinan').modal('show');
                        $('.invalid-feedback').text('');
                        $('.form-control').removeClass('is-invalid');
                    }
                });
            });

            // Delete Data dengan SweetAlert2
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const jenis = $(this).data('jenis');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Data "${jenis}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/kedisiplinan/${id}`,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    loadData();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data.',
                                });
                            }
                        });
                    }
                });
            });

            // Fungsi untuk memuat data
            function loadData() {
                $.get('/admin/kedisiplinan', function(data) {
                    const tbody = $('#table-kedisiplinan tbody');
                    tbody.empty();

                    if (data.length === 0) {
                        tbody.append(`
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data</td>
                    </tr>
                `);
                    } else {
                        $.each(data, function(index, item) {
                            tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.jenis}</td>
                            <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id}" data-jenis="${item.jenis}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                        });
                    }
                });
            }
        });
    </script>
@endpush
