<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date_applied', 'desc')->get();
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'date_actual' => 'required|date',
            'date_applied' => 'required|date',
            'notes' => 'nullable',
        ]);

        // Mengambil ID karyawan berdasarkan relasi user login sesuai master sebelumnya
        $createdBy = Auth::user()->employee?->id ?? null;

        $holiday = Holiday::create([
            'name' => $request->name,
            'date_actual' => $request->date_actual,
            'date_applied' => $request->date_applied,
            'notes' => $request->notes,

            // Mengisi field audit trail
            'created_by' => $createdBy,
            'updated_by' => $createdBy,
        ]);

        ActivityLogger::log(
            'Holiday',
            'Create',
            'Menambahkan hari libur: ' . $holiday->name,
            [],
            $holiday->toArray()
        );

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Hari libur berhasil dibuat');
    }

    public function edit(Holiday $holiday)
    {
        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'name' => 'required|max:255',
            'date_actual' => 'required|date',
            'date_applied' => 'required|date',
            'notes' => 'nullable',
        ]);

        $oldData = $holiday->toArray();

        // Mengambil ID karyawan pengubah data
        $updatedBy = Auth::user()->employee?->id ?? null;

        $holiday->update([
            'name' => $request->name,
            'date_actual' => $request->date_actual,
            'date_applied' => $request->date_applied,
            'notes' => $request->notes,

            // Mengupdate field updated_by
            'updated_by' => $updatedBy,
        ]);

        ActivityLogger::log(
            'Holiday',
            'Update',
            'Mengubah data hari libur: ' . $holiday->name,
            $oldData,
            $holiday->toArray()
        );

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Hari libur berhasil diperbarui');
    }

    public function destroy(Holiday $holiday)
    {
        $oldData = $holiday->toArray();
        $holidayName = $holiday->name;

        $holiday->delete();

        ActivityLogger::log(
            'Holiday',
            'Delete',
            'Menghapus hari libur: ' . $holidayName,
            $oldData,
            []
        );

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Hari libur berhasil dihapus');
    }

    public function show(Holiday $holiday)
    {
        return view(
            'holidays.show',
            compact('holiday')
        );
    }
}