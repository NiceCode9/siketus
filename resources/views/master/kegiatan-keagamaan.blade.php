@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Kegiatan Keagamaan</h3>
                        <button class="btn btn-primary btn-sm float-right" id="btn-tambah">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-kegiatan">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kegiatan</th>
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

    <!-- Modal -->
    <div class="modal fade" id="modal-kegiatan" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Data Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form-kegiatan">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="nama_kegiatan">Nama Kegiatan</label>
                            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
                            <div class="invalid-feedback" id="nama_kegiatan-error"></div>
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

            // Tombol Tambah
            $('#btn-tambah').click(function() {
                $('#form-kegiatan')[0].reset();
                $('#modal-title').text('Tambah Data Kegiatan');
                $('#modal-kegiatan').modal('show');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
            });

            // Simpan Data (Create & Update)
            $('#form-kegiatan').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const id = $('#id').val();
                const url = id ? `/kegiatan-keagamaan/${id}` : '/kegiatan-keagamaan';
                const method = id ? 'PUT' : 'POST';

                if (id) {
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
                            $('#modal-kegiatan').modal('hide');
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
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $('.invalid-feedback').text('');
                            $('.form-control').removeClass('is-invalid');

                            for (const field in errors) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(errors[field][0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });

            // Edit Data
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: `/kegiatan-keagamaan/${id}/edit`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#id').val(response.data.id);
                            $('#nama_kegiatan').val(response.data.nama_kegiatan);
                            $('#modal-title').text('Edit Data Kegiatan');
                            $('#modal-kegiatan').modal('show');
                            $('.invalid-feedback').text('');
                            $('.form-control').removeClass('is-invalid');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat data untuk diedit.'
                        });
                    }
                });
            });

            // Delete Data
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Data "${nama}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/kegiatan-keagamaan/${id}`,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.success) {
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
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Gagal menghapus data.'
                                });
                            }
                        });
                    }
                });
            });

            // Fungsi untuk memuat data
            function loadData() {
                $.ajax({
                    url: '/kegiatan-keagamaan',
                    method: 'GET',
                    success: function(response) {
                        const tbody = $('#table-kegiatan tbody');
                        tbody.empty();

                        if (response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.nama_kegiatan}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id}" data-nama="${item.nama_kegiatan}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        `);
                            });
                        } else {
                            tbody.append(`
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data</td>
                        </tr>
                    `);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat data.'
                        });
                    }
                });
            }
        });
    </script>
@endpush
