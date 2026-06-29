@extends('layouts.app')
@section('title', 'Buat Pengguna')
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
                                    Membuat pengguna
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('users.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label fw-semibold">
                                                <iconify-icon icon="solar:user-bold" class="me-1"></iconify-icon>Nama
                                                Lengkap
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required
                                                placeholder="Masukkan nama lengkap pengguna" maxlength="255">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Nama lengkap yang akan ditampilkan di
                                                seluruh sistem</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label fw-semibold">
                                                <iconify-icon icon="solar:letter-bold" class="me-1"></iconify-icon>Alamat
                                                Email
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}" required
                                                placeholder="user@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Digunakan untuk login dan notifikasi
                                                sistem</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label fw-semibold">
                                                <iconify-icon icon="solar:lock-password-bold"
                                                    class="me-1"></iconify-icon>Password
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" required
                                                    placeholder="Masukkan kata sandi yang aman" minlength="8">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="togglePassword('password')">
                                                    <iconify-icon icon="solar:eye-bold" id="password-icon"></iconify-icon>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Minimal 8 karakter dengan huruf besar dan
                                                kecil, angka, dan simbol</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label fw-semibold">
                                                <iconify-icon icon="solar:lock-password-bold"
                                                    class="me-1"></iconify-icon>Konfirmasi
                                                Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password_confirmation"
                                                    name="password_confirmation" required
                                                    placeholder="Masukkan kembali kata sandi">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="togglePassword('password_confirmation')">
                                                    <iconify-icon icon="solar:eye-bold"
                                                        id="password_confirmation-icon"></iconify-icon>
                                                </button>
                                            </div>
                                            <small class="form-text text-muted">Harus sesuai dengan kata sandi di
                                                atas</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="roles" class="form-label fw-semibold">
                                                <iconify-icon icon="solar:archive-check-linear"
                                                    class="me-1"></iconify-icon>Peran <span class="text-danger">*</span>
                                            </label>
                                            <div class="row">
                                                @foreach($roles as $role)
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="roles[]"
                                                                value="{{ $role->name }}" id="role{{ $role->id }}">
                                                            <label class="form-check-label" for="role{{ $role->id }}">
                                                                {{ $role->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                Status Pengguna
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
                                                <a href="{{ route('users.index') }}"
                                                    class="btn btn-outline-secondary w-100">
                                                    Cancel </a>
                                            </div>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Informasi Pengguna
                                </h4>
                            </div>

                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>Catatan:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Email harus unik.</li>
                                        <li>Password minimal 8 karakter.</li>
                                    </ul>
                                </div>
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