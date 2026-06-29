@extends('layouts.app')

@section('title', 'Attendance Processor')

@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">
                @if(session('success'))

                    <div class="alert alert-success shadow-sm">

                        <h5 class="mb-3">

                            <iconify-icon icon="solar:check-circle-bold" class="me-1">
                            </iconify-icon>

                            Attendance berhasil diproses

                        </h5>

                        <table class="table table-borderless mb-0">

                            <tr>
                                <td width="220">
                                    Periode
                                </td>

                                <td>
                                    :
                                    {{ session('summary.period') }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Total Karyawan
                                </td>

                                <td>
                                    :
                                    {{ session('summary.employee') }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Total Attendance
                                </td>

                                <td>
                                    :
                                    {{ session('summary.attendance') }}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Waktu Proses
                                </td>

                                <td>
                                    :
                                    {{ session('summary.duration') }}
                                </td>
                            </tr>

                        </table>

                    </div>

                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h3 class="fw-bold mb-1">
                            Attendance Processor
                        </h3>

                        <p class="text-muted mb-0">
                            Generate Attendance Daily berdasarkan Attendance Log, Shift, Hari Libur dan Cuti.
                        </p>

                    </div>

                </div>

                <div class="row">

                    {{-- FORM --}}
                    <div class="col-xl-8">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Generate Attendance

                                </h4>

                            </div>

                            <div class="card-body">

                                <form action="{{ route('attendance-processor.generate') }}" method="POST" id="generateForm">

                                    @csrf

                                    <div class="row">

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">

                                                Bulan Payroll

                                            </label>

                                            <select name="month" class="form-select" required>

                                                @foreach(range(1, 12) as $month)

                                                    <option value="{{ $month }}" {{ now()->month == $month ? 'selected' : '' }}>

                                                        {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label class="form-label">

                                                Tahun

                                            </label>

                                            <select name="year" class="form-select">

                                                @foreach(range(now()->year - 2, now()->year + 2) as $year)

                                                    <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>

                                                        {{ $year }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                    </div>

                                    <div class="alert alert-info">

                                        <strong>

                                            Periode Payroll

                                        </strong>

                                        <br>

                                        Sistem otomatis menggunakan periode

                                        <strong>

                                            26 bulan sebelumnya

                                        </strong>

                                        sampai

                                        <strong>

                                            25 bulan yang dipilih

                                        </strong>

                                        sesuai kebijakan perusahaan.

                                    </div>

                                    <button type="submit" class="btn btn-primary" id="generateBtn">

                                        <iconify-icon icon="solar:play-bold" class="me-1"></iconify-icon>

                                        Generate Attendance

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                    {{-- SIDEBAR --}}
                    <div class="col-xl-4">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header">

                                <h4 class="card-title mb-0">

                                    Informasi

                                </h4>

                            </div>

                            <div class="card-body">

                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item px-0">

                                        <strong>

                                            Attendance Logs

                                        </strong>

                                        <br>

                                        <span class="text-muted">

                                            Data mentah hasil Import Fingerprint.

                                        </span>

                                    </li>

                                    <li class="list-group-item px-0">

                                        <strong>

                                            Attendance Daily

                                        </strong>

                                        <br>

                                        <span class="text-muted">

                                            Data hasil perhitungan Attendance Processor.

                                        </span>

                                    </li>

                                    <li class="list-group-item px-0">

                                        <strong>

                                            Sumber Perhitungan

                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            • Attendance Log<br>
                                            • Shift Assignment<br>
                                            • Shift Detail<br>
                                            • Holiday<br>
                                            • Leave Request

                                        </small>

                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <script>

        document.getElementById('generateForm').addEventListener('submit', function () {

            const btn = document.getElementById('generateBtn');

            btn.disabled = true;

            btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-1"
                  role="status"
                  aria-hidden="true"></span>
            Processing Attendance...
        `;

        });

    </script>
@endsection