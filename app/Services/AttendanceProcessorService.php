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
        | 1. IDT / IPC Identification Terlebih Dahulu (Wajib di Atas)
        |--------------------------------------------------------------------------
        */
        $isIdt = false;
        $isIpc = false;
        $isKhs = false; // <-- Tambahan untuk Izin Khusus

        if ($leaveRequest) {
            $code = $leaveRequest->leaveType?->code;
            $isIdt = $code === 'I-IDT';
            $isIpc = $code === 'I-IPC';
            $isKhs = $code === 'I-KHS'; // <-- Menangkap kode Izin Khusus
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Required Work Minutes (Berdasarkan Hari)
        |--------------------------------------------------------------------------
        */
        $dayOfWeek = $date->dayOfWeekIso; // 1 = Senin, 5 = Jumat, 6 = Sabtu
        $isSaturday = ($dayOfWeek == 6);  // Mengembalikan variabel isSaturday untuk batas acuan di bawah

        if ($dayOfWeek == 5) {
            // PERBAIKAN: Shift pagi adalah yang masuk di atas jam 4 subuh dan sebelum jam 12 siang.
            // Ini mencegah Shift Malam (00:00) terbaca sebagai Shift Pagi.
            $isShiftPagi = $shiftDetail->start_time > '04:00:00' && $shiftDetail->start_time < '12:00:00';

            if ($isShiftPagi) {
                $requiredWorkMinutes = 510; // Jumat Pagi: Sesuai aturan khusus Anda sebelumnya (510 menit)
            } else {
                $requiredWorkMinutes = 480; // Jumat Sore/Malam: Normal 8 Jam (480 menit)
            }
        } elseif ($dayOfWeek == 6) {
            $requiredWorkMinutes = 300; // Sabtu: 5 Jam (300 menit)
        } else {
            $requiredWorkMinutes = 480; // Senin - Kamis: 8 Jam (480 menit)
        }

        // Jika punya izin IDT (Telat) atau IPC (Pulang Cepat), target kerja dikurangi 1 jam (60 menit)
        if ($isIdt || $isIpc) {
            $requiredWorkMinutes -= 60;
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Actual Time
        |--------------------------------------------------------------------------
        */
        $actualIn = null;
        $actualOut = null;

        if (!empty($log->check_in)) {
            $actualIn = Carbon::parse($startDateStr . ' ' . $log->check_in);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Set Batas Acuan Berdasarkan Shift di Database
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
        | 5. Target Pulang Dinamis & Kunci Batas Jam Kerja
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
        | 6. Parse Jam Pulang Aktual & Proteksi Day-Crossing Overlap
        |--------------------------------------------------------------------------
        */
        if (!empty($log->check_out)) {
            $actualOut = Carbon::parse($startDateStr . ' ' . $log->check_out);
            if ($actualIn && $actualOut->lt($actualIn)) {
                $actualOut->addDay(); // Ini krusial agar jam 07:30 pagi besoknya terbaca dengan benar!
            }
        }

        // Tentukan dulu apakah hari ini libur atau weekend
        $apakahWeekendAtauLibur = $isHariLiburAtauWeekend || $date->isSunday();

        // Jika hari libur / weekend, jangan pernah anggap lupa check-in / check-out
        if ($apakahWeekendAtauLibur) {
            $forgotCheckIn = false;
            $forgotCheckOut = false;
        } else {
            $forgotCheckIn = empty($log->check_in);
            $forgotCheckOut = empty($log->check_out);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Perhitungan Keterlambatan
        |--------------------------------------------------------------------------
        */
        $lateMinutes = 0;
        if ($actualIn) {
            $actualInTimeStr = $actualIn->format('H:i:s');
            if ($actualInTimeStr > $shiftDetail->late_deadline) {
                // PERBAIKAN: Ubah batas jam malam dari '23:00:00' menjadi '21:00:00' 
                // agar check-in pukul 22:37 (datang awal/shift malam) tidak dianggap telat
                if ($shiftDetail->start_time === '00:00:00' && $actualInTimeStr >= '21:00:00') {
                    $lateMinutes = 0;
                } else {
                    $lateMinutes = $lateDeadline->diffInMinutes($actualIn);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Perhitungan Menit Kerja Aktual
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
        | 9. Perhitungan Pulang Cepat (Early Leave)
        |--------------------------------------------------------------------------
        */
        $earlyLeaveMinutes = 0;
        // Penambahan $actualIn memastikan yang lupa absen masuk tidak dihitung pulang cepat
        if ($actualIn && $requiredCheckOut && $actualOut) {
            if ($actualOut->lt($requiredCheckOut)) {
                $earlyLeaveMinutes = $actualOut->diffInMinutes($requiredCheckOut);
            }
        }

        // --- TAMBAHAN KODE UNTUK MEMPERBAIKI KASUS SHIFT MALAM ---
        // Jika total menit kerja (workMinutes) aktual karyawan sudah memenuhi atau melebihi 
        // target kewajiban jam kerja hari itu (requiredWorkMinutes), maka status Pulang Cepat dihapus (0)
        if ($workMinutes >= $requiredWorkMinutes) {
            $earlyLeaveMinutes = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Kompensasi Keterlambatan (IDT) dan Pulang Cepat (IPC)
        |--------------------------------------------------------------------------
        */
        // Jika ada izin IDT, potong denda telat sebanyak 60 menit
        if ($isIdt) {
            $lateMinutes = max(0, $lateMinutes - 60);
        }

        // Jika ada izin IPC, potong denda pulang cepat sebanyak 60 menit
        if ($isIpc) {
            $earlyLeaveMinutes = max(0, $earlyLeaveMinutes - 60);
        }

        $shortWorkMinutes = max(0, $requiredWorkMinutes - $workMinutes);

        // --- TAMBAHAN KODE UNTUK IZIN KHUSUS (I-KHS) ---
        // Jika karyawan memiliki izin khusus (I-KHS), maka kurang jam kerja mutlak di-nol-kan (putih)
        if ($isKhs) {
            $shortWorkMinutes = 0;

            // Opsional: Jika aturan perusahaan juga ingin memutihkan sanksi telat / pulang cepat 
            // akibat penugasan khusus dari atasan tersebut, Anda bisa mengaktifkan baris di bawah ini:
            // $lateMinutes = 0;
            // $earlyLeaveMinutes = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Penentuan Status & Identitas WFA
        |--------------------------------------------------------------------------
        */
        $apakahWeekendAtauLibur = $isHariLiburAtauWeekend || $date->isSunday();

        if ($apakahWeekendAtauLibur) {
            $status = 'wfa';
            $isWfa = true;

            // ATURAN WFA: Fleksibel. Hapus sanksi keterlambatan dan pulang cepat.
            $lateMinutes = 0;
            $earlyLeaveMinutes = 0;

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
                'is_wfa' => $isWfa,
                'is_khs' => $isKhs,
                'leave_request_id' => $leaveRequest?->id,
                'leave_type_id' => $leaveRequest?->leave_type_id,
                'status' => $status,
                'source' => $log->source,
                'notes' => $log->notes,
                'processed_at' => now()
            ]
        );
    }
}