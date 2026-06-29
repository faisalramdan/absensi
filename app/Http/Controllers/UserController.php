<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Helpers\ActivityLogger;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with([
            'roles',
            'employee'
        ]);

        // Search nama / email
        if ($request->filled('search')) {
            $search = $request->search;

            $users->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $users->where('is_active', $request->status);
        }

        // Filter role
        if ($request->filled('role')) {
            $users->role($request->role);
        }

        $users = $users
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view(
            'users.index',
            compact('users', 'roles')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->boolean('is_active'),
            'created_by' => auth()->id(),
        ]);
        ActivityLogger::log(
            'User',
            'Create',
            'Menambahkan user: ' . $user->name,
            [],
            [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $request->roles ?? [],
                'is_active' => $user->is_active,
            ]
        );

        $user->syncRoles($request->roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        $employee = Employee::where(
            'user_id',
            $user->id
        )->first();

        return view(
            'users.edit',
            compact(
                'user',
                'roles',
                'employee'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'nullable|boolean',
        ]);
        $oldData = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'is_active' => $user->is_active ? 't' : 'f',
        ];

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => Carbon::now(),
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $employee = Employee::where(
            'user_id',
            $user->id
        )->first();

        if ($employee) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Role user yang terhubung dengan karyawan tidak dapat diubah dari menu User.'
                );
        }
        // Update role
        $user->syncRoles($request->roles ?? []);

        $newData = [
            'name' => $user->fresh()->name,
            'email' => $user->fresh()->email,
            'roles' => $user->fresh()->roles->pluck('name')->toArray(),
            'is_active' => $user->fresh()->is_active,
        ];

        ActivityLogger::log(
            'User',
            'Update',
            'Mengubah user: ' . $user->name,
            $oldData,
            $newData
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $oldData = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'is_active' => $user->is_active,
        ];

        $name = $user->name;
        $user->delete();
        ActivityLogger::log(
            'User',
            'Delete',
            'Menghapus user: ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
