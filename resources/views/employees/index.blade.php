@extends('layouts.app')
@section('title', 'List Karyawan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <!-- info/alert -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="container-fluid mt-3">
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}

                            <button type="button" class="btn-close" data-bs-dismiss="alert">
                            </button>
                        </div>
                    </div>
                @endif
                <!-- endinfo -->

                <!-- Advanced Filters -->
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter Karyawan
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('employees.index') }}">
                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Cari Nama Karyawan atau NIK
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone">
                                            </iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control" placeholder="Nama atau NIK..."
                                            value="{{ request('search') }}">
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Status Karyawan
                                    </label>

                                    <select name="employee_status_id" class="form-select">
                                        <option value="">Semua Status</option>

                                        @foreach($employeeStatuses as $status)
                                            <option value="{{ $status->id }}" {{ request('employee_status_id') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Button --}}
                                <div class="col-md-2">

                                    <label class="form-label d-block">
                                        &nbsp;
                                    </label>

                                    <div class="d-flex gap-2">

                                        <button type="submit" class="btn btn-primary">
                                            Filter
                                        </button>

                                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                            Reset
                                        </a>

                                    </div>

                                </div>

                            </div>


                        </form>

                    </div>

                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">
                                        Data Karyawan
                                    </h4>
                                    <p class="text-muted mb-0"> Ada {{ $employees->total() }} karyawan yang ditemukan dalam
                                        sistem Anda.
                                    </p>
                                </div>
                                <div>
                                    @can('employee.create')
                                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                            + Tambah Karyawan
                                        </a>
                                    @endcan
                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table class="table table-hover align-middle">

                                        <thead>

                                            <tr>
                                                <th>No</th>
                                                <th>Foto</th>
                                                <th>Nama Lengkap</th>
                                                <th>Jabatan</th>
                                                <th>Kontak</th>

                                                <th>Akun Login</th>
                                                <th>Status</th>
                                                <th width="100">Aksi</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            @forelse($employees as $employee)

                                                <tr>

                                                    <td>
                                                        {{ $employees->firstItem() + $loop->index }}
                                                    </td>

                                                    <td>
                                                        @if($employee->photo)
                                                            <img src="{{ asset('storage/' . $employee->photo) }}" width="50"
                                                                height="50" class="rounded-circle object-fit-cover"
                                                                alt="{{ $employee->full_name }}">
                                                        @else
                                                            @php
                                                                $nameParts = explode(' ', trim($employee->full_name));
                                                                $initials = '';

                                                                foreach ($nameParts as $part) {
                                                                    if (!empty($part)) {
                                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                                    }
                                                                }

                                                                $initials = substr($initials, 0, 2);
                                                            @endphp

                                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                                style="width:50px;height:50px;font-size:18px;font-weight:600;">
                                                                {{ $initials }}
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {{ $employee->full_name }} <br>
                                                        <small class="text-muted"><B>NIK :</B> {{ $employee->nik }}</small>
                                                    </td>

                                                    <td>{{ $employee->position?->name }}</td>

                                                    <td>{{ $employee->phone }}
                                                        <br>
                                                        <small class="text-muted">{{ $employee->email }}</small>
                                                    </td>

                                                    <td>
                                                        @if($employee->user_id)

                                                            <span class="badge bg-success">

                                                                <iconify-icon icon="solar:user-check-bold-duotone" class="me-1">
                                                                </iconify-icon>
                                                                Sudah Ada
                                                            </span>

                                                        @else

                                                            <span class="badge bg-danger">
                                                                <iconify-icon icon="solar:user-cross-bold-duotone" class="me-1">
                                                                </iconify-icon>
                                                                Belum Ada
                                                            </span>

                                                        @endif
                                                    </td>

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

                                                    <td>

                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle"
                                                                data-bs-toggle="dropdown">

                                                                Aksi

                                                            </button>

                                                            <ul class="dropdown-menu">

                                                                @can('employee.view')
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('employees.show', $employee) }}">
                                                                            Detail
                                                                        </a>
                                                                    </li>
                                                                @endcan

                                                                @can('employee.edit')
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('employees.edit', $employee) }}">
                                                                            Edit
                                                                        </a>
                                                                    </li>
                                                                @endcan

                                                                @can('employee.delete')
                                                                    <li>
                                                                        <form action="{{ route('employees.destroy', $employee) }}"
                                                                            method="POST" class="delete-form">

                                                                            @csrf
                                                                            @method('DELETE')

                                                                            <button type="button"
                                                                                class="dropdown-item text-danger btn-delete">

                                                                                Hapus

                                                                            </button>

                                                                        </form>
                                                                    </li>
                                                                @endcan

                                                            </ul>

                                                        </div>

                                                    </td>

                                                </tr>

                                            @empty

                                                <tr>

                                                    <td colspan="8" class="text-center">

                                                        Data tidak ditemukan

                                                    </td>

                                                </tr>

                                            @endforelse

                                        </tbody>

                                    </table>

                                </div>

                                {{ $employees->links() }}

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {

            button.addEventListener('click', function () {

                let form = this.closest('form');

                Swal.fire({
                    title: 'Hapus Karyawan?',
                    text: 'Data karyawan akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });
    </script>
@endsection