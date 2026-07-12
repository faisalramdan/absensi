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

                <div class="col-xl-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent py-3">
                           <form method="GET" action="{{ request()->url() }}" id="filterForm">
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

                                    {{-- 3. Tanggal Start --}}
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold text-primary">Tanggal Start</label>
                                        <input type="date" name="start_date" id="startDate" value="{{ $startDate }}" class="form-control border-primary-subtle">
                                    </div>

                                    {{-- 4. Tanggal End --}}
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold text-primary">Tanggal End</label>
                                        <input type="date" name="end_date" id="endDate" value="{{ $endDate }}" class="form-control border-primary-subtle">
                                    </div>
                                    <div class="col-md-4 d-flex gap-2 mt-3">
                                        <a href="{{ request()->url() }}" class="btn btn-secondary px-4">Reset</a>
                                        <button type="submit" class="btn btn-primary px-4">Apply Filter</button>
                                    </div>
                                    

                                    

                                </div>
                            </form>
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
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold">
                                                        Present: {{ number_format($summary['present'] ?? 0) }}
                                                    </span>
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1.5 fw-semibold">
                                                        WFA: {{ number_format($summary['wfa'] ?? 0) }}
                                                    </span>
                                                    <span
                                                        class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1.5 fw-semibold">
                                                        Sakit: {{ number_format($summary['sakit'] ?? 0) }}
                                                    </span>
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1.5 fw-semibold">
                                                        Ijin: {{ number_format($summary['ijin'] ?? 0) }}
                                                    </span>
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 fw-semibold">
                                                        Alpha: {{ number_format($summary['alpha'] ?? 0) }}
                                                    </span>
                                                    <span class="badge px-2 py-1.5 fw-semibold border"
                                                        style="background-color: #f3e5f5; color: #8e24aa; border-color: #d1c4e9 !important;">
                                                        Cuti: {{ number_format($summary['cuti'] ?? 0) }}
                                                    </span>
                                                    <span class="badge px-2 py-1.5 fw-semibold border"
                                                        style="background-color: #e8eaf6; color: #3f51b5; border-color: #c5cae9 !important;">
                                                        IDT: {{ number_format($summary['idt'] ?? 0) }}
                                                    </span>
                                                    <span class="badge px-2 py-1.5 fw-semibold border"
                                                        style="background-color: #e0f2f1; color: #00796b; border-color: #b2dfdb !important;">
                                                        IPC: {{ number_format($summary['ipc'] ?? 0) }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Pelanggaran Waktu Masuk --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Keterlambatan (Late)</th>
                                            <td>
                                                <span
                                                    class="fw-bold text-warning">{{ number_format($summary['late'] ?? 0) }}
                                                    Kali</span>
                                                @if(($summary['total_late_minutes'] ?? 0) > 0)
                                                    <span class="text-muted small ms-2">
                                                        (Total durasi: {{ $summary['late_hours'] ?? 0 }} Jam
                                                        {{ $summary['late_minutes_remainder'] ?? 0 }} Menit)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Pelanggaran Waktu Pulang --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Pulang Cepat (Early Leave)</th>
                                            <td>
                                                <span class="fw-bold"
                                                    style="color: #00796b;">{{ number_format($summary['early_leave'] ?? 0) }}
                                                    Kali</span>
                                                @if(($summary['total_early_leave_minutes'] ?? 0) > 0)
                                                    <span class="text-muted small ms-2">
                                                        (Total durasi: {{ $summary['early_leave_hours'] ?? 0 }} Jam
                                                        {{ $summary['early_leave_minutes_remainder'] ?? 0 }} Menit)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Kelalaian Absen --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Kelalaian Log Absensi</th>
                                            <td>
                                                <span class="text-danger fw-medium">
                                                    Lupa Check-In: <strong>{{ $summary['forgot_check_in'] ?? 0 }}</strong>
                                                    <span class="text-muted mx-2">|</span>
                                                    Lupa Check-Out: <strong>{{ $summary['forgot_check_out'] ?? 0 }}</strong>
                                                </span>
                                            </td>
                                        </tr>

                                        {{-- Hari Kerja --}}
                                        <tr>
                                            <th class="fw-semibold text-secondary">Hari Kerja Resmi</th>
                                            <td class="text-dark fw-medium">
                                                {{ $workingDays ?? 0 }} Hari Kerja
                                                <span class="text-muted small fw-normal ms-2">
                                                    (Dari {{ $calendarDays ?? 0 }} hari kalender - {{ $sundayCount ?? 0 }}
                                                    Off - {{ $holidayCount ?? 0 }} Libur)
                                                </span>
                                            </td>
                                        </tr>

                                        {{-- Akumulasi Kurang Jam Kerja --}}
                                        <tr class="table-light">
                                            <th class="fw-medium text-secondary">Kurang dari jam kerja</th>
                                            <td class="fw-bold text-dark">
                                                {{ $summary['short_work_count'] ?? 0 }}x
                                            </td>
                                        </tr>
                                        <tr class="table-danger">
                                            <th class="fw-bold text-danger">Total kurang jam kerja</th>
                                            <td class="fs-5 fw-bold text-danger">
                                                @if(isset($summary['total_short_work_minutes']) && $summary['total_short_work_minutes'] > 0)
                                                    {{ floor($summary['total_short_work_minutes'] / 60) }} <small
                                                        class="fs-6 fw-normal">Jam</small>
                                                    {{ $summary['total_short_work_minutes'] % 60 }} <small
                                                        class="fs-6 fw-normal">Menit</small>
                                                @else
                                                    0 <small class="fs-6 fw-normal">Jam</small> 0 <small
                                                        class="fs-6 fw-normal">Menit</small>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
                {{-- Statistics (Hitungan Status Pengajuan Awal) --}}
                <div class="row mb-4">
                    @php
                        $cards = [
                            ['Pending', $pendingLeaves, 'warning', 'solar:clock-circle-bold-duotone'],
                            ['Approved', $approvedLeaves, 'success', 'solar:check-circle-bold-duotone'],
                            ['Rejected', $rejectedLeaves, 'danger', 'solar:close-circle-bold-duotone'],
                        ];
                    @endphp

                    @foreach($cards as $card)
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 fw-medium">{{ $card[0] }}</p>
                                            <h2 class="mb-0 fw-bold">{{ $card[1] }}</h2>
                                        </div>

                                        <div class="avatar-md bg-{{ $card[2] }}-subtle rounded">
                                            <div class="avatar-title">
                                                <iconify-icon icon="{{ $card[3] }}" class="fs-28 text-{{ $card[2] }}">
                                                </iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Leave Table (Rincian Kuota Cuti) --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h4 class="card-title mb-0 fw-bold text-dark">
                            <iconify-icon icon="solar:clipboard-list-bold-duotone"
                                class="align-middle me-1 text-primary fs-20"></iconify-icon>
                            Rincian Kuota Cuti Saya
                        </h4>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
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

                                            $percentage = $quota > 0
                                                ? min(100, round(($used / $quota) * 100))
                                                : 0;

                                            $barColor = 'bg-primary';
                                            if ($percentage >= 80) {
                                                $barColor = 'bg-danger';
                                            } elseif ($percentage >= 50) {
                                                $barColor = 'bg-warning';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-medium text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ $allocation->leaveType?->name ?? 'Jenis Cuti N/A' }}
                                                </div>
                                            </td>
                                            <td class="text-center fw-semibold text-secondary">
                                                {{ floatval($quota) }}
                                            </td>
                                            <td class="text-center fw-semibold text-danger">
                                                {{ floatval($used) }}
                                            </td>
                                            <td class="text-center fw-bold text-success">
                                                {{ floatval($remaining) }}
                                            </td>
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
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <iconify-icon icon="solar:box-minimalistic-bold-duotone"
                                                    class="fs-40 mb-2 d-block text-secondary"></iconify-icon>
                                                Tidak ada data alokasi cuti aktif untuk periode kontrak Anda.
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