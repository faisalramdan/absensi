@extends('layouts.app')
@section('title', 'Penjadwalan Karyawan Matrix')

@section('content')
<style>
    /* Menghilangkan border default dan panah bawaan browser agar dropdown tampak bersih */
    .inline-shift-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important;
        padding-right: 0 !important;
        text-align: center;
        text-align-last: center; /* Memastikan teks di tengah untuk beberapa browser */
        border: none !important;
        background-color: transparent;
        width: 100%;
        height: 100%;
        min-width: 65px;
        font-weight: 600 !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Memberikan efek sedikit bayangan/border tipis saat dropdown di-klik atau fokus */
    .inline-shift-select:focus {
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.2) !important;
        background-color: #fff !important;
        border-radius: 4px;
    }

    /* Mengembalikan warna teks opsi di dalam dropdown agar tetap hitam/jelas saat dibuka */
    .inline-shift-select option {
        color: #333 !important;
        font-weight: normal !important;
        background-color: #fff !important;
    }
</style>

    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:danger-bold" class="me-1"></iconify-icon>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('assignments.index') }}">
                        <div class="row g-3 align-items-end">
                            
                            {{-- 1. Kolom Filter Company --}}
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pilih Perusahaan</label>
                                
                                @php
                                    if (request()->has('company_id')) {
                                        $selectedCompany = request('company_id');
                                    } else {
                                        $selectedCompany = auth()->user()->company_id ?? (auth()->user()->employee->company_id ?? null);
                                    }
                                @endphp

                                {{-- Tambahkan id="company_id" untuk Javascript --}}
                                <select name="company_id" id="company_id" class="form-select">
                                    <option value="">Semua Perusahaan</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $selectedCompany == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Kolom Filter Karyawan --}}
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pilih Karyawan</label>
                                
                                {{-- Tambahkan id="employee_id" untuk Javascript --}}
                                <select name="employee_id" id="employee_id" class="form-select">
                                    <option value="">-- Semua Karyawan --</option>
                                    @foreach($allActiveEmployees as $emp)
                                        {{-- Kita simpan company_id di atribut data-company-id --}}
                                        <option value="{{ $emp->id }}" data-company-id="{{ $emp->company_id }}">
                                            {{ $emp->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Kolom Filter Bulan --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Bulan</label>
                                <select name="month" class="form-select">
                                    @foreach(range(1, 12) as $m)
                                        @php
                                            $monthName = \Carbon\Carbon::create(2000, $m, 1)->translatedFormat('F');
                                            $selected = request('month', date('m')) == $m ? 'selected' : '';
                                        @endphp
                                        <option value="{{ sprintf('%02d', $m) }}" {{ $selected }}>{{ $monthName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 4. Kolom Filter Tahun --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Tahun</label>
                                <select name="year" class="form-select">
                                    @foreach(range(date('Y') - 1, date('Y') + 2) as $y)
                                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 5. Kolom Tombol --}}
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <a href="{{ route('assignments.index') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Matriks Jadwal Shift Kerja</h4>
                            <p class="text-muted mb-0">Periode: <b>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</b> sampai <b>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</b></p>
                        </div>
                        <div>
                            @can('shift-assignment.create')
                                <a href="{{ route('assignments.create') }}" class="btn btn-primary btn-sm">
                                    + Atur Jadwal Massal
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered align-middle table-sm table-hover text-center mb-0">
                                <thead class="table-light sticky-top" style="z-index: 2;">
                                    <tr>
                                        <th scope="col" class="text-start ps-3 align-middle" style="min-width: 180px; position: sticky; left: 0; background: #f8f9fa; z-index: 3;">Nama Karyawan</th>
                                        
                                        @foreach($dates as $date)
                                            @php
                                                $carbonDate = \Carbon\Carbon::parse($date);
                                                $isSunday = $carbonDate->isSunday();
                                                $isHoliday = array_key_exists($date, $holidays);
                                                $holidayName = $isHoliday ? $holidays[$date] : '';
                                            @endphp
                                            
                                            <th class="text-center {{ $isSunday || $isHoliday ? 'bg-danger text-white' : '' }}" 
                                                style="min-width: 50px;"
                                                @if($isHoliday) data-bs-toggle="tooltip" title="Hari Libur: {{ $holidayName }}" @endif>
                                                <small class="d-block fs-10 fw-normal">{{ $carbonDate->translatedFormat('D') }}</small>
                                                <span class="fs-12 fw-bold">{{ $carbonDate->format('d') }}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr>
                                            <td class="text-start ps-3 fw-semibold text-dark" style="position: sticky; left: 0; background: #fff; z-index: 1; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">
                                                {{ $employee->full_name }}
                                            </td>

                                            @foreach($dates as $date)
                                                @php
                                                    $isSunday = \Carbon\Carbon::parse($date)->isSunday();
                                                    $isHoliday = array_key_exists($date, $holidays);
                                                    $holidayName = $isHoliday ? $holidays[$date] : '';
                                                    
                                                    // Ambil data array penugasan baru dari controller
                                                    $assignment = $assignmentsData[$employee->id][$date] ?? null;
                                                    $currentShiftId = $assignment ? $assignment['shift_id'] : '';
                                                    $shiftName      = $assignment ? $assignment['shift_name'] : '-';
                                                @endphp

                                                {{-- Kolom td otomatis memerah samar jika Minggu / Hari Libur Nasional --}}
                                                <td class="text-center p-1 @if($isSunday || $isHoliday) bg-danger-subtle @endif"
                                                    @if($isHoliday) data-bs-toggle="tooltip" title="Libur Nasional: {{ $holidayName }}" @endif>
                                                    
                                                    {{-- Dropdown Live Edit Inline Matrix --}}
                                                    <select class="form-select form-select-sm inline-shift-select text-center p-0 fs-11 fw-medium border-0 bg-transparent"
                                                            data-employee-id="{{ $employee->id }}"
                                                            data-date="{{ $date }}"
                                                            style="min-width: 75px; cursor: pointer;">
                                                        
                                                        <option value="" class="text-muted">-</option>
                                                        @foreach($shifts as $shift)
                                                            <option value="{{ $shift->id }}" {{ $currentShiftId == $shift->id ? 'selected' : '' }}>
                                                                {{ $shift->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($dates) + 1 }}" class="text-center text-muted py-4">
                                                Tidak ada data karyawan ditemukan.
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

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // =========================================================
        // 1. LOGIKA FILTER DINAMIS (PERUSAHAAN -> KARYAWAN)
        // =========================================================
        const companySelect = document.getElementById("company_id");
        const employeeSelect = document.getElementById("employee_id");
        
        // Memastikan elemen ada agar tidak error
        if (companySelect && employeeSelect) {
            const allEmployeeOptions = Array.from(employeeSelect.options);
            const oldEmployeeSelected = "{{ request('employee_id') }}";

            function updateEmployeeList() {
                const selectedCompany = companySelect.value;
                employeeSelect.innerHTML = "";

                allEmployeeOptions.forEach(option => {
                    if (option.value === "") {
                        employeeSelect.appendChild(option);
                        return;
                    }

                    const employeeCompanyId = option.getAttribute("data-company-id");
                    if (selectedCompany === "" || employeeCompanyId === selectedCompany) {
                        employeeSelect.appendChild(option);
                    }
                });

                if (oldEmployeeSelected) {
                    employeeSelect.value = oldEmployeeSelected;
                }
            }

            companySelect.addEventListener("change", function() {
                updateEmployeeList();
                employeeSelect.value = ""; 
            });

            updateEmployeeList();
        }


        // =========================================================
        // 2. LOGIKA UPDATE JADWAL INLINE (BAWAAN ANDA)
        // =========================================================
        const selects = document.querySelectorAll('.inline-shift-select');

        selects.forEach(select => {
            // Beri warna teks awal saat halaman pertama dimuat
            applySelectColor(select);

            // Handler ketika dropdown diubah nilainya
            select.addEventListener('change', function () {
                const employeeId = this.getAttribute('data-employee-id');
                const date = this.getAttribute('data-date');
                const shiftId = this.value;
                const element = this;

                // Indikator loading visual sementara proses simpan
                element.classList.add('bg-warning-subtle');

                fetch(`{{ route('assignments.update-inline') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        date: date,
                        shift_id: shiftId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    element.classList.remove('bg-warning-subtle');
                    if (data.status === 'success') {
                        // Perbarui pewarnaan teks
                        applySelectColor(element);
                        
                        // Efek kilas sukses warna hijau kilat
                        element.classList.add('bg-success-subtle');
                        setTimeout(() => element.classList.remove('bg-success-subtle'), 400);
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    element.classList.remove('bg-warning-subtle');
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem saat menyimpan jadwal.');
                    location.reload();
                });
            });
        });

        // Fungsi mengubah kelas warna teks Bootstrap secara real-time
        function applySelectColor(el) {
            const selectedText = el.options[el.selectedIndex].text.toLowerCase();
            
            el.classList.remove('text-primary', 'text-success', 'text-purple', 'text-danger', 'text-muted', 'fw-bold');
            
            if (selectedText === '-') {
                el.classList.add('text-muted');
            } else if (selectedText.includes('siang')) {
                el.classList.add('text-primary', 'fw-bold');
            } else if (selectedText.includes('pagi')) {
                el.classList.add('text-success', 'fw-bold');
            } else if (selectedText.includes('malam')) {
                el.classList.add('text-purple', 'fw-bold');
            } else {
                el.classList.add('text-danger', 'fw-bold');
            }
        }
    });
</script>

@endsection