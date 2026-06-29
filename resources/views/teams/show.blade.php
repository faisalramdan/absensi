@extends('layouts.app')
@section('title', 'Detail Team')
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
                        {{ $team->company?->name }}
                    </p>
                </div>

                <div>
                    <a href="{{ route('teams.edit', $team) }}"
                        class="btn btn-warning">
                        <iconify-icon icon="solar:pen-bold"></iconify-icon>
                        Edit
                    </a>
                    <a href="{{ route('teams.members.index', $team) }}"
                        class="btn btn-primary">
                        <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                        Kelola Anggota
                    </a>

                    <a href="{{ route('teams.index') }}"
                        class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold"></iconify-icon>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Informasi Team --}}
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Informasi Team
                            </h4>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <th width="220">
                                            Nama Team
                                        </th>
                                        <td>
                                            {{ $team->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Company
                                        </th>
                                        <td>
                                            {{ $team->company?->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Parent Team
                                        </th>
                                        <td>
                                            {{ $team->parent?->name ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Jumlah Leader
                                        </th>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $leaders->count() }}
                                                Leader
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Jumlah Member
                                        </th>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $members->total() }}
                                                Member
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Description
                                        </th>
                                        <td>
                                            {{ $team->description ?: '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Status
                                        </th>
                                        <td>
                                            @if($team->is_active)
                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    Non Aktif
                                                </span>
                                            @endif

                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                {{-- Informasi Audit --}}

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Informasi
                            </h4>
                        </div>

                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Dibuat Oleh</strong>
                                    <br>
                                    {{ $team->creator?->name ?? '-' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Dibuat Pada</strong>
                                    <br>
                                    {{ $team->created_at->format('d M Y H:i') }}
                                </li>

                                <li class="list-group-item">
                                    <strong>Diubah Oleh</strong>
                                    <br>
                                    {{ $team->updater?->name ?? '-' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Terakhir Diubah</strong>
                                    <br>
                                    {{ $team->updated_at->diffForHumans() }}
                                </li>
                            </ul>

                        </div>

                    </div>

                    {{-- Leader Team --}}

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Leader Team
                            </h4>
                        </div>

                        <div class="card-body">
                            @forelse($leaders as $leader)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-md bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold">
                                        {{ strtoupper(substr($leader->employee->full_name,0,1)) }}
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0">
                                            {{ $leader->employee->full_name }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $leader->employee->position?->name }}
                                        </small>
                                    </div>
                                </div>

                            @empty
                                <div class="text-center text-muted">
                                    Belum ada Leader Team.
                                </div>
                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

            {{-- Daftar Anggota Team --}}

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            Daftar Anggota Team
                        </h4>
                    </div>

                    <div>
                        <a href="#"
                            class="btn btn-primary">
                            <iconify-icon icon="solar:user-plus-bold"></iconify-icon>
                            Tambah Anggota
                        </a>

                    </div>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
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

                                            <div class="d-flex align-items-center">

                                                @if($member->employee->photo)
                                                    <img src="{{ asset('storage/'.$member->employee->photo) }}"class="rounded-circle me-2" width="45" height="45">
                                                @else
                                                    <div class="avatar-md bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2"> {{ strtoupper(substr($member->employee->full_name,0,1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ $member->employee->full_name }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $member->employee->email }}
                                                    </small>
                                                </div>

                                            </div>

                                        </td>

                                        <td>

                                            {{ $member->employee->position?->name ?? '-' }}

                                        </td>

                                        <td>

                                            @switch($member->member_role)

                                                @case('Leader')

                                                    <span class="badge bg-primary">

                                                        Leader

                                                    </span>

                                                    @break

                                                @case('Co Leader')

                                                    <span class="badge bg-warning">

                                                        Co Leader

                                                    </span>

                                                    @break

                                                @default

                                                    <span class="badge bg-info">

                                                        Member

                                                    </span>

                                            @endswitch

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

                                            <div class="dropdown">

                                                <button
                                                    class="btn btn-light btn-sm dropdown-toggle"
                                                    data-bs-toggle="dropdown">

                                                    Aksi

                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    <li>

                                                        <a href="#"
                                                            class="dropdown-item">

                                                            <iconify-icon icon="solar:pen-bold"></iconify-icon>

                                                            Edit

                                                        </a>

                                                    </li>

                                                    <li>

                                                        <a href="#"
                                                            class="dropdown-item text-danger">

                                                            <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>

                                                            Hapus

                                                        </a>

                                                    </li>

                                                </ul>

                                            </div>

                                        </td>

                                    </tr>

                                    @empty

                                    <tr>

                                        <td colspan="6" class="text-center py-5">

                                            <img src="{{ asset('assets/images/empty.svg') }}"
                                                width="180"
                                                class="mb-3">

                                            <h5>

                                                Belum Ada Anggota Team

                                            </h5>

                                            <p class="text-muted">

                                                Silakan tambahkan anggota ke dalam team ini.

                                            </p>

                                            <a href="#"
                                                class="btn btn-primary">

                                                <iconify-icon icon="solar:user-plus-bold"></iconify-icon>

                                                Tambah Anggota

                                            </a>

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