@extends('layouts.app')
@section('title', 'List Aktifitas Log')
@section('content')

    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <div class="card mb-4">

                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Filtering
                        </h4>
                    </div>

                    <div class="card-body">
                        <form method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Cari aktivitas..."
                                        value="{{ request('search') }}">
                                </div>

                                <div class="col-md-2">
                                    <select name="user_id" class="form-select">

                                        <option value="">
                                            Semua User
                                        </option>

                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="module" class="form-select">

                                        <option value="">
                                            Semua Modul
                                        </option>

                                        @foreach($modules as $module)
                                            <option value="{{ $module }}" @selected(request('module') == $module)>
                                                {{ $module }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="action" class="form-select">

                                        <option value="">
                                            Semua Aksi
                                        </option>

                                        <option value="Create">Create</option>
                                        <option value="Update">Update</option>
                                        <option value="Delete">Delete</option>

                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        Filter
                                    </button>

                                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Semua Aktifitas</h4>
                            <p class="text-muted mb-0">{{ $logs->total() }} aktifitas yang ditemukan di sistem Anda
                            </p>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama User</th>
                                        <th>Modul/Menu</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi</th>
                                        <th>Waktu</th>
                                        <th width="80">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ $logs->firstItem() + $loop->index }}</td>
                                            <td>{{ $log->user?->name ?? '-' }}</td>
                                            <td>{{ $log->module }}</td>
                                            <td>
                                                @php
                                                    $badge = match ($log->action) {
                                                        'Create' => 'success',
                                                        'Update' => 'warning',
                                                        'Delete' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp

                                                <span class="badge bg-{{ $badge }}">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td>{{ $log->description }}</td>
                                            <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <a href=" {{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-info"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Detail">
                                                    <iconify-icon icon="solar:eye-bold"></iconify-icon></a>
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
                    </div>
                    <div class="card-footer">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection