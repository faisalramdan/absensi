<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
// Mempertahankan helper log aktivitas
use App\Helpers\ActivityLogger;

class EmployeeContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian dan filter status jika ada
        $search = $request->input('search');
        $selectedStatus = $request->input('employee_status_id');

        $contracts = EmployeeContract::with([
            'employee',
            'employeeStatus',
            'creator',
            'updater',
        ])
            // Logika Filter Pencarian (Nama, NIK, No Kontrak)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('contract_number', 'like', '%' . $search . '%')
                        ->orWhereHas('employee', function ($empQuery) use ($search) {
                            $empQuery->where('full_name', 'like', '%' . $search . '%')
                                ->orWhere('nik', 'like', '%' . $search . '%');
                        });
                });
            })
            // Logika Filter Berdasarkan Status Karyawan
            ->when($selectedStatus, function ($query) use ($selectedStatus) {
                $query->where('employee_status_id', $selectedStatus);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Memastikan parameter filter tidak hilang saat pindah halaman pagination

        // Disamakan menjadi $statusId agar cocok dengan variabel di blade: @foreach($statusId as $status)
        $statusId = EmployeeStatus::where('is_active', true)->orderBy('name')->get();

        return view('employee-contracts.index', compact('contracts', 'statusId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $statuses = EmployeeStatus::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employee-contracts.create', compact('employees', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'employee_status_id' => 'required|exists:employee_statuses,id',
            'contract_number' => 'nullable|string|max:100|unique:employee_contracts,contract_number',
            'end_date' => 'required|date|after_or_equal:start_date',
            'file_contract' => 'nullable|file|mimes:pdf|max:5120',
            'notes' => 'nullable|string',

            // VALIDASI TANGGAL MULAI (Harus setelah kontrak terakhir selesai)
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $employeeId = $request->employee_id;
                    $inputStartDate = $value;

                    if ($employeeId) {
                        // Cari kontrak terakhir milik karyawan ini (baik yang aktif maupun tidak)
                        $lastContract = EmployeeContract::where('employee_id', $employeeId)
                            ->orderBy('end_date', 'desc')
                            ->first();

                        // Jika ditemukan kontrak sebelumnya, tanggal mulai baru harus melewati tanggal selesai lama
                        if ($lastContract && $inputStartDate <= $lastContract->end_date) {
                            $formattedEndDate = \Carbon\Carbon::parse($lastContract->end_date)->format('d/m/Y');
                            $fail('Tanggal mulai kontrak baru harus setelah tanggal selesai kontrak sebelumnya (' . $formattedEndDate . ').');
                        }
                    }
                }
            ],
        ], [
            'contract_number.unique' => 'Nomor kontrak ini sudah terdaftar di sistem, silakan gunakan nomor lain.',
            'end_date.after_or_equal' => 'Tanggal selesai kontrak harus sama atau setelah tanggal mulai.',
        ]);

        // Cek keamanan relasi user login ke data employee
        if (!auth()->user()->employee) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Akun login Anda belum terhubung dengan data Karyawan (Employee).');
        }

        // Lolos validasi -> Non-aktifkan semua kontrak lama karyawan ini
        EmployeeContract::where('employee_id', $request->employee_id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $data = $request->all();

        if ($request->hasFile('file_contract')) {
            $data['file_contract'] = $request->file('file_contract')->store('contracts', 'public');
        }

        // Simpan log pencatat menggunakan ID Employee
        $data['created_by'] = auth()->user()->employee->id;
        $data['updated_by'] = auth()->user()->employee->id;
        $data['is_active'] = true; // Kontrak baru otomatis aktif

        $contract = EmployeeContract::create($data);

        // Ambil nama karyawan untuk keperluan deskripsi log
        $employeeName = $contract->employee?->full_name ?? 'N/A';

        // ACTIVITY LOG: Create
        ActivityLogger::log(
            'EmployeeContract',
            'Create',
            'Menambahkan kontrak baru dan menonaktifkan kontrak lama untuk karyawan: ' . $employeeName,
            [],
            $contract->toArray()
        );

        return redirect()
            ->route('employee-contracts.index')
            ->with('success', 'Kontrak baru berhasil ditambahkan dan kontrak sebelumnya telah dinonaktifkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeContract $employeeContract)
    {
        // 1. Ambil data alokasi cuti yang TERIKAT dengan kontrak ini
        // (Atau terikat ke employee_id, sesuaikan dengan struktur database Anda)
        $leaveAllocations = LeaveAllocation::where('employee_contract_id', $employeeContract->id)
            ->with('leaveType') // eager load untuk mengambil nama tipe cutinya
            ->get();

        // 2. Ambil semua master tipe cuti untuk pilihan di dropdown Modal text
        $leaveTypes = LeaveType::all();

        // 3. Lempar ketiga variabel tersebut ke view show
        return view('employee-contracts.show', compact(
            'employeeContract',
            'leaveAllocations',
            'leaveTypes'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeContract $employeeContract)
    {
        $employees = Employee::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $statuses = EmployeeStatus::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employee-contracts.edit', compact('employeeContract', 'employees', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeContract $employeeContract)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'employee_status_id' => 'required|exists:employee_statuses,id',
            'contract_number' => 'nullable|string|max:100|unique:employee_contracts,contract_number,' . $employeeContract->id,
            'end_date' => 'required|date|after_or_equal:start_date',
            'file_contract' => 'nullable|file|mimes:pdf|max:5120',
            'notes' => 'nullable|string',

            // VALIDASI PERIODE TANGGAL MULAI (Mencegah Overlapping dengan pengecualian ID saat ini)
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request, $employeeContract) {
                    $startDate = $value;
                    $endDate = $request->end_date;
                    $employeeId = $request->employee_id;

                    if ($endDate && $employeeId) {
                        $isOverlapping = EmployeeContract::where('employee_id', $employeeId)
                            ->where('id', '!=', $employeeContract->id) // Abaikan ID kontrak yang sedang diedit
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereBetween('start_date', [$startDate, $endDate])
                                    ->orWhereBetween('end_date', [$startDate, $endDate])
                                    ->orWhere(function ($q) use ($startDate, $endDate) {
                                        $q->where('start_date', '<=', $startDate)
                                            ->where('end_date', '>=', $endDate);
                                    });
                            })->exists();

                        if ($isOverlapping) {
                            $fail('Karyawan sudah memiliki kontrak aktif di dalam rentang tanggal tersebut. Silakan periksa kembali.');
                        }
                    }
                }
            ],
        ], [
            'contract_number.unique' => 'Nomor kontrak ini sudah terdaftar di sistem, silakan gunakan nomor lain.',
            'end_date.after_or_equal' => 'Tanggal selesai kontrak harus sama atau setelah tanggal mulai.',
        ]);

        // Cek keamanan relasi user login ke data employee
        if (!auth()->user()->employee) {
            return redirect()->back()->with('error', 'Gagal memperbarui. Akun login Anda belum terhubung dengan data Karyawan (Employee).');
        }

        // Backup data lama untuk log pembanding (Old Data)
        $oldData = $employeeContract->toArray();

        $data = $request->all();

        if ($request->hasFile('file_contract')) {
            // Opsional: hapus berkas lama dari media penyimpanan lokal jika ada file baru yang diunggah
            if ($employeeContract->file_contract) {
                Storage::disk('public')->delete($employeeContract->file_contract);
            }
            $data['file_contract'] = $request->file('file_contract')->store('contracts', 'public');
        }

        // Isi dengan ID Employee yang melakukan update
        $data['updated_by'] = auth()->user()->employee->id;

        $employeeContract->update($data);

        // Ambil nama karyawan terkait
        $employeeName = $employeeContract->employee?->full_name ?? 'N/A';

        // ACTIVITY LOG: Update (DIKEMBALIKAN & DIPERTAHANKAN)
        ActivityLogger::log(
            'EmployeeContract',
            'Update',
            'Memperbarui kontrak karyawan: ' . $employeeName,
            $oldData,
            $employeeContract->refresh()->toArray()
        );

        return redirect()
            ->route('employee-contracts.index')
            ->with('success', 'Kontrak berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeContract $employeeContract)
    {
        // Backup data lama dan nama karyawan sebelum record-nya dihapus dari database
        $oldData = $employeeContract->toArray();
        $employeeName = $employeeContract->employee?->full_name ?? 'N/A';

        // Opsional: Hapus file fisik dari storage saat data dihapus
        if ($employeeContract->file_contract) {
            Storage::disk('public')->delete($employeeContract->file_contract);
        }

        $employeeContract->delete();

        // ACTIVITY LOG: Delete (DIKEMBALIKAN & DIPERTAHANKAN)
        ActivityLogger::log(
            'EmployeeContract',
            'Delete',
            'Menghapus kontrak milik karyawan: ' . $employeeName,
            $oldData,
            []
        );

        return redirect()
            ->route('employee-contracts.index')
            ->with('success', 'Kontrak berhasil dihapus.');
    }
}