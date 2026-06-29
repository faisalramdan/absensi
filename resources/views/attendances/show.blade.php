@extends('layouts.app')

@section('title', 'Detail Attendance')

@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h3 class="fw-bold mb-1">
                            {{ $attendance->employee?->full_name }}
                        </h3>

                        <p class="text-muted mb-0">
                            Detail hasil proses attendance harian karyawan
                        </p>

                    </div>

                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">

                        <iconify-icon icon="solar:arrow-left-bold" class="me-1">
                        </iconify-icon>

                        Kembali

                    </a>

                </div>

                <div class="row">

                    <div class="col-xl-8">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Informasi Attendance

                                </h4>

                            </div>

                            <div class="card-body">

                                <table class="table table-bordered align-middle">

                                    <tr>

                                        <th width="35%">Tanggal</th>

                                        <td>

                                            {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>NIK</th>

                                        <td>

                                            {{ $attendance->employee?->nik }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Nama Karyawan</th>

                                        <td>

                                            {{ $attendance->employee?->full_name }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Shift</th>

                                        <td>

                                            {{ $attendance->shift?->name ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Jadwal Masuk</th>

                                        <td>

                                            {{ $attendance->scheduled_check_in ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Jadwal Pulang</th>

                                        <td>

                                            {{ $attendance->scheduled_check_out ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Check In</th>

                                        <td>

                                            {{ $attendance->actual_check_in ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Check Out</th>

                                        <td>

                                            {{ $attendance->actual_check_out ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Terlambat</th>

                                        <td>

                                            {{ $attendance->late_minutes }} Menit

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Pulang Cepat</th>

                                        <td>

                                            {{ $attendance->early_leave_minutes }} Menit

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Total Jam Kerja</th>

                                        <td>

                                            {{ $attendance->work_minutes }} Menit

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Kurang Jam Kerja</th>

                                        <td>

                                            {{ $attendance->short_work_minutes }} Menit

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Status</th>

                                        <td>

                                            @php

                                                $color = match ($attendance->status) {

                                                    'present' => 'success',

                                                    'leave' => 'warning',

                                                    'holiday' => 'info',

                                                    'off' => 'secondary',

                                                    default => 'danger'

                                                };

                                            @endphp

                                            <span class="badge bg-{{ $color }}">

                                                {{ strtoupper($attendance->status) }}

                                            </span>

                                        </td>

                                    </tr>
                                    <tr>

                                        <th>Source</th>

                                        <td>

                            
                                             @switch($attendance->source)

                                                    @case('import_excel')
                                                        <span class="badge bg-success-subtle text-success">
                                                            Import Excel
                                                        </span>
                                                    @break

                                                    @case('manual')
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            Manual
                                                        </span>
                                                    @break

                                                    @case('generated')
                                                        <span class="badge bg-secondary-subtle text-secondary">
                                                            Generated
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">
                                                            -
                                                        </span>

                                                @endswitch

                                        </td>

                                    </tr>
                                    <tr>

                                        <th>Notes</th>

                                        <td>

                                            {{ $attendance->notes ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Lupa Check In</th>

                                        <td>

                                            @if($attendance->forgot_check_in)

                                                <span class="badge bg-danger">

                                                    Ya

                                                </span>

                                            @else

                                                -

                                            @endif

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Lupa Check Out</th>

                                        <td>

                                            @if($attendance->forgot_check_out)

                                                <span class="badge bg-danger">

                                                    Ya

                                                </span>

                                            @else

                                                -

                                            @endif

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>IDT</th>

                                        <td>

                                            {{ $attendance->is_idt ? 'Ya' : '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>IPC</th>

                                        <td>

                                            {{ $attendance->is_ipc ? 'Ya' : '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Jenis Cuti</th>

                                        <td>

                                            {{ $attendance->leaveType?->name ?? '-' }}

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Keterangan</th>

                                        <td>

                                            {{ $attendance->remarks ?? '-' }}

                                        </td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-4">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Audit Informasi

                                </h4>

                            </div>

                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item px-0 pt-0">

                                        <strong class="text-muted d-block mb-1 fs-12">

                                            Diproses Pada

                                        </strong>

                                        <span class="fw-semibold text-dark">

                                            {{ optional($attendance->processed_at)->format('d M Y H:i') ?? '-' }}

                                        </span>

                                    </li>

                                    <li class="list-group-item px-0">

                                        <strong class="text-muted d-block mb-1 fs-12">

                                            Dibuat Pada

                                        </strong>

                                        <span class="fw-semibold text-dark">

                                            {{ $attendance->created_at->format('d M Y H:i') }}

                                        </span>

                                    </li>

                                    <li class="list-group-item px-0">

                                        <strong class="text-muted d-block mb-1 fs-12">

                                            Terakhir Diubah

                                        </strong>

                                        <span class="fw-semibold text-dark">

                                            {{ $attendance->updated_at->diffForHumans() }}

                                        </span>

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