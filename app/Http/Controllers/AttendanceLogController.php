<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceLogImport;
use Carbon\Carbon;
use App\Helpers\ActivityLogger; // Pastikan namespace ActivityLogger disesuaikan dengan struktur project Anda

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        // Ambil SEMUA karyawan aktif beserta company_id nya agar bisa difilter di JavaScript
        $employees = Employee::orderBy('full_name', 'asc')->get(['id', 'nik', 'full_name', 'company_id']);

        $selectedCompany = $request->input('company_id');
        $selectedEmployee = $request->input('employee_id');
        $selectedMonth = $request->input('month', Carbon::now()->format('m'));
        $selectedYear = $request->input('year', Carbon::now()->format('Y'));

        // Hitung rentang tanggal (Cut-off: 26 Bulan Lalu s/d 25 Bulan Terpilih)
        $dateSelected = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        $startDate = $dateSelected->copy()->subMonth()->day(26)->startOfDay();
        $endDate = $dateSelected->copy()->day(25)->endOfDay();

        // Format text untuk ditampilkan di bawah dropdown bulan (Locale Indonesia)
        Carbon::setLocale('id');
        $dateRangeText = $startDate->translatedFormat('j F') . ' - ' . $endDate->translatedFormat('j F');

        // Query Attendance Logs
        $attendanceLogs = AttendanceLog::query();

        // Filter berdasarkan Perusahaan (melalui relasi employee)
        if (!empty($selectedCompany)) {
            $attendanceLogs->whereHas('employee', function ($query) use ($selectedCompany) {
                $query->where('company_id', $selectedCompany);
            });
        }

        // Filter berdasarkan Karyawan (jika pilih karyawan tertentu)
        if (!empty($selectedEmployee)) {
            $attendanceLogs->where('employee_id', $selectedEmployee);
        }

        // Filter berdasarkan rentang tanggal dari bulan & tahun yang dipilih
        $attendanceLogs->whereBetween('date', [$startDate, $endDate]);

        // Eksekusi query & pagination
        $attendanceLogs = $attendanceLogs
            ->with(['employee.company']) // Load relasi employee beserta company-nya agar tidak N+1 query
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        return view('attendance-logs.index', compact(
            'attendanceLogs',
            'companies',
            'employees',
            'selectedMonth',
            'selectedYear',
            'selectedCompany',
            'selectedEmployee',
            'dateRangeText'
        ));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::orderBy('full_name')->get();

        // Default company ID sesuai user yang login (jika relasi user->employee->company_id ada)
        $defaultCompanyId = auth()->user()->employee->company_id ?? null;

        return view('attendance-logs.create', compact('employees', 'companies', 'defaultCompanyId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
        ]);

        $attendanceLog = AttendanceLog::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'source' => 'manual',
            'notes' => $request->notes,
            'created_by' => Auth::user()->employee?->id,
        ]);

        // --- MENCATAT LOG ACTIVITY CREATE ---
        $employee = Employee::find($request->employee_id);
        ActivityLogger::log(
            'Attendance Log',
            'Create',
            'Menambahkan log absensi manual untuk karyawan: ' . ($employee ? $employee->full_name : 'Unknown'),
            [],
            $attendanceLog->toArray()
        );
        // ------------------------------------

        return redirect()->route('attendance-logs.index')
            ->with('success', 'Attendance Log berhasil ditambahkan.');
    }

    public function edit(AttendanceLog $attendanceLog)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get(); // <-- Tambahkan ini

        $employees = Employee::orderBy('full_name')->get();

        return view(
            'attendance-logs.edit',
            compact(
                'attendanceLog',
                'employees',
                'companies' // <-- Jangan lupa masukkan ke compact
            )
        );
    }

    public function update(Request $request, AttendanceLog $attendanceLog)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
        ]);

        // --- SIMPAN DATA LAMA SEBELUM UPDATE ---
        $oldData = $attendanceLog->toArray();
        // ---------------------------------------

        $attendanceLog->update([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'notes' => $request->notes,
            'updated_by' => Auth::user()->employee?->id,
        ]);

        // --- MENCATAT LOG ACTIVITY UPDATE ---
        $employee = Employee::find($request->employee_id);
        ActivityLogger::log(
            'Attendance Log',
            'Update',
            'Mengubah data log absensi untuk karyawan: ' . ($employee ? $employee->full_name : 'Unknown'),
            $oldData,
            $attendanceLog->refresh()->toArray()
        );
        // ------------------------------------

        return redirect()->route('attendance-logs.index')
            ->with('success', 'Attendance Log berhasil diubah.');
    }

    public function destroy(AttendanceLog $attendanceLog)
    {
        // --- SIMPAN DATA LAMA SEBELUM DELETE ---
        $oldData = $attendanceLog->toArray();
        $employeeName = $attendanceLog->employee ? $attendanceLog->employee->full_name : 'Unknown';
        // ---------------------------------------

        $attendanceLog->delete();

        // --- MENCATAT LOG ACTIVITY DELETE ---
        ActivityLogger::log(
            'Attendance Log',
            'Delete',
            'Menghapus log absensi untuk karyawan: ' . $employeeName,
            $oldData,
            []
        );
        // ------------------------------------

        return redirect()->route('attendance-logs.index')
            ->with('success', 'Attendance Log berhasil dihapus.');
    }

    public function importForm()
    {
        return view('attendance-logs.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        Excel::import(
            new AttendanceLogImport,
            $request->file('file')
        );

        // --- MENCATAT LOG ACTIVITY IMPORT ---
        ActivityLogger::log(
            'Attendance Log',
            'Import',
            'Melakukan import data absensi melalui file excel: ' . $request->file('file')->getClientOriginalName(),
            [],
            []
        );
        // ------------------------------------

        return redirect()->route('attendance-logs.index')
            ->with('success', 'Attendance berhasil diimport.');
    }
}