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

            {{-- FILTER DATA --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header d-flex align-items-center bg-transparent py-3">
                    <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                    <h5 class="mb-0 fw-semibold text-dark">Filter Data</h5>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="row align-items-end g-3">
                            {{-- Tahun --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-secondary small">Tahun Periode</label>
                                <select name="year" class="form-select">
                                    @for($y = date('Y'); $y >= date('Y')-3; $y--)
                                        <option value="{{ $y }}" @selected($selectedYear == $y)>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Bulan --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-secondary small">Bulan Periode</label>
                                <select name="month" class="form-select">
                                    @foreach([
                                        '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                                        '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                                        '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                                    ] as $num=>$name)
                                        <option value="{{ $num }}" @selected($selectedMonth == $num)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-primary small">Tanggal Start</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control border-primary-subtle">
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-primary small">Tanggal End</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control border-primary-subtle">
                            </div>

                            {{-- Employee --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Karyawan</label>
                                <select name="employee_id" class="form-select">
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>
                                            [{{ $employee->nik }}] {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('attendance-monthly.index') }}" class="btn btn-secondary px-3">Reset</a>
                                <button type="submit" class="btn btn-primary px-3 d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:filter-bold"></iconify-icon>
                                    Apply Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SUMMARY BLOCK: AKUMULASI METRIK (TAMPILAN BARU) --}}
            <div class="col-xl-12 mb-4">
                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Ringkasan Statistik Kehadiran (Total Makro)</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr class="bg-light text-muted small uppercase">
                                        <th width="35%" class="ps-3">Metrik / Status Kehadiran</th>
                                        <th width="65%">Akumulasi Data Periode Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Status Utama --}}
                                    <tr>
                                        <th class="fw-semibold text-secondary ps-3">Status Kehadiran Utama</th>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold">
                                                    Present: {{ number_format($cards['present']) }}
                                                </span>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 fw-semibold">
                                                    Sakit: {{ number_format($cards['sick'] ?? 0) }}
                                                </span>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1.5 fw-semibold">
                                                    Izin: {{ number_format($cards['izin']) }}
                                                </span>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 fw-semibold">
                                                    Alpha: {{ number_format($cards['alpha']) }}
                                                </span>
                                                <span class="badge bg-light text-dark border px-2 py-1.5 fw-semibold" style="background-color: #f3e5f5; color: #8e24aa !important; border-color: #d1c4e9 !important;">
                                                    Cuti: {{ number_format($cards['cuti']) }}
                                                </span>
                                                <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">
                                                    IDT: {{ number_format($cards['idt']) }}
                                                </span>
                                                <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">
                                                    IPC: {{ number_format($cards['ipc']) }}
                                                </span>
                                                @if(isset($cards['wfa']))
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 fw-semibold">
                                                    WFA: {{ number_format($cards['wfa']) }}
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Keterlambatan --}}
                                    <tr>
                                        <th class="fw-semibold text-secondary ps-3">Keterlambatan (Late)</th>
                                        <td>
                                            <span class="fw-bold text-warning">{{ number_format($cards['late']) }} Kali</span>
                                            @if($cards['total_late_minutes'] > 0)
                                                <span class="text-muted small ms-2">
                                                    (Total durasi: {{ $cards['late_hours'] }} Jam {{ $cards['late_minutes_remainder'] }} Menit)
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Pelanggaran Waktu Pulang --}}
                                    <tr>
                                        <th class="fw-semibold text-secondary ps-3">Pulang Cepat (Early Leave)</th>
                                        <td>
                                            <span class="fw-bold" style="color: #00796b;">{{ number_format($cards['early_leave']) }} Kali</span>
                                            @if($cards['total_early_leave_minutes'] > 0)
                                                <span class="text-muted small ms-2">
                                                    (Total durasi: {{ $cards['early_leave_hours'] }} Jam {{ $cards['early_leave_minutes_remainder'] }} Menit)
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    {{-- Kelalaian Absen --}}
                                    <tr>
                                        <th class="fw-semibold text-secondary ps-3">Kelalaian Log Absen</th>
                                        <td>
                                            <span class="text-danger fw-medium">
                                                Lupa Check-In:  <strong>{{ $cards['forgot_in'] }}</strong>
                                                <span class="text-muted mx-2">|</span>
                                                Lupa Check-Out: <strong>{{ $cards['forgot_out'] }}</strong>
                                                
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- Hari Kerja --}}
                                    <tr>
                                        <th class="fw-semibold text-secondary ps-3">Hari Kerja Resmi</th>
                                        <td class="text-dark">
                                            <strong>{{ $workingDays }}</strong> Hari Kerja
                                            <span class="text-muted small ms-2">
                                                  (Dari {{ $calendarDays ?? '0' }} hari kalender - {{ $sundayCount }} Off - {{ $holidayCount }} Libur)
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- Defisit/Kurang Hari & Jam --}}
                                    <tr class="table-danger-subtle" style="background-color: #fdf2f2;">
                                        <th class="fw-semibold text-danger ps-3">Total Defisit Jam/Hari Kerja</th>
                                        <td class="text-danger fw-bold">
                                            Kurang Hari Kerja: <span class="badge bg-danger px-2 py-1 ms-1">{{ $cards['kurang_hk'] }} Hari</span>
                                            <span class="text-muted mx-2">|</span>
                                            Kurang Durasi: 
                                            <span class="badge bg-danger px-2 py-1 ms-1">
                                                {{ floor($cards['kurang_jam']/60) }} Jam {{ $cards['kurang_jam']%60 }} Menit
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL DATA PER KARYAWAN --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">Rincian Performa Kehadiran Karyawan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Nama Karyawan / NIK</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Sakit</th>
                                    <th class="text-center">Izin</th>
                                    <th class="text-center">Alpha</th>
                                    <th class="text-center">Cuti</th>
                                    <th class="text-center">WFA</th>
                                    <th class="text-center">Late</th>
                                    <th class="text-center">IDT</th>
                                    <th class="text-center">Forgot In</th>
                                    <th class="text-center">Forgot Out</th>
                                    <th class="text-center">IPC</th>
                                    <th class="text-center" style="background-color: #fff5f5;">Kurang HK</th>
                                    <th class="text-center" style="background-color: #fff5f5;">Kurang Jam</th>
                                    <th class="text-center" width="60">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary as $item)
                                    <tr>
                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item['employee']->full_name }}</div>
                                            <div class="text-muted small">{{ $item['employee']->nik }}</div>
                                        </td>
                                        <td class="text-center fw-medium text-success">{{ $item['present'] }}</td>
                                        <td class="text-center">{{ $item['sick'] }}</td>
                                        <td class="text-center">{{ $item['permission'] }}</td>
                                        <td class="text-center @if($item['alpha'] > 0) text-danger fw-bold @endif">{{ $item['alpha'] }}</td>
                                        <td class="text-center">{{ $item['annual_leave'] }}</td>
                                        <td class="text-center text-muted">{{ $item['wfa'] ?: '-' }}</td>
                                        <td class="text-center">
                                            @if($item['late'] > 0)
                                                <span class="badge bg-warning-subtle text-warning fw-semibold">{{ $item['late'] }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">{{ $item['idt'] ?: '-' }}</td>
                                        <td class="text-center text-muted">{{ $item['forgot_in'] ?: '-' }}</td>
                                        <td class="text-center text-muted">{{ $item['forgot_out'] ?: '-' }}</td>
                                        <td class="text-center text-muted">{{ $item['ipc'] ?: '-' }}</td>
                                        <td class="text-center fw-medium @if($item['kurang_hk'] > 0) text-danger @endif" style="background-color: #fffdfd;">
                                            {{ $item['kurang_hk'] ?: '-' }}
                                        </td>
                                        <td class="text-center fw-medium @if($item['kurang_jam'] > 0) text-danger @endif" style="background-color: #fffdfd;">
                                            @php
                                                $jam = floor($item['kurang_jam'] / 60);
                                                $menit = $item['kurang_jam'] % 60;
                                            @endphp
                                            @if($item['kurang_jam'] == 0)
                                                <span class="text-muted">-</span>
                                            @else
                                                {{ $jam }}j {{ $menit }}m
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('attendance-monthly.show', [
                                                'employee' => $item['employee']->id,
                                                'start_date' => $startDate,
                                                'end_date' => $endDate
                                            ]) }}" class="btn btn-sm btn-light border p-1 d-inline-flex align-items-center" title="Lihat Detail">
                                                <iconify-icon icon="solar:eye-bold" class="text-primary fs-16"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center py-5 text-muted">
                                            <iconify-icon icon="solar:document-text-broken" class="fs-40 text-muted mb-2 d-block mx-auto"></iconify-icon>
                                            Tidak ada data log keharmonisan atau performa kerja pada periode ini.
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