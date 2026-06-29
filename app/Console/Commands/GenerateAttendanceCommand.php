<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Console\Command;
use App\Services\AttendanceProcessorService;

class GenerateAttendanceCommand extends Command
{
    protected $signature =
        'attendance:generate
        {startDate?}
        {endDate?}';

    protected $description =
        'Generate attendance from attendance logs';

    public function handle(
        AttendanceProcessorService $processor
    ) {

        $startDate =
            $this->argument('startDate')
            ?? now()->startOfMonth()->format('Y-m-d');

        $endDate =
            $this->argument('endDate')
            ?? now()->format('Y-m-d');

        $employees = Employee::all();

        $currentDate =
            Carbon::parse($startDate);

        $end =
            Carbon::parse($endDate);

        while (
            $currentDate->lte($end)
        ) {

            foreach (
                $employees as $employee
            ) {

                $processor->process(
                    $employee->id,
                    $currentDate->format('Y-m-d')
                );
            }

            $currentDate->addDay();
        }

        $this->info(
            'Attendance generated successfully.'
        );

        return self::SUCCESS;
    }
}