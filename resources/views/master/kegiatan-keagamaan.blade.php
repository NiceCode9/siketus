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
                                        <th>Tahun Akademik</th>
                                        <th>Tingkat Kelas</th>
                                        <th>Semester</th>
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

    <!-- Modal Tambah (Bulk Insert) -->
    <div class="modal fade" id="modal-kegiatan-bulk" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Kegiatan (Bulk Insert)</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form-kegiatan-bulk">
                    <div class="modal-body">
                        <button type="button" class="btn btn-success btn-sm mb-3" id="btn-tambah-baris">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-bulk-input">
                                <thead>
                                    <tr>
                                        <th width="30%">Nama Kegiatan</th>
                                        <th width="25%">Tahun Akademik</th>
                                        <th width="15%">Tingkat</th>
                                        <th width="15%">Semester</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bulk-input-rows">
                                    <!-- Rows akan ditambahkan via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-simpan-bulk">
                            <i class="fas fa-save"></i> Simpan Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit (Single) -->
    <div class="modal fade" id="modal-kegiatan-edit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="form-kegiatan-edit">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">

                        <div class="form-group">
                            <label for="edit-nama-kegiatan">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-nama-kegiatan" name="nama_kegiatan"
                                required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit-tahun-akademik">Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit-tahun-akademik" name="tahun_akademik_id" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit-tingkat-kelas">Tingkat Kelas <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit-tingkat-kelas" name="tingkat_kelas" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="edit-semester">Semester <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit-semester" name="semester" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let tahunAkademikData = [];
        let rowCounter = 0;

        $(document).ready(function() {
            // CSRF Token untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load data saat halaman dimuat
            loadData();
            loadTahunAkademik();

            // Tombol Tambah - Buka Modal Bulk
            $('#btn-tambah').click(function() {
                $('#bulk-input-rows').empty();
                rowCounter = 0;
                addNewRow(); // Tambah 1 row default
                $('#modal-kegiatan-bulk').modal('show');
            });

            // Tombol Tambah Baris
            $('#btn-tambah-baris').click(function() {
                addNewRow();
            });

            // Hapus Baris
            $(document).on('click', '.btn-hapus-baris', function() {
                if ($('#bulk-input-rows tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Minimal harus ada 1 baris data'
                    });
                }
            });

            // Submit Bulk Insert
            $('#form-kegiatan-bulk').submit(function(e) {
                e.preventDefault();

                const kegiatanData = [];
                let isValid = true;

                $('#bulk-input-rows tr').each(function() {
                    const row = $(this);
                    const namaKegiatan = row.find('.nama-kegiatan').val().trim();
                    const tahunAkademikId = row.find('.tahun-akademik').val();
                    const tingkatKelas = row.find('.tingkat-kelas').val();
                    const semester = row.find('.semester').val();

                    // Clear previous errors
                    row.find('.form-control').removeClass('is-invalid');

                    // Validate
                    if (!namaKegiatan) {
                        row.find('.nama-kegiatan').addClass('is-invalid');
                        isValid = false;
                    }
                    if (!tahunAkademikId) {
                        row.find('.tahun-akademik').addClass('is-invalid');
                        isValid = false;
                    }
                    if (!tingkatKelas) {
                        row.find('.tingkat-kelas').addClass('is-invalid');
                        isValid = false;
                    }
                    if (!semester) {
                        row.find('.semester').addClass('is-invalid');
                        isValid = false;
                    }

                    if (namaKegiatan && tahunAkademikId && tingkatKelas && semester) {
                        kegiatanData.push({
                            nama_kegiatan: namaKegiatan,
                            tahun_akademik_id: tahunAkademikId,
                            tingkat_kelas: tingkatKelas,
                            semester: semester
                        });
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Mohon lengkapi semua field yang bertanda merah'
                    });
                    return;
                }

                if (kegiatanData.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Tidak ada data yang valid untuk disimpan'
                    });
                    return;
                }

                // Disable button saat submit
                $('#btn-simpan-bulk').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: '/admin/kegiatan-keagamaan',
                    type: 'POST',
                    data: JSON.stringify({
                        kegiatan: kegiatanData
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response.success) {
                            $('#modal-kegiatan-bulk').modal('hide');
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
                            let errorMessage = 'Validasi gagal:\n';

                            for (const field in errors) {
                                errorMessage += '- ' + errors[field][0] + '\n';
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal!',
                                text: errorMessage
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    },
                    complete: function() {
                        $('#btn-simpan-bulk').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Simpan Semua');
                    }
                });
            });

            // Edit Data
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: `/admin/kegiatan-keagamaan/${id}/edit`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#edit-id').val(response.data.id);
                            $('#edit-nama-kegiatan').val(response.data.nama_kegiatan);
                            $('#edit-tahun-akademik').val(response.data.tahun_akademik_id);
                            $('#edit-tingkat-kelas').val(response.data.tingkat_kelas);
                            $('#edit-semester').val(response.data.semester);

                            $('.invalid-feedback').text('');
                            $('.form-control').removeClass('is-invalid');

                            $('#modal-kegiatan-edit').modal('show');
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

            // Submit Edit
            $('#form-kegiatan-edit').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const id = $('#edit-id').val();
                formData.append('_method', 'PUT');

                $.ajax({
                    url: `/admin/kegiatan-keagamaan/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#modal-kegiatan-edit').modal('hide');
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
                                const input = $(`#edit-${field.replace('_', '-')}`);
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(errors[field][0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat mengupdate data.'
                            });
                        }
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
                            url: `/admin/kegiatan-keagamaan/${id}`,
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
                    url: '/admin/kegiatan-keagamaan',
                    method: 'GET',
                    success: function(response) {
                        const tbody = $('#table-kegiatan tbody');
                        tbody.empty();

                        if (response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                const tahunAkademik = item.tahun_akademik ? item.tahun_akademik
                                    .nama_tahun_akademik : '-';

                                tbody.append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.nama_kegiatan}</td>
                                        <td>${tahunAkademik}</td>
                                        <td>${item.tingkat_kelas}</td>
                                        <td>${item.semester.toUpperCase()}</td>
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
                                    <td colspan="6" class="text-center">Tidak ada data</td>
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

            // Fungsi untuk load tahun akademik
            function loadTahunAkademik() {
                $.ajax({
                    url: '/get-tahun-akademik',
                    method: 'GET',
                    success: function(response) {
                        console.log(response);

                        if (response.success) {
                            tahunAkademikData = response.data;

                            // Populate dropdown edit
                            const editSelect = $('#edit-tahun-akademik');
                            editSelect.find('option:not(:first)').remove();

                            $.each(response.data, function(index, item) {
                                const badge = item.status_aktif ?
                                    ' <span class="badge badge-success">Aktif</span>' : '';
                                editSelect.append(
                                    `<option value="${item.id}">${item.nama_tahun_akademik}(${badge})</option>`
                                );
                            });
                        }
                    }
                });
            }

            // Fungsi tambah baris baru
            function addNewRow() {
                rowCounter++;

                let tahunAkademikOptions = '<option value="">-- Pilih --</option>';
                $.each(tahunAkademikData, function(index, item) {
                    let badge = item.status_aktif ?
                        ' <span class="badge badge-success">(Aktif)</span>' : '';
                    tahunAkademikOptions +=
                        `<option value="${item.id}">${item.nama_tahun_akademik}${badge}</option>`;
                });

                const newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control form-control-sm nama-kegiatan" placeholder="Nama Kegiatan" required>
                        </td>
                        <td>
                            <select class="form-control form-control-sm tahun-akademik" required>
                                ${tahunAkademikOptions}
                            </select>
                        </td>
                        <td>
                            <select class="form-control form-control-sm tingkat-kelas" required>
                                <option value="">-- Pilih --</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control form-control-sm semester" required>
                                <option value="">-- Pilih --</option>
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-baris">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#bulk-input-rows').append(newRow);
            }
        });
    </script>
@endpush
