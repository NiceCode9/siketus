@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Kalender Akademik</h4>
                    <a href="{{ route('admin.kalender.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Libur
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Tahun Akademik -->
                    <form method="GET" action="{{ route('admin.kalender.index') }}" class="mb-4">
                        <div class="form-row align-items-center">
                            <div class="col-md-4">
                                <label for="tahun_akademik_id">Tahun Akademik:</label>
                                <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-control"
                                    onchange="this.form.submit()">
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @foreach ($tahunAkademikList as $tahun)
                                        <option value="{{ $tahun->id }}"
                                            {{ $tahunAkademikId == $tahun->id ? 'selected' : '' }}>
                                            {{ $tahun->nama_tahun_akademik }} {{ $tahun->status_aktif ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="20%">Jenis Libur</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%">Tahun Akademik</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kalenderList as $index => $kalender)
                                    <tr>
                                        <td>{{ ($kalenderList->currentPage() - 1) * $kalenderList->perPage() + $index + 1 }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($kalender->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $kalender->jenis_libur == 'nasional' ? 'danger' : ($kalender->jenis_libur == 'ujian' ? 'warning' : 'info') }}">
                                                {{ ucfirst($kalender->jenis_libur) }}
                                            </span>
                                        </td>
                                        <td>{{ $kalender->keterangan }}</td>
                                        <td>{{ $kalender->tahunAkademik->nama_tahun_akademik ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.kalender.edit', $kalender->id) }}"
                                                    class="btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.kalender.destroy', $kalender->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data kalender akademik</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $kalenderList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
