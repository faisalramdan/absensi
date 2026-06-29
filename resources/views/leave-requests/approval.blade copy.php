@extends('layouts.app')

@section('title', 'Approval Cuti')

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
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Approval Pengajuan Cuti</h4>
                                    <p class="text-muted mb-0">
                                        {{ $leaveRequests->total() }} pengajuan cuti/izin yang belum diproses. Silakan lakukan peninjauan untuk memberikan persetujuan atau penolakan.
                                    </p>
                                </div>
                            </div>

                            <div class="card-body">
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

                                                <div class="modal fade" id="approvalModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Persetujuan Pengajuan Cuti / Izin</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
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
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada pengajuan pending</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $leaveRequests->links() }}
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