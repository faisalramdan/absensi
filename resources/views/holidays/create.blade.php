@extends('layouts.app')
@section('title', 'Tambah Hari Libur')
@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                <div class="row">

                    <div class="col-xl-8">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">
                                <h4 class="card-title mb-0 d-flex align-items-center">
                                    <iconify-icon icon="solar:calendar-add-bold-duotone"
                                        class="me-2 text-primary fs-22"></iconify-icon>
                                    Tambah Hari Libur
                                </h4>
                            </div>

                            <div class="card-body">

                                <form action="{{ route('holidays.store') }}" method="POST">
                                    @csrf

                                    <div class="row">

                                        {{-- Nama Hari Libur --}}
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                Nama Hari Libur
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}"
                                                placeholder="Contoh : Hari Raya Idul Fitri, Tahun Baru Masehi" required>

                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Tanggal Kalender Asli --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tanggal Kalender Asli
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" id="date_actual" name="date_actual"
                                                class="form-control @error('date_actual') is-invalid @enderror"
                                                value="{{ old('date_actual') }}" required>

                                            @error('date_actual')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted">Tanggal berdasarkan kalender resmi nasional.</small>
                                        </div>

                                        {{-- Tanggal Diterapkan (Libur) --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tanggal Diterapkan (Libur)
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" id="date_applied" name="date_applied"
                                                class="form-control @error('date_applied') is-invalid @enderror"
                                                value="{{ old('date_applied') }}" required>

                                            @error('date_applied')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted">Tanggal libur karyawan yang berlaku di sistem.</small>
                                        </div>

                                        {{-- Catatan / Keterangan --}}
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-semibold">
                                                Catatan / Alasan Pergeseran
                                            </label>
                                            <textarea name="notes" rows="4"
                                                class="form-control @error('notes') is-invalid @enderror"
                                                placeholder="Masukkan catatan opsional atau alasan jika libur digeser...">{{ old('notes') }}</textarea>

                                            @error('notes')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="p-3 bg-light mb-0 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-3">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Simpan Data
                                                </button>
                                            </div>
                                            <div class="col-lg-3">
                                                <a href="{{ route('holidays.index') }}"
                                                    class="btn btn-outline-secondary w-100">
                                                    Batal
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                    {{-- Sidebar Informasi --}}
                    <div class="col-xl-4">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">
                                <h4 class="card-title mb-0">Informasi</h4>
                            </div>

                            <div class="card-body">

                                <div class="alert alert-info mb-0">
                                    <h6 class="fw-bold">
                                        <iconify-icon icon="solar:info-circle-bold"
                                            class="me-1 vertical-middle"></iconify-icon>
                                        Petunjuk Pengisian
                                    </h6>
                                    <ul class="mb-0 ps-3 mt-2">
                                        <li class="mb-2">
                                            <strong>Nama Hari Libur:</strong> Tuliskan nama perayaan resmi secara jelas.
                                        </li>
                                        <li class="mb-2">
                                            <strong>Tanggal Kalender Asli:</strong> Merupakan tanggal jatuhnya hari besar
                                            tersebut.
                                        </li>
                                        <li class="mb-2">
                                            <strong>Tanggal Diterapkan:</strong> Jika libur digeser atau dialihkan ke hari
                                            kerja lain, sesuaikan tanggal di bagian ini.
                                        </li>
                                        <li>
                                            Sistem secara otomatis mendeteksi jika terjadi pergeseran tanggal operasional
                                            kerja.
                                        </li>
                                    </ul>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
        // Autofill tanggal diterapkan agar sama dengan tanggal aktual saat pertama kali dipilih
        const dateActual = document.getElementById('date_actual');
        const dateApplied = document.getElementById('date_applied');

        dateActual.addEventListener('change', function () {
            if (!dateApplied.value) {
                dateApplied.value = this.value;
            }
        });
    </script>

@endsection