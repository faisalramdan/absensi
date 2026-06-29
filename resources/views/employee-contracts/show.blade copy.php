@extends('layouts.app')
@section('title', 'Detail Kontrak Karyawan')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('employee-contracts.index') }}" class="btn btn-soft-secondary d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:arrow-left-broken" class="fs-18"></iconify-icon> Kembali ke Daftar
                    </a>
                    @can('employee-contract.edit')
                        <a href="{{ route('employee-contracts.edit', $employeeContract) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:pen-2-broken" class="fs-18"></iconify-icon> Edit Kontrak
                        </a>
                    @endcan
                </div>

                <div class="row">
                    <div class="col-xl-5">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-light-subtle">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    <iconify-icon icon="solar:user-id-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                                    Informasi Karyawan
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">Nama Lengkap</td>
                                        <td width="5%">:</td>
                                        <td class="text-dark fw-medium">{{ $employeeContract->employee?->full_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NIK / ID Karyawan</td>
                                        <td>:</td>
                                        <td>{{ $employeeContract->employee?->nik ?? '-' }} <span class="text-muted small">(ID: {{ $employeeContract->employee_id }})</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Status Kerja</td>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info px-2 py-1 border border-info-subtle">
                                                {{ $employeeContract->employeeStatus?->name ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-light-subtle">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    <iconify-icon icon="solar:document-text-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                                    Rincian Kontrak Kerja
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">No. Kontrak</td>
                                        <td width="5%">:</td>
                                        <td class="font-monospace fw-medium text-secondary">{{ $employeeContract->contract_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Mulai Berlaku</td>
                                        <td>:</td>
                                        <td class="text-success fw-medium">
                                            {{ \Carbon\Carbon::parse($employeeContract->start_date)->format('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Selesai Kontrak</td>
                                        <td>:</td>
                                        <td class="text-danger fw-medium">
                                            {{ \Carbon\Carbon::parse($employeeContract->end_date)->format('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Status Kontrak</td>
                                        <td>:</td>
                                        <td>
                                            @if($employeeContract->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Non-Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted text-top">Keterangan/Catatan</td>
                                        <td class="text-top">:</td>
                                        <td>
                                            <p class="text-muted mb-0 small text-wrap" style="max-height: 100px; overflow-y: auto;">
                                                {{ $employeeContract->notes ?? 'Tidak ada catatan khusus.' }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-footer bg-light-subtle text-muted small py-3">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start gap-1">
                                            <iconify-icon icon="solar:user-hand-up-broken" class="text-secondary fs-16 mt-0.5"></iconify-icon>
                                            <div>
                                                <span class="d-block text-dark fw-medium">Dibuat Oleh:</span>
                                                <span>{{ $employeeContract->creator?->full_name ?? 'Sistem' }}</span>
                                                <small class="text-muted d-block">{{ $employeeContract->created_at ? $employeeContract->created_at->format('d M Y, H:i') : '-' }} WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 border-start-sm">
                                        <div class="d-flex align-items-start gap-1">
                                            <iconify-icon icon="solar:pen-new-square-broken" class="text-secondary fs-16 mt-0.5"></iconify-icon>
                                            <div>
                                                <span class="d-block text-dark fw-medium">Update Terakhir:</span>
                                                <span>{{ $employeeContract->updater?->full_name ?? 'Belum Diubah' }}</span>
                                                <small class="text-muted d-block">{{ $employeeContract->updated_at ? $employeeContract->updated_at->format('d M Y, H:i') : '-' }} WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="card shadow-sm border-0" style="min-height: 435px;">
                            <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    <iconify-icon icon="solar:file-check-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                                    Lampiran Berkas Digital
                                </h5>
                                @if($employeeContract->file_contract)
                                    <a href="{{ asset('storage/' . $employeeContract->file_contract) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:download-minimalistic-broken"></iconify-icon> Buka Tab Baru
                                    </a>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-0" style="background-color: #f8f9fa;">
                                @if($employeeContract->file_contract)
                                    <object data="{{ asset('storage/' . $employeeContract->file_contract) }}" type="application/pdf" width="100%" style="height: 500px;">
                                        <div class="p-4 text-center">
                                            <iconify-icon icon="solar:document-bold" class="text-danger fs-48 mb-2"></iconify-icon>
                                            <p class="text-muted mb-2">Browser Anda tidak mendukung pratinjau PDF instan.</p>
                                            <a href="{{ asset('storage/' . $employeeContract->file_contract) }}" target="_blank" class="btn btn-danger btn-sm">Unduh Dokumen Kontrak</a>
                                        </div>
                                    </object>
                                @else
                                    <div class="p-5 text-center text-muted">
                                        <iconify-icon icon="solar:document-cross-broken" class="fs-48 mb-2 text-black-50"></iconify-icon>
                                        <p class="mb-0 small fw-medium">Tidak ada salinan berkas PDF yang diunggah untuk kontrak kerja ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </div>
        </div>
    @endsection