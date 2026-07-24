@extends('layouts.app')
@section('title', 'Atur Jadwal Massal Karyawan')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:danger-bold" class="me-1"></iconify-icon>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 fw-semibold text-dark">Form Penjadwalan Massal (Bulk Assign)</h5>
                                <p class="text-muted small mb-0">Fitur ini hanya memunculkan karyawan yang <b>belum memiliki
                                        jadwal sama sekali</b> pada periode terpilih.</p>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('assignments.store') }}" method="POST">
                                    @csrf

                                    <div class="row g-4">

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Pilih Shift Kerja <span
                                                    class="text-danger">*</span></label>
                                            <select name="shift_id" class="form-select" required>
                                                <option value="">-- Pilih Master Shift --</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Bulan Rencana Jadwal</label>
                                            <select id="plan_month" class="form-select">
                                                @foreach(range(1, 12) as $m)
                                                    @php
                                                        $monthName = \Carbon\Carbon::create(2000, $m, 1)->translatedFormat('F');
                                                        $selected = date('m') == $m ? 'selected' : '';
                                                    @endphp
                                                    <option value="{{ sprintf('%02d', $m) }}" {{ $selected }}>{{ $monthName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Tahun Rencana Jadwal</label>
                                            <select id="plan_year" class="form-select">
                                                @foreach(range(date('Y') - 1, date('Y') + 2) as $y)
                                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Tanggal Mulai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date" id="start_date"
                                                class="form-control bg-light" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Tanggal Selesai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date" id="end_date" class="form-control bg-light"
                                                required>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold d-block mb-2">Pengecualian Hari
                                                Libur</label>

                                            <div class="form-check form-check-inline me-4">
                                                <input class="form-check-input" type="checkbox" name="include_sunday"
                                                    value="1" id="includeSunday" {{ old('include_sunday') ? 'checked' : '' }}>
                                                <label class="form-check-label text-dark" for="includeSunday">
                                                    Tetap masukkan jadwal pada hari <span
                                                        class="badge bg-light text-danger fw-medium">Minggu</span>
                                                </label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="include_holiday"
                                                    value="1" id="includeHoliday" {{ old('include_holiday') ? 'checked' : '' }}>
                                                <label class="form-check-label text-dark" for="includeHoliday">
                                                    Tetap masukkan jadwal pada hari <span
                                                        class="badge bg-light text-danger fw-medium">Libur Nasional</span>
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1 fs-12">Jika tidak dicentang, sistem secara
                                                otomatis akan melewati (skip) hari tersebut.</small>
                                        </div>

                                        {{-- 🌟 TAMBAHAN: PILIH PERUSAHAAN --}}
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Pilih Perusahaan <span
                                                    class="text-danger">*</span></label>
                                            @php
                                                // Logika otomatis pilih perusahaan berdasarkan user login
                                                $selectedCompany = auth()->user()->company_id ?? (auth()->user()->employee->company_id ?? null);
                                            @endphp
                                            <select name="company_id" id="company_select" class="form-select" required>
                                                <option value="">-- Pilih Perusahaan --</option>
                                                {{-- Pastikan variabel $companies sudah dikirim dari Controller --}}
                                                @if(isset($companies))
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $selectedCompany == $company->id ? 'selected' : '' }}>
                                                            {{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Pilih Karyawan Yang Belum Punya Jadwal
                                                <span class="text-danger">*</span></label>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted" id="employee_count_info">Memuat daftar
                                                    karyawan...</small>
                                                <button type="button" class="btn btn-sm btn-light-primary py-0 px-2 fs-12"
                                                    id="btn-select-all">Pilih Semua Karyawan</button>
                                            </div>
                                            <select name="employee_ids[]" id="employee_select" class="form-select"
                                                style="height: 200px;" multiple required>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Catatan / Keterangan Jadwal <span
                                                    class="text-muted small">(Opsional)</span></label>
                                            <textarea name="notes" class="form-control" rows="3"
                                                placeholder="Contoh: Penugasan Shift Reguler Periode Bulan Ini...">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Batal</a>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <iconify-icon icon="solar:diskette-bold"
                                                class="align-middle me-1"></iconify-icon>
                                            Proses & Simpan Jadwal
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnSelectAll = document.getElementById('btn-select-all');
            const employeeSelect = document.getElementById('employee_select');
            const planMonth = document.getElementById('plan_month');
            const planYear = document.getElementById('plan_year');
            const companySelect = document.getElementById('company_select'); // 🌟 Ambil elemen perusahaan
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const infoText = document.getElementById('employee_count_info');

            let isAllSelected = false;

            // 1. Fungsi Menghitung Tanggal Periode & Ambil Karyawan via AJAX
            function updatePeriodAndEmployees() {
                const month = planMonth.value;
                const year = planYear.value;
                const companyId = companySelect.value; // 🌟 Ambil ID Perusahaan yang sedang dipilih

                if (!month || !year || !companyId) {
                    // Jika perusahaan belum dipilih, kosongkan list
                    employeeSelect.innerHTML = '<option value="" disabled>Silakan pilih perusahaan terlebih dahulu...</option>';
                    infoText.textContent = "Menunggu pilihan perusahaan.";
                    return;
                }

                // Set Tanggal Selesai (25 Bulan Terpilih)
                const endDateStr = `${year}-${String(month).padStart(2, '0')}-25`;

                // Set Tanggal Mulai (Mundur 1 bulan, Kunci tanggal 26)
                let startYear = parseInt(year);
                let startMonth = parseInt(month) - 1;
                if (startMonth < 1) {
                    startMonth = 12;
                    startYear -= 1;
                }
                const startDateStr = `${startYear}-${String(startMonth).padStart(2, '0')}-26`;

                startDateInput.value = startDateStr;
                endDateInput.value = endDateStr;

                // Reset Status tombol pilih semua
                isAllSelected = false;
                btnSelectAll.textContent = "Pilih Semua Karyawan";
                btnSelectAll.classList.replace('btn-light-danger', 'btn-light-primary');

                // Eksekusi Ambil Data Karyawan via AJAX
                infoText.textContent = "Menghitung ulang daftar karyawan...";
                employeeSelect.innerHTML = '<option value="" disabled>Sedang memuat data...</option>';

                // 🌟 Kirim parameter company_id ke URL AJAX
                fetch(`{{ route('assignments.available-employees') }}?month=${month}&year=${year}&company_id=${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        employeeSelect.innerHTML = ''; // bersihkan status loading

                        if (data.length === 0) {
                            infoText.textContent = "Semua karyawan di perusahaan ini sudah memiliki jadwal pada periode ini.";
                            return;
                        }

                        infoText.textContent = `Terdapat ${data.length} karyawan yang belum memiliki jadwal.`;

                        // Isi opsi dropdown karyawan baru
                        data.forEach(emp => {
                            const option = document.createElement('option');
                            option.value = emp.id;
                            option.textContent = `${emp.full_name} (${emp.nik || '-'})`;
                            employeeSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        infoText.textContent = "Gagal memuat data karyawan.";
                    });
            }

            // Jalankan otomatis saat form dibuka pertama kali
            updatePeriodAndEmployees();

            // Jalankan setiap kali dropdown Bulan, Tahun, atau PERUSAHAAN diganti
            planMonth.addEventListener('change', updatePeriodAndEmployees);
            planYear.addEventListener('change', updatePeriodAndEmployees);
            if (companySelect) {
                companySelect.addEventListener('change', updatePeriodAndEmployees); // 🌟 Trigger jika perusahaan diganti
            }

            // 2. Logika Tombol Pilih Semua Karyawan
            if (btnSelectAll && employeeSelect) {
                btnSelectAll.addEventListener('click', function () {
                    if (employeeSelect.options.length === 0 || employeeSelect.options[0].disabled) return;

                    isAllSelected = !isAllSelected;

                    for (let i = 0; i < employeeSelect.options.length; i++) {
                        employeeSelect.options[i].selected = isAllSelected;
                    }

                    if (isAllSelected) {
                        this.textContent = "Batalkan Semua Pilihan";
                        this.classList.replace('btn-light-primary', 'btn-light-danger');
                    } else {
                        this.textContent = "Pilih Semua Karyawan";
                        this.classList.replace('btn-light-danger', 'btn-light-primary');
                    }
                });
            }
        });
    </script>
@endsection