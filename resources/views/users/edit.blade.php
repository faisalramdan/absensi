@extends('layouts.app')

@section('title', 'Perbarui Pengguna')

@section('content')
<div class="wrapper">
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <iconify-icon icon="solar:user-pen-bold-duotone" class="me-2"></iconify-icon>
                                Edit Pengguna
                            </h4>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('users.update', $user) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">

                                    {{-- Nama --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <iconify-icon icon="solar:user-bold" class="me-1"></iconify-icon>
                                            Nama Lengkap
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}"
                                            required>

                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <iconify-icon icon="solar:letter-bold" class="me-1"></iconify-icon>
                                            Email
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}"
                                            required>

                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Password --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <iconify-icon icon="solar:lock-password-bold"
                                                class="me-1"></iconify-icon>
                                            Password Baru
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password"
                                                name="password"
                                                placeholder="Kosongkan jika tidak ingin mengganti password">

                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                onclick="togglePassword('password')">

                                                <iconify-icon
                                                    icon="solar:eye-bold"
                                                    id="password-icon">
                                                </iconify-icon>

                                            </button>
                                        </div>

                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <small class="text-muted">
                                            Biarkan kosong jika password tidak ingin diubah.
                                        </small>
                                    </div>

                                    {{-- Konfirmasi Password --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <iconify-icon icon="solar:lock-password-bold"
                                                class="me-1"></iconify-icon>
                                            Konfirmasi Password Baru
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="password_confirmation"
                                                name="password_confirmation"
                                                placeholder="Masukkan ulang password baru">

                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                onclick="togglePassword('password_confirmation')">

                                                <iconify-icon
                                                    icon="solar:eye-bold"
                                                    id="password_confirmation-icon">
                                                </iconify-icon>

                                            </button>
                                        </div>
                                    </div>

                                    

                                    {{-- Roles --}}
                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">
                                            <iconify-icon
                                                icon="solar:shield-user-bold-duotone"
                                                class="me-1">
                                            </iconify-icon>
                                            Role
                                        </label>

                                        <div class="row">
                                            @foreach($roles as $role)
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio" name="roles[]"value="{{ $role->name }}"
                                                            id="role{{ $role->id }}"{{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                            {{ $employee ? 'disabled' : '' }}>

                                                        <label
                                                            class="form-check-label" for="role{{ $role->id }}">{{ $role->name }}
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

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="is_active"
                                                value="1"
                                                {{ $user->is_active ? 'checked' : '' }}>

                                            <label class="form-check-label">

                                                {{ $user->is_active ? 'Aktif' : 'Non Aktif' }}

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
                                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
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
                                Informasi Akun
                            </h4>
                        </div>

                        <div class="card-body">

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Dibuat :  </strong> {{ $user->creator?->name ?? '-' }}<br>
                                    {{ $user->created_at->format('d M Y H:i') }}
                                </li>

                                <li class="list-group-item">
                                    <strong>Terakhir Diubah : </strong> {{ $user->updater?->name ?? '-' }}<br>
                                    {{ $user->updated_at->diffForHumans() }}
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