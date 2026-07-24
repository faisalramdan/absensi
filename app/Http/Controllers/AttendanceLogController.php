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


class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        // ❌ SALAH (Jangan dibatasi dengan where di sini, karena akan hilang jika perusahaan diganti)
        // $employees = Employee::where('company_id', $selectedCompany)->get();

        // ✅ BENAR (Ambil SEMUA karyawan aktif beserta company_id nya agar bisa difilter di JavaScript)
        $employees = Employee::orderBy('full_name', 'asc')->get(['id', 'nik', 'full_name', 'company_id']);

        $selectedCompany = $request->input('company_id');
        $selectedEmployee = $request->input('employee_id');
        $selectedMonth = $request->input('month', Carbon::now()->format('m'));
        $selectedYear = $request->input('year', Carbon::now()->format('Y'));

        // 3. Hitung rentang tanggal (Cut-off: 26 Bulan Lalu s/d 25 Bulan Terpilih)
        $dateSelected = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        $startDate = $dateSelected->copy()->subMonth()->day(26)->startOfDay();
        $endDate = $dateSelected->copy()->day(25)->endOfDay();

        // Format text untuk ditampilkan di bawah dropdown bulan (Locale Indonesia)
        Carbon::setLocale('id');
        $dateRangeText = $startDate->translatedFormat('j F') . ' - ' . $endDate->translatedFormat('j F');

        // 4. Query Attendance Logs
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
            'selectedCompany',   // <-- Jangan lupa sertakan ini ke compact
            'selectedEmployee',
            'dateRangeText'
        ));
    }

    public function create()
    {
        $employees = Employee::orderBy(
            'full_name'
        )->get();

        return view(
            'attendance-logs.create',
            compact('employees')
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'employee_id' => 'required',

            'date' => 'required|date',

            'check_in' => 'nullable',

            'check_out' => 'nullable',

        ]);

        AttendanceLog::create([

            'employee_id' => $request->employee_id,

            'date' => $request->date,

            'check_in' => $request->check_in,

            'check_out' => $request->check_out,

            'source' => 'manual',

            'notes' => $request->notes,

            'created_by' => Auth::user()->employee?->id,


        ]);

        return redirect()
            ->route(
                'attendance-logs.index'
            )
            ->with(
                'success',
                'Attendance Log berhasil ditambahkan.'
            );
    }

    public function edit(
        AttendanceLog $attendanceLog
    ) {
        $employees = Employee::orderBy(
            'full_name'
        )->get();

        return view(
            'attendance-logs.edit',
            compact(
                'attendanceLog',
                'employees'
            )
        );
    }

    public function update(
        Request $request,
        AttendanceLog $attendanceLog
    ) {
        $request->validate([

            'employee_id' => 'required',

            'date' => 'required|date',

            'check_in' => 'nullable',

            'check_out' => 'nullable',

        ]);

        $attendanceLog->update([

            'employee_id' => $request->employee_id,

            'date' => $request->date,

            'check_in' => $request->check_in,

            'check_out' => $request->check_out,

            'notes' => $request->notes,

            'updated_by' => Auth::user()->employee?->id,

        ]);

        return redirect()
            ->route(
                'attendance-logs.index'
            )
            ->with(
                'success',
                'Attendance Log berhasil diubah.'
            );
    }

    public function destroy(
        AttendanceLog $attendanceLog
    ) {
        $attendanceLog->delete();

        return redirect()
            ->route(
                'attendance-logs.index'
            )
            ->with(
                'success',
                'Attendance Log berhasil dihapus.'
            );
    }

    public function importForm()
    {
        return view(
            'attendance-logs.import'
        );
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

        return redirect()
            ->route('attendance-logs.index')
            ->with(
                'success',
                'Attendance berhasil diimport.'
            );
    }



}