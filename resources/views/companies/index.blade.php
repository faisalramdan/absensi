@extends('layouts.app')
@section('title', 'List Perusahaan')
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
                <!-- end info -->

                <div class="card">

                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">
                                Data Perusahaan
                            </h4>
                            <p class="text-muted mb-0"> Ada {{ $companies->total() }} data yang ditemukan dalam
                                sistem Anda.
                            </p>
                        </div>
                        <div>
                            @can('company.create')
                                <a href="{{ route('companies.create') }}" class="btn btn-primary">
                                    + Tambah Perusahaan
                                </a>
                            @endcan
                        </div>

                    </div>

                    <div class="card-body p-0">

                        <table class="table align-middle mb-0">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($companies as $company)

                                    <tr>

                                        <td>
                                            {{ $companies->firstItem() + $loop->index }}
                                        </td>

                                        <td>{{ $company->code }}</td>

                                        <td>{{ $company->name }}</td>

                                        <td>{{ $company->email }}</td>

                                        <td>{{ $company->phone }}</td>

                                        <td>
                                            @if($company->is_active)
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

                                                <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">

                                                    Aksi

                                                </button>

                                                <ul class="dropdown-menu">

                                                    @can('company.view')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('companies.show', $company) }}">
                                                                Detail
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    @can('company.edit')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('companies.edit', $company) }}">
                                                                Edit
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    @can('company.delete')
                                                        <li>
                                                            <form action="{{ route('companies.destroy', $company) }}" method="POST"
                                                                class="delete-form">

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

                    <div class="card-footer">

                        <div class="d-flex justify-content-between">

                            <small>
                                Menampilkan
                                {{ $companies->count() }}
                                dari
                                {{ $companies->total() }}
                                perusahaan
                            </small>

                            {{ $companies->links() }}

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
                    title: 'Hapus Company?',
                    text: 'Data company akan dihapus permanen.',
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