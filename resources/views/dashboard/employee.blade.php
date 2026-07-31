@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                {{-- Welcome --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 shadow-sm">
                            <div class="card-body bg-primary">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h3 class="text-white mb-2">
                                            Selamat Datang, {{ $employee->full_name }} 👋
                                        </h3>

                                        <p class="text-white opacity-75 mb-4">
                                            Berikut ringkasan informasi Anda.
                                        </p>

                                        <div class="row text-white">
                                            <div class="col-md-3">
                                                <small class="opacity-75">NIK</small>
                                                <div class="fw-bold">{{ $employee->nik }}</div>
                                            </div>

                                            <div class="col-md-3">
                                                <small class="opacity-75">Jabatan</small>
                                                <div class="fw-bold">{{ $employee->position?->name ?? '-' }}</div>
                                            </div>

                                            <div class="col-md-3">
                                                <small class="opacity-75 d-block mb-1">Masa Kontrak Anda</small>
                                                <div class="fw-bold">
                                                    @if($activeContract)
                                                        {{ \Carbon\Carbon::parse($activeContract->start_date)->translatedFormat('d F Y') }}
                                                        <span class="fw-bold">s/d</span>
                                                        {{ \Carbon\Carbon::parse($activeContract->end_date)->translatedFormat('d F Y') }}
                                                    @else
                                                        <span class="fw-bold">Tidak ada kontrak aktif</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-end d-none d-lg-block">
                                        @if($employee->photo)
                                            <img src="{{ asset('storage/' . $employee->photo) }}" width="200" height="200"
                                                class="img-fluid rounded object-fit-cover" alt="{{ $employee->full_name }}">
                                        @else
                                            <img src="{{ asset('assets/images/users/dummy-avatar.jpg') }}" width="150"
                                                height="150" class="rounded-circle object-fit-cover" alt="">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================================================= --}}
                {{-- 🌟 GABUNGAN: FILTER, RINGKASAN & RIWAYAT ABSENSI 🌟 --}}
                {{-- ======================================================= --}}
                <div class="card border-0 shadow-sm mb-4">

                    {{-- 1. Bagian Header & Filter --}}
                    <div class="card-header bg-white py-3 border-bottom">
                        <form method="GET" action="{{ request()->url() }}" id="filterForm">
                            <div class="row align-items-end g-3">
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Tahun Periode</label>
                                    <select name="year" id="filterYear" class="form-select">
                                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                                            <option value="{{ $y }}" @selected($selectedYear == $y)>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Bulan Periode</label>
                                    <select name="month" id="filterMonth" class="form-select">
                                        @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                            <option value="{{ $num }}" @selected($selectedMonth == $num)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-primary">Tanggal Start</label>
                                    <input type="date" name="start_date" id="startDate" value="{{ $startDate }}"
                                        class="form-control border-primary-subtle">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-primary">Tanggal End</label>
                                    <input type="date" name="end_date" id="endDate" value="{{ $endDate }}"
                                        class="form-control border-primary-subtle">
                                </div>
                                <div class="col-md-4 d-flex gap-2 mt-3">
                                    <a href="{{ request()->url() }}" class="btn btn-secondary px-4">Reset</a>
                                    <button type="submit" class="btn btn-primary px-4">Apply Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- 2. Bagian Ringkasan Kehadiran (Background sedikit dibedakan) --}}
                    {{-- 2. Bagian Ringkasan Kehadiran (TAMPILAN FULL LENGKAP) --}}
                    <div class="card-body bg-light border-bottom">
                        <h6 class="fw-bold mb-3 text-secondary text-uppercase">
                            <iconify-icon icon="solar:pie-chart-2-bold-duotone"
                                class="align-middle me-1 fs-18"></iconify-icon>
                            Ringkasan Kehadiran
                        </h6>

                        <div class="row g-3">
                            {{-- Box 1: Kehadiran Utama --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="p-3 bg-white border rounded shadow-sm h-100">
                                    <span class="d-block text-muted mb-3 fw-bold"
                                        style="font-size: 11px; letter-spacing: 0.5px;">KEHADIRAN</span>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Hadir (Present)</span>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2">{{ $summary['present'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Work From Anywhere</span>
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">{{ $summary['wfa'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Izin Datang Telat (IDT)</span>
                                        <span class="badge px-2 border"
                                            style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">{{ $summary['idt'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Izin Pulang Cepat (IPC)</span>
                                        <span class="badge px-2 border"
                                            style="background-color: #e0f2f1; color: #00796b; border-color: #b2dfdb !important;">{{ $summary['ipc'] ?? 0 }}</span>
                                    </div>

                                </div>
                            </div>

                            {{-- Box 2: Cuti & Ketidakhadiran --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="p-3 bg-white border rounded shadow-sm h-100">
                                    <span class="d-block text-muted mb-3 fw-bold"
                                        style="font-size: 11px; letter-spacing: 0.5px;">CUTI & KETIDAKHADIRAN</span>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Sakit</span>
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">{{ $summary['sakit'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Izin</span>
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">{{ $summary['ijin'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Cuti</span>
                                        <span class="badge px-2 border"
                                            style="background-color: #f3e5f5; color: #8e24aa; border-color: #d1c4e9 !important;">{{ $summary['cuti'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark fs-14">Alpha</span>
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">{{ $summary['alpha'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Box 3: Pelanggaran Waktu --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="p-3 bg-white border rounded shadow-sm h-100">

                                    <div class="mb-3">
                                        <span class="text-muted fw-bold text-uppercase"
                                            style="font-size:11px; letter-spacing:.5px;">
                                            Pelanggaran Waktu
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Terlambat</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">
                                            {{ $summary['late'] ?? 0 }}x
                                            ({{ $summary['late_hours'] ?? 0 }}j
                                            {{ $summary['late_minutes_remainder'] ?? 0 }}m)
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Pulang Cepat</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">
                                            {{ $summary['early_leave'] ?? 0 }}x
                                            ({{ $summary['early_leave_hours'] ?? 0 }}j
                                            {{ $summary['early_leave_minutes_remainder'] ?? 0 }}m)
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Lupa Absen Masuk</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">
                                            {{ $summary['forgot_check_in'] ?? 0 }}x

                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark fs-14">Lupa Absen Pulang</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">
                                            {{ $summary['forgot_check_out'] ?? 0 }}x

                                        </span>
                                    </div>

                                </div>
                            </div>

                            {{-- Box 4: Izin Khusus & Total Waktu --}}
                            @php
                                $totalMinutes = $summary['total_short_work_minutes'] ?? 0;
                                $hours = floor($totalMinutes / 60);
                                $minutes = $totalMinutes % 60;
                            @endphp

                            <div class="col-xl-3 col-md-6">
                                <div class="p-3 bg-white border rounded shadow-sm h-100">

                                    <div class="mb-3">
                                        <span class="text-muted fw-bold text-uppercase"
                                            style="font-size:11px; letter-spacing:.5px;">
                                            Lainnya
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Kurang Dari Jam Kerja</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">
                                            {{ $summary['short_work_count'] ?? 0 }}x
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Total Kurang Jam Kerja</span>
                                        <span class="fw-semibold text-danger">
                                            {{ $hours }}j {{ $minutes }}m
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Hari Kerja Resmi</span>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2">
                                            {{ $workingDays ?? 0 }} Hari
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-dark fs-14">Libur Nasional</span>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2">
                                            {{ $summary['holiday'] ?? 0 }}
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark fs-14">Hari Libur (Off/Minggu)</span>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2">
                                            {{ $summary['off'] ?? 0 }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Bagian Log Tabel Riwayat --}}
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <iconify-icon icon="solar:history-bold-duotone"
                                class="align-middle me-1 text-primary fs-20"></iconify-icon>
                            Detail Riwayat Absensi
                        </h5>
                        <span class="badge bg-primary-subtle text-primary">{{ $attendances->total() }} Data</span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Tanggal</th>
                                        <th>Shift</th>
                                        <th>Masuk</th>
                                        <th>Pulang</th>
                                        <th>Terlambat</th>
                                        <th>Pulang Cepat</th>
                                        <th>Jam Kerja</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                        <tr>
                                            <td class="ps-4">
                                                <span
                                                    class="text-muted small d-block mb-1">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</span>
                                                <span
                                                    class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $shiftName = $attendance->shift?->name ?? '-';
                                                    $badgeClass = 'bg-light text-muted';
                                                    if (str_contains(strtolower($shiftName), 'siang'))
                                                        $badgeClass = 'bg-primary text-white';
                                                    elseif (str_contains(strtolower($shiftName), 'pagi'))
                                                        $badgeClass = 'bg-success text-white';
                                                    elseif (str_contains(strtolower($shiftName), 'malam'))
                                                        $badgeClass = 'bg-purple text-white';
                                                    elseif ($shiftName != '-')
                                                        $badgeClass = 'bg-danger text-white';
                                                @endphp
                                                <span
                                                    class="badge {{ $badgeClass }} px-2 py-1 fw-medium">{{ $shiftName }}</span>
                                            </td>
                                            <td><span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium">{{ $attendance->actual_check_in ? date('H:i', strtotime($attendance->actual_check_in)) : '--:--' }}</span>
                                            </td>
                                            <td><span
                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-medium">{{ $attendance->actual_check_out ? date('H:i', strtotime($attendance->actual_check_out)) : '--:--' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="{{ $attendance->late_minutes > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $attendance->late_minutes ?? 0 }}
                                                    mnt</span>
                                                @if($attendance->is_idt === true || $attendance->is_idt === 't' || $attendance->is_idt == 1)
                                                    <div class="mt-1">
                                                        <span
                                                            class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-medium">
                                                            I-IDT
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

                                            <td><span
                                                    class="{{ $attendance->early_leave_minutes > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">{{ $attendance->early_leave_minutes ?? 0 }}
                                                    mnt</span>
                                                @if($attendance->is_ipc === true || $attendance->is_ipc === 't' || $attendance->is_ipc == 1)
                                                    <div class="mt-1">
                                                        <span
                                                            class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-medium">
                                                            I-IPC
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium text-dark">
                                                        @if($attendance->work_minutes)
                                                            {{ floor($attendance->work_minutes / 60) }}
                                                            Jam
                                                            {{ $attendance->work_minutes % 60 }} Menit
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                    @if($attendance->status !== 'wfa' && $attendance->status !== 'holiday' && $attendance->status !== 'off')
                                                        @if($attendance->short_work_minutes && $attendance->short_work_minutes > 0)
                                                            <small class="text-danger fw-bold mt-1">
                                                                (- {{ floor($attendance->short_work_minutes / 60) }} Jam
                                                                {{ $attendance->short_work_minutes % 60 }} Menit)
                                                            </small>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusStyles = ['present' => 'bg-success-subtle text-success border-success-subtle', 'alpha' => 'bg-danger-subtle text-danger border-danger-subtle', 'leave' => 'bg-warning-subtle text-warning border-warning-subtle', 'holiday' => 'bg-info-subtle text-info border-info-subtle', 'off' => 'bg-light text-secondary border'];
                                                    $currentStatus = strtolower($attendance->status);
                                                    $class = $statusStyles[$currentStatus] ?? 'bg-light text-dark';
                                                @endphp
                                                <span
                                                    class="badge {{ $class }} border px-2 py-1 fw-semibold">{{ strtoupper($attendance->status) }}</span>

                                                @if($currentStatus === 'leave' && $attendance->leaveType)
                                                    <div class="small text-muted fw-bold mt-1" style="font-size: 0.75rem;">
                                                        ({{ $attendance->leaveType->code }})
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat absensi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 4. Footer Pagination --}}
                    @if($attendances->hasPages())
                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Hal. {{ $attendances->currentPage() }} dari
                                    {{ $attendances->lastPage() }}</span>
                                {{ $attendances->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 🌟 FITUR BARU: Tabel Jadwal Kerja Saya (Statis & Read-Only) 🌟 --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h4 class="card-title mb-1 fw-bold text-dark">
                            <iconify-icon icon="solar:calendar-date-bold-duotone"
                                class="align-middle me-1 text-primary fs-20"></iconify-icon>
                            Jadwal Kerja Saya
                        </h4>

                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="mt-4 mt-lg-0">
                                    <div id="user-calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ======================================================= --}}
                {{-- 🌟 GABUNGAN: STATUS & RINCIAN KUOTA CUTI 🌟 --}}
                {{-- ======================================================= --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h4 class="card-title mb-0 fw-bold text-dark">
                            <iconify-icon icon="solar:clipboard-list-bold-duotone"
                                class="align-middle me-1 text-primary fs-20"></iconify-icon>
                            Status Pengajuan & Kuota Cuti
                        </h4>
                    </div>

                    <div class="card-body">
                        {{-- Widget Kotak Status --}}
                        <div class="row mb-4">
                            @php
                                $leaveCards = [
                                    ['Menunggu Approval', $pendingLeaves, 'warning', 'solar:clock-circle-bold-duotone'],
                                    ['Disetujui', $approvedLeaves, 'success', 'solar:check-circle-bold-duotone'],
                                    ['Ditolak', $rejectedLeaves, 'danger', 'solar:close-circle-bold-duotone'],
                                ];
                            @endphp
                            @foreach($leaveCards as $card)
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div
                                        class="border border-{{ $card[2] }}-subtle rounded p-3 bg-{{ $card[2] }}-subtle d-flex justify-content-between align-items-center h-100">
                                        <div>
                                            <p class="text-{{ $card[2] }} mb-1 fw-semibold">{{ $card[0] }}</p>
                                            <h3 class="mb-0 fw-bold text-dark">{{ $card[1] }} <small
                                                    class="fs-6 fw-normal text-muted">Pengajuan</small></h3>
                                        </div>
                                        <div class="avatar-md bg-white rounded shadow-sm d-flex justify-content-center align-items-center"
                                            style="width: 50px; height: 50px;">
                                            <iconify-icon icon="{{ $card[3] }}"
                                                class="fs-32 text-{{ $card[2] }}"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tabel Sisa Kuota --}}
                        <h6 class="fw-bold mb-3 text-secondary uppercase">Rincian Kuota Tersedia</h6>
                        <div class="table-responsive border rounded">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 60px;">No</th>
                                        <th>Jenis Cuti/Izin</th>
                                        <th class="text-center">Kuota (Hari)</th>
                                        <th class="text-center">Terpakai (Hari)</th>
                                        <th class="text-center">Sisa (Hari)</th>
                                        <th class="pe-4" style="width: 250px;">Persentase Pemakaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leaveAllocations as $index => $allocation)
                                        @php
                                            $quota = $allocation->allocated_days;
                                            $used = $allocation->used_days;
                                            $remaining = $allocation->remaining_days;
                                            $percentage = $quota > 0 ? min(100, round(($used / $quota) * 100)) : 0;
                                            $barColor = $percentage >= 80 ? 'bg-danger' : ($percentage >= 50 ? 'bg-warning' : 'bg-primary');
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-medium text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $allocation->leaveType?->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="text-center fw-semibold text-secondary">{{ floatval($quota) }}</td>
                                            <td class="text-center fw-semibold text-danger">{{ floatval($used) }}</td>
                                            <td class="text-center fw-bold text-success fs-15">{{ floatval($remaining) }}</td>
                                            <td class="pe-4">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <small class="text-muted fs-11">Terpakai</small>
                                                    <small class="fw-bold text-dark fs-11">{{ $percentage }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $barColor }}" style="width: {{ $percentage }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">Tidak ada data alokasi cuti
                                                aktif.</td>
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ==========================================
                // 1. LOGIKA INTERAKTIF DROPDOWN FILTER DATA
                // ==========================================
                const filterYear = document.getElementById('filterYear');
                const filterMonth = document.getElementById('filterMonth');
                const startDateInput = document.getElementById('startDate');
                const endDateInput = document.getElementById('endDate');

                if (filterYear && filterMonth && startDateInput && endDateInput) {
                    function updateDateRange() {
                        const year = parseInt(filterYear.value);
                        const month = parseInt(filterMonth.value);

                        if (!year || !month) return;

                        // Buat tanggal target: tanggal 1 dari bulan & tahun terpilih
                        let targetDate = new Date(year, month - 1, 1);

                        // Cari tanggal 26 dari 1 bulan sebelumnya
                        let startPeriod = new Date(targetDate.getFullYear(), targetDate.getMonth() - 1, 26);

                        // Cari tanggal 25 di bulan terpilih
                        let endPeriod = new Date(targetDate.getFullYear(), targetDate.getMonth(), 25);

                        // Helper memformat date objek ke string YYYY-MM-DD lokal
                        const formatDate = (date) => {
                            const d = new Date(date);
                            let m = '' + (d.getMonth() + 1);
                            let day = '' + d.getDate();
                            const y = d.getFullYear();

                            if (m.length < 2) m = '0' + m;
                            if (day.length < 2) day = '0' + day;

                            return [y, m, day].join('-');
                        };

                        startDateInput.value = formatDate(startPeriod);
                        endDateInput.value = formatDate(endPeriod);
                    }

                    // Trigger pembaruan otomatis saat dropdown berubah
                    filterYear.addEventListener('change', updateDateRange);
                    filterMonth.addEventListener('change', updateDateRange);
                }

                // ==========================================
                // 2. LOGIKA INITIALISASI FULLCALENDAR
                // ==========================================
                var calendarEl = document.getElementById('user-calendar');

                if (calendarEl) {
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        themeSystem: 'bootstrap', // FIX: Typo dari tthemeSystem sebelumnya
                        initialView: 'dayGridMonth',
                        firstDay: 1,

                        buttonText: {
                            today: 'Today',
                            prev: 'Prev', // Memaksa menampilkan teks 'Prev' daripada ikon panah
                            next: 'Next'  // Memaksa menampilkan teks 'Next' daripada ikon panah
                        },

                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: ''
                        },

                        locale: 'id', // Memastikan bahasa Indonesia aktif
                        height: 650,

                        // Jalur mengambil data event dari Route API Laravel
                        events: "{{ route('employee.schedule.events') }}",

                        editable: false,   // Read-only (Mencegah drag-and-drop)
                        selectable: false, // Read-only (Mencegah blok tanggal)

                        // Mengatur style tampilan kotak jadwal agar rapi mengikuti badge Larkon
                        eventDidMount: function (info) {
                            info.el.style.whiteSpace = 'normal';
                            info.el.style.borderRadius = '4px';
                            info.el.style.padding = '4px 6px';
                            info.el.style.border = 'none';
                        }
                    });

                    // Menyalakan sistem kalendarnya
                    calendar.render();
                }
            });
        </script>
    @endpush
@endsection