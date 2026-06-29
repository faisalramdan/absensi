@extends('layouts.app')
@section('title', 'Mengubah Kontrak Karyawan')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            <!-- Start Container Fluid -->
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <iconify-icon icon="solar:pen-2-bold-duotone" class="me-2"></iconify-icon>
                                    Mengubah Kontrak Karyawan
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('employee-contracts.update', $employeeContract) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        
                                        <!-- Pilih Karyawan -->
                                        <div class="col-md-6 mb-3">
                                            <label for="employee_id" class="form-label fw-semibold">
                                                Karyawan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                                <option value="">-- Pilih Karyawan --</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ old('employee_id', $employeeContract->employee_id) == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->full_name }} ({{ $employee->nik ?? 'No NIK' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Pilih Status Kerja -->
                                        <div class="col-md-6 mb-3">
                                            <label for="employee_status_id" class="form-label fw-semibold">
                                                Status Kerja Karyawan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('employee_status_id') is-invalid @enderror" id="employee_status_id" name="employee_status_id" required>
                                                <option value="">-- Pilih Status --</option>
                                                @foreach($statuses as $status)
                                                    <option value="{{ $status->id }}" {{ old('employee_status_id', $employeeContract->employee_status_id) == $status->id ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_status_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Nomor Kontrak -->
                                        <div class="col-md-12 mb-3">
                                            <label for="contract_number" class="form-label fw-semibold">
                                                Nomor Kontrak
                                            </label>
                                            <input type="text" class="form-control @error('contract_number') is-invalid @enderror"
                                                id="contract_number" name="contract_number" value="{{ old('contract_number', $employeeContract->contract_number) }}"
                                                placeholder="Contoh: 001/SPK/HRD/2026" maxlength="100">
                                            @error('contract_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tanggal Mulai -->
                                        <div class="col-md-6 mb-3">
                                            <label for="start_date" class="form-label fw-semibold">
                                                Tanggal Mulai Kontrak <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date" value="{{ old('start_date', $employeeContract->start_date ? \Carbon\Carbon::parse($employeeContract->start_date)->format('Y-m-d') : '') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tanggal Selesai -->
                                        <div class="col-md-6 mb-3">
                                            <label for="end_date" class="form-label fw-semibold">
                                                Tanggal Selesai Kontrak <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                                id="end_date" name="end_date" value="{{ old('end_date', $employeeContract->end_date ? \Carbon\Carbon::parse($employeeContract->end_date)->format('Y-m-d') : '') }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Unggah File Berkas -->
                                        <div class="col-md-12 mb-3">
                                            <label for="file_contract" class="form-label fw-semibold">
                                                Dokumen Lampiran Kontrak (PDF)
                                            </label>
                                            <input type="file" class="form-control @error('file_contract') is-invalid @enderror"
                                                id="file_contract" name="file_contract" accept="application/pdf">
                                            @error('file_contract')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted d-block mb-2">Kosongkan jika tidak ingin mengubah berkas dokumen kontrak lama Anda.</small>
                                            
                                            <!-- Review Berkas Lama Jika Ada -->
                                            @if($employeeContract->file_contract)
                                                <div class="d-flex align-items-center p-2 bg-light rounded-2 border border-dashed">
                                                    <iconify-icon icon="solar:document-bold-duotone" class="text-danger fs-28 me-2"></iconify-icon>
                                                    <div>
                                                        <span class="text-dark fw-medium d-block small">Berkas Kontrak Saat Ini</span>
                                                        <a href="{{ asset('storage/' . $employeeContract->file_contract) }}" target="_blank" class="btn btn-link btn-sm p-0 text-primary text-decoration-none">
                                                            <iconify-icon icon="solar:eye-broken" class="align-middle me-1"></iconify-icon>Lihat PDF Aktif
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Catatan Tambahan -->
                                        <div class="col-md-12 mb-3">
                                            <label for="notes" class="form-label fw-semibold">Catatan Keterangan</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                                id="notes" name="notes" rows="3"
                                                placeholder="Tambahkan informasi opsional mengenai detail kesepakatan kontrak...">{{ old('notes', $employeeContract->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    <!-- Bagian Tombol Kontrol -->
                                    <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                            <div class="col-lg-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Perbarui
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
                </div>
            </div>
        </div>
    </div>
@endsection