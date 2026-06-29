<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\ActivityLogger;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $positions = Position::query();

        if ($request->filled('search')) {

            $positions->where(function ($q) use ($request) {

                $q->where('name', 'ILIKE', '%' . $request->search . '%');

            });

        }

        $positions = $positions
            ->orderBy('name', 'asc')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'positions.index',
            compact('positions')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $positions = Position::orderBy('name')->get();

        return view('positions.create', compact('positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:positions,code',
            'name' => 'required|max:255',
        ]);

        $position = Position::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        ActivityLogger::log(
            'Position',
            'Create',
            'Menambahkan jabatan: ' . $position->name,
            [],
            $position->toArray()
        );

        return redirect()
            ->route('positions.index')
            ->with('success', 'Jabatan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        $positions = Position::orderBy('name')->get();
        return view(
            'positions.edit',
            compact('position')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'code' => 'required|max:50|unique:positions,code,' . $position->id,
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);
        $oldData = $position->toArray();

        $position->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
            'updated_by' => auth()->id(),
        ]);
        ActivityLogger::log(
            'Position',
            'Update',
            'Mengubah jabatan: ' . $position->name,
            $oldData,
            $position->fresh()->toArray()
        );

        return redirect()
            ->route('positions.index')
            ->with('success', 'Jabatan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $oldData = $position->toArray();
        $name = $position->name;
        $position->delete();

        ActivityLogger::log(
            'Position',
            'Delete',
            'Menghapus jabatan: ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }
}
