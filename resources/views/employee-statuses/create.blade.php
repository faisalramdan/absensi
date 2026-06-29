@extends('layouts.app')

@section('title', 'Buat Status Karyawan')

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
                                    Membuat Status Karyawan
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employee-statuses.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="code" class="form-label fw-semibold">
                                                Kode
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code') }}" required
                                                placeholder="Masukkan kode " maxlength="255">
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">kode tidak boleh sama</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label fw-semibold">
                                                Nama Status Karyawan
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required
                                                placeholder="Masukkan nama status karyawan" maxlength="255">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Status karyawan yang akan ditampilkan di
                                                seluruh sistem</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                name="description" rows="3"
                                                placeholder="Opsional deskripsi/Global keterangan...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Status
                                            </label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                    checked>
                                                <label class="form-check-label">
                                                    Aktif
                                                </label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Simpan</button>
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


                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">


    </div>

@endsection