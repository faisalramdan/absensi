@extends('layouts.app')
@section('title', 'List Cuti / Izin')
@section('content')

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

                {{-- FILTER --}}
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter Data
                        </h5>
                    </div>

                    <div class="card-body">

                        <form method="GET">

                            <div class="row">

                                {{-- Search --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        Cari Data
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone">
                                            </iconify-icon>
                                        </span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Kode atau nama..." value="{{ request('search') }}">
                                    </div>
                                </div>

                                <div class="col-md-2">

                                    <label class="form-label fw-semibold">
                                        Tag
                                    </label>

                                    <select name="tag" class="form-select">
                                        <option value="">
                                            Semua
                                        </option>
                                        <option value="cuti" {{ request('tag') == 'cuti' ? 'selected' : '' }}>
                                            Cuti
                                        </option>
                                        <option value="izin" {{ request('tag') == 'izin' ? 'selected' : '' }}>
                                            Izin
                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-2">

                                    <label class="form-label fw-semibold">
                                        Jenis
                                    </label>

                                    <select name="type" class="form-select">

                                        <option value="">
                                            Semua
                                        </option>
                                        <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>
                                            Perusahaan
                                        </option>
                                        <option value="government" {{ request('type') == 'government' ? 'selected' : '' }}>
                                            Pemerintah
                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-2">

                                    <label class="form-label fw-semibold">
                                        Status
                                    </label>

                                    <select name="status" class="form-select">
                                        <option value="">
                                            Semua
                                        </option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                            Non Aktif
                                        </option>
                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label d-block">
                                        &nbsp;
                                    </label>

                                    <button type="submit" class="btn btn-primary">
                                        Filter 
                                    </button>

                                    <a href="{{ route('leave-types.index') }}" class="btn btn-secondary">
                                        Reset
                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                {{-- TABLE --}}
                <div class="card">

                    <div class="d-flex card-header justify-content-between align-items-center">

                        <div>
                            <h4 class="card-title">Semua Daftar Cuti/Izin</h4>
                            <p class="text-muted mb-0">{{ $leaveTypes->total() }} data yang ditemukan di sistem Anda
                            </p>
                        </div>

                        <div>
                            @can('leave-type.create')
                                <a href="{{ route('leave-types.create') }}" class="btn btn-primary">
                                    + Tambah Cuti/Izin
                                </a>
                            @endcan
                        </div>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table align-middle mb-0">

                                <thead>

                                    <tr>

                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Tag</th>
                                        <th>Jenis</th>
                                        <th>Kuota</th>
                                        <th>Reset</th>
                                        <th>Status</th>
                                        <th>Aksi</th>

                                    </tr>

                                </thead>

                                <tbody>
                                    @forelse($leaveTypes as $leaveType)
                                        <tr>
                                            <td>{{ $leaveTypes->firstItem() + $loop->index }}</td>
                                            <td>{{ $leaveType->code }}</td>
                                            <td>{{ $leaveType->name }}</td>
                                            <td>
                                                @if($leaveType->tag == 'cuti')
                                                    <span class="badge bg-warning">
                                                        Cuti
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        Izin
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($leaveType->type == 'company')
                                                    <span class="badge bg-primary">
                                                        Perusahaan
                                                    </span>
                                                @else
                                                    <span class="badge bg-purple">
                                                        Pemerintah
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $leaveType->quota ?? '-' }}</td>
                                            <td>@switch($leaveType->reset_period)

                                                @case('month')
                                                    Bulanan
                                                    @break

                                                @case('year')
                                                    Tahunan
                                                    @break

                                                @case('never')
                                                    Tidak Ditentukan
                                                    @break

                                            @endswitch
                                        </td>
                                            <td>
                                                @if($leaveType->is_active)
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
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('leave-type.view')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('leave-types.show', $leaveType) }}">
                                                                    Detail
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('leave-type.edit')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('leave-types.edit', $leaveType) }}">
                                                                    Edit
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('leave-type.delete')
                                                            <li>
                                                                <form
                                                                    action="{{ route('leave-types.destroy', $leaveType) }}"
                                                                    method="POST"
                                                                    class="delete-form">

                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button
                                                                        type="button"
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
                                            <td colspan="9" class="text-center">
                                                Tidak ada data
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <span>
                                Menampilkan
                                {{ $leaveTypes->count() }}
                                dari
                                {{ $leaveTypes->total() }}
                                data
                            </span>
                            {{ $leaveTypes->links() }}
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
                    title: 'Hapus Cuti/Izin?',
                    text: 'Data Cuti/Izin akan dihapus permanen.',
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