@extends('layouts.app')
@section('title', 'List Pengguna')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->

        <div class="page-content">

            <!-- Start Container Fluid -->
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
                            Filter Pengguna
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('users.index') }}">

                            <div class="row g-3">

                                {{-- Search --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Cari User
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone">
                                            </iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Nama atau email..." value="{{ request('search') }}">
                                    </div>
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

                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                            Aktif
                                        </option>

                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                            Tidak Aktif
                                        </option>

                                    </select>

                                </div>

                                {{-- Role --}}
                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Peran
                                    </label>

                                    <select name="role" class="form-select">

                                        <option value="">
                                            Semua Peran
                                        </option>

                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}
                                            </option>
                                        @endforeach

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

                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            Reset
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>
                <!-- Start Tabel -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Semua Daftar Pengguna</h4>
                                    <p class="text-muted mb-0">{{ $users->total() }} pengguna yang ditemukan di sistem Anda
                                    </p>
                                </div>

                                <div>
                                    @can('user.create')
                                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                                            Tambah Pengguna
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
                                                <th width="20%">Nama</th>
                                                <th width="20%">Email</th>
                                                <th width="15%">Peran</th>
                                                <th width="15%">Dibuat</th>

                                                <th width="10%">Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($users as $user)
                                                <tr>
                                                    <td>{{ $users->firstItem() + $loop->index }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @foreach($user->roles as $role)
                                                            <span class="badge bg-info">
                                                                {{ $role->name }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <span
                                                                class="text-dark fw-medium"></span>{{ $user->creator?->name ?? '-' }}</span><br>
                                                            <small
                                                                class="text-muted">{{ $user->created_at->format('d M Y H:i:s') }}</small>

                                                        </div>
                                                    </td>

                                                    <td>
                                                        @if($user->is_active)
                                                            <span class="badge bg-success">
                                                                Aktif
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                Non Aktif
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            @can('user.edit')

                                                                @if($user->employee)
                                                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        data-bs-title="Kelola melalui Master Karyawan, karena pengguna ini sudah terhubung dengan data karyawan.">

                                                                        <button type="button" class="btn btn-soft-secondary btn-sm"
                                                                            disabled>

                                                                            <iconify-icon icon="solar:pen-2-broken"
                                                                                class="align-middle fs-18">
                                                                            </iconify-icon>

                                                                        </button>
                                                                    </span>

                                                                @else

                                                                    <a href="{{ route('users.edit', $user) }}"
                                                                        class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-title="Edit">

                                                                        <iconify-icon icon="solar:pen-2-broken"
                                                                            class="align-middle fs-18">
                                                                        </iconify-icon>

                                                                    </a>

                                                                @endif

                                                            @endcan
                                                            @can('user.delete')

                                                                @if($user->employee)

                                                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        data-bs-title="Kelola melalui Master Karyawan, karena pengguna ini sudah terhubung dengan data karyawan.">

                                                                        <button type="button" class="btn btn-soft-secondary btn-sm"
                                                                            disabled>

                                                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                                class="align-middle fs-18">
                                                                            </iconify-icon>

                                                                        </button>

                                                                    </span>

                                                                @else

                                                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                                        class="d-inline" delete-form>

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

                                                                @endif

                                                            @endcan

                                                        </div>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        No Data
                                                    </td>
                                                </tr>
                                            @endforelse


                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table-responsive -->
                            </div>

                            <!-- Start pagination -->
                            <div class="card-footer d-flex justify-content-end">
                                {{ $users->links() }}
                            </div>
                            <!-- End pagination -->

                        </div>
                    </div>

                </div>
                <!-- End Tabel -->
            </div>
            <!-- End Container Fluid -->
        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->
    </div>
    <!-- END Wrapper -->

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {

            button.addEventListener('click', function () {

                let form = this.closest('form');

                Swal.fire({
                    title: 'Hapus User?',
                    text: 'Data user akan dihapus permanen.',
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