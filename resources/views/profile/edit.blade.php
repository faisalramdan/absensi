@extends('layouts.app')
@section('title', 'Profile')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">

                                    {{-- Photo --}}
                                    <div class="col-lg-2 text-lg-center">
                                        <div class="bg-body d-flex align-items-center justify-content-center rounded py-4">
                                            <img src="{{ !empty($employee?->photo) ? asset('storage/' . $employee->photo) : asset('images/default-avatar.png') }}"
                                                alt="{{ $employee->full_name }}"
                                                class="avatar-xxl rounded-circle object-fit-cover">
                                        </div>

                                        <div class="mt-3">
                                            @if($employee->is_active)
                                                <span class="badge bg-success-subtle text-success">
                                                    {{ $employee->status?->name ?? 'Active' }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    {{ $employee->status?->name ?? 'Inactive' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Information --}}
                                    <div class="col-lg-10">

                                        <h4 class="mb-1">
                                            {{ $employee->full_name ?? '-' }}
                                        </h4>

                                        <p class="text-muted mb-3">
                                            {{ $employee->position?->name ?? '-' }}
                                        </p>

                                        <div class="row">

                                            <div class="col-md-6">



                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <div
                                                        class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                                        <iconify-icon icon="solar:letter-bold-duotone"
                                                            class="fs-20 text-primary"></iconify-icon>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted d-block">Email</small>
                                                        <span class="fw-semibold">
                                                            {{ $employee->email ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <div
                                                        class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                                        <iconify-icon icon="solar:outgoing-call-rounded-bold-duotone"
                                                            class="fs-20 text-primary"></iconify-icon>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted d-block">Phone</small>
                                                        <span class="fw-semibold">
                                                            {{ $employee->phone ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-6">

                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <div
                                                        class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                                        <iconify-icon icon="solar:square-academic-cap-2-bold-duotone"
                                                            class="fs-20 text-primary"></iconify-icon>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted d-block">Education</small>
                                                        <span class="fw-semibold">
                                                            {{ $employee->education ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>



                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <div
                                                        class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                                        <iconify-icon icon="solar:calendar-bold-duotone"
                                                            class="fs-20 text-primary"></iconify-icon>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted d-block">Join Date</small>

                                                        @if($employee->join_date)
                                                            @php
                                                                $joinDate = \Carbon\Carbon::parse($employee->join_date);
                                                                $duration = $joinDate->diff(now());

                                                                $masaKerja = collect([
                                                                    $duration->y ? $duration->y . ' Tahun' : null,
                                                                    $duration->m ? $duration->m . ' Bulan' : null,
                                                                ])->filter()->implode(' ');
                                                            @endphp

                                                            <span class="fw-semibold">
                                                                {{ $joinDate->format('d M Y') }}
                                                            </span>

                                                            <small class="text-success d-block">
                                                                Masa Kerja {{ $masaKerja }}
                                                            </small>
                                                        @else
                                                            <span>-</span>
                                                        @endif

                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2 mb-3">
                                                    <div
                                                        class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                                        <iconify-icon icon="solar:map-point-bold-duotone"
                                                            class="fs-20 text-primary"></iconify-icon>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted d-block">Address</small>
                                                        <span class="fw-semibold">
                                                            {{ $employee->address ?? '-' }}
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="row">
                        <div class="col-xl-8 col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">About</h4>
                                </div>
                                <div class="card-body">
                                    <p>I'm the model of the new Project Head Manager. I've combined a deep background in
                                        brand management at blue chip CPG companies with eCommerce growth marketing at the
                                        world's biggest retailer. I've run SingleFire I've created world-class campaigns;
                                        I've built digital marketing organizations from the ground up. I have over 20 years'
                                        experience leading... <a href="#!" class="text-primary">See more</a></p>
                                    <h4 class="card-title mb-2">My Approach :</h4>
                                    <p>When it comes to Project Head Manager, I believe in a holistic approach that combines
                                        creativity with technical expertise. I start by understanding your unique vision and
                                        goals, then work tirelessly to bring that vision to life. Whether you need a sleek
                                        portfolio site, an engaging e-commerce platform, or anything in between, I've got
                                        you covered.</p>
                                    <div class="row g-2 mt-2 mb-4">
                                        <div class="col-lg-6">
                                            <div class="border p-3 rounded">
                                                <h4 class="card-title">Marketing expertise</h4>
                                                <div class="d-flex gap-2 flex-wrap mt-3">
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Leadership</span>
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Advertising</span>
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Public-relations</span>
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Branding-manage</span>
                                                </div>
                                                <p class="mb-0 text-dark mt-3">Open to networking :<span
                                                        class="badge bg-success-subtle text-success ms-1">Yes</span></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="border p-3 rounded">
                                                <h4 class="card-title">Marketing interests</h4>
                                                <div class="d-flex gap-2 flex-wrap mt-3">
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Event-marketing</span>
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Performance-marketing</span>
                                                    <span
                                                        class="badge bg-body text-muted px-2 py-1 fs-12">#Account-based-marketing</span>
                                                </div>
                                                <p class="mb-0 text-dark mt-3">Open to advising :<span
                                                        class="badge bg-success-subtle text-success ms-1">Yes</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="card-title">My Core Skills :</h4>
                                    <div class="row mt-2">
                                        <div class="col-lg-4">
                                            <div class="d-flex align-items-center justify-content-satrt gap-2">
                                                <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                </ul>
                                                <p class="fw-medium mb-0 text-dark fs-15">Inbound Marketing</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex align-items-center justify-content-satrt gap-2">
                                                <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                </ul>
                                                <p class="fw-medium mb-0 text-dark fs-15">Entrepreneurship</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex align-items-center justify-content-satrt gap-2">
                                                <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                    <li>
                                                        <i class="bx bxs-star"></i>
                                                    </li>
                                                </ul>
                                                <p class="fw-medium mb-0 text-dark fs-15">Growth Marketing</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                            <div class="card">
                                <form method="POST" action="{{ route('password.update') }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="card-header">
                                        <h4 class="card-title mb-0">
                                            <iconify-icon icon="solar:lock-password-bold-duotone"
                                                class="me-2"></iconify-icon>
                                            Informasi Kata Sandi
                                        </h4>
                                    </div>

                                    <div class="card-body">
                                        <div class="card">
                                            {{-- Current Password --}}
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label fw-semibold">
                                                    <iconify-icon icon="solar:lock-keyhole-bold"
                                                        class="me-1"></iconify-icon>
                                                    Kata Sandi Saat Ini
                                                </label>

                                                <div class="input-group">
                                                    <input type="password"
                                                        class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                                        id="current_password" name="current_password"
                                                        placeholder="Masukkan kata sandi saat ini">

                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('current_password')">
                                                        <iconify-icon icon="solar:eye-bold" id="current_password-icon">
                                                        </iconify-icon>
                                                    </button>
                                                </div>
                                                <small class="form-text text-muted">Masukkan kata sandi Anda saat ini untuk
                                                    memverifikasi identitas Anda sebelum mengubah
                                                    Identitas Anda.</small>

                                                @error('current_password', 'updatePassword')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- New Password --}}
                                            <div class="mb-3">
                                                <label for="password" class="form-label fw-semibold">
                                                    <iconify-icon icon="solar:lock-password-bold"
                                                        class="me-1"></iconify-icon>
                                                    Kata Sandi Baru
                                                </label>

                                                <div class="input-group">
                                                    <input type="password"
                                                        class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                                        id="password" name="password"
                                                        placeholder="Masukkan Kata Sandi Baru">

                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('password')">
                                                        <iconify-icon icon="solar:eye-bold" id="password-icon">
                                                        </iconify-icon>
                                                    </button>
                                                </div>
                                                <small class="form-text text-muted">Biarkan kosong agar tetap terkini
                                                    kata sandi</small>

                                                @error('password', 'updatePassword')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- Confirm Password --}}
                                            <div class="mb-4">
                                                <label for="password_confirmation" class="form-label fw-semibold">
                                                    <iconify-icon icon="solar:shield-keyhole-bold"
                                                        class="me-1"></iconify-icon>
                                                    Konfirmasi Kata Sandi Baru
                                                </label>

                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password_confirmation"
                                                        name="password_confirmation"
                                                        placeholder="Konfirmasi kata sandi baru">

                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('password_confirmation')">
                                                        <iconify-icon icon="solar:eye-bold" id="password_confirmation-icon">
                                                        </iconify-icon>
                                                    </button>
                                                </div>
                                                <small class="form-text text-muted">Harus sesuai dengan kata sandi di atas
                                                    jika
                                                    mengubah</small>
                                            </div>

                                            <div class="d-flex gap-2">

                                                <button type="reset" class="btn btn-secondary">
                                                    <iconify-icon icon="solar:close-circle-bold"
                                                        class="me-1"></iconify-icon>
                                                    Mengatur ulang
                                                </button>

                                                <button type="submit" class="btn btn-primary" id="update-password-btn">
                                                    <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon>
                                                    Perbarui Kata Sandi
                                                </button>

                                            </div>

                                            @if (session('status') === 'password-updated')
                                                <div class="alert alert-success mt-3 mb-0">
                                                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                                                    Kata sandi berhasil diperbarui.
                                                </div>
                                            @endif

                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
    </div>
    <script>
        // Password toggle functionality
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('icon', 'solar:eye-closed-bold');
            } else {
                field.type = 'password';
                icon.setAttribute('icon', 'solar:eye-bold');
            }
        } 
    </script>
@endsection