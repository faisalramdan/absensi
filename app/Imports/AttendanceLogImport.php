<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\AttendanceLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceLogImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // 1. Ambil teks periode di Baris 4 (index 3), Kolom D (index 3)
        $periodText = $rows[3][3] ?? '';

        // Ekstrak bulan dan tahun dari akhir teks periode (misal: "25 Juni 2026")
        if (!preg_match('/s\/d\s+\d{1,2}\s+([A-Za-z]+)\s+(\d{4})/', $periodText, $matches)) {
            return;
        }

        $monthName = strtolower($matches[1]);
        $year = $matches[2];

        $monthsMap = [
            'januari' => '01',
            'februari' => '02',
            'maret' => '03',
            'april' => '04',
            'mei' => '05',
            'juni' => '06',
            'juli' => '07',
            'agustus' => '08',
            'september' => '09',
            'oktober' => '10',
            'november' => '11',
            'desember' => '12'
        ];
        $defaultMonth = $monthsMap[$monthName] ?? Carbon::now()->format('m');

        // 2. Header Angka Tanggal berada di Baris 6 (Index 5)
        $dateHeaders = $rows[5] ?? collect();

        // 3. Proses Data Karyawan
        $totalRows = count($rows);

        // Kita gunakan perulangan satu-per-satu ($i++) agar bisa mendeteksi NIK secara fleksibel
        for ($i = 6; $i < $totalRows; $i++) {

            $currentRow = $rows[$i] ?? null;
            if (!$currentRow) {
                continue;
            }

            // Ambil kolom E (Index 4) yang berpotensi berisi NIK
            $nik = trim($currentRow[4] ?? '');

            // Jika kolom ini kosong atau bukan angka murni NIK, lewati baris ini
            if ($nik === '' || !is_numeric($nik)) {
                continue;
            }

            // Cari karyawan berdasarkan NIK
            $employee = Employee::where('nik', $nik)->first();
            if (!$employee) {
                continue; // Lempar/skip jika NIK tidak terdaftar di sistem database Anda
            }

            // Dapatkan baris jam masuk dan jam keluar tepat di bawah baris NIK ini secara dinamis
            $rowCheckIn = $rows[$i + 1] ?? null;
            $rowCheckOut = $rows[$i + 2] ?? null;

            // Cek apakah baris berikutnya adalah NIK milik karyawan lain (berarti data jam kosong)
            // Jika baris berikutnya ternyata NIK baru, maka karyawan saat ini tidak punya data jam absen.
            $nextRowNik = trim($rowCheckIn[4] ?? '');
            if ($nextRowNik !== '' && is_numeric($nextRowNik)) {
                // Berarti karyawan saat ini tidak punya data jam, langsung lompat ke baris berikutnya
                continue;
            }

            // Loop tanggal dari Kolom F (Index 5) sampai Kolom AJ (Index 35)
            for ($col = 5; $col <= 35; $col++) {

                $day = $dateHeaders[$col] ?? null;
                if (!$day || !is_numeric($day)) {
                    continue;
                }

                // Penentuan Bulan Berdasarkan Cut-off 26 s/d 25
                if ($day >= 26) {
                    $dateObj = Carbon::create($year, $defaultMonth, 1)->subMonth();
                    $month = $dateObj->format('m');
                    $currentYear = $dateObj->format('Y');
                } else {
                    $month = $defaultMonth;
                    $currentYear = $year;
                }

                $date = Carbon::create($currentYear, $month, $day)->format('Y-m-d');

                // Ambil data mentah jam masuk & keluar
                $checkInRaw = isset($rowCheckIn[$col]) ? trim($rowCheckIn[$col]) : null;
                // Pastikan rowCheckOut bukan baris milik NIK karyawan lain
                $checkOutRaw = ($rowCheckOut && (!isset($rowCheckOut[4]) || trim($rowCheckOut[4]) === ''))
                    ? trim($rowCheckOut[$col] ?? '')
                    : null;

                // Jika isi cell bernilai status huruf (seperti H, I, S, CUTI), bersihkan agar tidak dianggap jam
                if (preg_match('/[A-Za-z]/', $checkInRaw)) {
                    $checkInRaw = null;
                }
                if (preg_match('/[A-Za-z]/', $checkOutRaw)) {
                    $checkOutRaw = null;
                }

                // Jika kosong dua-duanya, skip tanggal ini
                if (empty($checkInRaw) && empty($emptyCheckIn) && empty($checkOutRaw)) {
                    continue;
                }

                // --- PROSES KONVERSI FORMAT JAM EXCEL ---
                $checkIn = null;
                $checkOut = null;

                if (!empty($checkInRaw)) {
                    if (is_numeric($checkInRaw)) {
                        $checkIn = Date::excelToDateTimeObject($checkInRaw)->format('H:i:s');
                    } else {
                        $checkIn = $checkInRaw;
                        if (strlen($checkIn) == 5) {
                            $checkIn .= ':00';
                        }
                    }
                }

                if (!empty($checkOutRaw)) {
                    if (is_numeric($checkOutRaw)) {
                        $checkOut = Date::excelToDateTimeObject($checkOutRaw)->format('H:i:s');
                    } else {
                        $checkOut = $checkOutRaw;
                        if (strlen($checkOut) == 5) {
                            $checkOut .= ':00';
                        }
                    }
                }

                // Jika setelah diekstrak format jam tidak valid, jangan dimasukkan
                if (empty($checkIn) && empty($checkOut)) {
                    continue;
                }

                // Simpan atau update log absensi ke database
                AttendanceLog::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $date,
                    ],
                    [
                        'check_in' => $checkIn ?: null,
                        'check_out' => $checkOut ?: null,
                        'source' => 'import_excel',
                        'created_by' => Auth::user()->employee?->id,
                    ]
                );
            }

            // Sesuaikan langkah index loop agar tidak membaca ulang baris jam yang sudah diproses
            if ($rowCheckOut && (!isset($rowCheckOut[4]) || trim($rowCheckOut[4]) === '')) {
                $i += 2; // Lompat 2 baris karena sukses membaca baris masuk & keluar
            } elseif ($rowCheckIn && (!isset($rowCheckIn[4]) || trim($rowCheckIn[4]) === '')) {
                $i += 1; // Lompat 1 baris jika hanya ada baris masuk
            }
        }
    }
}