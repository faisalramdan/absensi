@extends('layouts.app')
@section('title', 'Detail Kontrak Karyawan')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                {{-- Alert Flash Message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('employee-contracts.index') }}"
                        class="btn btn-soft-secondary d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:arrow-left-broken" class="fs-18"></iconify-icon> Kembali ke Daftar
                    </a>
                    @can('employee-contract.edit')
                        <a href="{{ route('employee-contracts.edit', $employeeContract) }}"
                            class="btn btn-primary d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:pen-2-broken" class="fs-18"></iconify-icon> Edit Kontrak
                        </a>
                    @endcan
                </div>

                <div class="row">
                    <div class="col-xl-5">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-light-subtle">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    <iconify-icon icon="solar:user-id-bold-duotone"
                                        class="text-primary me-2 fs-20"></iconify-icon>
                                    Informasi Karyawan
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">Nama Lengkap</td>
                                        <td width="5%">:</td>
                                        <td class="text-dark fw-medium">{{ $employeeContract->employee?->full_name ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NIK / ID Karyawan</td>
                                        <td>:</td>
                                        <td>{{ $employeeContract->employee?->nik ?? '-' }} <span
                                                class="text-muted small">(ID: {{ $employeeContract->employee_id }})</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Status Kerja</td>
                                        <td>:</td>
                                        <td>
                                            <span
                                                class="badge bg-info-subtle text-info px-2 py-1 border border-info-subtle">
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
                                    <iconify-icon icon="solar:document-text-bold-duotone"
                                        class="text-primary me-2 fs-20"></iconify-icon>
                                    Rincian Kontrak Kerja
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="35%" class="fw-semibold text-muted">No. Kontrak</td>
                                        <td width="5%">:</td>
                                        <td class="font-monospace fw-medium text-secondary">
                                            {{ $employeeContract->contract_number ?? '-' }}
                                        </td>
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
                                            <p class="text-muted mb-0 small text-wrap"
                                                style="max-height: 100px; overflow-y: auto;">
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
                                            <iconify-icon icon="solar:user-hand-up-broken"
                                                class="text-secondary fs-16 mt-0.5"></iconify-icon>
                                            <div>
                                                <span class="d-block text-dark fw-medium">Dibuat Oleh:</span>
                                                <span>{{ $employeeContract->creator?->full_name ?? 'Sistem' }}</span>
                                                <small
                                                    class="text-muted d-block">{{ $employeeContract->created_at ? $employeeContract->created_at->format('d M Y, H:i') : '-' }}
                                                    WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 border-start-sm">
                                        <div class="d-flex align-items-start gap-1">
                                            <iconify-icon icon="solar:pen-new-square-broken"
                                                class="text-secondary fs-16 mt-0.5"></iconify-icon>
                                            <div>
                                                <span class="d-block text-dark fw-medium">Update Terakhir:</span>
                                                <span>{{ $employeeContract->updater?->full_name ?? 'Belum Diubah' }}</span>
                                                <small
                                                    class="text-muted d-block">{{ $employeeContract->updated_at ? $employeeContract->updated_at->format('d M Y, H:i') : '-' }}
                                                    WIB</small>
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
                                    <iconify-icon icon="solar:file-check-bold-duotone"
                                        class="text-primary me-2 fs-20"></iconify-icon>
                                    Lampiran Berkas Digital
                                </h5>
                                @if($employeeContract->file_contract)
                                    <a href="{{ asset('storage/' . $employeeContract->file_contract) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:download-minimalistic-broken"></iconify-icon> Buka Tab Baru
                                    </a>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-0"
                                style="background-color: #f8f9fa;">
                                @if($employeeContract->file_contract)
                                    <object data="{{ asset('storage/' . $employeeContract->file_contract) }}"
                                        type="application/pdf" width="100%" style="height: 500px;">
                                        <div class="p-4 text-center">
                                            <iconify-icon icon="solar:document-bold"
                                                class="text-danger fs-48 mb-2"></iconify-icon>
                                            <p class="text-muted mb-2">Browser Anda tidak mendukung pratinjau PDF instan.</p>
                                            <a href="{{ asset('storage/' . $employeeContract->file_contract) }}" target="_blank"
                                                class="btn btn-danger btn-sm">Unduh Dokumen Kontrak</a>
                                        </div>
                                    </object>
                                @else
                                    <div class="p-5 text-center text-muted">
                                        <iconify-icon icon="solar:document-cross-broken"
                                            class="fs-48 mb-2 text-black-50"></iconify-icon>
                                        <p class="mb-0 small fw-medium">Tidak ada salinan berkas PDF yang diunggah untuk kontrak
                                            kerja ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- --- SEKSI MANAJEMEN ALOKASI CUTI --- --}}
                <div class="row mt-4" id="leave-allocation-section">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    <iconify-icon icon="solar:calendar-add-bold-duotone"
                                        class="text-primary me-2 fs-20"></iconify-icon>
                                    Alokasi Jatahan Cuti/Izin Karyawan
                                </h5>
                                <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#addLeaveAllocationModal">
                                    + Alokasikan Cuti
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Tipe Cuti/Izin</th>

                                                <th>Kuota (Hari)</th>
                                                <th>Pemakaian (Hari)</th>
                                                <th>Sisa (Hari)</th>
                                                <th>Catatan</th>
                                                <th width="10%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($leaveAllocations ?? [] as $allocation)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span
                                                            class="fw-semibold text-dark">{{ $allocation->leaveType?->name ?? 'N/A' }}</span>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-secondary px-2 py-1 fs-12">
                                                            {{ floatval($allocation->allocated_days) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-orange px-2 py-1 fs-12">
                                                            {{ floatval($allocation->used_days) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success px-2 py-1 fs-12">
                                                            {{ floatval($allocation->remaining_days) }}
                                                        </span>
                                                    </td>
                                                    <td><small class="text-muted">{{ $allocation->notes ?? '-' }}</small></td>
                                                    <td class="text-center">
                                                        <form action="{{ route('leave-allocations.destroy', $allocation->id) }}"
                                                            method="POST" class="d-inline delete-allocation-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-soft-danger btn-sm btn-delete-allocation"
                                                                data-bs-toggle="tooltip" title="Hapus Alokasi">
                                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                    class="fs-16 align-middle"></iconify-icon>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">Belum ada alokasi jatah
                                                        cuti/izin khusus untuk masa kontrak ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- --- AKHIR SEKSI --- --}}

            </div>
        </div>
    </div>

    {{-- --- BOOTSTRAP MODAL: ALOKASI MASSAL JATAH CUTI --- --}}
    <div class="modal fade" id="addLeaveAllocationModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addLeaveAllocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- Ukuran diperbesar ke modal-lg agar muat tabel --}}
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="addLeaveAllocationModalLabel">
                        <iconify-icon icon="solar:calendar-add-bold-duotone"
                            class="text-primary me-1 align-middle"></iconify-icon> Atur Kuota Cuti Kontrak
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('leave-allocations.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="employee_contract_id" value="{{ $employeeContract->id }}">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted small mb-1">Nama Lengkap Karyawan</label>
                                <input type="text" class="form-control bg-light fw-medium text-dark"
                                    value="{{ $employeeContract->employee?->full_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted small mb-1">NIK / ID Karyawan</label>
                                <input type="text" class="form-control bg-light text-secondary font-monospace"
                                    value="{{ $employeeContract->employee?->nik ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted small mb-1">Mulai Berlaku Kontrak</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-success">
                                        <iconify-icon icon="solar:calendar-date-broken"></iconify-icon>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0 fw-medium text-success"
                                        value="{{ \Carbon\Carbon::parse($employeeContract->start_date)->format('d F Y') }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-muted small mb-1">Selesai Kontrak</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-danger">
                                        <iconify-icon icon="solar:calendar-date-broken"></iconify-icon>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0 fw-medium text-danger"
                                        value="{{ \Carbon\Carbon::parse($employeeContract->end_date)->format('d F Y') }}"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">Pilih</th>
                                        <th>Nama Cuti/Izin</th>
                                        <th width="25%">Kuota (Hari)</th>
                                        <th>Catatan Spesifik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaveTypes ?? [] as $index => $type)
                                        <tr>
                                            <td class="text-center">
                                                {{-- Checkbox untuk menentukan jenis cuti apa saja yang mau disimpan --}}
                                                <input type="checkbox" name="allocations[{{ $index }}][selected]" value="1"
                                                    class="form-check-input allocation-checkbox" checked>
                                                <input type="hidden" name="allocations[{{ $index }}][leave_type_id]"
                                                    value="{{ $type->id }}">
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $type->name }}</span>
                                                <small class="text-muted d-block">Default: {{ $type->quota ?? 0 }} Hari</small>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    {{-- Nilai otomatis mengambil dari field quota milik LeaveType --}}
                                                    <input type="number" name="allocations[{{ $index }}][allocated_days]"
                                                        class="form-control" value="{{ $type->quota ?? 0 }}" min="0" required>
                                                    <span class="input-group-text">Hari</span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="allocations[{{ $index }}][notes]"
                                                    class="form-control form-control-sm" placeholder="Opsional...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light-subtle">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Semua Kuota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script Validasi SweetAlert untuk Hapus Item Alokasi Cuti --}}
    <script>
        document.querySelectorAll('.btn-delete-allocation').forEach(button => {
            button.addEventListener('click', function () {
                let form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Alokasi Cuti?',
                    text: 'Kuota cuti untuk tipe ini akan dihapus dari data kontrak.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection