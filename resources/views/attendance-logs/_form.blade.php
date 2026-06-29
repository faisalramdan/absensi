<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Karyawan
        </label>

        <select name="employee_id" class="form-control" required>

            <option value="">
                -- Pilih Karyawan --
            </option>

            @foreach($employees as $employee)

                <option value="{{ $employee->id }}" @selected(
                    old(
                        'employee_id',
                        $attendanceLog->employee_id ?? ''
                    ) == $employee->id
                )>
                    {{ $employee->full_name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3 mb-3">

        <label class="form-label">
            Tanggal
        </label>

        <input type="date" name="date" class="form-control" value="{{ old('date', $attendanceLog->date ?? '') }}"
            required>

    </div>

    <div class="col-md-3 mb-3">

        <label class="form-label">
            Source
        </label>

        <input type="text" class="form-control" value="{{ $attendanceLog->source ?? 'manual' }}" readonly>

    </div>

</div>

<div class="row">

    <div class="col-md-3 mb-3">

        <label class="form-label">
            Check In
        </label>

        <input type="time" name="check_in" class="form-control"
            value="{{ old('check_in', $attendanceLog->check_in ?? '') }}">

    </div>

    <div class="col-md-3 mb-3">

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