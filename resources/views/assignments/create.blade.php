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
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const infoText = document.getElementById('employee_count_info');

            let isAllSelected = false;

            // 1. Fungsi Menghitung Tanggal Periode & Ambil Karyawan via AJAX
            function updatePeriodAndEmployees() {
                const month = planMonth.value;
                const year = planYear.value;

                if (!month || !year) return;

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

                fetch(`{{ route('assignments.available-employees') }}?month=${month}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        employeeSelect.innerHTML = ''; // bersihkan status loading

                        if (data.length === 0) {
                            infoText.textContent = "Semua karyawan sudah memiliki jadwal pada periode ini.";
                            return;
                        }

                        // KODE YANG BENAR
                        infoText.textContent = `Terdapat ${data.length} karyawan yang belum memiliki jadwal.`;

                        // Isi opsi dropdown karyawan baru
                        data.forEach(emp => {
                            const option = document.createElement('option');
                            option.value = emp.id;
                            option.textContent = `${emp.full_name} (${emp.employee_code || '-'})`;
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

            // Jalankan setiap kali dropdown Bulan atau Tahun diganti
            planMonth.addEventListener('change', updatePeriodAndEmployees);
            planYear.addEventListener('change', updatePeriodAndEmployees);

            // 2. Logika Tombol Pilih Semua Karyawan
            if (btnSelectAll && employeeSelect) {
                btnSelectAll.addEventListener('click', function () {
                    if (employeeSelect.options.length === 0) return;

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