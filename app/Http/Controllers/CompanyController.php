<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::query();

        if ($request->filled('search')) {

            $companies->where(function ($q) use ($request) {

                $q->where('name', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('code', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'ILIKE', '%' . $request->search . '%')
                    ->orWhere('phone', 'ILIKE', '%' . $request->search . '%');

            });

        }

        if ($request->filled('status')) {

            $companies->where(
                'is_active',
                $request->status
            );

        }

        $companies = $companies
            ->with([
                'creator',
                'updater'
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'companies.index',
            compact('companies')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:20|unique:companies,code',
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:50',
            'address' => 'nullable',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $logo = null;

        if ($request->hasFile('logo')) {

            $logo = $request
                ->file('logo')
                ->store('companies', 'public');

        }

        $company = Company::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'logo' => $logo,
            'is_active' => $request->boolean('is_active'),

            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        ActivityLogger::log(
            'Company',
            'Create',
            'Menambahkan perusahaan: ' . $company->name,
            [],
            $company->toArray()
        );

        return redirect()
            ->route('companies.index')
            ->with(
                'success',
                'Perusahaan berhasil ditambahkan'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view(
            'companies.show',
            compact('company')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view(
            'companies.edit',
            compact('company')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Company $company
    ) {
        $request->validate([
            'code' => 'required|max:20|unique:companies,code,' . $company->id,
            'name' => 'required|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:50',
            'address' => 'nullable',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $oldData = $company->toArray();

        $logo = $company->logo;

        if ($request->hasFile('logo')) {

            $logo = $request
                ->file('logo')
                ->store('companies', 'public');

        }

        $company->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'logo' => $logo,
            'is_active' => $request->boolean('is_active'),

            'updated_by' => auth()->id(),
        ]);

        ActivityLogger::log(
            'Company',
            'Update',
            'Mengubah perusahaan: ' . $company->name,
            $oldData,
            $company->fresh()->toArray()
        );

        return redirect()
            ->route('companies.index')
            ->with(
                'success',
                'Perusahaan berhasil diperbarui'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $oldData = $company->toArray();

        $name = $company->name;

        $company->delete();

        ActivityLogger::log(
            'Company',
            'Delete',
            'Menghapus perusahaan: ' . $name,
            $oldData,
            []
        );

        return redirect()
            ->route('companies.index')
            ->with(
                'success',
                'Perusahaan berhasil dihapus'
            );
    }
}