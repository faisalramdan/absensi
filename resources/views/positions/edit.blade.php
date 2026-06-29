@extends('layouts.app')
@section('title', 'Perbarui Jabatan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            <!-- Start Container Fluid -->
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <iconify-icon icon="solar:user-plus-bold-duotone" class="me-2"></iconify-icon>
                                    Perbarui Jabatan
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('positions.update', $position) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="code" class="form-label fw-semibold">
                                                Kode Jabatan
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code', $position->code) }}" required
                                                placeholder="Masukkan kode jabatan" maxlength="255">
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">kode jabatan tidak boleh sama</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label fw-semibold">
                                                Nama Jabatan
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $position->name) }}" required
                                                placeholder="Masukkan nama jabatan" maxlength="255">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Nama jabatan yang akan ditampilkan di
                                                seluruh sistem</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                name="description" rows="3"
                                                placeholder="Opsional deskripsi/Global keterangan...">{{ old('description', $position->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Status Jabatan
                                            </label>

                                            <div class="form-check form-switch">

                                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                    {{ $position->is_active ? 'checked' : '' }}>

                                                <label class="form-check-label">

                                                    {{ $position->is_active ? 'Aktif' : 'Non Aktif' }}

                                                </label>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Perbarui</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="{{ route('positions.index') }}"
                                                    class="btn btn-outline-secondary w-100">
                                                    Cancel </a>
                                            </div>

                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                    {{-- Sidebar --}}
                    <div class="col-xl-4">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Informasi
                                </h4>
                            </div>

                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <strong>Dibuat : </strong> {{ $position->creator?->name ?? '-' }}<br>
                                        {{ $position->created_at->format('d M Y H:i') }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Terakhir Diubah : </strong>
                                        {{ $position->updater?->name ?? '-' }}<br>
                                        {{ $position->updated_at->diffForHumans() }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">


    </div>

@endsection