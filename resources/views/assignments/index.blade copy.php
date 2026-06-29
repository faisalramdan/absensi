@extends('layouts.app')
@section('title', 'Penjadwalan Karyawan')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:danger-bold" class="me-1"></iconify-icon>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 Mentions-fs-20"></iconify-icon>
                        <h5 class="mb-0 fw-semibold">Filter Jadwal Shift</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('assignments.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nama Karyawan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan..." value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Pilih Shift</label>
                                    <select name="shift_id" class="form-select">
                                        <option value="">-- Semua Shift --</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tanggal Spesifik</label>
                                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                        <a href="{{ route('assignments.index') }}" class="btn btn-secondary text-nowrap">Reset</a>
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
                                    <h4 class="card-title">Daftar Jadwal Shift Karyawan</h4>
                                    <p class="text-muted mb-0">Ada {{ $assignments->total() }} riwayat penugasan shift yang ditemukan.</p>
                                </div>
                                <div>
                                    @can('shift-assignment.create')
                                        <a href="{{ route('assignments.create') }}" class="btn btn-primary">
                                            + Atur Jadwal Massal
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 text-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="ps-4" width="60">No</th>
                                                <th>Nama Karyawan</th>
                                                <th>Tanggal Penugasan</th>
                                                <th>Nama Shift</th>
                                                <th>Catatan / Keterangan</th>
                                                <th>Dibuat Oleh</th>
                                                <th width="100" class="pe-4 text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($assignments as $assignment)
                                                <tr>
                                                    <td class="ps-4">
                                                        {{ $assignments->firstItem() + $loop->index }}
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold text-dark">
                                                            {{ $assignment->employee?->full_name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-medium">
                                                            {{ $assignment->date->translatedFormat('d F Y') }}
                                                        </div>
                                                        <small class="text-muted fs-11">
                                                            {{ $assignment->date->translatedFormat('l') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold">
                                                            {{ $assignment->shift?->name ?? 'Deleted Shift' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted text-wrap d-block text-truncate" style="max-width: 200px;">
                                                            {{ $assignment->notes ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="small fw-semibold text-secondary">
                                                            {{ $assignment->creator?->full_name ?? 'System' }}
                                                        </div>
                                                        <small class="text-muted fs-11">
                                                            {{ $assignment->created_at->format('d M Y, H:i') }}
                                                        </small>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                                Aksi
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                @can('shift-assignment.delete')
                                                                    <li>
                                                                        <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="delete-form">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="dropdown-item text-danger btn-delete">
                                                                                Hapus Jadwal
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        Data jadwal karyawan tidak ditemukan.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top">
                                {{ $assignments->links() }}
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
                    title: 'Hapus Jadwal?',
                    text: 'Karyawan terkait tidak akan memiliki ketentuan shift pada tanggal tersebut.',
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