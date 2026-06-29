@extends('layouts.app')

@section('title', 'Approval Cuti/Izin')

@section('content')

    <!-- START Wrapper -->
    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">

                <!-- info/alert -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <!-- endinfo -->

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header d-flex flex-column justify-content-between">
                                <div class="mb-3">
                                    <h4 class="card-title">Approval & Riwayat Pengajuan Cuti/Izin</h4>
                                    <p class="text-muted mb-0">
                                        Kelola pengajuan cuti/izin karyawan yang berada di bawah jalur koordinasi Anda.
                                    </p>
                                </div>

                                <!-- Nav Tabs (Menggunakan format nav-justified Anda) -->
                                <ul class="nav nav-tabs nav-justified" role="tablist">
                                    <li class="nav-item">
                                        <a href="#pendingTabs" data-bs-toggle="tab" class="nav-link {{ request()->has('history_page') ? '' : 'active' }}">
                                            <span class="d-block d-sm-none"><i class="bx bx-time-five"></i></span>
                                            <span class="d-none d-sm-block">Pending Request ({{ $leaveRequests->total() }})</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#historyTabs" data-bs-toggle="tab" class="nav-link {{ request()->has('history_page') ? 'active' : '' }}">
                                            <span class="d-block d-sm-none"><i class="bx bx-history"></i></span>
                                            <span class="d-none d-sm-block">History Pengajuan</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content pt-2 text-muted">
                                    
                                    <!-- TAB 1: PENDING REQUEST -->
                                    <div class="tab-pane fade {{ request()->has('history_page') ? '' : 'show active' }}" id="pendingTabs">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Karyawan</th>
                                                        <th>Jenis</th>
                                                        <th>Periode</th>
                                                        <th>Hari</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($leaveRequests as $leave)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                {{ $leave->employee?->full_name }}
                                                                <br>
                                                                <small class="text-muted">{{ $leave->employee?->nik }}</small>
                                                            </td>
                                                            <td>{{ $leave->leaveType?->name }}</td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}<br>
                                                                s/d <br>
                                                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                                            </td>
                                                            <td>{{ $leave->total_days }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#approvalModal{{ $leave->id }}">
                                                                    <i class="bx bx-enter me-1"></i>Lihat pengajuan
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <!-- Modal Detail Approval -->
                                                        <div class="modal fade" id="approvalModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Persetujuan Pengajuan Cuti / Izin</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>

                                                                    <div class="modal-body text-start">
                                                                        <div class="row">
                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="fw-bold">Nama Karyawan</label>
                                                                                <div class="text-primary fw-semibold">{{ $leave->employee?->full_name }}</div>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="fw-bold">NIK</label>
                                                                                <div>{{ $leave->employee?->nik }}</div>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="fw-bold">Jenis Cuti</label>
                                                                                <div>{{ $leave->leaveType?->name }}</div>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="fw-bold">Total Hari</label>
                                                                                <div>{{ $leave->total_days }} Hari</div>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="fw-bold">Periode</label>
                                                                                <div>
                                                                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="fw-bold">Lampiran</label>
                                                                                <div>
                                                                                    @if($leave->attachment)
                                                                                        <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank">
                                                                                            <button type="button" class="btn btn-info btn-sm">
                                                                                                <i class="bx bx-paperclip me-1"></i>Lihat lampiran
                                                                                            </button>
                                                                                        </a>
                                                                                    @else
                                                                                        <span class="text-muted">Tidak ada</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12 mb-3">
                                                                                <label class="fw-bold">Alasan Pengajuan</label>
                                                                                <div class="border rounded p-3 bg-light text-primary fw-semibold">
                                                                                    {{ $leave->reason }}
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <label class="fw-bold">Catatan Approval</label>
                                                                                <textarea id="approvalNotes{{ $leave->id }}" class="form-control" rows="4" placeholder="Masukkan catatan approval (wajib jika menolak)..."></textarea>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Hidden Forms Container -->
                                                                        <form id="approveForm{{ $leave->id }}" action="{{ route('leave-requests.approve', $leave) }}" method="POST" class="d-none">
                                                                            @csrf
                                                                            <input type="hidden" name="approval_notes" id="approveNoteHidden{{ $leave->id }}">
                                                                        </form>

                                                                        <form id="rejectForm{{ $leave->id }}" action="{{ route('leave-requests.reject', $leave) }}" method="POST" class="d-none">
                                                                            @csrf
                                                                            <input type="hidden" name="approval_notes" id="rejectNoteHidden{{ $leave->id }}">
                                                                        </form>
                                                                    </div>

                                                                    <div class="modal-footer d-flex w-100">
                                                                        <div class="d-flex gap-2 mx-auto">
                                                                            <button type="button" class="btn btn-danger" onclick="submitReject({{ $leave->id }})">
                                                                                <i class="bx bx-x-circle me-1"></i>Reject
                                                                            </button>

                                                                            <button type="button" class="btn btn-success" onclick="submitApprove({{ $leave->id }})">
                                                                                <i class="bx bx-check-double me-1"></i>Approve
                                                                            </button>
                                                                        </div>
                                                                        <button type="button" class="btn btn-secondary ms-auto" data-bs-dismiss="modal">Batal</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Modal Detail Approval -->
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">Tidak ada pengajuan pending</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            {{ $leaveRequests->appends(request()->except('pending_page'))->links() }}
                                        </div>
                                    </div>

                                    <!-- TAB 2: HISTORY (APPROVED / REJECTED) -->
                                    <div class="tab-pane fade {{ request()->has('history_page') ? 'show active' : '' }}" id="historyTabs">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Karyawan</th>
                        <th>Leave</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Team</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveHistory as $history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $history->employee?->full_name }}</strong><br>
                                <small class="text-muted">{{ $history->employee?->nik }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $history->leaveType?->name }}</span><br>
                                <small>{{ $history->total_days }} Hari ({{ \Carbon\Carbon::parse($history->start_date)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($history->end_date)->format('d/m/y') }})</small>
                            </td>
                            <td>
                                @if($history->status === 'approved')
                                    <span class="badge bg-success-subtle text-success px-2 py-1">Approved</span>
                                @elseif($history->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">Rejected</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">{{ ucfirst($history->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $history->approver?->full_name ?? '-' }}<br>
                                 <small class="text-muted">{{ $history->approved_at ? \Carbon\Carbon::parse($history->approved_at)->format('d M Y H:i') : '-' }}</small>
                            </td>
                            <td>{{ $history->employee?->activeTeam?->team?->name ?? '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-light btn-sm border"
                                    data-bs-toggle="modal"
                                    data-bs-target="#historyModal{{ $history->id }}">
                                    <i class="bx bx-show me-1"></i>Detail
                                </button>
                            </td>
                        </tr>

                                                <div class="modal fade" id="historyModal{{ $history->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Riwayat Pengajuan Cuti / Izin</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body text-start">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Nama Karyawan</label>
                                                <div class="text-primary fw-semibold">{{ $history->employee?->full_name }}</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">NIK</label>
                                                <div>{{ $history->employee?->nik }}</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Jenis Cuti</label>
                                                <div>{{ $history->leaveType?->name }}</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Total Hari</label>
                                                <div>{{ $history->total_days }} Hari</div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Periode</label>
                                                <div>
                                                    {{ \Carbon\Carbon::parse($history->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($history->end_date)->format('d M Y') }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Lampiran</label>
                                                <div>
                                                    @if($history->attachment)
                                                        <a href="{{ asset('storage/' . $history->attachment) }}" target="_blank">
                                                            <button type="button" class="btn btn-info btn-sm">
                                                                <i class="bx bx-paperclip me-1"></i>Lihat lampiran
                                                            </button>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Tidak ada</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="fw-bold">Alasan Pengajuan</label>
                                                <div class="border rounded p-3 bg-light text-primary fw-semibold">
                                                    {{ $history->reason ?? 'Tidak ada alasan khusus yang dicantumkan.' }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Status Akhir</label>
                                                <div>
                                                    @if($history->status === 'approved')
                                                        <span class="badge bg-success text-white px-2 py-1">Approved</span>
                                                    @else
                                                        <span class="badge bg-danger text-white px-2 py-1">Rejected</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Diproses Oleh</label>
                                                <div>
                                                    <span class="text-dark fw-semibold">{{ $history->approver?->full_name ?? 'System/HR' }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $history->approved_at ? \Carbon\Carbon::parse($history->approved_at)->format('d M Y H:i') : '-' }}</small>
                                                </div>
                                            </div>

                                            <div class="col-12 text-start">
                                                <label class="fw-bold">Catatan Keputusan Atasan</label>
                                                <div class="border rounded p-3 {{ $history->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} fw-medium">
                                                    {{ $history->approval_notes ?? 'Tidak memberikan catatan tambahan.' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                                            @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada riwayat pengajuan</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $leaveHistory->appends(request()->except('history_page'))->links() }}
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
</div>

<script>
    function submitApprove(id) {
        let note = document.getElementById('approvalNotes' + id).value;
        document.getElementById("approveNoteHidden" + id).value = note;
        document.getElementById("approveForm" + id).submit();
    }

    function submitReject(id) {
        let note = document.getElementById('approvalNotes' + id).value;

        if (note.trim() === "") {
            alert("Alasan penolakan (Catatan Approval) wajib diisi.");
            return;
        }

        document.getElementById("rejectNoteHidden" + id).value = note;
        document.getElementById("rejectForm" + id).submit();
    }
</script>

@endsection