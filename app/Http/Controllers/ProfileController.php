<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $employee = $user->employee()->with([
            'company',
            'position',
            'status',
            'emergencyContacts'
        ])->first();

        return view('profile.edit', [
            'user' => $user,
            'employee' => $employee,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data employee tidak ditemukan');
        }

        $oldData = $employee->toArray();

        $request->validate([
            'full_name' => 'required|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $employee->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'updated_by' => $user->id,
        ]);

        // sinkron ke user login (biar auth tetap konsisten)
        $user->update([
            'name' => $employee->full_name,
            'email' => $employee->email,
        ]);

        ActivityLogger::log(
            'Employee',
            'Update Profile',
            'User update profile employee: ' . $employee->full_name,
            $oldData,
            $employee->fresh()->toArray()
        );

        return redirect()
            ->route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ], [
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $employee = Auth::user()->employee; // Sesuaikan dengan relasi user ke karyawan Anda

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada dan bukan avatar default
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }

            // Simpan foto baru ke folder storage/app/public/employee-photos
            $path = $request->file('photo')->store('employee-photos', 'public');

            // Update database
            $employee->update([
                'photo' => $path
            ]);
        }

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

}
