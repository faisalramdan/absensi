<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * Tampilan Daftar Shift
     */
    public function index(Request $request)
    {
        // 1. Ambil keyword pencarian dari input URL (?search=...)
        $search = $request->input('search');

        // 2. Buat query dasar beserta relasi creatornya
        $query = Shift::with('creator')->latest();

        // 3. Jika ada input pencarian, saring berdasarkan nama atau deskripsi shift
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // 4. Gunakan paginate (misal: 10 data per halaman) agar sesuai dengan tampilan Blade
        $shifts = $query->paginate(10)->withQueryString();

        // 5. Kembalikan ke view (pastikan folder path view sesuai dengan struktur Anda)
        // Jika file blade ditaruh di resources/views/admin/shifts/index.blade.php, gunakan 'admin.shifts.index'
        return view('shifts.index', compact('shifts'));
    }

    /**
     * Tampilan Form Tambah Shift
     */
    public function create()
    {
        // Daftar hari standar untuk looping di file Blade/HTML nanti
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return view('shifts.create', compact('days'));
    }

    /**
     * Proses Simpan Data Shift & Detail Harian
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name',
            'description' => 'nullable|string',
            'days' => 'required|array', // Memastikan array inputan hari masuk
        ]);

        // Ambil employee_id dari user admin yang sedang login
        $employeeId = auth()->user()->employee?->id;

        // 2. Gunakan DB Transaction agar jika salah satu hari gagal disimpan, semua proses dibatalkan (Aman untuk PGSQL)
        DB::beginTransaction();

        try {
            // Simpan ke tabel induk (shifts)
            $shift = Shift::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => $employeeId,
                'updated_by' => $employeeId,
            ]);

            // Simpan ke tabel anak (shift_details) looping per hari
            foreach ($request->days as $dayName => $timeData) {
                // Cek apakah admin mencentang tombol 'libur' untuk hari tersebut
                $isOff = isset($timeData['is_off']) ? true : false;

                ShiftDetail::create([
                    'shift_id' => $shift->id,
                    'day_name' => $dayName,
                    'start_time' => $isOff ? null : $timeData['start_time'],
                    'end_time' => $isOff ? null : $timeData['end_time'],
                    'late_deadline' => $isOff ? null : $timeData['late_deadline'],
                    'is_off' => $isOff,
                ]);
            }

            DB::commit();
            return redirect()->route('shifts.index')->with('success', 'Master Shift berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan Form Edit Shift
     */
    public function edit(Shift $shift)
    {
        // Load data detail hariannya sekalian agar bisa muncul di form edit
        $shift->load('details');

        // Mengubah collection detail menjadi format key-value array [day_name => detail_data]
        $shiftDetails = $shift->details->keyBy('day_name');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('shifts.edit', compact('shift', 'shiftDetails', 'days'));
    }

    /**
     * Proses Update Data Shift
     */
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,' . $shift->id,
            'description' => 'nullable|string',
            'days' => 'required|array',
        ]);

        $employeeId = auth()->user()->employee?->id;

        DB::beginTransaction();

        try {
            // Update tabel induk
            $shift->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_by' => $employeeId,
            ]);

            // Update atau buat ulang data detail per harinya
            foreach ($request->days as $dayName => $timeData) {
                $isOff = isset($timeData['is_off']) ? true : false;

                ShiftDetail::updateOrCreate(
                    [
                        'shift_id' => $shift->id,
                        'day_name' => $dayName
                    ],
                    [
                        'start_time' => $isOff ? null : $timeData['start_time'],
                        'end_time' => $isOff ? null : $timeData['end_time'],
                        'late_deadline' => $isOff ? null : $timeData['late_deadline'],
                        'is_off' => $isOff,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('shifts.index')->with('success', 'Master Shift berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Master Shift
     */
    public function destroy(Shift $shift)
    {
        // Karena di migration menggunakan onDelete('cascade'), data detail otomatis ikut terhapus
        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Master Shift berhasil dihapus!');
    }
}
