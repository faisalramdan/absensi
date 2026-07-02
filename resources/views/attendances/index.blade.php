@extends('layouts.app')
@section('title', 'Absensi Harian')
@section('content')

    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                {{-- ALERT SUCCESS --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ALERT ERROR --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:danger-bold" class="me-1"></iconify-icon>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif


                {{-- FILTER --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                        <h5 class="mb-0 fw-semibold">Filter Data</h5>
                    </div>

                    <div class="card-body">
                        <form method="GET" id="filterForm">
                            <div class="row align-items-end g-3">

                                {{-- 1. Filter Tahun --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Tahun Periode</label>
                                    <select name="year" id="filterYear" class="form-select">
                                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                                            <option value="{{ $y }}" @selected($selectedYear == $y)>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- 2. Filter Bulan --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Bulan Periode</label>
                                    <select name="month" id="filterMonth" class="form-select">
                                        @foreach([
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                                            '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ] as $num => $name)
                                            <option value="{{ $num }}" @selected($selectedMonth == $num)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 3. Tanggal Start (Otomatis berubah via JS) --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-primary">Tanggal Start</label>
                                    <input type="date" name="start_date" id="startDate" value="{{ $startDate }}" class="form-control border-primary-subtle">
                                </div>

                                {{-- 4. Tanggal End (Otomatis berubah via JS) --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-primary">Tanggal End</label>
                                    <input type="date" name="end_date" id="endDate" value="{{ $endDate }}" class="form-control border-primary-subtle">
                                </div>

                                {{-- 5. Pilih Karyawan --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Pilih Karyawan</label>
                                    <select name="employee_id" class="form-select">
                                        <option value="">-- Semua Karyawan --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>
                                                [{{ $employee->nik }}] {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 6. Pilih Status --}}
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">-- Semua Status --</option>
                                        <option value="present" @selected(request('status') == 'present')>Present</option>
                                        <option value="alpha" @selected(request('status') == 'alpha')>Alpha</option>
                                        <option value="leave" @selected(request('status') == 'leave')>Leave</option>
                                        <option value="holiday" @selected(request('status') == 'holiday')>Holiday</option>
                                        <option value="off" @selected(request('status') == 'off')>Off</option>
                                    </select>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary px-4">Reset</a>
                                    <button type="submit" class="btn btn-primary px-4">Apply Filter</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- SUMMARY BLOCK: STATISTIK KEHADIRAN --}}
                <div class="col-xl-12 mb-4">
                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-transparent py-3">
                            <h4 class="card-title mb-0 fw-semibold text-dark">Ringkasan Statistik Kehadiran</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead>
                                        <tr class="bg-light text-muted small uppercase">
                                            <th width="40%">Metrik / Status Kehadiran</th>
                                            <th width="60%">Akumulasi Data & Keterangan Periode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Status Utama Kehadiran --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Status Kehadiran Utama</th>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold">
                                                        Present: {{ number_format($summary['present']) }}
                                                    </span>
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 fw-semibold">
                                                        Sakit: {{ number_format($summary['sakit']) }}
                                                    </span>
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1.5 fw-semibold">
                                                        Ijin: {{ number_format($summary['ijin']) }}
                                                    </span>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 fw-semibold">
                                                        Alpha: {{ number_format($summary['alpha']) }}
                                                    </span>
                                                    <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #f3e5f5; color: #8e24aa; border-color: #d1c4e9 !important;">
                                                        Cuti: {{ number_format($summary['cuti']) }}
                                                    </span>
                                                    <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">
                                                        IDT: {{ number_format($summary['idt']) }}
                                                    </span>
                                                    <span class="badge px-2 py-1.5 fw-semibold border" style="background-color: #e0f2f1; color: #00796b; border-color: #b2dfdb !important;">
                                                        IPC: {{ number_format($summary['ipc']) }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        {{-- Pelanggaran Waktu Masuk --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Keterlambatan (Late)</th>
                                            <td>
                                                <span class="fw-bold text-warning">{{ number_format($summary['late']) }} Kali</span>
                                                @if($summary['total_late_minutes'] > 0)
                                                    <span class="text-muted small ms-2">
                                                        (Total durasi: {{ $summary['late_hours'] }} Jam {{ $summary['late_minutes_remainder'] }} Menit)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Pelanggaran Waktu Pulang --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Pulang Cepat (Early Leave)</th>
                                            <td>
                                                <span class="fw-bold" style="color: #00796b;">{{ number_format($summary['early_leave']) }} Kali</span>
                                                @if($summary['total_early_leave_minutes'] > 0)
                                                    <span class="text-muted small ms-2">
                                                        (Total durasi: {{ $summary['early_leave_hours'] }} Jam {{ $summary['early_leave_minutes_remainder'] }} Menit)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Kelalaian Absen --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Kelalaian Log Absensi</th>
                                            <td>
                                                <span class="text-danger fw-medium">
                                                    Lupa Check-In: <strong>{{ $summary['forgot_check_in'] }}</strong> 
                                                    <span class="text-muted mx-2">|</span> 
                                                    Lupa Check-Out: <strong>{{ $summary['forgot_check_out'] }}</strong>
                                                </span>
                                            </td>
                                        </tr>

                                        {{-- Hari Kerja --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Hari Kerja Resmi</th>
                                            <td class="text-dark fw-medium">
                                                {{ $workingDays }} Hari Kerja 
                                                <span class="text-muted small fw-normal ms-2">
                                                    (Dari {{ $calendarDays }} hari kalender - {{ $sundayCount }} Off - {{ $holidayCount }} Libur)
                                                </span>
                                            </td>
                                        </tr>

                                        {{-- Akumulasi Jam Kerja --}}
                                        <tr class="table-success">
                                            <th class="fw-bold text-success">Total Jam Kerja Efektif</th>
                                            <td class="fs-5 fw-bold text-success">
                                                {{ number_format($summary['total_work_minutes']/60, 1) }} <small class="fs-6 fw-normal">Jam</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Attendance</h4>
                            <p class="text-muted mb-0">
                                {{ $attendances->total() }} data yang ditemukan di sistem Anda.
                            </p>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Karyawan</th>
                                        <th>Shift</th>
                                        <th>Masuk</th>
                                        <th>Pulang</th>
                                        <th>Telat</th>
                                        <th>Pulang Cepat</th>
                                        <th>Jam Kerja</th>
                                        <th>Status</th>
                                        <th>Source</th>
                                        <th class="text-end px-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                        <tr>
                                            <td>
                                                {{-- Menampilkan Nama Hari (misal: Senin) --}}
                                                <span class="text-muted small d-block mb-1">
                                                    {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}
                                                </span>
                                                {{-- Menampilkan Tanggal (misal: 26 Jun 2026) --}}
                                                <span class="fw-semibold text-dark">
                                                    {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $attendance->employee?->full_name ?? '-' }}</div>
                                                <small class="text-muted">{{ $attendance->employee?->nik }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    // Ambil nama shift, jika tidak ada default ke '-'
                                                    $shiftName = $attendance->shift?->name ?? '-';

                                                    // Tentukan warna badge berdasarkan jenis shift kerja (meniru contoh Anda)
                                                    $badgeClass = 'bg-light text-muted'; // Default jika '-'

                                                    if (str_contains(strtolower($shiftName), '1')) {
                                                        $badgeClass = 'bg-primary text-white';
                                                    } elseif (str_contains(strtolower($shiftName), '2')) {
                                                        $badgeClass = 'bg-success text-white';
                                                    } elseif (str_contains(strtolower($shiftName), '3')) {
                                                        $badgeClass = 'bg-purple text-white'; // Pastikan kelas .bg-purple tersedia di CSS/Bootstrap Anda
                                                    } elseif ($shiftName != '-') {
                                                        $badgeClass = 'bg-danger text-white';
                                                    }
                                                @endphp

                                                <span class="badge {{ $badgeClass }} px-2 py-1 fw-medium">
                                                    {{ $shiftName }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium">
                                                    {{ $attendance->actual_check_in ? date('H:i', strtotime($attendance->actual_check_in)) : '--:--' }}
                                                </span>

                                                
                                            </td>
                                            <td>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-medium">
                                                    {{ $attendance->actual_check_out ? date('H:i', strtotime($attendance->actual_check_out)) : '--:--' }}
                                                </span>

                                                
                                            </td>
                                            <td>
                                                <span
                                                    class="{{ $attendance->late_minutes > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                                    {{ $attendance->late_minutes ?? 0 }} mnt
                                                </span>
                                                @if($attendance->is_idt === true || $attendance->is_idt === 't' || $attendance->is_idt == 1)
                                                    <div class="mt-1">
                                                        <span
                                                            class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-medium">
                                                            I-IDT
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="{{ $attendance->early_leave_minutes > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">
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
                                                        {{ floor($attendance->work_minutes / 60) }} Jam
                                                        {{ $attendance->work_minutes % 60 }} Menit
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            
                                            <td>
                                                @php
                                                    $statusStyles = [
                                                        'present' => 'bg-success-subtle text-success border border-success-subtle',
                                                        'alpha' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                        'leave' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                        'holiday' => 'bg-info-subtle text-info border border-info-subtle',
                                                        'off' => 'bg-light text-secondary border',
                                                    ];
                                                    $currentStatus = strtolower($attendance->status);
                                                    $class = $statusStyles[$currentStatus] ?? 'bg-light text-dark';
                                                @endphp

                                                <span class="badge {{ $class }} px-2 py-1 fw-semibold">
                                                    {{ strtoupper($attendance->status) }}
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

                                                        <iconify-icon
                                                            icon="solar:file-download-bold-duotone"
                                                            class="text-success fs-22"
                                                            data-bs-toggle="tooltip"
                                                            title="Import Excel">
                                                        </iconify-icon>

                                                    @break

                                                    @case('manual')

                                                        <iconify-icon
                                                            icon="solar:pen-bold-duotone"
                                                            class="text-warning fs-22"
                                                            data-bs-toggle="tooltip"
                                                            title="Input Manual">
                                                        </iconify-icon>

                                                    @break

                                                    @case('generated')

                                                        <iconify-icon
                                                            icon="solar:cpu-bolt-bold-duotone"
                                                            class="text-primary fs-22"
                                                            data-bs-toggle="tooltip"
                                                            title="Generated System">
                                                        </iconify-icon>

                                                    @break

                                                    @default

                                                        <iconify-icon
                                                            icon="solar:question-circle-bold-duotone"
                                                            class="text-muted fs-22"
                                                            data-bs-toggle="tooltip"
                                                            title="Unknown">
                                                        </iconify-icon>

                                                @endswitch

                                            </td>
                                            <td class="text-end px-3">
                                                <a href="{{ route('attendances.show', $attendance) }}"
                                                    class="btn btn-sm btn-primary">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            {{-- Colspan disesuaikan menjadi 10 kolom --}}
                                            <td colspan="10" class="text-center py-4 text-muted">
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
                                Menampilkan {{ $attendances->count() }} dari {{ $attendances->total() }} data
                            </span>
                            {{ $attendances->withQueryString()->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    /*
    |--------------------------------------------------------------------------
    | Inisialisasi Bootstrap Tooltip
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    /*
    |--------------------------------------------------------------------------
    | Logika Auto-Update Tanggal Cut-Off (26 - 25)
    |--------------------------------------------------------------------------
    */
    const yearSelect = document.getElementById("filterYear");
    const monthSelect = document.getElementById("filterMonth");
    const startDateInput = document.getElementById("startDate");
    const endDateInput = document.getElementById("endDate");

    function updateCutoffDates() {
        // Ambil nilai tahun dan bulan dari dropdown filter
        const year = parseInt(yearSelect.value);
        const month = parseInt(monthSelect.value); // Nilai riil: 1 - 12

        // Validasi jika data input kosong atau corrupt, batalkan eksekusi
        if (isNaN(year) || isNaN(month)) return;

        // 1. Cari tanggal akhir (End Date): Tanggal 25 bulan ini
        const endDateObj = new Date(year, month - 1, 25);
        
        // 2. Cari tanggal awal (Start Date): Tanggal 26 dari bulan lalu
        // Jika bulan berjalan Januari (1), fungsi native JS otomatis mundur ke Desember tahun lalu.
        const startDateObj = new Date(year, month - 2, 26);

        // Helper fungsi format Date Object ke format input standar HTML: YYYY-MM-DD
        const formatDate = (date) => {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        };

        // Render hasil kalkulasi tanggal ke layar pengguna
        startDateInput.value = formatDate(startDateObj);
        endDateInput.value = formatDate(endDateObj);
    }

    // Pasang Event Listener agar fungsi berjalan setiap kali dropdown berubah nilai
    if (yearSelect && monthSelect) {
        yearSelect.addEventListener("change", updateCutoffDates);
        monthSelect.addEventListener("change", updateCutoffDates);
    }
});
</script>
@endsection