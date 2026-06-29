<?php

namespace App\Http\Controllers;

use App\Models\EmployeeContract;
use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveAllocationController extends Controller
{
    public function index(EmployeeContract $employeeContract)
    {
        $allocations = $employeeContract
            ->leaveAllocations()
            ->with([
                'leaveType',
                'creator',
                'updater',
            ])
            ->paginate(10);

        return view(
            'leave-allocations.index',
            compact(
                'employeeContract',
                'allocations'
            )
        );
    }

    public function create(EmployeeContract $employeeContract)
    {
        $leaveTypes = LeaveType::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'leave-allocations.create',
            compact(
                'employeeContract',
                'leaveTypes'
            )
        );
    }

    public function store(Request $request)
    {
        $contractId = $request->input('employee_contract_id');

        $request->validate([
            'employee_contract_id' => 'required|exists:employee_contracts,id',
            'allocations' => 'required|array',
            'allocations.*.leave_type_id' => 'required|exists:leave_types,id',
            'allocations.*.allocated_days' => 'required|numeric|min:0',
            'allocations.*.notes' => 'nullable|string',
        ]);

        // Tambahkan log sementara untuk debugging jika nanti masih belum masuk
        // \Log::info('Data Allocations Masuk:', $request->all());

        if (!auth()->user()->employee) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Akun Anda belum terhubung dengan data Karyawan.');
        }

        $contract = EmployeeContract::findOrFail($contractId);
        $creatorId = auth()->user()->employee->id;
        $savedCount = 0;

        foreach ($request->allocations as $item) {
            // PERUBAHAN LOGIKA: 
            // Kita cek apakah baris ini dipilih (checkbox dicentang) ATAU kuotanya diisi lebih dari 0
            $isChosen = isset($item['selected']) && $item['selected'] == '1';

            if ($isChosen && $item['allocated_days'] >= 0) {

                LeaveAllocation::updateOrCreate(
                    [
                        'employee_contract_id' => $contract->id,
                        'leave_type_id' => $item['leave_type_id'],
                    ],
                    [
                        'allocated_days' => $item['allocated_days'],
                        'remaining_days' => $item['allocated_days'], // sisa hari awal = total alokasi
                        'used_days' => 0, // pastikan default nilai terisi jika kolom tidak nullable
                        'notes' => $item['notes'] ?? null,
                        'created_by' => $creatorId,
                        'updated_by' => $creatorId,
                    ]
                );
                $savedCount++;
            }
        }

        if ($savedCount === 0) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Tidak ada jenis cuti yang dipilih untuk dialokasikan.');
        }

        return redirect()
            ->to(route('employee-contracts.show', $contract->id) . '#leave-allocation-section')
            ->with('success', "$savedCount alokasi jatah cuti berhasil dikonfigurasi.");
    }

    public function update(Request $request, LeaveAllocation $leaveAllocation)
    {
        $request->validate([
            'allocated_days' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (!auth()->user()->employee) {
            return redirect()->back()->with('error', 'Gagal memperbarui. Akun Anda belum terhubung dengan data Karyawan.');
        }

        $allocated = $request->allocated_days;
        $used = $leaveAllocation->used_days;

        if ($allocated < $used) {
            return redirect()->back()->with('error', "Gagal memperbarui. Jumlah alokasi baru ($allocated hari) tidak boleh lebih kecil dari yang sudah digunakan ($used hari).");
        }

        $leaveAllocation->update([
            'allocated_days' => $allocated,
            'remaining_days' => $allocated - $used,
            'notes' => $request->notes,
            'updated_by' => auth()->user()->employee->id,
        ]);

        return redirect()
            ->to(route('employee-contracts.show', $leaveAllocation->employee_contract_id) . '#leave-allocation-section')
            ->with('success', 'Alokasi jatah cuti berhasil diperbarui.');
    }

    public function destroy(LeaveAllocation $leaveAllocation)
    {
        if ($leaveAllocation->used_days > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus. Kuota cuti ini sudah mulai digunakan oleh karyawan.');
        }

        $contractId = $leaveAllocation->employee_contract_id;
        $leaveAllocation->delete();

        return redirect()
            ->to(route('employee-contracts.show', $contractId) . '#leave-allocation-section')
            ->with('success', 'Alokasi jatah cuti berhasil dihapus.');
    }


    public function show(
        EmployeeContract $employeeContract,
        LeaveAllocation $leaveAllocation
    ) {
        return view(
            'leave-allocations.show',
            compact(
                'employeeContract',
                'leaveAllocation'
            )
        );
    }

    public function edit(
        EmployeeContract $employeeContract,
        LeaveAllocation $leaveAllocation
    ) {
        $leaveTypes = LeaveType::where(
            'is_active',
            true
        )->orderBy('name')->get();

        return view(
            'leave-allocations.edit',
            compact(
                'employeeContract',
                'leaveAllocation',
                'leaveTypes'
            )
        );
    }


}
