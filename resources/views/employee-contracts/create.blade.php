@extends('layouts.app')
@section('title', 'Membuat Kontrak Karyawan')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <iconify-icon icon="solar:user-plus-bold-duotone" class="me-2"></iconify-icon>
                                    Membuat Kontrak Karyawan
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employee-contracts.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="employee_id" class="form-label fw-semibold">
                                                Karyawan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                                <option value="">-- Pilih Karyawan --</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->full_name }} ({{ $employee->nik ?? 'No NIK' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="employee_status_id" class="form-label fw-semibold">
                                                Status Kerja Karyawan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('employee_status_id') is-invalid @enderror" id="employee_status_id" name="employee_status_id" required>
                                                <option value="">-- Pilih Status --</option>
                                                @foreach($statuses as $status)
                                                    <option value="{{ $status->id }}" {{ old('employee_status_id') == $status->id ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_status_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="contract_number" class="form-label fw-semibold">
                                                Nomor Kontrak
                                            </label>
                                            <input type="text" class="form-control @error('contract_number') is-invalid @enderror"
                                                id="contract_number" name="contract_number" value="{{ old('contract_number') }}"
                                                placeholder="Contoh: 001/SPK/HRD/2026" maxlength="100">
                                            @error('contract_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="start_date" class="form-label fw-semibold">
                                                Tanggal Mulai Kontrak <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="end_date" class="form-label fw-semibold">
                                                Tanggal Selesai Kontrak <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                                id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="file_contract" class="form-label fw-semibold">
                                                Dokumen Lampiran Kontrak (PDF)
                                            </label>
                                            <input type="file" class="form-control @error('file_contract') is-invalid @enderror"
                                                id="file_contract" name="file_contract" accept="application/pdf">
                                            @error('file_contract')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Ekstensi yang diperbolehkan hanya berkas .pdf dengan ukuran maksimal 5 MB</small>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="notes" class="form-label fw-semibold">Catatan Keterangan</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                                id="notes" name="notes" rows="3"
                                                placeholder="Tambahkan informasi opsional mengenai detail kesepakatan kontrak...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Simpan
                                                </button>
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="{{ route('employee-contracts.index') }}"
                                                    class="btn btn-outline-secondary w-100">
                                                    Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light-subtle d-flex align-items-center">
                                <iconify-icon icon="solar:info-circle-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                                <h4 class="card-title mb-0">Aturan & Informasi Kontrak</h4>
                            </div>

                            <div class="card-body fs-13 text-muted">
                                <div class="d-flex align-items-start gap-2 mb-3">
                                    <iconify-icon icon="solar:document-text-broken" class="text-primary fs-18 mt-0.5 flex-shrink-0"></iconify-icon>
                                    <div>
                                        <strong class="text-dark d-block mb-0.5">Nomor Kontrak Kerja</strong>
                                        Pastikan nomor kontrak yang diinput bersifat unik dan belum pernah terdaftar sebelumnya di dalam sistem untuk menghindari duplikasi legalitas.
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-2 mb-3">
                                    <iconify-icon icon="solar:calendar-date-broken" class="text-warning fs-18 mt-0.5 flex-shrink-0"></iconify-icon>
                                    <div>
                                        <strong class="text-dark d-block mb-0.5">Ketentuan Rentang Tanggal</strong>
                                        Tanggal mulai (<span class="text-monospace">start_date</span>) kontrak baru wajib lebih besar (setelah) tanggal selesai kontrak terakhir milik karyawan yang bersangkutan. Sistem memblokir tanggal yang tumpang tindih.
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-2">
                                    <iconify-icon icon="solar:shield-check-broken" class="text-success fs-18 mt-0.5 flex-shrink-0"></iconify-icon>
                                    <div>
                                        <strong class="text-dark d-block mb-0.5">Sistem Satu Kontrak Aktif</strong>
                                        Setiap karyawan hanya diizinkan memiliki <strong>1 kontrak aktif</strong>. Saat kontrak baru berhasil disimpan, sistem secara otomatis mengubah status kontrak lama menjadi <span class="badge bg-danger-subtle text-danger p-0 px-1">Tidak Aktif</span>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection