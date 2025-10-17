@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manajemen Penugasan Mapel Guru</h3>
                    <button type="button" class="btn btn-primary float-right" id="add-mapel-btn">
                        <i class="fas fa-plus"></i> Tambah Penugasan
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mapel-table">
                            <thead>
                                <tr>
                                    <th>Nama Guru</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
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

    <!-- Modal Tambah/Edit Penugasan Mapel Guru -->
    <div class="modal fade" id="mapel-modal" tabindex="-1" role="dialog" aria-labelledby="mapel-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapel-modal-label">Tambah Penugasan Mapel Guru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="mapel-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" id="id" name="id">
                            <label for="guru_id">Guru</label>
                            <select class="form-control" id="guru_id" name="guru_id" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach ($guru as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="guru_id-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="mapel_id">Mata Pelajaran</label>
                            <select class="form-control" id="mapel_id" name="mapel_id" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach ($mapel as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_mapel }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="mapel_id-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan..."></textarea>
                            <div class="invalid-feedback" id="keterangan-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

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
            let table = $('#mapel-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('guru-mapel.index') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'guru.nama',
                        name: 'guru.nama'
                    },
                    {
                        data: 'mapel.nama_mapel',
                        name: 'mapel.nama_mapel'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Add Mapel Button Click Event
            $('#add-mapel-btn').click(function() {
                $('#mapel-modal-label').text('Tambah Penugasan Mapel Guru');
                $('#mapel-form').trigger('reset');
                $('#mapel-modal').modal('show');
            });

            // Edit Button
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('guru-mapel.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    success: function(response) {
                        $('#id').val(response.id);
                        $('#guru_id').val(response.guru_id);
                        $('#mapel_id').val(response.mapel_id);
                        $('#keterangan').val(response.keterangan);

                        $('#mapel-modal-label').text('Edit Penugasan Mapel Guru');
                        $('#mapel-modal').modal('show');
                        clearValidationErrors();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengambil data penugasan mapel guru.'
                        });
                    }
                });
            });

            // Form Submit Event
            $('#mapel-form').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var id = $('#id').val();
                var url = id ? '{{ route('guru-mapel.update', ':id') }}'.replace(':id', id) :
                    '{{ route('guru-mapel.store') }}';

                if (id) {
                    // Edit Mode
                    formData += '&_method=PUT';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message
                        })

                        $('#mapel-modal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        // Handle error response
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data penugasan mapel guru.'
                        });
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

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('guru-mapel.destroy', ':id') }}".replace(':id',
                                id),
                            type: "DELETE",
                            success: function(response) {
                                if (response.success) {
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
        });
    </script>
@endpush
