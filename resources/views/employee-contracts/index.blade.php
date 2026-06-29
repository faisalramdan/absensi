@extends('layouts.app')
@section('title', 'Daftar Kontrak Karyawan')
@section('content')
    <div class="wrapper">
        <div class="page-content">

            <div class="container-xxl">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="container-fluid mt-3">
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}

                            <button type="button" class="btn-close" data-bs-dismiss="alert">
                            </button>
                        </div>
                    </div>
                @endif
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter Kontrak
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('employee-contracts.index') }}">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Cari Nama, NIK atau No. Kontrak
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Nama Karyawan, NIK atau No. Kontrak..."
                                            value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Status Karyawan
                                    </label>
                                    <select name="employee_status_id" class="form-select">
                                        <option value="">Semua Status</option>
                                        @foreach($statusId as $status)
                                            <option value="{{ $status->id }}" {{ request('employee_status_id') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label d-block">
                                        &nbsp;
                                    </label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            Filter
                                        </button>
                                        <a href="{{ route('employee-contracts.index') }}" class="btn btn-secondary">
                                            Reset
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>

                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Semua Daftar Kontrak Karyawan</h4>
                                    <p class="text-muted mb-0">{{ $contracts->total() }} Kontrak yang ditemukan di sistem
                                        Anda
                                    </p>
                                </div>

                                <div>
                                    @can('employee-contract.create')
                                        <a href="{{ route('employee-contracts.create') }}" class="btn btn-primary">
                                            + Tambah Kontrak
                                        </a>
                                    @endcan
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover table-centered">
                                        <thead class="bg-light-subtle">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="20%">Karyawan</th>
                                                <th width="15%">No. Kontrak</th>
                                                <th width="15%">Status Kerja</th>
                                                <th width="15%">Masa Kerja</th>
                                                <th width="15%">Berkas</th>
                                                <th width="15%">Status Kontrak</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($contracts as $contract)
                                                <tr>
                                                    <td>{{ $contracts->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <span
                                                            class="text-dark fw-semibold d-block">{{ $contract->employee?->full_name }}</span>
                                                        <small class="text-muted">ID: {{ $contract->employee?->nik }}</small>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-secondary font-monospace">{{ $contract->contract_number ?? '-' }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info-subtle text-info px-2 py-1 border border-info-subtle">
                                                            {{ $contract->employeeStatus?->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <small class="text-dark fw-medium d-block">
                                                                {{ \Carbon\Carbon::parse($contract->start_date)->format('d M Y') }}
                                                            </small>
                                                            <small class="text-muted">s/d</small>
                                                            <small class="text-danger fw-medium d-block">
                                                                {{ \Carbon\Carbon::parse($contract->end_date)->format('d M Y') }}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($contract->file_contract)
                                                            <a href="{{ asset('storage/' . $contract->file_contract) }}"
                                                                target="_blank"
                                                                class="btn btn-soft-primary btn-sm d-inline-flex align-items-center gap-1">
                                                                <iconify-icon icon="solar:document-bold-duotone"
                                                                    class="fs-16"></iconify-icon>
                                                                Lihat PDF
                                                            </a>
                                                        @else
                                                            <span class="text-muted small">Tidak ada file</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($contract->is_active)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                                Aktif
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                                Tidak Aktif
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            @can('employee-contract.show')
                                                                <a href="{{ route('employee-contracts.show', $contract) }}"
                                                                    class="btn btn-soft-info btn-sm" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Detail Kontrak">
                                                                    <iconify-icon icon="solar:eye-broken"
                                                                        class="align-middle fs-18"></iconify-icon>
                                                                </a>
                                                            @endcan

                                                            @can('employee-contract.edit')
                                                                <a href="{{ route('employee-contracts.edit', $contract) }}"
                                                                    class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Edit">
                                                                    <iconify-icon icon="solar:pen-2-broken"
                                                                        class="align-middle fs-18"></iconify-icon>
                                                                </a>
                                                            @endcan

                                                            @can('employee-contract.delete')
                                                                <form action="{{ route('employee-contracts.destroy', $contract) }}"
                                                                    method="POST" class="d-inline" delete-form>
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="button"
                                                                        class="btn btn-soft-danger btn-sm btn-delete"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        data-bs-title="Hapus Permanent">
                                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                            class="align-middle fs-18">
                                                                        </iconify-icon>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        No Data
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end">
                                {{ $contracts->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                let form = this.closest('form');

                Swal.fire({
                    title: 'Hapus Kontrak?',
                    text: 'Data kontrak karyawan ini akan dihapus permanen.',
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