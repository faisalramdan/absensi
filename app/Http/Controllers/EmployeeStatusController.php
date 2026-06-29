<?php

namespace App\Http\Controllers;

use App\Models\EmployeeStatus;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\ActivityLogger;

class EmployeeStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = EmployeeStatus::query();

        if ($request->filled('search')) {

            $statuses->where(function ($q) use ($request) {

                $q->where('name', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('code', 'ILIKE', '%' . $request->search . '%');

            });
        }

        if ($request->filled('status')) {

            $statuses->where(
                'is_active',
                $request->status
            );
        }

        $statuses = $statuses
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'employee-statuses.index',
            compact('statuses')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee-statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:50|unique:employee_statuses,code',
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);



        $status = EmployeeStatus::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        ActivityLogger::log(
            'Employee Status',
            'Create',
            'Menambahkan status karyawan: ' . $status->name
        );

        return redirect()
            ->route('employee-statuses.index')
            ->with('success', 'Status karyawan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeStatus $employeeStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeStatus $employeeStatus)
    {
        return view(
            'employee-statuses.edit',
            [
                'status' => $employeeStatus
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        EmployeeStatus $employeeStatus
    ) {
        $request->validate([
            'code' => 'required|max:50|unique:employee_statuses,code,' . $employeeStatus->id,
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $oldData = $employeeStatus->getOriginal();

        $employeeStatus->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),

            'updated_by' => auth()->id(),
        ]);
        ActivityLogger::log(
            'Employee Status',
            'Update',
            'Mengubah status ' . $employeeStatus->name,
            $oldData,
            $employeeStatus->fresh()->toArray()
        );

        return redirect()
            ->route('employee-statuses.index')
            ->with('success', 'Status karyawan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeStatus $employeeStatus)
    {
        $oldData = $employeeStatus->toArray();
        $name = $employeeStatus->name;
        $employeeStatus->delete();

        ActivityLogger::log(
            'Employee Status',
            'Delete',
            'Menghapus status ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('employee-statuses.index')
            ->with('success', 'Status karyawan berhasil dihapus');
    }
}