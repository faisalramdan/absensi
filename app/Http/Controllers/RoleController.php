<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\Role;
use App\Helpers\ActivityLogger;

class RoleController extends Controller
{
    public function index()
    {

        $roles = Role::latest()->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = Permission::all()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view(
            'roles.create',
            compact('groupedPermissions')
        );
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'created_by' => auth()->id(),
        ]);

        $role->syncPermissions(
            $request->permissions ?? []
        );
        ActivityLogger::log(
            'Role',
            'Create',
            'Menambahkan role: ' . $role->name,
            [],
            [
                'name' => $role->name,
                'permissions' => $request->permissions ?? [],
            ]
        );

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dibuat');
    }
    public function edit(Role $role)
    {
        $groupedPermissions = Permission::all()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view(
            'roles.edit',
            compact(
                'role',
                'groupedPermissions'
            )
        );
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        $oldData = [
            'name' => $role->name,
            'permissions' => $role->permissions
                ->pluck('name')
                ->toArray(),
        ];

        $role->update([
            'name' => $request->name,
            'updated_at' => Carbon::now(),
            'updated_by' => auth()->id(),
        ]);

        $role->syncPermissions(
            $request->permissions ?? []
        );

        $newData = [
            'name' => $role->fresh()->name,
            'permissions' => $role->permissions
                ->pluck('name')
                ->toArray(),
        ];

        ActivityLogger::log(
            'Role',
            'Update',
            'Mengubah role: ' . $role->name,
            $oldData,
            $newData
        );

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(Role $role)
    {
        $oldData = [
            'name' => $role->name,
            'permissions' => $role->permissions
                ->pluck('name')
                ->toArray(),
        ];

        $name = $role->name;
        $role->delete();
        ActivityLogger::log(
            'Role',
            'Delete',
            'Menghapus role: ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('roles.index')
            ->with('success', 'Peran berhasil dihapus');
    }
}
