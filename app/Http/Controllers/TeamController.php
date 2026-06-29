<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\ActivityLogger;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teams = Team::with([
            'company',
            'parent'
        ]);

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $teams->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'ILIKE',
                    "%{$search}%"
                );

            });

        }

        // Company
        if ($request->filled('company')) {

            $teams->where(
                'company_id',
                $request->company
            );

        }

        // Status
        if ($request->filled('status')) {

            $teams->where(
                'is_active',
                $request->status
            );

        }

        $teams = $teams
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $companies = Company::orderBy('name')->get();

        return view(
            'teams.index',
            compact(
                'teams',
                'companies'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();

        $parents = Team::orderBy('name')->get();

        return view(
            'teams.create',
            compact(
                'companies',
                'parents'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'company_id' => 'required|exists:companies,id',

            'name' => 'required|max:255',

            'parent_id' => 'nullable|exists:teams,id',

            'description' => 'nullable',

            'sort_order' => 'nullable|integer',

            'is_active' => 'required|boolean',

        ]);

        $team = Team::create([

            'company_id' => $request->company_id,

            'name' => $request->name,

            'parent_id' => $request->parent_id,

            'description' => $request->description,

            'sort_order' => $request->sort_order ?? 0,

            'is_active' => $request->is_active,

            'created_by' => auth()->id(),

            'updated_by' => auth()->id(),

        ]);

        ActivityLogger::log(

            'Team',

            'Create',

            'Menambahkan Team',

            [],

            $team->toArray()

        );

        return redirect()
            ->route('teams.index')
            ->with(
                'success',
                'Team berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load([
            'company',
            'parent',
            'creator',
            'updater'
        ]);

        $leaders = $team->members()
            ->with([
                'employee.position'
            ])
            ->where('member_role', 'Leader')
            ->get();

        $members = $team->members()
            ->with([
                'employee.position'
            ])
            ->paginate(10);

        return view(
            'teams.show',
            compact(
                'team',
                'leaders',
                'members'
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $companies = Company::orderBy('name')->get();

        $parents = Team::where(
            'id',
            '!=',
            $team->id
        )->orderBy('name')->get();

        return view(
            'teams.edit',
            compact(
                'team',
                'companies',
                'parents'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Team $team
    ) {
        $request->validate([

            'company_id' => 'required|exists:companies,id',

            'name' => 'required|max:255',

            'parent_id' => 'nullable|exists:teams,id',

            'description' => 'nullable',

            'sort_order' => 'nullable|integer',

            'is_active' => 'required|boolean',

        ]);

        $old = $team->getOriginal();

        $team->update([

            'company_id' => $request->company_id,

            'name' => $request->name,

            'parent_id' => $request->parent_id,

            'description' => $request->description,

            'sort_order' => $request->sort_order ?? 0,

            'is_active' => $request->is_active,

            'updated_by' => auth()->id(),

        ]);

        ActivityLogger::log(

            'Team',

            'Update',

            'Mengubah Team',

            $old,

            $team->fresh()->toArray()

        );

        return redirect()
            ->route('teams.index')
            ->with(
                'success',
                'Team berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $old = $team->toArray();

        $team->delete();

        ActivityLogger::log(

            'Team',

            'Delete',

            'Menghapus Team',

            $old,

            []

        );

        return back()->with(
            'success',
            'Team berhasil dihapus.'
        );
    }
}
