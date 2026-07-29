@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                {{-- Welcome --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card overflow-hidden">
                            <div class="card-body bg-primary position-relative">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="text-white mb-2">
                                            Selamat Datang, {{ auth()->user()->name ?? 'Administrator' }} 👋
                                        </h3>
                                        <p class="text-white opacity-75 mb-0">
                                            Berikut ringkasan aktivitas HR dan absensi hari ini.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="text-white"
                                            style="font-size:90px">
                                        </iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistic Cards --}}
                <div class="row">
                    
                        {{-- 1. Total Karyawan Keseluruhan --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1">Total Karyawan</p>
                                            <h3 class="mb-0">{{ number_format($totalEmployees) }}</h3>
                                        </div>
                                        <div class="avatar-md bg-success-subtle rounded">
                                            <div class="avatar-title">
                                                <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-28 text-success"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Hadir Hari Ini --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1">Hadir Hari Ini</p>
                                            <h3 class="mb-0">{{ number_format($hadirHariIni) }}</h3>
                                        </div>
                                        <div class="avatar-md bg-info-subtle rounded">
                                            <div class="avatar-title">
                                                <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-28 text-info"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Jumlah Karyawan per Company --}}
                        <div class="col-xl-4 col-md-12 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-muted mb-3 font-size-14">Total Karyawan - <b>{{ number_format($totalEmployees) }}</b></h5>
                                    <div class="table-responsive" style="max-height: 120px; overflow-y: auto;">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                @forelse($companiesWithEmployeeCount as $comp)
                                                    <tr>
                                                        <td><span class="text-dark fw-semibold">{{ $comp->name }}</span></td>
                                                        <td class="text-end">
                                                            <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                                                {{ $comp->employees_count }} Orang
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted">Belum ada data perusahaan</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    

                    <div class="row">
                        {{-- Attendance Overview --}}
                        <div class="col-12 col-md-8 col-lg-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4 class="card-title d-flex align-items-center" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-html="true" title="<strong>Cara Perhitungan Grafik:</strong><br>
                                                1. <strong>Kehadiran Fisik</strong>: (Present) / Total Hari Kerja.<br>
                                                2. <strong>Ketidakhadiran Sah</strong>: (Sakit + Cuti + Izin) / Total Hari Kerja.<br>
                                                3. <strong>Mangkir (Alpha)</strong>: Karyawan absen tanpa keterangan.">
                                        Statistik Kehadiran Bulanan
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div id="attendance-chart" style="height: 350px;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- List Cuti/Izin --}}
                        <div class="col-12 col-md-4 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4 class="card-title">Cuti/Izin Bulan Ini (Approved)</h4>
                                </div>
                                <div class="card-body">
                                    @if(isset($approvedLeaves) && $approvedLeaves->count() > 0)
                                        <ul class="list-group list-group-flush">
                                            @foreach($approvedLeaves as $leave)
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-0">{{ $leave->employee->full_name ?? $leave->employee->name ?? 'Nama Karyawan' }}</h6>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} -
                                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-info">{{ $leave->leaveType->name ?? 'Cuti/Izin' }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted text-center mb-0">Tidak ada karyawan cuti bulan ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- --- BARIS KETIGA (KARYAWAN & KONTRAK TERBARU) --- --}}
                    <div class="row">
                        {{-- Employee --}}
                        <div class="col-12 col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4 class="card-title">Karyawan Terbaru</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Jabatan</th>
                                                    <th>Tanggal Bergabung</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($latestEmployees as $employee)
                                                    <tr>
                                                        <td>
                                                            {{ $employee->full_name }} <br>
                                                            <small class="text-muted"><B>NIK :</B> {{ $employee->nik }}</small>
                                                        </td>
                                                        <td>{{ $employee->position?->name ?? '-' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($employee->join_date)->format('d M Y') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Belum ada data karyawan.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kontrak Karyawan Terbaru --}}
                        <div class="col-12 col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0 text-dark">
                                        <i class="bi bi-clock-history text-warning me-2"></i>Kontrak Karyawan Segera Berakhir
                                    </h4>
                                    <a href="{{ route('employee-contracts.index') }}" class="btn btn-sm btn-light">Lihat Semua</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama Karyawan</th>
                                                    <th>No. Kontrak</th>
                                                    <th>Berakhir Pada</th>
                                                    <th>Peringatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($expiringContracts as $contract)
                                                    @php
                                                        // Menghitung sisa hari
                                                        $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($contract->end_date), false);
                                                        // Merah jika sisa <= 7 hari, Kuning jika lebih
                                                        $badgeColor = $daysLeft <= 7 ? 'danger' : 'warning';
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <span class="fw-medium text-dark">{{ $contract->employee?->full_name ?? '-' }}</span><br>
                                                            <small class="text-muted">NIK: {{ $contract->employee?->nik ?? '-' }}</small>
                                                        </td>
                                                        <td class="font-monospace small">
                                                            {{ $contract->contract_number ?? '-' }} <br>
                                                            <span class="badge bg-info-subtle text-info px-2 py-1 border border-info-subtle">
                                                                {{ $contract->employeeStatus?->name ?? '-' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/y') }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $badgeColor }} text-white px-2 py-1">
                                                                Sisa {{ ceil($daysLeft) }} Hari
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-4">
                                                            <i class="bi bi-shield-check fs-4 d-block mb-1 text-success"></i>
                                                            Aman! Tidak ada kontrak yang akan berakhir dalam 30 hari ke depan.
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


                    {{-- --- BARIS KEEMPAT (AKTIVITAS LOGIN & ULANG TAHUN) --- --}}
                    <div class="row">
                        {{-- Ulang Tahun Bulan Ini --}}
                        <div class="col-12 col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h4 class="card-title mb-0">Ulang Tahun Bulan Ini 🎂</h4>
                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1">
                                        {{ \Carbon\Carbon::now()->translatedFormat('F') }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    @if(isset($upcomingBirthdays) && $upcomingBirthdays->count() > 0)
                                        <ul class="list-group list-group-flush">
                                            @foreach($upcomingBirthdays as $employee)
                                                @php
                                                    // Menghitung umur / ulang tahun ke-berapa saat ini
                                                    $age = \Carbon\Carbon::parse($employee->birth_date)->age;
                                                @endphp
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom border-dashed' : '' }}">
                                                    <div class="d-flex align-items-center">
                                                        
                                                        <div class="me-3">
                                                            @if($employee->photo)
                                                                <div style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#photoModal{{ $employee->id }}">
                                                                    <img src="{{ asset('storage/' . $employee->photo) }}" 
                                                                        alt="Foto {{ $employee->full_name }}" 
                                                                        class="rounded-circle object-fit-cover shadow-sm" 
                                                                        style="width: 40px; height: 40px; transition: transform 0.2s;"
                                                                        onmouseover="this.style.transform='scale(1.1)'"
                                                                        onmouseout="this.style.transform='scale(1)'">
                                                                </div>

                                                                <div class="modal fade" id="photoModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content bg-transparent border-0 shadow-none">
                                                                            <div class="modal-header border-0 justify-content-end pb-0">
                                                                                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body text-center">
                                                                                <img src="{{ asset('storage/' . $employee->photo) }}" 
                                                                                    alt="Foto {{ $employee->full_name }}" 
                                                                                    class="img-fluid rounded shadow-lg" 
                                                                                    style="max-height: 70vh; object-fit: contain;">
                                                                                <h5 class="text-white fw-bold mt-3">{{ $employee->full_name }} ({{ $age }} Tahun)</h5>
                                                                                <span class="badge bg-light text-dark">{{ $employee->position?->name ?? 'Karyawan' }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="avatar-sm bg-purple-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                    <iconify-icon icon="solar:user-rounded-bold-duotone" class="text-primary fs-20"></iconify-icon>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <h6 class="mb-0 fw-semibold text-dark">
                                                                {{ $employee->full_name }}
                                                                <small class="text-purple fw-bold ms-1" style="font-size: 11px;">({{ $age }} th)</small>
                                                            </h6>
                                                            <small class="text-muted">{{ $employee->position?->name ?? 'Karyawan' }}</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-end">
                                                        <span class="badge bg-purple text-white fw-bold px-2 py-1 fs-12 rounded mb-1 d-block">
                                                            {{ \Carbon\Carbon::parse($employee->birth_date)->format('d M') }}
                                                        </span>
                                                        
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <iconify-icon icon="solar:confetti-bold-duotone" class="fs-32 text-muted mb-2 d-block m-auto"></iconify-icon>
                                            Tidak ada yang berulang tahun di bulan ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- Login Activities --}}
                        <div class="col-12 col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h4 class="card-title mb-0">Aktivitas Autentikasi Sistem</h4>
                                    <span class="badge bg-primary-subtle text-primary">Live Log</span>
                                </div>
                                <div class="card-body">
                                    @forelse($latestLogins as $activity)
                                        @php
                                            switch ($activity->event) {
                                                case 'login':
                                                    $bgColor = 'bg-success-subtle';
                                                    $iconColor = 'text-success';
                                                    $iconName = 'solar:login-3-bold';
                                                    $statusText = 'Login Berhasil';
                                                    break;
                                                case 'logout':
                                                    $bgColor = 'bg-warning-subtle';
                                                    $iconColor = 'text-warning';
                                                    $iconName = 'solar:logout-2-bold';
                                                    $statusText = 'Logout Sistem';
                                                    break;
                                                case 'failed_login':
                                                    $bgColor = 'bg-danger-subtle';
                                                    $iconColor = 'text-danger';
                                                    $iconName = 'solar:shield-warning-bold';
                                                    $statusText = 'Gagal Login';
                                                    break;
                                                default:
                                                    $bgColor = 'bg-secondary-subtle';
                                                    $iconColor = 'text-secondary';
                                                    $iconName = 'solar:question-circle-bold';
                                                    $statusText = ucfirst(str_replace('_', ' ', $activity->event));
                                                    break;
                                            }
                                        @endphp

                                        <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom border-dashed mb-2' : '' }}">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm {{ $bgColor }} rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 38px; height: 38px; min-width: 38px;">
                                                    <iconify-icon icon="{{ $iconName }}" class="{{ $iconColor }} fs-18"></iconify-icon>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 mb-0 text-dark fw-semibold">{{ $activity->email }}</h5>
                                                    <div class="text-muted d-flex align-items-center gap-2 mt-1" style="font-size: 12px;">
                                                        <span class="{{ $iconColor }} fw-medium">{{ $statusText }}</span>
                                                        <span>•</span>
                                                        <span>IP: {{ $activity->ip_address }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-muted small fw-medium" title="{{ $activity->logged_at->format('d M Y H:i:s') }}">
                                                    {{ $activity->logged_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <iconify-icon icon="solar:shield-warning-bold-duotone" class="fs-32 text-warning mb-2 d-block m-auto"></iconify-icon>
                                            Belum ada aktivitas autentikasi terekam.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        
                    </div>

                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true
                },
                colors: ['#28a745', '#ffc107', '#dc3545'],
                series: [
                    {
                        name: 'Physical Attendance (Hadir Fisik)',
                        data: @json($kpiData['physical'])
                    },
                    {
                        name: 'Authorized Absence (Sakit, Cuti, Izin)',
                        data: @json($kpiData['authorized'])
                    },
                    {
                        name: 'Unauthorized Absence (Mangkir)',
                        data: @json($kpiData['unauthorized'])
                    }
                ],
                xaxis: {
                    categories: @json($months),
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: {
                    max: 100,
                    labels: {
                        formatter: function (val) {
                            return val + "%";
                        }
                    }
                }
            };

            var chart = new ApexCharts(
                document.querySelector("#attendance-chart"),
                options
            );
            chart.render();
        });
    </script>
@endpush