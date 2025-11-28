@extends('layouts.app')

@push('css')
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fullcalendar/main.css">
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Enhanced Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-calendar-alt mr-2"></i> Manajemen Jadwal Pelajaran
                                </h3>
                                <p class="mb-0 mt-1 text-white-50 small">Kelola dan lihat jadwal pelajaran</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group shadow-sm" role="group">
                                    <button type="button" class="btn btn-light btn-sm" id="view-table">
                                        <i class="fas fa-table mr-1"></i> Table
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" id="view-calendar">
                                        <i class="fas fa-calendar mr-1"></i> Calendar
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" id="view-grid">
                                        <i class="fas fa-th-large mr-1"></i> Grid
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Enhanced Filter Section -->
                        <div class="filter-section p-3 bg-light rounded mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="filter_tahun_akademik" class="font-weight-bold">
                                        <i class="fas fa-calendar-check text-primary"></i> Tahun Akademik
                                    </label>
                                    <select class="form-control form-control-sm" id="filter_tahun_akademik">
                                        @foreach ($tahunAkademikList as $ta)
                                            <option value="{{ $ta->id }}"
                                                {{ $ta->id == $tahunAkademikId ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun_akademik }}
                                                @if ($ta->status_aktif)
                                                    <span class="badge badge-success">Aktif</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_kelas" class="font-weight-bold">
                                        <i class="fas fa-school text-success"></i> Kelas
                                    </label>
                                    <select class="form-control form-control-sm" id="filter_kelas">
                                        <option value="">-- Semua Kelas --</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_hari" class="font-weight-bold">
                                        <i class="fas fa-calendar-day text-info"></i> Hari
                                    </label>
                                    <select class="form-control form-control-sm" id="filter_hari">
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
                                    <div class="btn-group-custom">
                                        <button class="btn btn-primary btn-sm btn-block mb-1" id="btn-filter">
                                            <i class="fas fa-search mr-1"></i> Terapkan Filter
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm btn-block" id="btn-reset">
                                            <i class="fas fa-redo mr-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons with Icons -->
                        <div class="action-buttons mb-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-success btn-action" id="btn-add">
                                        <i class="fas fa-plus-circle mr-1"></i> Tambah Jadwal
                                    </button>
                                    <button class="btn btn-info btn-action" id="btn-import">
                                        <i class="fas fa-file-upload mr-1"></i> Import Excel
                                    </button>
                                    <button class="btn btn-warning btn-action" id="btn-export-pdf">
                                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                    </button>
                                    <button class="btn btn-dark btn-action" id="btn-export-excel">
                                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                                    </button>
                                    <button class="btn btn-secondary btn-action" id="btn-print">
                                        <i class="fas fa-print mr-1"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-3" id="stats-cards">
                            <div class="col-md-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 id="total-jadwal">0</h3>
                                        <p>Total Jadwal</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 id="total-kelas">0</h3>
                                        <p>Kelas Aktif</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-school"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 id="total-guru">0</h3>
                                        <p>Guru Mengajar</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3 id="total-konflik">0</h3>
                                        <p>Konflik Jadwal</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar View (Enhanced) -->
        <div id="calendar-view" style="display: none;">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-primary mr-2"></i>Kalender Jadwal Pelajaran</h5>
                </div>
                <div class="card-body p-4">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Grid View (New) -->
        <div id="grid-view" style="display: none;">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-th-large text-success mr-2"></i>Grid Jadwal Mingguan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-grid">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="10%">Waktu</th>
                                    <th>Senin</th>
                                    <th>Selasa</th>
                                    <th>Rabu</th>
                                    <th>Kamis</th>
                                    <th>Jumat</th>
                                    <th>Sabtu</th>
                                </tr>
                            </thead>
                            <tbody id="grid-body">
                                <!-- Will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table View (Enhanced) -->
        <div id="table-view">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-table text-info mr-2"></i>Daftar Jadwal Pelajaran</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="jadwal-table">
                            <thead class="thead-dark">
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

    <!-- Enhanced Modal Form -->
    <div class="modal fade" id="jadwal-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="modal-title">
                        <i class="fas fa-calendar-plus mr-2"></i>Tambah Jadwal Pelajaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="jadwal-form">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="jadwal_id">

                        <div class="form-section mb-4">
                            <h6 class="section-title"><i class="fas fa-info-circle text-primary"></i> Informasi Dasar</h6>
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
                        </div>

                        <div class="form-section mb-4">
                            <h6 class="section-title"><i class="fas fa-clock text-success"></i> Waktu</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jam Mulai <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="jam_mulai" id="jam_mulai"
                                            required>
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
                        </div>

                        <div class="form-section">
                            <h6 class="section-title"><i class="fas fa-door-open text-info"></i> Lokasi</h6>
                            <div class="form-group">
                                <label>Ruangan</label>
                                <input type="text" class="form-control" name="ruangan" id="ruangan"
                                    placeholder="Contoh: Ruang 201, Lab Kimia">
                                <small class="form-text text-muted">Opsional - Kosongkan jika belum ditentukan</small>
                                <div class="invalid-feedback" id="ruangan-error"></div>
                            </div>
                        </div>

                        <div class="alert alert-warning alert-dismissible fade show" id="conflict-warning"
                            style="display: none;">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span id="conflict-message"></span>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal View Detail -->
    <div class="modal fade" id="detail-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle mr-2"></i>Detail Jadwal Pelajaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-card">
                                <h6 class="text-primary"><i class="fas fa-school mr-2"></i>Informasi Kelas</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Kelas</th>
                                        <td id="detail-kelas">-</td>
                                    </tr>
                                    <tr>
                                        <th>Mata Pelajaran</th>
                                        <td id="detail-mapel">-</td>
                                    </tr>
                                    <tr>
                                        <th>Guru Pengajar</th>
                                        <td id="detail-guru">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card">
                                <h6 class="text-success"><i class="fas fa-clock mr-2"></i>Waktu & Tempat</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Hari</th>
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
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="detail-card mt-3">
                        <h6 class="text-info"><i class="fas fa-calendar-check mr-2"></i>Tahun Akademik</h6>
                        <p class="mb-0" id="detail-tahun">-</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        /* Enhanced Styles */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-custom {
            border-radius: 10px;
            border: none;
        }

        .filter-section {
            border-left: 4px solid #667eea;
        }

        .btn-action {
            margin-right: 5px;
            margin-bottom: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .small-box {
            border-radius: 8px;
            padding: 15px;
            position: relative;
            overflow: hidden;
            color: white;
            transition: transform 0.3s ease;
        }

        .small-box:hover {
            transform: translateY(-5px);
        }

        .small-box .inner {
            position: relative;
            z-index: 2;
        }

        .small-box .inner h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .small-box .inner p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .small-box .icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4rem;
            opacity: 0.2;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .badge-mapel {
            font-size: 0.85rem;
            padding: 0.4em 0.8em;
            font-weight: 500;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 5px;
            padding: 4px 6px;
            border: none !important;
            font-size: 0.85rem;
        }

        .fc-event:hover {
            opacity: 0.85;
            transform: scale(1.02);
        }

        .select2-container--bootstrap4 .select2-selection {
            height: calc(2.25rem + 2px) !important;
        }

        .btn-group .btn.active {
            background-color: #667eea !important;
            color: white !important;
            border-color: #667eea !important;
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

        .form-section {
            border-left: 3px solid #e0e0e0;
            padding-left: 15px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #555;
        }

        .detail-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .table-grid {
            font-size: 0.85rem;
        }

        .table-grid td {
            height: 80px;
            vertical-align: top;
            padding: 8px;
        }

        .grid-item {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 5px;
            border-left: 3px solid #667eea;
            cursor: pointer;
            transition: all 0.2s;
        }

        .grid-item:hover {
            background: #e9ecef;
            transform: translateX(3px);
        }

        .grid-item-mapel {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .grid-item-info {
            font-size: 0.75rem;
            color: #666;
        }

        /* FullCalendar custom styles */
        .fc-toolbar {
            margin-bottom: 20px !important;
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
        }

        .fc-button {
            background-color: #667eea !important;
            border-color: #667eea !important;
        }

        .fc-button:hover {
            background-color: #5568d3 !important;
            border-color: #5568d3 !important;
        }

        .fc-daygrid-day-number {
            font-weight: 600;
        }

        .fc-col-header-cell {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        /* Pastikan event memiliki warna yang jelas */
        .fc-event {
            border: none !important;
            margin-bottom: 2px !important;
        }

        .fc-event-main {
            color: white !important;
        }

        .fc-daygrid-event {
            padding: 2px 4px !important;
            margin: 1px 0 !important;
        }

        .fc-daygrid-event-dot {
            display: none !important;
        }

        /* Styling untuk event di month view */
        .fc-daygrid-block-event .fc-event-main {
            padding: 2px 4px;
        }

        /* Hover effect untuk event */
        .fc-event:hover {
            filter: brightness(1.1);
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
            let currentView = 'table';

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
                    ],
                    drawCallback: function() {
                        updateStats();
                    }
                });
            }

            initDataTable();

            // Toggle View Functions
            function switchView(view) {
                currentView = view;

                // Hide all views
                $('#table-view, #calendar-view, #grid-view').hide();

                // Remove active class from all buttons
                $('#view-table, #view-calendar, #view-grid').removeClass('active');

                // Show selected view and activate button
                switch (view) {
                    case 'table':
                        $('#table-view').show();
                        $('#view-table').addClass('active');
                        break;
                    case 'calendar':
                        $('#calendar-view').show();
                        $('#view-calendar').addClass('active');
                        if (!calendar) {
                            initCalendar();
                        } else {
                            calendar.refetchEvents();
                        }
                        break;
                    case 'grid':
                        $('#grid-view').show();
                        $('#view-grid').addClass('active');
                        loadGridView();
                        break;
                }
            }

            // Toggle View Buttons
            $('#view-table').click(function() {
                switchView('table');
            });

            $('#view-calendar').click(function() {
                switchView('calendar');
            });

            $('#view-grid').click(function() {
                switchView('grid');
            });

            // Initialize Calendar with Fix
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
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        day: 'Hari'
                    },
                    slotMinTime: '07:00:00',
                    slotMaxTime: '17:00:00',
                    allDaySlot: false,
                    height: 'auto',
                    expandRows: true,
                    navLinks: true,
                    editable: false,
                    dayMaxEvents: 3,
                    moreLinkText: 'lainnya',
                    events: function(info, successCallback, failureCallback) {
                        $.ajax({
                            url: "{{ route('admin.jadwal.index') }}",
                            data: {
                                calendar: true,
                                tahun_akademik_id: $('#filter_tahun_akademik').val(),
                                kelas_id: $('#filter_kelas').val()
                            },
                            success: function(data) {
                                // Check current view type
                                const currentView = calendar ? calendar.view.type :
                                    'dayGridMonth';

                                if (currentView === 'dayGridMonth') {
                                    // For month view, use recurring format
                                    successCallback(data);
                                    console.log(currentView);
                                } else {
                                    // For week/day view, expand to specific dates
                                    let expandedEvents = [];
                                    const startDate = moment(info.start);
                                    const endDate = moment(info.end);

                                    data.forEach(function(event) {
                                        if (event.daysOfWeek && event.daysOfWeek
                                            .length > 0) {
                                            let current = moment(startDate);

                                            while (current.isBefore(endDate)) {
                                                // Check if current day matches event's day of week
                                                if (event.daysOfWeek[0] === current
                                                    .day()) {
                                                    // Extract time from original start/end
                                                    const startTime = moment(event
                                                        .start).format(
                                                        'HH:mm:ss');
                                                    const endTime = moment(event
                                                        .end).format('HH:mm:ss');

                                                    expandedEvents.push({
                                                        id: event.id,
                                                        title: event.title,
                                                        start: current
                                                            .format(
                                                                'YYYY-MM-DD'
                                                            ) + 'T' +
                                                            startTime,
                                                        end: current.format(
                                                                'YYYY-MM-DD'
                                                            ) + 'T' +
                                                            endTime,
                                                        backgroundColor: event
                                                            .backgroundColor,
                                                        borderColor: event
                                                            .borderColor,
                                                        extendedProps: event
                                                            .extendedProps
                                                    });
                                                }
                                                current.add(1, 'days');
                                            }
                                        }
                                    });

                                    successCallback(expandedEvents);
                                }
                            },
                            error: function() {
                                failureCallback();
                            }
                        });
                    },
                    eventClick: function(info) {
                        viewDetail(info.event.id);
                    },
                    eventDidMount: function(info) {
                        // Hapus tooltip lama jika ada
                        if ($(info.el).data('bs.tooltip')) {
                            $(info.el).tooltip('dispose');
                        }

                        // Tambahkan tooltip baru
                        const tooltipContent =
                            info.event.extendedProps.mapel + ' - ' + info.event.extendedProps.kelas +
                            '\n' +
                            'Guru: ' + info.event.extendedProps.guru + '\n' +
                            'Ruangan: ' + info.event.extendedProps.ruangan;

                        $(info.el).attr('title', tooltipContent);
                        $(info.el).attr('data-toggle', 'tooltip');
                        $(info.el).attr('data-placement', 'top');
                        $(info.el).tooltip();
                    },
                    eventContent: function(arg) {
                        let html = '<div class="p-1" style="color: white;">';

                        if (arg.view.type === 'dayGridMonth') {
                            // View Bulan - Tampilan compact
                            html += '<div class="font-weight-bold small">' + arg.event.extendedProps
                                .mapel + '</div>';
                            html += '<div class="small" style="opacity: 0.9;">' + arg.event
                                .extendedProps.kelas + '</div>';
                        } else {
                            // View Minggu/Hari - Tampilan detail
                            html += '<div class="font-weight-bold">' + arg.event.extendedProps.mapel +
                                '</div>';
                            html += '<div class="small mb-1">' + arg.event.extendedProps.kelas +
                                '</div>';
                            html += '<div class="small"><i class="fas fa-user mr-1"></i>' + arg.event
                                .extendedProps.guru + '</div>';
                            html += '<div class="small"><i class="fas fa-door-open mr-1"></i>' + arg
                                .event.extendedProps.ruangan + '</div>';
                        }

                        html += '</div>';
                        return {
                            html: html
                        };
                    }
                });
                calendar.render();
            }

            // Load Grid View
            function loadGridView() {
                const tahunAkademikId = $('#filter_tahun_akademik').val();
                const kelasId = $('#filter_kelas').val();

                $.ajax({
                    url: "{{ route('admin.jadwal.index') }}",
                    data: {
                        calendar: true,
                        tahun_akademik_id: tahunAkademikId,
                        kelas_id: kelasId
                    },
                    success: function(data) {
                        renderGridView(data);
                    }
                });
            }

            function renderGridView(jadwalData) {
                const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const timeSlots = generateTimeSlots('07:00', '17:00', 60);

                let html = '';

                timeSlots.forEach(function(slot) {
                    html += '<tr>';
                    html += '<td class="font-weight-bold text-center bg-light">' + slot.start +
                        '<br><small>' + slot.end + '</small></td>';

                    hariList.forEach(function(hari) {
                        html += '<td>';

                        // Find jadwal for this time slot and day
                        const jadwalForSlot = jadwalData.filter(function(j) {
                            const hariEn = getHariEnglish(hari);
                            const slotStart = moment(slot.start, 'HH:mm');
                            const slotEnd = moment(slot.end, 'HH:mm');
                            const jadwalStart = moment(j.start).format('HH:mm');
                            const jadwalEnd = moment(j.end).format('HH:mm');

                            return j.daysOfWeek && j.daysOfWeek[0] === getDayNumber(hari) &&
                                jadwalStart >= slot.start && jadwalStart < slot.end;
                        });

                        jadwalForSlot.forEach(function(j) {
                            html += '<div class="grid-item" style="border-left-color: ' + j
                                .backgroundColor + '" onclick="viewDetail(' + j.id + ')">';
                            html += '<div class="grid-item-mapel">' + j.extendedProps
                                .mapel + '</div>';
                            html += '<div class="grid-item-info">' + j.extendedProps.kelas +
                                '</div>';
                            html +=
                                '<div class="grid-item-info"><i class="fas fa-user"></i> ' +
                                j.extendedProps.guru + '</div>';
                            html +=
                                '<div class="grid-item-info"><i class="fas fa-door-open"></i> ' +
                                j.extendedProps.ruangan + '</div>';
                            html += '</div>';
                        });

                        html += '</td>';
                    });

                    html += '</tr>';
                });

                $('#grid-body').html(html);
            }

            function generateTimeSlots(start, end, interval) {
                const slots = [];
                let current = moment(start, 'HH:mm');
                const endTime = moment(end, 'HH:mm');

                while (current.isBefore(endTime)) {
                    const next = moment(current).add(interval, 'minutes');
                    slots.push({
                        start: current.format('HH:mm'),
                        end: next.format('HH:mm')
                    });
                    current = next;
                }

                return slots;
            }

            function getDayNumber(hari) {
                const mapping = {
                    'Senin': 1,
                    'Selasa': 2,
                    'Rabu': 3,
                    'Kamis': 4,
                    'Jumat': 5,
                    'Sabtu': 6,
                    'Minggu': 0
                };
                return mapping[hari];
            }

            function getHariEnglish(hari) {
                const mapping = {
                    'Senin': 'Monday',
                    'Selasa': 'Tuesday',
                    'Rabu': 'Wednesday',
                    'Kamis': 'Thursday',
                    'Jumat': 'Friday',
                    'Sabtu': 'Saturday',
                    'Minggu': 'Sunday'
                };
                return mapping[hari];
            }

            // Update Stats
            function updateStats() {
                const tahunAkademikId = $('#filter_tahun_akademik').val();
                const kelasId = $('#filter_kelas').val();

                $.ajax({
                    url: "{{ route('admin.jadwal.index') }}",
                    data: {
                        calendar: true,
                        tahun_akademik_id: tahunAkademikId,
                        kelas_id: kelasId
                    },
                    success: function(data) {
                        $('#total-jadwal').text(data.length);

                        // Count unique kelas
                        const uniqueKelas = new Set(data.map(j => j.extendedProps.kelas));
                        $('#total-kelas').text(uniqueKelas.size);

                        // Count unique guru
                        const uniqueGuru = new Set(data.map(j => j.extendedProps.guru));
                        $('#total-guru').text(uniqueGuru.size);

                        // Count conflicts (simplified - you may want to implement proper conflict detection)
                        $('#total-konflik').text('0');
                    }
                });
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
                        console.log(data);
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
            updateStats();

            // Filter Change
            $('#filter_tahun_akademik').change(function() {
                loadKelasOptions();
            });

            $('#btn-filter').click(function() {
                table.draw();
                if (calendar) {
                    calendar.refetchEvents();
                }
                if (currentView === 'grid') {
                    loadGridView();
                }
                updateStats();
            });

            $('#btn-reset').click(function() {
                $('#filter_kelas').val('').trigger('change');
                $('#filter_hari').val('');
                table.draw();
                if (calendar) {
                    calendar.refetchEvents();
                }
                if (currentView === 'grid') {
                    loadGridView();
                }
                updateStats();
            });

            // Add Button
            $('#btn-add').click(function() {
                $('#jadwal-form')[0].reset();
                $('#jadwal_id').val('');
                $('#modal-title').html('<i class="fas fa-calendar-plus mr-2"></i>Tambah Jadwal Pelajaran');
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
                        $('#modal-title').html(
                            '<i class="fas fa-edit mr-2"></i>Edit Jadwal Pelajaran');
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
                        $('#detail-kelas').html('<strong>' + data.kelas + '</strong>');
                        $('#detail-mapel').html('<span class="badge badge-primary">' + data.mapel +
                            '</span>');
                        $('#detail-guru').html('<i class="fas fa-user-tie mr-1"></i>' + data.guru);
                        $('#detail-hari').html('<span class="badge badge-info">' + data.hari +
                            '</span>');
                        $('#detail-waktu').html('<i class="fas fa-clock mr-1"></i>' + data.waktu);
                        $('#detail-ruangan').html('<i class="fas fa-door-open mr-1"></i>' + (data
                            .ruangan || '-'));
                        $('#detail-tahun').html('<strong>' + data.tahun_akademik + '</strong>');
                        $('#detail-modal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil detail jadwal.', 'error');
                    }
                });
            }

            // Make viewDetail globally accessible for grid view
            window.viewDetail = viewDetail;

            // Save Form
            $('#jadwal-form').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const id = $('#jadwal_id').val();
                const url = id ? "{{ url('admin/jadwal') }}/" + id : "{{ route('admin.jadwal.store') }}";
                const method = id ? 'PUT' : 'POST';

                $('#btn-save').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

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
                            if (currentView === 'grid') {
                                loadGridView();
                            }
                            updateStats();
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
                    },
                    complete: function() {
                        $('#btn-save').prop('disabled', false).html(
                            '<i class="fas fa-save mr-1"></i> Simpan');
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    html: "Jadwal <strong>\"" + nama + "\"</strong> akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, hapus!',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal'
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
                                    if (currentView === 'grid') {
                                        loadGridView();
                                    }
                                    updateStats();
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

            // Print
            $('#btn-print').click(function() {
                window.print();
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

            // Add moment.js via CDN if not already included
            if (typeof moment === 'undefined') {
                $('<script>').attr('src', 'https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js').appendTo(
                    'head');
            }
        });
    </script>
@endpush
