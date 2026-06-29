@extends('layouts.app')
@section('title', 'List Aktifitas Login')
@section('content')
    <!-- START Wrapper -->
    <div class="wrapper">
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->

        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-xxl">

                <!-- Advanced Filters -->
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header d-flex align-items-center">
                        <iconify-icon icon="solar:filter-bold-duotone" class="text-primary me-2 fs-20">
                        </iconify-icon>

                        <h5 class="mb-0 fw-semibold">
                            Filter Aktifitas
                        </h5>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('login-activities.index') }}">

                            <div class="row g-3">
                                {{-- Search --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Cari email
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            <iconify-icon icon="solar:magnifer-bold-duotone">
                                            </iconify-icon>
                                        </span>

                                        <input type="text" name="search" class="form-control"
                                            placeholder="Masukkan email..." value="{{ request('search') }}">

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

                                        <a href="{{ route('login-activities.index') }}" class="btn btn-secondary">
                                            Reset
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">
                            Aktifitas Login
                        </h4>
                        <p class="text-muted mb-0">{{ $activities->total() }} aktifitas yang ditemukan di sistem Anda
                        </p>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Email</th>
                                        <th>Event</th>
                                        <th>IP Address</th>
                                        <th width="40%">User Agent</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td>

                                                <div>
                                                    <span
                                                        class="text-dark fw-medium">{{ $activity->logged_at->format('d M Y') }}</span>
                                                    <br><small
                                                        class="text-muted">{{ $activity->logged_at->format('H:i:s') }}</small>
                                                    <br><small
                                                        class="text-muted">{{ $activity->logged_at->diffForHumans() }}</small>
                                                </div>
                                            </td>

                                            <td>
                                                {{ $activity->email }}
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match ($activity->event) {
                                                        'login' => 'success',
                                                        'logout' => 'warning',
                                                        'failed_login' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp

                                                <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $activity->event)) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $activity->ip_address }}
                                            </td>
                                            <td>
                                                {{ $activity->user_agent }}
                                            </td>
                                            <td>
                                                <a href="{{ route('login-activities.show', $activity) }}"
                                                    class="btn btn-soft-info btn-sm" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-title="Lihat Detail">
                                                    <iconify-icon icon="solar:eye-bold-duotone">
                                                    </iconify-icon>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                No Data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>

                        <!-- Start pagination -->
                        <div class="card-footer d-flex justify-content-end">
                            {{ $activities->links() }}
                        </div>
                        <!-- End pagination -->

                    </div>

                </div>


            </div>
        </div>
    </div>

@endsection