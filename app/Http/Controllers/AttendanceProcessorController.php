<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Employee;
use App\Models\Attendance;

class AttendanceProcessorController extends Controller
{
    public function index()
    {
        return view('attendance-processor.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $selected = Carbon::create(
            $request->year,
            $request->month,
            1
        );

        $start = $selected
            ->copy()
            ->subMonth()
            ->day(26);

        $end = $selected
            ->copy()
            ->day(25);

        $before = microtime(true);

        Artisan::call(
            'attendance:generate',
            [
                'startDate' => $start->format('Y-m-d'),
                'endDate' => $end->format('Y-m-d'),
            ]
        );

        $after = microtime(true);

        $employeeCount = Employee::count();

        $attendanceCount = Attendance::whereBetween(
            'date',
            [$start, $end]
        )->count();

        return back()->with([
            'success' => 'Attendance berhasil diproses.',

            'summary' => [

                'period' =>
                    $start->translatedFormat('d F Y')
                    . ' - ' .
                    $end->translatedFormat('d F Y'),

                'employee' => number_format($employeeCount),

                'attendance' => number_format($attendanceCount),

                'duration' =>
                    number_format(
                        $after - $before,
                        2
                    ) . ' detik',

            ]

        ]);
    }
}