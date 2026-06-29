<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\ActivityLog;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveTypes = LeaveType::query();

        // Search
        if ($request->filled('search')) {

            $leaveTypes->where(function ($q) use ($request) {

                $q->where('name', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('code', 'ILIKE', '%' . $request->search . '%');

            });
        }

        // Filter Tag
        if ($request->filled('tag')) {

            $leaveTypes->where(
                'tag',
                $request->tag
            );
        }

        // Filter Jenis
        if ($request->filled('type')) {

            $leaveTypes->where(
                'type',
                $request->type
            );
        }

        // Filter Status
        if ($request->filled('status')) {

            $leaveTypes->where(
                'is_active',
                $request->status
            );
        }

        $leaveTypes = $leaveTypes
            ->with([
                'creator',
                'updater'
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'leave-types.index',
            compact('leaveTypes')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:50|unique:leave_types,code',
            'name' => 'required|max:255',
            'tag' => 'required',
            'type' => 'required',
            'quota' => 'nullable|integer|min:0',
            'reset_period' => 'required',
            'description' => 'nullable',
        ]);

        $leaveType = LeaveType::create([

            'code' => strtoupper($request->code),
            'name' => $request->name,
            'tag' => $request->tag,
            'type' => $request->type,
            'quota' => $request->quota,
            'reset_period' => $request->reset_period,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),

            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),

        ]);

        ActivityLogger::log(
            'Leave Type',
            'Create',
            'Menambahkan jenis cuti/izin: ' . $leaveType->name,
            [],
            $leaveType->toArray()
        );

        return redirect()
            ->route('leave-types.index')
            ->with(
                'success',
                'Jenis cuti/izin berhasil dibuat'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType)
    {
        return view(
            'leave-types.show',
            compact('leaveType')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType)
    {
        $leaveType->load(['creator', 'updater']);

        return view(
            'leave-types.edit',
            compact('leaveType')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        LeaveType $leaveType
    ) {
        $request->validate([
            'code' => 'required|max:50|unique:leave_types,code,' . $leaveType->id,
            'name' => 'required|max:255',
            'tag' => 'required',
            'type' => 'required',
            'quota' => 'nullable|integer|min:0',
            'reset_period' => 'required',
            'description' => 'nullable',
        ]);

        $oldData = $leaveType->toArray();

        $leaveType->update([

            'code' => strtoupper($request->code),
            'name' => $request->name,
            'tag' => $request->tag,
            'type' => $request->type,
            'quota' => $request->quota,
            'reset_period' => $request->reset_period,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),

            'updated_by' => auth()->id(),

        ]);

        ActivityLogger::log(
            'Leave Type',
            'Update',
            'Mengubah jenis cuti/izin: ' . $leaveType->name,
            $oldData,
            $leaveType->fresh()->toArray()
        );

        return redirect()
            ->route('leave-types.index')
            ->with(
                'success',
                'Jenis cuti/izin berhasil diperbarui'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        $oldData = $leaveType->toArray();

        $name = $leaveType->name;

        $leaveType->delete();

        ActivityLogger::log(
            'Leave Type',
            'Delete',
            'Menghapus jenis cuti/izin: ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('leave-types.index')
            ->with(
                'success',
                'Jenis cuti/izin berhasil dihapus'
            );
    }

}
