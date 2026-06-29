@extends('layouts.app')
@section('title', 'Detail Aktifitas Pengguna')
@section('content')

    <div class="wrapper">
        <div class="page-content">

            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h4 class="mb-1">
                            Detail Login Activity
                        </h4>

                        <p class="text-muted mb-0">
                            Activity #{{ $loginActivity->id }}
                        </p>
                    </div>

                    <a href="{{ route('login-activities.index') }}" class="btn btn-secondary">

                        <iconify-icon icon="solar:arrow-left-bold">
                        </iconify-icon>

                        Kembali

                    </a>

                </div>

                @php

                    $badgeColor = match ($loginActivity->event) {
                        'login' => 'success',
                        'logout' => 'warning',
                        'failed_login' => 'danger',
                        default => 'secondary'
                    };

                @endphp

                <div class="row">

                    {{-- LEFT --}}
                    <div class="col-lg-8">

                        {{-- ACTIVITY --}}
                        <div class="card">

                            <div class="card-header">

                                <h5 class="card-title mb-0">
                                    Informasi Aktivitas
                                </h5>

                            </div>

                            <div class="card-body">

                                <div class="row g-4">

                                    <div class="col-md-6">

                                        <label class="text-muted">
                                            Event
                                        </label>

                                        <div class="mt-2">

                                            <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} fs-6">

                                                {{ ucfirst(str_replace('_', ' ', $loginActivity->event)) }}

                                            </span>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="text-muted">
                                            User ID
                                        </label>

                                        <h6 class="mt-2">

                                            {{ $loginActivity->user_id ?? '-' }}

                                        </h6>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="text-muted">
                                            Email
                                        </label>

                                        <h6 class="mt-2">

                                            {{ $loginActivity->email }}

                                        </h6>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="text-muted">
                                            IP Address
                                        </label>

                                        <h6 class="mt-2">

                                            {{ $loginActivity->ip_address }}

                                        </h6>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="text-muted">
                                            Tanggal
                                        </label>

                                        <h6 class="mt-2">

                                            {{ \Carbon\Carbon::parse($loginActivity->logged_at)->format('d M Y') }}

                                        </h6>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="text-muted">
                                            Jam
                                        </label>

                                        <h6 class="mt-2">

                                            {{ \Carbon\Carbon::parse($loginActivity->logged_at)->format('H:i:s') }}

                                        </h6>

                                    </div>

                                    <div class="col-12">

                                        <label class="text-muted">
                                            Relative Time
                                        </label>

                                        <h6 class="mt-2">

                                            {{ \Carbon\Carbon::parse($loginActivity->logged_at)->diffForHumans() }}

                                        </h6>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- DEVICE --}}
                        <div class="card">

                            <div class="card-header">

                                <h5 class="card-title mb-0">

                                    Device Information

                                </h5>

                            </div>

                            <div class="card-body">

                                <div class="row text-center">

                                    <div class="col-md-4">

                                        <iconify-icon icon="solar:window-frame-bold-duotone" class="fs-1 text-primary">
                                        </iconify-icon>

                                        <h6 class="mt-2">

                                            {{ $browser ?: 'Unknown' }}

                                        </h6>

                                        <small class="text-muted">
                                            Browser
                                        </small>

                                    </div>

                                    <div class="col-md-4">

                                        <iconify-icon icon="solar:monitor-bold-duotone" class="fs-1 text-success">
                                        </iconify-icon>

                                        <h6 class="mt-2">

                                            {{ $platform ?: 'Unknown' }}

                                        </h6>

                                        <small class="text-muted">
                                            Operating System
                                        </small>

                                    </div>

                                    <div class="col-md-4">

                                        <iconify-icon icon="solar:smartphone-bold-duotone" class="fs-1 text-warning">
                                        </iconify-icon>

                                        <h6 class="mt-2">

                                            {{ $device }}

                                        </h6>

                                        <small class="text-muted">
                                            Device Type
                                        </small>

                                    </div>

                                </div>

                                <hr>

                                <label class="text-muted">
                                    User Agent
                                </label>

                                <div class="mt-2">

                                    <code class="text-wrap">

                                            {{ $loginActivity->user_agent }}

                                        </code>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-lg-4">

                        {{-- SAME IP --}}
                        <div class="card">

                            <div class="card-header">

                                <h5 class="card-title mb-0">

                                    Aktivitas dari IP yang Sama

                                </h5>

                            </div>

                            <div class="card-body">

                                @forelse($relatedByIp as $activity)

                                    <div class="border-bottom pb-2 mb-2">

                                        <div>

                                            {{ $activity->email }}

                                        </div>

                                        <small class="text-muted">

                                            {{ ucfirst($activity->event) }}

                                            •

                                            {{ \Carbon\Carbon::parse($activity->logged_at)->diffForHumans() }}

                                        </small>

                                    </div>

                                @empty

                                    <p class="text-muted mb-0">

                                        Tidak ada data

                                    </p>

                                @endforelse

                            </div>

                        </div>

                        {{-- SAME USER --}}
                        <div class="card">

                            <div class="card-header">

                                <h5 class="card-title mb-0">

                                    Aktivitas User yang Sama

                                </h5>

                            </div>

                            <div class="card-body">

                                @forelse($relatedByEmail as $activity)

                                    <div class="border-bottom pb-2 mb-2">

                                        <div>

                                            {{ $activity->ip_address }}

                                        </div>

                                        <small class="text-muted">

                                            {{ ucfirst($activity->event) }}

                                            •

                                            {{ \Carbon\Carbon::parse($activity->logged_at)->diffForHumans() }}

                                        </small>

                                    </div>

                                @empty

                                    <p class="text-muted mb-0">

                                        Tidak ada data

                                    </p>

                                @endforelse

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection