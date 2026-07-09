@extends('layouts.app')

@section('title', 'Attendance Monthly Summary')

@section('content')
@push('styles')
<style>
.attendance-table {
    max-height: 75vh;
    overflow: auto;
}
.attendance-table table {
    white-space: nowrap;
}
.attendance-table thead th {
    position: sticky;
    top: 0;
    z-index: 30;
    background: #fff;
    box-shadow: 0 2px 2px rgba(0,0,0,.05);
}
/* Freeze No */
.sticky-col-1 {
    position: sticky;
    left: 0;
    z-index: 25;
    background: #fff;
    min-width: 60px;
}
/* Freeze NIK/Nama Group */
.sticky-col-2 {
    position: sticky;
    left: 60px;
    z-index: 25;
    background: #fff;
    min-width: 240px;
}
/* Header Freeze */
thead .sticky-col-1,
thead .sticky-col-2 {
    z-index: 40;
    background: #f8f9fa !important;
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

            {{-- SUMMARY BLOCK: AKUMULASI METRIK GLOBAL --}}
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
                                                    Present: {{ number_format($cards['present'] ?? 0) }}
                                                </span>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 fw-semibold">
                                                    WFA: {{ number_format($cards['wfa'] ?? 0) }}
                                                </span>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 fw-semibold">
                                                    Sakit: {{ number_format($cards['sick'] ?? 0) }}
                                                </span>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1.5 fw-semibold">
                                                    Izin: {{ number_format($cards['izin'] ?? 0) }}
                                                </span>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 fw-semibold">
                                                    Alpha: {{ number_format($cards['alpha'] ?? 0) }}
                                                </span>
                                                <span class="badge bg-light text-dark border px-2 py-1.5 fw-semibold" style="background-color: #f3e5f5; color: #8e24aa !important; border-color: #d1c4e9 !important;">
                                                    Cuti: {{ number_format($cards['cuti'] ?? 0) }}
                                                </span>
                                                <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">
                                                    IDT: {{ number_format($cards['idt'] ?? 0) }}
                                                </span>
                                                <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">
                                                    IPC: {{ number_format($cards['ipc'] ?? 0) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Keterlambatan --}}
                                    <tr>
                                        <th class="fw-semibold text-secondary ps-3">Keterlambatan (Late)</th>
                                        <td>
                                            <span class="fw-bold text-warning">{{ number_format($cards['late'] ?? 0) }} Kali</span>
                                            @if(($cards['total_late_minutes'] ?? 0) > 0)
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
                                            <span class="fw-bold" style="color: #00796b;">{{ number_format($cards['early_leave'] ?? 0) }} Kali</span>
                                            @if(($cards['total_early_leave_minutes'] ?? 0) > 0)
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
                                                Lupa Check-In: <strong>{{ $cards['forgot_in'] ?? 0 }}</strong>
                                                <span class="text-muted mx-2">|</span>
                                                Lupa Check-Out: <strong>{{ $cards['forgot_out'] ?? 0 }}</strong>
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
                                    {{-- Bagian baris tabel ringkasan statis individu --}}
                                    {{-- Akumulasi Kurang Jam Kerja Per Karyawan di Halaman Index --}}
                                    <tr class="table-light">
                                        <th class="fw-medium text-secondary">Kurang dari jam kerja</th>
                                        <td class="fw-bold text-dark">
                                            {{ $cards['short_work_count'] ?? 0 }}x
                                        </td>
                                    </tr>
                                    <tr class="table-danger">
                                        <th class="fw-bold text-danger">Total kurang jam kerja</th>
                                        <td class="fs-5 fw-bold text-danger">
                                            @if(isset($cards['kurang_jam']) && $cards['kurang_jam'] > 0)
                                                {{ floor($cards['kurang_jam'] / 60) }} <small class="fs-6 fw-normal">Jam</small>
                                                {{ $cards['kurang_jam'] % 60 }} <small class="fs-6 fw-normal">Menit</small>
                                            @else
                                                0 <small class="fs-6 fw-normal">Jam</small> 0 <small class="fs-6 fw-normal">Menit</small>
                                            @endif
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
                    <div class="attendance-table">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="text-center sticky-col-1" width="50">No</th>
                                    <th class="sticky-col-2">Nama Karyawan / NIK</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center" style="background-color: #f0f7ff;">WFA</th>
                                    <th class="text-center">Sakit</th>
                                    <th class="text-center">Izin</th>
                                    <th class="text-center">Alpha</th>
                                    <th class="text-center">Cuti</th>
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
                                        <td class="text-center text-muted sticky-col-1">{{ $loop->iteration }}</td>
                                        <td class="sticky-col-2">
                                            <div class="fw-semibold text-dark">{{ $item['employee']->full_name }}</div>
                                            <div class="text-muted small">{{ $item['employee']->nik }}</div>
                                        </td>
                                        <td class="text-center fw-medium text-success">{{ $item['present'] }}</td>
                                        {{-- Data WFA Karyawan --}}
                                        <td class="text-center fw-medium text-primary" style="background-color: #fafdff;">
                                            {{ $item['wfa'] ?: '-' }}
                                        </td>
                                        <td class="text-center">{{ $item['sick'] }}</td>
                                        <td class="text-center">{{ $item['permission'] }}</td>
                                        <td class="text-center @if($item['alpha'] > 0) text-danger fw-bold @endif">{{ $item['alpha'] }}</td>
                                        <td class="text-center">{{ $item['annual_leave'] }}</td>
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
                                                <div class="text-muted" style="font-size: 10px; font-weight: normal;">({{ $item['short_work_count'] ?? 0 }}x)</div>
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

{{-- JAVASCRIPT AUTOMATION CUT-OFF --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthSelect = document.querySelector('select[name="month"]');
    const yearSelect = document.querySelector('select[name="year"]');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');

    function updateCutoffDates() {
        const selectedMonth = parseInt(monthSelect.value);
        const selectedYear = parseInt(yearSelect.value);

        if (!selectedMonth || !selectedYear) return;

        const endMonthStr = String(selectedMonth).padStart(2, '0');
        const endDateStr = `${selectedYear}-${endMonthStr}-25`;

        let startMonth = selectedMonth - 1;
        let startYear = selectedYear;

        if (startMonth === 0) {
            startMonth = 12;
            startYear = selectedYear - 1;
        }

        const startMonthStr = String(startMonth).padStart(2, '0');
        const startDateStr = `${startYear}-${startMonthStr}-26`;

        startDateInput.value = startDateStr;
        endDateInput.value = endDateStr;
    }

    monthSelect.addEventListener('change', updateCutoffDates);
    yearSelect.addEventListener('change', updateCutoffDates);
});
</script>
@endpush
@endsection