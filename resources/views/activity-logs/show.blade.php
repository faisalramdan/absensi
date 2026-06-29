@extends('layouts.app')
@section('title', 'Detail Log Aktivitas')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">
                            Log Aktivitas Terperinci
                        </h4>
                        <p class="text-muted mb-0">
                            Audit Trail System
                        </p>
                    </div>

                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">

                        <iconify-icon icon="solar:arrow-left-bold" class="me-1">
                        </iconify-icon>

                        Kembali

                    </a>
                </div>

                <div class="row">

                    {{-- Informasi --}}
                    <div class="col-lg-4">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    Informasi Aktivitas
                                </h5>
                            </div>

                            <div class="card-body">

                                <table class="table table-borderless mb-0">

                                    <tr>
                                        <th width="120">User</th>
                                        <td>
                                            {{ $activityLog->user?->name ?? '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Modul</th>
                                        <td>
                                            {{ $activityLog->module }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Aksi</th>
                                        <td>

                                            @php
                                                $badge = match ($activityLog->action) {
                                                    'Create' => 'success',
                                                    'Update' => 'warning',
                                                    'Delete' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp

                                            <span class="badge bg-{{ $badge }}">
                                                {{ $activityLog->action }}
                                            </span>

                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Tanggal</th>
                                        <td>
                                            {{ $activityLog->created_at->format('d M Y H:i:s') }}
                                        </td>
                                    </tr>

                                </table>

                            </div>
                        </div>

                        <div class="card mt-3">

                            <div class="card-header">
                                <h5 class="mb-0">
                                    Deskripsi
                                </h5>
                            </div>

                            <div class="card-body">
                                {{ $activityLog->description }}
                            </div>

                        </div>

                    </div>

                    {{-- Detail Audit --}}
                    <div class="col-lg-8">

                        <div class="card">

                            <div class="card-header">
                                <h5 class="mb-0">
                                    Detail Perubahan
                                </h5>
                            </div>

                            <div class="card-body">

                                {{-- CREATE --}}
                                @if($activityLog->action === 'Create')

                                    <div class="alert alert-success">
                                        Data berhasil dibuat.
                                    </div>

                                    <div class="table-responsive">

                                        <table class="table table-bordered">

                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($activityLog->new_values ?? [] as $field => $value)

                                                    <tr>
                                                        <td>{{ $field }}</td>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                @endif

                                {{-- UPDATE --}}
                                @if($activityLog->action === 'Update')

                                    <div class="table-responsive">

                                        <table class="table table-bordered">

                                            <thead class="table-light">
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Nilai Lama</th>
                                                    <th>Nilai Baru</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($activityLog->new_values ?? [] as $field => $value)

                                                    @php
                                                        $oldValue = $activityLog->old_values[$field] ?? null;
                                                        $newValue = $value;

                                                        // Normalisasi PostgreSQL boolean
                                                        if ($oldValue === 't') {
                                                            $oldValue = true;
                                                        }

                                                        if ($oldValue === 'f') {
                                                            $oldValue = false;
                                                        }
                                                    @endphp

                                                    @if($oldValue !== $newValue)

                                                        <tr>
                                                            <td><strong>{{ $field }}</strong></td>

                                                            <td>
                                                                @if(is_bool($oldValue))
                                                                    <span class="badge bg-{{ $oldValue ? 'success' : 'danger' }}">
                                                                        {{ $oldValue ? 'Aktif' : 'Non Aktif' }}
                                                                    </span>
                                                                @elseif(is_array($oldValue))
                                                                    {{ implode(', ', $oldValue) }}
                                                                @else
                                                                    {{ $oldValue }}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if(is_bool($newValue))
                                                                    <span class="badge bg-{{ $newValue ? 'success' : 'danger' }}">
                                                                        {{ $newValue ? 'Aktif' : 'Non Aktif' }}
                                                                    </span>
                                                                @elseif(is_array($newValue))
                                                                    {{ implode(', ', $newValue) }}
                                                                @else
                                                                    {{ $newValue }}
                                                                @endif
                                                            </td>
                                                        </tr>

                                                    @endif

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                @endif

                                {{-- DELETE --}}
                                @if($activityLog->action === 'Delete')

                                    <div class="alert alert-danger">
                                        Data telah dihapus.
                                    </div>

                                    <div class="table-responsive">

                                        <table class="table table-bordered">

                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($activityLog->old_values ?? [] as $field => $value)

                                                    <tr>
                                                        <td>{{ $field }}</td>
                                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection