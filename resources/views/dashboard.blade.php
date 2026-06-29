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

                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <p class="text-muted mb-1">
                                            Total User
                                        </p>

                                        <h3 class="mb-0">
                                            32
                                        </h3>
                                    </div>

                                    <div class="avatar-md bg-primary-subtle rounded">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:user-bold-duotone" class="fs-28 text-primary">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <p class="text-muted mb-1">
                                            Total Karyawan
                                        </p>

                                        <h3 class="mb-0">
                                            125
                                        </h3>
                                    </div>

                                    <div class="avatar-md bg-success-subtle rounded">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"
                                                class="fs-28 text-success">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <p class="text-muted mb-1">
                                            Hadir Hari Ini
                                        </p>

                                        <h3 class="mb-0">
                                            102
                                        </h3>
                                    </div>

                                    <div class="avatar-md bg-info-subtle rounded">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-28 text-info">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <p class="text-muted mb-1">
                                            Izin / Cuti
                                        </p>

                                        <h3 class="mb-0">
                                            8
                                        </h3>
                                    </div>

                                    <div class="avatar-md bg-warning-subtle rounded">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:calendar-mark-bold-duotone"
                                                class="fs-28 text-warning">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>


                <div class="row">

                    {{-- Attendance Overview --}}
                    <div class="col-xl-8">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Statistik Kehadiran Bulanan
                                </h4>
                            </div>

                            <div class="card-body">

                                <div id="attendance-chart" style="height: 350px;"></div>

                            </div>

                        </div>

                    </div>


                    {{-- Attendance Summary --}}
                    <div class="col-xl-4">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Status Hari Ini
                                </h4>
                            </div>

                            <div class="card-body">

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <span>Hadir</span>
                                        <span>85%</span>
                                    </div>

                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-success" style="width:85%">
                                        </div>
                                    </div>
                                </div>


                                <div class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <span>Izin</span>
                                        <span>8%</span>
                                    </div>

                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-warning" style="width:8%">
                                        </div>
                                    </div>
                                </div>


                                <div class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <span>Sakit</span>
                                        <span>4%</span>
                                    </div>

                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-info" style="width:4%">
                                        </div>
                                    </div>
                                </div>


                                <div>
                                    <div class="d-flex justify-content-between">
                                        <span>Alpha</span>
                                        <span>3%</span>
                                    </div>

                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-danger" style="width:3%">
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="row">

                    {{-- Employee --}}
                    <div class="col-xl-6">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Karyawan Terbaru
                                </h4>
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

                                            <tr>
                                                <td>Joni</td>
                                                <td>Staff</td>
                                                <td>09 Jun 2026</td>
                                            </tr>

                                            <tr>
                                                <td>Budi</td>
                                                <td>Supervisor</td>
                                                <td>08 Jun 2026</td>
                                            </tr>

                                            <tr>
                                                <td>Sinta</td>
                                                <td>HRD</td>
                                                <td>07 Jun 2026</td>
                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Login Activities --}}
                    <div class="col-xl-6">

                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Login Activities
                                </h4>
                            </div>

                            <div class="card-body">

                                <div class="d-flex align-items-center mb-3">

                                    <div class="avatar-sm bg-success-subtle rounded-circle me-3">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:login-3-bold" class="text-success">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                    <div>
                                        <strong>Faisal Ramdan</strong>
                                        <div class="text-muted small">
                                            Login 5 menit yang lalu
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex align-items-center mb-3">

                                    <div class="avatar-sm bg-danger-subtle rounded-circle me-3">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:logout-2-bold" class="text-danger">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                    <div>
                                        <strong>Joni</strong>
                                        <div class="text-muted small">
                                            Logout 10 menit yang lalu
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex align-items-center">

                                    <div class="avatar-sm bg-success-subtle rounded-circle me-3">
                                        <div class="avatar-title">
                                            <iconify-icon icon="solar:login-3-bold" class="text-success">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                    <div>
                                        <strong>Sinta</strong>
                                        <div class="text-muted small">
                                            Login 30 menit yang lalu
                                        </div>
                                    </div>

                                </div>

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
                    height: 350
                },

                series: [{
                    name: 'Kehadiran',
                    data: [80, 92, 88, 95, 90, 97]
                }],

                xaxis: {
                    categories: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'Mei',
                        'Jun'
                    ]
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