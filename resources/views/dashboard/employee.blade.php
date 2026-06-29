@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                {{-- Welcome --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 shadow-sm">
                            <div class="card-body bg-primary">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h3 class="text-white mb-2">
                                            Selamat Datang, {{ $employee->full_name }} 👋
                                        </h3>

                                        <p class="text-white opacity-75 mb-4">
                                            Berikut ringkasan informasi Anda.
                                        </p>

                                        <div class="row text-white">
                                            <div class="col-md-3">
                                                <small class="opacity-75">NIK</small>
                                                <div class="fw-bold">{{ $employee->nik }}</div>
                                            </div>

                                            <div class="col-md-3">
                                                <small class="opacity-75">Jabatan</small>
                                                <div class="fw-bold">{{ $employee->position?->name ?? '-' }}</div>
                                            </div>

                                            <div class="col-md-3">
                                                <small class="opacity-75 d-block mb-1">Masa Kontrak Anda</small>
                                                <div class="fw-bold">
                                                    @if($activeContract)
                                                        {{ \Carbon\Carbon::parse($activeContract->start_date)->translatedFormat('d F Y') }}
                                                        <span class="fw-bold">s/d</span>
                                                        {{ \Carbon\Carbon::parse($activeContract->end_date)->translatedFormat('d F Y') }}
                                                    @else
                                                        <span class="fw-bold">Tidak ada kontrak aktif</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-end d-none d-lg-block">
                                        @if($employee->photo)
                                            <img src="{{ asset('storage/' . $employee->photo) }}" width="200" height="200"
                                                class="img-fluid rounded object-fit-cover" alt="{{ $employee->full_name }}">
                                        @else
                                            <img src="{{ asset('assets/images/users/dummy-avatar.jpg') }}" width="150"
                                                height="150" class="rounded-circle object-fit-cover" alt="">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistics (Kembali ke Hitungan Status Pengajuan Awal) --}}
                <div class="row mb-4">
                    @php
                        $cards = [
                            ['Pending', $pendingLeaves, 'warning', 'solar:clock-circle-bold-duotone'],
                            ['Approved', $approvedLeaves, 'success', 'solar:check-circle-bold-duotone'],
                            ['Rejected', $rejectedLeaves, 'danger', 'solar:close-circle-bold-duotone'],
                        ];
                    @endphp

                    @foreach($cards as $card)
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 fw-medium">{{ $card[0] }}</p>
                                            <h2 class="mb-0 fw-bold">{{ $card[1] }}</h2>
                                        </div>

                                        <div class="avatar-md bg-{{ $card[2] }}-subtle rounded">
                                            <div class="avatar-title">
                                                <iconify-icon icon="{{ $card[3] }}" class="fs-28 text-{{ $card[2] }}">
                                                </iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Leave Table (Sama seperti show.blade) --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h4 class="card-title mb-0 fw-bold text-dark">
                            <iconify-icon icon="solar:clipboard-list-bold-duotone"
                                class="align-middle me-1 text-primary fs-20"></iconify-icon>
                            Rincian Kuota Cuti Saya
                        </h4>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 60px;">No</th>
                                        <th>Jenis Cuti/Izin</th>
                                        <th class="text-center">Kuota (Hari)</th>
                                        <th class="text-center">Terpakai (Hari)</th>
                                        <th class="text-center">Sisa (Hari)</th>
                                        <th class="pe-4" style="width: 250px;">Persentase Pemakaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leaveAllocations as $index => $allocation)
                                        @php
                                            $quota = $allocation->allocated_days;
                                            $used = $allocation->used_days;
                                            $remaining = $allocation->remaining_days;

                                            $percentage = $quota > 0
                                                ? min(100, round(($used / $quota) * 100))
                                                : 0;

                                            // Menentukan warna progress bar berdasarkan persentase
                                            $barColor = 'bg-primary';
                                            if ($percentage >= 80) {
                                                $barColor = 'bg-danger';
                                            } elseif ($percentage >= 50) {
                                                $barColor = 'bg-warning';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-medium text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ $allocation->leaveType?->name ?? 'Jenis Cuti N/A' }}</div>
                                            </td>
                                            <td class="text-center fw-semibold text-secondary">
                                                {{ floatval($quota) }}
                                            </td>
                                            <td class="text-center fw-semibold text-danger">
                                                {{ floatval($used) }}
                                            </td>
                                            <td class="text-center fw-bold text-success">
                                                {{ floatval($remaining) }}
                                            </td>
                                            <td class="pe-4">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <small class="text-muted fs-11">Terpakai</small>
                                                    <small class="fw-bold text-dark fs-11">{{ $percentage }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $barColor }}" style="width: {{ $percentage }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <iconify-icon icon="solar:box-minimalistic-bold-duotone"
                                                    class="fs-40 mb-2 d-block text-secondary"></iconify-icon>
                                                Tidak ada data alokasi cuti aktif untuk periode kontrak Anda.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection