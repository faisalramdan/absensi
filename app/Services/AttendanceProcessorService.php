<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Holiday;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\AttendanceLog;
use App\Models\ShiftAssignment;

class AttendanceProcessorService
{
    public function process(
        int $employeeId,
        string $date
    ) {
        $date = Carbon::parse($date);
        $startDateStr = $date->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | 1. Ambil Data Log Absensi Terlebih Dahulu
        |--------------------------------------------------------------------------
        */
        $log = AttendanceLog::where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();

        $hasLogData = $log && (!empty($log->check_in) || !empty($log->check_out));

        /*
        |--------------------------------------------------------------------------
        | 2. Holiday Check
        |--------------------------------------------------------------------------
        */
        $holiday = Holiday::whereDate('date_applied', $date)->first();

        // Indikator apakah hari ini adalah hari libur nasional atau weekend/off
        $isHariLiburAtauWeekend = false;

        if ($holiday) {
            $isHariLiburAtauWeekend = true;

            // Jika hari libur NASIONAL tapi TIDAK ADA log absen, langsung set status 'holiday'
            if (!$hasLogData) {
                return Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $startDateStr
                    ],
                    [
                        'status' => 'holiday',
                        'remarks' => $holiday->name,
                        'is_wfa' => false,
                        'source' => 'generated',
                        'processed_at' => now()
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Leave Request Check
        |--------------------------------------------------------------------------
        */
        $leaveRequest = LeaveRequest::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();

        if ($leaveRequest && !$hasLogData) {
            $leaveCode = $leaveRequest->leaveType?->code;

            if (!in_array($leaveCode, ['I-IDT', 'I-IPC'])) {
                $status = $leaveCode == 'I-SKT' ? 'sick' : 'leave';

                return Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $startDateStr
                    ],
                    [
                        'status' => $status,
                        'leave_request_id' => $leaveRequest->id,
                        'leave_type_id' => $leaveRequest->leave_type_id,
                        'is_wfa' => false,
                        'source' => 'generated',
                        'processed_at' => now()
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Shift Assignment Check
        |--------------------------------------------------------------------------
        */
        $assignment = ShiftAssignment::with('shift.details')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();

        if (!$assignment && !$hasLogData) {
            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $startDateStr
                ],
                [
                    'status' => 'off',
                    'is_wfa' => false,
                    'source' => 'generated',
                    'processed_at' => now()
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Shift Detail Check
        |--------------------------------------------------------------------------
        */
        $dayName = $date->format('l');
        $shiftDetail = $assignment ? $assignment->shift->details->where('day_name', $dayName)->first() : null;

        // Jika jadwalnya OFF atau tidak ditemukan jadwal resmi
        if (!$shiftDetail || $shiftDetail->is_off) {
            $isHariLiburAtauWeekend = true;

            // Jika TIDAK ADA log absen -> Tetap Set 'off'
            if (!$hasLogData) {
                return Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $startDateStr
                    ],
                    [
                        'status' => 'off',
                        'is_wfa' => false,
                        'source' => 'generated',
                        'processed_at' => now()
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Jika Tanpa Log Absen Kerja -> Alpha
        |--------------------------------------------------------------------------
        */
        if (!$log) {
            return Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'date' => $startDateStr
                ],
                [
                    'shift_id' => $assignment?->shift_id,
                    'status' => 'alpha',
                    'is_wfa' => false,
                    'source' => 'generated',
                    'processed_at' => now()
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. KARYAWAN MASUK (Proses Penentuan Jam Kerja & Status WFA)
        |--------------------------------------------------------------------------
        */
        if (!$assignment || !$shiftDetail) {
            $shiftDetail = (object) [
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'late_deadline' => '08:00:00',
                'is_off' => true
            ];
        }

        return $this->processPresent(
            $employeeId,
            $assignment,
            $shiftDetail,
            $log,
            $date,
            $leaveRequest,
            $isHariLiburAtauWeekend // Oper status libur/weekend ke fungsi hitung
        );
    }

    private function processPresent(
        $employeeId,
        $assignment,
        $shiftDetail,
        $log,
        $date,
        $leaveRequest = null,
        $isHariLiburAtauWeekend = false
    ) {
        $startDateStr = $date->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Required Work Minutes
        |--------------------------------------------------------------------------
        */
        $isSaturday = $date->isSaturday();
        $requiredWorkMinutes = $isSaturday ? 300 : 480;

        /*
        |--------------------------------------------------------------------------
        | Actual Time
        |--------------------------------------------------------------------------
        */
        $actualIn = null;
        $actualOut = null;

        if (!empty($log->check_in)) {
            $actualIn = Carbon::parse($startDateStr . ' ' . $log->check_in);
        }

        /*
        |--------------------------------------------------------------------------
        | Set Batas Acuan Berdasarkan Shift di Database
        |--------------------------------------------------------------------------
        */
        $shiftStart = Carbon::parse($startDateStr . ' ' . $shiftDetail->start_time);
        $lateDeadline = Carbon::parse($startDateStr . ' ' . $shiftDetail->late_deadline);
        $shiftEnd = Carbon::parse($startDateStr . ' ' . $shiftDetail->end_time);

        if ($isSaturday) {
            $shiftEnd = $shiftStart->copy()->addMinutes($requiredWorkMinutes);
        } elseif ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Target Pulang Dinamis & Kunci Batas Jam Kerja
        |--------------------------------------------------------------------------
        */
        $requiredCheckOut = null;

        if ($actualIn) {
            $actualInTime = $actualIn->format('H:i:s');

            if ($shiftDetail->start_time === '00:00:00') {
                if ($actualInTime > $shiftDetail->late_deadline && $actualInTime < '12:00:00') {
                    $targetTime = $isSaturday ? '06:00:00' : '09:00:00';
                    $requiredCheckOut = Carbon::parse($startDateStr . ' ' . $targetTime);
                    $shiftEnd = Carbon::parse($startDateStr . ' ' . $targetTime);
                } else {
                    $requiredCheckOut = $actualIn->copy()->addMinutes($requiredWorkMinutes);
                    $targetEnd = $isSaturday ? '05:00:00' : '08:00:00';
                    $shiftEnd = Carbon::parse($startDateStr . ' ' . $targetEnd);
                }
            } else {
                if ($actualInTime <= $shiftDetail->late_deadline) {
                    $requiredCheckOut = $actualIn->copy()->addMinutes($requiredWorkMinutes);
                } else {
                    $lateDiff = $lateDeadline->diffInMinutes($actualIn);
                    $requiredCheckOut = $shiftEnd->copy()->addMinutes($lateDiff);
                }
            }
        } else {
            $requiredCheckOut = $shiftEnd->copy();
        }

        /*
        |--------------------------------------------------------------------------
        | Parse Jam Pulang Aktual & Proteksi Day-Crossing Overlap
        |--------------------------------------------------------------------------
        */
        if (!empty($log->check_out)) {
            $actualOut = Carbon::parse($startDateStr . ' ' . $log->check_out);
            if ($actualIn && $actualOut->lt($actualIn)) {
                $actualOut->addDay();
            }
        }

        $forgotCheckIn = empty($log->check_in);
        $forgotCheckOut = empty($log->check_out);

        /*
        |--------------------------------------------------------------------------
        | Perhitungan Keterlambatan
        |--------------------------------------------------------------------------
        */
        $lateMinutes = 0;
        if ($actualIn) {
            $actualInTimeStr = $actualIn->format('H:i:s');
            if ($actualInTimeStr > $shiftDetail->late_deadline) {
                if ($shiftDetail->start_time === '00:00:00' && $actualInTimeStr >= '23:00:00') {
                    $lateMinutes = 0;
                } else {
                    $lateMinutes = $lateDeadline->diffInMinutes($actualIn);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Perhitungan Menit Kerja Aktual
        |--------------------------------------------------------------------------
        */
        $workMinutes = 0;
        if ($actualIn && $actualOut) {
            $workMinutes = $actualIn->diffInMinutes($actualOut);
        }

        if (($actualIn && !$actualOut) || (!$actualIn && $actualOut)) {
            $workMinutes = $requiredWorkMinutes;
        }

        /*
        |--------------------------------------------------------------------------
        | Perhitungan Pulang Cepat (Early Leave)
        |--------------------------------------------------------------------------
        */
        $earlyLeaveMinutes = 0;
        if ($requiredCheckOut && $actualOut) {
            if ($actualOut->lt($requiredCheckOut)) {
                $earlyLeaveMinutes = $actualOut->diffInMinutes($requiredCheckOut);
            }
        }

        $shortWorkMinutes = max(0, $requiredWorkMinutes - $workMinutes);

        /*
        |--------------------------------------------------------------------------
        | IDT / IPC Identification
        |--------------------------------------------------------------------------
        */
        $isIdt = false;
        $isIpc = false;
        if ($leaveRequest) {
            $code = $leaveRequest->leaveType?->code;
            $isIdt = $code === 'I-IDT';
            $isIpc = $code === 'I-IPC';
        }

        /*
        |--------------------------------------------------------------------------
        | ATURAN BARU: Penentuan Status & Identitas is_wfa
        |--------------------------------------------------------------------------
        */
        $apakahWeekendAtauLibur = $isHariLiburAtauWeekend || $date->isSunday();

        if ($apakahWeekendAtauLibur) {
            $status = 'wfa';
            $isWfa = true;
        } else {
            $status = 'present';
            $isWfa = false;
        }

        $finalShiftId = $assignment ? $assignment->shift_id : 1;

        return Attendance::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'date' => $startDateStr
            ],
            [
                'shift_id' => $finalShiftId,
                'scheduled_check_in' => $shiftDetail->start_time,
                'scheduled_check_out' => $shiftEnd->format('H:i:s'),
                'actual_check_in' => $log->check_in,
                'actual_check_out' => $log->check_out,
                'late_minutes' => $lateMinutes,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'work_minutes' => $workMinutes,
                'short_work_minutes' => $shortWorkMinutes,
                'forgot_check_in' => $forgotCheckIn,
                'forgot_check_out' => $forgotCheckOut,
                'is_idt' => $isIdt,
                'is_ipc' => $isIpc,
                'is_wfa' => $isWfa, // Menyimpan identitas is_wfa ke database
                'leave_request_id' => $leaveRequest?->id,
                'leave_type_id' => $leaveRequest?->leave_type_id,
                'status' => $status, // Berisi 'wfa' jika masuk di hari libur/weekend, atau 'present' di hari kerja biasa
                'source' => $log->source,
                'notes' => $log->notes,
                'processed_at' => now()
            ]
        );
    }
}