@extends('layouts.app')
@section('title', 'Detail Rekap Absensi Bulanan')
@section('content')

<div class="wrapper">
    <div class="page-content">
        <div class="container-xxl">

            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">{{ $employee->full_name }}</h3>
                    <p class="text-muted mb-0">
                        Rekap Absensi Periode 
                        <strong>
                            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                        </strong>
                    </p>
                </div>
                <a href="{{ route('attendance-monthly.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold" class="me-1"></iconify-icon>
                    Kembali
                </a>
            </div>

            <!-- Profile & Summary Cards -->
            <div class="row">
                <!-- Employee Information -->
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Informasi Karyawan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered align-middle">
                                <tr>
                                    <th width="30%">NIK</th>
                                    <td>{{ $employee->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Departemen</th>
                                    <td>{{ $employee->department?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td>{{ $employee->position?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Periode</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary Attendance -->
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Ringkasan</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Present</span>
                                    <strong>{{ $summary['present'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Late</span>
                                    <strong>{{ $summary['late'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Alpha</span>
                                    <strong>{{ $summary['alpha'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Cuti</span>
                                    <strong>{{ $summary['cuti'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Izin</span>
                                    <strong>{{ $summary['izin'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Forgot Check In</span>
                                    <strong>{{ $summary['forgot_in'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Forgot Check Out</span>
                                    <strong>{{ $summary['forgot_out'] }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total Jam Kerja</span>
                                    <strong>
                                        {{ floor($summary['work_minutes'] / 60) }} Jam {{ $summary['work_minutes'] % 60 }} Menit
                                    </strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE ATTENDANCE --}}
            <div class="card">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Attendance</h4>
                        <p class="text-muted mb-0">
                            {{ $attendances->count() }} data yang ditemukan di sistem Anda.
                        </p>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    
                                    <th>Shift</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Telat</th>
                                    <th>Pulang Cepat</th>
                                    <th>Jam Kerja</th>
                                    <th>Status</th>
                                    <th class="text-center">Source</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>
                                            {{-- Menampilkan Nama Hari --}}
                                            <span class="text-muted small d-block mb-1">
                                                {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}
                                            </span>
                                            {{-- Menampilkan Tanggal --}}
                                            <span class="fw-semibold text-dark">
                                                {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            @php
                                                $shiftName = $attendance->shift?->name ?? '-';
                                                $badgeClass = 'bg-light text-muted';

                                                if (str_contains(strtolower($shiftName), '1')) {
                                                    $badgeClass = 'bg-primary text-white';
                                                } elseif (str_contains(strtolower($shiftName), '2')) {
                                                    $badgeClass = 'bg-success text-white';
                                                } elseif (str_contains(strtolower($shiftName), '3')) {
                                                    $badgeClass = 'bg-purple text-white'; 
                                                } elseif ($shiftName != '-') {
                                                    $badgeClass = 'bg-danger text-white';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1 fw-medium">
                                                {{ $shiftName }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium">
                                                {{ $attendance->actual_check_in ? date('H:i', strtotime($attendance->actual_check_in)) : '--:--' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-medium">
                                                {{ $attendance->actual_check_out ? date('H:i', strtotime($attendance->actual_check_out)) : '--:--' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{ $attendance->late_minutes > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                                {{ $attendance->late_minutes ?? 0 }} mnt
                                            </span>
                                            @if($attendance->is_idt === true || $attendance->is_idt === 't' || $attendance->is_idt == 1)
                                                <div class="mt-1">
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-medium">
                                                        I-IDT
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $attendance->early_leave_minutes > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">
                                                {{ $attendance->early_leave_minutes ?? 0 }} mnt
                                            </span>
                                            @if($attendance->is_ipc === true || $attendance->is_ipc === 't' || $attendance->is_ipc == 1)
                                                <div class="mt-1">
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-medium">
                                                        I-IPC
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">
                                                @if($attendance->work_minutes)
                                                    {{ floor($attendance->work_minutes / 60) }} Jam {{ $attendance->work_minutes % 60 }} Menit
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusStyles = [
                                                    'present' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'alpha'   => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                    'leave'   => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                    'holiday' => 'bg-info-subtle text-info border border-info-subtle',
                                                    'off'     => 'bg-light text-secondary border',
                                                ];
                                                $currentStatus = strtolower($attendance->status);
                                                $class = $statusStyles[$currentStatus] ?? 'bg-light text-dark';
                                            @endphp

                                            <span class="badge {{ $class }} px-2 py-1 fw-semibold">
                                                {{ strtoupper($attendance->status ?? 'UNKNOWN') }}
                                            </span>

                                            @if($currentStatus === 'leave' && $attendance->leaveType)
                                                <div class="small text-muted fw-bold mt-1" style="font-size: 0.75rem;">
                                                    ({{ $attendance->leaveType->code }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @switch($attendance->source)
                                                @case('import_excel')
                                                    <iconify-icon icon="solar:file-download-bold-duotone" class="text-success fs-22" data-bs-toggle="tooltip" title="Import Excel"></iconify-icon>
                                                    @break
                                                @case('manual')
                                                    <iconify-icon icon="solar:pen-bold-duotone" class="text-warning fs-22" data-bs-toggle="tooltip" title="Input Manual"></iconify-icon>
                                                    @break
                                                @case('generated')
                                                    <iconify-icon icon="solar:cpu-bolt-bold-duotone" class="text-primary fs-22" data-bs-toggle="tooltip" title="Generated System"></iconify-icon>
                                                    @break
                                                @default
                                                    <iconify-icon icon="solar:question-circle-bold-duotone" class="text-muted fs-22" data-bs-toggle="tooltip" title="Unknown"></iconify-icon>
                                            @endswitch
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Colspan disesuaikan menjadi 11 kolom agar sejajar penuh --}}
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            Tidak ada data absensi ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- FOOTER PAGINATION --}}
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            Menampilkan {{ $attendances->count() }} dari  data
                        </span>
                        
                    </div>
                </div>
            </div>
    </div>
</div>

@endsection