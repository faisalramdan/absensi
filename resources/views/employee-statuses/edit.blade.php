@extends('layouts.app')

@section('title', 'Perbarui Status Karyawan')

@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <div class="row">

                    {{-- Form --}}
                    <div class="col-xl-8">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <iconify-icon icon="solar:user-plus-bold-duotone" class="me-2"></iconify-icon>
                                    Perbarui Status Karyawan
                                </h4>
                            </div>

                            <div class="card-body">

                                <form action="{{ route('employee-statuses.update', $status) }}" method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="row">

                                        {{-- Kode --}}
                                        <div class="col-md-6 mb-3">

                                            <label for="code" class="form-label fw-semibold">
                                                Kode
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code', $status->code) }}" required
                                                maxlength="50" placeholder="Masukkan kode status">

                                            @error('code')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <small class="text-muted">
                                                Kode status tidak boleh sama
                                            </small>

                                        </div>

                                        {{-- Nama --}}
                                        <div class="col-md-6 mb-3">

                                            <label for="name" class="form-label fw-semibold">
                                                Nama Status Karyawan
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $status->name) }}" required
                                                maxlength="255" placeholder="Masukkan nama status">

                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <small class="text-muted">
                                                Nama status yang akan tampil di seluruh sistem
                                            </small>

                                        </div>

                                        {{-- Deskripsi --}}
                                        <div class="col-md-12 mb-3">

                                            <label class="form-label fw-semibold">
                                                Deskripsi
                                            </label>

                                            <textarea name="description" rows="4"
                                                class="form-control @error('description') is-invalid @enderror"
                                                placeholder="Masukkan deskripsi status (opsional)">{{ old('description', $status->description) }}</textarea>

                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                        {{-- Status --}}
                                        <div class="col-md-12 mb-3">

                                            <label class="form-label fw-semibold">
                                                Status
                                            </label>

                                            <div class="form-check form-switch">

                                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                    {{ old('is_active', $status->is_active) ? 'checked' : '' }}>

                                                <label class="form-check-label">
                                                    {{ $status->is_active ? 'Aktif' : 'Non Aktif' }}
                                                </label>

                                            </div>

                                        </div>

                                    </div>

                                    {{-- Preview Status --}}
                                    <div class="alert alert-light border">

                                        <strong>Status Saat Ini :</strong>

                                        @if($status->is_active)
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Non Aktif
                                            </span>
                                        @endif

                                    </div>

                                    <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Perbarui</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="{{ route('employee-statuses.index') }}"
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
                                <h4 class="card-title mb-0">
                                    Informasi Data
                                </h4>
                            </div>

                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item">

                                        <strong>Dibuat Oleh :</strong><br>

                                        {{ $status->creator?->name ?? '-' }}

                                        <br>

                                        <small class="text-muted">
                                            {{ $status->created_at?->format('d M Y H:i') }}
                                        </small>

                                    </li>

                                    <li class="list-group-item">

                                        <strong>Terakhir Diubah Oleh :</strong><br>

                                        {{ $status->updater?->name ?? '-' }}

                                        <br>

                                        <small class="text-muted">
                                            {{ $status->updated_at?->format('d M Y H:i') }}
                                        </small>

                                    </li>



                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection