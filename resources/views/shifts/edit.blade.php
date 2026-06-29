@extends('layouts.app')
@section('title', 'Edit Shift: ' . $shift->name)
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
                <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0 fw-semibold text-dark">Ubah Informasi Utama Shift</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nama Shift <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder="Contoh: Shift 1, Jam Kerja Reguler" value="{{ old('name', $shift->name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                                            <input type="text" name="description" class="form-control" placeholder="Contoh: Shift operasional normal Senin - Jumat" value="{{ old('description', $shift->description) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0 fw-semibold text-dark">Konfigurasi Jam Kerja Harian</h5>
                                    <p class="text-muted small mb-0">Sesuaikan kembali jam masuk, jam pulang, dan batas toleransi terlambat untuk masing-masing hari.</p>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light text-secondary">
                                                <tr>
                                                    <th class="ps-4" width="15%">Hari</th>
                                                    <th width="15%" class="text-center">Libur (OFF)</th>
                                                    <th width="23%">Jam Masuk <span class="text-danger">*</span></th>
                                                    <th width="23%">Jam Pulang <span class="text-danger">*</span></th>
                                                    <th width="24%" class="pe-4">Batas Akhir Terlambat <span class="text-danger">*</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $dayLabels = [
                                                        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                                                    ];
                                                @endphp

                                                @foreach($dayLabels as $engDay => $indoDay)
                                                    @php
                                                        // Mengambil data detail dari database untuk hari terkait (jika ada)
                                                        $detail = $shiftDetails->get($engDay);
                                                        $isOff = old("days.$engDay.is_off", $detail?->is_off ?? false);
                                                        
                                                        // Format jam agar sesuai dengan input type="time" (HH:MM)
                                                        $startTime = old("days.$engDay.start_time", $detail?->start_time ? \Carbon\Carbon::parse($detail->start_time)->format('H:i') : '08:00');
                                                        $endTime = old("days.$engDay.end_time", $detail?->end_time ? \Carbon\Carbon::parse($detail->end_time)->format('H:i') : '17:00');
                                                        $lateDeadline = old("days.$engDay.late_deadline", $detail?->late_deadline ? \Carbon\Carbon::parse($detail->late_deadline)->format('H:i') : '08:15');
                                                    @endphp
                                                    
                                                    <tr class="day-row">
                                                        <td class="ps-4 fw-semibold text-dark">{{ $indoDay }}</td>
                                                        
                                                        <td class="text-center">
                                                            <div class="form-check d-inline-block">
                                                                <input class="form-check-input checkbox-off" type="checkbox" 
                                                                       name="days[{{ $engDay }}][is_off]" value="1" id="off-{{ $engDay }}"
                                                                       {{ $isOff ? 'checked' : '' }}>
                                                                <label class="form-check-label fs-12 text-muted" for="off-{{ $engDay }}">Libur</label>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <input type="time" name="days[{{ $engDay }}][start_time]" 
                                                                   class="form-control time-input" 
                                                                   value="{{ $startTime }}">
                                                        </td>

                                                        <td>
                                                            <input type="time" name="days[{{ $engDay }}][end_time]" 
                                                                   class="form-control time-input" 
                                                                   value="{{ $endTime }}">
                                                        </td>

                                                        <td class="pe-4">
                                                            <input type="time" name="days[{{ $engDay }}][late_deadline]" 
                                                                   class="form-control time-input" 
                                                                   value="{{ $lateDeadline }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card-footer d-flex justify-content-end gap-2 bg-transparent border-top p-3">
                                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <iconify-icon icon="solar:diskette-bold" class="align-middle me-1"></iconify-icon>
                                        Perbarui Master Shift
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkBoxes = document.querySelectorAll('.checkbox-off');

            checkBoxes.forEach(checkbox => {
                function toggleInputs(cb) {
                    const row = cb.closest('.day-row');
                    const timeInputs = row.querySelectorAll('.time-input');
                    
                    timeInputs.forEach(input => {
                        if (cb.checked) {
                            input.disabled = true;
                            input.removeAttribute('required');
                            input.style.opacity = '0.5';
                        } else {
                            input.disabled = false;
                            input.setAttribute('required', 'required');
                            input.style.opacity = '1';
                        }
                    });
                }

                // Jalankan di awal untuk mendeteksi data lama yang berstatus Libur (is_off = true)
                toggleInputs(checkbox);

                checkbox.addEventListener('change', function () {
                    toggleInputs(this);
                });
            });
        });
    </script>
@endsection