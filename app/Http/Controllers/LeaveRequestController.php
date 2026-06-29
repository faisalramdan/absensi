<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Akun Anda belum terhubung dengan data karyawan.');
        }

        $leaveRequests = LeaveRequest::query()
            ->with(['employee', 'leaveType', 'approver'])
            ->where('employee_id', $employee->id);

        if ($request->filled('type')) {
            $leaveRequests->where('leave_type_id', $request->type);
        }

        if ($request->filled('status')) {
            $leaveRequests->where('status', $request->status);
        }

        $leaveRequests = $leaveRequests
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // ini penting
        $leaveTypes = LeaveType::all();

        return view('leave-requests.index', compact('leaveRequests', 'leaveTypes'));
    }

    public function create()
    {
        // 1. Ambil data karyawan yang sedang login
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'Gagal memuat formulir. Akun Anda belum terhubung dengan data Karyawan.');
        }

        // 2. Ambil data kontrak karyawan yang saat ini statusnya Aktif ('t') yang terbaru
        $activeContract = \App\Models\EmployeeContract::where('employee_id', $employee->id)
            ->where('is_active', 't')
            ->latest()
            ->first();

        // 3. Jika kontrak aktif ditemukan, ambil jatah alokasi cutinya. Jika tidak ada, buat koleksi kosong.
        if ($activeContract) {
            $leaveAllocations = \App\Models\LeaveAllocation::where('employee_contract_id', $activeContract->id)
                ->with('leaveType')
                ->get();
        } else {
            $leaveAllocations = collect();
        }

        // 4. Lempar variabel ke view blade leave-requests/create.blade.php
        return view(
            'leave-requests.create',
            compact(
                'employee',
                'leaveAllocations'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $employee = auth()->user()->employee;

        if (!$employee) {
            return back()->with(
                'error',
                'Data karyawan tidak ditemukan.'
            );
        }

        // 1. Cari kontrak aktif karyawan ini untuk mencocokkan alokasi cutinya
        $activeContract = \App\Models\EmployeeContract::where('employee_id', $employee->id)
            ->where('is_active', 't')
            ->latest()
            ->first();

        if (!$activeContract) {
            return back()->withInput()->withErrors([
                'leave_type_id' => 'Anda tidak memiliki kontrak aktif yang terdaftar.'
            ]);
        }

        // 2. Ambil data alokasi jatah cuti dari kontrak aktif tersebut
        $allocation = \App\Models\LeaveAllocation::where('employee_contract_id', $activeContract->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->first();

        if (!$allocation) {
            return back()->withInput()->withErrors([
                'leave_type_id' => 'Jatah cuti untuk jenis ini belum dialokasikan oleh HRD pada kontrak Anda.'
            ]);
        }

        // Hitung jumlah hari pengajuan
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $totalDays = $start->diffInDays($end) + 1;

        // Ambil sisa cuti langsung dari kolom remaining_days di tabel alokasi
        $remainingLeave = floatval($allocation->remaining_days);

        // Jika kuota sudah habis
        if ($remainingLeave <= 0) {
            return back()->withInput()->withErrors([
                'leave_type_id' => 'Kuota cuti Anda sudah habis.'
            ]);
        }

        // Jika pengajuan melebihi sisa cuti
        if ($totalDays > $remainingLeave) {
            return back()->withInput()->withErrors([
                'leave_type_id' => 'Sisa cuti Anda hanya ' . $remainingLeave . ' hari. Pengajuan Anda: ' . $totalDays . ' hari.'
            ]);
        }

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store(
                'leave-requests',
                'public'
            );
        }

        // 3. Simpan data pengajuan cuti menggunakan employee_id
        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment' => $attachment,
            'status' => 'pending',
            'created_by' => $employee->id, // Diubah ke employee_id
            'updated_by' => $employee->id, // Diubah ke employee_id
        ]);

        ActivityLogger::log(
            'Leave Request',
            'Create',
            'Mengajukan cuti/izin',
            [],
            $leaveRequest->toArray()
        );

        return redirect()
            ->route('leave-requests.index')
            ->with(
                'success',
                'Pengajuan berhasil dibuat.'
            );
    }

    public function show(
        LeaveRequest $leaveRequest
    ) {

        $leaveRequest->load([
            'employee',
            'leaveType',
            'approver'
        ]);

        return view(
            'leave-requests.show',
            compact('leaveRequest')
        );
    }

    public function edit(
        LeaveRequest $leaveRequest
    ) {

        if ($leaveRequest->status != 'pending') {

            return back()->with(
                'error',
                'Pengajuan yang sudah diproses tidak dapat diubah.'
            );

        }

        $leaveTypes = LeaveType::where(
            'is_active',
            true
        )->get();

        return view(
            'leave-requests.edit',
            compact(
                'leaveRequest',
                'leaveTypes'
            )
        );
    }

    public function update(
        Request $request,
        LeaveRequest $leaveRequest
    ) {

        if ($leaveRequest->status != 'pending') {

            return back()->with(
                'error',
                'Pengajuan yang sudah diproses tidak dapat diubah.'
            );

        }

        $request->validate([

            'leave_type_id' => 'required|exists:leave_types,id',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after_or_equal:start_date',

            'reason' => 'required',

            'attachment' => 'nullable|file|max:2048',
        ]);

        $oldData = $leaveRequest->toArray();

        $attachment = $leaveRequest->attachment;

        if ($request->hasFile('attachment')) {

            if (
                $leaveRequest->attachment &&
                Storage::disk('public')->exists(
                    $leaveRequest->attachment
                )
            ) {

                Storage::disk('public')
                    ->delete(
                        $leaveRequest->attachment
                    );

            }

            $attachment = $request
                ->file('attachment')
                ->store(
                    'leave-requests',
                    'public'
                );

        }

        $start = Carbon::parse(
            $request->start_date
        );

        $end = Carbon::parse(
            $request->end_date
        );

        $totalDays = $start
            ->diffInDays($end) + 1;

        $leaveRequest->update([

            'leave_type_id' => $request->leave_type_id,

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

            'total_days' => $totalDays,

            'reason' => $request->reason,

            'attachment' => $attachment,

            'updated_by' => auth()->id(),
        ]);

        ActivityLogger::log(
            'Leave Request',
            'Update',
            'Mengubah pengajuan cuti',
            $oldData,
            $leaveRequest->fresh()->toArray()
        );

        return redirect()
            ->route('leave-requests.index')
            ->with(
                'success',
                'Pengajuan berhasil diperbarui'
            );
    }

    public function destroy(
        LeaveRequest $leaveRequest
    ) {

        if ($leaveRequest->status != 'pending') {

            return back()->with(
                'error',
                'Pengajuan yang sudah diproses tidak dapat dihapus.'
            );

        }

        $oldData = $leaveRequest->toArray();

        if (
            $leaveRequest->attachment &&
            Storage::disk('public')->exists(
                $leaveRequest->attachment
            )
        ) {

            Storage::disk('public')
                ->delete(
                    $leaveRequest->attachment
                );

        }

        $leaveRequest->delete();

        ActivityLogger::log(
            'Leave Request',
            'Delete',
            'Menghapus pengajuan cuti',
            $oldData,
            []
        );

        return redirect()
            ->route('leave-requests.index')
            ->with(
                'success',
                'Pengajuan berhasil dihapus'
            );
    }

    public function approval()
    {
        $employee = auth()->user()->employee;
        $perPage = 10;

        // --- 1. PROSES DATA PENDING (TAB 1) ---
        $collectionPending = LeaveRequest::with([
            'employee',
            'leaveType'
        ])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->filter(function ($leave) use ($employee) {
                return $leave->canApprove($employee);
            })
            ->values();

        $pagePending = request()->get('pending_page', 1);

        $leaveRequests = new LengthAwarePaginator(
            $collectionPending->forPage($pagePending, $perPage),
            $collectionPending->count(),
            $perPage,
            $pagePending,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => 'pending_page', // Membuat URL menjadi ?pending_page=2
            ]
        );

        // --- 2. PROSES DATA HISTORY / RIWAYAT (TAB 2) ---
        $collectionHistory = LeaveRequest::with([
            'employee.activeTeam.team',
            'leaveType',
            'approver'
        ])
            ->whereIn('status', ['approved', 'rejected']) // Hanya yang sudah diproses
            ->latest('approved_at')
            ->get()
            ->filter(function ($leave) use ($employee) {
                // TRIKNYA DI SINI:
                // Kita buat tiruan (clone) object pengajuan ini, lalu ubah statusnya sementara menjadi 'pending'.
                // Mengapa? Agar fungsi $leave->canApprove($employee) bawaan sistem Anda 
                // tetap mendeteksi bahwa Anda (Leader) adalah bagian dari jalur organisasi pengajuan ini!
                $clonedLeave = clone $leave;
                $clonedLeave->status = 'pending';

                return $clonedLeave->canApprove($employee) || $leave->approved_by == $employee->id;
            })
            ->values();

        $pageHistory = request()->get('history_page', 1);

        $leaveHistory = new LengthAwarePaginator(
            $collectionHistory->forPage($pageHistory, $perPage),
            $collectionHistory->count(),
            $perPage,
            $pageHistory,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => 'history_page', // Membuat URL menjadi ?history_page=2
            ]
        );

        // --- 3. KIRIM KEDUA VARIABEL KE VIEW ---
        return view(
            'leave-requests.approval', // Pastikan folder dan nama file blade Anda sudah sesuai
            compact('leaveRequests', 'leaveHistory')
        );
    }


    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Ambil data employee milik user yang sedang login saat ini
        $approverEmployee = auth()->user()->employee;

        if (!$approverEmployee) {
            return redirect()->back()->with('error', 'Akun login Anda belum terhubung dengan data Employee!');
        }

        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        // Gunakan Database Transaction agar sinkronisasi data kuota aman dan tidak bentrok
        \DB::beginTransaction();

        try {
            // 1. Cari kontrak aktif milik karyawan yang mengajukan cuti
            $activeContract = \App\Models\EmployeeContract::where('employee_id', $leaveRequest->employee_id)
                ->where('is_active', 't')
                ->latest()
                ->first();

            if (!$activeContract) {
                return redirect()->back()->with('error', 'Gagal memproses. Karyawan yang mengajukan cuti tidak memiliki kontrak aktif.');
            }

            // 2. Cari baris alokasi kuota yang sesuai dengan tipe cuti yang diajukan
            $allocation = \App\Models\LeaveAllocation::where('employee_contract_id', $activeContract->id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->first();

            if (!$allocation) {
                return redirect()->back()->with('error', 'Gagal memproses. Jatah alokasi cuti untuk jenis ini tidak ditemukan pada kontrak karyawan.');
            }

            // 3. Validasi ulang sisa kuota (memastikan jatahnya masih cukup sebelum di-approve)
            if ($allocation->remaining_days < $leaveRequest->total_days) {
                return redirect()->back()->with('error', 'Gagal menyetujui. Sisa kuota cuti karyawan tidak mencukupi.');
            }

            // 4. Potong kuota di tabel Leave Allocation
            $allocation->update([
                'used_days' => $allocation->used_days + $leaveRequest->total_days,
                'remaining_days' => $allocation->remaining_days - $leaveRequest->total_days,
                'updated_by' => $approverEmployee->id,
            ]);

            // 5. Eksekusi update status pengajuan cuti menggunakan ID Employee (bukan ID User)
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $approverEmployee->id, // Menyimpan ID Employee (misal: 19)
                'approved_at' => now(),
                'approval_notes' => $request->input('approval_notes'),
                'updated_by' => $approverEmployee->id, // Menyimpan ID Employee yang sama
            ]);

            \DB::commit();
            return redirect()->back()->with('success', 'Pengajuan cuti berhasil disetujui dan kuota jatah cuti karyawan telah dipotong.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$leaveRequest->canApprove($employee)) {

            abort(403);

        }

        $request->validate([

            'approval_notes' => 'required|string'

        ]);

        $leaveRequest->update([

            'status' => 'rejected',

            'approved_by' => $employee->id,

            'approved_at' => now(),

            'approval_notes' => $request->approval_notes,

            'updated_by' => auth()->id(),

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Pengajuan berhasil ditolak.'
            );
    }
}