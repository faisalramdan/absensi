<div class="row">
    {{-- 1. Pilihan Perusahaan (Company) --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Perusahaan <span class="text-danger">*</span>
        </label>
        <select name="company_id" id="company_id" class="form-control" required>
            <option value="">-- Pilih Perusahaan --</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ (old('company_id', $defaultCompanyId ?? '') == $company->id) ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 2. Pilihan Karyawan (Tersaring Berdasarkan Perusahaan) --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Karyawan <span class="text-danger">*</span>
        </label>
        <select name="employee_id" id="employee_id" class="form-control" required>
            <option value="">-- Pilih Karyawan --</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" data-company-id="{{ $employee->company_id }}" {{ (old('employee_id', $attendanceLog->employee_id ?? '') == $employee->id) ? 'selected' : '' }}>
                    {{ $employee->nik }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Tanggal <span class="text-danger">*</span>
        </label>
        <input type="date" name="date" class="form-control"
            value="{{ old('date', $attendanceLog->date ?? date('Y-m-d')) }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">
            Source
        </label>
        <input type="text" class="form-control" value="{{ $attendanceLog->source ?? 'manual' }}" disabled readonly>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Check In
        </label>
        <input type="time" name="check_in" class="form-control"
            value="{{ old('check_in', $attendanceLog->check_in ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">
            Check Out
        </label>
        <input type="time" name="check_out" class="form-control"
            value="{{ old('check_out', $attendanceLog->check_out ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">
        Catatan
    </label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $attendanceLog->notes ?? '') }}</textarea>
</div>

{{-- Skrip JavaScript untuk Sinkronisasi Dropdown Perusahaan -> Karyawan --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const companySelect = document.getElementById("company_id");
        const employeeSelect = document.getElementById("employee_id");

        if (companySelect && employeeSelect) {
            // Simpan semua opsi karyawan mentah
            const originalOptions = Array.from(employeeSelect.options).map(option => ({
                value: option.value,
                text: option.text,
                companyId: option.getAttribute("data-company-id")
            }));

            const oldEmployeeSelected = "{{ old('employee_id', $attendanceLog->employee_id ?? '') }}";

            function filterEmployees() {
                const selectedCompany = companySelect.value;
                employeeSelect.innerHTML = "";

                // Tambahkan opsi default
                const defaultOpt = document.createElement("option");
                defaultOpt.value = "";
                defaultOpt.text = "-- Pilih Karyawan --";
                employeeSelect.appendChild(defaultOpt);

                // Masukkan karyawan yang sesuai dengan company_id
                originalOptions.forEach(opt => {
                    if (opt.value !== "") {
                        if (selectedCompany === "" || opt.companyId === selectedCompany) {
                            const newOpt = document.createElement("option");
                            newOpt.value = opt.value;
                            newOpt.textContent = opt.text;
                            newOpt.setAttribute("data-company-id", opt.companyId);

                            // Set selected jika cocok dengan data sebelumnya
                            if (newOpt.value === oldEmployeeSelected) {
                                newOpt.selected = true;
                            }

                            employeeSelect.appendChild(newOpt);
                        }
                    }
                });
            }

            // Jalankan saat perusahaan diubah manual oleh user
            companySelect.addEventListener("change", function () {
                // Reset pilihan karyawan jika perusahaan diganti
                employeeSelect.value = "";
                filterEmployees();
            });

            // Jalankan saat pertama kali halaman dimuat (untuk menyesuaikan default login / old value)
            filterEmployees();
        }
    });
</script>