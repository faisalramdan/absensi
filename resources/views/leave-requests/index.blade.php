@extends('layouts.app')

@section('title', 'List Pengajuan Cuti / Izin')

@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <!-- info/alert -->
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
                <!-- endinfo -->

                <!-- Advanced Filters -->
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('leave-requests.index') }}">
                            <div class="row g-3">

                                {{-- Jenis Cuti/Izin --}}
                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Jenis Cuti/Izin
                                    </label>

                                    <select name="type" class="form-select">
                                        <option value="">
                                            Semua Jenis
                                        </option>
                                        @foreach($leaveTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Status
                                    </label>

                                    <select name="status" class="form-select">

                                        <option value="">
                                            Semua Status
                                        </option>

                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                            Approved
                                        </option>

                                        <option value="reject" {{ request('status') === 'reject' ? 'selected' : '' }}>
                                            Reject
                                        </option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>

                                    </select>

                                </div>

                                {{-- Button --}}
                                <div class="col-md-2">

                                    <label class="form-label d-block">
                                        &nbsp;
                                    </label>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            Filter
                                        </button>

                                        <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
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
                                    <h4 class="card-title">
                                        Riwayat Cuti & Izin
                                    </h4>
                                    <p class="text-muted mb-0">{{ $leaveRequests->total() }} data cuti/izin ditemukan di
                                        akun Anda.
                                    </p>
                                </div>
                                <div>
                                    @can('leave-request.create')
                                        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                                            + Ajukan
                                        </a>
                                    @endcan
                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table class="table table-hover align-middle">

                                        <thead>
                                            <th>No</th>
                                            <th>Tanggal Buat</th>
                                            <th>Jenis Cuti / Izin</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Total Hari</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </thead>

                                        <tbody>
                                            @forelse($leaveRequests as $leave)

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y H:i:s') }}
                                                    </td>
                                                    <td class="text-primary fw-semibold">{{ $leave->leaveType?->name }}</td>

                                                    <td>
                                                        {{ \Carbon\Carbon::parse($leave->start_date)->locale('id')->translatedFormat('l') }}
                                                        <br>
                                                        {{ \Carbon\Carbon::parse($leave->start_date)->locale('id')->translatedFormat('d F Y') }}
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($leave->end_date)->locale('id')->translatedFormat('l') }}
                                                        <br>
                                                        {{ \Carbon\Carbon::parse($leave->end_date)->locale('id')->translatedFormat('d F Y') }}
                                                    </td>
                                                    <td>{{ $leave->total_days }} Hari</td>
                                                    <td>
                                                        @if($leave->status == 'pending')
                                                            <span class="badge bg-warning">
                                                                Pending
                                                            </span>
                                                        @elseif($leave->status == 'approved')
                                                            <span class="badge bg-success">
                                                                Approved
                                                            </span>
                                                        @elseif($leave->status == 'rejected')
                                                            <span class="badge bg-danger">
                                                                Rejected
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>

                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-secondary dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                Aksi
                                                            </button>

                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('leave-requests.show', $leave) }}">
                                                                        Detail
                                                                    </a>
                                                                </li>

                                                                @if($leave->status == 'pending')
                                                                    @can('leave-request.delete')
                                                                        <li>
                                                                            <form action="{{ route('leave-requests.destroy', $leave) }}"
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
                                                                @endif
                                                            </ul>
                                                        </div>


                                                    </td>


                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        Tidak ada data
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>
                                {{ $leaveRequests->links() }}
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
                    title: 'Hapus Pengajuan Cuti & Izin anda ?',
                    text: 'Pengajuan akan dihapus permanen.',
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