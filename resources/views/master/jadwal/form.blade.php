@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ isset($jadwal) ? 'Edit' : 'Tambah' }} Jadwal Pelajaran</h4>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    <form
                        action="{{ isset($jadwal) ? route('admin.jadwal.update', $jadwal->id) : route('admin.jadwal.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($jadwal))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label>Guru - Mata Pelajaran - Kelas <span class="text-danger">*</span></label>
                            <select name="guru_kelas_id" class="form-control @error('guru_kelas_id') is-invalid @enderror"
                                required>
                                <option value="">Pilih...</option>
                                @foreach ($guruKelasList as $gk)
                                    <option value="{{ $gk->id }}"
                                        {{ old('guru_kelas_id') == $gk->id || (isset($jadwal) && $jadwal->guru_kelas_id == $gk->id && !old('guru_kelas_id')) ? 'selected' : '' }}>
                                        {{ $gk->guruMapel->guru->nama ?? '-' }} -
                                        {{ $gk->guruMapel->mapel->nama_mapel ?? '-' }} -
                                        {{ $gk->kelas->nama_lengkap ?? '-' }}
                                        ({{ $gk->tahunAkademik->nama_tahun_akademik ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-control @error('hari') is-invalid @enderror" required>
                                <option value="">Pilih Hari</option>
                                @foreach ($hariList as $hari)
                                    <option value="{{ $hari }}"
                                        {{ old('hari') == $hari || (isset($jadwal) && $jadwal->hari == $hari) ? 'selected' : '' }}>
                                        {{ $hari }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_mulai"
                                        class="form-control @error('jam_mulai') is-invalid @enderror"
                                        value="{{ isset($jadwal) ? $jadwal->jam_mulai->format('H:i') : old('jam_mulai') }}"
                                        required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_selesai"
                                        class="form-control @error('jam_selesai') is-invalid @enderror"
                                        value="{{ isset($jadwal) ? $jadwal->jam_selesai->format('H:i') : old('jam_selesai') }}"
                                        required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Ruangan</label>
                            <input type="text" name="ruangan" class="form-control @error('ruangan') is-invalid @enderror"
                                value="{{ isset($jadwal) ? $jadwal->ruangan : old('ruangan') }}"
                                placeholder="Contoh: Lab Komputer 1">
                            @error('ruangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
