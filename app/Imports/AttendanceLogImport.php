<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\AttendanceLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class AttendanceLogImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // 1. Ambil periode dengan aman menggunakan ?? ''
        $periodText = $rows[1][2] ?? '';

        // lakukan pengecekan format regex
        if (!preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $periodText, $matches)) {
            // Jika format tanggal tidak ditemukan di baris 2 kolom 3, 
            // Anda bisa berikan return atau throw error kustom agar user tahu.
            return;
        }

        $month = $matches[2];
        $year = $matches[3];

        // Header tanggal (pastikan baris ke-3 ini ada)
        $dateHeaders = $rows[2] ?? collect();

        // Data employee mulai row ke-4
        foreach ($rows->slice(4) as $row) {

            $nik = trim($row[0] ?? '');

            if (empty($nik)) {
                continue;
            }

            $employee = Employee::where('nik', $nik)->first();

            if (!$employee) {
                continue;
            }

            // Loop tanggal (kolom 3 sampai 33)
            for ($col = 3; $col <= 33; $col++) {

                $day = $dateHeaders[$col] ?? null;

                if (!$day || !is_numeric($day)) {
                    continue;
                }

                $cellValue = $row[$col] ?? null;

                if (empty($cellValue)) {
                    continue;
                }

                $times = preg_split("/\r\n|\n|\r/", trim($cellValue));

                $checkIn = $times[0] ?? null;
                $checkOut = $times[1] ?? null;

                $date = Carbon::create($year, $month, $day)->format('Y-m-d');

                AttendanceLog::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $date,
                    ],
                    [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'source' => 'import_excel',
                        'created_by' => Auth::user()->employee?->id,
                    ]
                );
            }
        }
    }
}