@extends('layouts.app')

@section('title', 'List Status Karyawan')

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
                                    <h4 class="card-title">Semua Daftar Status Karyawan</h4>
                                    <p class="text-muted mb-0">{{ $statuses->total() }} Status karyawan yang ditemukan di
                                        sistem
                                        Anda
                                    </p>
                                </div>

                                <div>
                                    @can('employee-status.create')
                                        <a href="{{ route('employee-statuses.create') }}" class="btn btn-primary">
                                            + Tambah Status Karyawan
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
                                                <th width="10%">Kode</th>
                                                <th width="20%">Nama Status Karyawan</th>
                                                <th width="25%">Deskripsi</th>
                                                <th width="15%">Dibuat</th>
                                                <th width="10%">Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($statuses as $status)
                                                <tr>
                                                    <td>{{ $statuses->firstItem() + $loop->index }}</td>
                                                    <td>{{ $status->code }}</td>
                                                    <td>{{ $status->name }}</td>
                                                    <td>{{ $status->description }}</td>
                                                    <td>
                                                        <div>
                                                            <span
                                                                class="text-dark fw-medium"></span>{{ $status->creator?->name ?? '-' }}</span><br>
                                                            <small
                                                                class="text-muted">{{ $status->created_at->format('d M Y H:i:s')}}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($status->is_active)
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
                                                            @can('employee-status.edit')
                                                                <a href="{{ route('employee-statuses.edit', $status) }}"
                                                                    class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Edit"><iconify-icon
                                                                        icon="solar:pen-2-broken"
                                                                        class="align-middle fs-18"></iconify-icon></a>
                                                            @endcan
                                                            @can('employee-status.delete')
                                                                <form action="{{ route('employee-statuses.destroy', $status) }}"
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
                                                    <td colspan="7" class="text-center">
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
                                {{ $statuses->links() }}
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
                    title: 'Hapus Status Karyawan?',
                    text: 'Data Status Karyawan akan dihapus permanen.',
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