@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <h2>Manajemen Guru Kelas</h2>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Filter Data</h5>
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Guru</label>
                                <select class="form-control" id="filter_guru_id" name="guru_id">
                                    <option value="">Semua Guru</option>
                                    @foreach ($gurus as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Mata Pelajaran</label>
                                <select class="form-control" id="filter_mapel_id" name="mapel_id">
                                    <option value="">Semua Mapel</option>
                                    @foreach ($mapels as $mapel)
                                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select class="form-control" id="filter_kelas_id" name="kelas_id">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tahun Akademik</label>
                                <select class="form-control" id="filter_tahun_akademik_id" name="tahun_akademik_id">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($tahunAkademiks as $ta)
                                        <option value="{{ $ta->id }}">{{ $ta->nama_tahun_akademik }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" id="filter_aktif" name="aktif">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-secondary btn-block" id="resetFilter">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary" id="addBtn">
                    <i class="fas fa-plus"></i> Tambah Guru Kelas
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="guruKelasTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Tahun Akademik</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Guru Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="guruKelasForm">
                    <div class="modal-body">
                        <input type="hidden" id="guru_kelas_id" name="id">

                        <div class="form-group">
                            <label>Guru <span class="text-danger">*</span></label>
                            <select class="form-control" id="form_guru_id" name="guru_id" required>
                                <option value="">Pilih Guru</option>
                                @foreach ($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-control" id="form_mapel_id" name="mapel_id" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach ($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Guru Mapel <span class="text-danger">*</span></label>
                            <select class="form-control" id="guru_mapel_id" name="guru_mapel_id" required>
                                <option value="">Pilih Guru dan Mapel terlebih dahulu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kelas <span class="text-danger">*</span></label>
                            <select class="form-control" id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-control" id="tahun_akademik_id" name="tahun_akademik_id" required>
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach ($tahunAkademiks as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun_akademik }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="aktif" name="aktif" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
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
            // Initialize DataTable
            let table = $('#guruKelasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('guru-kelas.getData') }}",
                    data: function(d) {
                        d.guru_id = $('#filter_guru_id').val();
                        d.mapel_id = $('#filter_mapel_id').val();
                        d.kelas_id = $('#filter_kelas_id').val();
                        d.tahun_akademik_id = $('#filter_tahun_akademik_id').val();
                        d.aktif = $('#filter_aktif').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'guru_nama',
                        name: 'guruMapel.guru.nama'
                    },
                    {
                        data: 'mapel_nama',
                        name: 'guruMapel.mapel.nama_mapel'
                    },
                    {
                        data: 'kelas_nama',
                        name: 'kelas.nama_kelas'
                    },
                    {
                        data: 'tahun_akademik_nama',
                        name: 'tahunAkademik.nama_tahun_akademik'
                    },
                    {
                        data: 'status',
                        name: 'aktif'
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

            // Filter change event
            $('#filterForm select').on('change', function() {
                table.ajax.reload();
            });

            // Reset filter
            $('#resetFilter').on('click', function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
            });

            // Load Guru Mapel based on Guru and Mapel selection
            function loadGuruMapel() {
                let guruId = $('#form_guru_id').val();
                let mapelId = $('#form_mapel_id').val();

                if (guruId && mapelId) {
                    $.ajax({
                        url: "{{ route('guru-kelas.getGuruMapel') }}",
                        type: 'GET',
                        data: {
                            guru_id: guruId,
                            mapel_id: mapelId
                        },
                        success: function(response) {
                            let options = '<option value="">Pilih Guru Mapel</option>';

                            if (response.data.length > 0) {
                                response.data.forEach(function(item) {
                                    options +=
                                        `<option value="${item.id}">${item.guru.nama_guru} - ${item.mapel.nama_mapel}</option>`;
                                });
                            } else {
                                options = '<option value="">Tidak ada data Guru Mapel</option>';
                            }

                            $('#guru_mapel_id').html(options);
                        }
                    });
                } else {
                    $('#guru_mapel_id').html('<option value="">Pilih Guru dan Mapel terlebih dahulu</option>');
                }
            }

            $('#form_guru_id, #form_mapel_id').on('change', function() {
                loadGuruMapel();
            });

            // Add button
            $('#addBtn').on('click', function() {
                $('#guruKelasForm')[0].reset();
                $('#guru_kelas_id').val('');
                $('#modalTitle').text('Tambah Guru Kelas');
                $('#guru_mapel_id').html('<option value="">Pilih Guru dan Mapel terlebih dahulu</option>');
                $('#formModal').modal('show');
            });

            // Edit button
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: `/guru-kelas/${id}`,
                    type: 'GET',
                    success: function(response) {
                        let data = response.data;

                        $('#guru_kelas_id').val(data.id);
                        $('#form_guru_id').val(data.guru_mapel.guru_id);
                        $('#form_mapel_id').val(data.guru_mapel.mapel_id);

                        // Load guru mapel first, then set the value
                        $.ajax({
                            url: "{{ route('guru-kelas.getGuruMapel') }}",
                            type: 'GET',
                            data: {
                                guru_id: data.guru_mapel.guru_id,
                                mapel_id: data.guru_mapel.mapel_id
                            },
                            success: function(gmResponse) {
                                let options =
                                    '<option value="">Pilih Guru Mapel</option>';
                                gmResponse.data.forEach(function(item) {
                                    let selected = item.id == data
                                        .guru_mapel_id ? 'selected' : '';
                                    options +=
                                        `<option value="${item.id}" ${selected}>${item.guru.nama_guru} - ${item.mapel.nama_mapel}</option>`;
                                });
                                $('#guru_mapel_id').html(options);
                            }
                        });

                        $('#kelas_id').val(data.kelas_id);
                        $('#tahun_akademik_id').val(data.tahun_akademik_id);
                        $('#aktif').val(data.aktif ? '1' : '0');
                        $('#keterangan').val(data.keterangan);

                        $('#modalTitle').text('Edit Guru Kelas');
                        $('#formModal').modal('show');
                    }
                });
            });

            // Submit form
            $('#guruKelasForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#guru_kelas_id').val();
                let url = id ? `/guru-kelas/${id}` : "{{ route('guru-kelas.store') }}";
                let method = id ? 'PUT' : 'POST';

                let formData = {
                    guru_mapel_id: $('#guru_mapel_id').val(),
                    kelas_id: $('#kelas_id').val(),
                    tahun_akademik_id: $('#tahun_akademik_id').val(),
                    aktif: $('#aktif').val(),
                    keterangan: $('#keterangan').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#formModal').modal('hide');
                        table.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan!';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: message
                        });
                    }
                });
            });

            // Delete button
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');

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
                            url: `/guru-kelas/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
