@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Tambah Kalender Akademik</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.kalender.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="tahun_akademik_id">Tahun Akademik *</label>
                            <select name="tahun_akademik_id" id="tahun_akademik_id"
                                class="form-control @error('tahun_akademik_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach ($tahunAkademikList as $tahun)
                                    <option value="{{ $tahun->id }}"
                                        {{ old('tahun_akademik_id') == $tahun->id ? 'selected' : '' }}>
                                        {{ $tahun->nama_tahun_akademik }} {{ $tahun->status_aktif ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_akademik_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tanggal">Tanggal *</label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}"
                                required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jenis_libur">Jenis Libur *</label>
                            <select name="jenis_libur" id="jenis_libur"
                                class="form-control @error('jenis_libur') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Libur --</option>
                                @foreach ($jenisLiburList as $jenis)
                                    <option value="{{ $jenis }}"
                                        {{ old('jenis_libur') == $jenis ? 'selected' : '' }}>
                                        {{ ucfirst($jenis) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_libur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan *</label>
                            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                rows="3" required>{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('admin.kalender.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
