@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Jurusan</h3>
                        <button type="button" class="btn btn-primary float-right" id="btn-tambah">
                            <i class="fas fa-plus"></i> Tambah Jurusan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table-jurusan">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Jurusan</th>
                                        <th>Kode Jurusan</th>
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
    <div class="modal fade" id="modal-jurusan" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Jurusan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form-jurusan">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="nama_jurusan">Nama Jurusan</label>
                            <input type="text" class="form-control" id="nama_jurusan" name="nama_jurusan" required>
                            <div class="invalid-feedback" id="nama_jurusan-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="kode_jurusan">Kode Jurusan</label>
                            <input type="text" class="form-control" id="kode_jurusan" name="kode_jurusan" required>
                            <div class="invalid-feedback" id="kode_jurusan-error"></div>
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

    <!-- Modal Hapus -->
    <div class="modal fade" id="modal-hapus" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jurusan ini?</p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-hapus">Hapus</button>
                </div>
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

            // Load data saat halaman dibuka
            loadData();

            // Tombol Tambah
            $('#btn-tambah').click(function() {
                $('#form-jurusan')[0].reset();
                $('#id').val('');
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#modal-title').text('Tambah Jurusan');
                $('#modal-jurusan').modal('show');
            });

            // Simpan Data (Create/Update)
            $('#form-jurusan').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const id = $('#id').val();
                const url = id ? `/jurusan/${id}` : '/jurusan';
                const method = id ? 'PUT' : 'POST';

                if (id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-jurusan').modal('hide');
                            loadData();
                            showAlert('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}-error`).text(value[0]);
                            });
                        } else {
                            showAlert('error', 'Terjadi kesalahan!');
                        }
                    }
                });
            });

            // Edit Data
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.get(`/jurusan/${id}/edit`, function(response) {
                    if (response.success) {
                        $('#id').val(response.data.id);
                        $('#nama_jurusan').val(response.data.nama_jurusan);
                        $('#kode_jurusan').val(response.data.kode_jurusan);
                        $('.invalid-feedback').text('');
                        $('.form-control').removeClass('is-invalid');
                        $('#modal-title').text('Edit Jurusan');
                        $('#modal-jurusan').modal('show');
                    }
                });
            });

            // Hapus Data - Tampilkan konfirmasi
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');
                $('#delete-id').val(id);
                $('#modal-hapus').modal('show');
            });

            // Hapus Data - Eksekusi
            $('#btn-hapus').click(function() {
                const id = $('#delete-id').val();

                $.ajax({
                    url: `/jurusan/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            $('#modal-hapus').modal('hide');
                            loadData();
                            showAlert('success', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Terjadi kesalahan!');
                    }
                });
            });

            // Fungsi Load Data
            function loadData() {
                $.get('/jurusan', function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.nama_jurusan}</td>
                                    <td>${item.kode_jurusan}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-edit" data-id="${item.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-hapus" data-id="${item.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            });
                        } else {
                            html += `
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data jurusan.</td>
                            </tr>
                            `;
                        }
                        $('#table-jurusan tbody').html(html);
                    }
                });
            }

            // Fungsi Show Alert dengan SweetAlert2
            function showAlert(type, message) {
                const icon = type === 'success' ? 'success' : 'error';
                Swal.fire({
                    icon: icon,
                    title: type === 'success' ? 'Berhasil!' : 'Gagal!',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    </script>
@endpush
