@extends('layouts.app')
@section('title', 'List Jabatan')
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
                <!-- Advanced Filters -->
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter Jabatan
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('positions.index') }}">
                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Cari Jabatan
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone">
                                            </iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control" placeholder="Nama Jabatan ..."
                                            value="{{ request('search') }}">
                                    </div>

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

                                        <a href="{{ route('positions.index') }}" class="btn btn-secondary">
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
                                    <h4 class="card-title">Semua Daftar Jabatan</h4>
                                    <p class="text-muted mb-0">{{ $positions->total() }} Jabatan yang ditemukan di sistem
                                        Anda
                                    </p>
                                </div>

                                <div>
                                    @can('position.create')
                                        <a href="{{ route('positions.create') }}" class="btn btn-primary">
                                            <iconify-icon icon="solar:add-circle-bold"></iconify-icon>
                                            Tambah Jabatan
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
                                                <th width="20%">Nama Jabatan</th>
                                                <th width="25%">Deskripsi</th>
                                                <th width="15%">Dibuat</th>
                                                <th width="10%">Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($positions as $position)
                                                <tr>
                                                    <td>{{ $positions->firstItem() + $loop->index }}</td>
                                                    <td>{{ $position->code }}</td>
                                                    <td>{{ $position->name }}</td>
                                                    <td>{{ $position->description }}</td>
                                                    <td>
                                                        <div>
                                                            <span
                                                                class="text-dark fw-medium"></span>{{ $position->creator?->name ?? '-' }}</span><br>
                                                            <small
                                                                class="text-muted">{{ $position->created_at->format('d M Y H:i:s')}}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($position->is_active)
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
                                                            @can('position.edit')
                                                                <a href="{{ route('positions.edit', $position) }}"
                                                                    class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Edit"><iconify-icon
                                                                        icon="solar:pen-2-broken"
                                                                        class="align-middle fs-18"></iconify-icon></a>
                                                            @endcan
                                                            @can('position.delete')
                                                                <form action="{{ route('positions.destroy', $position) }}"
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
                                {{ $positions->links() }}
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
                    title: 'Hapus Jabatan?',
                    text: 'Data jabatan akan dihapus permanen.',
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