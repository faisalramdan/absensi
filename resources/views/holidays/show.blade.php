@extends('layouts.app')
@section('title', 'Detail Hari Libur')
@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">
                            {{ $holiday->name }}
                        </h3>
                        <p class="text-muted mb-0">
                            Detail data master tanggal merah dan pergeseran operasional perusahaan
                        </p>
                    </div>

                    <a href="{{ route('holidays.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold" class="me-1"></iconify-icon>
                        Kembali
                    </a>
                </div>

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm">

                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    Informasi Hari Libur
                                </h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered align-middle">

                                    <tr>
                                        <th width="35%">Nama Hari Libur</th>
                                        <td class="fw-semibold text-dark">{{ $holiday->name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Tanggal Kalender Asli</th>
                                        <td>
                                            <span class="badge bg-light text-secondary border px-2 py-1 fs-12">
                                                {{ \Carbon\Carbon::parse($holiday->date_actual)->translatedFormat('D, d M Y') }}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Tanggal Diterapkan (Libur Real)</th>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1 fs-12">
                                                {{ \Carbon\Carbon::parse($holiday->date_applied)->translatedFormat('D, d M Y') }}
                                            </span>

                                            @if($holiday->date_actual->format('Y-m-d') !== $holiday->date_applied->format('Y-m-d'))
                                                <div class="text-warning small mt-2 fw-medium">
                                                    <iconify-icon icon="solar:transfer-horizontal-bold-duotone"
                                                        class="vertical-middle me-1"></iconify-icon>
                                                    Mengalami pergeseran operasional dari tanggal kalender asli
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Catatan / Keterangan</th>
                                        <td>
                                            <span class="text-muted fs-13">{{ $holiday->notes ?? '-' }}</span>
                                        </td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                    {{-- Sidebar Audit Informasi --}}
                    <div class="col-xl-4">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    Audit Informasi
                                </h4>
                            </div>

                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item px-0 pt-0">
                                        <strong class="text-muted d-block mb-1 fs-12">Dibuat Oleh</strong>
                                        <span class="fw-semibold text-dark">
                                            {{ $holiday->creator?->full_name ?? '-' }}
                                        </span>
                                    </li>

                                    <li class="list-group-item px-0">
                                        <strong class="text-muted d-block mb-1 fs-12">Dibuat Pada</strong>
                                        <span class="fw-semibold text-dark">
                                            {{ $holiday->created_at->format('d M Y H:i') }}
                                        </span>
                                    </li>

                                    <li class="list-group-item px-0">
                                        <strong class="text-muted d-block mb-1 fs-12">Diubah Oleh</strong>
                                        <span class="fw-semibold text-dark">
                                            {{ $holiday->updater?->full_name ?? '-' }}
                                        </span>
                                    </li>

                                    <li class="list-group-item px-0 pb-0">
                                        <strong class="text-muted d-block mb-1 fs-12">Terakhir Diubah</strong>
                                        <span class="fw-semibold text-dark">
                                            {{ $holiday->updated_at->diffForHumans() }}
                                        </span>
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection