<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Position;
use App\Models\EmployeeStatus;
use App\Models\emergencyContact;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $employeeStatuses = EmployeeStatus::orderBy('name')->get();

        $employees = Employee::query();

        if ($request->filled('search')) {
            $employees->where(function ($q) use ($request) {
                $q->where('nik', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('full_name', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'ILIKE', '%' . $request->search . '%');
            });
        }


        // Filter Aktif / Non Aktif
        if ($request->filled('status')) {
            $employees->where(
                'is_active',
                $request->status
            );
        }

        $employees = $employees
            ->with([
                'company',
                'position',
                'status',
                'role',
                'creator',
                'updater'
            ])
            ->orderBy('join_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'employees.index',
            compact('employees', 'employeeStatuses')
        );
    }

    public function create()
    {
        return view('employees.create', [
            'companies' => Company::where('is_active', true)->orderBy('name')->get(),
            'positions' => Position::where('is_active', true)->orderBy('name')->get(),
            'statuses' => EmployeeStatus::where('is_active', true)->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:employees,nik',

            'full_name' => 'required|max:255',

            'email' => 'nullable|email',

            'photo' => 'nullable|image|max:2048',

            'emergency_name.*' => 'nullable|string|max:100',
            'emergency_relationship.*' => 'nullable|string|max:50',
            'emergency_phone.*' => 'nullable|string|max:20',

        ]);

        $photo = null;

        if ($request->hasFile('photo')) {

            $photo = $request
                ->file('photo')
                ->store('employees', 'public');

        }

        // Simpan Employee dulu
        $employee = Employee::create([

            'nik' => $request->nik,
            'full_name' => $request->full_name,

            'email' => $request->email,
            'phone' => $request->phone,

            'gender' => $request->gender,

            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,

            'education' => $request->education,

            'photo' => $photo,

            'ktp_number' => $request->ktp_number,
            'address' => $request->address,

            'company_id' => $request->company_id,
            'position_id' => $request->position_id,

            'role_id' => $request->role_id,
            'user_id' => $request->user_id,

            'join_date' => $request->join_date,

            'is_active' => $request->boolean('is_active'),

            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $user = User::create([
            'name' => $employee->full_name,
            'email' => $employee->email,
            'password' => Hash::make($employee->nik),
            'is_active' => $request->boolean('is_active'),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),

        ]);

        if ($employee->role_id) {
            $role = Role::find($employee->role_id);
            if ($role) {
                $user->assignRole($role);
            }
        }

        $employee->update([
            'user_id' => $user->id,
        ]);

        // Simpan Kontak Darurat
        if ($request->filled('emergency_name')) {

            foreach ($request->emergency_name as $key => $name) {

                if (empty($name)) {
                    continue;
                }

                $employee->emergencyContacts()->create([

                    'name' => $name,

                    'relationship' => $request->emergency_relationship[$key] ?? null,

                    'phone' => $request->emergency_phone[$key] ?? null,

                ]);
            }
        }

        ActivityLogger::log(
            'User',
            'Create',
            'Membuat akun login untuk karyawan: ' . $employee->full_name,
            [],
            [
                'email' => $user->email,
                'role' => $role?->name,
            ]
        );

        ActivityLogger::log(
            'Employee',
            'Create',
            'Menambahkan karyawan: ' . $employee->full_name,
            [],
            $employee->toArray()
        );

        return redirect()
            ->route('employees.index')
            ->with(
                'success',
                'Karyawan berhasil dibuat. Password awal menggunakan NIK.'
            );
    }



    public function edit(Employee $employee)
    {
        $employee->load('emergencyContacts');

        return view('employees.edit', [
            'employee' => $employee,
            'companies' => Company::where('is_active', true)->orderBy('name')->get(),
            'positions' => Position::where('is_active', true)->orderBy('name')->get(),
            'statuses' => EmployeeStatus::where('is_active', true)->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),

        ]);
    }

    public function update(
        Request $request,
        Employee $employee
    ) {

        $request->validate([

            'nik' => 'required|unique:employees,nik,' . $employee->id,
            'full_name' => 'required|max:255',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|max:2048',
            'emergency_name.*' => 'nullable|string|max:100',
            'emergency_relationship.*' => 'nullable|string|max:50',
            'emergency_phone.*' => 'nullable|string|max:20',

        ]);

        $oldData = $employee->toArray();

        $photo = $employee->photo;

        if ($request->hasFile('photo')) {

            if (
                $employee->photo &&
                Storage::disk('public')->exists($employee->photo)
            ) {

                Storage::disk('public')
                    ->delete($employee->photo);

            }

            $photo = $request
                ->file('photo')
                ->store('employees', 'public');
        }

        $employee->update([

            'nik' => $request->nik,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'education' => $request->education,
            'photo' => $photo,
            'ktp_number' => $request->ktp_number,
            'address' => $request->address,
            'company_id' => $request->company_id,
            'position_id' => $request->position_id,
            'role_id' => $request->role_id,
            'join_date' => $request->join_date,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ]);

        if ($employee->user) {
            $employee->user->update([
                'name' => $employee->full_name,
                'email' => $employee->email,
                'is_active' => $employee->is_active,
                'updated_by' => auth()->id(),
            ]);
        }

        if ($employee->user && $employee->role_id) {
            $role = Role::find($employee->role_id);
            if ($role) {
                $employee
                    ->user
                    ->syncRoles([$role->name]);
            }
        }
        // Hapus kontak lama
        $employee->emergencyContacts()->delete();

        // Simpan ulang kontak terbaru
        if ($request->filled('emergency_name')) {

            foreach ($request->emergency_name as $key => $name) {

                if (empty($name)) {
                    continue;
                }

                $employee->emergencyContacts()->create([

                    'name' => $name,

                    'relationship' => $request->emergency_relationship[$key] ?? null,

                    'phone' => $request->emergency_phone[$key] ?? null,

                ]);
            }
        }

        ActivityLogger::log(
            'Employee',
            'Update',
            'Mengubah karyawan: ' . $employee->full_name,
            $oldData,
            $employee->fresh()->toArray()
        );

        return redirect()
            ->route('employees.index')
            ->with(
                'success',
                'Karyawan berhasil diperbarui'
            );
    }

    public function destroy(Employee $employee)
    {
        $oldData = $employee->toArray();

        $name = $employee->full_name;

        if (
            $employee->photo &&
            Storage::disk('public')->exists($employee->photo)
        ) {

            Storage::disk('public')
                ->delete($employee->photo);

        }

        $employee->delete();

        ActivityLogger::log(
            'Employee',
            'Delete',
            'Menghapus karyawan: ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('employees.index')
            ->with(
                'success',
                'Karyawan berhasil dihapus'
            );
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'company',
            'position',
            'status',
            'role',
            'user',
            'creator',
            'updater',
            'emergencyContacts'
        ]);

        return view(
            'employees.show',
            compact('employee')
        );
    }
}