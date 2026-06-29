<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Helpers\ActivityLogger;

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
}
