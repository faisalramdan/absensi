@extends('layouts.app')
@section('title', 'Master Hari Libur')
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
                {{-- FILTER --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>
                        <h5 class="mb-0 fw-semibold">Filter Data</h5>
                    </div>

                    <div class="card-body">
                        <form method="GET">
                            <div class="row align-items-end">
                                {{-- Search --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cari Hari Libur</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Masukkan nama hari libur..." value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-md-4 mt-3 mt-md-0">
                                    <button type="submit" class="btn btn-primary">
                                        Filter
                                    </button>
                                    <a href="{{ route('holidays.index') }}" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- TABLE --}}
                <div class="card border-0 shadow-sm">

                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Semua Daftar Hari Libur</h4>
                            <p class="text-muted mb-0">{{ $holidays->count() }} data master hari libur terdaftar</p>
                        </div>

                        <div>
                            @can('holiday.create')
                                <a href="{{ route('holidays.create') }}" class="btn btn-primary">
                                    + Tambah Hari Libur
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Hari Libur</th>
                                        <th>Tanggal Kalender Asli</th>
                                        <th>Tanggal Diterapkan (Libur)</th>
                                        <th>Catatan / Alasan</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($holidays as $index => $holiday)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold text-dark">{{ $holiday->name }}</td>
                                            <td>
                                                <span class="badge bg-light text-secondary border">
                                                    {{ \Carbon\Carbon::parse($holiday->date_actual)->translatedFormat('D, d M Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1 fs-13">
                                                    {{ \Carbon\Carbon::parse($holiday->date_applied)->translatedFormat('D, d M Y') }}
                                                </span>
                                                @if($holiday->date_actual->format('Y-m-d') !== $holiday->date_applied->format('Y-m-d'))
                                                    <small class="text-warning d-block mt-1 fw-medium">
                                                        <iconify-icon icon="solar:transfer-horizontal-bold-duotone"
                                                            class="vertical-middle"></iconify-icon>
                                                        Mengalami Pergeseran
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted fs-13">{{ $holiday->notes ?? '-' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @can('holiday.edit')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('holidays.edit', $holiday->id) }}">
                                                                    Edit
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('holiday.view')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('holidays.show', $holiday->id) }}">
                                                                    Detail
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('holiday.delete')
                                                            <li>
                                                                <form action="{{ route('holidays.destroy', $holiday->id) }}"
                                                                    method="POST" class="delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button"
                                                                        class="dropdown-item text-danger btn-delete">
                                                                        Hapus
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
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Tidak ada data hari libur yang ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Opsional jika di Controller menggunakan paginate(), aktifkan bagian ini --}}
                    {{-- <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Menampilkan {{ $holidays->count() }} data</span>
                            {{ $holidays->links() }}
                        </div>
                    </div> --}}

                </div>

            </div>

        </div>

    </div>

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                let form = this.closest('form');

                Swal.fire({
                    title: 'Hapus Hari Libur?',
                    text: 'Data hari libur nasional/pergeseran ini akan dihapus permanen dari sistem.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection