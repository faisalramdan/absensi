@extends('layouts.app')
@section('title', 'List Tim')
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
                            Filter Tim
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET" action="{{ route('teams.index') }}">
                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Cari Nama Tim
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone">
                                            </iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control" placeholder="Nama Tim..."
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

                                        <a href="{{ route('teams.index') }}" class="btn btn-secondary">
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
                                        Data Tim
                                    </h4>
                                    <p class="text-muted mb-0"> Ada {{ $teams->total() }} Tim yang ditemukan dalam
                                        sistem Anda.
                                    </p>
                                </div>
                                <div>
                                    @can('team.create')
                                        <a href="{{ route('teams.create') }}" class="btn btn-primary">
                                            + Tambah Tim
                                        </a>
                                    @endcan
                                </div>

                            </div>

                            <div class="card-body">

                                <div class="table-responsive">

                                    <table class="table table-hover align-middle">

                                        <thead>

                                            <tr>
                                                <th>No</th>
                                                <th>Nama Tim</th>
                                                <th>Perusahaan</th>
                                                <th>Parent</th>
                                                <th>Leader</th>
                                                <th>Member</th>
                                                <th>Status</th>
                                                <th width="100">Aksi</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            @forelse($teams as $team)

                                                <tr>

                                                    <td>
                                                        {{ $teams->firstItem() + $loop->index }}
                                                    </td>

                                                    <td>{{ $team->name }}
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $team->description }}
                                                        </small>
                                                    </td>
                                                    <td>{{ $team->company?->name }}</td>

                                                    <td>{{ $team->parent?->name ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            {{ $team->members()->where('member_role', 'Leader')->count() }}
                                                            Leader
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ $team->members()->count() }}
                                                            Member
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($team->is_active)
                                                            <span class="badge bg-success">Aktif</span>
                                                        @else
                                                            <span class="badge bg-danger">Nonaktif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle"
                                                                data-bs-toggle="dropdown">

                                                                Aksi

                                                            </button>

                                                            <ul class="dropdown-menu">

                                                                @can('team.view')
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('teams.show', $team) }}">
                                                                            Detail
                                                                        </a>
                                                                    </li>
                                                                @endcan

                                                                @can('team.edit')
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('teams.edit', $team) }}">
                                                                            Edit
                                                                        </a>
                                                                    </li>
                                                                @endcan

                                                                @can('team.delete')
                                                                    <li>
                                                                        <form action="{{ route('teams.destroy', $team) }}"
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

                                                    <td colspan="8" class="text-center">

                                                        Data tidak ditemukan

                                                    </td>

                                                </tr>

                                            @endforelse

                                        </tbody>

                                    </table>

                                </div>

                                {{ $teams->links() }}

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
                    title: 'Hapus Tim?',
                    text: 'Data tim akan dihapus permanen.',
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