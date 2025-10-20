@extends('layouts.app')

@push('css')
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fullcalendar/main.css">
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-calendar-alt"></i> Jadwal Pelajaran
                                </h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="view-calendar">
                                        <i class="fas fa-calendar"></i> Calendar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary active" id="view-table">
                                        <i class="fas fa-table"></i> Table
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="filter_tahun_akademik">Tahun Akademik</label>
                                <select class="form-control" id="filter_tahun_akademik">
                                    @foreach ($tahunAkademikList as $ta)
                                        <option value="{{ $ta->id }}"
                                            {{ $ta->id == $tahunAkademikId ? 'selected' : '' }}>
                                            {{ $ta->nama_tahun_akademik }}
                                            @if ($ta->status_aktif)
                                                (Aktif)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_kelas">Kelas</label>
                                <select class="form-control" id="filter_kelas">
                                    <option value="">-- Semua Kelas --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filter_hari">Hari</label>
                                <select class="form-control" id="filter_hari">
                                    <option value="">-- Semua Hari --</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <div>
                                    <button class="btn btn-primary" id="btn-filter">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <button class="btn btn-secondary" id="btn-reset">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button class="btn btn-success" id="btn-add">
                                    <i class="fas fa-plus"></i> Tambah Jadwal
                                </button>
                                <button class="btn btn-info" id="btn-import">
                                    <i class="fas fa-file-upload"></i> Import Excel
                                </button>
                                <button class="btn btn-warning" id="btn-export-pdf">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                                <button class="btn btn-dark" id="btn-export-excel">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="calendar-view" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div id="table-view">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="jadwal-table">
                            <thead class="thead-light">
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="10%">Hari</th>
                                    <th width="12%">Waktu</th>
                                    <th width="15%">Kelas</th>
                                    <th width="15%">Mata Pelajaran</th>
                                    <th width="15%">Guru</th>
                                    <th width="10%">Ruangan</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
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

    <!-- Modal Form -->
    <div class="modal fade" id="jadwal-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modal-title">Tambah Jadwal Pelajaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="jadwal-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="jadwal_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Guru & Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="guru_kelas_id" id="guru_kelas_id"
                                        required>
                                        <option value="">-- Pilih --</option>
                                    </select>
                                    <div class="invalid-feedback" id="guru_kelas_id-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hari <span class="text-danger">*</span></label>
                                    <select class="form-control" name="hari" id="hari" required>
                                        <option value="">-- Pilih Hari --</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                        <option value="Sabtu">Sabtu</option>
                                    </select>
                                    <div class="invalid-feedback" id="hari-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam_mulai" id="jam_mulai" required>
                                    <div class="invalid-feedback" id="jam_mulai-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam_selesai" id="jam_selesai"
                                        required>
                                    <div class="invalid-feedback" id="jam_selesai-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Ruangan</label>
                            <input type="text" class="form-control" name="ruangan" id="ruangan"
                                placeholder="Contoh: Ruang 201, Lab Kimia">
                            <div class="invalid-feedback" id="ruangan-error"></div>
                        </div>

                        <div class="alert alert-info" id="conflict-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="conflict-message"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal View Detail -->
    <div class="modal fade" id="detail-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detail Jadwal Pelajaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kelas</th>
                            <td id="detail-kelas">-</td>
                        </tr>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <td id="detail-mapel">-</td>
                        </tr>
                        <tr>
                            <th>Guru</th>
                            <td id="detail-guru">-</td>
                        </tr>
                        <tr>
                            <th>Hari</th>
                            <td id="detail-hari">-</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td id="detail-waktu">-</td>
                        </tr>
                        <tr>
                            <th>Ruangan</th>
                            <td id="detail-ruangan">-</td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td id="detail-tahun">-</td>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .badge-mapel {
            font-size: 0.85rem;
            padding: 0.4em 0.6em;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 3px;
            padding: 2px 4px;
        }

        .fc-event:hover {
            opacity: 0.8;
        }

        .select2-container--bootstrap4 .select2-selection {
            height: calc(2.25rem + 2px) !important;
        }

        .btn-group .btn.active {
            background-color: #007bff;
            color: white;
        }

        .conflict-badge {
            background-color: #dc3545;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

    <script>
        $(document).ready(function() {
            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Variables
            let calendar;
            let table;
            const mapelColors = {
                'Matematika': '#007bff',
                'Fisika': '#28a745',
                'Kimia': '#17a2b8',
                'Biologi': '#20c997',
                'Bahasa Indonesia': '#fd7e14',
                'Bahasa Inggris': '#ffc107',
                'Sejarah': '#6f42c1',
                'Geografi': '#e83e8c',
                'Ekonomi': '#6610f2',
                'Sosiologi': '#f06292',
                'Pendidikan Agama': '#795548',
                'Pendidikan Kewarganegaraan': '#607d8b',
                'Seni Budaya': '#e91e63',
                'Penjasorkes': '#dc3545'
            };

            // Initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#jadwal-table')) {
                    $('#jadwal-table').DataTable().destroy();
                }

                table = $('#jadwal-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.jadwal.index') }}",
                        data: function(d) {
                            d.tahun_akademik_id = $('#filter_tahun_akademik').val();
                            d.kelas_id = $('#filter_kelas').val();
                            d.hari = $('#filter_hari').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'hari'
                        },
                        {
                            data: 'waktu',
                            orderable: false
                        },
                        {
                            data: 'kelas'
                        },
                        {
                            data: 'mapel'
                        },
                        {
                            data: 'guru'
                        },
                        {
                            data: 'ruangan'
                        },
                        {
                            data: 'status',
                            orderable: false
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'asc'],
                        [2, 'asc']
                    ]
                });
            }

            initDataTable();

            // Toggle View
            $('#view-calendar').click(function() {
                $(this).addClass('active');
                $('#view-table').removeClass('active');
                $('#table-view').hide();
                $('#calendar-view').show();
                if (!calendar) {
                    initCalendar();
                }
            });

            $('#view-table').click(function() {
                $(this).addClass('active');
                $('#view-calendar').removeClass('active');
                $('#calendar-view').hide();
                $('#table-view').show();
            });

            // Initialize Calendar
            function initCalendar() {
                const calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'id',
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth'
                    },
                    slotMinTime: '07:00:00',
                    slotMaxTime: '17:00:00',
                    allDaySlot: false,
                    height: 'auto',
                    events: function(info, successCallback, failureCallback) {
                        $.ajax({
                            url: "{{ route('admin.jadwal.index') }}",
                            data: {
                                calendar: true,
                                tahun_akademik_id: $('#filter_tahun_akademik').val(),
                                kelas_id: $('#filter_kelas').val()
                            },
                            success: function(data) {
                                successCallback(data);
                            },
                            error: function() {
                                failureCallback();
                            }
                        });
                    },
                    eventClick: function(info) {
                        viewDetail(info.event.id);
                    },
                    eventContent: function(arg) {
                        return {
                            html: '<div style="padding: 2px;">' +
                                '<strong>' + arg.event.extendedProps.mapel + '</strong><br>' +
                                '<small>' + arg.event.extendedProps.kelas + '</small><br>' +
                                '<small>' + arg.event.extendedProps.ruangan + '</small>' +
                                '</div>'
                        };
                    }
                });
                calendar.render();
            }

            // Load Kelas Options
            function loadKelasOptions() {
                const tahunAkademikId = $('#filter_tahun_akademik').val();
                $.ajax({
                    url: "{{ route('admin.jadwal.get-kelas') }}",
                    data: {
                        tahun_akademik_id: tahunAkademikId
                    },
                    success: function(data) {
                        let options = '<option value="">-- Semua Kelas --</option>';
                        data.forEach(function(kelas) {
                            options +=
                                `<option value="${kelas.id}">${kelas.nama_kelas}</option>`;
                        });
                        $('#filter_kelas').html(options);
                    }
                });
            }

            // Load Guru Kelas Options
            function loadGuruKelasOptions() {
                $.ajax({
                    url: "{{ route('admin.jadwal.get-guru-kelas') }}",
                    success: function(data) {
                        let options = '<option value="">-- Pilih --</option>';
                        data.forEach(function(item) {
                            options += `<option value="${item.id}">${item.text}</option>`;
                        });
                        $('#guru_kelas_id').html(options);
                    }
                });
            }

            loadKelasOptions();
            loadGuruKelasOptions();

            // Filter Change
            $('#filter_tahun_akademik').change(function() {
                loadKelasOptions();
            });

            $('#btn-filter').click(function() {
                table.draw();
                if (calendar) {
                    calendar.refetchEvents();
                }
            });

            $('#btn-reset').click(function() {
                $('#filter_kelas').val('').trigger('change');
                $('#filter_hari').val('');
                table.draw();
                if (calendar) {
                    calendar.refetchEvents();
                }
            });

            // Add Button
            $('#btn-add').click(function() {
                $('#jadwal-form')[0].reset();
                $('#jadwal_id').val('');
                $('#modal-title').text('Tambah Jadwal Pelajaran');
                $('#guru_kelas_id').val('').trigger('change');
                $('#conflict-warning').hide();
                clearValidationErrors();
                $('#jadwal-modal').modal('show');
            });

            // Edit Button
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: "{{ url('admin/jadwal') }}/" + id + "/edit",
                    success: function(data) {
                        $('#jadwal_id').val(data.id);
                        $('#guru_kelas_id').val(data.guru_kelas_id).trigger('change');
                        $('#hari').val(data.hari);
                        $('#jam_mulai').val(data.jam_mulai);
                        $('#jam_selesai').val(data.jam_selesai);
                        $('#ruangan').val(data.ruangan);
                        $('#modal-title').text('Edit Jadwal Pelajaran');
                        $('#conflict-warning').hide();
                        clearValidationErrors();
                        $('#jadwal-modal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil data jadwal.', 'error');
                    }
                });
            });

            // View Detail Button
            $(document).on('click', '.view-btn', function() {
                viewDetail($(this).data('id'));
            });

            function viewDetail(id) {
                $.ajax({
                    url: "{{ url('admin/jadwal') }}/" + id,
                    success: function(data) {
                        $('#detail-kelas').text(data.kelas);
                        $('#detail-mapel').text(data.mapel);
                        $('#detail-guru').text(data.guru);
                        $('#detail-hari').text(data.hari);
                        $('#detail-waktu').text(data.waktu);
                        $('#detail-ruangan').text(data.ruangan || '-');
                        $('#detail-tahun').text(data.tahun_akademik);
                        $('#detail-modal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil detail jadwal.', 'error');
                    }
                });
            }

            // Save Form
            $('#jadwal-form').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const id = $('#jadwal_id').val();
                const url = id ? "{{ url('admin/jadwal') }}/" + id : "{{ route('admin.jadwal.store') }}";
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $('#jadwal-modal').modal('hide');
                            table.draw();
                            if (calendar) {
                                calendar.refetchEvents();
                            }
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
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            Swal.fire('Error!', xhr.responseJSON?.message ||
                                'Terjadi kesalahan.', 'error');
                        }
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Jadwal \"" + nama + "\" akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/jadwal') }}/" + id,
                            type: "DELETE",
                            success: function(response) {
                                if (response.status) {
                                    table.draw();
                                    if (calendar) {
                                        calendar.refetchEvents();
                                    }
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON?.message ||
                                    'Gagal menghapus data.', 'error');
                            }
                        });
                    }
                });
            });

            // Export PDF
            $('#btn-export-pdf').click(function() {
                const tahunAkademikId = $('#filter_tahun_akademik').val();
                const kelasId = $('#filter_kelas').val();
                window.open("{{ route('admin.jadwal.export-pdf') }}?tahun_akademik_id=" + tahunAkademikId +
                    "&kelas_id=" + kelasId, '_blank');
            });

            // Export Excel
            $('#btn-export-excel').click(function() {
                const tahunAkademikId = $('#filter_tahun_akademik').val();
                const kelasId = $('#filter_kelas').val();
                window.location.href = "{{ route('admin.jadwal.export-excel') }}?tahun_akademik_id=" +
                    tahunAkademikId + "&kelas_id=" + kelasId;
            });

            // Import Excel (placeholder)
            $('#btn-import').click(function() {
                Swal.fire('Info', 'Fitur import akan segera tersedia!', 'info');
            });

            // Validation Functions
            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function showValidationErrors(errors) {
                clearValidationErrors();
                $.each(errors, function(field, messages) {
                    $('[name="' + field + '"]').addClass('is-invalid');
                    $('#' + field + '-error').text(messages[0]);
                });
            }
        });
    </script>
@endpush
