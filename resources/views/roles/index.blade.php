@extends('layouts.app')
@section('title', 'List Peran')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->

        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-xxl">
                <!-- info -->
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
                <!-- Start Tabel -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Semua Peran</h4>
                                    <p class="text-muted mb-0">{{ $roles->total() }} peran yang ditemukan di sistem Anda
                                    </p>
                                </div>

                                <div>
                                    @can('role.create')
                                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                                            Tambah Peran
                                        </a>
                                    @endcan
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover table-centered">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Peran</th>
                                                <th width="15%">Guard</th>
                                                <th>Dibuat</th>
                                                <th>Diperbarui</th>
                                                <th width="15%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($roles as $role)
                                                <tr>
                                                    <td>{{ $roles->firstItem() + $loop->index }}</td>
                                                    <td>{{ $role->name }}</td>
                                                    <td>{{ $role->guard_name }}</td>
                                                    <td>
                                                        <div>
                                                            <span
                                                                class="text-dark fw-medium"></span>{{ $role->creator?->name ?? '-' }}</span><br>
                                                            <small
                                                                class="text-muted">{{ $role->created_at->format('d M Y H:i:s')}}</small>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <span
                                                                class="text-dark fw-medium"></span>{{ $role->updater?->name ?? '-' }}</span><br>
                                                            <small
                                                                class="text-muted">{{ $role->updated_at->format('d M Y H:i:s') }}</small>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        @can('role.edit')
                                                            <a href="{{ route('roles.edit', $role->id) }}"
                                                                class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Edit"><iconify-icon
                                                                    icon="solar:pen-2-broken"
                                                                    class="align-middle fs-18"></iconify-icon></a>
                                                        @endcan
                                                        @can('role.edit')
                                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                                class="d-inline delete-form">

                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="button" class="btn btn-soft-danger btn-sm btn-delete"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-title="Hapus">

                                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                        class="align-middle fs-18">
                                                                    </iconify-icon>
                                                                </button>
                                                            </form>
                                                        @endcan

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">
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
                                {{ $roles->links() }}
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
                    title: 'Hapus Peran?',
                    text: 'Data Peran akan dihapus permanen.',
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