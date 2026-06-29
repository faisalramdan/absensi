@extends('layouts.app')

@section('title', 'Attendance Monthly Summary')

@section('content')
@push('styles')

<style>

.attendance-table{

    max-height:75vh;

    overflow:auto;

}

.attendance-table table{

    white-space:nowrap;

}

.attendance-table thead th{

    position:sticky;

    top:0;

    z-index:30;

    background:#fff;

    box-shadow:0 2px 2px rgba(0,0,0,.05);

}

/* Freeze No */

.sticky-col-1{

    position:sticky;

    left:0;

    z-index:25;

    background:#fff;

    min-width:60px;

}

/* Freeze NIK */

.sticky-col-2{

    position:sticky;

    left:60px;

    z-index:25;

    background:#fff;

    min-width:120px;

}

/* Freeze Nama */

.sticky-col-3{

    position:sticky;

    left:180px;

    z-index:25;

    background:#fff;

    min-width:260px;

}

/* Header */

thead .sticky-col-1,
thead .sticky-col-2,
thead .sticky-col-3{

    z-index:40;

    background:#f8f9fa;

}

</style>

@endpush

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                {{-- FILTER --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">

                        <iconify-icon
                            icon="solar:filter-bold-duotone"
                            class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter Data
                        </h5>

                    </div>

                    <div class="card-body">

                        <form method="GET">

                            <div class="row align-items-end g-3">

                                {{-- Tahun --}}
                                <div class="col-md-2">

                                    <label class="form-label fw-semibold">
                                        Tahun Periode
                                    </label>

                                    <select
                                        name="year"
                                        class="form-select">

                                        @for($y = date('Y'); $y >= date('Y')-3; $y--)

                                            <option
                                                value="{{ $y }}"
                                                @selected($selectedYear==$y)>

                                                {{ $y }}

                                            </option>

                                        @endfor

                                    </select>

                                </div>

                                {{-- Bulan --}}
                                <div class="col-md-2">

                                    <label class="form-label fw-semibold">

                                        Bulan Periode

                                    </label>

                                    <select
                                        name="month"
                                        class="form-select">

                                        @foreach([
                                            '01'=>'Januari',
                                            '02'=>'Februari',
                                            '03'=>'Maret',
                                            '04'=>'April',
                                            '05'=>'Mei',
                                            '06'=>'Juni',
                                            '07'=>'Juli',
                                            '08'=>'Agustus',
                                            '09'=>'September',
                                            '10'=>'Oktober',
                                            '11'=>'November',
                                            '12'=>'Desember'
                                        ] as $num=>$name)

                                            <option
                                                value="{{ $num }}"
                                                @selected($selectedMonth==$num)>

                                                {{ $name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                {{-- Start --}}
                                <div class="col-md-2">

                                    <label class="form-label fw-semibold text-primary">

                                        Tanggal Start

                                    </label>

                                    <input
                                        type="date"
                                        name="start_date"
                                        value="{{ $startDate }}"
                                        class="form-control border-primary-subtle">

                                </div>

                                {{-- End --}}
                                <div class="col-md-2">

                                    <label class="form-label fw-semibold text-primary">

                                        Tanggal End

                                    </label>

                                    <input
                                        type="date"
                                        name="end_date"
                                        value="{{ $endDate }}"
                                        class="form-control border-primary-subtle">

                                </div>

                                {{-- Employee --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">

                                        Karyawan

                                    </label>

                                    <select
                                        name="employee_id"
                                        class="form-select">

                                        <option value="">

                                            -- Semua Karyawan --

                                        </option>

                                        @foreach($employees as $employee)

                                            <option
                                                value="{{ $employee->id }}"
                                                @selected(request('employee_id')==$employee->id)>

                                                [{{ $employee->nik }}]
                                                {{ $employee->full_name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                {{-- Button --}}
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">

                                    <a
                                        href="{{ route('attendance-monthly.index') }}"
                                        class="btn btn-secondary">

                                        Reset

                                    </a>

                                    <button
                                        class="btn btn-primary">

                                        <iconify-icon
                                            icon="solar:filter-bold"
                                            class="me-1">
                                        </iconify-icon>

                                        Apply Filter

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <div class="card border-0 shadow-sm">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h5 class="mb-0 fw-semibold">

                            Attendance Monthly Summary

                        </h5>

                        <span class="badge bg-primary">

                            {{ $summary->count() }} Karyawan

                        </span>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive attendance-table">

                            <table class="table table-bordered table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>No</th>

                                        <th>Nama Karyawan</th>

                                        <th class="text-center">Present</th>

                                        <th class="text-center">Sakit</th>

                                        <th class="text-center">Izin</th>

                                        <th class="text-center">Alpha</th>

                                        <th class="text-center">Cuti</th>

                                        <th class="text-center">WFA</th>

                                        <th class="text-center">Holiday</th>

                                        <th class="text-center">Off</th>

                                        <th class="text-center">Late</th>

                                        <th class="text-center">IDT</th>

                                        <th class="text-center">Forgot In</th>

                                        <th class="text-center">Forgot Out</th>

                                        <th class="text-center">IPC</th>

                                        <th class="text-center">Kurang HK</th>

                                        <th class="text-center">Kurang Jam</th>

                                        <th width="70">Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($summary as $item)

                                        <tr>

                                            <td>
                                                {{ $loop->iteration }}
                                            </td>

                                           

                                            <td class="fw-semibold">
                                                {{ $item['employee']->full_name }} <br>
                                                {{ $item['employee']->nik }}
                                            </td>

                                            <td class="text-center">{{ $item['present'] }}</td>

                                            <td class="text-center">{{ $item['sick'] }}</td>

                                            <td class="text-center">{{ $item['permission'] }}</td>

                                            <td class="text-center">{{ $item['alpha'] }}</td>

                                            <td class="text-center">{{ $item['annual_leave'] }}</td>

                                            <td class="text-center">{{ $item['wfa'] }}</td>

                                            <td class="text-center">{{ $item['holiday'] }}</td>

                                            <td class="text-center">{{ $item['off'] }}</td>

                                            <td class="text-center">

                                                @if($item['late'] > 0)

                                                    <span class="badge bg-warning-subtle text-warning">

                                                        {{ $item['late'] }}

                                                    </span>

                                                @else

                                                    -

                                                @endif

                                            </td>

                                            <td class="text-center">

                                                {{ $item['idt'] }}

                                            </td>

                                            <td class="text-center">

                                                {{ $item['forgot_in'] }}

                                            </td>

                                            <td class="text-center">

                                                {{ $item['forgot_out'] }}

                                            </td>

                                            <td class="text-center">

                                                {{ $item['ipc'] }}

                                            </td>

                                            <td class="text-center">

                                                {{ $item['kurang_hk'] }}

                                            </td>

                                            <td>

                                                @php

                                                    $jam = floor($item['kurang_jam'] / 60);

                                                    $menit = $item['kurang_jam'] % 60;

                                                @endphp

                                                @if($item['kurang_jam'] == 0)

                                                    -

                                                @else

                                                    {{ $jam }}j {{ $menit }}m

                                                @endif

                                            </td>

                                            <td>

                                                <a href="{{ route('attendance-monthly.show', $item['employee']->id) }}"
                                                    class="btn btn-soft-primary btn-icon btn-sm">

                                                    <iconify-icon icon="solar:eye-bold">
                                                    </iconify-icon>

                                                </a>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="19" class="text-center py-5 text-muted">

                                                Tidak ada data.

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection