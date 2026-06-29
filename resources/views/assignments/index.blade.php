@extends('layouts.app')
@section('title', 'Penjadwalan Karyawan Matrix')

@section('content')
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:danger-bold" class="me-1"></iconify-icon>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('assignments.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cari Karyawan</label>
                                    <input type="text" name="search" class="form-control" placeholder="Masukkan nama..." value="{{ request('search') }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Bulan</label>
                                    <select name="month" class="form-select">
                                        @foreach(range(1, 12) as $m)
                                            @php
                                                $monthName = \Carbon\Carbon::create(2000, $m, 1)->translatedFormat('F');
                                                // Default ke bulan berjalan saat ini atau sesuai request filter
                                                $selected = request('month', date('m')) == $m ? 'selected' : '';
                                            @endphp
                                            <option value="{{ sprintf('%02d', $m) }}" {{ $selected }}>{{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Tahun</label>
                                    <select name="year" class="form-select">
                                        @foreach(range(date('Y') - 1, date('Y') + 2) as $y)
                                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="{{ route('assignments.index') }}" class="btn btn-secondary w-100">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Matriks Jadwal Shift Kerja</h4>
                            <p class="text-muted mb-0">Periode: <b>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</b> sampai <b>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</b></p>
                        </div>
                        <div>
                            @can('shift-assignment.create')
                                <a href="{{ route('assignments.create') }}" class="btn btn-primary btn-sm">
                                    + Atur Jadwal Massal
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered align-middle table-sm table-hover text-center mb-0">
                                <thead class="table-light sticky-top" style="z-index: 2;">
                                    <tr>
                                        <th scope="col" class="text-start ps-3 align-middle" style="min-width: 180px; position: sticky; left: 0; background: #f8f9fa; z-index: 3;">Nama Karyawan</th>
                                        
                                        @foreach($dates as $date)
                                            @php
                                                $carbonDate = \Carbon\Carbon::parse($date);
                                                $isSunday = $carbonDate->isSunday();
                                                $isHoliday = array_key_exists($date, $holidays);
                                                $holidayName = $isHoliday ? $holidays[$date] : '';
                                            @endphp
                                            
                                            <th class="text-center {{ $isSunday || $isHoliday ? 'bg-danger text-white' : '' }}" 
                                                style="min-width: 50px;"
                                                @if($isHoliday) data-bs-toggle="tooltip" title="Hari Libur: {{ $holidayName }}" @endif>
                                                <small class="d-block fs-10 fw-normal">{{ $carbonDate->translatedFormat('D') }}</small>
                                                <span class="fs-12 fw-bold">{{ $carbonDate->format('d') }}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr>
                                            <td class="text-start ps-3 fw-semibold text-dark" style="position: sticky; left: 0; background: #fff; z-index: 1; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">
                                                {{ $employee->full_name }}
                                            </td>

                                            @foreach($dates as $date)
                                                @php
                                                    $isSunday = \Carbon\Carbon::parse($date)->isSunday();
                                                    $isHoliday = array_key_exists($date, $holidays);
                                                    $holidayName = $isHoliday ? $holidays[$date] : '';
                                                    $shiftName = $assignmentsData[$employee->id][$date] ?? '-';

                                                    // 🌟 KODE ANDA TETAP DIPERTAHANKAN UTUH DI SINI:
                                                    // Tentukan warna badge berdasarkan jenis shift kerja
                                                    $badgeClass = 'bg-light text-muted';
                                                    if (str_contains(strtolower($shiftName), '1')) {
                                                        $badgeClass = 'badge bg-primary me-1';
                                                    } elseif (str_contains(strtolower($shiftName), '2')) {
                                                        $badgeClass = 'badge bg-success me-1';
                                                    } elseif (str_contains(strtolower($shiftName), '3')) {
                                                        $badgeClass = 'badge bg-purple me-1';
                                                    } elseif ($shiftName != '-') {
                                                        $badgeClass = 'badge bg-danger me-1';
                                                    }
                                                @endphp

                                                {{-- Kolom td akan otomatis memerah samar jika Minggu ATAU Hari Libur Nasional --}}
                                                <td class="text-center @if($isSunday || $isHoliday) bg-danger-subtle text-danger @endif"
                                                    @if($isHoliday) data-bs-toggle="tooltip" title="Libur Nasional: {{ $holidayName }}" @endif>
                                                    
                                                    @if($isHoliday && $shiftName == '-')
                                                        <span class="fw-bold fs-11 text-danger" style="cursor: help;">HOL</span>
                                                    @else
                                                        <span class="{{ $badgeClass }} fs-11 fw-medium">{{ $shiftName }}</span>
                                                    @endif
                                                    
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($dates) + 1 }}" class="text-center text-muted py-4">
                                                Tidak ada data karyawan ditemukan.
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