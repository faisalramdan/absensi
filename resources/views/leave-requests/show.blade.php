@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')

    <div class="wrapper">

        <div class="page-content">

            <div class="container-xxl">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold">
                            Detail Pengajuan Cuti / Izin
                        </h3>
                        <p class="text-muted mb-0">
                            Informasi lengkap pengajuan anda terkait Cuti dan Izin
                        </p>
                    </div>

                    <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold"></iconify-icon>
                        Kembali
                    </a>
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">

                            <div class="card-header">
                                <h4 class="card-title">
                                    Informasi Detail
                                </h4>
                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="30%">Jenis Cuti</th>
                                        <td class="text-primary fw-semibold">{{ $leaveRequest->leaveType?->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Mulai</th>
                                        <td>
                                            {{ \Carbon\Carbon::parse($leaveRequest->start_date)->locale('id')->translatedFormat('l') }}
                                            -
                                            {{ \Carbon\Carbon::parse($leaveRequest->start_date)->locale('id')->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Selesai</th>
                                        <td>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->locale('id')->translatedFormat('l') }}
                                            -
                                            {{ \Carbon\Carbon::parse($leaveRequest->end_date)->locale('id')->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Total Hari</th>
                                        <td>{{ $leaveRequest->total_days }}</td>
                                    </tr>

                                    <tr>
                                        <th>Alasan</th>
                                        <td>{{ $leaveRequest->reason }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lampiran</th>
                                        <td>
                                            @if($leaveRequest->attachment)
                                                <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank"
                                                    class="text-primary fw-semibold">
                                                    <button type="button" class="btn btn-info">
                                                        <i class="bx bx-paperclip me-1"></i>Lihat lampiran
                                                    </button>
                                                </a>
                                            @else
                                                Tidak ada
                                            @endif
                                        </td>
                                    </tr>

                                </table>
                            </div>

                            @if($leaveRequest->canApprove(auth()->user()->employee))

                                <hr>

                                <div class="text-end">

                                    <form action="{{ route('leave-requests.approve', $leaveRequest) }}" method="POST"
                                        class="d-inline">

                                        @csrf

                                        <button class="btn btn-success">

                                            Approve

                                        </button>

                                    </form>

                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">

                                        Reject

                                    </button>

                                </div>

                            @endif

                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Approval Informasi
                                </h4>
                            </div>

                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Status Approval</th>
                                        <td>

                                            @if($leaveRequest->status == 'pending')
                                                <span class="badge bg-warning">
                                                    <h1>Pending</h1>
                                                </span>
                                            @elseif($leaveRequest->status == 'approved')
                                                <span class="badge bg-success">
                                                    <h1>Approved</h1>
                                                </span>
                                            @elseif($leaveRequest->status == 'rejected')
                                                <span class="badge bg-danger">
                                                    <h1>Rejected</h1>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Catatan Approval</th>
                                        <td>
                                            {{ $leaveRequest->approval_notes ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Disetujui Oleh</th>
                                        <td class="text-primary fw-semibold">
                                            {{ $leaveRequest->approver?->full_name ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Approval</th>
                                        <td class="text-primary fw-semibold">
                                            {{ $leaveRequest->approved_at ? \Carbon\Carbon::parse($leaveRequest->approved_at)->format('d M Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection