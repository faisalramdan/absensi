@extends('layouts.app')
@section('title', 'Master Shift & Jam Kerja')
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
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20"></iconify-icon>
                        <h5 class="mb-0 fw-semibold">Filter Shift</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('shifts.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cari Nama Shift</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control" placeholder="Nama Shift..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Reset</a>
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
                                    <h4 class="card-title">Data Master Shift</h4>
                                    <p class="text-muted mb-0">Kelola master jam kerja operasional harian karyawan.</p>
                                </div>
                                <div>
                                    @can('shift.create')
                                        <a href="{{ route('shifts.create') }}" class="btn btn-primary">
                                            + Tambah Shift Baru
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
                                                <th>Nama Shift</th>
                                                <th>Deskripsi</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Detail Jam Kerja Harian</th>
                                                <th width="100" class="pe-4 text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($shifts as $index => $shift)
                                                <tr>
                                                    <td class="ps-4">{{ $index + 1 }}</td>
                                                    <td><span class="fw-semibold text-dark">{{ $shift->name }}</span></td>
                                                    <td>
                                                        <span class="text-muted text-wrap d-block text-truncate" style="max-width: 250px;">
                                                            {{ $shift->description ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="small fw-semibold text-secondary">{{ $shift->creator?->full_name ?? 'System' }}</div>
                                                        <small class="text-muted fs-11">{{ $shift->created_at->format('d M Y, H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-light text-primary border d-inline-flex align-items-center fw-medium" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#detail-shift-{{ $shift->id }}">
                                                            <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-1 fs-14"></iconify-icon>
                                                            Lihat Jam Kerja
                                                        </button>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                                Aksi
                                                            </button>
                                                            <ul class="dropdown-menuDropdown dropdown-menu dropdown-menu-end">
                                                                @can('shift.edit')
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('shifts.edit', $shift) }}">
                                                                            Edit
                                                                        </a>
                                                                    </li>
                                                                @endcan
                                                                @can('shift.delete')
                                                                    <li>
                                                                        <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="delete-form">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="dropdown-item text-danger btn-delete">
                                                                                Hapus
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr class="collapse bg-light-subtle" id="detail-shift-{{ $shift->id }}">
                                                    <td colspan="6" class="p-3 bg-light">
                                                        <div class="card card-body border-0 shadow-sm mx-3 my-1">
                                                            <div class="row g-2">
                                                                @php
                                                                    $dayLabels = [
                                                                        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                                                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                                                                    ];
                                                                    $details = $shift->details->keyBy('day_name');
                                                                @endphp
                                                                @foreach($dayLabels as $engDay => $indoDay)
                                                                    @php $dayData = $details->get($engDay); @endphp
                                                                    <div class="col-md-3 col-sm-6">
                                                                        <div class="p-2 border rounded bg-white">
                                                                            <div class="fw-bold text-dark small mb-1">{{ $indoDay }}</div>
                                                                            @if($dayData && !$dayData->is_off)
                                                                                <div class="fs-12 text-muted">Jam: <strong>{{ \Carbon\Carbon::parse($dayData->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($dayData->end_time)->format('H:i') }}</strong></div>
                                                                                <div class="fs-11 text-danger">Batas Telat: {{ \Carbon\Carbon::parse($dayData->late_deadline)->format('H:i') }}</div>
                                                                            @else
                                                                                <span class="badge bg-danger-subtle text-danger fs-11">OFF (Libur)</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">Data tidak ditemukan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
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
                    title: 'Hapus Master Shift?',
                    text: 'Seluruh konfigurasi detail jam kerja pada shift ini akan dihapus permanen.',
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