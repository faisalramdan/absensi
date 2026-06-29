@extends('layouts.app')
@section('title', 'Attendance Logs')
@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">
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
                {{-- FILTER --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                        <h5 class="mb-0 fw-semibold">Filter Data</h5>
                    </div>

                    <div class="card-body">
                        <form method="GET">
                            <div class="row align-items-end g-3">

                                {{-- 1. Pilih Karyawan --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Pilih Karyawan</label>
                                    <select name="employee_id" class="form-select">
                                        <option value="">-- Semua Karyawan --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $selectedEmployee == $employee->id ? 'selected' : '' }}>
                                                [{{ $employee->nik }}] {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                </div>

                                {{-- 2. Pilih Bulan --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Bulan</label>
                                    <select name="month" class="form-select">
                                        @for($m = 1; $m <= 12; $m++)
                                            @php
                                                $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT);
                                                $monthName = \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F');
                                            @endphp
                                            <option value="{{ $monthValue }}" {{ $selectedMonth == $monthValue ? 'selected' : '' }}>
                                                {{ $monthName }}
                                            </option>
                                        @endfor
                                    </select>
                                    
                                    
                                </div>

                                {{-- 3. Pilih Tahun --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Tahun</label>
                                    <select name="year" class="form-select">
                                        @php
                                            $currentYear = \Carbon\Carbon::now()->format('Y');
                                            // Menampilkan rentang 5 tahun ke belakang dan 1 tahun ke depan
                                            $startYear = $currentYear - 5;
                                            $endYear = $currentYear + 1;
                                        @endphp
                                        @for($y = $startYear; $y <= $endYear; $y++)
                                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                    
                                </div>

                                {{-- Tombol Aksi Filter --}}
                                <div class="col-md-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            Filter
                                        </button>
                                        <a href="{{ route('attendance-logs.index') }}" class="btn btn-secondary w-100">
                                            Reset
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="card">

                    <div class="d-flex card-header justify-content-between align-items-center">

                        <div>
                            <h4 class="card-title">Attendance Logs</h4>
                                    
                            <p class="text-muted mb-0">{{ $attendanceLogs->total() }} data yang ditemukan di sistem Anda.
                            Periode ({{ $dateRangeText }})</p>
                        </div>

                        <div>
                            @can('attendance-log.create')
                                <a href="{{ route('attendance-logs.create') }}" class="btn btn-primary">
                                    + Tambah Log
                                </a>
                            @endcan
                            @can('attendance-log.import')

                                <a href="{{ route('attendance-logs.import.form') }}" class="btn btn-success">
                                    Import Excel
                                </a>

                            @endcan
                        </div>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table align-middle mb-0">

                                <thead>

                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Karyawan</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Source</th>
                                        <th>Note</th>
                                        <th>Aksi</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    @forelse($attendanceLogs as $log)
                                        <tr>
                                            <td>{{ $attendanceLogs->firstItem() + $loop->index }}</td>
                                            <td>
                                                <span class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $log->employee?->full_name ?? '-' }}</div>
                                                <small class="text-muted">{{ $log->employee?->nik }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium">
                                                    {{ $log->check_in ? date('H:i', strtotime($log->check_in)) : '--:--' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $log->check_out ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-muted' }} px-2 py-1 fw-medium">
                                                    {{ $log->check_out ? date('H:i', strtotime($log->check_out)) : '--:--' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-secondary border px-2 py-1 fs-11">
                                                    {{ strtoupper($log->source ?? 'Device') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $log->notes ?? '-' }}</div>
                                                
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('attendance-log.edit')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('attendance-logs.edit', $log) }}">
                                                                    Edit
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('attendance-log.delete')
                                                            <li>
                                                                <form action="{{ route('attendance-logs.destroy', $log) }}"
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
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                Tidak ada data log absensi
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                Menampilkan
                                {{ $attendanceLogs->count() }}
                                dari
                                {{ $attendanceLogs->total() }}
                                data
                            </span>
                            {{ $attendanceLogs->withQueryString()->links() }}
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
                    title: 'Hapus Log Absensi?',
                    text: 'Data log absensi akan dihapus permanen.',
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