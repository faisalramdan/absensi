@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h4 class="mb-0">
                        Detail Karyawan
                    </h4>

                    <div>

                        <a href="{{ route('employees.index') }}" class="btn btn-soft-secondary">
                            <iconify-icon icon="solar:arrow-left-broken" class="me-1">
                            </iconify-icon>
                            Kembali
                        </a>
                        @can('employee.edit')
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-soft-primary">
                                <iconify-icon icon="solar:pen-2-broken" class="me-1">
                                </iconify-icon>
                                Edit
                            </a>
                        @endcan
                    </div>

                </div>
                <div class="row">

                    {{-- SIDEBAR --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">

                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" class="rounded-circle mb-3 border"
                                        width="150" height="150" style="object-fit: cover;">
                                @else
                                    @php
                                        $words = explode(' ', trim($employee->full_name));
                                        $initials = '';

                                        foreach ($words as $word) {
                                            $initials .= strtoupper(substr($word, 0, 1));
                                        }

                                        $initials = substr($initials, 0, 2);
                                    @endphp

                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="
                                                                                                                                                        width:150px;
                                                                                                                                                        height:150px;
                                                                                                                                                        background:#0d6efd;
                                                                                                                                                        color:white;
                                                                                                                                                        font-size:48px;
                                                                                                                                                        font-weight:700;
                                                                                                                                                    ">
                                        {{ $initials }}
                                    </div>
                                @endif

                                <h4>
                                    {{ $employee->full_name }}<i class='bx bxs-badge-check text-success align-middle'></i>
                                </h4>

                                <p class="text-muted">

                                    {{ $employee->position?->name ?? '-' }}

                                </p>

                                <hr>

                                <table class="table table-borderless">

                                    <tr>
                                        <th>NIK</th>
                                        <td>{{ $employee->nik }}</td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>

                                        <td>

                                            @if($employee->is_active)

                                                <span class="badge bg-success">

                                                    Aktif

                                                </span>

                                            @else

                                                <span class="badge bg-danger">

                                                    Non Aktif

                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                    <tr>
                                        <th>Akun Login</th>
                                        <td>
                                            @if($employee->user)
                                                <span class="badge bg-success">
                                                    Sudah Ada
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    Belum Ada
                                                </span>
                                            @endif
                                        </td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>


                    {{-- CONTENT --}}
                    <div class="col-lg-8">

                        {{-- INFORMASI PRIBADI --}}
                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">Informasi Pribadi</h4>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Email
                                        </label>

                                        <p class="text-primary fw-semibold">
                                            {{ $employee->email ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Telepon
                                        </label>

                                        <p class="text-primary fw-semibold">
                                            {{ $employee->phone ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Jenis Kelamin
                                        </label>

                                        <p>
                                            {{ $employee->gender ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Pendidikan
                                        </label>

                                        <p>
                                            {{ $employee->education ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Tempat Lahir
                                        </label>

                                        <p>
                                            {{ $employee->birth_place ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Tanggal Lahir
                                        </label>

                                        <p>
                                            {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('d M Y') : '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Alamat
                                        </label>

                                        <p>
                                            {{ $employee->address ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Nomor KTP
                                        </label>

                                        <p>
                                            {{ $employee->ktp_number ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- INFORMASI PEGAWAI --}}
                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title">Informasi Kepegawaian</h4>

                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Perusahaan
                                        </label>

                                        <p>
                                            {{ $employee->company?->name ?? '-' }}
                                        </p>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Jabatan
                                        </label>

                                        <p>
                                            {{ $employee->position?->name ?? '-' }}
                                        </p>

                                    </div>



                                    <div class="col-md-6 mb-3">

                                        <label class="fw-semibold">
                                            Tanggal Bergabung
                                        </label>
                                        @if($employee->join_date)
                                            @php
                                                $joinDate = \Carbon\Carbon::parse($employee->join_date);
                                                $duration = $joinDate->diff(now());
                                            @endphp

                                            <p>
                                                {{ $joinDate->format('d M Y') }}
                                                <span class="text-muted ms-2">
                                                    ({{ $duration->y }} Tahun {{ $duration->m }} Bulan)
                                                </span>
                                            </p>


                                        @else
                                            <span class="fw-semibold">-</span>
                                        @endif


                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- AKUN LOGIN --}}
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Akun Login</h4>
                            </div>

                            <div class="card-body">
                                @if($employee->user)
                                    <table class="table table-hover align-middle">
                                        <tr>
                                            <th>Nama User</th>
                                            <td>{{ $employee->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email Login</th>
                                            <td>{{ $employee->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Role</th>
                                            <td class="text-primary fw-semibold">{{ $employee->role?->name ?? '-' }}</td>
                                        </tr>
                                    </table>

                                @else

                                    <div class="alert alert-warning">

                                        Karyawan belum memiliki akun login.

                                    </div>

                                @endif

                            </div>

                        </div>

                        {{-- KONTAK DARURAT --}}
                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title">Kontak Darurat</h4>

                            </div>

                            <div class="card-body">

                                @forelse($employee->emergencyContacts as $contact)

                                    <div class="border rounded p-3 mb-3">

                                        <h6>

                                            {{ $contact->name }}

                                        </h6>

                                        <p class="mb-1">

                                            Hubungan:
                                            {{ $contact->relationship }}

                                        </p>

                                        <p class="mb-0">

                                            Telepon:
                                            {{ $contact->phone }}

                                        </p>

                                    </div>

                                @empty

                                    <div class="alert alert-light">

                                        Belum ada kontak darurat.

                                    </div>

                                @endforelse

                            </div>

                        </div>

                        {{-- AUDIT --}}
                        <div class="card">

                            <div class="card-header">

                                <h4 class="card-title">Audit Information</h4>

                            </div>

                            <div class="card-body">

                                <table class="table">

                                    <tr>
                                        <th>Dibuat Oleh</th>
                                        <td>{{ $employee->creator?->name ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Dibuat Pada</th>
                                        <td>{{ $employee->created_at?->format('d M Y H:i') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Diubah Oleh</th>
                                        <td>{{ $employee->updater?->name ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Diubah Pada</th>
                                        <td>{{ $employee->updated_at?->format('d M Y H:i') }}</td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection