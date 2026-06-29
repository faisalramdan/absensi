<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Employee;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class TeamMemberController extends Controller
{
    public function index(
        Team $team,
        Request $request
    ) {
        $members = TeamMember::with([
            'employee.position'
        ])
            ->where(
                'team_id',
                $team->id
            );

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $members->whereHas(
                'employee',
                function ($q) use ($search) {

                    $q->where(
                        'full_name',
                        'ILIKE',
                        "%{$search}%"
                    );

                }
            );

        }

        // Role
        if ($request->filled('role')) {

            $members->where(
                'member_role',
                $request->role
            );

        }

        // Status
        if ($request->filled('status')) {

            $members->where(
                'is_active',
                $request->status
            );

        }

        $members = $members
            ->orderByDesc('member_role')
            ->paginate(10)
            ->withQueryString();

        return view(
            'team-members.index',
            compact(
                'team',
                'members'
            )
        );
    }

    public function create(
        Team $team
    ) {
        $employeeIds = TeamMember::pluck(
            'employee_id'
        );

        $employees = Employee::whereNotIn(
            'id',
            $employeeIds
        )
            ->where(
                'is_active',
                true
            )
            ->orderBy(
                'full_name'
            )
            ->get();

        return view(
            'team-members.create',
            compact(
                'team',
                'employees'
            )
        );
    }

    public function store(
        Request $request,
        Team $team
    ) {
        $request->validate([

            'employee_id' =>
                'required|exists:employees,id',

            'member_role' =>
                'required|in:Leader,Co Leader,Member',

            'joined_at' =>
                'nullable|date',

            'is_active' =>
                'required|boolean',

        ]);

        $member = TeamMember::create([

            'team_id' =>
                $team->id,

            'employee_id' =>
                $request->employee_id,

            'member_role' =>
                $request->member_role,

            'joined_at' =>
                $request->joined_at,

            'is_active' =>
                $request->is_active,

            'created_by' =>
                auth()->id(),

            'updated_by' =>
                auth()->id(),

        ]);

        ActivityLogger::log(

            'Team Member',

            'Create',

            'Menambahkan anggota team',

            [],

            $member->toArray()

        );

        return redirect()
            ->route(
                'teams.members.index',
                $team
            )
            ->with(
                'success',
                'Anggota team berhasil ditambahkan.'
            );
    }

    public function edit(
        Team $team,
        TeamMember $member
    ) {
        return view(
            'team-members.edit',
            compact(
                'team',
                'member'
            )
        );
    }

    public function update(
        Request $request,
        Team $team,
        TeamMember $member
    ) {
        $request->validate([

            'member_role' =>
                'required|in:Leader,Co Leader,Member',

            'joined_at' =>
                'nullable|date',

            'left_at' =>
                'nullable|date',

            'is_active' =>
                'required|boolean',

        ]);

        $old = $member->getOriginal();

        $member->update([

            'member_role' =>
                $request->member_role,

            'joined_at' =>
                $request->joined_at,

            'left_at' =>
                $request->left_at,

            'is_active' =>
                $request->is_active,

            'updated_by' =>
                auth()->id(),

        ]);

        ActivityLogger::log(

            'Team Member',

            'Update',

            'Mengubah anggota team',

            $old,

            $member->fresh()->toArray()

        );

        return redirect()
            ->route(
                'teams.members.index',
                $team
            )
            ->with(
                'success',
                'Data berhasil diperbarui.'
            );
    }

    public function destroy(
        Team $team,
        TeamMember $member
    ) {
        $old = $member->toArray();

        $member->delete();

        ActivityLogger::log(

            'Team Member',

            'Delete',

            'Menghapus anggota team',

            $old,

            []

        );

        return back()->with(
            'success',
            'Anggota team berhasil dihapus.'
        );
    }
}
