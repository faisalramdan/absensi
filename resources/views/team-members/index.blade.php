@extends('layouts.app')

@section('title', 'Anggota Team')

@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h3 class="fw-bold mb-1">

                            {{ $team->name }}

                        </h3>

                        <p class="text-muted mb-0">

                            Daftar Anggota Team

                        </p>

                    </div>

                    <div>

                        <a href="{{ route('teams.show', $team) }}" class="btn btn-secondary">

                            <iconify-icon icon="solar:arrow-left-bold"></iconify-icon>

                            Kembali

                        </a>

                        <a href="{{ route('teams.members.create', $team) }}" class="btn btn-primary">

                            <iconify-icon icon="solar:user-plus-bold"></iconify-icon>

                            Tambah Anggota

                        </a>

                    </div>

                </div>

                {{-- Filter --}}

                <div class="card">

                    <div class="card-header">

                        <h4 class="card-title">

                            Filter

                        </h4>

                    </div>

                    <div class="card-body">

                        <form method="GET">

                            <div class="row">

                                <div class="col-md-4">

                                    <label class="form-label">

                                        Search

                                    </label>

                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                        placeholder="Nama Karyawan">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label">

                                        Role

                                    </label>

                                    <select name="role" class="form-select">

                                        <option value="">

                                            Semua

                                        </option>

                                        <option value="Leader" @selected(request('role') == 'Leader')>

                                            Leader

                                        </option>

                                        <option value="Co Leader" @selected(request('role') == 'Co Leader')>

                                            Co Leader

                                        </option>

                                        <option value="Member" @selected(request('role') == 'Member')>

                                            Member

                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label">

                                        Status

                                    </label>

                                    <select name="status" class="form-select">

                                        <option value="">

                                            Semua

                                        </option>

                                        <option value="1" @selected(request('status') === '1')>

                                            Aktif

                                        </option>

                                        <option value="0" @selected(request('status') === '0')>

                                            Non Aktif

                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-2 d-flex align-items-end">

                                    <button class="btn btn-primary me-2">

                                        Filter

                                    </button>

                                    <a href="{{ route('teams.members.index', $team) }}" class="btn btn-light">

                                        Reset

                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                {{-- Table --}}

                <div class="card">

                    <div class="card-header">

                        <h4 class="card-title">

                            Daftar Anggota

                        </h4>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead>

                                    <tr>

                                        <th width="60">

                                            No

                                        </th>

                                        <th>

                                            Nama

                                        </th>

                                        <th>

                                            Jabatan

                                        </th>

                                        <th>

                                            Role

                                        </th>

                                        <th>

                                            Status

                                        </th>

                                        <th>

                                            Tanggal Bergabung

                                        </th>

                                        <th width="120">

                                            Aksi

                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($members as $member)

                                                                <tr>

                                                                    <td>

                                                                        {{ $members->firstItem() + $loop->index }}

                                                                    </td>

                                                                    <td>

                                                                        <div class="fw-semibold">

                                                                            {{ $member->employee->full_name }}

                                                                        </div>

                                                                        <small class="text-muted">

                                                                            {{ $member->employee->nik }}

                                                                        </small>

                                                                    </td>

                                                                    <td>

                                                                        {{ $member->employee->position?->name }}

                                                                    </td>

                                                                    <td>

                                                                        @if($member->member_role == 'Leader')

                                                                            <span class="badge bg-primary">

                                                                                Leader

                                                                            </span>

                                                                        @elseif($member->member_role == 'Co Leader')

                                                                            <span class="badge bg-warning">

                                                                                Co Leader

                                                                            </span>

                                                                        @else

                                                                            <span class="badge bg-secondary">

                                                                                Member

                                                                            </span>

                                                                        @endif

                                                                    </td>

                                                                    <td>

                                                                        @if($member->is_active)

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

                                                                        {{ $member->joined_at
                                        ? \Carbon\Carbon::parse($member->joined_at)->format('d M Y')
                                        : '-' }}

                                                                    </td>

                                                                    <td>

                                                                        <div class="dropdown">

                                                                            <button class="btn btn-light btn-sm dropdown-toggle"
                                                                                data-bs-toggle="dropdown">

                                                                                Aksi

                                                                            </button>

                                                                            <ul class="dropdown-menu dropdown-menu-end">

                                                                                <li>

                                                                                    <a href="{{ route('teams.members.edit', [$team, $member]) }}"
                                                                                        class="dropdown-item">

                                                                                        <iconify-icon icon="solar:pen-bold"></iconify-icon>

                                                                                        Edit

                                                                                    </a>

                                                                                </li>

                                                                                <li>

                                                                                    <form action="{{ route('teams.members.destroy', [$team, $member]) }}"
                                                                                        method="POST">

                                                                                        @csrf

                                                                                        @method('DELETE')

                                                                                        <button onclick="return confirm('Hapus anggota team?')"
                                                                                            class="dropdown-item text-danger">

                                                                                            <iconify-icon
                                                                                                icon="solar:trash-bin-trash-bold"></iconify-icon>

                                                                                            Hapus

                                                                                        </button>

                                                                                    </form>

                                                                                </li>

                                                                            </ul>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                    @empty

                                        <tr>

                                            <td colspan="7" class="text-center py-5">

                                                Belum ada anggota Team.

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        <div class="mt-3">

                            {{ $members->links() }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection